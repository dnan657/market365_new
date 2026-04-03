<?php

$gl_api_func_json = [
	'save'     => 'f_api_store_save',
	'get'      => 'f_api_store_get',
	'get_list' => 'f_api_store_get_list',
];


function f_api_store_require_b2b(){
	$u = f_user_get();
	if( !is_array($u) || empty($u['_id']) ){
		return ['error' => 'Требуется вход', 'error_code' => -1];
	}
	$t = trim((string)($u['user_type'] ?? ''));
	if( $t === '' ){
		$t = (string)($u['type'] ?? '');
	}
	$ok = in_array($t, ['business', 'b2b', 'admin'], true) || ($u['type'] ?? '') === 'admin';
	if( !$ok ){
		return ['error' => 'Доступно только для бизнес-аккаунта', 'error_code' => -2];
	}
	return null;
}


function f_api_store_slug_normalize($slug){
	$slug = strtolower(trim((string)$slug));
	$slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug);
	$slug = trim($slug, '-');
	return $slug;
}


function f_api_store_save($ARGS, $_web = null){
	$err = f_api_store_require_b2b();
	if( $err ){
		return $err;
	}
	if( !f_db_table_exists('store') ){
		return ['error' => 'Магазины недоступны (нет таблицы store)', 'error_code' => -3];
	}

	$response_json = [
		'error' => '',
		'error_code' => 0,
		'slug' => '',
		'_id' => 0,
	];

	$me = intval(f_user_get()['_id']);
	$name = mb_substr(trim((string)($ARGS['name'] ?? '')), 0, 200);
	$slug = f_api_store_slug_normalize($ARGS['slug'] ?? '');
	$description = mb_substr(trim((string)($ARGS['description'] ?? '')), 0, 5000);
	$phone = mb_substr(trim((string)($ARGS['phone'] ?? '')), 0, 30);
	$address = mb_substr(trim((string)($ARGS['address'] ?? '')), 0, 300);
	$city_id = intval($ARGS['city_id'] ?? 0) ?: null;
	$logo_upload_id = intval($ARGS['logo_upload_id'] ?? 0) ?: null;
	$banner_upload_id = intval($ARGS['banner_upload_id'] ?? 0) ?: null;

	if( $name === '' || $slug === '' || strlen($slug) < 3 ){
		$response_json['error'] = 'Укажите название и slug (мин. 3 символа, латиница и дефис)';
		$response_json['error_code'] = -4;
		return $response_json;
	}

	$mine = f_db_select(
		'SELECT `_id` FROM `store` WHERE `user_id` = ' . $me . ' LIMIT 1'
	);
	$existing_id = !empty($mine) ? intval($mine[0]['_id']) : 0;

	$by_slug = f_db_select(
		'SELECT `_id` FROM `store` WHERE `slug` = ' . f_db_sql_value($slug) . ' LIMIT 1'
	);
	if( !empty($by_slug) ){
		$slug_id = intval($by_slug[0]['_id'] ?? 0);
		if( $existing_id === 0 || $slug_id !== $existing_id ){
			$response_json['error'] = 'Такой адрес магазина (slug) уже занят';
			$response_json['error_code'] = -5;
			return $response_json;
		}
	}

	$data = [
		'name' => $name,
		'slug' => $slug,
		'description' => $description !== '' ? $description : null,
		'phone' => $phone !== '' ? $phone : null,
		'address' => $address !== '' ? $address : null,
		'city_id' => $city_id,
		'logo_upload_id' => $logo_upload_id,
		'banner_upload_id' => $banner_upload_id,
	];

	if( $existing_id > 0 ){
		f_db_update_smart('store', ['_id' => $existing_id], $data);
		$response_json['_id'] = $existing_id;
	}else{
		$data['user_id'] = $me;
		$new_id = f_db_insert('store', $data);
		$response_json['_id'] = (int)$new_id;
	}

	$response_json['slug'] = $slug;
	$response_json['shop_url'] = f_page_link('shop') . '/' . rawurlencode($slug);
	return $response_json;
}


