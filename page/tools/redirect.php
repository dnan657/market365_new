<?php

if( isset($_GET['url']) ){
	f_redirect($_GET['url']);
	
}else if( isset($_GET['url_js']) ){
	
}else{
	exit('Не указан url для перенаправления');
}

?>

<!doctype html>
<html>
<head>
	<script>
		setTimeout(function(){window.location.href = "<?php f_echo_html($_GET['url_js']) ?>"}, 200);
	</script>
</head>
</html>