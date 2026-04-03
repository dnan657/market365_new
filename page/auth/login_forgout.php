<?php

$status_page = 0;
$alert_page = "";

function f_html_email_forgout($_id, $password_hash){
	return '
		<div class="container" style="font-family: Arial, sans-serif; line-height: 1.6; width: 100%; max-width: 500px; margin: 20px auto; padding: 20px;">
			<p>
				<img src="https://'. $GLOBALS['WEB_JSON']['page_json']['site_host'] .'/public/favicon/270x270.png" width="100px" height="100px"/>
			</p>
			<h2>
				' . f_translate( 'Password Recovery' ) . '
			</h2>
			<p>
				' . f_translate( 'Please activate your account by clicking on the button below:' ) . '
			</p>
			<a href="https://'. $GLOBALS['WEB_JSON']['page_json']['site_host'] . f_page_link('login_forgout') . '?token='. f_num_encode($_id) .'&password_hash='. $password_hash .'" style="text-decoration: none; padding: 10px 20px; display: block; width: max-content; max-width: 100%; border-radius: 5px; background: #005bd1; color: white; cursor: pointer; user-select: none;">
				' . f_translate( 'Change your password' ) . '
			</a>
			<p>
				' . f_translate( 'The activation link is valid for 24 hours' ) . '
			</p>
			<p>
				' . f_translate( 'If you didn\'t do anything, just ignore this message' ) . '
			</p>
		</div>
	';
}


// Проверяем наличие token(_id_str)
if (isset($_GET['token'])){
	
	// Валидация token (_id)
	$_id = f_num_decode( $_GET['token'] );
	$password_hash = $_GET['password_hash'];
	
	// Поиск _id в БД
	$data_find_user = f_db_get_user(['_id'=>$_id]);
	
	if( !isset($data_find_user) ){
		//$status_page = -1;
		//$alert_page = "Пользователь не найден";
	
	// Проверка что пользвователь найден
	}else{
		
		// Ждем активации
		$status_page = 1;
		
		// Пользователь - Не активирован
		if( $data_find_user['activation_on'] == 0 ){
			f_redirect( f_page_link('login_activation') . '?token='.f_num_encode($data_find_user['_id']) );
		
		// Время ссылки на активацию истекло, генерация новой ссылки
		}else if( strtotime('now') >= strtotime($data_find_user['forgout_expired_date'] ?? '+1 days') ){
		
			// БД - Обновляем статус
			f_db_update_smart(
				"user",
				["_id" => $_id], 
				[
					"forgout_password"				=> null,
					"forgout_password_hash_sha256"	=> null,
					"forgout_create_date"			=> null,
					"forgout_expired_date"			=> null,
					"forgout_send_date"				=> null,
				]
			);
			
			$status_page = 0;
		
		// Есть password_hash и он верный
		}else if( isset($password_hash) && $data_find_user['forgout_password_hash_sha256'] == $password_hash ){
			
			// БД - Обновляем статус
			f_db_update_smart(
				"user",
				["_id" => $_id], 
				[
					"password_hash_sha256"			=> $data_find_user['forgout_password_hash_sha256'],
					"forgout_password"				=> null,
					"forgout_password_hash_sha256"	=> null,
					"forgout_create_date"			=> null,
					"forgout_expired_date"			=> null,
					"forgout_send_date"				=> null,
				]
			);
			
			$status_page = 2;
		
		// Прошло больше 10 минут, можно переотправить Email письмо
		}else if( strtotime('-10 minutes') >= strtotime($data_find_user['forgout_send_date']) ){
			
			f_db_update_smart(
				"user",
				["_id" => $_id], 
				[
					"forgout_send_date" => date('Y-m-d H:i:s')
				]
			);
			
			// Отправка Email активации
			f_email_send(
				$data_find_user['email'],
				'Password Recovery',
				f_html_email_forgout($_id, $data_find_user['forgout_password_hash_sha256']),
				"main"
			);
		}
	}
}


$_post_form = $_POST['forgout'];
if (isset( $_post_form )){
	// Валидация данных
	$email = trim($_post_form["email"]);
	$pass_1 = trim($_post_form["password_1"]);
	$pass_2 = trim($_post_form["password_2"]);
	
	
	if( 2 > strlen($email) || strlen($email) > 100 ){
		$alert_page = 'The Email address is incorrect';
		$status_page = -1;
		
	}else if( 2 > strlen($pass_1) || strlen($pass_1) > 30 ){
		$alert_page = 'The password is incorrect';
		$status_page = -1;
		
	}else if( $pass_1 !== $pass_2 ){
		$alert_page = 'Passwords don\'t match';
		$status_page = -1;
		
	// Проверка - Капчи
	}else if(!f_google_recaptcha_v2($_POST['g-recaptcha-response'])['ok']){
		$alert_page = 'The captcha is not passed, try again';
		$status_page = -1;
	} 
	
	

	// Поиск в БД
	if($status_page == 0){
		
		$data_find_user = f_db_get_user([
			'email'		=> $email,
			'phone'		=> $email
		]);
		
		// Пользователь найден
		if( !isset($data_find_user) ){
			$alert_page = 'The user was not found with this Email';
			$status_page = -1;
			
		}else if( !$data_find_user['email'] ){
			$alert_page = 'The user does not have an Email address specified';
			$status_page = -1;
			
		}else{
			
			
			
			// Пользователь - Не активирован
			if( $data_find_user['activation_on'] == 0 ){
				f_redirect( f_page_link('login_activation') . '?token='.f_num_encode($data_find_user['_id']) );
			}
			
			$password = $pass_1;
			$password_hash = hash('sha256', $password);
			$cur_date = date('Y-m-d H:i:s');
			$expired_date = date("Y-m-d H:i:s", strtotime("+1 days"));;
			
			// Сохранение в БД
			f_db_update_smart(
				"user",
				["_id" => $data_find_user['_id']], 
				[
					"forgout_password_hash_sha256"	=> $password_hash,
					"forgout_create_date"			=> $cur_date,
					"forgout_expired_date"			=> $expired_date,
					"forgout_send_date"				=> null,
				]
			);
				
			f_redirect( f_page_link('login_forgout') . '?token='. f_num_encode( $data_find_user['_id'] ));
		
		}
	}}


