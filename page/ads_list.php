<?php

//$uri_page = $WEB_JSON["uri_dir_arr"][0]; // ads
//$uri_type = $WEB_JSON["uri_dir_arr"][1]; // list
$uri_category_1 = $WEB_JSON["uri_dir_arr"][2]; // transport
$uri_category_2 = $WEB_JSON["uri_dir_arr"][3]; // services
$uri_category_3 = $WEB_JSON["uri_dir_arr"][4]; // rubbish-collection

//f_test( $WEB_JSON["uri_dir_arr"] );

$is_category_1 = $uri_category_1 != '';
$is_category_2 = $uri_category_2 != '';
$is_category_3 = $uri_category_3 != '';

$category_1_json = $uri_category_1 == false ? false : f_db_select_smart("ads_category", ["hide_on" => 0, "level" => 1, "domain" => $uri_category_1], 1)[0];
$category_2_json = $uri_category_2 == false ? false : f_db_select_smart("ads_category", ["hide_on" => 0, "level" => 2, "domain" => $uri_category_2], 1)[0];
$category_3_json = $uri_category_3 == false ? false : f_db_select_smart("ads_category", ["hide_on" => 0, "level" => 3, "domain" => $uri_category_3], 1)[0];

$link_ads_category = f_page_link('ads_category');
$link_ads_list = f_page_link('ads_list');

//$where_ads_param_category = [1];
$where_ads_param_category = ["`ads_param_category`.`hide_on` = 0"];

$page_title = 'All ads';

$category_end_json = [];

$arr_breadcump = [['title'=>'All ads', 'domain'=>'']];

if( $category_1_json == false ){ $where_ads_param_category[] = "`ads_param_key`.`_id` = 2"; } // показываем только Цену если категории нет
if( $category_1_json ){$category_end_json = $category_1_json; $where_ads_param_category[] = "`ads_param_category`.`ads_category_1_id` = " . $category_1_json['_id']; $arr_breadcump[] = ['title'=>$category_1_json['title_en'], 'domain'=>$category_1_json['domain']]; $page_title = $category_1_json['title_en'];}
if( $category_2_json ){$category_end_json = $category_2_json; $where_ads_param_category[] = "`ads_param_category`.`ads_category_2_id` = " . $category_2_json['_id']; $arr_breadcump[] = ['title'=>$category_2_json['title_en'], 'domain'=>$category_2_json['domain']]; $page_title = $category_2_json['title_en'];}
if( $category_3_json ){$category_end_json = $category_3_json; $where_ads_param_category[] = "`ads_param_category`.`ads_category_3_id` = " . $category_3_json['_id']; $arr_breadcump[] = ['title'=>$category_3_json['title_en'], 'domain'=>$category_3_json['domain']]; $page_title = $category_3_json['title_en'];}

// Если без категории
//if( $uri_category_1 == '' ){$arr_breadcump[] = ['title'=>'All ads', 'domain'=>'']; $page_title = 'All ads';}

//f_test($arr_breadcump);



