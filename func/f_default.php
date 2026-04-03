<?php
	
	
function f_echo($text=''){
	echo( $text );
}
function f_echo_html($text=''){
	echo( htmlentities($text) );
}
function f_translate_echo($text=''){
	f_echo_html( f_translate($text) );
}
function f_html($text=''){
	return htmlentities($text);
}

function f_number_parse($number) {
    $str = preg_replace('/[^0-9]/', '', $number . '');
    return $str;
}
function f_number_beauty($number) {
    $str = number_format($number, 0, '.', ' ');
    return $str;
}

function f_number_if_min_max($min, $number, $max){
	$number = $min >= $number ? $min : ($number >= $max ? $max : $number);
	return $number;
}


function f_html_head_title(){
	$title = $GLOBALS['WEB_JSON']['page_json']['title'];
	$title_important = $GLOBALS['WEB_JSON']['page_json']['title_important'];
	$title_glue = $GLOBALS['WEB_JSON']['page_json']['title_glue'];
	$site_name = $GLOBALS['WEB_JSON']['page_json']['site_name'];
	
	if( $title_important == '' ){
		$title .= ( $title == '' ? $site_name : ( $title_glue . $site_name ) );
	}else{
		$title = $title_important;
	}
	
	return $title;
}


function f_page_currency(){
	return $GLOBALS['WEB_JSON']['page_json']['site_currency'];
}

function f_page_breadcump($arr_link=[], $domain_link=""){
	$arr_domain_link = [];
	$arr_a_html = [];
	$i = 0;
	array_unshift($arr_link, ['title'=>f_translate('Main'), 'domain'=>'']);
	foreach($arr_link as $json_link){
		if(!$json_link){
			continue;
		}
		$arr_domain_link[] = $json_link['domain'];
		$a_href = str_replace("//", "/", ($i == 0 ? "/" : $domain_link) . implode('/', $arr_domain_link));
		$arr_a_html[] = "<li><a itemprop='item' href='" . $a_href ."'><span itemprop='name'>". $json_link['title'] ."</span></a><meta itemprop='position' content='". ($i+1) ."'></li>";
		$i += 1;
	}
	f_echo( '<div class="breadcump_page" itemscope="" itemtype="https://schema.org/BreadcrumbList"><ul>' . implode('', $arr_a_html) . '</ul></div>' );
}

function f_page_title_set($title='', $important_on=false){
	if( $important_on == true ){
		$GLOBALS['WEB_JSON']['page_json']['title_important'] = $title;
	}else{
		$GLOBALS['WEB_JSON']['page_json']['title'] = $title;
	}
}

function f_page_library_add($library_name=""){
	$lib_json = $GLOBALS['WEB_JSON']['page_library'][$library_name];
	if($lib_json){
		if( is_array($lib_json) ){
			$GLOBALS['WEB_JSON']['page_json']['html_head'] .= $lib_json['css'];
			$GLOBALS['WEB_JSON']['page_json']['html_bottom'] .= $lib_json['js'];
		}else{
			$GLOBALS['WEB_JSON']['page_json']['html_head'] .= $lib_json; // lib_json that string
		}
	}
}

function f_html_add($key_name=""){
	
	$json_html = [
		'no_index' => [
			'where' => 'head',
			'html' => '
				<meta name="robots" content="noindex, nofollow" />
			'
		],
		'google_recaptcha_v2' => [
			'where' => 'head',
			'html' => '
				<!-- RECAPTCHA V2 -->
				<script src="https://www.google.com/recaptcha/api.js" async defer></script>
			'
		],
	];
	
	if( $json_html[$key_name] ){
		$html = $json_html[$key_name]['html'];
		
		if( $json_html[$key_name]['where'] == 'head' ){
			$GLOBALS['WEB_JSON']['page_json']['html_head'] .= $html;
			
		}else if( $json_html[$key_name]['where'] == 'top' ){
			$GLOBALS['WEB_JSON']['page_json']['html_top'] .= $html;
			
		}else if( $json_html[$key_name]['where'] == 'bottom' ){
			$GLOBALS['WEB_JSON']['page_json']['html_bottom'] .= $html;
		}
	}
	
}

function f_page_link($page_name=""){
	
	$link = '/';
	
	if( $GLOBALS['WEB_JSON']['page_link'][$page_name] ){
		$link = $GLOBALS['WEB_JSON']['page_link'][$page_name];
	}
	
	return $link;
}
function f_page_link_echo($page_name=""){
	echo( f_page_link($page_name) );
}


function f_datetime_current($format='Y-m-d H:i:s'){
	return date( $format );
}

function f_datetime_beauty($date=""){
	if($date == ""){
		return "";
	}
	return date('d.m.Y H:i:s', strtotime($date));
}

function f_date_beauty($date=""){
	if($date == ""){
		return "";
	}
	return date('d.m.Y', strtotime($date));
}


function f_phone_beauty($number="") {
    // Удаляем все, кроме цифр
    $number = preg_replace('/\D/', '', (string)$number);
	
	if($number == ''){
		return $number;
	}
	
	//return '+' . substr($number, 0, -10) . ' (' .   substr($number, -10, 3) . ') ' .  substr($number, -7, 3) . '-' . substr($number, -4, 2) . '-' . substr($number, -2);
	return '+' . substr($number, 0, -10) . ' (' .   substr($number, -10, 3) . ') ' .  substr($number, -7, 3) . ' ' . substr($number, -4, 2) . ' ' . substr($number, -2);
}


// POST - Перезагрузка страницы - что бы сбросить постоянную отправку POST
function f_post_end($url=false){
	$url = $url === false ? $_SERVER['REQUEST_URI'] : $url;
	header('Location: '.$url, true, 303);
	exit();
}
// GET - Перезагрузка страницы - что бы сбросить постоянную отправку GET
function f_get_end($url=false){
	$url = $url === false ? $_SERVER['REQUEST_URI'] : $url;
	$url = explode('?', $url)[0];
	header('Location: '.$url, true, 303);
	exit();
}

