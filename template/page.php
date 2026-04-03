<!DOCTYPE html>
<html lang="<?php f_echo_html( $GLOBALS['WEB_JSON']['page_json']['lang'] ); ?>">
<head>
	
	<?php
		f_template('head_body');
		echo( $GLOBALS['WEB_JSON']['page_json']['html_head'] );
		f_template('style');
	?>
	
	<!-- Google Adsense Verification -->
	<meta name="google-adsense-account" content="ca-pub-5845837730667464">

</head>
<body>

<div class="page">

<?php

echo( $GLOBALS['WEB_JSON']['page_json']['html_top'] );

f_template('nav_top');

echo($GLOBALS['WEB_PAGE_HTML']);

?>

<?php
	if( $GLOBALS['WEB_JSON']['page_ads']['side'] ){
?>
	<!-- Google Adsense Banner Side Left -->
	<div class="page_ads_side_left">
		<div class="page_ads_side_x_body">
			<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5845837730667464" crossorigin="anonymous"></script>
			<ins class="adsbygoogle"
				 style="display:block"
				 data-ad-format="autorelaxed"
				 data-ad-client="ca-pub-5845837730667464"
				 data-ad-slot="6722038286"></ins>
			<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
		</div>
	</div>
	
	<!-- Google Adsense Banner Side Right -->
	<div class="page_ads_side_right">
		<div class="page_ads_side_x_body">
			<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5845837730667464" crossorigin="anonymous"></script>
			<ins class="adsbygoogle"
				 style="display:block"
				 data-ad-format="autorelaxed"
				 data-ad-client="ca-pub-5845837730667464"
				 data-ad-slot="4155584692"></ins>
			<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
		</div>
	</div>
<?php
	}
?>

</div>


<?php

f_template('footer');

f_template('script');

echo( $GLOBALS['WEB_JSON']['page_json']['html_bottom'] );

?>

</body>
</html>