/*
	Задача - Отобразить список фильтров исходя из категорий
		+ Определить ID категории
		- Найти все фильтры по этой категории
			- составить SQL с JOIN
		- Отобразить эти фильтры 

SELECT * FROM `ads_param_category` where ads_category_1_domain like '%\%%' OR ads_category_3_domain like '%\%%';

update `ads_param_category` set ads_category_2_domain = NULL, ads_category_1_id = NULL, ads_category_2_id = NULL, ads_category_3_id = NULL, ads_category_id = NULL


UPDATE ads_param_category
	JOIN ads_category
		ON
			ads_param_category.ads_category_1_domain = ads_category.tmp_parent_1_domain
			AND
			ads_param_category.ads_category_3_domain = ads_category.tmp_parent_3_domain
	SET
		ads_param_category.ads_category_2_domain = ads_category.tmp_parent_2_domain
	WHERE
		ads_param_category.ads_category_1_domain = ads_category.tmp_parent_1_domain
		AND 
		ads_param_category.ads_category_3_domain = ads_category.tmp_parent_3_domain



UPDATE ads_param_category
	JOIN ads_category
		ON
			ads_param_category.ads_category_1_domain = ads_category.tmp_parent_1_domain
			AND
			ads_param_category.ads_category_3_domain = ads_category.tmp_parent_3_domain
	SET
		ads_param_category.ads_category_1_id = ads_category.parent_1_id,
		ads_param_category.ads_category_2_id = ads_category.parent_2_id,
		ads_param_category.ads_category_3_id = ads_category.parent_3_id,
		ads_param_category.ads_category_id = ads_category.parent_3_id
	WHERE
		ads_param_category.ads_category_1_domain = ads_category.tmp_parent_1_domain
		AND 
		ads_param_category.ads_category_3_domain = ads_category.tmp_parent_3_domain


SELECT
	`ads_param_key`.* 
FROM
	`ads_param_category`
	
RIGHT JOIN `ads_param_key`
	ON
	`ads_param_key`.`_id` = `ads_param_category`.`ads_param_key_id`
	
WHERE
	`ads_param_category`.`ads_category_1_id` = 1
	AND
	`ads_param_category`.`ads_category_2_id` = 26
	AND
	`ads_param_category`.`ads_category_3_id` = 201



UPDATE `ads_param_key` SET `form_type` = 'range' WHERE `form_type` = 'checkbox' and title_ru like '%Количество%';

//

SELECT
    `ads_param_key`.* 
FROM
    `ads_param_category`
    
RIGHT JOIN `ads_param_key`
    ON
    `ads_param_key`.`_id` = `ads_param_category`.`ads_param_key_id`
    
WHERE
    `ads_param_category`.`ads_category_1_id` = 1
    AND
    `ads_param_category`.`ads_category_3_id` = 201;
 
*/

// Поиск фильтров по выбранной категории
//f_test( f_db_select_smart("ads_param_category", $where_ads_param_category, 2000) );






$sql_filters = "
	SELECT
		DISTINCT `ads_param_key`.* 
	FROM
		`ads_param_category`
		
	JOIN `ads_param_key`
		ON
		`ads_param_key`.`_id` = `ads_param_category`.`ads_param_key_id`
		
	WHERE
		1 AND ". implode(' AND ', $where_ads_param_category) ."
	ORDER BY
		`ads_param_key`.`sort` ASC,
		`ads_param_key`.`form_type` DESC,
		`ads_param_key`.`title_en` ASC
";

//f_test( $sql_filters );

$tmp_filters = f_db_select( $sql_filters );
$arr_filters = [];

/*
for($i=0; $i < count( $tmp_filters ); $i++){
	$json_filter = $tmp_filters[$i];
	
	$json_filter['title_en'] = $json_filter['title_en'] ?: $json_filter['title_ru'];
	
	if( $json_filter['form_type'] == 'checkbox' ){
		
		$json_filter['json_checkbox'] = [];
		
		$arr_checkbox = f_db_select( "SELECT `_id`, `title_en`, `title_ru`, '". $json_filter['_id'] ."' AS 'ads_param_key_id' FROM `ads_param_value` WHERE `ads_param_key_id` = " . $json_filter['_id'] . " ORDER BY `title_en` ASC" ) ?: [];	
		
		if( count( $arr_checkbox ) > 0 ){
			$last_filter = end($arr_filters);
			
			if( $last_filter['title_en'] == $json_filter['title_en'] ){
				// Добавляем в последний фильтр дополнительные опции (потому что сортировка по имени, что значит прошлый элемент скорее всего с таким же названием)
				$index_last_filter = count($arr_filters) - 1;
				
				foreach($arr_checkbox as $item_checkbox){
					if( isset($last_filter['json_checkbox'][$item_checkbox['title_en']]) ){
						$arr_filters[$index_last_filter]['json_checkbox'][$item_checkbox['title_en']]['ads_param_key_id'] .= ',' . $item_checkbox['_id'];
					}else{
						$arr_filters[$index_last_filter]['json_checkbox'][$item_checkbox['title_en']] = $item_checkbox;
					}
				}
				//$arr_filters[ count($arr_filters) - 1 ]['arr_checkbox'] = array_merge( $last_filter['arr_checkbox'], $json_filter['arr_checkbox'] );
				
			}else{
				$arr_filters[] = $json_filter;
			}
			
		}
		
	}else if( $json_filter['form_type'] == 'range' ){
		$arr_filters[] = $json_filter;
		
	}
}
*/


