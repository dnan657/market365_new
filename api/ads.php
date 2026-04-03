<?php


$gl_api_func_json = [
	"get"			=> "f_api_get_ads",
	"get_list"		=> "f_api_get_list_ads",
	"save"			=> "f_api_ads_save",
	"delete"		=> "f_api_ads_delete",
];


/**
 * Список объявлений для ленты (реальная БД, пагинация, фильтры, превью из ads_img).
 */
function f_api_get_list_ads($ARGS){
	$response_json = [
		'error' => '',
		'error_code' => 0,
		'arr_item' => [],
	];

	$page_num = max(1, intval($ARGS['page_num'] ?? 1));
	$page_size = f_number_if_min_max(1, intval($ARGS['page_size'] ?? 20), 100);
	$offset = ($page_num - 1) * $page_size;

	$sort = preg_replace('/[^a-z_]/', '', $ARGS['sort'] ?? 'newest');
	if( $sort === '' ){
		$sort = 'newest';
	}

	$filter_json = [];
	if( isset($ARGS['json_url_query']) ){
		if( is_string($ARGS['json_url_query']) && $ARGS['json_url_query'] !== '' ){
			$decoded = json_decode($ARGS['json_url_query'], true);
			if( is_array($decoded) ){
				$filter_json = $decoded;
			}
		}elseif( is_array($ARGS['json_url_query']) ){
			$filter_json = $ARGS['json_url_query'];
		}
	}

	static $ads_has_is_top = null;
	if( $ads_has_is_top === null ){
		$col = f_db_select("SHOW COLUMNS FROM `ads` LIKE 'is_top'");
		$ads_has_is_top = !empty($col);
	}

	static $ads_has_promo_until = null;
	if( $ads_has_promo_until === null ){
		$c1 = f_db_select("SHOW COLUMNS FROM `ads` LIKE 'is_top_until'");
		$ads_has_promo_until = !empty($c1);
	}

	$order_boost = '';
	if( $ads_has_promo_until ){
		$order_boost = '(ads.`is_top_until` IS NOT NULL AND ads.`is_top_until` > NOW()) DESC, (ads.`is_vip_until` IS NOT NULL AND ads.`is_vip_until` > NOW()) DESC, ';
	}

	$order_top = $ads_has_is_top ? 'COALESCE(ads.`is_top`,0) DESC, ' : '';
	if( $sort === 'price_asc' ){
		$order_sql = 'ORDER BY ' . $order_boost . $order_top . ' ads.`price` ASC, ads.`_id` DESC';
	}elseif( $sort === 'price_desc' ){
		$order_sql = 'ORDER BY ' . $order_boost . $order_top . ' ads.`price` DESC, ads.`_id` DESC';
	}else{
		$order_sql = 'ORDER BY ' . $order_boost . $order_top . ' ads.`_create_date` DESC, ads.`_id` DESC';
	}

	$where = [
		'ads.`delete_on` = 0',
		'ads.`publication_on` = 1',
	];

	$category_id = intval($ARGS['category_id'] ?? 0);
	if( $category_id > 0 ){
		$cat_ids = f_db_ads_category_descendant_ids($category_id);
		if( $cat_ids ){
			$in = implode(',', array_map('intval', $cat_ids));
			$where[] = '(ads.`ads_category_id` IN (' . $in . ') OR ads.`ads_category_1_id` IN (' . $in . ') OR ads.`ads_category_2_id` IN (' . $in . ') OR ads.`ads_category_3_id` IN (' . $in . '))';
		}
	}

	$search_title = trim((string)($ARGS['ads_search_title'] ?? ''));
	if( $search_title !== '' ){
		$esc = f_db_sql_string_escape($search_title);
		$where[] = "(ads.`title` LIKE '%" . $esc . "%' OR ads.`description` LIKE '%" . $esc . "%')";
	}

	$city_param = $ARGS['ads_search_city_id'] ?? '';
	if( $city_param !== '' && $city_param !== null ){
		$city_ids = array_filter(array_map('intval', explode(',', (string)$city_param)));
		if( $city_ids ){
			$where[] = 'ads.`city_id` IN (' . implode(',', $city_ids) . ')';
		}
	}

	foreach( $filter_json as $key => $val ){
		if( !is_numeric($key) ){
			continue;
		}
		$key_id = intval($key);
		if( $key_id <= 0 ){
			continue;
		}
		if( is_array($val) && (isset($val['min']) || isset($val['max'])) ){
			$sub = "SELECT `ads_item_id` FROM `ads_item_param_value` WHERE `ads_param_key_id` = " . $key_id . " AND (`_delete_on` IS NULL OR `_delete_on` = 0)";
			if( isset($val['min']) && $val['min'] !== '' && $val['min'] !== null ){
				$sub .= ' AND `value_int` >= ' . floatval($val['min']);
			}
			if( isset($val['max']) && $val['max'] !== '' && $val['max'] !== null ){
				$sub .= ' AND `value_int` <= ' . floatval($val['max']);
			}
			$where[] = 'ads.`_id` IN (' . $sub . ')';
		}elseif( is_array($val) ){
			$vids = array_filter(array_map('intval', $val));
			if( $vids ){
				$where[] = 'ads.`_id` IN (SELECT `ads_item_id` FROM `ads_item_param_value` WHERE `ads_param_key_id` = ' . $key_id . ' AND `ads_param_value_id` IN (' . implode(',', $vids) . ') AND (`_delete_on` IS NULL OR `_delete_on` = 0))';
			}
		}elseif( is_scalar($val) && $val !== '' ){
			$vid = intval($val);
			if( $vid > 0 ){
				$where[] = 'ads.`_id` IN (SELECT `ads_item_id` FROM `ads_item_param_value` WHERE `ads_param_key_id` = ' . $key_id . ' AND `ads_param_value_id` = ' . $vid . ' AND (`_delete_on` IS NULL OR `_delete_on` = 0))';
			}
		}
	}

	$where_sql = implode(' AND ', $where);

	$ads_select_extra = '';
	if( $ads_has_promo_until ){
		$ads_select_extra = ', ads.`is_top_until`, ads.`is_vip_until`';
	}

	$sql_from = "
		FROM `ads` AS ads
		LEFT JOIN `city` AS ct ON ct.`_id` = ads.`city_id`
		LEFT JOIN (
			SELECT ai1.`ads_id`, ai1.`jpg_path`, ai1.`webp_path`
			FROM `ads_img` ai1
			INNER JOIN (
				SELECT `ads_id`, MIN(`_id`) AS `mid`
				FROM `ads_img`
				GROUP BY `ads_id`
			) t ON t.`mid` = ai1.`_id` AND t.`ads_id` = ai1.`ads_id`
		) AS img ON img.`ads_id` = ads.`_id`
		WHERE " . $where_sql . "
	";

	$sql_count = 'SELECT COUNT(DISTINCT ads.`_id`) AS `cnt` ' . $sql_from;
	$count_row = f_db_select($sql_count);
	$total = isset($count_row[0]['cnt']) ? intval($count_row[0]['cnt']) : 0;

	$sql_data = "
		SELECT
			ads.`_id`,
			ads.`title`,
			ads.`price`,
			ads.`price_currency`,
			ads.`_create_date`,
			ads.`city_id`
			" . $ads_select_extra . ",
			img.`jpg_path` AS `thumb_jpg_path`,
			img.`webp_path` AS `thumb_webp_path`,
			ct.`title_en` AS `city_title_en`
		" . $sql_from . "
		" . $order_sql . "
		LIMIT " . intval($offset) . ", " . intval($page_size) . "
	";

	$rows = f_db_select($sql_data);

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

		$html_promo = '';
		if( $ads_has_promo_until ){
			$tt = $row['is_top_until'] ?? null;
			$vt = $row['is_vip_until'] ?? null;
			if( $tt && strtotime((string)$tt) > time() ){
				$html_promo = 'top';
			}elseif( $vt && strtotime((string)$vt) > time() ){
				$html_promo = 'vip';
			}
		}

		$response_json['arr_item'][] = [
			'html_img_src' => f_db_ads_img_public_url($row['thumb_jpg_path'] ?? '', $row['thumb_webp_path'] ?? ''),
			'title' => $title,
			'html_price' => f_number_space($price) . ' ' . $curr,
			'html_city' => $city_label,
			'html_date' => f_html_date_to_last_day($row['_create_date'] ?? ''),
			'html_favorite_on' => 0,
			'html_link_ad' => f_page_link('ads_item') . '/' . f_seo_text_to_url($title, 100) . '-' . intval($row['_id']),
			'html_promo' => $html_promo,
		];
	}

	$response_json['count_total'] = $total;
	$response_json['page_num'] = $page_num;
	$response_json['page_size'] = $page_size;
	$response_json['has_more'] = ($offset + count($rows)) < $total;

	return $response_json;
}


