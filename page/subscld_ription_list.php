<?php

// Проверка на авторизацию
f_user_check_redirect();


$page_search = trim($_GET['search']);
$page_custom = $_GET['custom'];
$page_current = intval($_GET['page']);
$page_current = $page_current < 1 ? 1 : $page_current;
$limit = 30;
$sql_where = '';

$column_order = $_GET['column'];
$column_json_order = [
	'id'						=> ' `pdd_subscription`.`_id` ',
	'create_date'				=> ' `pdd_subscription`.`_create_date` ',
	'tmp_from_name'				=> ' `tmp_from_user`.`name` ',
	'tmp_to_name'				=> ' `tmp_to_user`.`name` ',
	'to_user_group_name'		=> ' `pdd_subscription`.`to_user_group_name` ',
	'activation_status'			=> ' `pdd_subscription`.`activation_status` ',
	'category_question'			=> ' `pdd_subscription`.`category_question` ',
	'activation_status_expired'	=> ' `pdd_subscription`.`activation_status` '
];
$column_order = $column_json_order[$column_order] ? $column_json_order[$column_order] : $column_json_order['id'];

$desc_order = $_GET['desc'] == '0' ? 'ASC' : 'DESC';


$count_total = 0;
$count_user_activation_active = 0;
$count_user_activation_wait = 0;
$count_admin_activation_on = 0;
$count_admin_activation_off = 0;
$activation_status_expired = 0;

if( f_user_get()['type'] == 'admin' ){
	
	$count_user_activation_active = f_db_select_count('pdd_subscription', ' AND `activation_status` = 1' );
	$count_user_activation_wait = f_db_select_count('pdd_subscription', ' AND `activation_status` = 0 ' );
	$activation_status_expired = f_db_select_count('pdd_subscription', ' AND `activation_status` = -1 ' );
	$count_admin_activation_on = f_db_select_count('pdd_subscription', ' AND `admin_activation_on` = 1 ' );
	$count_admin_activation_off = f_db_select_count('pdd_subscription', ' AND `admin_activation_on` = 0 ' );
	
}else if( f_user_get()['type'] == 'school' ){
	$sql_where .= ' AND  `pdd_subscription`.`from_user_id`  =  ' . f_user_get()['_id'];
	
	$count_user_activation_active = f_db_select_count('pdd_subscription', ' AND `activation_status` = 1 AND `from_user_id` = ' . f_user_get()['_id'] );
	$count_user_activation_wait = f_db_select_count('pdd_subscription', ' AND `activation_status` = 0 AND `from_user_id` = ' . f_user_get()['_id'] );
	$activation_status_expired = f_db_select_count('pdd_subscription', ' AND `activation_status` = -1 AND `from_user_id` = ' . f_user_get()['_id'] );
	$count_admin_activation_on = f_db_select_count('pdd_subscription', ' AND `admin_activation_on` = 1 AND `from_user_id` = ' . f_user_get()['_id'] );
	$count_admin_activation_off = f_db_select_count('pdd_subscription', ' AND `admin_activation_on` = 0 AND `from_user_id` = ' . f_user_get()['_id'] );
	
}else if( f_user_get()['type'] == 'user' ){
	$sql_where .= ' AND  `pdd_subscription`.`to_user_id`  =  ' . f_user_get()['_id'];
	
	$count_user_activation_active = f_db_select_count('pdd_subscription', ' AND `activation_status` = 1 AND `to_user_id` = ' . f_user_get()['_id'] );
	$count_user_activation_wait = f_db_select_count('pdd_subscription', ' AND `admin_activation_on` = 0 AND `to_user_id` = ' . f_user_get()['_id'] );
	$activation_status_expired = f_db_select_count('pdd_subscription', ' AND `admin_activation_on` = -1 AND `to_user_id` = ' . f_user_get()['_id'] );
}

$count_total = f_db_select_count('pdd_subscription', $sql_where );