for($i=0; $i < count( $tmp_filters ); $i++){
	$json_filter = $tmp_filters[$i];
	
	$json_filter['title_en'] = $json_filter['title_en'] ?: $json_filter['title_ru'];
	
	if( $json_filter['form_type'] == 'checkbox' ){
		
		$json_filter['arr_checkbox'] = f_db_select( "SELECT `_id`, `title_en`, `title_ru`, `parent_domain`, '". $json_filter['_id'] ."' AS 'ads_param_key_id' FROM `ads_param_value` WHERE `ads_param_key_id` = " . $json_filter['_id'] . " ORDER BY `sort` ASC, `title_en` ASC" ) ?: [];	
		
		if( count( $json_filter['arr_checkbox'] ) > 0 ){
			$last_filter = end($arr_filters);
			
			//$arr_filters[] = $json_filter;
			
			if( $last_filter['title_en'] == $json_filter['title_en'] ){
				// Добавляем в последний фильтр дополнительные опции (потому что сортировка по имени, что значит прошлый элемент скорее всего с таким же названием)
				//$arr_filters[ count($arr_filters) - 1 ]['arr_checkbox'] = array_merge( $last_filter['arr_checkbox'], $json_filter['arr_checkbox'] );
				
				//f_test( $arr_filters[ count($arr_filters) - 1 ] );
			}else{
				$arr_filters[] = $json_filter;
			}
			
		}
		
	}else if( $json_filter['form_type'] == 'range' ){
		$arr_filters[] = $json_filter;
		
	}
}


//f_test( $arr_filters );



f_page_title_set( $page_title );

?>

<style>

</style>

<div class="container">
	
	
	<?php f_page_breadcump( $arr_breadcump, f_page_link('ads_list') ); ?>
	
	<div class="head_page  mb-3  pb-2">
	
		<a class="back_head_page  btn btn-outline-dark"  back_page_link  href="/">
			<i class="bi bi-chevron-left"></i>
		</a>
		
		<h1 class="title_head_page">
			<?php f_translate_echo( $page_title ); ?>
		</h1>
		
	</div>
	
	
	<style>
	
	.box_split_list_ads{
		display: flex;
		flex-wrap: nowrap;
		gap: var(--v_p_20);
	}
	
	.list_box_split_list_ads{
		width: calc(100% - var(--v_p_20) - 300px);
		flex-shrink: 1;
	}
	
	.filter_box_split_list_ads{
		position: sticky;
		top: calc(var(--v_p_20) + var(--v_navbar_height));
		width: 300px;
		
		height: max-content;
		/*height: 2000px;*/
		max-height: calc(100vh - var(--v_p_40) - var(--v_navbar_height));
		
		padding: var(--v_p_15);
		
		flex-shrink: 0;
		color: var(--v_c_black);
		background: var(--v_c_white);
		border: 1px solid var(--v_c_border);
		border-radius: var(--v_radius);
	}
	
	/* для Мобилок */
	@media (max-width: 1000px) {
		.search_section {
			display: none;
		}
		
		.box_split_list_ads{
			gap: 0px;
			display: block;
		}
		
		.filter_box_split_list_ads{
			display: none;
		}
		.list_box_split_list_ads{
			width: 100%;
		}
	}
	
	</style>
	
	
	<div class="box_split_list_ads">
	
		<div class="list_box_split_list_ads">
		
			<div class="search_section  pt-0  mb-4">
				<?php f_template('box_search'); ?>
			</div>
			

			<?php f_template('ads_swiper_top'); ?>
			
			
			<h2 class="sub_title_head_page  mt-4  mb-2">
				<?php f_translate_echo( 'We found 20 ads' ); ?>
			</h2>
			<div class="list_line_ads"  ads_list_type="line" ads_list_query="recomendation" ads_list_category_id="<?php f_echo_html( $category_end_json['_id'] ); ?>">
				<?php
					for($i=0; $i<20; $i++){
				?>
					<div class="item_ad">
						<a href="#" class="body_item_ad">
							
							<img class="img_item_ad" src="/public/ad_default.jpg">
							<div class="text_item_ad">
								<div class="d-flex  justify-content-between">
									<div class="title_item_ad">
										I will sell a new Luxury segment car directly from the salon
									</div>
									<div class="btn_favorite_item_ad   bi bi-heart"></div>
								</div>
								<div class="price_item_ad">
									20 000 $
								</div>
								<div class="city_item_ad">
									London
								</div>
								<div class="date_item_ad">
									Today
									<!--21 июля 2024 г.-->
								</div>
							</div>
						</a>
					</div>
				<?php
					}
				?>
			</div>
			
		</div>

