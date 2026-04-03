<?php

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE & ~E_DEPRECATED & ~E_WARNING );
// Время по Астане
date_default_timezone_set('Etc/GMT-0');

session_name('ssid');
session_set_cookie_params([
	'lifetime' => 0, // до закрытии браузера
	'path' => '/',
	'domain' => $_SERVER['HTTP_HOST'],
	'secure' => true,
	'httponly' => true
]);
session_start();

$GLOBALS['MARKET365_SECRETS'] = [];
$__m365SecretsFile = __DIR__ . '/secrets.local.php';
if (is_file($__m365SecretsFile)) {
	$__m365Loaded = require $__m365SecretsFile;
	if (is_array($__m365Loaded)) {
		$GLOBALS['MARKET365_SECRETS'] = $__m365Loaded;
	}
}
function market365_env(string $key, string $default = ''): string {
	$v = getenv($key);
	if ($v !== false && $v !== '') {
		return $v;
	}
	$sec = $GLOBALS['MARKET365_SECRETS'][$key] ?? null;
	if (is_string($sec) && $sec !== '') {
		return $sec;
	}
	return $default;
}

if($_SERVER['HTTP_CF_IPCOUNTRY'] != 'KZ' && $_SERVER['HTTP_CF_IPCOUNTRY'] != 'UZ'){
	//exit();
}




/*
if ($_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != 'admin') {
    header('WWW-Authenticate: Basic realm="Please login"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Вам нужно авторизоваться';
    exit();
}


header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
*/

$ARGS = array_merge($_POST, $_GET);

