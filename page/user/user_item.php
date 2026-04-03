<?php


$page_json = [
	'user_type' => f_user_get()['user_type'] ?? f_user_get()['type'],
	'type' => false,
	'status' => 0,
	'alert' => '',
];

/*
// Поиск данных по _ID_STR
$_id_str = $WEB_JSON["uri_dir_arr"][1];
$_id = f_num_decode( $_id_str );

$item_json = f_db_get_user(['_id' => $_id]);

*/


$item_json = f_user_get();

$eff_profile = trim((string)(f_user_get()['user_type'] ?? '')) !== '' ? trim(f_user_get()['user_type']) : (string)f_user_get()['type'];
$is_admin  = $eff_profile === 'admin';
$is_business  = $eff_profile === 'business';
$is_user  = $eff_profile === 'user';

$disabled_if_not_admin = $is_admin ? '' : 'disabled';

$store_row = null;
if( $is_business && f_db_table_exists('store') ){
	$sr = f_db_select(
		'SELECT * FROM `store` WHERE `user_id` = ' . intval($item_json['_id']) . ' LIMIT 1'
	);
	if( !empty($sr) ){
		$store_row = $sr[0];
	}
}


/*
if( !isset($item_json) || ( $item_json['_id'] != f_user_get()['_id'] && !$is_admin)){
	if($is_admin){
		f_redirect('/user/list');
	}else{
		f_redirect('/');
	}
}

if( !$is_admin && $_id != f_user_get()['_id'] ){
	f_redirect('/');
}
*/

$title_page = f_translate('User Settings');

f_page_title_set( $title_page );

?>


