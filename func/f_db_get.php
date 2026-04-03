<?php


function f_db_get_subscription_list($sql_where, $sql_continue=""){
	
	$result_json = [
		'sql_query' => '',
		
		'data_arr' => false,
		
		'count_total' => -1,
		'count_show' => -1,
		
		'sql_select' => '',
		'sql_where' => $sql_where,
		'sql_continue' => $sql_continue
	];

	$sql_select = "
		SELECT
			`subscription`.`_id`						AS '_id',
			`subscription`.`_create_date`				AS '_create_date',
			`subscription`.`_create_user_id`			AS '_create_user_id',
			
			`subscription`.`from_user_id`				AS 'from_user_id',
			`subscription`.`to_user_id`					AS 'to_user_id',
			`subscription`.`to_user_send_date`			AS 'to_user_send_date',
			`subscription`.`to_user_group_name`			AS 'to_user_group_name',
			`subscription`.`category_question`			AS 'category_question',
			
			`subscription`.`admin_activation_on`			AS 'admin_activation_on',
			`subscription`.`admin_activation_date`			AS 'admin_activation_date',
			
			`subscription`.`activation_status`			AS 'activation_status',
			`subscription`.`activation_date`			AS 'activation_date',
			`subscription`.`activation_period_day`		AS 'activation_period_day',
			`subscription`.`activation_expired_date`	AS 'activation_expired_date',
			
			`subscription`.`admin_price`				AS 'admin_price',
			`subscription`.`admin_comment`				AS 'admin_comment',
			
			`subscription`.`school_price`				AS 'school_price',
			`subscription`.`school_comment`				AS 'school_comment',
			
			`tmp_create_user`.`_id`							AS 'tmp_create_id',
			`tmp_create_user`.`_create_date`				AS 'tmp_create_create_date',
			`tmp_create_user`.`_create_user_id`				AS 'tmp_create_create_user_id',
			`tmp_create_user`.`login`						AS 'tmp_create_login',
			`tmp_create_user`.`type`						AS 'tmp_create_type',
			`tmp_create_user`.`name`						AS 'tmp_create_name',
			`tmp_create_user`.`phone`						AS 'tmp_create_phone',
			`tmp_create_user`.`email`						AS 'tmp_create_email',
			`tmp_create_user`.`gender`						AS 'tmp_create_gender',
			`tmp_create_user`.`birthday_date`				AS 'tmp_create_birthday_date',
			`tmp_create_user`.`admin_comment`				AS 'tmp_create_admin_comment',
			
			`tmp_from_user`.`_id`							AS 'tmp_from_id',
			`tmp_from_user`.`_create_date`					AS 'tmp_from_create_date',
			`tmp_from_user`.`_create_user_id`				AS 'tmp_from_create_user_id',
			`tmp_from_user`.`login`							AS 'tmp_from_login',
			`tmp_from_user`.`type`							AS 'tmp_from_type',
			`tmp_from_user`.`name`							AS 'tmp_from_name',
			`tmp_from_user`.`phone`							AS 'tmp_from_phone',
			`tmp_from_user`.`email`							AS 'tmp_from_email',
			`tmp_from_user`.`gender`						AS 'tmp_from_gender',
			`tmp_from_user`.`city`							AS 'tmp_from_city',
			`tmp_from_user`.`birthday_date`					AS 'tmp_from_birthday_date',
			`tmp_from_user`.`admin_comment`					AS 'tmp_from_admin_comment',
			
			`tmp_to_user`.`_id`								AS 'tmp_to_id',
			`tmp_to_user`.`_create_user_id`					AS 'tmp_to_create_user_id',
			`tmp_to_user`.`_create_date`					AS 'tmp_to_create_date',
			`tmp_to_user`.`login`							AS 'tmp_to_login',
			`tmp_to_user`.`type`							AS 'tmp_to_type',
			`tmp_to_user`.`name`							AS 'tmp_to_name',
			`tmp_to_user`.`phone`							AS 'tmp_to_phone',
			`tmp_to_user`.`email`							AS 'tmp_to_email',
			`tmp_to_user`.`gender`							AS 'tmp_to_gender',
			`tmp_to_user`.`city`							AS 'tmp_to_city',
			`tmp_to_user`.`birthday_date`					AS 'tmp_to_birthday_date',
			`tmp_to_user`.`admin_comment`					AS 'tmp_to_admin_comment',
			`tmp_to_user`.`password`						AS 'tmp_to_password'
		FROM
			`subscription`
			
		LEFT JOIN 
			`user` AS `tmp_create_user`
				ON `subscription`.`_create_user_id` = `tmp_create_user`.`_id`
				
		LEFT JOIN 
			`user` AS `tmp_from_user`
				ON `subscription`.`from_user_id` = `tmp_from_user`.`_id`
				
		LEFT JOIN 
			`user` AS `tmp_to_user`
				ON `subscription`.`to_user_id` = `tmp_to_user`.`_id`
		";
	
	$sql_query = $sql_select . ' WHERE 1 ' . $sql_where . ' ' . $sql_continue;
	
	$result_json['sql_select'] = $sql_select;
	
	$result_json['sql_query'] = $sql_query;
	
	$result_json['data_arr'] = f_db_select( $sql_query );
	
	for ($i = 0; $i < count($result_json['data_arr']); $i++) {
		//f_category_beauty( $item_json['category_question'] )
		//f_datetime_beauty( $item_json['activation_date'] )
		//f_datetime_beauty( $item_json['activation_expired_date'] )
		
		//$item_json['html_id'] = f_num_encode( $item_json['_id'] );
		
		// Если это не Админ и не Родитель пользователя
		if( f_user_get()['type'] != 'admin' && $result_json['data_arr'][$i]['tmp_to_create_user_id'] != f_user_get()['_id'] ){
			unset( $result_json['data_arr'][$i]['tmp_to_password'] );
		}
		
		
		$day_left_expired = f_day_left( $result_json['data_arr'][$i]['activation_expired_date'] );
		$day_left_activation = f_day_left( $result_json['data_arr'][$i]['activation_date'] );
		$day_left_create = f_day_left( $result_json['data_arr'][$i]['_create_date'] );
		
		
		$result_json['data_arr'][$i]['html_id'] = f_num_encode( $result_json['data_arr'][$i]['_id'] );
		
		//$result_json['data_arr'][$i]['html_to_city'] = f_user_city( $result_json['data_arr'][$i]['tmp_to_city'] );
		//$result_json['data_arr'][$i]['html_from_city'] = f_user_city( $result_json['data_arr'][$i]['tmp_from_city'] );
		
		$result_json['data_arr'][$i]['html_activation_expired_day_left'] = f_day_left( $result_json['data_arr'][$i]['activation_expired_date'] );
		$result_json['data_arr'][$i]['html_text_activation_expired_day_left'] = $day_left_expired == '-' ? '' : ($day_left_expired == 0 ? f_translate( 'Сегодня' ) : ( $day_left_expired < 0 ? f_translate( 'Истёк' ) . ' - ' . abs($day_left_expired) . ' ' . f_translate( f_number_word( $day_left_expired, ['день', 'дня', 'дней']) ) . ' '. f_translate( 'назад' )  : ( $day_left_expired . ' ' . f_translate( f_number_word( $day_left_expired, ['день', 'дня', 'дней']) ) ) ) );
		$result_json['data_arr'][$i]['html_text_activation_date'] = $day_left_activation == '-' ? '' : ($day_left_activation == 0 ? f_translate( 'Сегодня' ) : ( abs($day_left_activation) . ' ' . f_translate( f_number_word( $day_left_activation, ['день', 'дня', 'дней']) ) . ' ' . f_translate('назад') ) );
		$result_json['data_arr'][$i]['html_text_activation_expired_date'] = $day_left_expired == '-' ? '' : ($day_left_expired == 0 ? f_translate( 'Сегодня' ) : ( abs($day_left_expired) . ' ' . f_translate( f_number_word( $day_left_expired, ['день', 'дня', 'дней']) ) . ' ' . f_translate('назад') ) );
		$result_json['data_arr'][$i]['html_text_create_date'] = $day_left_create == '-' ? '' : ($day_left_create == 0 ? f_translate( 'Сегодня' ) : ( abs($day_left_create) . ' ' . f_translate( f_number_word( abs($day_left_create), ['день', 'дня', 'дней']) ) . ' ' . f_translate('назад') ) );
	}
	
	$result_json['count_show'] = isset($result_json['data_arr']) ? count( $result_json['data_arr'] ) : -2;
	//$result_json['count_total'] = f_db_select_count('subscription', $sql_where);
	
	return $result_json;
}

