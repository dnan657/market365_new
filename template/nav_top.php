
<style>

.nav_top{
	position: fixed;
	left: 0px;
	top: 0px;
	z-index: 1001;

	display: flex;
	width: 100%;
	align-items: center;
	background: var(--v_c_navbar);
	border-bottom: 1px solid var(--v_c_border);
}


.body_nav_top{
	width: 100%;
	height: var(--v_navbar_height);
	display: flex;
	align-items: center;
	justify-content: space-between;
	user-select: none;
}



.logo_nav_top{
	text-decoration: none;
	color: var(--v_c_black);
	font-weight: 700;
	font-family: Helvetica;
	font-size: 24px;
	margin: 0;
	margin-right: var(--v_p_15);
	display: flex;
	align-items: center;
	justify-content: center;
	line-height: 1;
	gap: var(--v_p_10);
}
.logo_nav_top  img{
	width: 40px;
}

.right_nav_top{
	display: flex;
	flex-wrap: nowrap;
	margin-left: auto;
	align-items: center;
	gap: var(--v_p_10);
}

.right_nav_top a{
	text-decoration: none;
	color: var(--v_c_black);
	font-size: var(--v_font_small);
	padding: var(--v_p_5) var(--v_p_10);
	white-space: nowrap;
	
	display: flex;
	flex-wrap: nowrap;
	align-items: center;
}
.right_nav_top  i{
	font-size: 20px;
	margin-right: var(--v_p_10);
	display: block;
	line-height: 1;
	position: relative;
}
.right_nav_top  a  i  .count_icon,
.nav_mobile  a  i  .count_icon{
	display: flex;
	align-items: center;
	justify-content: center;
	flex-wrap: nowrap;
	
	position: absolute;
	left: 10px;
	top: -6px;
	
	border-radius: 20px;
	font-size: var(--v_font_small_2);
	background: var(--v_c_red);
	color: var(--v_c_white);
	
	padding: 2px 4px;
	line-height: 1;
	font-style: normal;
	white-space: nowrap;
}

.nav_mobile  a  i  .count_icon{
	top: 12px;
	left: 16px;
}

.right_nav_top  .bi-person{
	font-size: 25px;
}

#box_collapse_user_nav_top{
	position: absolute;
	bottom: 0px;
	left: 0px;
	transform: translateY(100%);
	z-index: 100;
	min-width: 100%;
	max-width: max-content;
}
#box_collapse_user_nav_top  a{
	border-radius: var(--v_radius);
	width: 100%;
}
#box_collapse_user_nav_top  i{
	margin-right: var(--v_p_10);
}

.collapse_user_nav_top{
	position: relative;
}
.collapse_user_nav_top  a[aria-expanded="true"]  .bi-chevron-down::before{
	transform: rotate(180deg);
}

@media (hover: hover){
	#box_collapse_user_nav_top  a:hover{
		background: rgb(0,0,0,0.05);
	}
}

/*
.info_user_collapse_user_nav_top{
	display: flex;
	flex-wrap: nowrap;
	align-items: center;
	justify-content: flex-start;
	padding: var(--v_p_5) var(--v_p_10);
}
*/
.name_user_collapse_user_nav_top{
	font-size: var(--v_font_small);
	line-height: 1;
	padding-bottom: var(--v_p_5);
	
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
	max-width: 120px;
	width: max-content;
}
.balance_user_collapse_user_nav_top{
	font-size: var(--v_font_small_extra);
	color: var(--v_c_black_80);
	line-height: 1;
}



.nav_bottom{
	position: fixed;
	left: 0px;
	bottom: 0px;
	z-index: 1001;
	width: 100%;
}

.nav_mobile{

	display: flex;
	align-items: center;
	justify-content: space-around;
	
	width: 100%;
	height: var(--v_navbar_height);
	
	padding-left: var(--v_p_10);
	padding-right: var(--v_p_10);
	
	background: var(--v_c_navbar);
	border-top: 1px solid var(--v_c_border);
}


