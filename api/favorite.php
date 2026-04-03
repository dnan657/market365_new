<?php

$gl_api_func_json = [
	'toggle'   => 'f_api_favorite_toggle',
	'get_list' => 'f_api_favorite_get_list',
];


function f_api_favorite_require_user(){
	$u = f_user_get();
	if( !is_array($u) || empty($u['_id']) ){
		return [
			'error' => 'Требуется вход',
			'error_code' => -1,
		];
	}
	return null;
}


function f_api_favorite_toggle($ARGS){
	$err = f_api_favorite_require_user();
	if( $err ){
		return $err;
	}
	$me = intval(f_user_get()['_id']);
	$ads_id = intval($ARGS['ads_id'] ?? 0);
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'is_favorite' => false,
	];

	if( $ads_id <= 0 ){
		$response_json['error'] = 'Не указано объявление';
		$response_json['error_code'] = -2;
		return $response_json;
	}

	if( !f_db_table_exists('user_favorite') ){
		$response_json['error'] = 'Избранное недоступно';
		return $response_json;
	}

	$ad = f_db_select(
		'SELECT `_id` FROM `ads` WHERE `_id` = ' . $ads_id . ' AND `delete_on` = 0 LIMIT 1'
	);
	if( empty($ad) ){
		$response_json['error'] = 'Объявление не найдено';
		return $response_json;
	}

	$ex = f_db_select(
		'SELECT `_id` FROM `user_favorite` WHERE `user_id` = ' . $me . ' AND `ads_id` = ' . $ads_id . ' LIMIT 1'
	);
	if( !empty($ex) ){
		f_db_query(
			'DELETE FROM `user_favorite` WHERE `user_id` = ' . $me . ' AND `ads_id` = ' . $ads_id . ' LIMIT 1'
		);
		$response_json['is_favorite'] = false;
	} else {
		f_db_insert('user_favorite', [
			'user_id' => $me,
			'ads_id' => $ads_id,
		]);
		$response_json['is_favorite'] = true;
	}

	return $response_json;
}


function f_api_favorite_get_list($ARGS){
	$err = f_api_favorite_require_user();
	if( $err ){
		return $err;
	}
	$me = intval(f_user_get()['_id']);
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'arr_item' => [],
	];

	if( !f_db_table_exists('user_favorite') ){
		return $response_json;
	}

	$sql_from = "
		FROM `user_favorite` uf
		INNER JOIN `ads` AS ads ON ads.`_id` = uf.`ads_id`
		LEFT JOIN `city` AS ct ON ct.`_id` = ads.`city_id`
		LEFT JOIN (
			SELECT ai1.`ads_id`, ai1.`jpg_path`, ai1.`webp_path`
			FROM `ads_img` ai1
			INNER JOIN (
				SELECT `ads_id`, MIN(`_id`) AS `mid` FROM `ads_img` GROUP BY `ads_id`
			) t ON t.`mid` = ai1.`_id` AND t.`ads_id` = ai1.`ads_id`
		) AS img ON img.`ads_id` = ads.`_id`
		WHERE uf.`user_id` = $me
		AND ads.`delete_on` = 0
		AND ads.`publication_on` = 1
		ORDER BY uf.`_create_date` DESC
	";

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
			'html_favorite_on' => 1,
			'html_link_ad' => f_page_link('ads_item') . '/' . f_seo_text_to_url($title, 100) . '-' . intval($row['_id']),
			'ads_id' => intval($row['_id']),
		];
	}

	return $response_json;
}