<script>



</script>



<style>

.param_group{
	margin-top: var(--v_p_10);
}

.param_name{
	font-size: var(--v_font_small);
	margin-bottom: var(--v_p_5);
	font-weight: 600;
}

.param_group  .input-group  .input-group-text{
	border: 1px solid var(--v_c_black_50);
	border-right: none;
}

.param_group  .form-check{
	user-select: none;
}

.param_group  .input-group  input,
.param_group  .input-check  input{
	border: 1px solid var(--v_c_black_50);
	font-size: var(--v_font_small);
	padding-top: 8px;
	padding-bottom: 8px;
}


.param_group  .input-group  input:focus,
.param_group  .input-check  input:focus{
	border: 1px solid var(--v_c_black);
}
.param_group  .input-group  input:not(:placeholder-shown){
	border-color: var(--v_c_blue);
}

.filter_box_split_list_ads  .sub_title_head_page{
	position: sticky;
	top: 0;
	font-weight: bold;
	background: var(--v_c_white);
	z-index: 102;
	margin: calc(var(--v_p_15)* -1);
	margin-bottom: var(--v_p_15);
	padding: var(--v_p_15);
	/*top: calc(var(--v_p_15)* -1); */
	border-bottom: 1px solid var(--v_c_border);
	border-radius: var(--v_radius) var(--v_radius) 0px 0px;
	
	display: flex;
	align-items: center;
	flex-wrap: nowrap;
}

.btn_close_filter_box_split_list_ads{
	display: none;
	margin-left: auto;
	padding: 0 var(--v_p_10);
	margin-right: calc(-1* var(--v_p_10));
}

.search_filter_box_split_list_ads{
	margin-bottom: var(--v_p_30);
	display: none;
}

/* для Мобилок */
@media (max-width: 1000px) {
	.show_filter_box_split_list_ads  .filter_box_split_list_ads{
		display: block;
		/*
		height: calc(100vh - var(--v_navbar_height) - var(--v_navbar_height));
		height: calc(100dvh - var(--v_navbar_height) - var(--v_navbar_height));
		top: var(--v_navbar_height);
		*/
		
		top: 0;
		height: 100vh;
		height: 100dvh;
		
		bottom: var(--v_navbar_height);
		max-height: unset;
		max-width: unset;
		width: 100vw;
		width: 100dvw;
		
		position: fixed;
		padding-bottom: var(--v_p_40);
		left: 0;
		
		z-index: 1020;
	}
	
	.show_filter_box_split_list_ads  .filter_box_split_list_ads  .sub_title_head_page{
		font-size: var(--v_font_h2);
		height: var(--v_navbar_height);
		border-radius: 0;
	}
	
	.btn_close_filter_box_split_list_ads{
		display: block;
	}
	
	.search_filter_box_split_list_ads{
		display: block;
	}
}