//$page_custom = in_array($page_custom, ['access', 'check', 'activate', 'no_activate', 'expired'])
$json_sql_where_custom = [
	'admin_activation_on'	=> ' AND `admin_activation_on` = 1 ',
	'admin_activation_off'	=> ' AND `admin_activation_on` = 0 ',
	'activation_status'		=> ' AND `activation_status` = 1 ',
	'user_activation_off'	=> ' AND `activation_status` = 0 ',
	'activation_status_expired'			=> ' AND `activation_status` = -1 ',
];

$sql_where .= $json_sql_where_custom[ $page_custom ] ? $json_sql_where_custom[ $page_custom ] : '';



$count_row = f_db_select_count('pdd_subscription', $sql_where );

if( isset($page_search) != '' ){
	$sql_search_number = intval($page_search);
	$sql_search = f_db_sql_value_only($page_search);
	$sql_where .= '
		AND  
		(
			`pdd_subscription`.`from_user_id`  =  ' . $sql_search_number . '
			OR
			`pdd_subscription`.`to_user_id`  =  ' . $sql_search_number . '
			
			OR
			`pdd_subscription`.`category_question`  LIKE  "%' . str_replace(', ', '_', $sql_search) . '%"
			
			OR
			`pdd_subscription`.`admin_comment`  LIKE  "%' . $sql_search . '%"
			OR
			`pdd_subscription`.`school_comment`  LIKE  "%' . $sql_search . '%"
			
			OR
			`tmp_to_user`.`name`  LIKE  "%' . $sql_search . '%"
			OR
			`tmp_to_user`.`iin`	  LIKE  "%' . $sql_search . '%"
			OR
			`tmp_to_user`.`phone`  LIKE  "%' . $sql_search . '%"
			OR
			`tmp_to_user`.`email`  LIKE  "%' . $sql_search . '%"
			OR
			`tmp_to_user`.`type`  LIKE  "%' . $sql_search . '%"
			
			OR
			`tmp_from_user`.`name`  LIKE  "%' . $sql_search . '%"
			OR
			`tmp_from_user`.`iin`	  LIKE  "%' . $sql_search . '%"
			OR
			`tmp_from_user`.`phone`  LIKE  "%' . $sql_search . '%"
			OR
			`tmp_from_user`.`email`  LIKE  "%' . $sql_search . '%"
			OR
			`tmp_from_user`.`type`  LIKE  "%' . $sql_search . '%"
		)
	';
}

$sql_continue = "
	ORDER BY
		". $column_order ." ". $desc_order . "
		
	LIMIT
		". ( ($page_current - 1) * $limit) .", ". $limit . "
	";

		
$db_result = f_db_get_subscription_list($sql_where, $sql_continue);

$row_arr = $db_result['data_arr'];

?>

<!doctype html>
<html lang="<?php f_echo_html( $WEB_JSON['page_json']['lang'] ); ?>">
<head>

	<title>
		<?php f_echo_html( f_translate('Подписки') ); ?>
	</title>

	<?php f_template('head_body'); ?>

</head>
<body>

