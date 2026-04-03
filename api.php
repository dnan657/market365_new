<?php


header('Content-Type: application/json; charset=utf-8');

$gl_response_json = [
	'data' => [],
	
	'metadata' => [
	
		'query_json' => [
			'get' => $_GET,
			'post' => $_POST,
			'args' => $ARGS,
		],
		
		'recaptcha' => [
			'ok' => "",
			'success' => "",
			'score' => "",
			'action' => "",
		],
		
		'query' => $WEB_JSON['uri_dir_arr'][2],
		'method' => $WEB_JSON['uri_dir_arr'][1],
		
		'error' => "",
		'error_description' => "",
		'error_code' => 0,
		
		'response_date' => gmdate('Y-m-d H:i:s', time()),
	]
];
	

$api_file_path = $WEB_JSON['dir_api'] . $gl_response_json['metadata']['method'] . '.php';

if (file_exists($api_file_path)) {
	require($api_file_path); // gl_api_func_json
	
	if( $gl_api_func_json[ $gl_response_json['metadata']['query'] ] != '' ){
		
		// Проверка Капчи - Если она установлена
		$recaptcha_ok = true;
		$recaptcha_response = $ARGS['recaptcha_response'];
		if(isset($recaptcha_response)){
			$recaptcha_json = f_google_recaptcha($recaptcha_response);
			$recaptcha_ok = $recaptcha_json['ok'];
			$gl_response_json['metadata']['recaptcha']['ok'] = $recaptcha_json['ok'];
			$gl_response_json['metadata']['recaptcha']['success'] = $recaptcha_json['success'];
			$gl_response_json['metadata']['recaptcha']['score'] = $recaptcha_json['score'];
			$gl_response_json['metadata']['recaptcha']['action'] = $recaptcha_json['action'];
		}
		
		if( $recaptcha_ok == true ){
			$gl_response_json['data'] = $gl_api_func_json[ $gl_response_json['metadata']['query'] ]($ARGS, $WEB_JSON);
			
			if( $gl_response_json['data']['error'] ){
				$gl_response_json['data']['error'] = f_translate( $gl_response_json['data']['error'] );
			}
		}else{
			$gl_response_json['data']['error'] = f_translate('Возможно, вы были ошибочно определены как бот. Пожалуйста, повторите попытку позже');
			$gl_response_json['data']['error_code'] = 1;
		}
		
	}else{
		$gl_response_json['metadata']['error'] = f_translate('Запрос для API не найден');
		$gl_response_json['metadata']['error_code'] = -2;
	}
}else{
	$gl_response_json['metadata']['error'] = f_translate('Метод для API не найден');
	$gl_response_json['metadata']['error_code'] = -1;
}

f_api_response_exit($gl_response_json);
	
?>