$WEB_JSON = [
	'uri' => $_SERVER['REQUEST_URI'],
	'uri_clean' => '',
	'uri_dir_arr' => [],
	
	'get' => $_GET,
	'post' => $_POST,
	'args' => $ARGS,
	
	'dir_main'			=> __DIR__ . '/',
	'dir_func'			=> __DIR__ . '/func/',
	'dir_public'		=> __DIR__ . '/public/',
	'dir_upload'		=> __DIR__ . '/public/upload/',
	'dir_upload_img'	=> __DIR__ . '/public/upload/img',
	'dir_upload_file'	=> __DIR__ . '/public/upload/file',
	'dir_class'			=> __DIR__ . '/class/',
	'dir_page'			=> __DIR__ . '/page/',
	'dir_page_auth'		=> __DIR__ . '/page/auth/',
	'dir_page_user'		=> __DIR__ . '/page/user/',
	'dir_page_admin'	=> __DIR__ . '/page/admin/',
	'dir_page_tools'	=> __DIR__ . '/page/tools/',
	'dir_page_files'	=> __DIR__ . '/page/file/',
	'dir_page_file'		=> __DIR__ . '/page_file/',
	'dir_api'			=> __DIR__ . '/api/',
	'dir_template'		=> __DIR__ . '/template/',
	
	'upload_json' => [
		'ads' => [ // название $table DB
			'image' => [ // тип
				'mime_regex' => '/^image\/(jpeg|png)$/',
				'file_size_max' => 10 * 1024 * 1024,
				'img_compress_on' => true,
				'img_min_px_size' => 200,
				'img_max_px_size' => 10000,
				'img_compress_px_size' => 1000,
				'img_compress_quality' => 80,
				'img_thumb_compress_px_size' => 400,
				'img_thumb_compress_quality' => 80,
			],
			'test' => [ // тип
				'mime_regex' => '/^.*$/',
				'file_size_max' => 50 * 1024 * 1024,
				'img_compress_on' => false
			],
		],
		'user' => [
			'avatar' => [
				'mime_regex' => '/^image\/(jpeg|png)$/',
				'file_size_max' => 5 * 1024 * 1024,
				'img_compress_on' => true,
				'img_min_px_size' => 50,
				'img_max_px_size' => 8000,
				'img_compress_px_size' => 800,
				'img_compress_quality' => 85,
				'img_thumb_compress_px_size' => 200,
				'img_thumb_compress_quality' => 80,
			],
		],
	],
	
	'did_json' => [
		'_id' => 0,
		'visit_date' => '',
		'ua' => '',
		'ip' => '',
		'lang' => '',
		'lang_full' => '',
		'country' => '',
		'password' => '',
		'password_sha256' => '',
	],
	
	'user_json' => false,
	
	
	'api_json' => [
		'stripe_public' => market365_env('MARKET365_STRIPE_PUBLIC'),
		'stripe_secret' => market365_env('MARKET365_STRIPE_SECRET'),
		
		'google_recaptcha_v3_public' => market365_env('MARKET365_RECAPTCHA_V3_PUBLIC'),
		'google_recaptcha_v3_secret' => market365_env('MARKET365_RECAPTCHA_V3_SECRET'),
		
		'google_recaptcha_v2_public' => market365_env('MARKET365_RECAPTCHA_V2_PUBLIC'),
		'google_recaptcha_v2_secret' => market365_env('MARKET365_RECAPTCHA_V2_SECRET'),
		
		'google_oauth_client_id'	=> market365_env('MARKET365_GOOGLE_OAUTH_CLIENT_ID'),
		'google_oauth_client_secret'=> market365_env('MARKET365_GOOGLE_OAUTH_CLIENT_SECRET'),
		'google_oauth_redirect_url' => 'https://market365.uk.com/login/oauth/google',
	],
	
	'email_json' => [
		'main' => [
			"login" => "noreply@market365.uk.com",
			"pass" => market365_env('MARKET365_SMTP_PASS'),
			
			"port" => "465",
			"host" => "smtp.mail.ru", // в mail.ru создать новую почту, войти в неё, привязать телефон и в безопасности создать новый пароль для внешних сервисов
			"ssl" => true,
			
			"from_email" => 'noreply@market365.uk.com',
			"from_name" => 'noreply@market365.uk.com'
		],
	],
	
	'file_on' => false,
	
	'page_library' => [
		'masonry_layout' => '<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>',
		'swiper' => [
			'css' => '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>',
			'js' => '<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>',
			//'js' => '<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>',
		],
		'simplebar' => [
			'css' => '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplebar/6.2.7/simplebar.min.css" integrity="sha512-rptDreZF629VL73El0GaBEH9tlYEKDJFUr+ysb+9whgSGbwYfGGA61dVtQFL0qC8/SZv/EQFW5JtwEFf+8zKYg==" crossorigin="anonymous" referrerpolicy="no-referrer" />',
			'js' => '<script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/6.2.7/simplebar.min.js" integrity="sha512-NkLfoy5pkmAKUxk/OXl9vi1PDSFnEEJ3bDFdX0ln1L0pto6jM7On0lLsjhNC62i2ifyXhPWjPpcGEXawBfErtQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>',
		]
	],
	
	'page_ads' => [
		'side' => false
	],
	
	'page_link' => [
		'login'					=>  '/login',
		'login_registration'	=>  '/login#registration',
		'login_forgout'			=>  '/login/forgout-password',
		'login_activation'		=>  '/login/activation',
		'login_google'			=>  '/login/oauth/google',
		'login_apple'			=>  '/login/oauth/apple',
		'login_facebook'		=>  '/login/oauth/facebook',
		
		'email_support'			=>  'support@market365.uk.com',
		
		'user'					=>  '/user',
		'user_exit'				=>  '/user/exit/'.session_id(),
		'user_ads'				=>  '/user/ads',
		'user_messages'			=>  '/user/messages',
		'user_favorites'		=>  '/user/favorites',
		'user_pays'				=>  '/user/pays',
		'user_pays_add'			=>  '/user/pays/add',
		'user_notifications'	=>  '/user/notifications',
		'user_settings'			=>  '/user/settings',
		
		'admin_users_list'			=>  '/admin/users',
		'admin_pays_list'			=>  '/admin/pays',
		'admin_ads_list'			=>  '/admin/ads',
		'admin_pages_list'			=>  '/admin/pages',
		'admin_messages_list'		=>  '/admin/messages',
		'admin_favorites_list'		=>  '/admin/favorites',
		'admin_notifications_list'	=>  '/admin/notifications',
		
		'ads_create'			=>  '/ads/create',
		'ads_list'				=>  '/ads/list',
		'ads_category'			=>  '/ads/category',
		'ads_item'				=>  '/ads/item',
		'ads_promote'			=>  '/ads/promote',
		
		'page_info'				=>  '/info',
		'page_about_us'			=>  '/info/about-us',
		'page_rules'			=>  '/info/terms-of-service',
		'page_privacy'			=>  '/info/privacy-policy',
		'page_faq'				=>  '/info/faq',
		//'page_sitemap'			=>  '/info/sitemap',
		'page_sitemap'			=>  '/sitemap',
		'page_payment'			=>  '/info/payment',
		'page_recomendations'	=>  '/info/recomendations',
		'page_safety_tips'		=>  '/info/safety-tips',
		
		'img_logo'					=>  '/public/logo.png',
		'img_logo_google'			=>  '/public/img/logo_google.svg?r=3',
		'img_logo_google_pay'		=>  '/public/img/logo_google_pay.svg?r=3',
		'img_logo_stripe'			=>  '/public/img/logo_stripe.svg?r=3',
		'img_logo_mastercard'		=>  '/public/img/logo_mastercard.svg?r=3',
		'img_logo_visa'				=>  '/public/img/logo_visa.svg?r=3',
		'img_logo_american_express'	=>  '/public/img/logo_american_express.svg?r=3',
		'img_logo_facebook'			=>  '/public/img/logo_facebook.svg?r=3',
		'img_logo_apple'			=>  '/public/img/logo_apple.svg?r=3',
		'img_logo_apple_pay'		=>  '/public/img/logo_apple_pay.svg?r=3',
	],
	
	'page_json' => [
		
		'theme' => 'default', // ''/'clean'
		
		'lang' => 'en',
		'lang_iso' => 'en-EN',
		
		'url' => '',
		
		'theme_color' => '#fff', // '#f5e6db',
		'background_color' => '#fff',
		
		'site_host' => $_SERVER['HTTP_HOST'],
		'site_name' => 'Market365',
		'site_name_short' => 'Market365',
		//'site_currency' => '£ (GBP)',
		'site_currency' => '£',
		
		'title' => '',
		'title_important' => '',
		'title_glue' => ' – ',
		
		'description' => '',
		'keywords' => '',
		'keywords_news' => '', // w1,слово,слово
		'arr_tag' => [], // ['w1', 'слово', 'слово']
		'date' => '',
		'date_update' => '',
		
		'html_head' => '',
		'html_top' => '',
		'html_bottom' => '',
	],
];

