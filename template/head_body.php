
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />


<?php

// НУЖНО БУДЕТ СКАЧАТЬ ВСЕ БИБЛИОТЕКИ - если какой-то сайт библиотеки упадет, то упадет и сайт

?>

<title>
	<?php echo( htmlentities( f_html_head_title() ) ); ?>
</title>

<link href="/manifest.json" rel="manifest">

<meta name="robots" content="all">

<link rel="shortcut icon" href="/public/favicon/favicon.ico?r=<?php echo(filemtime($WEB_JSON['dir_public'].'favicon/favicon.ico')); ?>">
<link rel="icon" type="image/x-icon" href="/public/favicon/favicon.ico?r=<?php echo(filemtime($WEB_JSON['dir_public'].'favicon/favicon.ico')); ?>">
<!--<link rel="mask-icon" href="/public/favicon/logo.svg" color="#000">-->
<?php
	$favicon_size_arr = [16, 32, 57, 60, 70, 72, 76, 96, 114, 120, 128, 144, 150, 152, 167, 180, 192, 195, 196, 228, 270, 310, 512];
	foreach($favicon_size_arr as&$size){
		echo('<link rel="icon" type="image/png" sizes="'.$size.'x'.$size.'" href="/public/favicon/'.$size.'x'.$size.'.png?r='. filemtime($WEB_JSON['dir_public'].'favicon/'.$size.'x'.$size.'.png') . '">');
	}
?>


<!-- Apple Favicon -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="white">
<link rel="apple-touch-icon" href="/public/favicon/180x180.png?r=<?php echo(filemtime($WEB_JSON['dir_public'].'favicon/180x180.png')); ?>">
<?php
	$favicon_size_arr = [57, 72, 76, 114, 120, 144, 152, 180];
	foreach($favicon_size_arr as&$size){
		echo('<link rel="apple-touch-icon" sizes="'.$size.'x'.$size.'" href="/public/favicon/'.$size.'x'.$size.'.png?r='. filemtime($WEB_JSON['dir_public'].'favicon/'.$size.'x'.$size.'.png') .'">');
	}
?>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />

<link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet" />


<!-- Подключение CSS для Leaflet -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />


<!-- Подключение CSS для Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

<?php

	f_page_library_add("simplebar");

?>



<!--
<script>
	window.addEventListener('load', async () => {
		if('serviceWorker' in navigator){
			navigator.serviceWorker.register('/sw.js?r=1')
		}
	})
</script>
-->
