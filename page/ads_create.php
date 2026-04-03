<?php

$title_page = f_translate('Creating an ad');

f_page_title_set( $title_page );


$user_json = f_user_get();


function f_arr_child_tree($arr=[], $parent_id=null, $key_id="_id", $key_parent="parent_id", $key_arr_child="arr_child") {
    $arr_res = [];
    foreach ($arr as &$item) {
        if ($item[$key_parent] === $parent_id) {
            $arr_child = f_arr_child_tree($arr, $item[$key_id], $key_id, $key_parent, $key_arr_child);
            if ($arr_child) {
                $item[$key_arr_child] = $arr_child;
            }
            $arr_res[$item[$key_id]] = $item;
            unset($item);
        }
    }
    return $arr_res;
}


// Функция для рекурсивного вывода элементов select
function f_arr_child_tree_options($arr, $arr_select_id, $level=0, $dop_text="") {
	$html = '';
    foreach ($arr as $item) {
        $selected = in_array($item['_id'], $arr_select_id) ? 'selected' : '';
        //$padding = str_repeat(' -', $level);
		$padding = '';
        $html .= '<option value="' . f_html($item['_id']) . '" ' . $selected . '>' . $padding . ' ' . $dop_text . '/' .f_html($item['title_en']) . f_html($dop_text) .'</option>';
        if (isset($item['arr_child'])) {
            $html .= f_arr_child_tree_options($item['arr_child'], $arr_select_id, $level + 1, $dop_text.'/'.$item['title_en']);
        }
    }
	return $html;
}




?>

<style>

.item_section{
	background: var(--v_c_white);
	padding: var(--v_p_20);
	margin-bottom: var(--v_p_20);
	border-radius: var(--v_radius);
}

.list_img_ads{
	margin-top: var(--v_p_20);
	margin-bottom: var(--v_p_20);
	display: flex;
	justify-content: flex-start;
	flex-wrap: wrap;
	gap: var(--v_p_15);
}

.item_img_ads{
	width: calc(25% - 45px / 4);
	height: 100px;
	border-radius: var(--v_radius);
	overflow: hidden;
}


/* для Мобилок */
@media (max-width: 1000px) {
	.list_img_ads{
		gap: var(--v_p_10);
	}
	.item_img_ads{
		width: calc(33.33% - 20px / 3);
	}
}

