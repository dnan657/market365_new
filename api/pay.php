<?php

$gl_api_func_json = [
	'create_intent' => 'f_api_pay_create_intent',
	'get_list'      => 'f_api_pay_get_list',
];


function f_api_pay_require_user(){
	$u = f_user_get();
	if( !is_array($u) || empty($u['_id']) ){
		return [
			'error' => 'Требуется вход',
			'error_code' => -1,
		];
	}
	return null;
}


function f_pay_transaction_next_id(){
	$r = f_db_select('SELECT COALESCE(MAX(`_id`), 0) + 1 AS `n` FROM `pay_transaction`');
	return isset($r[0]['n']) ? (int)$r[0]['n'] : 1;
}


function f_pay_transaction_id_is_autoincrement(){
	static $cache = null;
	if( $cache !== null ){
		return $cache;
	}
	$c = f_db_select("SHOW COLUMNS FROM `pay_transaction` WHERE Field = '_id'");
	$extra = isset($c[0]['Extra']) ? strtolower((string)$c[0]['Extra']) : '';
	$cache = strpos($extra, 'auto_increment') !== false;
	return $cache;
}


function f_pay_table_has_column($col){
	static $cache = [];
	if( isset($cache[$col]) ){
		return $cache[$col];
	}
	$c = f_db_select('SHOW COLUMNS FROM `pay_transaction` LIKE ' . f_db_sql_value($col));
	$cache[$col] = !empty($c);
	return $cache[$col];
}


function f_pay_service_amount_gbp($service_type){
	if( $service_type === 'top' ){
		return [499, 4.99, f_translate('TOP listing (7 days)')];
	}
	if( $service_type === 'vip' ){
		return [999, 9.99, f_translate('VIP listing (30 days)')];
	}
	return null;
}


function f_stripe_payment_intent_create($amount_pence, $metadata){
	$secret = trim((string)($GLOBALS['WEB_JSON']['api_json']['stripe_secret'] ?? ''));
	if( $secret === '' ){
		return ['ok' => false, 'error' => 'Stripe secret not configured'];
	}
	$fields = [
		'amount' => (string)intval($amount_pence),
		'currency' => 'gbp',
		'automatic_payment_methods[enabled]' => 'true',
	];
	foreach( $metadata as $k => $v ){
		$fields['metadata[' . $k . ']'] = (string)$v;
	}
	$ch = curl_init('https://api.stripe.com/v1/payment_intents');
	curl_setopt_array($ch, [
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($fields),
		CURLOPT_USERPWD => $secret . ':',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 30,
	]);
	$raw = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	if( $raw === false ){
		return ['ok' => false, 'error' => $err ?: 'Stripe request failed'];
	}
	$json = json_decode($raw, true);
	if( !is_array($json) ){
		return ['ok' => false, 'error' => 'Invalid Stripe response'];
	}
	if( !empty($json['error']['message']) ){
		return ['ok' => false, 'error' => (string)$json['error']['message']];
	}
	if( empty($json['id']) || empty($json['client_secret']) ){
		return ['ok' => false, 'error' => 'Stripe response missing intent'];
	}
	return ['ok' => true, 'intent' => $json];
}


