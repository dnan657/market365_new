<?php


$gl_api_func_json = [
	"find"			=> "f_api_user_find",
	"save"			=> "f_api_user_save",

	"login_create"	=> "f_api_user_login_create",
	"login_edit"	=> "f_api_user_login_edit",
];


function f_api_user_effective_type($row) {
	if (!is_array($row)) {
		return '';
	}
	$ut = isset($row['user_type']) ? trim((string)$row['user_type']) : '';
	return $ut !== '' ? $ut : (string)($row['type'] ?? '');
}


function f_api_user_find($ARGS){

	$response_json = [
		'error' => '',
		'error_code' => 0,
	];

	$me = f_user_get();
	$eff = f_api_user_effective_type($me);
	if( $eff !== 'admin' ){
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
			`_create_user_id`,
			`name`,
			`email`,
			`login`,
			`phone`,
			`address`,
			`user_type`,
			`type`,
			`_create_date`,
			`gender`,
			`birthday_date`,
			`admin_comment`,
			`city_type_id`
		FROM
			`user`
		WHERE
			(
				`_id` = ' . $number . '
				OR
				`phone` = "' . $number . '"
				OR
				`name` LIKE "%' . $text . '%"
				OR
				`email` LIKE "%' . $text . '%"
				OR
				`login` LIKE "%@' . $text . '%"
			)
		LIMIT 20
	';

	$response_json['data_arr'] = f_db_select( $sql_query );

	for($i=0; $i<count($response_json['data_arr']); $i++){
		$response_json['data_arr'][$i]['_id_str'] = f_num_encode( $response_json['data_arr'][$i]['_id'] );

		$response_json['data_arr'][$i]['type_str'] = f_api_user_effective_type( $response_json['data_arr'][$i] );

		if( isset($response_json['data_arr'][$i]['phone']) ){
			$response_json['data_arr'][$i]['phone_str'] = f_phone_beauty( $response_json['data_arr'][$i]['phone'] );
		}
		if( isset($response_json['data_arr'][$i]['birthday_date']) ){
			$response_json['data_arr'][$i]['birthday_date_str'] = f_date_beauty( $response_json['data_arr'][$i]['birthday_date'] );
		}
		if( isset($response_json['data_arr'][$i]['_create_date']) ){
			$response_json['data_arr'][$i]['_create_date_str'] = f_datetime_beauty( $response_json['data_arr'][$i]['_create_date'] );
		}

		$cid = $response_json['data_arr'][$i]['city_type_id'] ?? null;
		if( $cid ){
			$cj = f_db_select_smart('city', ['_id' => $cid], 1)[0];
			$response_json['data_arr'][$i]['city_str'] = $cj['title_en'] ?? '';
		}else{
			$response_json['data_arr'][$i]['city_str'] = '';
		}

		$response_json['data_arr'][$i]['html_create_user_id'] = null;

		if( $response_json['data_arr'][$i]['_create_user_id'] == f_user_get()['_id'] || f_api_user_effective_type(f_user_get()) == 'admin'){
			$response_json['data_arr'][$i]['html_create_user_id'] = f_num_encode( $response_json['data_arr'][$i]['_create_user_id'] );
		}

		unset( $response_json['data_arr'][$i]['_id'] );
		unset( $response_json['data_arr'][$i]['_create_user_id'] );
	}

	return $response_json;
}




function f_api_user_login_create($ARGS){

	$response_json = [
		'error' => '',
		'error_code' => 0,
	];

	if( f_api_user_effective_type(f_user_get()) !== 'admin' ){
		$response_json['error'] = 'Нет доступа';
		$response_json['error_code'] = -2;
		return $response_json;
	}

	$login =  trim( str_replace('@', '', $ARGS['login']) );

	$name = trim($ARGS['name']);
	$password = trim($ARGS['password']);


	if( $login == '' || $name == '' || $password == '' ){
		$response_json['error'] = 'Не указаны Логин, Имя или Пароль';
		return $response_json;
	}

	$login = '@' . $login;

	$item_json = f_db_get_user( ['login' => $login] );

	if( $item_json !== false && !empty($item_json['_id']) ){
		$response_json['error'] = 'Пользователь с таким логином уже существует';
		return $response_json;
	}

	$email_base = preg_replace('/[^a-zA-Z0-9._-]/', '_', str_replace('@', '', $login));
	$email = $email_base . '@users.market365.internal';
	$n = 0;
	while( f_db_get_user(['email' => $email]) ){
		$n++;
		$email = $email_base . '_' . $n . '@users.market365.internal';
	}

	$create_json = [
		"email"					=> $email,
		"name"					=> $name,
		"password_hash_sha256"	=> hash('sha256', $password),
		"login"					=> $login,
		"_create_date"			=> date('Y-m-d H:i:s'),
		"_create_user_id"		=> f_user_get()['_id'],
		"activation_date"		=> date('Y-m-d H:i:s'),
		"activation_on"			=> 1,
		"type"					=> 'user',
		"user_type"				=> 'user',
	];

	f_db_insert(
		"user",
		$create_json
	);

	$item_json = f_db_get_user( ['login' => $login] );

	$response_json['user'] = [
		'_id_str'		=> f_num_encode( $item_json['_id'] ),
		'login'			=> $item_json['login'],
		'name'			=> $item_json['name'],
		'_create_date'	=> $item_json['_create_date'],
	];

	return $response_json;
}



function f_api_user_login_edit($ARGS){

	$response_json = [
		'error' => '',
		'error_code' => 0,
	];

	if( f_api_user_effective_type(f_user_get()) !== 'admin' ){
		$response_json['error'] = 'Нет доступа';
		$response_json['error_code'] = -2;
		return $response_json;
	}

	$_id = trim( f_num_decode( $ARGS['_id_str'] ) );
	$login =  trim( str_replace('@', '', $ARGS['login']) );
	$name = trim($ARGS['name']);
	$password = trim($ARGS['password']);



	if( $login == '' || $name == '' || $password == '' ){
		$response_json['error'] = 'Не указаны Логин, Имя или Пароль';
		return $response_json;
	}
	$login = '@' . $login;

	$item_json = f_db_get_user( ['_id' => $_id] );

	if( $item_json === false || empty($item_json['_id']) ){
		$response_json['error'] = 'Пользователь не найден';
		return $response_json;
	}

	if( $login !== null ){
		$tmp_item_json = f_db_get_user( ['login' => $login] );

		if( $tmp_item_json !== false && !empty($tmp_item_json['_id']) && $tmp_item_json['_id'] != $item_json['_id'] ){
			$response_json['error'] = 'Пользователь с таким логином уже существует';
			return $response_json;
		}
	}

	$update_json = [
		"login" => $login,
		"name" => $name,
		"password_hash_sha256" => hash('sha256', $password)
	];

	f_db_update_smart( "user", ["_id" => $_id], $update_json );

	$item_json = f_db_get_user( ['_id' => $_id] );

	$response_json['user'] = [
		'_id_str'		=> f_num_encode( $item_json['_id'] ),
		'login'			=> $item_json['login'],
		'name'			=> $item_json['name'],
		'_create_date'	=> $item_json['_create_date'],
	];

	return $response_json;
}





function f_api_user_save($ARGS){

	$response_json = [
		'error' => '',
		'error_code' => 0,
	];

	$is_admin = f_api_user_effective_type(f_user_get()) == 'admin' ;
	$_id_str = $is_admin ? $ARGS['_id_str'] : f_num_encode( f_user_get()['_id'] );

	$item_json = f_db_get_user( ['_id_str' => $_id_str] );

	if( !isset($item_json) || !$item_json ){
		$response_json['error'] = 'Не найдена запись';
		return $response_json;
	}

	$scope = $ARGS['save_scope'] ?? 'full';
	if( !$is_admin && $scope === 'password' ){
		$update_json = [];
		$ARGS['password'] = trim($ARGS['password'] ?? '');
		if( mb_strlen( $ARGS['password'] ) < 1 ){
			$response_json['error'] = 'Укажите новый пароль';
			return $response_json;
		}
		$has_password = mb_strlen((string)($item_json['password_hash_sha256'] ?? '')) > 0;
		if( $has_password ){
			$old = trim($ARGS['password_old'] ?? '');
			if( $item_json['password_hash_sha256'] !== hash('sha256', $old) ){
				$response_json['error'] = 'Неверный текущий пароль';
				return $response_json;
			}
		}
		$update_json['password_hash_sha256'] = hash('sha256', $ARGS['password']);
		$response_json['update'] = $update_json;
		f_db_update_smart( "user", ["_id" => $item_json['_id']], $update_json );
		return $response_json;
	}

	$update_json =[];

	$update_json['name'] = mb_substr( trim($ARGS['name'] ?? ''), 0, 200);
	$update_json['description'] = mb_substr( trim($ARGS['description'] ?? ''), 0, 1000);
	$update_json['address'] = mb_substr( trim($ARGS['address'] ?? ''), 0, 200);
	$update_json['phone'] = preg_replace('/\D/', '', (string)($ARGS['phone'] ?? ''));
	$update_json['birthday_date'] = f_db_value_str_date($ARGS['birthday_date'] ?? '');

	$update_json['gender'] = f_number_if_min_max( 0, intval($ARGS['gender'] ?? 0), 2 );

	$update_json['city_type_id'] = f_number_if_min_max( 0, intval($ARGS['city_type_id'] ?? 0), 10000 ) ?: NULL;

	$ARGS['password'] = trim($ARGS['password'] ?? '');
	if( mb_strlen( $ARGS['password'] ) > 0 ){
		if( !$is_admin ){
			$has_password = mb_strlen((string)($item_json['password_hash_sha256'] ?? '')) > 0;
			if( $has_password ){
				$old = trim($ARGS['password_old'] ?? '');
				if( $item_json['password_hash_sha256'] !== hash('sha256', $old) ){
					$response_json['error'] = 'Неверный текущий пароль';
					return $response_json;
				}
			}
		}
		$update_json['password_hash_sha256'] = hash('sha256', $ARGS['password']);
	}

	if( $update_json['name'] == '' ){
		$response_json['error'] = 'Имя не может быть пустым';
		return $response_json;
	}

	$ARGS['login'] = mb_substr( trim($ARGS['login'] ?? ''), 0, 200);
	if( $ARGS['login'] != '' ){
		$arr_item_json = f_db_get_user( [ 'login' => $ARGS['login'] ], 2);

		if( count($arr_item_json) > 0 ){
			if( $arr_item_json[0]['_id'] != $item_json['_id'] || count($arr_item_json) > 1 ){
				$response_json['error'] = 'Пользователь с таким Логином уже существует';
				return $response_json;
			}
		}
		$update_json['login'] = $ARGS['login'];
	}


	if( f_api_user_effective_type(f_user_get()) == 'admin' ){
		$update_json['email'] = mb_substr( trim($ARGS['email'] ?? ''), 0, 200);
		$update_json['admin_comment'] = mb_substr( trim($ARGS['admin_comment'] ?? ''), 0, 500);
		if( in_array($ARGS['user_type'] ?? '', ['admin', 'user', 'business', 'moderator']) ){
			$update_json['user_type'] = $ARGS['user_type'];
			$update_json['type'] = $ARGS['user_type'];
		}

		$update_json['_create_date'] = f_db_value_str_date($ARGS['_create_date'] ?? '');
		$update_json['activation_date'] = f_db_value_str_date($ARGS['activation_date'] ?? '');
		$update_json['activation_on'] = f_number_if_min_max(0, intval($ARGS['activation_on'] ?? 0), 1);
	}

	$response_json['update'] = $update_json;

	f_db_update_smart( "user", ["_id" => $item_json['_id']], $update_json );

	return $response_json;
}



?>
