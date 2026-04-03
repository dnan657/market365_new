<?php

header('Content-Type: application/json; charset=utf-8');

$gl_response_json = [
	
	"name" => $WEB_JSON['page_json']['site_name'],
	"short_name" => $WEB_JSON['page_json']['site_name_short'],
	"description" => "",
	"lang" =>  $WEB_JSON['page_json']['lang_iso'],
	"start_url" => "/",
	"display" => "standalone",
	"orientation" => "portrait",
	
	//"theme_color" => $WEB_JSON['page_json']['theme_color'], // создаёт цвет сверху и тень(которая всё портит)
	"background_color" => $WEB_JSON['page_json']['background_color'],
	
	"screenshots" => [
		/*
		[
			"src" => "/public/img/screenshot_1.png",
			"sizes" => "640x320",
			"type" => "image/png",
			"form_factor" => "wide",
			"label" => "Wonder Widgets"
		],
		[
			"src" => "/public/img/screenshot_1_phone.png",
			"sizes" => "320x640",
			"type" => "image/png",
			"form_factor" => "narrow"
			"label" => "Wonder Widgets"
		]
		*/
	],
	
	"icons" => [
		["src" => "/public/favicon/16x16.png", "sizes" => "16x16", "type" => "image/png"],
		["src" => "/public/favicon/32x32.png", "sizes" => "32x32", "type" => "image/png"],
		["src" => "/public/favicon/70x70.png", "sizes" => "70x70", "type" => "image/png"],
		["src" => "/public/favicon/144x144.png", "sizes" => "144x144", "type" => "image/png"],
		["src" => "/public/favicon/150x150.png", "sizes" => "150x150", "type" => "image/png"],
		["src" => "/public/favicon/192x192.png", "sizes" => "192x192", "type" => "image/png"],
		["src" => "/public/favicon/310x150.png", "sizes" => "310x150", "type" => "image/png"],
		["src" => "/public/favicon/310x310.png", "sizes" => "310x310", "type" => "image/png"],
		["src" => "/public/favicon/512x512.png", "sizes" => "512x512", "type" => "image/png"],
		["src" => "/public/favicon/70x70.png", "sizes" => "70x70", "type" => "image/png", "purpose" => "any maskable"],
		["src" => "/public/favicon/144x144.png", "sizes" => "144x144", "type" => "image/png", "purpose" => "any maskable"],
		["src" => "/public/favicon/192x192.png", "sizes" => "192x192", "type" => "image/png", "purpose" => "any maskable"],
		["src" => "/public/favicon/512x512.png", "sizes" => "512x512", "type" => "image/png", "purpose" => "any maskable"]
	]
];

f_api_response_exit($gl_response_json);

?>