function f_api_pay_create_intent($ARGS, $_web = null){
	$err = f_api_pay_require_user();
	if( $err ){
		return $err;
	}
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'client_secret' => '',
		'stripe_public' => trim((string)($GLOBALS['WEB_JSON']['api_json']['stripe_public'] ?? '')),
	];

	$ads_id = intval($ARGS['ads_id'] ?? 0);
	$service_type = preg_replace('/[^a-z]/', '', strtolower((string)($ARGS['service_type'] ?? '')));
	if( $ads_id <= 0 || ($service_type !== 'top' && $service_type !== 'vip') ){
		$response_json['error'] = 'Invalid request';
		$response_json['error_code'] = -2;
		return $response_json;
	}

	$svc = f_pay_service_amount_gbp($service_type);
	if( !$svc ){
		$response_json['error'] = 'Invalid service';
		return $response_json;
	}
	list($pence, $gbp, $label) = $svc;

	$me = intval(f_user_get()['_id']);
	$ad = f_db_select(
		'SELECT `_id`, `user_id`, `title` FROM `ads` WHERE `_id` = ' . $ads_id . ' AND `delete_on` = 0 LIMIT 1'
	);
	if( empty($ad) ){
		$response_json['error'] = 'Ad not found';
		$response_json['error_code'] = -3;
		return $response_json;
	}
	if( intval($ad[0]['user_id'] ?? 0) !== $me && (f_user_get()['type'] ?? '') !== 'admin' ){
		$response_json['error'] = 'No access';
		$response_json['error_code'] = -4;
		return $response_json;
	}

	$meta = [
		'user_id' => (string)$me,
		'ads_id' => (string)$ads_id,
		'service_type' => $service_type,
	];
	$stripe = f_stripe_payment_intent_create($pence, $meta);
	if( !$stripe['ok'] ){
		$response_json['error'] = $stripe['error'] ?? 'Stripe error';
		$response_json['error_code'] = -5;
		return $response_json;
	}

	$intent = $stripe['intent'];
	$pi_id = (string)$intent['id'];
	$now = date('Y-m-d H:i:s');

	$row = [
		'item_name' => $label,
		'item_price' => $gbp,
		'item_price_currency' => 'GBP',
		'paid_amount' => $gbp,
		'paid_amount_currency' => 'GBP',
		'txn_id' => $pi_id,
		'payment_status' => 'pending',
		'create_date' => $now,
		'update_date' => $now,
	];
	if( !f_pay_transaction_id_is_autoincrement() ){
		$row['_id'] = f_pay_transaction_next_id();
	}
	if( f_pay_table_has_column('user_id') ){
		$row['user_id'] = $me;
	}
	if( f_pay_table_has_column('ads_id') ){
		$row['ads_id'] = $ads_id;
	}
	if( f_pay_table_has_column('stripe_intent_id') ){
		$row['stripe_intent_id'] = $pi_id;
	}
	if( f_pay_table_has_column('service_type') ){
		$row['service_type'] = $service_type;
	}

	f_db_insert('pay_transaction', $row);

	$response_json['client_secret'] = (string)$intent['client_secret'];
	return $response_json;
}


function f_api_pay_get_list($ARGS, $_web = null){
	$err = f_api_pay_require_user();
	if( $err ){
		return $err;
	}
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'arr_txn' => [],
	];
	if( !f_pay_table_has_column('user_id') ){
		return $response_json;
	}
	$me = intval(f_user_get()['_id']);
	$rows = f_db_select(
		'SELECT * FROM `pay_transaction` WHERE `user_id` = ' . $me . ' ORDER BY `create_date` DESC, `_id` DESC LIMIT 80'
	);
	if( !is_array($rows) ){
		return $response_json;
	}
	foreach( $rows as $r ){
		$st = strtolower((string)($r['payment_status'] ?? ''));
		$svc = (string)($r['service_type'] ?? '');
		if( $svc === '' && !empty($r['item_name']) ){
			$svc = (string)$r['item_name'];
		}
		$response_json['arr_txn'][] = [
			'txn_id' => (string)($r['txn_id'] ?? ''),
			'service_type' => $svc,
			'amount' => isset($r['paid_amount']) ? floatval($r['paid_amount']) : 0,
			'currency' => (string)($r['paid_amount_currency'] ?? 'GBP'),
			'status' => $st,
			'html_date' => f_datetime_beauty((string)($r['create_date'] ?? '')),
			'html_amount' => f_number_space(floatval($r['paid_amount'] ?? 0)) . ' ' . f_page_currency(),
		];
	}
	return $response_json;
}