$WEB_JSON['uri_clean'] = explode('#',
			explode('?', 
				substr( $WEB_JSON['uri'], 1) 
			)[0]
		)[0]; // "/uri?query=value#hash" => "uri"
$WEB_JSON['uri_dir_arr'] = explode('/', $WEB_JSON['uri_clean']);

require($WEB_JSON['dir_func'] . 'f_db.php');
require($WEB_JSON['dir_func'] . 'f_db_get.php');
require($WEB_JSON['dir_func'] . 'f_default.php');
require($WEB_JSON['dir_func'] . 'f_email_send.php');

if ($WEB_JSON['uri_clean'] === 'api/dev/db_init') {
	require($WEB_JSON['dir_func'] . 'f_db_init.php');
	header('Content-Type: text/plain; charset=utf-8');
	echo f_db_init();
	exit;
}


$arr_json_route = [
	// path, file
	['regex' => '',											'file_path' => $WEB_JSON['dir_page'] . 'landing.php'],
	
	['regex' => 'login',									'file_path' => $WEB_JSON['dir_page_auth'] . 'login.php',					'user_check'=>false],
	['regex' => 'login\/activation',						'file_path' => $WEB_JSON['dir_page_auth'] . 'login_activation.php',		'user_check'=>false],
	['regex' => 'login\/forgout\-password',					'file_path' => $WEB_JSON['dir_page_auth'] . 'login_forgout.php'],
	
	['regex' => 'login\/oauth\/.+',							'file_path' => $WEB_JSON['dir_page_auth'] . 'login_oauth.php',			'user_check'=>false],
	
	
	
	['regex' => 'user',										'file_path' => $WEB_JSON['dir_page_user'] . 'user_item.php',				'user_check'=>true],
	['regex' => 'user\/ads',								'file_path' => $WEB_JSON['dir_page_user'] . 'user_ads.php',				'user_check'=>true],
	['regex' => 'user\/pays',								'file_path' => $WEB_JSON['dir_page_user'] . 'user_pays.php',				'user_check'=>true],
	['regex' => 'user\/pays\/add',							'file_path' => $WEB_JSON['dir_page_user'] . 'user_pays_add.php',			'user_check'=>true],
	['regex' => 'user\/favorites',							'file_path' => $WEB_JSON['dir_page_user'] . 'user_favorites.php',		'user_check'=>true],
	['regex' => 'user\/messages',							'file_path' => $WEB_JSON['dir_page_user'] . 'user_messages.php',			'user_check'=>true],
	['regex' => 'user\/notifications',						'file_path' => $WEB_JSON['dir_page_user'] . 'user_notifications.php',	'user_check'=>true],
	['regex' => 'user\/settings',							'file_path' => $WEB_JSON['dir_page_user'] . 'user_settings.php',			'user_check'=>true],
	['regex' => 'user\/exit(\/[a-zA-Z0-9]*)?',				'file_path' => $WEB_JSON['dir_page_user'] . 'user_exit.php',				'user_check'=>true],
	
	['regex' => 'user\/set-auth',							'file_path' => $WEB_JSON['dir_page_user'] . 'user_set_auth.php'],
	
	['regex' => 'info',										'file_path' => $WEB_JSON['dir_page'] . 'info_list.php'],
	['regex' => 'info\/.*?',								'file_path' => $WEB_JSON['dir_page'] . 'info_item.php'],
	
	//['regex' => 'search',									'file_path' => $WEB_JSON['dir_page'] . 'ads_search.php'],
	//['regex' => '\@[a-zA-Z0-9\.\-\_]+',						'file_path' => $WEB_JSON['dir_page'] . 'ads_user.php'],
	//['regex' => '\@[a-zA-Z0-9\.\-\_]+\/ads\/[a-zA-Z0-9]*',	'file_path' => $WEB_JSON['dir_page'] . 'ads_user_item.php'],
	
	['regex' => 'ads\/list(\/.*)?',							'file_path' => $WEB_JSON['dir_page'] . 'ads_list.php',				'ads_side'=>true],
	['regex' => '(sitemap|ads|(ads\/category(\/.*)?))',		'file_path' => $WEB_JSON['dir_page'] . 'ads_category.php'],
	//['regex' => 'sitemap',								'file_path' => $WEB_JSON['dir_page'] . 'sitemap.php'],
	
	['regex' => 'ads\/create',								'file_path' => $WEB_JSON['dir_page'] . 'ads_create.php',			'user_check'=>true],
	['regex' => 'ads\/promote\/.*?',						'file_path' => $WEB_JSON['dir_page'] . 'ads_promote.php',			'user_check'=>true],
	['regex' => 'ads\/.*?',									'file_path' => $WEB_JSON['dir_page'] . 'ads_item.php',				'ads_side'=>true],
	
	['regex' => 'manifest.json',							'file_on' => true,			'file_path' => $WEB_JSON['dir_page_file'] . 'manifest.json.php'],
	['regex' => 'robots.txt',								'file_on' => true,			'file_path' => $WEB_JSON['dir_page_file'] . 'robots.txt.php'],
	['regex' => 'sw.js',									'file_on' => true,			'file_path' => $WEB_JSON['dir_page_file'] . 'sw.js.php'],
	
	['regex' => 'cookie-set',								'file_path' => $WEB_JSON['dir_page_tools'] . 'cookie_set.php'],
	
	['regex' => 'redirect',									'file_path' => $WEB_JSON['dir_page_tools'] . 'redirect.php'],
	
	['regex' => 'translate',								'file_path' => $WEB_JSON['dir_page_admin'] . 'translate_list.php'],
	
	/*
	['regex' => 'file(\/[a-zA-Z0-9]*)?',					'file_path' => $WEB_JSON['dir_page_files'] . 'file.php'],
	['regex' => 'translate',								'file_path' => $WEB_JSON['dir_page_admin'] . 'admin_translate_list.php'],
	['regex' => 'company',									'file_path' => $WEB_JSON['dir_page_admin'] . 'admin_company_list.php'],
	['regex' => 'pay',										'file_path' => $WEB_JSON['dir_page_admin'] . 'admin_pay_list.php'],
	['regex' => 'client',									'file_path' => $WEB_JSON['dir_page_admin'] . 'admin_client_list.php'],
	
	['regex' => 'user',										'file_path' => $WEB_JSON['dir_page_admin'] . 'user_list.php'],
	['regex' => 'user\/([a-zA-Z0-9]*)?',					'file_path' => $WEB_JSON['dir_page_user'] . 'user_item.php'],
	
	['regex' => 'main',										'file_path' => $WEB_JSON['dir_page'] . 'ads_list.php'],
	['regex' => 'ads',										'file_path' => $WEB_JSON['dir_page'] . 'ads_list.php'],
	['regex' => 'ads\/([a-zA-Z0-9]*)?',						'file_path' => $WEB_JSON['dir_page'] . 'ads_item.php'],
	*/
	
];


