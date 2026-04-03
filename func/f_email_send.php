<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require($WEB_JSON['dir_class'] . 'phpmailer/Exception.php');
require($WEB_JSON['dir_class'] . 'phpmailer/PHPMailer.php');
require($WEB_JSON['dir_class'] . 'phpmailer/SMTP.php');

function f_email_send($email_to, $title='', $html_body='', $login="noreply"){
	
	$result_json = [
		'error' => ''
	];
	
	$data_json = $GLOBALS['WEB_JSON']['email_json'][$login];
	
	if( !isset($data_json) ){
		$result_json['error'] = 'Не найден конфиг с таким логином - ' . $login;
		return $result_json;
	}
	
	$data_json['email_to'] = $email_to;
	$data_json['title'] = $title;
	$data_json['body'] = $html_body;
	
	$result_json = f_phpmailer_send($data_json);
	
	return $result_json;
}

function f_phpmailer_send($data_json=[]){
	
	$result_json = [
		'error' => '',
		'query_json' => $data_json
	];
	
	$check_keys_arr = ['login', 'pass', 'port', 'host'];
	foreach($check_keys_arr as $name => $value){
		if( !isset( $data_json[ $value ] ) ){
			$result_json['error'] = "Не указан - " . $value;
			return $result_json;
		}
	}
	
	if( !isset($data_json['email_to']) && !isset($data_json['email_to_arr']) ){
		$result_json['error'] = "Не указан - email_to & email_to_arr";
		return $result_json;
	}
	
	
	$data_json['email_to_arr'] = $data_json['email_to_arr'] ?? ( gettype($data_json["email_to"]) == 'array' ? $data_json["email_to"] : [$data_json["email_to"]] );
	
	$data_json["ssl"] = $data_json["ssl"] === false ? false : true;
	$data_json["html"] = $data_json["html"] ?? true;
	$data_json["charset"] = $data_json["charset"] ?? "UTF-8";
	$data_json["from_email"] = $data_json["from_email"] ?? $data_json["login"];
	$data_json["email_replay_to_arr"] = $data_json["email_replay_to_arr"] ?? [];
	$data_json["files_arr"] = $data_json["files_arr"] ?? [];
	
	$mail = new PHPMailer(true);
	
	try {
		
		$mail->CharSet = $data_json["charset"];
		$mail->isSMTP();   // Set mailer to use SMTP
		if($data_json["ssl"] == true){
			$mail->SMTPSecure = "ssl";   // Enable TLS encryption, `ssl` also accepted
		}
		
		$mail->SMTPDebug = 0;
		$mail->SMTPAuth = true;
		$mail->XMailer = ' ';   // Удаляем заголовок в письме что отправлено через программу ' ' (пробел обязателен)
		
		$mail->Host = $data_json["host"];
		$mail->Port = $data_json["port"];
		$mail->Username = $data_json["login"];
		$mail->Password = $data_json["pass"];
		
		if(!$data_json["from"]){
			$data_json["from"] = [$data_json["login"]];
		}
		
		if( isset( $data_json["from_name"] ) ){
			$mail->setFrom($data_json["from_email"], $data_json["from_name"]);
		}else{
			$mail->setFrom($data_json["from_email"]);
		}
		
		// Добавляем Email получателей
		foreach($data_json["email_to_arr"] as $email_to){
			$mail->addAddress($email_to);     // $mail->addAddress('ondus93@bk.ru', 'TO');   $mail->addAddress('ellen@example.com');
		}
		
		// Добавляем
		foreach($data_json["email_replay_to_arr"] as $email_to){
			$mail->addReplyTo($email_to);     //$mail->addReplyTo('info@example.com', 'Information');   $mail->addReplyTo('info@example.com')
		}
		foreach($data_json["files_arr"] as $file_path){
			$mail->addAttachment($file_path);     //$mail->addReplyTo('info@example.com', 'Information');   $mail->addReplyTo('info@example.com')
		}
		
		$mail->isHTML($data_json["html"]);
		$mail->Subject = $data_json["title"];
		$mail->Body    = $data_json["body"];
		
		$mail->AltBody = $data_json["alt_body"] ?? strip_tags($data_json["alt_body"]);
		
		$mail->send();
		
	} catch (Exception $e) {
		
		$result_json["error"] = $mail->ErrorInfo;
	}
	
	/*
	if($result_json["error"] != ''){
		var_dump( $result_json["error"] );
	}
	*/
	
	return $result_json;
}


/*
$data_json = [
	/*
	"login" => "ondus93@bk.ru",
	"pass" => "2012umeret",
	"port" => "465",
	"host" => "smtp.mail.ru",
	
	"login" => "loshadevich@gmail.com",
	"pass" => "2012umeret705706qqq",
	"port" => "465",
	"host" => "smtp.gmail.com",
	*
	"login" => "test@ree.kz",
	"pass" => "qwerty_1234",
	"port" => "465",
	"host" => "smtp.mail.ru",
	//"smtp" => true,
	"ssl" => true,
	
	"html" => true,
	"charset" => "UTF-8",
	"title" => "Title",
	"body" => "HTML Body",
	"alt_body" => "Alt Body",
	
	"from" => ['test@ree.kz', 'TEST'], // ('ondus93@bk.ru', 'Name_FROM')   ($email_from)
	"email_to_arr" => ['loshadevich@gmail.com', 'ondus93@bk.ru'], // ($email_to, 'Name_TO')   ($email_to)
	"email_replay_to_arr" => [], // 
	"files_arr" => [] // ('/tmp/image.jpg', 'new.jpg')   ('/tmp/image.jpg')
];
*/



?>