<?php

$status_page = 0;
$alert_page = "";

// Если Авторизован + Активирован
if( f_user_check() == true ){
	f_redirect('/');
}

function f_html_email_activation($_id, $activation_code){
	return '
		<div class="container" style="font-family: Arial, sans-serif; line-height: 1.6; width: 100%; max-width: 500px; margin: 20px auto; padding: 20px;">
			<p>
				<img src="https://'. $GLOBALS['WEB_JSON']['page_json']['site_host'] .'/public/favicon/270x270.png" width="100px" height="100px"/>
			</p>
			<h2>
				'. f_translate('Welcome!') .'
			</h2>
			<p>
				'. f_translate('Activate your account by clicking on the button below') .':
			</p>
			<a href="https://'. $GLOBALS['WEB_JSON']['page_json']['site_host'] . f_page_link('login_activation') .'?token='. f_num_encode($_id) .'&code='. $activation_code .'" style="text-decoration: none; padding: 10px 20px; display: block; width: max-content; max-width: 100%; border-radius: 5px; background: #005bd1; color: white; cursor: pointer; user-select: none;">
				'. f_translate('Activate your account') .'
			</a>
			<p>
				'. f_translate('The activation link is valid for 24 hours') .'
			</p>
			<p>
				'. f_translate("If you didn't do anything, just ignore this message") .'
			</p>
		</div>
	';
}



// Проверяем наличие token(_id_str)
if (isset($_GET['token'])){
	
	// Валидация token (_id)
	$_id = f_num_decode( $_GET['token'] );
	$code = $_GET['code'];
	
	// Поиск _id в БД
	$data_find_user = f_db_get_user(['_id'=>$_id]);
	
	// Проверка что пользвователь найден
	if( isset($data_find_user) ){
		
		// Ждем активации
		$status_page = 1;
		
		if( $data_find_user['activation_on'] ){
			$status_page = 2;
			
		// Есть код активации и он верный
		}else if( isset($code) && $data_find_user['activation_code'] == $code ){
			
			// БД - Обновляем статус активации
			f_db_update_smart(
				"user",
				["_id" => $_id], 
				[
					"activation_on" => 1,
					"activation_date" => date('Y-m-d H:i:s')
				]
			);
			
			$status_page = 2;
		
		// Время ссылки на активацию истекло, генерация новой ссылки
		}else if( strtotime('now') >= strtotime($data_find_user['activation_expired_date']) ){
			$cur_date = date('Y-m-d H:i:s');
			
			$activation_code = f_gen_password(10);
			$activation_expired_date = date("Y-m-d H:i:s", strtotime("+1 days"));
			
			f_db_update_smart(
				"user",
				["_id" => $_id], 
				[
					"activation_expired_date" => $activation_expired_date,
					"activation_send_date" => $cur_date,
					"activation_create_date" => $cur_date,
					"activation_code" => $activation_code
				]
			);
			
			// Отправка Email активации
			f_email_send(
				$data_find_user['email'],
				f_translate('Account activation'),
				f_html_email_activation($_id, $activation_code),
				"main"
			);
				
		// Прошло больше 5 минут, можно переотправить Email письмо
		}else if( strtotime('-5 minutes') >= strtotime($data_find_user['activation_send_date']) ){
			
			f_db_update_smart(
				"user",
				["_id" => $_id], 
				[
					"activation_send_date" => date('Y-m-d H:i:s')
				]
			);
			
			// Отправка Email активации
			f_email_send(
				$data_find_user['email'],
				f_translate('Account activation'),
				f_html_email_activation($_id, $data_find_user['activation_code']),
				"main"
			);
		}
	}
}


f_html_add('no_index');

f_page_title_set( f_translate('Account activation') );

?>



<div class="mini_form_fixed">
	<div class="body_mini_form_fixed">
		
		<a href="<?php f_page_link_echo('login'); ?>" class="btn btn-outline-primary  w-auto" style="position: absolute;top: 25px;left: 25px;"><i class="bi bi-arrow-left"></i></a>
		
		<a href="/" class="mb-3  mx-auto  d-block w-auto">
			<img src="<?php f_page_link_echo('img_logo'); ?>" alt="" width="70" height="70">
		</a>
		
		<h1 class="h5 mb-4 fw-normal text-center" style="font-size: 18px;">
			<?php f_echo_html( f_translate('Account activation') ); ?>
		</h1>
		
		<?php
			// Ждём  активации
			if( $status_page == 1 ){
		?>
				<?php f_echo_html( f_translate('To your email') ); ?> "<b><?php f_echo_html( $data_find_user['email'] ); ?></b>" <?php f_echo_html( f_translate('an email has been sent with a link to activate your account, click on it') ); ?>
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
					<?php f_echo_html( f_translate('Congratulations, the account has been successfully activated, it remains') ); ?>
					<a href="<?php f_page_link_echo('login'); ?>" class="d-block w-auto mx-auto btn btn-lg btn-primary mt-4"><?php f_echo_html( f_translate('Login') ); ?></a>
				</div>
		<?php
			// Аккаунт не найден
			}else{
		?>
				<div class="text-center">
					<?php f_echo_html( f_translate('The account was not found') ); ?>
				</div>
		<?php
			}
		?>
		
	</div>
</div>
