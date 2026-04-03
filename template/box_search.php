
<div class="box_search"> 
	<div class="input_search">
		<i class="bi bi-search"></i>
		<input  id="input_ads_search_title"  placeholder="<?php f_translate_echo('Search'); ?>"  value="<?php f_echo_html( $_GET['ads_search_title'] ); ?>">
	</div>
	
	<div class="input_search  select_search">
		<i class="bi bi-geo-alt"></i>
		
		<select select2  select2_search  select2_parent=".search_section  .select_search"  id="select_ads_search_city">
			<option value=""><?php f_echo_html( f_translate('All UK') ); ?></option>
			<?php
				$arr_select_id = explode(',', $_GET['ads_search_city_id']);
				$arr_tmp = f_db_select_smart('city', [], 100);
				foreach($arr_tmp as&$item){
					$selected = in_array( $item['_id'], $arr_select_id) ? 'selected' : '';
					echo('<option value="'. f_html($item['_id']) .'"  '.$selected.' >'. f_html($item['title_en']) .'</option>');
				}
			?>
		</select>
	</div>
	
	<div class="btn_search  btn btn-dark  btn-lg"  id="btn_ads_search_find">
		<i class="bi bi-search  me-3"></i>
		<?php f_translate_echo('Find'); ?>
	</div>
</div>



<script>

document.addEventListener("DOMContentLoaded", function(event){
	let jq_search_btn_find = $('#btn_ads_search_find');
	
	let jq_search_title = $('#input_ads_search_title');
	let jq_search_city = $('#select_ads_search_city');
	//let jq_search_category = $('#input_ads_search_category');
	
	let uri_search_list = "<?php f_echo( f_page_link('ads_list') ); ?>";
	
	if( location.pathname.includes(uri_search_list) ){
		uri_search_list = location.pathname; // так как содежит категории
	}
	
	// GET данные
	//let get_search_title = "<?php f_echo( addslashes( $_GET['ads_search_title'] ?: '' ) ); ?>";
	//let get_ads_search_city_id = "<?php f_echo( addslashes( $_GET['ads_search_city_id'] ?: '' ) ); ?>";
	//let get_ads_search_category = "<?php f_echo( addslashes( $_GET['ads_search_category'] ?: '' ) ); ?>";
	
	// Установка GET данных
	//jq_search_title.val( get_search_title );
	//jq_search_city.val( get_ads_search_city_id );
	//jq_search_category.val( get_ads_search_category );
	
	jq_search_btn_find.on('click', function(){
		let json_uri_get = f_url_query_to_json();
		
		json_uri_get['ads_search_title'] = jq_search_title.val().trim() || '';
		json_uri_get['ads_search_city_id'] = jq_search_city.val() || '';
		//json_uri_get['ads_search_category'] = jq_search_category.val() || '';
		
		window.location.href = uri_search_list + '/' + f_url_json_to_query(json_uri_get);
	})
});

</script>
