<?php

$gl_api_func_json = [
	'get_list'       => 'f_api_chat_get_list',
	'get_messages'   => 'f_api_chat_get_messages',
	'send'           => 'f_api_chat_send',
	'unread_count'   => 'f_api_chat_unread_count',
];


function f_api_chat_require_user(){
	$u = f_user_get();
	if( !is_array($u) || empty($u['_id']) ){
		return [
			'error' => 'Требуется вход',
			'error_code' => -1,
		];
	}
	return null;
}


function f_api_chat_abs_url($path){
	$host = $_SERVER['HTTP_HOST'] ?? '';
	$path = $path ?: '/';
	return 'https://' . $host . $path;
}


function f_api_chat_notify_email($recipient_user_id, $sender_name, $ads_title, $snippet){
	$row = f_db_get_user(['_id' => intval($recipient_user_id)]);
	if( !is_array($row) || trim((string)($row['email'] ?? '')) === '' ){
		return;
	}
	$title_safe = htmlspecialchars((string)$ads_title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	$name_safe = htmlspecialchars((string)$sender_name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	$snippet_safe = htmlspecialchars(mb_substr((string)$snippet, 0, 200), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	$url = f_api_chat_abs_url(f_page_link('user_messages'));
	$html = '<p>' . f_translate('New message on Market365') . '</p>'
		. '<p><strong>' . $title_safe . '</strong></p>'
		. '<p>' . $name_safe . ': ' . $snippet_safe . '</p>'
		. '<p><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . f_translate('Open messages') . '</a></p>';
	f_email_send(trim($row['email']), f_translate('New message on your listing'), $html, 'main');
}


function f_api_chat_get_list($ARGS){
	$err = f_api_chat_require_user();
	if( $err ){
		return $err;
	}
	$me = intval(f_user_get()['_id']);
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'arr_chat' => [],
	];

	if( !f_db_table_exists('chat') || !f_db_table_exists('chat_message') ){
		return $response_json;
	}

	$sql = "
		SELECT
			c.`_id` AS `chat_id`,
			c.`_create_date` AS `chat_create_date`,
			c.`ads_id`,
			c.`user_buyer_id`,
			c.`user_seller_id`,
			a.`title` AS `ads_title`,
			a.`price` AS `ads_price`,
			a.`price_currency` AS `ads_price_currency`,
			img.`jpg_path` AS `thumb_jpg_path`,
			img.`webp_path` AS `thumb_webp_path`,
			IF(c.`user_buyer_id` = $me, c.`user_seller_id`, c.`user_buyer_id`) AS `peer_user_id`,
			u.`name` AS `peer_name`,
			u.`email` AS `peer_email`,
			lm.`message_text` AS `last_message_text`,
			lm.`_create_date` AS `last_message_date`,
			(
				SELECT COUNT(*) FROM `chat_message` cm2
				WHERE cm2.`chat_id` = c.`_id`
				AND cm2.`user_sender_id` <> $me
				AND cm2.`is_read` = 0
			) AS `unread_count`
		FROM `chat` c
		INNER JOIN `ads` a ON a.`_id` = c.`ads_id`
		LEFT JOIN `user` u ON u.`_id` = IF(c.`user_buyer_id` = $me, c.`user_seller_id`, c.`user_buyer_id`)
		LEFT JOIN (
			SELECT ai1.`ads_id`, ai1.`jpg_path`, ai1.`webp_path`
			FROM `ads_img` ai1
			INNER JOIN (
				SELECT `ads_id`, MIN(`_id`) AS `mid` FROM `ads_img` GROUP BY `ads_id`
			) t ON t.`mid` = ai1.`_id` AND t.`ads_id` = ai1.`ads_id`
		) img ON img.`ads_id` = a.`_id`
		LEFT JOIN (
			SELECT m.`chat_id`, m.`message_text`, m.`_create_date`
			FROM `chat_message` m
			INNER JOIN (
				SELECT `chat_id`, MAX(`_id`) AS `max_id` FROM `chat_message` GROUP BY `chat_id`
			) mx ON mx.`max_id` = m.`_id` AND mx.`chat_id` = m.`chat_id`
		) lm ON lm.`chat_id` = c.`_id`
		WHERE (c.`user_buyer_id` = $me OR c.`user_seller_id` = $me)
		AND a.`delete_on` = 0
		ORDER BY COALESCE(lm.`_create_date`, c.`_create_date`) DESC
	";

	$rows = f_db_select($sql);
	if( !is_array($rows) ){
		return $response_json;
	}

	foreach( $rows as $row ){
		$peer = trim((string)($row['peer_name'] ?? ''));
		if( $peer === '' ){
			$peer = trim((string)($row['peer_email'] ?? ''));
			if( $peer !== '' ){
				$peer = explode('@', $peer)[0];
			}
		}
		$response_json['arr_chat'][] = [
			'chat_id' => intval($row['chat_id']),
			'ads_id' => intval($row['ads_id']),
			'ads_title' => (string)($row['ads_title'] ?? ''),
			'ads_thumb' => f_db_ads_img_public_url($row['thumb_jpg_path'] ?? '', $row['thumb_webp_path'] ?? ''),
			'ads_price_html' => f_number_space(floatval($row['ads_price'] ?? 0)) . ' '
				. (trim((string)($row['ads_price_currency'] ?? '')) ?: f_page_currency()),
			'peer_name' => $peer ?: f_translate('User'),
			'peer_user_id' => intval($row['peer_user_id'] ?? 0),
			'last_message_text' => (string)($row['last_message_text'] ?? ''),
			'last_message_date' => (string)($row['last_message_date'] ?? ''),
			'unread_count' => intval($row['unread_count'] ?? 0),
			'html_link_ad' => f_page_link('ads_item') . '/' . f_seo_text_to_url((string)($row['ads_title'] ?? ''), 100) . '-' . intval($row['ads_id']),
		];
	}

	return $response_json;
}


function f_api_chat_get_messages($ARGS){
	$err = f_api_chat_require_user();
	if( $err ){
		return $err;
	}
	$me = intval(f_user_get()['_id']);
	$chat_id = intval($ARGS['chat_id'] ?? 0);
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'arr_message' => [],
		'chat' => [],
	];

	if( $chat_id <= 0 ){
		$response_json['error'] = 'Не указан чат';
		$response_json['error_code'] = -2;
		return $response_json;
	}

	if( !f_db_table_exists('chat') || !f_db_table_exists('chat_message') ){
		$response_json['error'] = 'Чат недоступен';
		return $response_json;
	}

	$ch = f_db_select(
		'SELECT * FROM `chat` WHERE `_id` = ' . $chat_id . ' LIMIT 1'
	);
	if( empty($ch) ){
		$response_json['error'] = 'Чат не найден';
		$response_json['error_code'] = -3;
		return $response_json;
	}
	$c = $ch[0];
	if( intval($c['user_buyer_id']) !== $me && intval($c['user_seller_id']) !== $me ){
		$response_json['error'] = 'Нет доступа';
		$response_json['error_code'] = -4;
		return $response_json;
	}

	f_db_query(
		'UPDATE `chat_message` SET `is_read` = 1 WHERE `chat_id` = ' . $chat_id
		. ' AND `user_sender_id` <> ' . $me . ' AND `is_read` = 0'
	);

	$ads = f_db_select('SELECT `title`, `price`, `price_currency` FROM `ads` WHERE `_id` = ' . intval($c['ads_id']) . ' LIMIT 1');
	$ad = !empty($ads) ? $ads[0] : [];
	$aid = intval($c['ads_id']);
	$thumb_row = f_db_select(
		'SELECT `jpg_path`, `webp_path` FROM `ads_img` WHERE `ads_id` = ' . $aid . ' ORDER BY `_id` ASC LIMIT 1'
	);
	$thumb_jpg = $thumb_row[0]['jpg_path'] ?? '';
	$thumb_webp = $thumb_row[0]['webp_path'] ?? '';

	$response_json['chat'] = [
		'chat_id' => $chat_id,
		'ads_id' => $aid,
		'ads_title' => (string)($ad['title'] ?? ''),
		'ads_thumb' => f_db_ads_img_public_url($thumb_jpg, $thumb_webp),
		'html_link_ad' => f_page_link('ads_item') . '/' . f_seo_text_to_url((string)($ad['title'] ?? ''), 100) . '-' . $aid,
		'user_buyer_id' => intval($c['user_buyer_id']),
		'user_seller_id' => intval($c['user_seller_id']),
	];

	$msgs = f_db_select(
		'SELECT `_id`, `user_sender_id`, `message_text`, `_create_date`, `is_read`'
		. ' FROM `chat_message` WHERE `chat_id` = ' . $chat_id . ' ORDER BY `_id` ASC'
	);
	if( is_array($msgs) ){
		foreach( $msgs as $m ){
			$response_json['arr_message'][] = [
				'_id' => intval($m['_id']),
				'user_sender_id' => intval($m['user_sender_id']),
				'message_text' => (string)$m['message_text'],
				'_create_date' => (string)$m['_create_date'],
				'is_mine' => intval($m['user_sender_id']) === $me,
			];
		}
	}

	return $response_json;
}


function f_api_chat_send($ARGS){
	$err = f_api_chat_require_user();
	if( $err ){
		return $err;
	}
	$me = intval(f_user_get()['_id']);
	$text = trim((string)($ARGS['message_text'] ?? ''));
	if( $text === '' ){
		return [
			'error' => 'Пустое сообщение',
			'error_code' => -2,
		];
	}
	if( mb_strlen($text) > 8000 ){
		$text = mb_substr($text, 0, 8000);
	}

	$response_json = [
		'error' => '',
		'error_code' => 0,
		'chat_id' => 0,
		'message' => [],
	];

	if( !f_db_table_exists('chat') || !f_db_table_exists('chat_message') ){
		$response_json['error'] = 'Чат недоступен';
		return $response_json;
	}

	$chat_id = intval($ARGS['chat_id'] ?? 0);
	$ads_id = intval($ARGS['ads_id'] ?? 0);

	if( $chat_id > 0 ){
		$ch = f_db_select('SELECT * FROM `chat` WHERE `_id` = ' . $chat_id . ' LIMIT 1');
		if( empty($ch) ){
			$response_json['error'] = 'Чат не найден';
			return $response_json;
		}
		$c = $ch[0];
		if( intval($c['user_buyer_id']) !== $me && intval($c['user_seller_id']) !== $me ){
			$response_json['error'] = 'Нет доступа';
			return $response_json;
		}
	} else {
		if( $ads_id <= 0 ){
			$response_json['error'] = 'Не указано объявление';
			$response_json['error_code'] = -3;
			return $response_json;
		}
		$ads_rows = f_db_select(
			'SELECT `_id`, `user_id`, `title`, `delete_on`, `publication_on` FROM `ads` WHERE `_id` = ' . $ads_id . ' LIMIT 1'
		);
		if( empty($ads_rows) ){
			$response_json['error'] = 'Объявление не найдено';
			return $response_json;
		}
		$ad = $ads_rows[0];
		if( intval($ad['delete_on'] ?? 0) !== 0 ){
			$response_json['error'] = 'Объявление удалено';
			return $response_json;
		}
		$seller_id = intval($ad['user_id'] ?? 0);
		if( $seller_id <= 0 || $seller_id === $me ){
			$response_json['error'] = 'Нельзя написать себе';
			return $response_json;
		}
		$buyer_id = $me;
		$exist = f_db_select(
			'SELECT `_id` FROM `chat` WHERE `ads_id` = ' . $ads_id
			. ' AND `user_buyer_id` = ' . $buyer_id . ' AND `user_seller_id` = ' . $seller_id . ' LIMIT 1'
		);
		if( !empty($exist) ){
			$chat_id = intval($exist[0]['_id']);
		} else {
			$chat_id = intval(f_db_insert('chat', [
				'ads_id' => $ads_id,
				'user_buyer_id' => $buyer_id,
				'user_seller_id' => $seller_id,
			]));
		}
	}

	$msg_id = f_db_insert('chat_message', [
		'chat_id' => $chat_id,
		'user_sender_id' => $me,
		'message_text' => $text,
		'is_read' => 0,
	]);

	$response_json['chat_id'] = $chat_id;
	$response_json['message'] = [
		'_id' => intval($msg_id),
		'user_sender_id' => $me,
		'message_text' => $text,
		'_create_date' => date('Y-m-d H:i:s'),
		'is_mine' => true,
	];

	$ch2 = f_db_select('SELECT * FROM `chat` WHERE `_id` = ' . $chat_id . ' LIMIT 1');
	if( !empty($ch2) ){
		$c2 = $ch2[0];
		$recipient = intval($c2['user_seller_id']) === $me ? intval($c2['user_buyer_id']) : intval($c2['user_seller_id']);
		$ad_title = '';
		$adr = f_db_select('SELECT `title` FROM `ads` WHERE `_id` = ' . intval($c2['ads_id']) . ' LIMIT 1');
		if( !empty($adr) ){
			$ad_title = (string)$adr[0]['title'];
		}
		$sender = f_db_get_user(['_id' => $me]);
		$sender_name = is_array($sender) && trim((string)($sender['name'] ?? '')) !== ''
			? $sender['name']
			: (is_array($sender) ? explode('@', (string)($sender['email'] ?? ''))[0] : '');
		f_api_chat_notify_email($recipient, $sender_name, $ad_title, $text);
	}

	return $response_json;
}


function f_api_chat_unread_count($ARGS){
	$err = f_api_chat_require_user();
	if( $err ){
		return $err;
	}
	$me = intval(f_user_get()['_id']);
	return [
		'error' => '',
		'error_code' => 0,
		'count' => f_db_user_unread_chat_count($me),
	];
}