.item_img_ads  img {
	display: block;
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.drop_box {
	border: 2px dashed var(--v_c_border);
	border-radius: var(--v_radius);
	padding: var(--v_p_20);
	text-align: center;
	cursor: pointer;
	user-select: none;
	transition: background-color 0.2s ease; /* Плавное изменение цвета фона */
}

.drop_box.dragover {
	background-color: var(--v_c_black_10); /* Цвет фона при перетаскивании */
}

.drop_box.error {
	border-color: var(--v_c_red); /* Цвет рамки при ошибке */
}

</style>

<div class="container"  form_group="ads_create">
	
	<div class="head_page  mb-3  pb-2">
	
		<a class="back_head_page  btn btn-outline-dark"  href="<?php f_echo_html( $is_admin ? f_page_link('admin_ads_list') : '/');  ?>">
			<i class="bi bi-chevron-left"></i>
		</a>
		
		<h1 class="title_head_page">
			<?php f_echo( $title_page ); ?>
			<?php f_echo_html( $is_admin ? ' - #' . f_num_encode( $item_json['_id'] ) : '') ?>
		</h1>
		
	</div>
	
	<div class="item_section">
		<div class="col-lg-8">
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Title'); ?></label>
				<input type="text" field_name="title" class="form-control" value="<?php f_echo_html( $item_json['title'] ); ?>"  />
			</div>
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Photo'); ?></label>
				<div class="drop_box">
					<div><?php f_translate_echo('Drag and drop the file here or click to select'); ?></div>
				</div>
				<div class="list_img_ads">
					
				</div>
				<input type="file" field_name="photo" accept="image/png, image/jpeg" class="d-none" value=""  />
			</div>
			
			<div class="">
				<label class="form-label  mb-1"><?php f_translate_echo('Category'); ?></label>
				
				<div class="row">
					<div class="col-lg-4  mb-3">
						<select select2  select2_search  field_name="category_id"  select2_sub='[field_name="category_sub_id"]'  select2_sub_other='[field_name="category_sub_sub_id"]'>
							<option value=""><?php f_echo_html( f_translate('Not specified') ); ?></option>
							<?php
								$arr_select_id = explode(',', $item_json['category_id']);
								$arr_tmp = f_db_select_smart("ads_category", ["hide_on" => 0, "level" => 1], 1000, 0, ["sort"=>0]);
								foreach($arr_tmp as&$item){
									$selected = in_array( $item['_id'], $arr_select_id) ? 'selected' : '';
									echo('<option value="'. f_html($item['_id']) .'"  '.$selected.' >'. f_html($item['title_en']) .'</option>');
								}
								
								//f_echo( f_arr_child_tree_options( f_arr_child_tree($arr_tmp), $arr_select_id) );
							?>
						</select>
					</div>
					
					<div class="col-lg-4">
						<select select2  select2_search  field_name="category_sub_id"  select2_sub='[field_name="category_sub_sub_id"]'  class="mb-3  d-none">
							<option value=""><?php f_echo_html( f_translate('Not specified') ); ?></option>
							<?php
								$arr_select_id = explode(',', $item_json['category_sub_id']);
								$arr_tmp = f_db_select_smart("ads_category", ["hide_on" => 0, "level" => 2], 1000, 0, ["sort"=>0]);
								foreach($arr_tmp as&$item){
									$selected = in_array( $item['_id'], $arr_select_id) ? 'selected' : '';
									echo('<option value="'. f_html($item['_id']) .'"  parent_id="'. f_html($item['parent_id']) .'"  '.$selected.' >'. f_html($item['title_en']) .'</option>');
								}
							?>
						</select>
					</div>
					
					<div class="col-lg-4">
						<select select2  select2_search  field_name="category_sub_sub_id"  class="mb-3  d-none">
							<option value=""><?php f_echo_html( f_translate('Not specified') ); ?></option>
							<?php
								$arr_select_id = explode(',', $item_json['category_sub_sub_id']);
								$arr_tmp = f_db_select_smart("ads_category", ["hide_on" => 0, "level" => 3], 1000, 0, ["sort"=>0]);
								foreach($arr_tmp as&$item){
									$selected = in_array( $item['_id'], $arr_select_id) ? 'selected' : '';
									echo('<option value="'. f_html($item['_id']) .'"  parent_id="'. f_html($item['parent_id']) .'"  '.$selected.' >'. f_html($item['title_en']) .'</option>');
								}
							?>
						</select>
					</div>
					
				</div>
			</div>
		
			<div class="">
				<label class="form-label  mb-1"><?php f_translate_echo('Price'); ?></label>
				<div class="row">
					<div class="col-4  mb-3">
						<input type="text" field_name="price" inputmask="number" class="form-control" value="<?php f_echo_html( $item_json['price'] ); ?>"  />
					</div>
					<div class="col-2  mb-3">
						<select class="form-control" disabled>
							<option><?php f_echo_html( $item_json['price_currency'] ?: f_page_currency() ); ?></option>
						</select>
					</div>
					<div class="col-6">
					</div>
				</div>
			</div>
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Description'); ?></label>
				<textarea field_name="description" class="form-control" style="min-height: 250px; max-height: 500px;" value="<?php f_echo_html( $item_json['description'] ); ?>" ></textarea>
			</div>
			
		</div>
	</div>
	
	<div class="item_section">
		<div class="col-lg-4">
			
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Location'); ?></label>
				
				<select select2  select2_search  field_name="city_type_id">
					<option value=""><?php f_echo_html( f_translate('Not specified') ); ?></option>
					<?php
						$arr_select_id = explode(',', $item_json['city_type_id'] ?: $user_json['city_type_id']);
						$arr_tmp = f_db_select_smart('city', [], 100);
						foreach($arr_tmp as&$item){
							$selected = in_array( $item['_id'], $arr_select_id) ? 'selected' : '';
							echo('<option value="'. f_html($item['_id']) .'"  '.$selected.' >'. f_html($item['title_en']) .'</option>');
						}
					?>
				</select>
			</div>
			
		</div>
	</div>
	
	<div class="item_section">
		<div class="col-lg-4">
			
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Contact Name'); ?></label>
				<input type="text" field_name="user_name" class="form-control" value="<?php f_echo_html( $item_json['user_name'] ?: $user_json['name'] ); ?>"  />
			</div>
			
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Contact Phone number'); ?></label>
				<select class="form-control" disabled>
					<option><?php f_echo_html( $item_json['user_phone'] ?: $user_json['html_phone'] ); ?></option>
				</select>
			</div>
			
		</div>
	</div>
	
	<div class="item_section">
		<div class="row">
			<div class="col-md-6"></div>
			<div class="col-md-6  d-flex  gap-3">
				<div class="btn btn-outline-dark  py-2  w-100"><?php f_translate_echo('Preview'); ?></div>
				<div class="btn btn-primary  py-2  w-100"  field_btn="save" ><?php f_translate_echo('Publish'); ?></div>
			</div>
		</div>
	</div>
	
</div>


<script>

document.addEventListener("DOMContentLoaded", function(event){
	
	let jq_category_id = $('[field_name="category_id"]')
	let jq_category_sub_id = $('[field_name="category_sub_id"]')
	let jq_category_sub_sub_id = $('[field_name="category_sub_sub_id"]')
	
	
	
	
	let jq_btn_save = $('[field_btn="save"]');
	
	let jq_drop_box = $(".drop_box");
	let jq_input_photo = $('[field_name="photo"]');
	let jq_list_img_ads = $('.list_img_ads');
	

	jq_btn_save.on('click', function(){
		
		jq_btn_save.addClass('btn_sending')
		
		// Собираем данные
		let form_json = f_form_get( $('[form_group="ads_create"]') );
		
		f_ajax('ads', 'save', form_json, function(data){
			//console.log('return', data);
			jq_btn_save.removeClass('btn_sending')
			if( data['data']['error'] ){
				toastr.error( data['data']['error'] );
			}else{
				//toastr.success( "<?php f_echo_html( f_translate('Сохранено') ); ?>" );
				location.reload()
			}
		})
		
	});
	
	
	
	
	jq_drop_box.on("dragover", function(e) {
		e.preventDefault();
		e.stopPropagation();
		jq_drop_box.addClass("dragover");
	});

	jq_drop_box.on("dragleave", function(e) {
		e.preventDefault();
		e.stopPropagation();
		jq_drop_box.removeClass("dragover");
	});

	jq_drop_box.on("drop", function(e) {
		e.preventDefault();
		e.stopPropagation();
		jq_drop_box.removeClass("dragover");

		let files = e.originalEvent.dataTransfer.files;
		jq_input_photo.prop('files', files);
		jq_input_photo.trigger('change');
	});


	jq_drop_box.on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		jq_input_photo.click();
	});

	jq_input_photo.on('change', function(){
		
		let file = this.files[0];
		
		if (!['image/jpeg', 'image/png'].includes(file['type'])) {
			jq_drop_box.addClass("error");
			return;
		}
		
		jq_input_photo.prop('disabled', true);
		
		jq_drop_box.removeClass("error");
		
		// Собираем данные
		let form_json = {
			//'file': $('[form_group="ads_create"]').find(),
			'item_id': 1,
			'item_table': 'ads',
			'item_type': 'image',
			'file': file,
		};
		
		f_ajax('upload', 'file', form_json, function(data){
			console.log('return', data);
			jq_input_photo.val('');
			jq_input_photo.prop('disabled', false);
			if( data['data']['error'] ){
				toastr.error( data['data']['error'] );
			}else{
				console.log('success');
				
				let jq_tmp_img = $('<div class="item_img_ads"><img src=""></img></div>');
				jq_tmp_img.attr('data-fancybox', 'gallery')
				jq_tmp_img.attr('data-src', data['data']['link_jpg'])
				jq_tmp_img.find('img').attr('src', data['data']['link_jpg'])
				
				jq_list_img_ads.append( jq_tmp_img );
				//toastr.success( "<?php f_echo_html( f_translate('Сохранено') ); ?>" );
				//location.reload()
			}
		})
		
	});
	


})

</script>