f_html_add('google_recaptcha_v2');

f_page_title_set( f_translate('Changing the password') );

?>


<div class="mini_form_fixed">
	<div class="body_mini_form_fixed">
		
		<a href="<?php f_page_link_echo('login'); ?>" class="btn btn-outline-primary  w-auto" style="position: absolute;top: 25px;left: 25px;"><i class="bi bi-arrow-left"></i></a>
		
		<a href="/" class="mb-3  mx-auto  d-block w-auto">
			<img src="<?php f_page_link_echo('img_logo'); ?>" alt="" width="70" height="70">
		</a>
		
		<h1 class="h5 mb-4 fw-normal text-center">
			<?php f_echo_html( f_translate('Password Recovery') ); ?>
		</h1>
		
		<?php
			// Ждём  активации
			if( $status_page == 1 ){
		?>
				<?php f_echo_html( f_translate('To your email') ); ?> "<b><?php f_echo_html( $data_find_user['email'] ); ?></b>" <?php f_echo_html( f_translate('An email has been sent with a link to change your password, click on it.') ); ?>
				<br>
				<br>
				<?php f_echo_html( f_translate('The link will be valid for 24 hours') ); ?>
				<br>
				<br>
				<?php f_echo_html( f_translate('If you haven\'t found the email, check the "Spam" or "Trash" tabs') ); ?>
				<br>
				<br>
				<a href="http://<?php f_echo_html( explode('@', $data_find_user['email'])[1] ); ?>" target="_blank" class="d-block w-auto mx-auto btn btn-lg btn-primary"><?php f_echo_html( f_translate('Open the mail') ); ?></a>
		<?php
			// Аккаунт только что Активирован
			}else if( $status_page == 2 ){
		?>
				<div class="text-center">
					<?php f_echo_html( f_translate('The password has been changed, now it remains') ); ?>
					<a href="<?php f_page_link_echo('login'); ?>" class="d-block w-auto mx-auto btn btn-lg btn-primary mt-4"><?php f_echo_html( f_translate('Login') ); ?></a>
				</div>
		<?php
			// Форма восстановения
			}else{
		?>
				<form method="POST">
				<div class="form-floating  mb-3">
					<input type="text" name="forgout[email]" class="form-control" id="floatingInput" placeholder="_" value="<?php f_echo_html($_POST['forgout']["email"]); ?>">
					<label for="floatingInput">
						<?php f_echo_html( f_translate('Email') ); ?>
					</label>
				</div>
				
				<div class="form-floating  mb-3  box_show_pass">
					<input type="password" name="forgout[password_1]" class="form-control  pe-5" id="floatingPassword" placeholder="_" value="<?php f_echo_html($_POST['forgout']["password_1"]); ?>">
					<label for="floatingPassword">
						<?php f_echo_html( f_translate('New password') ); ?>
					</label>
					<div class="icon_show_pass  w-auto   text-muted  bi bi-eye-fill"  style="position: absolute; top: 18px; right: 20px; cursor: pointer;"></div>
				</div>
				<div class="form-floating  mb-3  box_show_pass">
					<input type="password" name="forgout[password_2]" class="form-control  pe-5" id="floatingPassword" placeholder="_" value="<?php f_echo_html($_POST['forgout']["password_2"]); ?>">
					<label for="floatingPassword">
						<?php f_echo_html( f_translate('Confirm the new password') ); ?>
					</label>
					<div class="icon_show_pass  w-auto   text-muted  bi bi-eye-fill"  style="position: absolute; top: 18px; right: 20px; cursor: pointer;"></div>
				</div>
			
				<div class="g-recaptcha  mb-3" data-sitekey="<?php f_echo_html($GLOBALS['WEB_JSON']['api_json']['google_recaptcha_v2_public']); ?>"></div>
				
				<div class="text-danger text-center  mb-3 <?php f_echo_html($status_page == -1 ? '' : 'd-none' ); ?>"><?php f_echo_html($alert_page); ?></div>
				
				<button class="btn btn-primary btn-lg w-100 py-2" type="submit">
					<?php f_echo_html( f_translate('Recovery') ); ?>
				</button>
				
			</form>
		<?php
			}
		?>
		
	</div>
</div>