<?php f_template('style'); ?>
<?php f_template('bg'); ?>
<?php f_template('nav_top'); ?>

	
<div class="container  page">
		
	<div class="box_page">
		
		<div class="d-flex  flex-wrap  align-items-center   gap-2   mb-4">
			<h1 class="h3  m-0">
				<?php f_echo_html( f_translate('Подписки') ); ?>
			</h1>
			
			
			<?php
				if( f_user_get()['type'] == 'admin' ){
			?>
				<div class="input-group   w-auto   ms-auto ">
					
					<div class="form-select  p-0  flex-shrink-0" style="width: 150px">
						<select  field_group="create_subscription"  field_select="from_user_id" class="d-none"></select>
					</div>
					
					<input  field_group="create_subscription" field_name="count"  class="form-control  flex-shrink-0" style="width: 60px;" type="number" value="1" min="1" max="10"></input>
					
					<input  field_group="create_subscription" field_name="admin_price"  class="form-control  flex-shrink-0" type="number" value="1000" min="1" max="10000" style="width: 80px;"></input>
					
					<button  field_group="create_subscription"  field_btn="btn_subscription_create" class="btn btn-warning  flex-shrink-0" type="button" ssid="<?php f_echo_html( session_id() ); ?>">
						<?php f_echo_html( f_translate('Создать') );  ?>
					</button>
				</div>
			<?php
				}
			?>
				
		</div>
		
		
		<div class="filter_custom    d-flex  flex-nowrap  align-items-center  overflow-auto  gap-3  pb-2  mb-4">
			
			<a href="/subscription" class="btn btn-<?php f_echo_html( $page_custom == '' ? '' : 'outline-' ); ?>secondary">
				<span><?php f_echo_html( f_translate('Все') ); ?>:</span>
				<?php f_echo_html( $count_total ); ?>
			</a>
			
			<?php
				if( f_user_get()['type'] == 'admin' ||  f_user_get()['type'] == 'school' ){
			?>
				<a href="/subscription?custom=admin_activation_on" class="btn btn-<?php f_echo_html( $page_custom == 'admin_activation_on' ? '' : 'outline-' ); ?>success">
					<span><?php f_echo_html( f_translate('Оплачено') ); ?>:</span>
					<?php f_echo_html( $count_admin_activation_on ); ?>
				</a>
				<a href="/subscription?custom=admin_activation_off" class="btn btn-<?php f_echo_html( $page_custom == 'admin_activation_off' ? '' : 'outline-' ); ?>secondary">
					<span><?php f_echo_html( f_translate('Не оплачено') ); ?>:</span>
					<?php f_echo_html( $count_admin_activation_off ); ?>
				</a>
			<?php
				}
			?>
			
			<a href="/subscription?custom=activation_status" class="btn btn-<?php f_echo_html( $page_custom == 'activation_status' ? '' : 'outline-' ); ?>success">
				<span><?php f_echo_html( f_translate('Активировано') ); ?>:</span>
				<?php f_echo_html( $count_user_activation_active ); ?>
			</a>
			<a href="/subscription?custom=user_activation_off" class="btn btn-<?php f_echo_html( $page_custom == 'user_activation_off' ? '' : 'outline-' ); ?>secondary">
				<span><?php f_echo_html( f_translate('Не активировано') ); ?>:</span>
				<?php f_echo_html( $count_user_activation_wait ); ?>
			</a>
			
			<a href="/subscription?custom=activation_status_expired" class="btn btn-<?php f_echo_html( $page_custom == 'activation_status_expired' ? '' : 'outline-' ); ?>dark">
				<span><?php f_echo_html( f_translate('Истекли') ); ?>:</span>
				<?php f_echo_html( $activation_status_expired ); ?>
			</a>
			
		</div>
		
		<div class="input-group   mb-2"  style="max-width: 100%; width: 400px;">
			
			<input  field_group="search" field_name="search"  class="form-control  flex-shrink-0" type="text" value="<?php f_echo_html( $_GET['search'] ); ?>" placeholder="<?php f_echo_html( f_translate('Поиск') ); ?>"></input>
			
			<button  field_group="search"  field_btn="btn_search" class="btn btn-primary  flex-shrink-0" type="button">
				<?php f_echo_html( f_translate('Поиск') );  ?>
			</button>
			
		</div>
		
		<div style="overflow-x: auto;">
			<table class="table  table-striped  table-hover  table-bordered  rounded  mb-0  table_mobile">
				<thead>
					<tr>
						<th column="id" desc="1">
							<?php f_echo_html( f_translate('Артикул') ); ?>
						</th>
						
						<th column="create_date">
							<?php f_echo_html( f_translate('Создан') ); ?>
						</th>
						
						<th column="tmp_from_name">
							<?php f_echo_html( f_translate('Источник') ); ?>
						</th>
						<!--
						<th column="admin_activation_on">
							<?php f_echo_html( f_translate('Оплачено') ); ?>
						</th>
						-->
						<th column="tmp_to_name">
							<?php f_echo_html( f_translate('Получатель') ); ?>
						</th>
						<th column="to_user_group_name">
							<?php f_echo_html( f_translate('Группа') ); ?>
						</th>
						<th column="activation_status">
							<?php f_echo_html( f_translate('Статус') ); ?>
						</th>
						<th column="activation_status_expired">
							<?php f_echo_html( f_translate('Осталось') ); ?>
						</th>
						<th column="category_question">
							<?php f_echo_html( f_translate('Тест') ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					
					<?php
						
						foreach( $row_arr as $item_json ){
							
							echo("
								<tr btn_href='/subscription/item/". f_num_encode( $item_json['_id'] ) ."'>
								
									<td  data-name='". f_translate('Артикул') ."'>". f_num_encode( $item_json['_id'] ) ."</td>
									<td  data-name='". f_translate('Создан') ."'>
										<div>
											<div>". f_datetime_beauty( $item_json['_create_date'] ) ."</div>
											<div class='small text-muted'>". f_html( $item_json['html_text_create_date'] ) . "</div>
										</div>
									</td>
									
									<td  data-name='". f_translate('Источник') ."'>
										<div>
											<div class='text-nowrap  fw-bold'>". f_html( $item_json['tmp_from_name'] ?: '-' ) ." </div>
											<div class='text-nowrap  small text-muted'> <i class='me-1  bi bi-envelope'></i> ". f_html( $item_json['tmp_from_email'] ?: '-' ) ."</div>
										</div>
									</td>
									
									<!--
									<td  data-name='". f_translate('Оплачено') ."'>
										<div>
											<div class='text-nowrap'>". f_html(  f_translate( $item_json['admin_activation_on'] == 1 ? 'Оплачено' : 'Не оплачено' ) ) ." </div>
											<div class='text-nowrap  small text-muted'> ". f_datetime_beauty( $item_json['admin_activation_date'] ) ."</div>
										</div>
									</td>
									-->
									
									<td  data-name='". f_translate('Получатель') ."'>
										<div>
										".(
											$item_json['to_user_id'] == null ? 
											"
												-
											" : "
												<div> <b>". f_html( $item_json['tmp_to_name'] ) ." </b> </div>
												" . ( !$item_json['tmp_to_iin'] ? "" : "<div class='text-nowrap  small text-muted'> <i class='me-1  bi bi-person-vcard'></i> ". f_html( $item_json['tmp_to_iin'] ) ."</div>") ."
												" . ( !$item_json['tmp_to_login'] ? "" : "<div class='text-nowrap  small text-muted'> <i class='me-1  bi bi-person-circle'></i> ". f_html( $item_json['tmp_to_login'] ) ."</div>") ."
												" . ( !f_phone_beauty( $item_json['tmp_to_phone'] ) ? "" : "<div class='text-nowrap  small text-muted'> <i class='me-1  bi bi-telephone'></i> ". f_html( f_phone_beauty( $item_json['tmp_to_phone'] ) ) ."</div>") ."
												" . ( !$item_json['tmp_to_email'] ? "" : "<div class='text-nowrap  small text-muted'> <i class='me-1  bi bi-envelope'></i> ". f_html( $item_json['tmp_to_email'] ) ."</div>") ."
											"
										)."
										</div>
									</td>
									
									<td  data-name='". f_translate('Группа') ."'>
										<div>
											<div class='text-nowrap'>". f_html( $item_json['to_user_group_name'] ?: '-' ) ." </div>
										</div>
									</td>
									
									<td  data-name='". f_translate('Статус') ."'>
										<div>
										".
										(
											$item_json['activation_status'] == 0 ? 
											"
												<div class='text-nowrap  badge rounded-pill text-bg-secondary'>". f_translate( 'Не активирован' ) ."</div>
											" : (
												$item_json['activation_status'] == 1 ?
												"
													<div class='text-nowrap  badge rounded-pill text-bg-success'>". f_translate( 'Активирован' ) ."</div>
													<div class='text-nowrap  small text-muted'> ". f_html( $item_json['html_text_activation_date'] ) ."</div>
												"
												:
												"
													<div class='text-nowrap  badge rounded-pill text-bg-danger'>". f_translate( 'Истёк' ) ."</div>
													<div class='text-nowrap  small text-muted'> ". f_html( $item_json['html_text_activation_expired_date'] ) ."</div>
												"
												)
										)
										."
										</div>
									</td>
									
									<td  data-name='". f_translate('Осталось') ."'>
										<div>
										".(
											$item_json['activation_status'] == 0 ? 
											"
												-
											" : "
												<div class='fw-bold'>".  $item_json['html_text_activation_expired_day_left'] ."</div>
												<div class='small text-muted'>". f_translate( 'до' ) . ' ' . f_datetime_beauty( $item_json['activation_expired_date'] ) ."</div>
											"
										)."
										</div>
									</td>
									
									<td  data-name='". f_translate('Тест') ."'>
										<div>
										".
										(
											$item_json['activation_status'] == 0 ? 
											"
												-
											" : "
												<div class='text-nowrap  fw-bold'>". f_pdd_category_beauty( $item_json['category_question'] ) ."</div>
											"
										)
										."
										</div>
									</td>
									
									<td class='d-md-none'>
										<div class='btn  btn-primary  btn-sm  d-block  w-100'>
											". f_translate( 'Открыть' ) ."
										</div>
									</td>
									
								</tr>
							");
						}
					?>
				</tbody>
			</table>
				
			<?php echo( count($row_arr) == 0 ? '<div class="empty"></div>' : '' ); ?>
			
		</div>
		
		<?php echo( f_html_pagination($page_current, $limit, $count_row) ); ?>
		
	</div>
