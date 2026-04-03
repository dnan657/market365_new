
<?php
	$lang_current = $GLOBALS['WEB_JSON']['page_json']['lang'];
?>

<form class="dropdown" method="post">
	<button class="btn btn-outline-dark dropdown-toggle  border_radius  mx-auto d-block" type="button" data-bs-toggle="dropdown" aria-expanded="false">
		<?php f_echo_html( mb_strtoupper( $lang_current ) ); ?>
	</button>
	<div class="dropdown-menu  border_radius  p-0" style="min-width: 65px; 	overflow: hidden;">
		<div><button class="dropdown-item  btn btn-outline-dark  border_radius  text-center  py-2  <?php f_echo_html( $lang_current == 'ru' ? 'active' : '' ); ?>" type="submit" name="change_lang" value="ru">RU</button></div>
		<div><button class="dropdown-item  btn btn-outline-dark  border_radius  text-center  <?php f_echo_html( $lang_current == 'kz' ? 'active' : '' ); ?>" type="submit" name="change_lang" value="kz">KZ</button></div>
		<div><button class="dropdown-item  btn btn-outline-dark  border_radius  text-center  py-2  <?php f_echo_html( $lang_current == 'en' ? 'active' : '' ); ?>" type="submit" name="change_lang" value="en">EN</button></div>
	</div>
</form>