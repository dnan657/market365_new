<?php


// Набор функций
$gl_api_func_json = [
	"edit"			=> "f_api_subscription_edit",
	"create"		=> "f_api_subscription_create"
];


/*

activation_date: ""
activation_expired_date: ""
activation_period_day: "30"
admin_activation_date: "2024-04-18T22:07:52"
admin_activation_on: "1"
admin_price: "1000"
category_question: ""
from_user_id_str: "Ng"
school_comment: ""
school_price: ""
to_user_id_str: null
_create_date: "2024-04-18T22:07:52"
_id_str: "Zw"

*/

function f_api_subscription_edit($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	$new_data_json = [
		'_id'						=> f_num_decode( $ARGS['_id_str'] ),
		
		'to_user_id'				=> f_num_decode( $ARGS['to_user_id_str'] ) ,
		'from_user_id'				=> f_num_decode( $ARGS['from_user_id_str'] ) ,
		
		'_create_date'				=> trim($ARGS['_create_date']) ,
		
		'activation_status'			=> intval($ARGS['activation_status']) ,
		'activation_date'			=> trim($ARGS['activation_date']) ,
		'activation_expired_date'	=> trim($ARGS['activation_expired_date']) ,
		'to_user_send_date'			=> trim($ARGS['to_user_send_date']) ,
		'to_user_group_name'		=> trim($ARGS['to_user_group_name']) ,
		'activation_period_day'		=> intval($ARGS['activation_period_day']) ,
		'admin_activation_on'		=> intval($ARGS['admin_activation_on']) ,
		'admin_price'				=> intval($ARGS['admin_price']) ,
		'category_question'			=> trim($ARGS['category_question']) ,
		'admin_comment'				=> mb_substr( trim($ARGS['admin_comment'] . ''), 0, 500) ,
		'school_comment'			=> mb_substr( trim($ARGS['school_comment'] . ''), 0, 500) ,
		'school_price'				=> intval($ARGS['school_price']) ,
	];
	
	$item_json = f_db_get_subscription_id($new_data_json['_id']);
	
	// Проверка существования Записи
	if( !isset($item_json) ){
		$response_json['error'] = 'Не найдена запись';
		return $response_json;
	}
	
	
	// Проверка прав - Админ или Источник подписки
	if( f_user_get()['type'] != 'admin' && $item_json['from_user_id'] != f_user_get()['_id'] ){
		$response_json['error'] = 'Нет доступа';
		return $response_json;
	}
	
	$update_json = [];
	
	if( in_array( f_user_get()['type'], ['admin', 'school'] )) {
		$pdd_cats = function_exists('f_get_pdd_category_arr') ? f_get_pdd_category_arr() : [];
		if( !is_array($pdd_cats) ){
			$pdd_cats = [];
		}
		$update_json['category_question'] = in_array($new_data_json['category_question'], $pdd_cats, true) ? $new_data_json['category_question'] : null;
		$update_json['school_price'] = f_number_if_min_max(0, $new_data_json['school_price'], 100000);
		$update_json['to_user_group_name'] = $new_data_json['to_user_group_name'];
		$update_json['school_comment'] = $new_data_json['school_comment'];
	}
	
	
	if( f_user_get()['type'] == 'admin' ){
		
		$update_json['to_user_id'] = $new_data_json['to_user_id'];
		$update_json['from_user_id'] = $new_data_json['from_user_id'];
		
		if( !f_db_get_user(['_id' => $new_data_json['from_user_id'] ]) ){
			$response_json['error'] = 'Отправитель не найден';
			return $response_json;
		}
		if( !f_db_get_user(['_id' => $new_data_json['to_user_id'] ]) ){
			$response_json['error'] = 'Получатель не найден';
			return $response_json;
		}
	
		$update_json['_create_date'] =  f_db_value_str_date( $new_data_json['_create_date'] );
		
		$update_json['activation_status'] = f_number_if_min_max(-1, $new_data_json['activation_status'], 1);
		$update_json['activation_period_day'] = f_number_if_min_max(1, $new_data_json['activation_period_day'], 100);
		$update_json['admin_activation_on'] = f_number_if_min_max(0, $new_data_json['admin_activation_on'], 1);
		
		$update_json['admin_price'] = f_number_if_min_max(0, $new_data_json['admin_price'], 100000);
		$update_json['admin_comment'] = $new_data_json['admin_comment'];
		
		$update_json['activation_date'] =  f_db_value_str_date( $new_data_json['activation_date'] );
		if( f_date_check( $new_data_json['activation_expired_date'] ) ){
			$update_json['activation_expired_date'] =  f_db_value_str_date( $new_data_json['activation_expired_date'] );
			$update_json['activation_period_day'] = f_date_diff_days($update_json['activation_date'], $update_json['activation_expired_date']);
		}
		$update_json['to_user_send_date'] =  f_db_value_str_date( $new_data_json['to_user_send_date'] );
		
		if( $update_json['activation_status'] == 0 ){
			$update_json['activation_date'] = null;
			$update_json['to_user_send_date'] = null;
			$update_json['activation_expired_date'] = null;
			$update_json['activation_period_day'] = null;
		}
		
		
	}else if( f_user_get()['type'] == 'school' ){
	
		// Школа - Нажала отменить Активацию
		if( $ARGS['btn_type'] == 'disactivate' ){
			
			if( $item_json['activation_status'] == 0 ){
				$response_json['error'] = 'Подписка уже была отключена';
				return $response_json;
			}
			
			if( strtotime($item_json['activation_date']) < strtotime('-5 minutes') ){
				$response_json['error'] = 'Прошло 5 минут с момента активации, её уже нельзя отменить';
				return $response_json;
			}
			
			$update_json['activation_status'] = 0;
			$update_json['activation_date'] = null;
			$update_json['to_user_send_date'] = null;
			//$update_json['to_user_id'] = null;
			$update_json['activation_expired_date'] = null;
		
		// Школа - Нажала Активацию
		}else if( $ARGS['btn_type'] == 'activate' ){
			
			if( !$update_json['category_question'] ){
				$response_json['error'] = 'Не указана категория тестов';
				return $response_json;
			}
			if( $item_json['activation_status'] != 0 ){
				$response_json['error'] = 'Подписка уже была активирована';
				return $response_json;
			}
			
			$update_json['activation_status'] = 1;
			$update_json['activation_date'] = f_datetime_current();
			$update_json['to_user_send_date'] = f_datetime_current();
			$update_json['to_user_id'] = $new_data_json['to_user_id'];
			$update_json['activation_expired_date'] = date('Y-m-d H:i:s', strtotime('+30 days') );
			
			if( !f_db_get_user(['_id' => $new_data_json['to_user_id'] ]) ){
				$response_json['error'] = 'Получатель не найден';
				return $response_json;
			}
		
		// Школа - нажала просто сохрнаить всё
		}else{
			
		}
		
		
		// Школа - Нажала Сохранить изменения
		if( $item_json['activation_status'] == 0 ){
			
			$update_json['to_user_id'] = $new_data_json['to_user_id'];
			
			if( !f_db_get_user(['_id' => $new_data_json['to_user_id'] ]) ){
				$response_json['error'] = 'Получатель не найден';
				return $response_json;
			}
			
		}
		

		
	}
	
	//return $update_json;
	
	f_db_update_smart("pdd_subscription", ["_id" => $new_data_json['_id']], $update_json);
	
	return $response_json;
}