function f_api_store_get($ARGS, $_web = null){
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'store' => null,
	];
	if( !f_db_table_exists('store') ){
		$response_json['error'] = 'Нет таблицы store';
		return $response_json;
	}
	$slug = f_api_store_slug_normalize($ARGS['slug'] ?? '');
	if( $slug === '' ){
		$response_json['error'] = 'Не указан slug';
		$response_json['error_code'] = -2;
		return $response_json;
	}
	$rows = f_db_select(
		'SELECT s.*, u.`name` AS `owner_name`, u.`email` AS `owner_email`
		FROM `store` s
		LEFT JOIN `user` u ON u.`_id` = s.`user_id`
		WHERE s.`slug` = ' . f_db_sql_value($slug) . ' LIMIT 1'
	);
	if( empty($rows) ){
		$response_json['error'] = 'Магазин не найден';
		$response_json['error_code'] = -3;
		return $response_json;
	}
	$s = $rows[0];
	$logo_url = '';
	$banner_url = '';
	if( !empty($s['logo_upload_id']) ){
		$upr = f_db_select_get('upload', ['_id' => intval($s['logo_upload_id'])], 1);
		if( !empty($upr[0]) ){
			$logo_url = f_db_ads_img_public_url((string)($upr[0]['img_jpg_path'] ?? ''), '');
		}
	}
	if( !empty($s['banner_upload_id']) ){
		$upr = f_db_select_get('upload', ['_id' => intval($s['banner_upload_id'])], 1);
		if( !empty($upr[0]) ){
			$banner_url = f_db_ads_img_public_url((string)($upr[0]['img_jpg_path'] ?? ''), '');
		}
	}
	$response_json['store'] = [
		'name' => (string)($s['name'] ?? ''),
		'slug' => (string)($s['slug'] ?? ''),
		'description' => (string)($s['description'] ?? ''),
		'phone' => (string)($s['phone'] ?? ''),
		'address' => (string)($s['address'] ?? ''),
		'city_id' => $s['city_id'] ?? null,
		'html_logo' => $logo_url,
		'html_banner' => $banner_url,
		'owner_name' => (string)($s['owner_name'] ?? ''),
	];
	return $response_json;
}


function f_api_store_get_list($ARGS, $_web = null){
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'arr_item' => [],
	];
	if( !f_db_table_exists('store') || !f_db_table_exists('ads') ){
		return $response_json;
	}
	$slug = f_api_store_slug_normalize($ARGS['slug'] ?? '');
	if( $slug === '' ){
		$response_json['error'] = 'Не указан slug';
		$response_json['error_code'] = -2;
		return $response_json;
	}
	$st = f_db_select(
		'SELECT `_id` FROM `store` WHERE `slug` = ' . f_db_sql_value($slug) . ' LIMIT 1'
	);
	if( empty($st) ){
		$response_json['error'] = 'Магазин не найден';
		$response_json['error_code'] = -3;
		return $response_json;
	}
	$store_id = intval($st[0]['_id']);

	$has_store_col = !empty(f_db_select('SHOW COLUMNS FROM `ads` LIKE ' . f_db_sql_value('store_id')));

	$sql_from = "
		FROM `ads` AS ads
		LEFT JOIN `city` AS ct ON ct.`_id` = ads.`city_id`
		LEFT JOIN (
			SELECT ai1.`ads_id`, ai1.`jpg_path`, ai1.`webp_path`
			FROM `ads_img` ai1
			INNER JOIN (
				SELECT `ads_id`, MIN(`_id`) AS `mid` FROM `ads_img` GROUP BY `ads_id`
			) t ON t.`mid` = ai1.`_id` AND t.`ads_id` = ai1.`ads_id`
		) AS img ON img.`ads_id` = ads.`_id`
		WHERE ads.`delete_on` = 0 AND ads.`publication_on` = 1
	";
	$owner_sub = '(SELECT `user_id` FROM `store` WHERE `_id` = ' . $store_id . ' LIMIT 1)';
	if( $has_store_col ){
		$sql_from .= ' AND (ads.`store_id` = ' . $store_id . ' OR (ads.`store_id` IS NULL AND ads.`user_id` = ' . $owner_sub . '))';
	}else{
		$sql_from .= ' AND ads.`user_id` = ' . $owner_sub;
	}

	$sql = "
		SELECT
			ads.`_id`,
			ads.`title`,
			ads.`price`,
			ads.`price_currency`,
			ads.`_create_date`,
			ads.`city_id`,
			img.`jpg_path` AS `thumb_jpg_path`,
			img.`webp_path` AS `thumb_webp_path`,
			ct.`title_en` AS `city_title_en`
		" . $sql_from . "
		ORDER BY ads.`_create_date` DESC
		LIMIT 200
	";

	$rows = f_db_select($sql);
	if( !is_array($rows) ){
		return $response_json;
	}

	foreach( $rows as $row ){
		$title = $row['title'] ?? '';
		$price = isset($row['price']) ? floatval($row['price']) : 0;
		$curr = trim((string)($row['price_currency'] ?? ''));
		if( $curr === '' ){
			$curr = f_page_currency();
		}
		$city_label = trim((string)($row['city_title_en'] ?? ''));
		if( $city_label === '' && !empty($row['city_id']) ){
			$city_label = f_translate('City') . ' #' . intval($row['city_id']);
		}
		if( $city_label === '' ){
			$city_label = f_translate('не указан');
		}
		$response_json['arr_item'][] = [
			'html_img_src' => f_db_ads_img_public_url($row['thumb_jpg_path'] ?? '', $row['thumb_webp_path'] ?? ''),
			'title' => $title,
			'html_price' => f_number_space($price) . ' ' . $curr,
			'html_city' => $city_label,
			'html_date' => f_html_date_to_last_day($row['_create_date'] ?? ''),
			'html_favorite_on' => 0,
			'html_link_ad' => f_page_link('ads_item') . '/' . f_seo_text_to_url($title, 100) . '-' . intval($row['_id']),
		];
	}
	return $response_json;
}