function f_auth_http($name_true, $pass_true=""){
	if ($_SERVER['PHP_AUTH_USER'] != $name_true || $_SERVER['PHP_AUTH_PW'] != $pass_true) {
		header('WWW-Authenticate: Basic realm="Please login"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Вам нужно авторизоваться';
		exit();
	}
}

function f_valid_date($str_datetime, $format_datetime='Y-m-d H:i:s') {
    // Попытка парсинга даты и времени
    $parse_datetime = date_create_from_format($format_datetime, $str_datetime);

    if ($parse_datetime !== false) {
        // Возвращаем отформатированное время
        return $parse_datetime->format($format_datetime);
    } else {
        // Возвращаем false в случае неудачи
        return false;
    }
}



function f_valid_type_id($value=""){
	
	if(gettype($value) == 'array'){
		$value = implode(',', $value);
	}else{
		$value = implode(',', array_filter(explode(',', $value), 'is_numeric'));
	}
	
	return $value ?: NULL;
}

function f_check_diap_number($name="", $min=null, $max=null){
	$error = "";
	if($min == null || $max == null){
		
	}else if($min >= $max){
		$error = 'The range is specified incorrectly "' . $name . '"';
	}
	return $error;
}
function f_check_diap_time($name="", $min=null, $max=null){
	
	$min = $min == null ? null : strtotime($min);
	$max = $max == null ? null : strtotime($max);
	
	return f_check_diap_number($name, $min, $max);
}



function f_gps_validate($gps_string, $set_default=true) {
    $arr_gps = explode(',', $gps_string ?: '');
    
	$result = NULL;
	
	if( $set_default ){
		$result = ['lat' => 0, 'lng' => 0];
	}

    if (count($arr_gps) == 2) {
        $lat = floatval(trim($arr_gps[0]));
        $lng = floatval(trim($arr_gps[1]));

        if ($lat != 0 && $lng != 0 && $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
			$result= ['lat' => $lat, 'lng' => $lng];
        }
    }

    return $result;
}

function f_gps_distanse($coord_1, $coord_2) {
    // Разделяем координаты
    list($lat_1, $lon_1) = explode(',', $coord_1);
    list($lat_2, $lon_2) = explode(',', $coord_2);

    // Переводим градусы в радианы
    $lat_1 = deg2rad($lat_1);
    $lon_1 = deg2rad($lon_1);
    $lat_2 = deg2rad($lat_2);
    $lon_2 = deg2rad($lon_2);

    // Разница координат
    $d_lat = $lat_2 - $lat_1;
    $d_lon = $lon_2 - $lon_1;

    // Формула гаверсинуса
    $a = sin($d_lat / 2) * sin($d_lat / 2) +
         cos($lat_1) * cos($lat_2) *
         sin($d_lon / 2) * sin($d_lon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Радиус Земли в километрах
    $earthRadius = 6371;

    // Расстояние
    $distance = $earthRadius * $c;

    return $distance;
}



$lang = mb_strtolower(mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
$lang = in_array($lang, ['ru', 'kz', 'en']) ? $lang : 'en';
$GLOBALS['WEB_JSON']['page_json']['lang'] = $lang;

// POST - Выбран язык
if (isset($_POST['change_lang'])){
	
	$lang = $_POST['change_lang'];
	$lang = in_array($lang, ['ru', 'kz', 'en']) ? $lang : 'en';
	
	f_cookie_set("lang", $lang);
	$GLOBALS['WEB_JSON']['page_json']['lang'] = $lang;
	
	f_post_end();
}
// GET - Выбран язык
if (isset($_GET['change_lang'])){
	
	$lang = $_GET['change_lang'];
	$lang = in_array($lang, ['ru', 'kz', 'en']) ? $lang : 'en';
	
	f_cookie_set("lang", $lang);
	$GLOBALS['WEB_JSON']['page_json']['lang'] = $lang;
	//f_redirect( $_SERVER['REQUEST_URI'] );
}

if( f_cookie_get('lang') != '' ){
	$GLOBALS['WEB_JSON']['page_json']['lang'] = f_cookie_get('lang');
}else{
	f_cookie_set('lang', $GLOBALS['WEB_JSON']['page_json']['lang']);
}



function f_template($file_name=""){
	require( $GLOBALS['WEB_JSON']['dir_template'] . $file_name . '.php' );
}



// Перевод текста DB
/*
CREATE TABLE `admin_default`.`translate` (`_id` INT NOT NULL AUTO_INCREMENT , `_create_date` DATETIME NULL DEFAULT NULL , `ru` TEXT NULL DEFAULT NULL , `ru_crc32` BIGINT NULL DEFAULT NULL ,  `en` TEXT NULL DEFAULT NULL , `kz` TEXT NULL DEFAULT NULL , PRIMARY KEY (`_id`), UNIQUE (`ru_crc32`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
*/
$GLOBALS['translate_cache_json'] = []; // Кэш - для оптимизации сюда попадают все переведенные текста [crc32=>'Text lang need']
function f_translate($text_ru="", $lang_need=false){
	
	$lang_need = $lang_need === false ? $GLOBALS['WEB_JSON']['page_json']['lang'] : $lang_need;
	
	// Оптимизация если требуемый язык RU - то не переводить русский текст
	if($lang_need == 'ru'){
		return $text_ru;
	}
	
	$text_ru = trim( $text_ru );
	
	if( $text_ru == '' ){
		return '';
	}
	
	$text_ru_crc32 = crc32( $text_ru );
	
	// Оптимизация если искомый текст есть в кэше запроса, то достать от туда
	if( isset( $GLOBALS['translate_cache_json'][$text_ru_crc32] ) ){
		return $GLOBALS['translate_cache_json'][$text_ru_crc32]; 
	}
	
	// Поиск в БД
	$result_json = f_db_select_get('translate', ['ru_crc32'=>$text_ru_crc32])[0];
	
	// Не найдено - Нужно создать новый
	if( !isset($result_json) ){
		$GLOBALS['translate_cache_json'][$text_ru_crc32] = $text_ru;
		// Добавить в БД
		f_db_insert(
			"translate", 
			[
				"_create_date" => date('Y-m-d H:i:s'),
				"ru" => $text_ru,
				"ru_crc32" => $text_ru_crc32,
			]
		);
		return $text_ru;
	}
	
	if( isset($result_json[$lang_need]) ){
		$GLOBALS['translate_cache_json'][$text_ru_crc32] = $result_json[$lang_need];
		return $result_json[$lang_need];
	}
	
	return $text_ru;
}






function f_did_request(){
	f_db_insert(
		"request", 
		[
			"_create_date" => $GLOBALS['WEB_JSON']['did_json']['_update_date'],
			"uri" => $_SERVER['REQUEST_URI'],
			"did_id" => $GLOBALS['WEB_JSON']['did_json']['_id'],
			"user_id" => $GLOBALS['WEB_JSON']['user_json']['_id'] ?: null,
			
			//"ip" => $_SERVER['REMOTE_ADDR'] ?: null,
			//"country" => $_SERVER['HTTP_CF_IPCOUNTRY'] ?: null,
			//"city" => $_SERVER['HTTP_CF_IPCITY'] ?: null,
			//"ua" => $_SERVER['HTTP_USER_AGENT'] ?: null,
			
			//"get_json" => ["GET" => $_GET, "POST" => $_POST, "COOKIE" => $_COOKIE, "SERVER" => $_SERVER],
			"get_json" => $_GET ?: null,
			"post_json" => $_POST ?: null,
			//"user_id" => "___"
		]
	);
}

function f_did_generate(){
	
	$cur_date = date('Y-m-d H:i:s');
	
	$device_json = [
		'ip' => $_SERVER['REMOTE_ADDR'] ?: null,
		'country' => mb_strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) ?: null,
		'city' => mb_strtolower($_SERVER['HTTP_CF_IPCITY']) ?: null,
		'lang' => mb_strtolower(mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) ?: null,
		'lang_orig' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?: null,
		'ua' => $_SERVER['HTTP_USER_AGENT'] ?: null,
		'visit_date' => $cur_date,
		'_update_date' => $cur_date,
		'_create_date' => $cur_date,
	];
	
	return $device_json;
}

function f_did_create(){
	// 1) Собираем информацию о девайсе
	$device_json = f_did_generate();
	
	$device_json['password'] = f_gen_password(10);
	$device_json['password_hash_sha256'] = hash('sha256', $device_json['password']);
	
	/*
	$device_json['test_json'] = [
		'get' => $_GET,
		'post' => $_POST,
		'server' => $_SERVER
	];
	*/
	$device_json['test_json'] = null;
	
	// 2) Создаём запись в БД и получаем ID
	$_id = f_db_insert("did", $device_json, ["ip", "country", "city", "lang", "lang_orig", "ua", "password", "password_hash_sha256", "visit_date", "_update_date", "_create_date", "test_json"]);
	
	// 3) Конвертируем число в СТРОКУ
	$_id_str = f_num_encode($_id);
	
	// 4) Записываем в куки "did" в формате "_id-password_hash_sha256"
	//f_cookie_set("did", $_id_str.'-'.$device_json['password_hash_sha256'], strtotime("+365 days"));
	
	$item_json = f_db_select_get('did', ['_id'=>$_id])[0];
	
	return $item_json;
}


// Учёт девайсов в БД
// ДАННЫЕ В БД - НЕ ОБНОВЛЯЮТСЯ - ДЛЯ ОПТИМИЗАЦИИ
function f_did_auto(){
	// БД - did - _id, _create_date, _update_date, visit_date, ip, country, lang, ua, password, password_hash_sha256
	
	// Не создавать дубли запросов
	if( f_user_request_ignored() ){
		return false;
	}
	
	$did = $_COOKIE["did"];
	$did_parse = explode('-', $did);
	
	$num_encode_id = $did_parse[0];
	$pass_sha256 = $did_parse[1];
	
	$item_json = [];
	
	$now_did = f_did_generate();
	
	if( $num_encode_id && $pass_sha256 ){
		$_id = f_num_decode($num_encode_id);
		$item_json = f_db_select_get('did', ['_id'=>$_id])[0];
		//f_test('1111');
		
		// Не найдено - Нужно создать новый
		if( !isset($item_json) ){
			//f_test('3333');
			$item_json = f_did_create();
		
		// HASH pass - не совпадают
		}else if($pass_sha256 != $item_json['password_hash_sha256']){
			//f_test('4444');
			$item_json = f_did_create();
		}else{
		
			f_db_update_smart("did", ["_id" => $_id], [
				'ip' => $now_did['ip'],
				'country' => $now_did['country'],
				'city' => $now_did['city'],
				'lang' => $now_did['lang'],
				'lang_orig' => $now_did['lang_orig'],
				'ua' => $now_did['ua'],
				'visit_date' => $now_did['visit_date'],
				'_update_date' => $now_did['_update_date'],
			]);
		}
	}else{
		//f_test('2222');
		$item_json = f_did_create();
		
	}
	
	f_cookie_set("did", f_num_encode($item_json['_id']).'-'.$item_json['password_hash_sha256'], strtotime("+365 days"));
	
	$GLOBALS['WEB_JSON']['did_json'] = array_merge($item_json, $now_did);
	
	f_user_auto();
	
	// Посещение страниц
	f_did_request();
	
	return;
}

f_did_auto();




function f_google_recaptcha($g_response=""){
	
	if( $g_response == '' ){
		return [
			'ok' => false,
			'success' => 0,
			'score' => 0,
			'action' => null,
		];
	}
	
	$result = json_decode(file_get_contents(
		"https://www.google.com/recaptcha/api/siteverify?secret=".$GLOBALS['WEB_JSON']['api_json']['google_recaptcha_v3_secret']
			."&response=".$g_response
			."&remoteip=".$_SERVER["REMOTE_ADDR"]
	), TRUE);
	
	
	
	return [
		'ok' => $result['success'] == 1 && $result['score'] >= 0.5,
		'success' => $result['success'],
		'score' => $result['score'],
		'action' => $result['action'],
	];
}

function f_google_recaptcha_v2($g_response=""){
	
	if( $g_response == '' ){
		return [
			'ok' => false,
			'success' => 0,
			'score' => 0,
			'action' => null,
		];
	}
	
	$result = json_decode(file_get_contents(
		"https://www.google.com/recaptcha/api/siteverify?secret=".$GLOBALS['WEB_JSON']['api_json']['google_recaptcha_v2_secret']."&response=".$g_response
	), TRUE);
	
	return [
		'ok' => $result['success'] == 1,
		'success' => $result['success'],
		'challenge_ts' => $result['challenge_ts'],
		'hostname' => $result['hostname'],
	];
}


function f_api_response_exit($data_json){
	header('Content-Type: application/json; charset=utf-8');
	echo( json_encode($data_json, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) );	
	exit();
}




function f_email_validate($email="") {
    // Проверка формата и допустимости символов
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Проверка наличия MX записей
    $domain = explode('@', $email)[1];
    if (!checkdnsrr($domain, 'MX')) {
        return false;
    }

    // Email валиден
    return true;
}

function f_date_validate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    // Проверка на соответствие формату и на то, что дата действительна
    return $d && $d->format($format) === $date;
}


function f_iin_parse_birth($iin="") {
    // Проверяем, что ИИН содержит 12 цифр
    if (preg_match('/\d{12}/', $iin)) {
        // Извлекаем год, месяц и день из ИИН
        $year = intval(substr($iin, 0, 2));
        $month = intval(substr($iin, 2, 2));
        $day = intval(substr($iin, 4, 2));

        // Определяем столетие по первой цифре ИИН
        $century = ($iin[6] > '3') ? 2000 : 1900;

        // Добавляем столетие к году
        $year += $century;

        // Формируем строку даты в формате YYYY-MM-DD
        $birth_date = sprintf('%04d-%02d-%02d', $year, $month, $day);
		
		if( !f_date_validate($birth_date) ){
			return false;
		}

        return $birth_date;
    } else {
        return false; // Возвращаем false, если ИИН невалиден
    }
}

function f_iin_parse_gender($iin="") {
    // Проверяем, что ИИН содержит 12 цифр
    if (preg_match('/\d{12}/', $iin)) {
        // Седьмая цифра ИИН указывает на век рождения и пол
        // Четные числа (2, 4, 6, 8) соответствуют женскому полу, нечетные (1, 3, 5, 7) - мужскому
        $genderCode = intval($iin[6]);

        // Определяем пол
        //$gender = ($genderCode % 2 === 0) ? 'женский' : 'мужской';
        $gender = ($genderCode % 2 === 0) ? 2 : 1;

        return $gender;
    } else {
        return false; // Возвращаем false, если ИИН невалиден
    }
}











function f_cookie_delete($name, $domain=false){
	$domain = $domain === false ? ('.' . $_SERVER["HTTP_HOST"]) : $domain;
	setcookie($name, '', -1, '/', $domain, 1);
}

function f_cookie_get($name){
	return $_COOKIE[$name];
}
function f_cookie_set($name, $value, $expired_time=false, $domain=false){
	$expired_time = $expired_time === false ? strtotime("+30 days") : $expired_time;
	//$domain = $domain === false ? ('.' . $_SERVER["HTTP_HOST"]) : $domain;
	//setcookie($name, $value, $expired_time, '/', $domain, 1, 1);
	$domain = $domain === false ? $_SERVER["HTTP_HOST"] : $domain;
	setcookie($name, $value, [
		"expires" => $expired_time, // Срок действия
		"path" => "/", // Доступно для всего сайта
		"domain" => $domain, // Доступно для домена и всех его поддоменов
		"secure" => true, // Только через HTTPS
		"httponly" => true, // Недоступно для JavaScript
		"samesite" => "Lax" // Ограничение кросс-доменных запросов
	]);
}





function f_file_gen_link($file_path=""){
	$file_path = base64_encode( $file_path );
	$file_path = str_replace('=', '', $file_path );
	$file_path = f_gen_password(1) . $file_path;
	
	$uid_id_str = f_num_encode($GLOBALS['WEB_JSON']['user_json']['_id']);
	
	$ssid = session_id();
	
	return '/file/'. $uid_id_str . '/' . $file_path . '/' . $ssid;
}



function f_gen_password($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}



function f_is_json($variable) {
    if (is_array($variable)) {
        $keys = array_keys($variable);
        return $keys === array_keys($keys) ? false : true;
    }
    return false;
}


// Поиск разницы между json, приоритет на json_1
function f_json_diff($json_1, $json_2, $only_key_arr=false, $without_key_arr=false) {
	$return_json = [
    	'status' => false,
      	'key_arr' => [],
      	'data_json' => [],
    ];
    $diff_arr = [];

    foreach ($json_1 as $key => $value) {
		if($only_key_arr !== false){
			if(!in_array($key, $only_key_arr)){
				continue;
			}
		}
		if($without_key_arr !== false){
			if(in_array($key, $without_key_arr)){
				continue;
			}
		}
		if ($json_1[$key] != $json_2[$key]) {
			$return_json['status'] = true;
			$return_json['key_arr'][] = $key;
			$return_json['data_json'][$key] = $value;
		}
	}

    return $return_json;
}


function f_redirect($url, $permanent = false){
	header('Location: ' . $url, true, $permanent ? 301 : 302);
	exit();
}



function f_user_get(){
	return $GLOBALS['WEB_JSON']['user_json'];
}


function f_test($data){
	echo('<pre>');
	var_dump($data);
	exit();
}



// Функция для форматирования размера файла
function f_byte_format($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}


function f_user_request_ignored(){
	if( $_GET['bot'] == 'cron' || in_array($_SERVER["REQUEST_URI"], ["/manifest.json", "/robots.txt", "/sw.js"]) ){
		return true;
	}
	return false;
}

// Авторизация - Автоматическая
function f_user_auto(){
	// БД - user - _id, _create_date, _update_date, visit_date, ip, country, lang, ua, password, password_hash_sha256
	
	// Не создавать дубли запросов
	if( f_user_request_ignored() ){
		return false;
	}
	
	
	$uid = $_COOKIE["uid"];
	$uid_parse = explode('-', $uid);
	
	$_id_str = $uid_parse[0];
	$pass_sha256 = $uid_parse[1];
	
	if($_id_str == '' || $pass_sha256 == ''){
		f_cookie_delete('uid');
		return false;
	}
	
	$_id = f_num_decode($_id_str);
	
	$data_json = f_db_get_user(['_id'=>$_id]);
	
	//var_dump($data_json);
	
	// Не найдено
	if( $data_json == false ){
		f_cookie_delete('uid');
		return false;
	}
	
	// Аккаунт не активирован
	if( $data_json['activation_on'] == 0 ){
		f_cookie_delete('uid');
		return false;
	}
	
	// Hash Паролей не сходится
	if( $pass_sha256 !== $data_json['password_hash_sha256'] && $pass_sha256 !== hash('sha256', $data_json['google_id']) ){
		//exit( 'Hash Паролей не сходится' );
		f_cookie_delete('uid');
		return false;
	}
	
	/*
	// DID не сходится (нужно для деавторизации, если уже была авторизаци на другом устройстве)
	if( $GLOBALS['WEB_JSON']['did_json']['_id'] !== $data_json['did_id'] ){
		f_cookie_delete('uid');
		return false;
	}
	*/
	
	f_cookie_set("uid", $uid, strtotime("+365 days"));
	
	$eff_type = isset($data_json['user_type']) && trim((string)$data_json['user_type']) !== ''
		? trim($data_json['user_type'])
		: (string)($data_json['type'] ?? 'user');
	$data_json['type'] = $eff_type;

	$GLOBALS['WEB_JSON']['user_json'] = $data_json;
	
	/*
	$tmp_substription_all_arr = f_db_get_subscription_user_id( $data_json['_id'] );
	$tmp_substription_arr = [];
	$tmp_substription_category_arr = [];
	foreach($tmp_substription_all_arr  as $item_subscription){
		
		if( $item_subscription['activation_expired_date'] == NULL ){
			continue;
		}
		
		if( strtotime( $item_subscription['activation_expired_date'] ) < strtotime('now') ){
			continue;
		}
		
		$tmp_substription_arr[] = $item_subscription; 
		
		if( !in_array( $item_subscription['category_question'], $tmp_substription_category_arr ) ){
			$tmp_substription_category_arr[] = $item_subscription['category_question'];
		}
		
	}
	$GLOBALS['WEB_JSON']['user_json']['tmp_substription_arr'] = $tmp_substription_arr ?: [];
	$GLOBALS['WEB_JSON']['user_json']['tmp_substription_all_arr'] = $tmp_substription_all_arr ?: [];
	$GLOBALS['WEB_JSON']['user_json']['tmp_substription_category_arr'] = $tmp_substription_category_arr ?: []; // ['a_a1', 'bc1', ...]
	*/
	$GLOBALS['WEB_JSON']['user_json']['domain'] = f_num_encode( $data_json['_id'] );
	
	$GLOBALS['WEB_JSON']['user_json']['tmp_id_str'] = f_num_encode( $data_json['_id'] );
	$GLOBALS['WEB_JSON']['user_json']['tmp_type_ru'] = (string)($data_json['type'] ?? '');
	//$GLOBALS['WEB_JSON']['user_json']['tmp_gender_ru'] = f_user_gender_ru( $data_json['gender'] );
	//$GLOBALS['WEB_JSON']['user_json']['tmp_city_ru'] = f_user_city_ru( $data_json['city'] );
	
	f_db_update_smart("user", ["_id" => $_id], [ 'visit_date' => date('Y-m-d H:i:s') ]);
	
	return true;
}

// f_user_auto();


function f_user_check(){
	
	return f_user_get() === false ? false : true;
	
}

function f_user_gender($user_gender=""){
	
	$user_gender_json = [
		'1'			=> 'Man',
		'2'			=> 'Women',
	];
	
	return $user_gender_json[$user_gender] ? $user_gender_json[$user_gender] : 'Unknown';
}

function f_user_city($user_city=""){
	$city_json = f_list_city();
	
	return $city_json[$user_city] ? $city_json[$user_city] : 'Unknown';
}


function f_user_check_redirect($type_user=false){
	if( f_user_check() == false ){
		f_user_exit();
	}else if( !in_array( f_user_get()['type'], ['admin', 'business', 'user', 'moderator'] ) ){
		f_user_exit();
	}
	
	if($type_user !== false){
		if( f_user_get()['type'] !== $type_user ){
			f_redirect('/');
		}
	}
}

function f_user_exit(){
	f_cookie_delete('uid');
	f_redirect('/');
}



function f_parse_number_str($text=""){
	return preg_replace('/\D/', '', $text);
}




function f_date_check($date=null){
	return strtotime($date) ? true : false;
}

function f_diff_date_to_time($date_1, $date_2, $format='%H:%I:%S') {
    // Преобразуем строки дат в объекты DateTime
    $date_1 = new DateTime($date_1);
    $date_2 = new DateTime($date_2);

    // Вычисляем разницу между датами
    $diff_date = $date_1->diff($date_2);

    // Возвращаем разницу в формате H:i:s
    return $diff_date->format('%H:%I:%S');
}

function f_date_diff_seconds($date_1, $date_2="now"){
	return strtotime($date_1) - strtotime($date_2);
}

function f_date_left_time($date_1, $date_2="now", $format="H:i:s"){
	return date($format, f_date_diff_seconds($date_1, $date_2="now"));
}
function f_date_left_time_1($date_end, $date_start="now") {
    // Получаем текущее время и время окончания работы в формате Unix timestamp
    $now = strtotime($date_start);
    $endTimestamp = strtotime($date_end);

    // Вычисляем разницу в секундах
    $diff = $endTimestamp - $now;

    // Проверяем, не прошла ли уже дата окончания
    if ($diff < 0) {
        return '00:00';
    }

    // Конвертируем разницу в минуты и секунды
    $minutes = floor($diff / 60);
    $seconds = $diff % 60;

    // Возвращаем строку с оставшимся временем в формате 'i:s'
    return sprintf('%02d:%02d', $minutes, $seconds);
}

function f_date_diff_days($date_1, $date_2){
	
    // Целевая дата
    $target_date_1 = DateTime::createFromFormat('Y-m-d H:i:s', $date_1);
    $target_date_2 = DateTime::createFromFormat('Y-m-d H:i:s', $date_2);
	
	$target_date_1->setTime(0, 0);
	$target_date_2->setTime(0, 0);
	
    // Разница между датами
    $interval = $target_date_1->diff($target_date_2);
    // Возвращаем количество дней
    return $interval->days;
}



function f_day_left($set_date, $only_date=true, $format_date='Y-m-d H:i:s') {

	if( !f_date_validate($set_date, $format_date) ){
		return '-';
	}
	
    // Текущее время
    $now = new DateTime();
    // Целевая дата
    $targetDate = DateTime::createFromFormat($format_date, $set_date);
	
	if($only_date == true){
		$now->setTime(0, 0);
		$targetDate->setTime(0, 0);
	}
	
    // Разница между датами
    $interval = $now->diff($targetDate);
	
	$days = $interval->days;
	
	if( strtotime($set_date) < strtotime('now') ){
		$days = $days * -1;
	}
	
    // Возвращаем количество дней
    return $days;
}


// Поиск подходящих слов к числу ["день", "дня", "дней"]  [1=> "день", 2=>"дня", 5=>"дней"]
function f_number_word($number, $word_arr) {
	$number = abs($number);
    $cases = [2, 0, 1, 1, 1, 2];
    $index = ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)];
    return $word_arr[$index];
}
function f_number_word_string($number, $word_arr, $glue=' ') {
    return $number . $glue . f_number_word($number, $word_arr);
}