function f_api_subscription_create($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];

	if( f_user_get()['type'] != 'admin' ){
		$response_json['error'] = 'Нет доступа';
		return $response_json;
	}
	
	$ssid = $ARGS['ssid'];

	if($ssid != session_id() ){
		$response_json['error'] = 'Не совпадают сессии';
		return $response_json;
	}

	$count = f_number_if_min_max(1, $ARGS['count'], 10);

	$admin_price =  intval($ARGS['admin_price']);

	$from_user_id = f_num_decode( $ARGS['from_user_id'] );

	$from_user_json = f_db_get_user(['_id'=>$from_user_id]);

	if( isset($from_user_json) == false || $ARGS['from_user_id'] == ''){
		$response_json['error'] = 'Источник не найден';
		return $response_json;
	}

	$current_date = date('Y-m-d H:i:s');
	
	$group_id = $from_user_id . '-' . f_user_get()['_id'] . '-' . $count  . '-' . $admin_price . '-' . strtotime('now');

	for($i=0; $i<$count; $i++){
		f_db_insert(
			"pdd_subscription", 
			[
				"_create_date"				=> $current_date,
				"_create_user_id"			=> f_user_get()['_id'],
				"from_user_id"				=> $from_user_id,
				"group_id"					=> $group_id,
				"admin_activation_on"		=> 1,
				"admin_activation_date"		=> $current_date,
				"activation_period_day"		=> 30,
				// Активация будет во время присвоения пользователя к подписки
				//"activation_expired_date"	=> date('Y-m-d H:i:s', strtotime('+30 days')),
				"admin_price"				=> $admin_price,
				"admin_comment"				=> 'Группа из подписок: ' . $count,
			]
		);
	}
	
	// Сохраняем платеж
	f_db_insert(
		"pdd_pay", 
		[
			"_create_date"				=> $current_date,
			"_create_user_id"			=> f_user_get()['_id'],
			"from_user_id"				=> $from_user_id,
			"subscription_group_id"		=> $group_id,
			"title"						=> 'Подписка 1 месяц',
			"count"						=> $count,
			"price"						=> $admin_price,
			"sum"						=> $count * $admin_price,
			// Активация будет во время присвоения пользователя к подписки
			//"activation_expired_date"	=> date('Y-m-d H:i:s', strtotime('+30 days')),
			//"comment"					=> 'Группа из подписок: ' . $count,
		]
	);

	return $response_json;
}





?>