</style>
		
		<div class="filter_box_split_list_ads   simplebar-scrollable-y  <?php f_echo_html( count($arr_filters) == 0 ? 'd-none' : '' ); ?>"  data-simplebar   >
			<div class="sub_title_head_page">
				<div><?php f_translate_echo( 'Filters' ); ?></div>
				<div class="btn_close_filter_box_split_list_ads"><i class="bi bi-x-lg"></i></div>
			</div>
			
			<div class="search_filter_box_split_list_ads">
				<?php f_template('box_search'); ?>
			</div>
			
			<div class="list_param_filter_box_split_list_ads">
			
				<?php
					$json_ads_filter_list_query = json_decode( $_GET['ads_list_filter'], true ) ?? [];
					
					foreach($arr_filters as $json_filter){
						//
						if( $json_filter['form_type'] == 'range' ){
							$val_min = $json_ads_filter_list_query[ $json_filter['_id'] ]['min'];
							$val_max = $json_ads_filter_list_query[ $json_filter['_id'] ]['max'];
				?>
							<div class="param_group">
								<div class="param_name">
									<?php
										f_echo( $json_filter['title_en'] );
										
										if( isset($json_filter['unit_en']) ){
									?>
											<span class="ms-1  text-muted">(<?php f_echo( $json_filter['unit_en'] ); ?>)</span>
									<?php
										}
									?>
								</div>
								<div class="input-group">
									<input type="number" class="form-control" min="0" max="10000000000" filter_type="min" filter_id="<?php f_echo( $json_filter['_id'] ); ?>" value="<?php f_echo_html( $val_min ); ?>" placeholder="<?php f_translate_echo( "min" ); ?>">
									<input type="number" class="form-control" min="0" max="10000000000" filter_type="max" filter_id="<?php f_echo( $json_filter['_id'] ); ?>" value="<?php f_echo_html( $val_max ); ?>" placeholder="<?php f_translate_echo( "max" ); ?>">
								</div>
							</div>
				<?php
						}
						//
						if( $json_filter['form_type'] == 'checkbox' ){
							$val_arr = $json_ads_filter_list_query[ $json_filter['_id'] ]
				?>
							<div class="param_group">
								<div class="param_name"><?php f_echo( $json_filter['title_en'] ); ?></div>
				<?php
								//f_test( $json_filter['json_checkbox'] );
								//foreach($json_filter['json_checkbox'] as $key => $json_checkbox){
								if( count($json_filter['arr_checkbox']) < 5 ){
									foreach($json_filter['arr_checkbox'] as $json_checkbox){
										$json_checkbox['title_en'] = $json_checkbox['title_en'] ?: $json_checkbox['title_ru'];
										
										$html_is_checked = in_array( $json_checkbox['_id'], $val_arr) ? 'checked' : '';
				?>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" filter_name="category" filter_id="<?php f_echo( $json_checkbox['ads_param_key_id'] ); ?>" filter_key_id="<?php f_echo( $json_checkbox['ads_param_key_id'] ); ?>" id="checkbox_<?php f_echo( $json_checkbox['_id'] ); ?>"  <?php f_echo( $html_is_checked ); ?> value="<?php f_echo( $json_checkbox['_id'] ); ?>" parent="">
											<label class="form-check-label" for="checkbox_<?php f_echo( $json_checkbox['_id'] ); ?>"><?php f_echo( $json_checkbox['title_en'] ); ?></label>
										</div>
				<?php
									}
								}else{
									$arr_select_id = $json_ads_filter_list_query[ $json_filter['_id'] ];
									$arr_select_id = gettype($arr_select_id) == 'array' ? $arr_select_id : [$arr_select_id];
									
									
									//$attr_select2_sub =  $json_filter['_id'] == 60 ? " select2_sub='#select_ads_filter_882' " : "";
									$attr_select2_sub = $json_filter['select_parent_id'] == null ? '' : " select2_sub='#select_ads_filter_".$json_filter['select_parent_id']."' ";
				?>
								<select select2  select2_search  select2_parent=".filter_box_split_list_ads"  multiple <?php f_echo_html( $attr_select2_sub ); ?>  filter_id="<?php f_echo_html( $json_filter['_id'] ); ?>"  id="select_ads_filter_<?php f_echo_html( $json_filter['_id'] ); ?>">
									<!--<option value="" <?php f_echo_html( $arr_select_id[0] == '' ? 'selected' : '' ); ?> ><?php f_echo_html( f_translate('All') ); ?></option>-->
				<?php
									foreach($json_filter['arr_checkbox'] as&$json_checkbox){
										$json_checkbox['title_en'] = $json_checkbox['title_en'] ?: $json_checkbox['title_ru'];
										$selected = in_array( $json_checkbox['_id'], $arr_select_id) ? 'selected' : '';
										$parent_domain = $json_checkbox['parent_domain'] ? (' parent_domain="'. $json_checkbox['parent_domain'] .'" ') : '';
										echo('<option value="'. f_html($json_checkbox['_id']) .'"  '.$selected . $parent_domain .' >'. f_html($json_checkbox['title_en']) .'</option>');
									}
								}
				?>
								</select>
							</div>
				<?php
						}
					}
				?>
			</div>
		</div>
		
	</div>


	
</div>