function f_number_space($number) {
    // Используем number_format для добавления пробелов между тысячами
    // Второй параметр указывает количество знаков после запятой
    // Третий параметр - разделитель десятичных
    // Четвёртый параметр - пробел в качестве разделителя тысяч
    return number_format($number, (int)(is_float($number) ? 2 : 0), '.', ' ');
}


function f_referer_check($domain=false) {
    // Получаем домен текущей страницы
	if($domain == false){
		$domain = $_SERVER['HTTP_HOST'];
	}

    // Проверяем наличие заголовка Referer
    if (isset($_SERVER['HTTP_REFERER'])) {
        // Разбираем URL из заголовка Referer
        $domain_referer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

        // Сравниваем хосты
        return $domain === $domain_referer;
    }

    // Заголовок Referer отсутствует
    return false;
}





function f_shuffle_seed(&$items, $seed){
    @mt_srand($seed);
    for ($i = count($items) - 1; $i > 0; $i--)
    {
        $j = @mt_rand(0, $i);
        $tmp = $items[$i];
        $items[$i] = $items[$j];
        $items[$j] = $tmp;
    }
}






function f_seo_text_to_url($text="", $max_length=200) {
    // Транслитерация русских, казахских и испанских букв
    $transliterationTable = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        // Казахские буквы
        'ә' => 'a', 'ғ' => 'g', 'қ' => 'k', 'ң' => 'n', 'ө' => 'o',
        'ұ' => 'u', 'ү' => 'u', 'һ' => 'h',
        // Испанские буквы
        'ñ' => 'n', 'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        // Украинские буквы
        'ґ' => 'g', 'є' => 'ie', 'і' => 'i', 'ї' => 'yi', 'й' => 'i',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'shch', 'ю' => 'yu', 'я' => 'ya',
        // Греческие буквы
        'α' => 'a', 'β' => 'v', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e',
        'ζ' => 'z', 'η' => 'i', 'θ' => 'th', 'ι' => 'i', 'κ' => 'k',
        'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'x', 'ο' => 'o',
        'π' => 'p', 'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y',
        'φ' => 'f', 'χ' => 'ch', 'ψ' => 'ps', 'ω' => 'o', 'ά' => 'a',
        'έ' => 'e', 'ή' => 'i', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y',
        'ώ' => 'o', 'ϊ' => 'i', 'ϋ' => 'y', 'ΐ' => 'i', 'ΰ' => 'y',
        // Арабские буквы
        'أ' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j',
        'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'dh', 'ر' => 'r',
        'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd',
        'ط' => 't', 'ظ' => 'dh', 'ع' => 'a', 'غ' => 'gh', 'ف' => 'f',
        'ق' => 'q', 'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n',
        'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ى' => 'a', 'ئ' => 'y',
        'ء' => 'a', 'ؤ' => 'w', 'إ' => 'a', 'آ' => 'a', 'ة' => 'h',
        '۔' => '.', 'ي' => 'y', 'ے' => 'e', 'ە' => 'a', 'چ' => 'ch',
        'ک' => 'k', 'گ' => 'g', 'ں' => 'n', 'ھ' => 'h', 'ہ' => 'h',
        'آ' => 'a', 'ا' => 'a', 'پ' => 'p', 'ژ' => 'zh', 'ڤ' => 'v',
        'ڭ' => 'ng', 'ڱ' => 'g', 'ڪ' => 'k', 'ں' => 'n', 'ھ' => 'h',
        'ہ' => 'h', 'ے' => 'e', 'ۓ' => 'ai',
    ];

    // Преобразование текста в нижний регистр
    $text = mb_strtolower($text, 'UTF-8');
    // Транслитерация
    $text = strtr($text, $transliterationTable);
    // Удаление всех символов, кроме букв, цифр и пробелов
    $text = preg_replace('/[^\\p{L}\\p{Nd}]+/u', '-', $text);
    // Удаление начальных и конечных тире
    $text = trim($text, '-');
    // Замена пробелов и повторяющихся тире на одиночные тире
    $text = preg_replace('/-+/', '-', $text);
    // Обрезание текста до максимальной длины
    $text = mb_substr($text, 0, $max_length, 'UTF-8');
    // Удаление конечных тире после обрезания
    $text = rtrim($text, '-');

    return $text;
}