function f_api_get_ads($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	$item_json = f_db_get_ads( ['_id_str' => $ARGS['_id_str']] );
	
	if( empty($item_json) || !is_array($item_json) ){
		$response_json['error'] = 'Не найдена запись';
		return $response_json;
	}
	
	$price = isset($item_json['price']) ? floatval($item_json['price']) : 0;
	$curr = trim((string)($item_json['price_currency'] ?? ''));
	if( $curr === '' ){
		$curr = f_page_currency();
	}
	
	$data = [
		'_id_str' => $item_json['_id_str'] ?? f_num_encode($item_json['_id'] ?? 0),
		'_id' => intval($item_json['_id'] ?? 0),
		'title' => (string)($item_json['title'] ?? ''),
		'description' => (string)($item_json['description'] ?? ''),
		'price' => $price,
		'price_currency' => $curr,
		'html_price' => f_number_space($price) . ' ' . $curr,
		'phone' => (string)($item_json['phone'] ?? ''),
		'address' => (string)($item_json['address'] ?? ''),
		'publication_on' => intval($item_json['publication_on'] ?? 0),
		'user_id' => intval($item_json['user_id'] ?? 0),
		'html_city' => (string)($item_json['html_city'] ?? ''),
		'html_create_date' => (string)($item_json['html_create_date'] ?? ''),
		'gps_address_lat_lng' => (string)($item_json['gps_address_lat_lng'] ?? ''),
		'html_user_name' => (string)($item_json['html_user_name'] ?? ''),
		'html_user_phone' => (string)($item_json['html_user_phone'] ?? ''),
		'html_user_login' => (string)($item_json['html_user_login'] ?? ''),
		'html_user_link' => (string)($item_json['html_user_link'] ?? ''),
	];
	if( array_key_exists('is_top_until', $item_json) ){
		$data['is_top_until'] = $item_json['is_top_until'];
	}
	if( array_key_exists('is_vip_until', $item_json) ){
		$data['is_vip_until'] = $item_json['is_vip_until'];
	}
	
	$response_json['data'] = $data;
	
	return $response_json;
}


