<?php


// Набор функций
$gl_api_func_json = [
	"get"			=> "f_api_get_ads",
	"get_list"		=> "f_api_get_list_ads",
	//"get_line"	=> "f_api_get_ads_line",
	//"get_top"		=> "f_api_get_ads_top",
	
	"save"			=> "f_api_ads_save",
	//"get"			=> "f_api_ads_get",
	"delete"		=> "f_api_ads_delete",
];



// Сохранение о пользователи
function f_api_get_list_ads($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	// Date Fix для борьбы со сдвигом страниц при сортировке
	
	/*
	$item_json = f_db_get_ads( ['_id_str' => $ARGS['_id_str']] );
	
	if( !isset($item_json) ){
		$response_json['error'] = 'Not found';
		return $response_json;
	}
	*/
	
	$json_template = [
		'html_img_src'		=> '/public/ad_default.jpg',
		'title'				=> 'I will sell a new Luxury segment car directly from the salon',
		'html_price'		=> f_number_space(20000) . ' $',
		'html_city'			=> 'London',
		'html_date'			=> 'html_date',
		'html_favorite_on'	=> 'Today',
		'html_link_ad'		=> f_page_link('ads_item') . '/' . f_seo_text_to_url('I will sell a new Luxury segment car directly from the salon', 100) . '-' . $i
	];
	
	$arr_item = [];
	
	for($i=0; $i<20; $i++){
		$arr_item[] = $json_template;
	}
	
	$response_json['arr_item'] = $arr_item;
	
	return $response_json;
}













// Сохранение о пользователи
function f_api_ads_get($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	$item_json = f_db_get_ads( ['_id_str' => $ARGS['_id_str']] );
	
	if( !isset($item_json) ){
		$response_json['error'] = 'Не найдена запись';
		return $response_json;
	}
	
	
	
	$response_json['data'] = [
		'_id_str' => $item_json['_id_str'],
		'title' => $item_json['title'],
		'description' => $item_json['description'],
		'html_price_min' => $item_json['html_price_min'],
		'html_lesson_dur' => $item_json['html_lesson_dur'],
		'html_lesson_count_week' => $item_json['html_lesson_count_week'],
		'html_city' => $item_json['html_city'],
		'html_age' => $item_json['html_age'],
		'html_category' => $item_json['html_category'],
		'html_people_count' => $item_json['html_people_count'],
		'html_level' => $item_json['html_level'],
		'html_time' => $item_json['html_time'],
		'html_lang_edu' => $item_json['html_lang_edu'],
		'html_week_of_day' => $item_json['html_week_of_day'],
		'html_equipment' => $item_json['html_equipment'],
		'html_support_invalid' => $item_json['html_support_invalid'],
		'address' => $item_json['address'],
		'html_lesson_count_min' => $item_json['html_lesson_count_min'],
		
		'html_user_name' => $item_json['html_user_name'],
		'html_user_phone' => $item_json['html_user_phone'],
		'html_user_login' => $item_json['html_user_login'],
		'html_user_link' => $item_json['html_user_link'],
		
	];
	
	return $response_json;
}



// Сохранение о пользователи
function f_api_ads_delete($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	if(f_user_get()['type'] == 'user'){
		$response_json['error'] = 'Нет доступа';
	}
	
	$item_json = f_db_get_ads( ['_id_str' => $ARGS['_id_str']] );
		
	if( !isset($item_json) ){
		$response_json['error'] = 'Не найдена запись';
		return $response_json;
	}
	
	if( $item_json['user_id'] != f_user_get()['_id'] && $is_admin ){
		$response_json['error'] = 'Нет доступа';
		return $response_json;
	}
	
	f_db_update_smart( "ads", ["_id" => $item_json['_id']], ['delete_on' => 1, 'delete_date' => date('Y-m-d H:i:s')] );
	
	return $response_json;
}

// Сохранение о пользователи
function f_api_ads_save($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	
	$is_new = $ARGS['_id_str'] ? false : true;
	$is_admin = f_user_get()['type'] == 'admin' ? true : false;
	
	
	if(f_user_get()['type'] == 'user'){
		$response_json['error'] = 'No access';
	}
		
	if( $is_new == false ){
		$item_json = f_db_get_ads( ['_id_str' => $ARGS['_id_str']] );
		
		if( !isset($item_json) ){
			$response_json['error'] = 'No record found';
			return $response_json;
		}
		
		if( $item_json['user_id'] != f_user_get()['_id'] && !$is_admin ){
			$response_json['error'] = 'No access';
			return $response_json;
		}
	}
	
	
	$update_json = [];
	$update_json['title'] = mb_substr( trim($ARGS['title']), 0, 100);
	$update_json['description'] = mb_substr( trim($ARGS['description']), 0, 500);
	$update_json['phone'] = mb_substr( f_number_parse($ARGS['phone']), 0, 100);
	//$update_json['gps_address'] = mb_substr( trim($ARGS['gps_address']), 0, 100) ?: null;
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
	
	
	
	//$response_json['error'] = f_check_diap_number('Количество людей', $update_json['people_count_min'], $update_json['people_count_max']);
	//if( $response_json['error'] ){ return $response_json; }
	
	
	
	if( mb_strlen($update_json['title']) == 0 ){
		$response_json['error'] = 'Title cannot be empty';
		return $response_json;
	}
	
	//f_test( $update_json );
	
	$response_json['update'] = $update_json;
	
	if( $is_new == true ){
		$update_json['_create_user_id'] = f_user_get()['_id'];
		$update_json['user_id'] = f_user_get()['_id'];
		//$response_json['redirect'] = '/ads/' . f_num_encode( f_db_insert('ads', $update_json) );
	}else{
		
		if( f_user_get()['type'] == 'admin' ){
			$update_json['_create_date'] = f_db_value_str_date($ARGS['_create_date']);
		}
		
		f_db_update_smart( "ads", ["_id" => $item_json['_id']], $update_json );
	}
	
	return $response_json;
}














?>