<div class="container">
	
	<?php f_template('user_info_box'); ?>
			
	<div class="row" form_group="user">
		
		<div class="col-md-4">
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Name'); ?></label>
				<input type="text" field_name="name" class="form-control" value="<?php f_echo_html( $item_json['name'] ); ?>" />
			</div>
			
			<?php
		
			
			/*
			
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Пол'); ?></label>
				<select class="form-select" field_name="gender">
					<option value="0" <?php f_echo_html( $item_json['gender'] == 0 ? 'selected' : '' ); ?>><?php f_translate_echo('Не указан'); ?></option>
					<option value="1" <?php f_echo_html( $item_json['gender'] == 1 ? 'selected' : '' ); ?>><?php f_translate_echo('Мужской'); ?></option>
					<option value="2" <?php f_echo_html( $item_json['gender'] == 2 ? 'selected' : '' ); ?>><?php f_translate_echo('Женский'); ?></option>
				</select>
			</div>
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Дата рождения'); ?></label>
				<input type="date" field_name="birthday_date" class="form-control" value="<?php f_echo_html( $item_json['birthday_date'] ); ?>" />
			</div>
			
			
			*/
				
			?>
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Phone'); ?></label>
				<input type="text" field_name="phone" inputmask="phone" class="form-control" value="<?php f_echo_html( $item_json['phone'] ); ?>"  />
			</div>
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Email'); ?></label>
				<input type="text" field_name="email" class="form-control" value="<?php f_echo_html( $item_json['email'] ); ?>"  <?php f_echo_html( $disabled_if_not_admin ); ?>  />
			</div>
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Password'); ?></label>
				<input type="text" field_name="password" class="form-control" value="" />
			</div>
			
		</div>
		
		
		<div class="col-md-4">
			
			<?php
				if( $is_user == false ){ 
			?>
				<div class="mb-3">
					<label class="form-label  mb-1"><?php f_translate_echo('Login'); ?></label>
					<input type="text" field_name="login" class="form-control" value="<?php f_echo_html( $item_json['login'] ); ?>" />
				</div>
		
				<div class="mb-3">
					<label class="form-label  mb-1"><?php f_translate_echo('Description'); ?></label>
					<textarea rows=4 field_name="description" class="form-control" ><?php f_echo_html( $item_json['description'] ); ?></textarea>
				</div>
			<?php
				}
			?>
			
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('City'); ?></label>
				
				<select select2  select2_search  field_name="city_type_id">
					<option value=""><?php f_echo_html( f_translate('Not specified') ); ?></option>
					<?php
						$arr_select_id = explode(',', $item_json['city_type_id']);
						$arr_tmp = f_db_select_smart('city', [], 100);
						foreach($arr_tmp as&$item){
							$selected = in_array( $item['_id'], $arr_select_id) ? 'selected' : '';
							echo('<option value="'. f_html($item['_id']) .'"  '.$selected.' >'. f_html($item['title_en']) .'</option>');
						}
					?>
				</select>
			</div>
			
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_translate_echo('Address'); ?></label>
				<input type="text" field_name="address" class="form-control" value="<?php f_echo_html( $item_json['address'] ); ?>" />
			</div>
			
		</div>
		
		
		<div class="col-md-4">
		
			<div class="mb-3">
				<label class="form-label  mb-1"><?php f_echo('ID'); ?></label>
				<input type="text" field_name="_id_str" class="form-control" value="<?php f_echo_html( f_num_encode($item_json['_id']) ); ?>"   disabled />
			</div>
				
			<?php
				if( $is_admin ){ 
			?>
			
				<div class="mb-3">
					<label class="form-label  mb-1"><?php f_translate_echo('Date of registration'); ?></label>
					<input type="datetime-local" field_name="_create_date" class="form-control" value="<?php f_echo_html( $item_json['_create_date'] ); ?>"  <?php f_echo_html( $disabled_if_not_admin ); ?>  />
					<div class='form-text  small text-muted'><?php f_echo_html( $item_json['html_create_date'] ); ?></div>
				</div>
					
				<?php $acc_type = trim((string)($item_json['user_type'] ?? '')) !== '' ? trim($item_json['user_type']) : (string)$item_json['type']; ?>
				<div class="mb-3">
					<label class="form-label  mb-1"><?php f_translate_echo('Account Type'); ?></label>
					<select class="form-select" field_name="user_type"    <?php f_echo_html( $disabled_if_not_admin ); ?>>
						<option value="user" <?php f_echo_html( $acc_type === 'user' ? 'selected' : '' ); ?>><?php f_translate_echo('User'); ?></option>
						<option value="business" <?php f_echo_html( $acc_type === 'business' ? 'selected' : '' ); ?>><?php f_translate_echo('Business'); ?></option>
						<option value="moderator" <?php f_echo_html( $acc_type === 'moderator' ? 'selected' : '' ); ?>><?php f_translate_echo('Moderator'); ?></option>
						<?php if( $is_admin ) { ?>
							<option value="admin" <?php f_echo_html( $acc_type === 'admin' ? 'selected' : '' ); ?>><?php f_translate_echo('Administrator'); ?></option>
						<?php } ?>
					</select>
				</div>
				
				<div class="mb-3">
					<label class="form-label  mb-1"><?php f_translate_echo('Email Activation Status'); ?></label>
					<select class="form-select" field_name="activation_on"    <?php f_echo_html( $disabled_if_not_admin ); ?>>
						<option value="0" <?php f_echo_html( $item_json['activation_on'] == 0 ? 'selected' : '' ); ?>><?php f_translate_echo('Not activated'); ?></option>
						<option value="1" <?php f_echo_html( $item_json['activation_on'] == 1 ? 'selected' : '' ); ?>><?php f_translate_echo('Activated'); ?></option>
					</select>
				</div>
				
				<div class="mb-3">
					<label class="form-label  mb-1"><?php f_translate_echo('Email Activation Date'); ?></label>
					<input type="datetime-local" field_name="activation_date" class="form-control" value="<?php f_echo_html( $item_json['activation_date'] ); ?>"    <?php f_echo_html( $disabled_if_not_admin ); ?> />
					<div class='form-text  small text-muted'><?php f_echo_html( $item_json['html_activation_date'] ); ?></div>
				</div>
				
				<div class="mb-3">
					<label class="form-label  mb-1"><?php f_translate_echo('Administrator\'s Comment'); ?></label>
					<textarea field_name="admin_comment" class="form-control"><?php f_echo_html( $item_json['admin_comment'] ); ?></textarea>
					<div class='form-text  small text-muted'><?php f_translate_echo( 'Hidden for the user' ); ?></div>
				</div>
				
			<?php
				}
			?>
			
			
		</div>

		<?php if( $is_business && f_db_table_exists('store') ){ ?>
		<div class="col-12 mt-4 pt-4 border-top" form_group="store_shop">
			<h3 class="h5 mb-3"><?php f_translate_echo('Магазин'); ?></h3>
			<p class="text-muted small"><?php f_translate_echo('Публичная витрина: после сохранения откройте страницу магазина по ссылке ниже.'); ?></p>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label mb-1"><?php f_translate_echo('Название'); ?></label>
					<input type="text" class="form-control" field_name="store_name" value="<?php f_echo_html((string)($store_row['name'] ?? '')); ?>" />
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label mb-1">URL (slug)</label>
					<input type="text" class="form-control" field_name="store_slug" value="<?php f_echo_html((string)($store_row['slug'] ?? '')); ?>" placeholder="my-shop" />
				</div>
				<div class="col-12 mb-3">
					<label class="form-label mb-1"><?php f_translate_echo('Описание'); ?></label>
					<textarea class="form-control" rows="3" field_name="store_description"><?php f_echo_html((string)($store_row['description'] ?? '')); ?></textarea>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label mb-1"><?php f_translate_echo('Phone'); ?></label>
					<input type="text" class="form-control" field_name="store_phone" value="<?php f_echo_html((string)($store_row['phone'] ?? '')); ?>" />
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label mb-1"><?php f_translate_echo('Address'); ?></label>
					<input type="text" class="form-control" field_name="store_address" value="<?php f_echo_html((string)($store_row['address'] ?? '')); ?>" />
				</div>
			</div>
			<?php if( !empty($store_row['slug']) ){ ?>
				<p class="mb-2">
					<a href="<?php f_echo_html(f_page_link('shop') . '/' . rawurlencode((string)$store_row['slug'])); ?>" target="_blank" rel="noopener"><?php f_translate_echo('Открыть витрину'); ?></a>
				</p>
			<?php } ?>
			<button type="button" class="btn btn-outline-primary" field_btn="save_store"><?php f_translate_echo('Сохранить магазин'); ?></button>
		</div>
		<?php } ?>
		
		
		<div class="btn  btn-lg  btn-dark  my-5 w-auto  px-5  mx-auto" field_btn="save"><?php f_translate_echo($is_new ? 'Create' : 'Save'); ?></div>
		
	</div>
