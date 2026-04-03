<?php

// Проверка на авторизацию
f_user_check_exist_redirect();

$service_name = $WEB_JSON["uri_dir_arr"][2]; // /login/oauth/GOOGLE_service_name

$code = $_GET['code'];

if ( $service_name == 'google' ){
	
	if( !isset( $code ) ) {
		f_redirect( f_google_auth_create_auth_url() );
		
	}else{
		
		$token_data = f_google_auth_get_access_token( $code );
		//var_dump( $token_data );
		
		if (isset($token_data['access_token'])) {
			
			$user_data = f_google_auth_get_user_data($token_data['access_token']);
			//$user_data['verified_email'];
			
			$google_id = $user_data['id'];
			$email = $user_data['email'];
			$firstname = $user_data['given_name'];
			$lastname = $user_data['family_name'];
			$user_data['picture'];
			
			
			// Поиск EMAIL в user
			$data_find_user = f_db_get_user(['email'=>$email]);

			// Нету, то создать, с авто верификацией, и установить пароль google_id
			if( !$data_find_user ){
				
				$cur_date = date('Y-m-d H:i:s');
				
				// Сохранение в БД
				$_id = f_db_insert(
					"user",
					[
						'email'						=> $email,
						'google_id'					=> $google_id,
						'google_login_first_date'	=> $cur_date,
						
						'name'						=> trim($firstname . ' ' . $lastname),
						
						"activation_on"				=> 1,
						"activation_date"			=> $cur_date,
						"activation_create_date"	=> $cur_date,
						
						"_create_date"				=> $cur_date,
						"_create_did_id"			=> $GLOBALS['WEB_JSON']['did_json']['_id'],
					]
				);
				
				$data_find_user = f_db_get_user(['_id'=>$_id]);
			}
			
			if( !$data_find_user['google_id'] ){
				f_db_update_smart("user", ['_id'=>$_id], ['google_id'=>$google_id, 'google_login_first_date'=>$cur_date]);
			}
			
			// Добавить COOKIE UID 
			// Перенаправление на главную
			f_user_set_cookie( $data_find_user, true);
			
			//var_dump( $user_data ); // Теперь у вас есть данные пользователя, и вы можете их использовать
			
			//f_redirect('/');
		}
		
		echo("<html><head><script>window.close();</script></head></html>");
		exit();
		
	}
}

f_redirect( f_page_link('login') );


?>