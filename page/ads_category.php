<?php

//$uri_page = $WEB_JSON["uri_dir_arr"][0]; // ads
$uri_type = $WEB_JSON["uri_dir_arr"][1]; // category
$uri_category = $WEB_JSON["uri_dir_arr"][2]; // transport
$uri_category_sub = $WEB_JSON["uri_dir_arr"][3]; // bus

$is_sitemap = $uri_type == "sitemap";
$is_category = ($is_sitemap == false && $uri_category != '');
$is_category_sub = false;

$link_ads_category = f_page_link('ads_category');
$link_ads_list = f_page_link('ads_list');

$category_parent_id = null;

//$category_arr = [];
$category_arr = f_db_select("SELECT * FROM `ads_category` WHERE `hide_on` = 0 ORDER BY `ads_category`.`sort` IS NULL, `ads_category`.`sort` ASC");

if($is_sitemap || $uri_category == ''){
	//$category_arr = f_db_select_smart("ads_category", ["hide_on" => 0], 1000, 0, ["sort"=>1]);
	
}else if($uri_category != ''){
	$category_json = f_db_select_smart("ads_category", ["hide_on" => 0, "level" => 1, "domain" => $uri_category], 1, 0, ["sort"=>1])[0];
	if( !$category_json ){
		f_redirect( $link_ads_category );
	}
	$category_parent_id = $category_json["_id"];
	
	$is_category_sub = true;
	
	//$category_arr_1 = f_db_select_smart("ads_category", ["hide_on" => 0, "parent_1_id" => $category_json["_id"]], 1000, 0, ["sort"=>1]);
	//$category_arr_2 = f_db_select_smart("ads_category", ["hide_on" => 0, "parent_bro_1_id" => $category_json["_id"]], 1000, 0, ["sort"=>1]);
	//$category_arr_2 = [];
	//$category_arr = array_merge($category_arr_1, $category_arr_2);
	//$category_arr[] = $category_json;
	//$category_arr = f_db_select_smart("ads_category", ["hide_on" => 0], 1000, 0, ["sort"=>1]);
	//$category_arr = f_db_select("SELECT * FROM `ads_category` WHERE `hide_on` = 0 ORDER BY `ads_category`.`sort` IS NULL, `ads_category`.`sort` ASC");
	//f_test($category_arr);
	if($uri_category_sub != ''){
		f_redirect( $link_ads_category . '/' . $uri_category );
	}
}

//$page_title = $is_sitemap ? 'Sitemap' : ($is_category ? 'Ads' : 'Ad categories');
$page_title = $is_sitemap ? 'Ad categories' : ($is_category ? 'Ads' : 'Ad categories');





// Корректировка

/*
// Ищем неправильные prent_bro_1_id
//$arr_data = f_db_select( 'SELECT * FROM `ads_category` where level = 3 and parent_1_id = parent_bro_1_id' );
$arr_data = f_db_select( 'SELECT * FROM `ads_category` where hide_on = 0 and parent_bro_1_id is null and parent_bro_id is not null;' );

// Перебираем
foreach ($arr_data as $item_data) {
	//Ищем их parent_bro_id
	$item_bro_data = f_db_select_smart("ads_category", ["_id" => $item_data['parent_bro_id']])[0];
	
	f_test($item_bro_data);
	
	// Обновляем неправильный item и устанавливаем в parent_bro_1_id = parent_bro['parent_1_id']
	f_db_update_smart( "ads_category", ["_id" => $item_data['_id']], [
		"parent_bro_1_id" => $item_bro_data['parent_1_id']
	]);
}
*/