function f_html_pagination($current_page, $limit_rows, $total_rows) {
	//$total_rows= 500;
	//$limit_rows = 10;
	
	$total_pages = ceil($total_rows / $limit_rows);
	
	$pagination_html = '<div class="pagination  flex-wrap  justify-content-center  mt-4">';

	// Ограничиваем количество отображаемых кнопок пагинации
	$start = max(1, $current_page - 2);
	$end = min($total_pages, $current_page + 2);
	
	// Текущий номер страницы больше общего количества
	if( $total_pages < 2 ){
		return '';
	}
	
	$is_current_error_big = false;
	if( $current_page > $total_pages ){
		$current_page = $total_pages;
		$start = max(1, $current_page - 2);
		$end = min($total_pages, $current_page + 2);
		$is_current_error_big = true;
	}
	
	// Кнопка "Назад"
	if ($current_page > 1) {
		$pagination_html .= '<div class="page-item" page="' . ( $is_current_error_big == false ? $current_page - 1 : $total_pages ) . '"> <div class="page-link  bi bi-chevron-left"> </div></div>';
	}
	
	// Кнопка "Начало"
	if($start != 1){
		$pagination_html .= '<div class="page-item" page="' . 1 . '"> <div class="page-link">' . 1 . '</div></div>';
		
		//if($start-1 != 1){
		if($start-1 > 1){
			$pagination_html .= '<div class="page-item  disabled"> <div class="page-link"> ... </div></div>';
		}
	}
	
	for ($i = $start; $i <= $end; $i++) {
		// Ссылка на страницу
		$pagination_html .= '<div class="page-item '. ( ($i == $current_page  && $is_current_error_big == false) ? 'active' : '') .'" page="' . $i . '"> <div class="page-link"> ' . $i . '</div></div>';
	}
	
	// Кнопка "Конец"
	if($end != $total_pages){
		if($end+1 != $total_pages){
			$pagination_html .= '<div class="page-item  disabled"> <div class="page-link"> ... </div></div>';
		}
		$pagination_html .= '<div class="page-item " page="' . $total_pages . '"> <div class="page-link">' . $total_pages . '</div></div>';
	}
	
	
	// Кнопка "Вперед"
	if ($current_page < $total_pages) {
		$pagination_html .= '<div class="page-item " page="' . ($current_page + 1) . '"> <div class="page-link  bi bi-chevron-right">  </div></div>';
	}

	$pagination_html .= '</div>';

	return $pagination_html;
}


