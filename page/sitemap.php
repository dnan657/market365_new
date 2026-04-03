<?php


$category_arr = f_db_select_smart("ads_category", ["hide_on" => 0], 1000, 0, ["sort"=>0]);

/*
// Поиск данных по _ID_STR
$page_uri = $WEB_JSON["uri_dir_arr"][1];

if( !isset($item_json) ){
	//f_redirect( f_page_link('page_info') );
	f_redirect( '/' );
}

if( $WEB_JSON["uri_dir_arr"][2] != '' ){
	f_redirect(f_page_link('page_info') . '/' . $page_uri);
}
*/

/*
function f_ads_category_tree_arr($category_arr, $parent_id=null, $url_path="/category/", $level=1){
	
	$new_arr = [];
	
	foreach ($category_arr as $category_json) {
		if ($category_json['parent_id'] == $parent_id || ($category_json['parent_bro_2_id'] == $parent_id && $parent_id != null)) {
			
			$category_json['tmp_type_class'] = $parent_id == null ? 'item_category' : 'mini_category';
			$category_json['tmp_url_path'] = $url_path . $category_json['domain'];

			// Рекурсивно вызываем функцию для построения подгрупп
			$category_json['tmp_arr_child'] = f_ads_category_tree($category_arr, $category_json['_id'], $category_json['tmp_url_path'] . '/', $level+1);
			
			$new_arr[] = $category_json;
		}
	}

	return $new_arr;
}

function f_ads_category_tree_draw($category_arr, $parent_id=null, $url_path="/category/", $level=1){
	$html = $parent_id == null ? '<div class="list_category">' : '<ul class="sub_category  row"  data-masonry=\'{"percentPosition": true }\'>';

	foreach ($category_arr as $category_json) {
		if ($category_json['parent_id'] == $parent_id || ($category_json['parent_bro_2_id'] == $parent_id && $parent_id != null)) {
			$html .= $parent_id == null ? '<div class="item_category">' : '<li class="mini_category  col-md-6">';
			
			$new_url_path = $url_path . $category_json['domain'];
			
			$html .= '<a href="' . $new_url_path .'">' . ($category_json['icon_class'] ? '<i class="mdi  '. $category_json['icon_class'] . '"></i>' : '') . $category_json['title_en'] . '</a>';

			// Рекурсивно вызываем функцию для построения подгрупп
			$html .= f_ads_category_tree($category_arr, $category_json['_id'], $new_url_path . '/', $level+1);

			$html .= $parent_id == null ? '</div>' : '</li>';
		}
	}
	
	$html .= $parent_id == null ? '</div>' : '</ul>';
	
	if( count($category_arr) == 0 ){
		$html = '';
	}

	return $html;
}
*/


// Функция для рекурсивного построения списка
function f_ads_category_tree($category_arr, $parent_id=null, $url_path="/category/", $level=1){
	$html = $parent_id == null ? '<div class="list_category">' : '<ul class="sub_category  row"  data-masonry=\'{"percentPosition": true }\'>';

	foreach ($category_arr as $category_json) {
		if ($category_json['parent_id'] == $parent_id || ($category_json['parent_bro_2_id'] == $parent_id && $parent_id != null)) {
			$html .= $parent_id == null ? '<div class="item_category">' : '<li class="mini_category  col-md-6">';
			
			$new_url_path = $url_path . $category_json['domain'];
			
			$html .= '<a href="' . $new_url_path .'">' . ($category_json['icon_class'] ? '<i class="mdi  '. $category_json['icon_class'] . '"></i>' : '') . $category_json['title_en'] . '</a>';

			// Рекурсивно вызываем функцию для построения подгрупп
			$html .= f_ads_category_tree($category_arr, $category_json['_id'], $new_url_path . '/', $level+1);

			$html .= $parent_id == null ? '</div>' : '</li>';
		}
	}
	
	$html .= $parent_id == null ? '</div>' : '</ul>';
	
	if( count($category_arr) == 0 ){
		$html = '';
	}

	return $html;
}


f_page_title_set( "Карта сайта" );

f_page_library_add('masonry_layout'); // <div class="list_category  row"  data-masonry=\'{"percentPosition": true }\'>



?>

<style>

.body_category{
	font-size: var(--v_font_default);
}
.body_category  h1{
	font-size: var(--v_font_h1);
}
.body_category  h2{
	font-size: var(--v_font_h2);
	margin-top: var(--v_p_30);
	margin-bottom: var(--v_p_10);
}

/* для Мобилок */
@media (max-width: 1000px) {
	
	.body_category{
		font-size: var(--v_font_small);
	}
	
	.body_category  h1{
		font-size: var(--v_font_h2);
	}
	.body_category  h2{
		font-size: var(--v_font_h3);
	}
}





.list_category  a{
	text-decoration: none;
	display: block;
	font-size: var(--v_font_small);
	width: max-content;
	max-width: 100%;
}

.list_category  ul {
	list-style-type: none;
	padding-left: 0;
}
.list_category  li{
	padding-top: var(--v_p_5);
}
.list_category  li::marker{
	display: none;
}

.list_category > .item_category{
	margin-top: var(--v_p_40);
}
.list_category > .item_category > a{
	font-size: var(--v_font_h2);
}
.list_category > .item_category > a  i{
	margin-right: var(--v_p_15);
	font-size: var(--v_font_h1);
	line-height: 1;
}
.list_category > .item_category > .sub_category > .mini_category{
	margin-top: var(--v_p_20);
}
.list_category > .item_category > .sub_category > .mini_category >  a{
	font-size: var(--v_font_h4);
	color: var(--v_c_black);
}
.list_category > .item_category > .sub_category > .mini_category >  .sub_category{
	display: block!important;
}
.list_category > .item_category > .sub_category > .mini_category >  .sub_category >  .mini_category{
	width: 100%;!important;
}

</style>


<div class="page_margin_top"></div>

 
<div class="container">
	<div class="body_category">

		<h1>
			<?php f_echo( "Карта сайта" ); ?>
		</h1>
			
		<?php
			//f_echo( json_encode($category_arr) );
			
			f_echo( f_ads_category_tree($category_arr) );
		
		?>

	</div>
</div>
