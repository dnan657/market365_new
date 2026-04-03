<?php


// Набор функций
$gl_api_func_json = [
	"find"			=> "f_api_pay_find",
	"save"			=> "f_api_pay_save"
];


// Список пользователей
function f_api_pay_find($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	// Доступ - тоько для Админа
	if( in_array(f_pay_get()['type'], ['admin', 'school']) == false ){
		$response_json['error'] = 'Нет доступа';
		$response_json['error_code'] = -2;
		return $response_json;
	}
	
	$search = trim( $ARGS['search'] );
	$text = f_db_sql_value_only( $search );
	$number = f_db_sql_value( intval( $search ) );
	
	$sql_query = '
		SELECT
			`_id`,
			`_create_pay_id`,
			`password`,
			`name`,
			`email`,
			`login`,
			`phone`,
			`city`
		FROM
			`pay`
		WHERE
			(
				`_id` = ' . $number . '
				OR 
				`phone` = "' . $number . '"
				OR 
				`iin` = "' . $number . '"
				OR
				`name` LIKE "%' . $text . '%"
				OR 
				`email` LIKE "%' . $text . '%"
				OR 
				`login` LIKE "%@' . $text . '%"
			)
	';
	
	if( f_pay_get()['type'] == 'admin' ){
		
		$sql_query = '
			SELECT
				`_id`,
				`name`,
				`_create_date`,
				`_create_pay_id`,
				`password`,
				`type`,
				`email`,
				`login`,
				`iin`,
				`phone`,
				`city`,
				`address`,
				`gender`,
				`birthday_date`,
				`admin_comment`
			FROM
				`pay`
			WHERE
				(
					`_id` = ' . $number . '
					OR 
					`phone` = "' . $number . '"
					OR 
					`iin` = "' . $number . '"
					OR
					`name` LIKE "%' . $text . '%"
					OR 
					`email` LIKE "%' . $text . '%"
					OR 
					`login` LIKE "%@' . $text . '%"
				)
		';
	}
	
	
	if( f_pay_get()['type'] == 'school' ){
		$sql_query .= "\n AND  `type` = 'user' ";
	}
	
	$sql_query .= "\n LIMIT 20 ";
	
	
	$response_json['data_arr'] = f_db_select( $sql_query );
	
	for($i=0; $i<count($response_json['data_arr']); $i++){
		$response_json['data_arr'][$i]['_id_str'] = f_num_encode( $response_json['data_arr'][$i]['_id'] );
		
		if( isset($response_json['data_arr'][$i]['type']) ){
			$response_json['data_arr'][$i]['type_str'] = f_pay_type_ru( $response_json['data_arr'][$i]['type'] );
		}
		if( isset($response_json['data_arr'][$i]['phone']) ){
			$response_json['data_arr'][$i]['phone_str'] = f_phone_beauty( $response_json['data_arr'][$i]['phone'] );
		}
		if( isset($response_json['data_arr'][$i]['birthday_date']) ){
			$response_json['data_arr'][$i]['birthday_date_str'] = f_date_beauty( $response_json['data_arr'][$i]['birthday_date'] );
		}
		if( isset($response_json['data_arr'][$i]['_create_date']) ){
			$response_json['data_arr'][$i]['_create_date_str'] = f_datetime_beauty( $response_json['data_arr'][$i]['_create_date_str'] );
		}
		if( isset($response_json['data_arr'][$i]['city']) ){
			$response_json['data_arr'][$i]['city_str'] = f_pay_city_ru( $response_json['data_arr'][$i]['_create_date_str'] );
		}
		if( isset($response_json['data_arr'][$i]['city']) ){
			$response_json['data_arr'][$i]['city_str'] = f_pay_city_ru( $response_json['data_arr'][$i]['_create_date_str'] );
		}
		
		$response_json['data_arr'][$i]['html_create_pay_id'] = null;
		
		// Если это Админ или Родитель пользователя
		if( $response_json['data_arr'][$i]['_create_pay_id'] == f_pay_get()['_id'] || f_pay_get()['type'] == 'admin'){
			$response_json['data_arr'][$i]['html_create_pay_id'] = f_num_encode( $response_json['data_arr'][$i]['_create_pay_id'] );
		}else{
			unset( $response_json['data_arr'][$i]['password'] );
		}
		
		unset( $response_json['data_arr'][$i]['_id'] );
		unset( $response_json['data_arr'][$i]['_create_pay_id'] );
	}
	
	return $response_json;
}





// Сохранение о пользователи
function f_api_pay_save($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	// Доступ - только Админы и Директоры
	if( !in_array(f_user_get()['type'], ['admin', 'director'] ) ){
		$response_json['error'] = 'Нет доступа';
		$response_json['error_code'] = -2;
		return $response_json;
	}
	
	$is_new = $ARGS['_id_str'] ? true : false;	
	
	$item_json = f_db_select_get( 'pay', ['_id_str' => $ARGS['_id_str']] );
	
	if( !isset($item_json) ){
		$response_json['error'] = 'Не найдена запись';
		return $response_json;
	}
	
	
	$update_json =[];
	$update_json['name'] = trim($ARGS['name']);
	$update_json['city'] = trim($ARGS['city']);
	$update_json['address'] = trim($ARGS['address']);
	
	$city_json = f_list_city();
	$update_json['city'] = $city_json[ $update_json['city'] ] ? $update_json['city'] : null;
	
	if( mb_strlen( trim($ARGS['password']) ) > 0 ){
		$update_json['password'] = trim($ARGS['password']);
		$update_json['password_hash_sha256'] = hash('sha256', $update_json['password']);
	}
	
	$ARGS['login'] =  trim( str_replace('@', '', $ARGS['login']) );
	
	if( f_pay_get()['type'] == 'admin' ){
		$update_json['login'] = $ARGS['login'] == '' ? null : '@'. $ARGS['login'];
		$update_json['email'] = trim($ARGS['email']);
		$update_json['iin'] = trim($ARGS['iin']);
		$update_json['phone'] = trim($ARGS['phone']);
		$update_json['admin_comment'] = trim($ARGS['admin_comment']);
		if( in_array($ARGS['type'], ['admin', 'school', 'user']) ){
			$update_json['type'] = $ARGS['type'];
		}
		
		$update_json['_create_date'] = f_db_value_str_date($ARGS['_create_date']);
		$update_json['birthday_date'] = f_db_value_str_date($ARGS['birthday_date']);
		$update_json['activation_date'] = f_db_value_str_date($ARGS['activation_date']);
		
		$update_json['gender'] = f_number_if_min_max( 0, intval($ARGS['gender']), 2 );
		$update_json['activation_on'] = f_number_if_min_max(0, intval($ARGS['activation_on']), 1);
	}
	
	
	// Проверка на дубли данных аутентификации
	$arr_item_json = f_db_get_pay( ['email' => $update_json['email'], 'phone' => $update_json['phone'], 'login' => $update_json['login'] ], 2);
	
	if( count($arr_item_json) == 1 ){
		if( $arr_item_json[0]['_id'] != $item_json['_id'] ){
			$response_json['data_1'] = $arr_item_json;
			$response_json['error'] = 'Пользователь с таким Email, Телефоном, или Логином уже существует';
			return $response_json;
		}
	}else if( count($arr_item_json) > 1 ){
		$response_json['data_2'] = $arr_item_json;
		$response_json['error'] = 'Пользователь с таким Email, Телефоном, или Логином уже существует';
		return $response_json;
	}
	
	$response_json['update'] = $update_json;
	
	f_db_update_smart( "pay", ["_id" => $item_json['_id']], $update_json );
	
	return $response_json;
}



?>