function f_html_checkbox($field_name, $checked_on=false, $title=''){
	$html = '
		<div class="form-check">
			<input class="form-check-input" type="checkbox" field_name="'. $field_name .'" id="'. $field_name .'" '. ( $checked_on == true ? 'checked' : '' ) .'>
			<label class="form-check-label" for="'. $field_name .'">' . f_translate($title) . '</label>
		</div>
	';
	return $html;
}
function f_html_checkbox_echo($field_name, $checked_on=false, $title=''){
	f_echo( f_html_checkbox($field_name, $checked_on, $title) );
}



function f_arr_random_group($arr) {
    $groups = [];
    $numbersCount = count($arr);

    // Пока есть числа в массиве
    while ($numbersCount > 0) {
        // Определяем случайное количество элементов для группы
        $groupCount = rand(1, $numbersCount);

        // Выбираем случайное подмножество
        $group = array_splice($arr, 0, $groupCount);
        shuffle($group);

        // Добавляем группу в результат
        $groups[] = $group;

        // Обновляем количество оставшихся чисел
        $numbersCount -= $groupCount;
    }

    return $groups;
}

function f_css_text_encode($text="") {
    $unicode_string = '';
    $textLength = mb_strlen($text, 'UTF-8');

    for ($i = 0; $i < $textLength; $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        $unicode = mb_convert_encoding($char, 'UCS-2BE', 'UTF-8');
        $unicode_string .= '\\' . strtoupper(bin2hex($unicode));
    }

    return $unicode_string;
}