</div>



<script>

document.addEventListener("DOMContentLoaded", function(event){
	
	let jq_btn_save = $('[field_btn="save"]');
	let jq_select_city = $('[field_name="city"]');
	let jq_password = $('[field_name="password"]');


	jq_btn_save.on('click', function(){
		
		jq_btn_save.addClass('btn_sending')
		
		// Собираем данные
		let form_json = f_form_get( $('[form_group="user"]') );
		
		/*
		$('[field_name]').each(function(i, elem){
			let jq_el = $(elem);
			form_json[ jq_el.attr('field_name') ] = jq_el.val();
		})
		*/
		//console.log( form_json );
		
		f_ajax('user', 'save', form_json, function(data){
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


	jq_password.on('input', function(){
		jq_password.val( jq_password.val().replaceAll(' ', '') )
	})

	$('[field_btn="save_store"]').on('click', function(){
		var jq_btn = $(this);
		jq_btn.prop('disabled', true);
		f_ajax('store', 'save', {
			name: $('[form_group="store_shop"] [field_name="store_name"]').val(),
			slug: $('[form_group="store_shop"] [field_name="store_slug"]').val(),
			description: $('[form_group="store_shop"] [field_name="store_description"]').val(),
			phone: $('[form_group="store_shop"] [field_name="store_phone"]').val(),
			address: $('[form_group="store_shop"] [field_name="store_address"]').val()
		}, function(data){
			jq_btn.prop('disabled', false);
			if (data['data'] && data['data']['error']) {
				toastr.error(data['data']['error']);
			} else {
				location.reload();
			}
		});
	});

})

</script>