.nav_mobile  a{
	display: flex;
	flex-direction: column;
	flex-wrap: nowrap;
	align-items: center;
	justify-content: center;
	
	text-decoration: none;
	color: var(--v_c_black);
	line-height: 1;
	white-space: nowrap;
	padding: 0 var(--v_p_10);
	height: 100%;
	user-select: none;
}


.nav_mobile  a  i{
	display: block;
	margin-bottom: var(--v_p_5);
	font-size: 25px;
	position: relative;
}
.nav_mobile  a  span{
	display: block;
	font-size: var(--v_font_small_extra);
	font-size: 11px;
}




.widget_policy_cookie{
	width: 100%;
	background: var(--v_c_white);
	color: var(--v_c_black);
	font-size: var(--v_font_small);
	padding: var(--v_p_20) 0;
	border-top: 1px solid var(--v_c_border);
}


</style>

<div class="nav_top">
	<div  class="container  body_nav_top">
	
		<div class="left_nav_top">
			<a class="logo_nav_top" href="/">
				<img src="<?php f_page_link_echo('img_logo'); ?>" />
				<span class="">
					<?php f_echo_html( $GLOBALS['WEB_JSON']['page_json']['site_name'] ); ?>
				</span>
			</a>
		</div>
		
		<div class="right_nav_top  desktop_hide">
			
			<a href="#search">
				<i class="bi bi-search  me-0"></i>
			</a>
			
			<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'user_notifications'); ?>">
				<i class="bi bi-bell  me-0" style="transform: scale(1.1);">
					<?php f_echo( f_user_get()['html_count_notifications'] ? '<div class="count_icon">'. f_user_get()['html_count_notifications'] .'</div>' : '' ); ?>
				</i>
			</a>
			
		</div>
		
		<div class="right_nav_top  mobile_hide">
			
			<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'user_messages'); ?>"  title="<?php f_translate_echo( 'Messages' ); ?>">
				<i class="bi bi-chat">
					<?php f_echo( f_user_get()['html_count_messages'] ? '<div class="count_icon">'. f_user_get()['html_count_messages'] .'</div>' : '' ); ?>
				</i>
				<?php f_translate_echo( 'Messages' ); ?>
			</a>
			
			<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'user_favorites'); ?>"  title="<?php f_translate_echo( 'Favorites' ); ?>">
				<i class="bi bi-heart  m-0  pt-1">
					<?php f_echo( f_user_get()['html_count_favorites'] ? '<div class="count_icon">'. f_user_get()['html_count_favorites'] .'</div>' : '' ); ?>
				</i>
			</a>
			
			<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'user_notifications'); ?>"  title="<?php f_translate_echo( 'Notifications' ); ?>">
				<i class="bi bi-bell  m-0  pt-1">
					<?php f_echo( f_user_get()['html_count_notifications'] ? '<div class="count_icon">'. f_user_get()['html_count_notifications'] .'</div>' : '' ); ?>
				</i>
			</a>
			
			<?php
				if( f_user_check() == false ){
			?>
				<a href="<?php f_page_link_echo('login'); ?>"  title="<?php f_translate_echo( 'My account' ); ?>">
						<i class="bi bi-person"></i>
						<?php f_translate_echo( 'My account' ); ?>
				</a>
			<?php
				}else{
			?>
			
				<div class="collapse_user_nav_top">
					<a data-bs-toggle="collapse" href="#box_collapse_user_nav_top" role="button" aria-expanded="false">
						<i class="bi bi-person"></i>
						<div>
							<div class="name_user_collapse_user_nav_top" title="<?php f_echo_html( f_user_get()['html_name'] ); ?>"><?php f_echo_html( f_user_get()['html_name'] ); ?></div>
							<div class="balance_user_collapse_user_nav_top"><?php f_echo_html( f_translate( 'Balance' ) . ': ' . f_user_get()['html_count_balance'] ); ?></div>
						</div>
						
						<i class="bi bi-chevron-down  ms-2  me-0"></i>
					</a>
					
					
					<div class="collapse" id="box_collapse_user_nav_top">
						<div class="card card-body  w-100  py-3  px-2  gap-2">
							
							<!--
							<div class="info_user_collapse_user_nav_top">
								<div class="icon_user_collapse_user_nav_top"></div>
								<div>
									<div class="name_user_collapse_user_nav_top" title="<?php f_echo_html( f_user_get()['html_name'] ); ?>"><?php f_echo_html( f_user_get()['html_name'] ); ?></div>
									<div class="balance_user_collapse_user_nav_top"><?php f_echo_html( f_translate( 'Balance' ) . ': ' . f_user_get()['html_count_balance'] ); ?></div>
								</div>
							</div>
							-->
							
							<a  href="<?php f_page_link_echo('user_ads'); ?>">
								<i class="bi bi-bag-check"></i>
								<?php f_translate_echo('My Ads'); ?>
							</a>
							
							<!--
							<a  href="<?php f_page_link_echo('user_messages'); ?>">
								<i class="bi bi-chat"></i>
								<?php f_translate_echo('Messages'); ?>
							</a>
							-->
							
							<a  href="<?php f_page_link_echo('user_pays'); ?>">
								<i class="bi bi-wallet2"></i>
								<?php f_translate_echo('Pays'); ?>
							</a>
							
							<!--
							<a  href="<?php f_page_link_echo('user_notifications'); ?>">
								<i class="bi bi-bell"></i>
								<?php f_translate_echo('Notifications'); ?>
							</a>
							-->
							
							<a  href="<?php f_page_link_echo('user_settings'); ?>">
								<i class="bi bi-gear"></i>
								<?php f_translate_echo('Settings'); ?>
							</a>
							
							<a class="text-danger" href="<?php f_page_link_echo('user_exit'); ?>">
								<i class="bi bi-box-arrow-left"></i>
								<?php f_translate_echo('Logout'); ?>
							</a>
						</div>
					</div>
				</div>
			
			<?php
				}
			?>
			
			
			<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'ads_create' ); ?>" class="btn btn-primary  py-2  pe-3  text-white  ms-2">
				<i class="bi bi-plus-lg"></i>
				<?php f_translate_echo( 'Submit an ad' ); ?>
			</a>
			
		</div>
		
	</div>