function f_stripe_webhook_verify_payload($payload, $sig_header, $secret){
	$secret = trim($secret);
	if( $secret === '' || $sig_header === '' || $payload === '' ){
		return null;
	}
	$parts = explode(',', $sig_header);
	$ts = null;
	$signatures = [];
	foreach( $parts as $p ){
		$p = trim($p);
		if( strncmp($p, 't=', 2) === 0 ){
			$ts = substr($p, 2);
		}elseif( strncmp($p, 'v1=', 3) === 0 ){
			$signatures[] = substr($p, 3);
		}
	}
	if( $ts === null || !$signatures ){
		return null;
	}
	if( abs(time() - intval($ts)) > 600 ){
		return null;
	}
	$signed = $ts . '.' . $payload;
	$expected = hash_hmac('sha256', $signed, $secret);
	foreach( $signatures as $sig ){
		if( hash_equals($expected, $sig) ){
			return json_decode($payload, true);
		}
	}
	return null;
}


function f_api_pay_webhook_apply_success($pi_id){
	if( $pi_id === '' ){
		return;
	}
	$esc = f_db_sql_string_escape($pi_id);
	$rows = f_db_select(
		'SELECT * FROM `pay_transaction` WHERE `txn_id` = "' . $esc . '" LIMIT 1'
	);
	if( empty($rows) && f_pay_table_has_column('stripe_intent_id') ){
		$rows = f_db_select(
			'SELECT * FROM `pay_transaction` WHERE `stripe_intent_id` = "' . $esc . '" LIMIT 1'
		);
	}
	if( empty($rows) ){
		return;
	}
	$row = $rows[0];
	if( strtolower((string)($row['payment_status'] ?? '')) === 'success' ){
		return;
	}
	$now = date('Y-m-d H:i:s');
	f_db_update_smart('pay_transaction', ['_id' => $row['_id']], [
		'payment_status' => 'success',
		'update_date' => $now,
	]);

	$ads_id = 0;
	if( f_pay_table_has_column('ads_id') && !empty($row['ads_id']) ){
		$ads_id = intval($row['ads_id']);
	}
	$service = strtolower((string)($row['service_type'] ?? ''));

	if( $ads_id > 0 && $service !== '' && f_db_table_exists('ads') ){
		$col_top = f_db_select('SHOW COLUMNS FROM `ads` LIKE ' . f_db_sql_value('is_top_until'));
		$col_vip = f_db_select('SHOW COLUMNS FROM `ads` LIKE ' . f_db_sql_value('is_vip_until'));
		if( $service === 'vip' && !empty($col_vip) ){
			f_db_query(
				'UPDATE `ads` SET `is_vip_until` = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE `_id` = ' . $ads_id . ' LIMIT 1'
			);
		}elseif( $service === 'top' && !empty($col_top) ){
			f_db_query(
				'UPDATE `ads` SET `is_top_until` = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE `_id` = ' . $ads_id . ' LIMIT 1'
			);
		}
	}

	$uid = 0;
	if( f_pay_table_has_column('user_id') && !empty($row['user_id']) ){
		$uid = intval($row['user_id']);
	}
	if( $uid > 0 ){
		$urow = f_db_get_user(['_id' => $uid]);
		$em = is_array($urow) ? trim((string)($urow['email'] ?? '')) : '';
		if( $em !== '' ){
			$html = '<p>' . f_translate('Your payment was successful. Your ad promotion is now active on Market365.') . '</p>';
			f_email_send($em, f_translate('Payment confirmed — Market365'), $html, 'main');
		}
	}
}


function f_api_pay_webhook_dispatch(){
	header('Content-Type: application/json; charset=utf-8');
	$secret = $GLOBALS['WEB_JSON']['api_json']['stripe_webhook_secret'] ?? '';
	$payload = file_get_contents('php://input');
	$sig = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
	$event = f_stripe_webhook_verify_payload($payload, $sig, $secret);
	if( !is_array($event) ){
		http_response_code(400);
		echo json_encode(['error' => 'invalid signature']);
		return;
	}
	$type = (string)($event['type'] ?? '');
	if( $type === 'payment_intent.succeeded' ){
		$obj = $event['data']['object'] ?? [];
		if( is_array($obj) && !empty($obj['id']) ){
			f_api_pay_webhook_apply_success((string)$obj['id']);
		}
	}
	http_response_code(200);
	echo json_encode(['received' => true]);
}