function f_chars_unique($text="") {
      $unique_chars = array_unique(mb_str_split($text, 1, 'UTF-8'));
      return $unique_chars;
}

function f_chars_popular(){
	$chars = 'әғқңөұүһіӘҒҚҢӨҰҮҺІ';
	$chars .= 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
	$chars .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$chars .= '1234567890';
	$chars .= "~!@#$%^&*()_+{}:\"`,.<>?№;%:?[]'/\\ \n\t—";
	return $chars;
}


function f_html_obus_chars_init(){

	$data_json = [
		'salt' => date('d'),
		'style' => '',
		'style_arr' => [],
		'chars_arr' => [],
		//'chars_unicode_arr' => [],
		//'chars_hash_arr' => [],
		//'chars_html_arr' => [],
		'chars_html_json' => [],
	];
	
	$data_json['chars_arr'] = mb_str_split(f_chars_popular(), 1, 'UTF-8');
	
	$data_json['style_arr'][] = "i[enc]{font-style: inherit}"; //i[enc]::after{margin-right: 5px}
	
	foreach($data_json['chars_arr'] as $letter){
		$letter_unicode = f_css_text_encode($letter);
		$letter_hash = hash('crc32c', $data_json['salt']  . $letter);
		$letter_html = '<i enc="'.$letter_hash.'"></i>';
		
		$data_json['style_arr'][] = 'i[enc="'.$letter_hash.'"]::after{content: "'. $letter_unicode .'"}';  // i[enc="asdf"]::after{content: '\8423'}
		//$data_json['style'] .= 'i[enc="'.$letter_hash.'"]::after{content: "'. $letter_unicode .'"}';  // i[enc="asdf"]::after{content: '\8423'}
		//$data_json['chars_unicode_arr'][] = $letter_unicode;
		//$data_json['chars_hash_arr'][] = $letter_hash;
		//$data_json['chars_html_arr'][] = $letter_html;
		
		$data_json['chars_html_json'][$letter] = $letter_html;
	}
	
    //shuffle($data_json['style_arr']);
	//$data_json['style'] = '<style>' . implode('',  $data_json['style_arr']) . '</style>';
	//$data_json['style'] = '<div><style>' . implode('</style><style>',  $data_json['style_arr']) . '</style></div>';
	$tmp_arr_group = f_arr_random_group( $data_json['style_arr'] );
	foreach($tmp_arr_group as $arr_group){
		$data_json['style'] .= '<style>' . implode('',  $arr_group) . '</style>';
	}
	
	unset($data_json['salt']);
	unset($data_json['style_arr']);
	unset($data_json['chars_arr']);
	
	$GLOBALS['html_text_obus_json'] = $data_json;
}
$html_text_obus_json = [];