function f_api_ads_delete($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	$u = f_user_get();
	if( !$u ){
		$response_json['error'] = 'Нет доступа';
		return $response_json;
	}
	$is_admin = ($u['type'] == 'admin');
	
	$item_json = f_db_get_ads( ['_id_str' => $ARGS['_id_str']] );
		
	if( empty($item_json) || !is_array($item_json) ){
		$response_json['error'] = 'Не найдена запись';
		return $response_json;
	}
	
	if( intval($item_json['user_id']) !== intval($u['_id']) && !$is_admin ){
		$response_json['error'] = 'Нет доступа';
		return $response_json;
	}
	
	f_db_update_smart( "ads", ["_id" => $item_json['_id']], ['delete_on' => 1, 'delete_date' => date('Y-m-d H:i:s')] );
	
	return $response_json;
}


function f_api_ads_save($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	
	$is_new = $ARGS['_id_str'] ? false : true;
	$u = f_user_get();
	if( !$u ){
		$response_json['error'] = 'No access';
		return $response_json;
	}
	$is_admin = ($u['type'] == 'admin');
	
	
	if( $is_new == false ){
		$item_json = f_db_get_ads( ['_id_str' => $ARGS['_id_str']] );
		
		if( empty($item_json) || !is_array($item_json) ){
			$response_json['error'] = 'No record found';
			return $response_json;
		}
		
		if( intval($item_json['user_id']) !== intval($u['_id']) && !$is_admin ){
			$response_json['error'] = 'No access';
			return $response_json;
		}
	}
	
	
	$update_json = [];
	$update_json['title'] = mb_substr( trim($ARGS['title']), 0, 100);
	$update_json['description'] = mb_substr( trim($ARGS['description']), 0, 500);
	$update_json['phone'] = mb_substr( f_number_parse($ARGS['phone']), 0, 100);
	$update_json['address'] = mb_substr( trim($ARGS['address']), 0, 100);
	$update_json['publication_on'] = f_number_if_min_max( 0, intval($ARGS['publication_on']), 1 );
	$update_json['publication_date'] = $update_json['publication_on'] == 0 ? NULL : f_datetime_current();
	
	
	$update_json['city_type_id'] = f_number_if_min_max( 0, intval($ARGS['city_type_id']), 10000 ) ?: NULL;
	
	$update_json['support_invalid_on'] = f_number_if_min_max( 0, intval($ARGS['support_invalid_on']), 1 );
	
	$update_json['delete_on'] = f_number_if_min_max( 0, intval($ARGS['delete_on']), 1 );
	if( $update_json['delete_on'] ){
		$update_json['delete_date'] = date('Y-m-d H:i:s');
	}

	$update_json['gps_address'] = f_gps_validate($ARGS['gps_address']);
	
	$update_json['lang_edu_type_arr_id'] = f_valid_type_id( $ARGS['lang_edu_type_arr_id'] );
	$update_json['week_of_day_type_arr_id'] = f_valid_type_id( $ARGS['week_of_day_type_arr_id'] );
	
	$update_json['age_min'] = f_number_if_min_max( 0, intval($ARGS['age_min']), 1000000000000 );
	$update_json['age_max'] = f_number_if_min_max( 0, intval($ARGS['age_max']), 1000000000000 ) ?: NULL;
	
	$update_json['category_type_id'] = f_number_if_min_max( 0, intval($ARGS['category_type_id']), 1000000000000 ) ?: NULL;
	
	$update_json['lesson_count_min'] = f_number_if_min_max( 0, floatval($ARGS['lesson_count_min']), 10000 ) ?: NULL;
	
	$update_json['price'] = f_number_if_min_max( 0, floatval($ARGS['price']), 1000000000000 );
	
	
	if( mb_strlen($update_json['title']) == 0 ){
		$response_json['error'] = 'Title cannot be empty';
		return $response_json;
	}
	
	$response_json['update'] = $update_json;
	
	if( $is_new == true ){
		$update_json['_create_user_id'] = $u['_id'];
		$update_json['user_id'] = $u['_id'];
		$new_id = f_db_insert('ads', $update_json);
		$response_json['_id_str'] = f_num_encode($new_id);
		$response_json['redirect'] = f_page_link('ads_item') . '/' . f_seo_text_to_url($update_json['title'], 100) . '-' . intval($new_id);
	}else{
		
		if( $is_admin ){
			$update_json['_create_date'] = f_db_value_str_date($ARGS['_create_date']);
		}
		
		f_db_update_smart( "ads", ["_id" => $item_json['_id']], $update_json );
	}
	
	return $response_json;
}



?>