</div>



<?php f_template('script'); ?>



<script>

let jq_btn_search = $('[field_group="search"][field_btn="btn_search"]');
let jq_search_input = $('[field_group="search"][field_name="search"]');

let jq_btn_create = $('[field_group="create_subscription"][field_btn="btn_subscription_create"]');
let jq_count_create = $('[field_group="create_subscription"][field_name="count"]');
let jq_from_user_id_create = $('[field_group="create_subscription"][field_select="from_user_id"]');
let jq_admin_price_create = $('[field_group="create_subscription"][field_name="admin_price"]');


jq_btn_search.on('click', function(){
	let jq_elem = $(this);
	
	let query_json = f_url_query_to_json();
	query_json['search'] = jq_search_input.val().trim();
	if(query_json['search'] == ''){
		delete query_json['search'];
	}
	
	location.href = location.pathname + f_url_json_to_query( query_json );
})


jq_btn_create.on('click', function(){
	let jq_elem = $(this);
	
	let ssid = jq_elem.attr('ssid');
	let count = parseInt( jq_count_create.val() );
	let admin_price = parseInt( jq_admin_price_create.val() );
	admin_price = isNaN(admin_price) ? 1 : admin_price;
	let from_user_id = jq_from_user_id_create.val() ;
	
	if(count == 0 || from_user_id == '' || from_user_id == null){
		toastr.error('<?php f_echo_html( f_translate('Неверно указаны данные') ); ?>');
		return;
	}
	
	let new_form_json = {
		'ssid': ssid,
		'from_user_id': from_user_id,
		'count': count,
		'admin_price': admin_price,
	}
	
	
	f_ajax('subscription', 'create', new_form_json, function(data){
		console.log('return', data);
		if( data['data']['error'] ){
			toastr.error( data['data']['error'] );
		}else{
			location.reload()
		}
	})
	
})


jq_from_user_id_create.select2({
    placeholder: '<?php f_echo_html( f_translate('Источник') ); ?>',
	minimumInputLength: 1, // Минимальная длина ввода перед началом поиска
	maximumSelectionLength: 20, // Лимит на количество выбранных элементов
	ajax: {
		url: '/api/user?query=find',
		dataType: 'json',
		delay: 250,
		cache: true,
		data: function(params_json) {
			return {
				search: params_json.term, // Поисковый запрос
				g_recaptcha: $('[name=g_recaptcha]').val(),
				//page: params.page,
			}
		},
		processResults: function(data) {
			let arr_for_select2 = [];
			data['data']['data_arr'].forEach(function(item_json){
				arr_for_select2.push({
					'id': item_json['_id_str'],
					'text': '#' + item_json['_id_str'] + ' - ' + item_json['name']
				})
			})
			return {
				results: arr_for_select2 // Ваши данные из PHP-скрипта
			}
		}
	},
});

</script>

	
</body>
</html>