</div>



<div class="nav_bottom">

	<div class="widget_policy_cookie  d-none">
		<div class="container">
			<div class="row  align-items-start">
				<div class="col-md-9">
					<i class="bi bi-cookie  me-2"></i>
					<?php f_translate_echo('By continuing to use our site, you consent to the processing of cookies that ensure the proper operation of the site.'); ?>
					<a href="<?php f_page_link_echo('page_privacy'); ?>" target="_blank"><?php f_translate_echo('More detailed'); ?>...</a>
				</div>
				<div class="col-md-3  mt-2  mt-md-0  btn  btn-sm btn-success">
					<?php f_echo_html( f_translate('Agree') ); ?> 
				</div>
			</div>
		</div>
	</div>
	
	<div class="nav_mobile  desktop_hide">
		<a href="/">
			<i class="bi bi-house<?php f_echo_html($GLOBALS['WEB_JSON']['uri_clean'] != '' ? '' : '-fill'); ?>"></i>
			<span><?php f_translate_echo( 'Home' ); ?></span>
		</a>
		
		<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'user_messages'); ?>">
			<i class="bi bi-chat<?php f_echo_html('/'.$GLOBALS['WEB_JSON']['uri_clean'] != f_page_link('user_messages') ? '' : '-fill'); ?>">
				<?php f_echo( f_user_get()['html_count_messages'] ? '<div class="count_icon">'. f_user_get()['html_count_messages'] .'</div>' : '' ); ?>
			</i>
			<span><?php f_translate_echo( 'Messages' ); ?></span>
		</a>
		
		<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'ads_create' ); ?>">
			<i class="bi bi-plus-circle<?php f_echo_html('/'.$GLOBALS['WEB_JSON']['uri_clean'] != f_page_link('ads_create') ? '' : '-fill'); ?>"></i>
			<span><?php f_translate_echo( 'Create' ); ?></span>
		</a>
		
		<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'user_favorites'); ?>">
			<i class="bi bi-heart<?php f_echo_html('/'.$GLOBALS['WEB_JSON']['uri_clean'] != f_page_link('user_favorites') ? '' : '-fill'); ?>"></i>
			<span><?php f_translate_echo( 'Favorites' ); ?></span>
		</a>
		
		<a href="<?php f_page_link_echo( f_user_check() == false ? 'login' : 'user'); ?>">
			<i class="bi bi-person<?php f_echo_html('/'.$GLOBALS['WEB_JSON']['uri_clean'] != f_page_link('user') ? '' : '-fill'); ?>"  style="transform: scale(1.2);"></i>
			<span><?php f_translate_echo( 'Profile' ); ?></span>
		</a>
	</div>
	