function f_html_obus_chars($text=""){
	$text_arr = mb_str_split($text, 1, 'UTF-8');
	
	$text_new = '';
	foreach($text_arr as $letter){
		$letter_html = $GLOBALS['html_text_obus_json']['chars_html_json'][$letter];
		
		$text_new .= $letter_html ?? $letter;
	}
	return $text_new;
}

function f_html_obus_chars_style(){
	return $GLOBALS['html_text_obus_json']['style'];
}









// Функция для создания URL авторизации Google
function f_google_auth_create_auth_url() {
    $base_url = "https://accounts.google.com/o/oauth2/auth";
    $params = [
        'response_type' => 'code',
        'client_id' => $GLOBALS['WEB_JSON']['api_json']['google_oauth_client_id'],
        'redirect_uri' => $GLOBALS['WEB_JSON']['api_json']['google_oauth_redirect_url'],
        'scope' => 'email profile',
        'access_type' => 'offline',
        'prompt' => 'select_account'
    ];
    return $base_url . '?' . http_build_query($params);
}

// Функция для получения токена доступа от Google
function f_google_auth_get_access_token($code) {
	
	return f_curl(
		'https://oauth2.googleapis.com/token',
		[
			'post' => [
				'code' => $code,
				'client_id' => $GLOBALS['WEB_JSON']['api_json']['google_oauth_client_id'],
				'client_secret' => $GLOBALS['WEB_JSON']['api_json']['google_oauth_client_secret'],
				'redirect_uri' => $GLOBALS['WEB_JSON']['api_json']['google_oauth_redirect_url'],
				'grant_type' => 'authorization_code'
			]
		],
		true
	);
	
}

