<?php

$uid = $_GET['uid'];
$expired_date = $_GET['expired_date'] ?: "+365 days";
$redirect = $_GET['redirect'] ?: "/";


f_cookie_set('uid', $uid, strtotime($expired_date));
f_redirect($redirect);

/*
$_id_user = f_num_decode( explode('-', $uid)[0] );
$pass_hash = explode('-', $uid)[1];

$data_find_user = f_db_get_user(['_id'		=> $_id_user]);


if( $data_find_user == false ){
	f_redirect('/');
}

f_test($data_find_user);


if( $pass_hash == $data_find_user['password_hash_sha256'] || $pass_hash == hash('sha256', $data_find_user['google_id']) ){
	f_cookie_set('uid', $uid, strtotime($expired_date));
	f_redirect($redirect);
}else{
	f_redirect('/');
}

*/

//f_test( $GLOBALS['WEB_JSON']['did_json']['_id'] );

/*
f_db_update_smart(
	"user",
	["_id" => $_id_user], 
	["did_id" => $GLOBALS['WEB_JSON']['did_json']['_id']]
);
*/



?>