</div>




<?php
/*
ob_start();
$GLOBALS['WEB_JSON']['page_json']['html_bottom'] .= ob_get_clean();
*/
?>

<script>

document.addEventListener("DOMContentLoaded", function(event){
	
	let jq_collapse_user_nav_top = $('.collapse_user_nav_top');
    let jq_box_collapse_user_nav_top = $('#box_collapse_user_nav_top');

	// ====================
    let th_hide_box_user;
	
	// Функция для задержки скрытия элемента
	function f_hide_box_user_nav_top() {
		th_hide_box_user = setTimeout(function() {
			jq_box_collapse_user_nav_top.collapse('hide');
		}, 300); // Задержка перед скрытием
	}

	// Когда пользователь наводит курсор на область показа меню
	jq_collapse_user_nav_top.on('mouseenter', function() {
		clearTimeout(th_hide_box_user);
		jq_box_collapse_user_nav_top.collapse('show');
	}).on('mouseleave', function() {
		f_hide_box_user_nav_top();
	});

	// Добавляем аналогичное поведение для самой раскрывающейся области
	jq_box_collapse_user_nav_top.on('mouseenter', function() {
		clearTimeout(th_hide_box_user); // Останавливаем таймер скрытия при наведении
	}).on('mouseleave', function() {
		f_hide_box_user_nav_top(); // Запускаем таймер скрытия при уходе курсора
	});

	// Дополнительно: если курсор выходит за пределы окна браузера
	$(document).on('mouseleave', function(event) {
		if (event.clientY <= 0 || event.clientX <= 0 || event.clientX >= window.innerWidth || event.clientY >= window.innerHeight) {
			f_hide_box_user_nav_top(); // Скрыть меню, если курсор выходит за пределы окна
		}
	});
	
	// Дополнительно: скрытие при потере фокуса окна
	$(window).on('blur', function() {
		f_hide_box_user_nav_top(); // Скрываем меню, когда окно теряет фокус
	});

	// Опционально: скрываем элемент при клике вне области
	$(document).on('click', function(event) {
		if (!$(event.target).closest('.collapse_user_nav_top, #box_collapse_user_nav_top').length) {
			jq_box_collapse_user_nav_top.collapse('hide');
		}
	});
	// ==============================
	
	let jq_widget_policy_cookie = $('.widget_policy_cookie');
	
	if( !localStorage['access_widget_policy_cookie'] ){
		jq_widget_policy_cookie.removeClass('d-none');
	}

	jq_widget_policy_cookie.find('.btn').on('click', function(){
		localStorage['access_widget_policy_cookie'] = 1;
		jq_widget_policy_cookie.addClass('d-none');
	})

});

</script>
