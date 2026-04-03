<?php

//f_test($WEB_JSON);

//https://pdd.ree.kz/file/_user_id_str/_random_charBASE64_FILEPATH/SSID
//$WEB_JSON["uri_dir_arr"] = [ "file", "charBASE64", "SSID" ];
// '/public/video_933/20220620_0701_3lifh5hd24_video.mp4'
// https://pdd.ree.kz/file/Mw/-L3B1YmxpYy92aWRlb185MzMvMjAyMjA2MjBfMDcwMV8zbGlmaDVoZDI0X3ZpZGVvLm1wNA/hdqo7tkpojr91v297sv4bbnmcc

// Проверка на Referer
/*
if(f_referer_check() == false){
	exit();
	exit('REFERER НЕ СОВПАДАЕТ');
}
*/

// Проверка на авторизацию
if(f_user_check() == false){
	//var_dump(f_user_get());
	exit();
	exit('ПОЛЬЗОВАТЕЛЬ НЕ АВТОРИЗОВАН');
}

// Проверка на совпадения uid_id_str
$uid_id_str = $WEB_JSON["uri_dir_arr"][1];
if($uid_id_str != f_num_encode($GLOBALS['WEB_JSON']['user_json']['_id']) ){
	exit();
	exit('USER_ID_STR НЕ СОВПАДАЕТ');
}


// Проверка на совпадения SSID
$ssid = $WEB_JSON["uri_dir_arr"][3];
$ssid = explode('.', $ssid)[0];
if($ssid != session_id()){
	exit();
	exit('СЕССИИ НЕ СОВПАДАЮТ');
}


// Расшифровка ПУТЯ к ФАЙЛУ
$file_path = $WEB_JSON["uri_dir_arr"][2];
$file_path = mb_substr($file_path, 1);
$file_path = base64_decode( $file_path );
$file_path = $WEB_JSON["dir_public"] . explode( '/public/', $file_path )[1];

$file_name_and_ext = basename($file_path);
$file_extension_arr = explode('.', $file_name_and_ext);

$file_name = $file_extension_arr[0];
$file_name = hash('crc32b', $file_extension_arr[0]);
$file_ext = count($file_extension_arr) == 1 ? '' : '.' . end($file_extension_arr);
$file_hash_name = $file_name . $file_ext;

if (!file_exists($file_path)) {
	exit();
	exit('ФАЙЛ НЕ НАЙДЕН');
}

header('Content-Description: File Transfer');
header('Content-Type: '. (mime_content_type($file_path) ?? 'application/octet-stream') );
header('Content-Disposition: attachment; filename="'.$file_hash_name.'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit();

?>