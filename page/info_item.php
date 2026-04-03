<?php

// Поиск данных по _ID_STR
$page_uri = $WEB_JSON["uri_dir_arr"][1];

$item_json = f_db_select_smart("info", ['uri' => $page_uri, '_delete_on' => 0])[0];

if( !isset($item_json) ){
	//f_redirect( f_page_link('page_info') );
	f_redirect( '/' );
}

if( $WEB_JSON["uri_dir_arr"][2] != '' ){
	f_redirect(f_page_link('page_info') . '/' . $page_uri);
}



f_page_title_set( $item_json['title_en'] );

?>

<style>
.description_info{
	margin-bottom: var(--v_p_20);
}

.body_info{
	font-size: var(--v_font_default);
}
.body_info  h1{
	font-size: var(--v_font_h1);
}
.body_info  h2{
	font-size: var(--v_font_h2);
	margin-top: var(--v_p_30);
	margin-bottom: var(--v_p_10);
}

/* для Мобилок */
@media (max-width: 1000px) {
	
	.body_info  h1{
		font-size: var(--v_font_h2);
	}
	.body_info  h2{
		font-size: var(--v_font_h3);
	}
	.body_info{
		font-size: var(--v_font_small);
	}
}

</style>


<div class="page_margin_top"></div>


<div class="container">
	<div class="info">

		<h1>
			<?php f_echo( $item_json['title_en'] ); ?>
		</h1>
		
		<div class="description_info">
			<?php f_echo( $item_json['description_en'] ); ?>
		</div>
		
		<div class="body_info">
			<?php f_echo( strip_tags( $item_json['body_html_en'], ['h2', 'h3', 'h4', 'h5', 'h6', 'span', 'div', 'ul', 'ol', 'li', 'table', 'tbody', 'thead', 'td', 'tr', 'th', 'i', 'b', 'small', 'img', 'p', 'a']  ) ); ?>
		</div>

	</div>
</div>