// Функция для получения данных пользователя от Google
function f_google_auth_get_user_data($access_token) {
	return f_curl(
		'https://www.googleapis.com/oauth2/v1/userinfo',
		[
			'get' => [
				'access_token' => $access_token
			]
		],
		true
	);
}


function f_curl($url, $json_params=[], $json_decode=false) {
	
	/*
	json_params = [
		'post' => [],
		'get' => [],
		'header' => [],
	];
	*/
	
    // Инициализация cURL
    $curl = curl_init();

    // Если это GET-запрос и есть параметры
    if ($json_params['get'] && !$json_params['post']) {
        $url .= '?' . http_build_query($json_params['get']);
    }

    // Установка URL и других соответствующих опций
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    // Если это POST-запрос
    if ($json_params['post']) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($json_params['post']));
    }

    // Если есть заголовки
    if ($json_params['header']) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $json_params['header']);
    }

    // Выполнение запроса и закрытие cURL
    $response = curl_exec($curl);
	//$error_message = curl_error($curl);
	
    curl_close($curl);
	
	if( $json_decode ){
		return json_decode($response, true);
	}

    // Возврат результата
    return $response;
}


function f_user_set_cookie($user_data, $is_login=false){
	
	$uid = f_num_encode($user_data['_id']) . '-' . ( $user_data['password_hash_sha256'] ?: hash('sha256', $user_data['google_id']) );
	
	if( $is_login == true ){
		f_db_update_smart(
			"user",
			["_id"			=> $user_data['_id']], 
			["visit_date"	=> date('Y-m-d H:i:s', strtotime('now'))]
		);
	}
	
	f_cookie_set("uid", $uid, strtotime("+30 days"));
	
	//var_dump($user_data);
	//exit();
}



function f_user_check_exist_redirect(){
	if( f_user_check() == true ){
		f_redirect('/');
	}
}


//var_dump( f_html_obus_chars('Добро пожаловать, к нам!') );

//var_dump( $GLOBALS['html_text_obus_json']['style'] );





/*
function f_unicode_encode_1($text) {
	return mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text));
}
function f_unicode_encode_2($text) {
    $unicode = array();
    for ($i = 0; $i < mb_strlen($text); $i++) {
        $char = mb_substr($text, $i, 1);
        $unicode[] = 'u' . dechex(mb_ord($char));
    }
    return implode(' ', $unicode);
}
function f_unicode_encode($text) {
	$unicodeStr = '';
	for ($i = 0; $i < mb_strlen($text); $i++) {
		$unicodeStr .= '&#' . mb_ord(mb_substr($text, $i, 1)) . ';';
	}
	return $unicodeStr;
}


function f_echo_encode($text=''){
	echo( f_unicode_encode($text) );
}

exit( '<h1>' . f_unicode_encode('Привет') . '</h1>' );
*/

?>