<?php

$status_page = 0;
$alert_page = "";

if( f_user_check() == true ){
	$status_page = 1;
	f_redirect('/');
	
}else if(isset($_POST['login'])){
	
	$email = trim($_POST['login']["email"]);
	$password = trim($_POST['login']["password"]);
	
	if( !f_google_recaptcha_v2($_POST['g-recaptcha-response'])['ok']){
		$status_page = -1;
		$alert_page = 'The captcha is not passed, try again';
		
	}else{
		
		$data_find_user = f_db_get_user([
			'email'		=> $email,
			'password'	=> $password
		]);
		
		if( $data_find_user == false ){
			$status_page = -1;
			$alert_page = 'Invalid Email or Password';
			
		}else if( $data_find_user['activation_on'] == 1 ){
			$status_page = 1;
			$uid = f_num_encode($data_find_user['_id']) . '-' . $data_find_user['password_hash_sha256'];
			
			f_db_update_smart(
				"user",
				["_id" => $data_find_user['_id']], 
				[
					"did_id" => $GLOBALS['WEB_JSON']['did_json']['_id'],
					"auth_date" => date('Y-m-d H:i:s', strtotime('now')),
				]
			);
			
			f_cookie_set("uid", $uid, strtotime("+365 days"));
			
			//f_redirect( f_page_link('login_google') );
			
			f_post_end();
			
		}else if( $data_find_user['activation_on'] == 0 ){
			f_redirect( f_page_link('login_activation') . '?token='. f_num_encode($data_find_user['_id']) );
			
		}
		
	}
	
}



$_post_reg = $_POST['reg'];

if (isset($_post_reg)){
	
	// Валидация данных
	$email = trim($_post_reg["email"]);
	$pass_1 = trim($_post_reg["pass_1"]);
	$pass_2 = trim($_post_reg["pass_2"]);
	//$name = trim($_post_reg["name"]);
	
	if( !f_email_validate( $email ) ){
		$alert_page = 'The email address is incorrect';
		$status_page = -1;
		
	}else if( 2 > strlen($pass_1) || strlen($pass_1) > 30 ){
		$alert_page = 'The password is incorrect';
		$status_page = -1;
		
	}else if( $pass_1 !== $pass_2 ){
		$alert_page = "Passwords don't match";
		$status_page = -1;
	
	/*
	}else if( 2 > strlen($name) || strlen($name) > 100 ){
		$alert_page = 'ФИО указано неверно';
		$status_page = -1;
	*/
	
	// Проверка - Капчи
	}else if(!f_google_recaptcha_v2($_POST['g-recaptcha-response'])['ok']){
		$alert_page = 'The captcha "I am not a robot" has not been passed, try again';
		$status_page = -1;
	}
	
	
	// Поиск в БД
	if($status_page == 0){
		
		$data_find_user = f_db_get_user(['email'=>$email]);
		
		// Пользователь найден
		if( $data_find_user ){
			
			// Пользователь - Не активирован
			if( $data_find_user['activation_on'] == 0 ){
				f_redirect( f_page_link('login_activation') .'?token='.f_num_encode($data_find_user['_id']) );
			
			// Вывод ошибки дубликации данных
			}else if($email == $data_find_user['email']){
				$alert_page = 'A user with such an Email already exists';
				$status_page = -1;
				
			}
		}
	}
	
	// Сохранение - сохранить в БД и отправить ссылку активации на Email
	if($status_page == 0){
		
		$cur_date = date('Y-m-d H:i:s');
		
		$activation_code = f_gen_password(10);
		$activation_expired_date = date("Y-m-d H:i:s", strtotime("+1 days"));
		
		// Сохранение в БД
		$_id = f_db_insert(
			"user",
			[
				'email'						=> $email,
				'name'						=> explode('@', $email)[0],
				'password_hash_sha256'		=> hash('sha256', $pass_1),
				
				"activation_expired_date"	=> $activation_expired_date,
				"activation_create_date"	=> $cur_date,
				"activation_code"			=> $activation_code,
				
				"_create_date"				=> $cur_date,
				"_create_did_id"			=> $GLOBALS['WEB_JSON']['did_json']['_id'],
			]
		);
			
		f_redirect( f_page_link('login_activation') . '?token='. f_num_encode($_id) );
	}
	
}