$WEB_PAGE_HTML = '';

$file_path = '';
$file_on = false;
foreach($arr_json_route as& $json_route){
	
	$regex = '/^'. $json_route['regex'] . '$/';
	
	if(preg_match($regex, $WEB_JSON['uri_clean'], $matches) > 0){
		
		
		if( isset($json_route['user_check']) && $json_route['user_check'] !== f_user_check() ){
			f_redirect('/');
		}
		
		// Реклама - по боковам - по умолчанию включить
		$GLOBALS['WEB_JSON']['page_ads']['side'] = $json_route['ads_side'] === true ? true : false;
		
		$file_path = $json_route['file_path'];
		break;
	}
	
	$file_on = $json_route['file_on'] == true;
	
}



// Подключаем API если его заправшивают "https://site.com/api/..."
if( $WEB_JSON['uri_dir_arr'][0] == 'api' ){
	require($WEB_JSON['dir_main'] . 'api.php');
}



if( $file_on == false ){
	// Страница не найдена
	if($file_path == ''){
		header('HTTP/1.1 404 Not Found');
		$file_path = $WEB_JSON['dir_page_tools'] . '404.php';	
	}
	
	header("Content-Type: text/html");
	ob_start();
	require($file_path);
	$WEB_PAGE_HTML = ob_get_clean();
	f_template('page');

}else{
	require($file_path);
}



?>