function f_db_get_subscription_id($_id='0'){
	$_id = intval( $_id );
	return f_db_get_subscription_list('AND  `subscription`.`_id`  =  ' . $_id, ' LIMIT 1')['data_arr'][0];
}

function f_db_get_subscription_group_name($user_id=false){
	
	if($user_id == false){
		$user_id = f_user_get()['_id'];
	}
	
	$arr_data = f_db_select('
		SELECT
			DISTINCT to_user_group_name AS "name"
		FROM
			`subscription`
		WHERE
			`from_user_id`=' . f_db_sql_value($user_id) . '
			AND
		   `to_user_group_name` IS NOT NULL
			AND
		   TRIM(`to_user_group_name`) != ""
	');
	
	return $arr_data;
}





function f_db_get_test_update_expired(){
	return f_db_update(' UPDATE  `test`  SET  `ready_on` = 1, `expired_on` = 1, `ready_date`=`expired_date`, `status` = -2  WHERE  `ready_on` = 0 AND `expired_date` <=' . f_db_sql_value( f_datetime_current() ) );
}

function f_db_get_subscription_update_expired(){
	return f_db_update(' UPDATE  `subscription`  SET  `activation_status` = -1  WHERE  `activation_status` > -1 AND `activation_expired_date` <=' . f_db_sql_value( f_datetime_current() ) );
}




function f_db_get_subscription_user_id($user_id='0'){
	$user_id = intval( $user_id );
	return f_db_get_subscription_list('
		AND  `subscription`.`admin_activation_on` = 1 
		AND  `subscription`.`activation_status` = 1 
		AND  `subscription`.`to_user_id`  =  ' . $user_id
	)['data_arr'];
	
	// AND  `subscription`.`activation_expired_date` < "'. date('Y-m-d H:i:s') .'"
}






function f_db_get_test_trouble($user_id){
	$sql_query = "
		SELECT
			COUNT(`question_id`) as 'total'
		FROM
			(
			SELECT
				`question_id`,
				SUM(CASE WHEN `status` = 1 THEN 1 ELSE 0 END) - SUM(CASE WHEN `status` < 0 THEN 1 ELSE 0 END) AS 'total_count'
			FROM
				`test_answer`
			WHERE
				`user_id` = ". $user_id ."
			GROUP BY
				`question_id`
			HAVING
				`total_count` < 0
			ORDER BY
				`total_count` ASC
			LIMIT 40
		) AS tmp
	";
	
	return f_db_select($sql_query)[0]['total'];
}


function f_db_get_test_list($sql_where, $sql_continue=""){
	
	$result_json = [
		'sql_query' => '',
		
		'data_arr' => false,
		
		'count_total' => -1,
		'count_show' => -1,
		
		'sql_select' => '',
		'sql_where' => $sql_where,
		'sql_continue' => $sql_continue
	];

	$sql_select = "
		SELECT
			`test`.`_id`						AS '_id',
			`test`.`_create_date`				AS '_create_date',
			`test`.`user_id`					AS 'user_id',
			`test`.`subscription_id`			AS 'subscription_id',
			`test`.`category`					AS 'category',
			`test`.`expired_on`					AS 'expired_on',
			`test`.`expired_date`				AS 'expired_date',
			`test`.`ready_on`					AS 'ready_on',
			`test`.`ready_date`					AS 'ready_date',
			`test`.`status`						AS 'status',
			`test`.`true_count`					AS 'true_count',
			`test`.`false_count`				AS 'false_count',
			`test`.`skip_count`					AS 'skip_count',
			`test`.`question_count`				AS 'question_count',
			
			`subscription`.`from_user_id`				AS 'from_user_id',
			`subscription`.`to_user_id`					AS 'to_user_id',
			`subscription`.`to_user_send_date`					AS 'to_user_send_date',
			
			`subscription`.`activation_status`			AS 'activation_status',
			`subscription`.`activation_date`			AS 'activation_date',
			`subscription`.`activation_period_day`		AS 'activation_period_day',
			`subscription`.`activation_expired_date`	AS 'activation_expired_date',
			
			`tmp_from_user`.`name`							AS 'tmp_from_name',
			`tmp_from_user`.`login`							AS 'tmp_from_login',
			`tmp_from_user`.`phone`							AS 'tmp_from_phone',
			`tmp_from_user`.`email`							AS 'tmp_from_email',
			`tmp_from_user`.`type`							AS 'tmp_from_type',
			
			`tmp_to_user`.`name`							AS 'tmp_to_name',
			`tmp_to_user`.`_create_user_id`					AS 'tmp_to_create_user_id',
			`tmp_to_user`.`login`							AS 'tmp_to_login',
			`tmp_to_user`.`phone`							AS 'tmp_to_phone',
			`tmp_to_user`.`email`							AS 'tmp_to_email',
			`tmp_to_user`.`type`							AS 'tmp_to_type'
		FROM
			`test`
			
		LEFT OUTER JOIN 
			`subscription`
				ON `test`.`subscription_id` = `subscription`.`_id`
			
		LEFT JOIN 
			`user` AS `tmp_create_user`
				ON `subscription`.`_create_user_id` = `tmp_create_user`.`_id`
				
		LEFT JOIN 
			`user` AS `tmp_from_user`
				ON `subscription`.`from_user_id` = `tmp_from_user`.`_id`
				
		LEFT JOIN 
			`user` AS `tmp_to_user`
				ON `test`.`user_id` = `tmp_to_user`.`_id`
		";
	
	$sql_query = $sql_select . ' WHERE 1 ' . $sql_where . ' ' . $sql_continue;
		
	$result_json['sql_select'] = $sql_select;
	
	$result_json['sql_query'] = $sql_query;
	
	//f_test($sql_query);
	
	$data_arr = f_db_select( $sql_query );
	
	for ($i = 0; $i < count( $data_arr ); $i++) {
		//f_category_beauty( $item_json['category_question'] )
		//f_datetime_beauty( $item_json['activation_date'] )
		//f_datetime_beauty( $item_json['activation_expired_date'] )
		
		//$item_json['html_id'] = f_num_encode( $item_json['_id'] );
		
	}
	
	$result_json['data_arr'] = $data_arr;
	
	$result_json['count_show'] = isset( $data_arr ) ? count( $data_arr ) : -2;
	//$result_json['count_total'] = f_db_select_count('test', $sql_where);
	
	return $result_json;
}


function f_db_get_test_not_expired($user_id=false){
	
	if($user_id == false){
		$user_id = f_user_get()['_id'];
	}
	
	$arr_data = f_db_select('SELECT * FROM `test` WHERE `user_id`=' . f_db_sql_value($user_id) . ' AND `ready_on` = 0 AND `expired_date` > ' . f_db_sql_value( f_datetime_current() ) );
	
	return $arr_data;
}








function f_db_get_translate($sql_where, $sql_continue=""){
	
	$result_json = [
		'sql_query' => '',
		
		'data_arr' => false,
		
		'count_total' => -1,
		'count_show' => -1,
		
		'sql_select' => '',
		'sql_where' => $sql_where,
		'sql_continue' => $sql_continue
	];

	$sql_select = "
		SELECT
			*
		FROM
			`translate`
		";
	
	$sql_query = $sql_select . ' WHERE 1 ' . $sql_where . ' ' . $sql_continue;
		
	$result_json['sql_select'] = $sql_select;
	
	$result_json['sql_query'] = $sql_query;
	
	$result_json['data_arr'] = f_db_select( $sql_query );
	
	$result_json['count_show'] = isset($result_json['data_arr']) ? count( $result_json['data_arr'] ) : -2;
	
	$result_json['count_total'] = f_db_select_count('translate', $sql_where);
	
	return $result_json;
}

function f_db_get_pay($sql_where, $sql_continue=""){
	
	$result_json = [
		'sql_query' => '',
		
		'data_arr' => false,
		
		'count_total' => -1,
		'count_show' => -1,
		
		'sql_select' => '',
		'sql_where' => $sql_where,
		'sql_continue' => $sql_continue
	];

	$sql_select = "
		SELECT
			`pay`.*,
			`tmp_from_user`.`name`		AS 'tmp_from_user_name',
			`tmp_from_user`.`phone`		AS 'tmp_from_user_phone',
			`tmp_from_user`.`login`		AS 'tmp_from_user_login'
		FROM
			`pay`
		LEFT OUTER JOIN 
			`user` AS `tmp_from_user`
				ON `tmp_from_user`.`_id` = `pay`.`from_user_id`
			
		";
	
	$sql_query = $sql_select . ' WHERE 1 ' . $sql_where . ' ' . $sql_continue;
		
	$result_json['sql_select'] = $sql_select;
	
	$result_json['sql_query'] = $sql_query;
	
	$result_json['data_arr'] = f_db_select( $sql_query );
	
	$result_json['total_sum'] = f_db_select(' SELECT SUM(`sum`) AS "total" FROM `pay` WHERE 1 '. $sql_where)[0]['total'];
	$result_json['total_count'] = f_db_select(' SELECT SUM(`count`) AS "total" FROM `pay` WHERE 1 '. $sql_where)[0]['total'];
	
	$result_json['total_row'] = f_db_select_count('pay', $sql_where);
	
	return $result_json;
}



function f_db_get_user_list($sql_where, $sql_continue=""){
	
	$result_json = [
		'sql_query' => '',
		
		'data_arr' => false,
		
		'count_total' => -1,
		'count_show' => -1,
		
		'sql_select' => '',
		'sql_where' => $sql_where,
		'sql_continue' => $sql_continue
	];

	$sql_select = "
		SELECT
			*
		FROM
			`user`
		";
	
	$sql_query = $sql_select . ' WHERE 1 ' . $sql_where . ' ' . $sql_continue;
		
	$result_json['sql_select'] = $sql_select;
	
	$result_json['sql_query'] = $sql_query;
	
	$data_arr = f_db_select( $sql_query );
	
	for($i=0; $i<count($data_arr); $i++){
		$ut = isset($data_arr[$i]['user_type']) && trim((string)$data_arr[$i]['user_type']) !== ''
			? trim($data_arr[$i]['user_type'])
			: (string)($data_arr[$i]['type'] ?? '');
		$data_arr[$i]['html_user_type'] = f_translate( $ut );
		
		$data_arr[$i]['html_name'] = ( $data_arr[$i]['name'] != '' ) ? $data_arr[$i]['name'] : explode('@', $data_arr[$i]['email'])[0] ;
		$data_arr[$i]['html_count_balance'] = '100 ' . f_page_currency();
		$data_arr[$i]['html_count_notifications'] = '5';
		$data_arr[$i]['html_count_messages'] = '12';
		$data_arr[$i]['html_count_favorites'] = '120';
		$data_arr[$i]['html_phone'] = f_phone_beauty($data_arr[$i]['phone']);
		
		$data_arr[$i]['html_create_date'] = f_html_date_to_last_day( $data_arr[$i]['_create_date'] );
		$data_arr[$i]['html_activation_date'] = f_html_date_to_last_day( $data_arr[$i]['activation_date'] );
		$data_arr[$i]['html_visit_date'] = f_html_date_to_last_day( $data_arr[$i]['visit_date'] );
		$data_arr[$i]['html_auth_date'] = f_html_date_to_last_day( $data_arr[$i]['auth_date'] );
		
		$data_arr[$i]['html_uid'] = f_num_encode($data_arr[$i]['_id']) . '-' . ( $data_arr[$i]['password_hash_sha256'] ?: hash('sha256', $data_arr[$i]['google_id']) ); 
		
		$data_arr[$i]['json_city_type_id'] = f_db_select_smart( 'type_item', ['_id' => $data_arr[$i]['city_type_id'] ] )[0];
		$data_arr[$i]['html_city'] = ($data_arr[$i]['json_city_type_id']['title'] ?: ' не указан');
		
		$data_arr[$i]['tmp_is_admin'] = $ut === 'admin';
		$data_arr[$i]['tmp_is_business'] = $ut === 'business';
		$data_arr[$i]['tmp_is_user'] = $ut === 'user';
		
	}
	
	$result_json['data_arr'] = $data_arr;
	
	$result_json['count_show'] = isset($result_json['data_arr']) ? count( $result_json['data_arr'] ) : -2;
	
	$result_json['count_total'] = f_db_select_count('user', $sql_where);
	
	return $result_json;
}




function f_html_date_to_last_day($date){
	$day_left = f_day_left( $date );
	
	$result = '';
	
	if($day_left == '-'){
		$result = '';
	}else if($day_left == 0){
		$result = f_translate( 'Сегодня' );
	}else{
		$day_left = abs($day_left);
		$result = $day_left . ' ' . f_translate( f_number_word( $day_left, ['день', 'дня', 'дней']) ) . ' ' . f_translate('назад');
	}
	
	return $result;
}


function f_db_get_user($data_json=[], $limit=1){
	
	$sql_where_arr = [];
	
	if( isset($data_json['_id']) ){
		$sql_where_arr[] = "`_id` = ". f_db_sql_value( $data_json['_id']  );
	}
	if( isset($data_json['_id_str']) ){
		$sql_where_arr[] = "`_id` = ". f_db_sql_value( f_num_decode($data_json['_id_str'])  );
	}
	if( isset($data_json['login']) ){
		$sql_where_arr[] = "`login` = ". f_db_sql_value( trim($data_json['login']) );
	}
	if( isset($data_json['email']) ){
		$sql_where_arr[] = "`email` = ". f_db_sql_value( trim($data_json['email']) );
	}
	if( isset($data_json['phone']) ){
		if( f_parse_number_str($data_json['phone']) != '' ){
			$sql_where_arr[] = "`phone` = ". f_db_sql_value( f_parse_number_str($data_json['phone'])  );
		}
	}
	
	if(count($sql_where_arr) == 0){
		return false;
	}
	
	
	$arr_item_json = f_db_get_user_list( 'AND ' . implode(' OR ', $sql_where_arr) )['data_arr'];
	
	

	
	if( $limit == 1 ){
		
		$item_json = $arr_item_json[0];
		
		if( isset($data_json['password']) ){
			if( $item_json['password_hash_sha256'] != hash('sha256', $data_json['password']) ){
				return false;
			}
		}	
		
		if( isset($data_json['password_hash_sha256']) ){
			if( $item_json['password_hash_sha256'] != $data_json['password_hash_sha256']){
				return false;
			}
		}
	
		return $item_json;
	}
	
	return $arr_item_json;
}




function f_db_get_label_ads(){
	$json_label = [
		'title' => f_translate('Заголовок'),
		'html_link' => f_translate('Ссылка'),
		'price_min' => f_translate('Цена'),
		'lesson_dur' => f_translate('Время 1 урока (час:мин)'),
		'lesson_count_week' => f_translate('Занятий в неделю'),
		'lesson_count_min' => f_translate('Всего занятий'),
		'course_dur_month' => f_translate('Месяцев обучения'),
		'time_min' => f_translate('Время начала и конца урока'),
		'time_max' => f_translate('Время начала и конца урока'),
		'____' => f_translate('___'),
	];
	
	return $json_label;
}


function f_db_get_list_ads($sql_where, $sql_continue=""){
	
	$result_json = [
		'sql_query' => '',
		
		'data_arr' => false,
		
		'count_total' => -1,
		'count_show' => -1,
		
		'sql_select' => '',
		'sql_where' => $sql_where,
		'sql_continue' => $sql_continue
	];

	$sql_select = "
		SELECT
			*,
			CONCAT(ST_X(gps_address), ',', ST_Y(gps_address)) AS gps_address_lat_lng
		FROM
			`ads`
		";
	
	$sql_query = $sql_select . ' WHERE 1 ' . $sql_where . ' ' . $sql_continue;
		
	$result_json['sql_select'] = $sql_select;
	
	$result_json['sql_query'] = $sql_query;
	
	$data_arr = f_db_select( $sql_query );
	
	for($i=0; $i<count($data_arr); $i++){
		$data_arr[$i]['_id_str'] = f_num_encode( $data_arr[$i]['_id'] );
		
		$data_arr[$i]['html_delete_on'] = f_translate( $data_arr[$i]['delete_on'] == 1 ? 'Удалено' : '' );
		$data_arr[$i]['html_publication_on'] = f_translate( $data_arr[$i]['publication_on'] == 1 ? 'Опубликовано' : 'Черновик' );
		
		$data_arr[$i]['html_create_date'] = f_html_date_to_last_day( $data_arr[$i]['_create_date'] );
		$data_arr[$i]['html_publication_date'] = f_html_date_to_last_day( $data_arr[$i]['publication_date'] );
		$data_arr[$i]['html_delete_date'] = f_html_date_to_last_day( $data_arr[$i]['delete_date'] );
		$data_arr[$i]['html_is_owner'] = $data_arr[$i]['user_id'] == f_user_get()['_id'];
		$data_arr[$i]['html_price_min'] = f_number_beauty( $data_arr[$i]['price_min'] ) . ' ' . f_translate('тенге');
		$data_arr[$i]['html_lesson_count_week'] = f_number_beauty( $data_arr[$i]['lesson_count_week'] ) . ' ' . f_translate('занятий') . '/' . f_translate('неделя');
		$data_arr[$i]['html_lesson_count_min'] = f_number_beauty( $data_arr[$i]['lesson_count_min'] ) . ' ' . f_translate('занятий');
		
		$data_arr[$i]['html_equipment'] = $data_arr[$i]['equipment_on'] ? f_translate('Нужно иметь экипировку') : f_translate('Ничего не нужно');
		$data_arr[$i]['html_support_invalid'] = $data_arr[$i]['support_invalid_on'] ? f_translate('Есть') : f_translate('Нет');
		
		
		$data_arr[$i]['html_lesson_dur'] = '';
		$tmp_lesson_dur_hours = intval(date('H', $data_arr[$i]['lesson_dur'] ?: -3600*5));
		$tmp_lesson_dur_minutes = intval(date('i', $data_arr[$i]['lesson_dur'] ?: -3600*5));
		if( $tmp_lesson_dur_hours ){
			$data_arr[$i]['html_lesson_dur'] .= $tmp_lesson_dur_hours . ' ' . f_translate('ч');
		}
		if( $tmp_lesson_dur_minutes ){
			$data_arr[$i]['html_lesson_dur'] .= ' ' . $tmp_lesson_dur_minutes . ' ' . f_translate('мин');
		}
		$data_arr[$i]['html_lesson_dur'] = trim( $data_arr[$i]['html_lesson_dur'] ?: '-' );
		
		
		$data_arr[$i]['html_time'] = '';
		if( $data_arr[$i]['time_min'] ){
			$data_arr[$i]['html_time'] .= f_translate('с') . ' ' . mb_substr($data_arr[$i]['time_min'], 0, -3);
		}
		if( $data_arr[$i]['time_max'] ){
			$data_arr[$i]['html_time'] .= ' ' . f_translate('до') . ' ' . mb_substr($data_arr[$i]['time_max'], 0, -3);
		}
		$data_arr[$i]['html_time'] = trim( $data_arr[$i]['html_time'] );
		
		
		$data_arr[$i]['html_age'] = '';
		if( $data_arr[$i]['age_min'] ){
			$data_arr[$i]['html_age'] .= f_translate('от') . ' ' . $data_arr[$i]['age_min'];
		}
		if( $data_arr[$i]['age_max'] ){
			$data_arr[$i]['html_age'] .= ' ' . f_translate('до') . ' ' . $data_arr[$i]['age_max'];
		}
		$data_arr[$i]['html_age'] = trim( $data_arr[$i]['html_age'] );
		if( $data_arr[$i]['html_age'] ){
			$data_arr[$i]['html_age'] .= ' ' . f_translate('лет');
		}
		
		
		$data_arr[$i]['html_people_count'] = '';
		if( $data_arr[$i]['people_count_min'] ){
			$data_arr[$i]['html_people_count'] .= f_translate('от') . ' ' . f_number_beauty( $data_arr[$i]['people_count_min'] );
		}
		if( $data_arr[$i]['people_count_max'] ){
			$data_arr[$i]['html_people_count'] .= ' ' . f_translate('до') . ' ' . f_number_beauty( $data_arr[$i]['people_count_max'] );
		}
		$data_arr[$i]['html_people_count'] = trim( $data_arr[$i]['html_people_count'] );
		if( $data_arr[$i]['html_people_count'] ){
			$data_arr[$i]['html_people_count'] .= ' ' . f_translate('человек');
		}
		
		$data_arr[$i]['json_city_type_id'] = f_db_select_smart( 'type_item', ['_id' => $data_arr[$i]['city_type_id'] ] )[0];
		$data_arr[$i]['html_city'] = 'г. ' . ($data_arr[$i]['json_city_type_id']['title'] ?: ' не указан');
		
		$data_arr[$i]['json_level_type_id'] = f_db_select_smart( 'type_item', ['_id' => $data_arr[$i]['level_type_id'] ] )[0];
		$data_arr[$i]['html_level'] = f_translate($data_arr[$i]['json_level_type_id']['title']);
		
		$data_arr[$i]['json_category_type_id'] = f_db_select_smart( 'type_item', ['_id' => $data_arr[$i]['category_type_id'] ] )[0];
		$data_arr[$i]['html_category'] = f_translate($data_arr[$i]['json_category_type_id']['title'] ?: 'не указано');
		
		$data_arr[$i]['json_user_id'] = f_db_get_user( ['_id' => $data_arr[$i]['user_id'] ] );
		
		$data_arr[$i]['html_user_name'] = $data_arr[$i]['json_user_id']['name'];
		$data_arr[$i]['html_user_phone'] = $data_arr[$i]['json_user_id']['phone'];
		$data_arr[$i]['html_user_login'] = $data_arr[$i]['json_user_id']['login'];
		$data_arr[$i]['html_user_link'] = 'https://'.  $_SERVER['HTTP_HOST'] . '/@'. $data_arr[$i]['json_user_id']['login'];
		
		$data_arr[$i]['html_link'] = 'https://'.  $_SERVER['HTTP_HOST'] . '/@'. $data_arr[$i]['json_user_id']['login'] .'/item/' . f_num_encode( $data_arr[$i]['_id'] );
		$data_arr[$i]['html_link_edit'] = 'https://'.  $_SERVER['HTTP_HOST'] . '/ads/' . f_num_encode( $data_arr[$i]['_id'] );
		
		$data_arr[$i]['arr_lang_edu_type_arr_id'] = f_db_select_smart(
			'type_item',
			[
				'_id' =>
				[
					'_type' => 'multy_id',
					'value' => $data_arr[$i]['lang_edu_type_arr_id']
				]
			],
			100
		);
		$tmp_arr = [];
		foreach( $data_arr[$i]['arr_lang_edu_type_arr_id'] as $tmp_item ){
			$tmp_arr[] = f_translate($tmp_item['title']);
		}
		$data_arr[$i]['html_lang_edu'] = implode(', ', $tmp_arr);
		
		
		$data_arr[$i]['arr_week_of_day_type_arr_id'] = f_db_select_smart(
			'type_item',
			[
				'_id' =>
				[
					'_type' => 'multy_id',
					'value' => $data_arr[$i]['week_of_day_type_arr_id']
				]
			],
			100
		);
		$tmp_arr = [];
		foreach( $data_arr[$i]['arr_week_of_day_type_arr_id'] as $tmp_item ){
			$tmp_arr[] = f_translate($tmp_item['title']);
		}
		$data_arr[$i]['html_week_of_day'] = implode(', ', $tmp_arr);
		
		
		unset( $data_arr[$i]['gps_address'] );
	}
	
	$result_json['count_show'] = count( $data_arr );
	
	$result_json['count_total'] = f_db_select_count('ads', $sql_where);
	
	$result_json['data_arr'] = $data_arr;
	
	return $result_json;
}

function f_db_get_ads($data_json=[], $limit=1){
	
	$sql_where_arr = [];
	
	if( isset($data_json['_id']) ){
		$sql_where_arr[] = "`_id` = ". f_db_sql_value( $data_json['_id']  );
	}
	if( isset($data_json['_id_str']) ){
		$sql_where_arr[] = "`_id` = ". f_db_sql_value( f_num_decode($data_json['_id_str'])  );
	}
	
	if( isset($data_json['delete_on']) ){
		$sql_where_arr[] = "`delete_on` = ". f_db_sql_value( $data_json['delete_on'] );
	}
	
	if(count($sql_where_arr) == 0){
		return [];
	}
	
	
	$arr_item_json = f_db_get_list_ads( 'AND ' . implode(' AND ', $sql_where_arr) )['data_arr'];
	
	return $arr_item_json[0];
}



function f_db_get_list_upload($sql_where, $sql_continue=""){
	
	$result_json = [
		'sql_query' => '',
		
		'data_arr' => false,
		
		'count_total' => -1,
		'count_show' => -1,
		
		'sql_select' => '',
		'sql_where' => $sql_where,
		'sql_continue' => $sql_continue
	];

	$sql_select = "
		SELECT
			*
		FROM
			`upload`
		";
	
	$sql_query = $sql_select . ' WHERE 1 ' . $sql_where . ' ' . $sql_continue;
		
	$result_json['sql_select'] = $sql_select;
	
	$result_json['sql_query'] = $sql_query;
	
	$data_arr = f_db_select( $sql_query );
	
	for($i=0; $i<count($data_arr); $i++){
		$data_arr[$i]['_id_str'] = f_num_encode( $data_arr[$i]['_id'] );
		
		$data_arr[$i]['html_delete_on'] = f_translate( $data_arr[$i]['delete_on'] == 1 ? 'Удалено' : '' );
		
		$data_arr[$i]['html_create_date'] = f_html_date_to_last_day( $data_arr[$i]['_create_date'] );
		$data_arr[$i]['html_delete_date'] = f_html_date_to_last_day( $data_arr[$i]['delete_date'] );
		$data_arr[$i]['html_is_owner'] = $data_arr[$i]['user_id'] == f_user_get()['_id'];
		
		$data_arr[$i]['json_user_id'] = f_db_get_user( ['_id' => $data_arr[$i]['user_id'] ] );
		
		$data_arr[$i]['html_user_link'] = 'https://'.  $_SERVER['HTTP_HOST'] . '/@'. $data_arr[$i]['json_user_id']['login'];
		
		//$data_arr[$i]['html_link'] = 'https://'.  $_SERVER['HTTP_HOST'] . '/@'. $data_arr[$i]['json_user_id']['login'] .'/item/' . f_num_encode( $data_arr[$i]['_id'] );
		
	}
	
	$result_json['count_show'] = count( $data_arr );
	
	$result_json['count_total'] = f_db_select_count('ads', $sql_where);
	
	$result_json['data_arr'] = $data_arr;
	
	return $result_json;
}


function f_db_get_upload($data_json=[], $limit=1){
	
	$sql_where_arr = [];
	
	if( isset($data_json['_id']) ){
		$sql_where_arr[] = "`_id` = ". f_db_sql_value( $data_json['_id']  );
	}
	if( isset($data_json['_id_str']) ){
		$sql_where_arr[] = "`_id` = ". f_db_sql_value( f_num_decode($data_json['_id_str'])  );
	}
	
	if( isset($data_json['delete_on']) ){
		$sql_where_arr[] = "`delete_on` = ". f_db_sql_value( $data_json['delete_on'] );
	}
	
	if(count($sql_where_arr) == 0){
		return [];
	}
	
	
	$arr_item_json = f_db_get_list_ads( 'AND ' . implode(' AND ', $sql_where_arr) )['data_arr'];
	
	return $arr_item_json[0];
}


/**
 * Собирает ID выбранной категории и всех потомков в дереве ads_category (parent_1_id / parent_2_id / parent_3_id / parent_id).
 */
function f_db_ads_category_descendant_ids($root_id){
	$root_id = intval($root_id);
	if( $root_id <= 0 ){
		return [];
	}
	$all = f_db_select("SELECT `_id`, `parent_1_id`, `parent_2_id`, `parent_3_id`, `parent_id` FROM `ads_category` WHERE `hide_on` = 0");
	if( !$all ){
		return [ $root_id ];
	}
	$ids = [ $root_id ];
	for( $guard = 0; $guard < 100; $guard++ ){
		$added = false;
		foreach( $all as $c ){
			$cid = intval($c['_id']);
			if( in_array($cid, $ids, true) ){
				continue;
			}
			$p1 = isset($c['parent_1_id']) && $c['parent_1_id'] !== null && $c['parent_1_id'] !== '' ? intval($c['parent_1_id']) : 0;
			$p2 = isset($c['parent_2_id']) && $c['parent_2_id'] !== null && $c['parent_2_id'] !== '' ? intval($c['parent_2_id']) : 0;
			$p3 = isset($c['parent_3_id']) && $c['parent_3_id'] !== null && $c['parent_3_id'] !== '' ? intval($c['parent_3_id']) : 0;
			$pid = isset($c['parent_id']) && $c['parent_id'] !== null && $c['parent_id'] !== '' ? intval($c['parent_id']) : 0;
			if( ($p1 && in_array($p1, $ids, true)) || ($p2 && in_array($p2, $ids, true)) || ($p3 && in_array($p3, $ids, true)) || ($pid && in_array($pid, $ids, true)) ){
				$ids[] = $cid;
				$added = true;
			}
		}
		if( !$added ){
			break;
		}
	}
	return array_values( array_unique( $ids ) );
}


/**
 * Публичный URL превью из строки пути ads_img (jpg/webp).
 */
function f_db_ads_img_public_url($jpg_path, $webp_path = ''){
	$p = $jpg_path ?: $webp_path;
	if( $p === null || $p === '' ){
		return '/public/ad_default.jpg';
	}
	if( preg_match('#^https?://#i', $p) ){
		return $p;
	}
	$p = str_replace('\\', '/', $p);
	$p = ltrim($p, '/');
	if( strncmp($p, 'public/', 7) === 0 ){
		return '/' . $p;
	}
	return '/public/upload/img/' . $p;
}


?>