f_html_add('google_recaptcha_v2');

f_page_title_set( f_translate('Authorization') );

?>


<style>

[google_auth_link]{
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 0;
	width: 100%;
}
[google_auth_link]  img{
	display: block;
	width: 30px;
	height: 30px;
	margin-right: var(--v_p_10);
	padding: var(--v_p_5);
	border-radius: var(--v_radius);
	background: var(--v_c_white);
}

</style>
	
<div class="mini_form_fixed"  >

	<div class="body_mini_form_fixed">
		
		<a href="/" class="mb-3  mx-auto  d-block w-auto">
			<img src="<?php f_page_link_echo('img_logo'); ?>" alt="" width="70" height="70">
		</a>
		
		<h1 class="h5 mb-4 fw-normal text-center">
			<?php f_translate_echo('Authorization'); ?>
		</h1>
			
		<div google_auth_link   class="btn  btn-primary  btn-lg">
			<img src="<?php f_page_link_echo('img_logo_google'); ?>">
			<?php f_translate_echo('Log in'); ?> Google
		</div>
			
		<hr class="mt-4 mb-0">
		<div style="width:max-content;margin: 0 auto;margin-top: -29px;transform: translateY(50%);background: var(--v_c_white);padding: 0 10px;position: relative;"  class="mb-4">
			<?php f_translate_echo('or'); ?>
		</div>
		
		<div class="nav nav-pills mb-3" role="tablist">
			<button class="nav-link   w-50   active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">
				<?php f_translate_echo('Login'); ?>
			</button>
			<button class="nav-link   w-50" id="pills-reg-tab" data-bs-toggle="pill" data-bs-target="#pills-reg" type="button" role="tab" aria-controls="pills-reg" aria-selected="false">
				<?php f_translate_echo('Registration'); ?>
			</button>
		</div>
		
		<div class="tab-content" id="pills-tabContent">
			<div class="tab-pane fade show active" id="pills-login" role="tabpanel" tabindex="0">
				<form method="POST">
					<div class="form-floating  mb-3">
						<input type="text" name="login[email]" class="form-control" id="input_login" placeholder="_" value="<?php f_echo_html($_POST['login']["email"]); ?>">
						<label for="input_login">
							<?php echo('Email'); ?>
						</label>
					</div>
					
					<div class="form-floating  mb-3  box_show_pass">
						<input type="password" name="login[password]" class="form-control  pe-5" id="input_password" placeholder="_">
						<label for="input_password">
							<?php f_translate_echo('Password'); ?>
						</label>
						<div class="icon_show_pass  w-auto   text-muted  bi bi-eye-fill"  style="position: absolute; top: 18px; right: 20px; cursor: pointer;"></div>
					</div>
					
					<div class="g-recaptcha  mb-3" data-sitekey="<?php f_echo_html($GLOBALS['WEB_JSON']['api_json']['google_recaptcha_v2_public']); ?>"></div>
					
					<div class="text-danger text-center  mb-3 <?php f_echo_html($status_page == -1 ? '' : 'd-none' ); ?>"><?php f_translate_echo($alert_page); ?></div>
					
					<button id="btn_login"  class="btn btn-primary btn-lg w-100 py-2  mb-3" type="submit">
						<?php f_translate_echo('Login'); ?>
					</button>
					
					<div class="row  mb-3">
						<a class="mx-auto  col-6 text-center" href="<?php f_page_link_echo('login_forgout'); ?>" style="text-decoration: none;"><?php f_translate_echo('Forgot password?'); ?></a>
					</div>
					
					<div class="small  text-center">
						<?php f_translate_echo('When you log in, you agree with our'); ?>
						<a href="<?php f_page_link_echo('page_rules'); ?>" target="_blank" class="d-block"><?php f_translate_echo('Terms of Use'); ?></a>
					</div>
					
				</form>
			</div>
			
			<div class="tab-pane fade" id="pills-reg" role="tabpanel" tabindex="0">
				<form method="POST">
					<div class="form-floating  mb-3">
						<input type="text" name="reg[email]" class="form-control" id="input_reg_login" placeholder="_" value="<?php f_echo_html($_post_reg["email"]); ?>">
						<label for="input_reg_login">
							<?php f_echo_html( f_translate('Email') ); ?>
							<span class="text-danger">*</span>
						</label>
					</div>
					
					<div class="form-floating  mb-3  box_show_pass">
						<input type="password" name="reg[pass_1]" class="form-control  pe-5" id="input_reg_pass_1" placeholder="_" value="<?php f_echo_html($_post_reg["pass_1"]); ?>">
						<label for="input_reg_pass_1">
							<?php f_echo_html( f_translate('Password') ); ?>
							<span class="text-danger">*</span>
						</label>
						<div class="icon_show_pass  w-auto   text-muted  bi bi-eye-fill"  style="position: absolute; top: 18px; right: 20px; cursor: pointer;"></div>
					</div>
					
					<div class="form-floating  mb-3  box_show_pass">
						<input type="password" name="reg[pass_2]" class="form-control  pe-5" id="input_reg_pass_2" placeholder="_" value="<?php f_echo_html($_post_reg["pass_2"]); ?>">
						<label for="input_reg_pass_2">
							<?php f_echo_html( f_translate('Confirm the password') ); ?>
							<span class="text-danger">*</span>
						</label>
						<div class="icon_show_pass  w-auto   text-muted  bi bi-eye-fill"  style="position: absolute; top: 18px; right: 20px; cursor: pointer;"></div>
					</div>
					
					<div class="g-recaptcha  mb-3" data-sitekey="<?php f_echo_html($GLOBALS['WEB_JSON']['api_json']['google_recaptcha_v2_public']); ?>"></div>
					
					<div class="text-danger text-center  mb-3 <?php f_echo_html($status_page == -1 ? '' : 'd-none' ); ?>"><?php f_echo_html( f_translate($alert_page) ); ?></div>
					
					<button class="btn btn-primary btn-lg w-100 py-2  mb-3" type="submit">
						<?php f_echo_html( f_translate('Register') ); ?>
					</button>
					
					<div class="small  text-center">
						<?php f_translate_echo('I agree with '); ?>
						<a href="<?php f_page_link_echo('page_rules'); ?>" target="_blank" class="mx-1"><?php f_translate_echo('Terms of Use'); ?></a>,
						<?php f_translate_echo('and also with the transmission and processing of my data.'); ?>
						<br>
						<?php f_translate_echo('I confirm my legal age and responsibility for the placement of the ad'); ?>
					</div>
				</form>
			</div>
			
		</div>

	</div>
</div>


<script>

document.addEventListener("DOMContentLoaded", function(event){

	$('[google_auth_link]').on('click', function(){
		let jq_this = $(this);
		let link = '<?php f_page_link_echo('login_google'); ?>';
		
		let w = 500;
		let h = 600;
		let left = window.top.outerWidth / 2 + window.top.screenX - (w / 2);
		let top = window.top.outerHeight / 2 + window.top.screenY - (h / 2);

		// Открываем новое окно по центру текущего окна браузера
		let new_window = window.open(link, 'google_login_window', 'width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

		// Проверяем, закрыто ли всплывающее окно
		let th_window = setInterval(function() { 
			if(new_window.closed) {
				clearInterval(th_window);
				window.location.reload();
			}
		}, 1000);
		
	})

	if( gl_hash_json['login'] || gl_hash_json['pass'] ){
		$('#input_login').val(gl_hash_json['login']);
		$('#input_password').val(gl_hash_json['password']);
		f_url_remove_hash();
	}

})

</script>