<?php

// https://pdd.ree.kz/api/cron?query=expired

// Набор функций
$gl_api_func_json = [
	"expired"			=> "f_api_cron_expired",
];


function f_api_cron_expired($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	$secret = trim((string)($GLOBALS['WEB_JSON']['api_json']['cron_secret'] ?? ''));
	if( $secret === '' || (string)($ARGS['secret'] ?? '') !== $secret ){
		$response_json['error'] = 'Forbidden';
		$response_json['error_code'] = 403;
		return $response_json;
	}
	
	f_db_get_test_update_expired();
	f_db_get_subscription_update_expired();
	
	return $response_json;
}



?>