// Функция для рекурсивного построения списка
function f_ads_category_tree($category_arr, $parent_id=null, $url_path="", $level=1){
	
	$html = $parent_id == null ? '<div class="list_category">' : '<ul class="sub_category  row"  data-masonry=\'{"percentPosition": true }\'>';

	foreach ($category_arr as $category_json) {
		$is_bro = ($category_json['parent_bro_2_id'] == $parent_id && $parent_id != null);
		
		if ($category_json['parent_id'] == $parent_id || $is_bro) {
			$html .= $parent_id == null ? '<div class="item_category" style="--v_tmp_bg: ' . $category_json['color_bg'] . '33">' : '<li class="mini_category  col-md-6">';
			
			/*
			if( $level == 2 ){
				$new_url_path = mb_substr($url_path, 0, -1);
			}else{
				$new_url_path = $url_path . $category_json['domain'];
			}
			*/
			
			$new_url_path = $url_path . $category_json['domain'];
			
			// Поиск домена для category_bro
			if( $is_bro ){
				$parent_bro_1_domain = false;
				$parent_bro_2_domain = false;
				foreach ($category_arr as $category_tmp_json) {
					if( $category_tmp_json['_id'] == $category_json['parent_1_id'] ){
						$parent_bro_1_domain = $category_tmp_json['domain'];
					}
					if( $category_tmp_json['_id'] == $category_json['parent_2_id'] ){
						$parent_bro_2_domain = $category_tmp_json['domain'];
					}
					if($parent_bro_1_domain && $parent_bro_2_domain){
						break;
					}
				}
				$new_url_path = $GLOBALS['link_ads_list'] . '/' . $parent_bro_1_domain . '/' . $parent_bro_2_domain . '/' . $category_json['domain'];
				
			}
			
			$html .= '<a href="' . $new_url_path .'">' . ($category_json['icon_class'] ? '<i class="mdi  '. $category_json['icon_class'] . '"></i>' : '') . '<span>' . $category_json['title_en'] . '</span></a>';

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


/*
// Функция для рекурсивного построения списка
function f_ads_category_tree($category_arr, $parent_id=null, $url_path="", $level=1){
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


f_page_title_set( $page_title );

f_page_library_add('masonry_layout'); // <div class="list_category  row"  data-masonry=\'{"percentPosition": true }\'>



?>

<style>

.body_category{
	font-size: var(--v_font_default);
	margin-bottom: var(--v_p_30);
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
		margin-bottom: 0;
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
	font-size: var(--v_font_default);
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
	display: flex;
	flex-wrap: nowrap;
	align-items: center;
	line-height: 1.2;
}
.list_category > .item_category > a  i{
	margin-right: var(--v_p_15);
	font-size: var(--v_font_h1);
	line-height: 1;
	color: var(--v_c_black);
	background: var(--v_tmp_bg);
	border-radius: 50%;
	padding: var(--v_p_10);
}
.list_category > .item_category > a  span{
	background: var(--v_tmp_bg);
	color: var(--v_c_black);
	padding: var(--v_p_5) var(--v_p_10);
	line-height: 1.2;
	border-radius: var(--v_radius);
}
.list_category > .item_category > .sub_category > .mini_category{
	margin-top: var(--v_p_20);
}
.list_category > .item_category > .sub_category > .mini_category >  a{
	font-size: var(--v_font_h4);
	color: var(--v_c_black);
	border-left: 15px solid var(--v_tmp_bg);
	padding: var(--v_p_5) var(--v_p_10);
	line-height: 1.2;
	border-radius: var(--v_radius);
}
/* для Hover */
@media (hover: hover) {
	.list_category > .item_category > .sub_category > .mini_category >  a:hover{
		background: var(--v_tmp_bg);
	}
}
.list_category > .item_category > .sub_category > .mini_category >  .sub_category{
	display: block!important;
}
.list_category > .item_category > .sub_category > .mini_category >  .sub_category:not(:empty){
	margin-top: var(--v_p_5);
}
.list_category > .item_category > .sub_category > .mini_category >  .sub_category >  .mini_category{
	width: 100%;!important;
	display: flex;
	align-items: center;
	flex-wrap: nowrap;
}

.list_category > .item_category > .sub_category > .mini_category >  .sub_category >  .mini_category::before{
	content: '';
	margin-right: 13px;
	margin-left: 3px;
	height: 8px;
	width: 8px;
	border-radius: 50%;
	background: var(--v_tmp_bg);
}


</style>

<div class="container">
	
	<div class="head_page">
		
		<?php
			$href_back = '/';
			if( $is_category ){
				$href_back = $link_ads_category;
			}
			
		?>
	
		<a class="back_head_page  btn btn-outline-dark"  <?php f_echo_html( $is_sitemap ? 'back_page_link' : '' ); ?>  href="<?php f_echo_html( $href_back ); ?>">
			<i class="bi bi-chevron-left"></i>
		</a>
		
		<h1 class="title_head_page">
			<?php f_translate_echo( $page_title ); ?>
		</h1>
		
	</div>
	
	
	<div class="body_category">
		<?php
			
			$url_path = $link_ads_list."/";
			
			if($is_category_sub){
				echo( '<div class="list_category">' );
				echo( '<div class="item_category" style="--v_tmp_bg: ' . $category_json['color_bg'] . '33">' );
				echo( '<a href="' . $link_ads_list."/".$category_json['domain']  .'" >' . ($category_json['icon_class'] ? '<i class="mdi  '. $category_json['icon_class'] . '"></i>' : ''). '<span>' . $category_json['title_en'] . '</span></a>' );
				$url_path .= $category_json['domain']."/";
			}
			
			f_echo( f_ads_category_tree($category_arr, $category_parent_id, $url_path, ($is_category_sub ? 2 : 1) ) );
			
			if($is_category_sub){
				echo( '</div></div>');
			}
		?>
	</div>
	
</div>
