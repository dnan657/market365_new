<?php

// Проверка на авторизацию
f_user_check_redirect('admin');


$status_page = 0;
$alert_page = "";




$post_type = $_POST['type'];

if( isset( $post_type ) ){
	
	if( $post_type == 'save_all' ){
		foreach($_POST['data_arr'] as $iten_json){
			
			f_db_update_smart(
			"user", 
			
			["_id" => intval( $iten_json['_id'] )], 
			
			[   ]);
		}
		
	}else if( $post_type == 'delete' ){
			
		f_db_update_smart(
			"user", 
			["_id" => intval( $iten_json['_id'] )], 
			[  'ban_on' => 1 ]
		);
	}
	
	f_api_response_exit([]);
}






$page_current = intval($_GET['page']);
$page_current = $page_current < 1 ? 1 : $page_current;
$limit = 30;

$column_order = $_GET['column'];
$column_json_order = [
	'_id'					=> ' `_id` ',
	'_create_date'			=> ' `_create_date` ',
	'_create_user'			=> ' `_create_user` ',
	'did_id'				=> ' `did_id` ',
	'email'					=> ' `email` ',
	'ban_on'				=> ' `ban_on` ',
	'type'					=> ' `type` ',
	'name'					=> ' `name` ',
	'phone'					=> ' `phone` ',
	'iin'					=> ' `iin` ',
	'city'					=> ' `city` ',
	'gender'							=> ' `gender` ',
	'birthday_date'						=> ' `birthday_date` ',
	'admin_comment'						=> ' `admin_comment` ',
	'password'							=> ' `password` ',
	'password_hash_sha256'				=> ' `password_hash_sha256` ',
	'_create_did_id'					=> ' `_create_did_id` ',
	'visit_date'						=> ' `visit_date` ',
	'auth_date'							=> ' `auth_date` ',
	'activation_on'						=> ' `activation_on` ',
	'activation_date'					=> ' `activation_date` ',
	'activation_code'					=> ' `activation_code` ',
	'activation_create_date'			=> ' `activation_create_date` ',
	'activation_expired_date'			=> ' `activation_expired_date` ',
	'activation_send_date'				=> ' `activation_send_date` ',
	'forgout_password'					=> ' `forgout_password` ',
	'forgout_password_hash_sha256'		=> ' `forgout_password_hash_sha256` ',
	'forgout_create_date'				=> ' `forgout_create_date` ',
	'forgout_expired_date'				=> ' `forgout_expired_date` ',
	'forgout_send_date'					=> ' `forgout_send_date` '
];
$column_order = $column_json_order[$column_order] ? $column_json_order[$column_order] : $column_json_order['_id'];

$desc_order = $_GET['desc'] == "0" ? 'ASC' : 'DESC';

$sql_where = '';

$sql_continue = "
	ORDER BY
		". $column_order ." ". $desc_order . "
		
	LIMIT
		". ( ($page_current - 1) * $limit) .", ". $limit . "
	";

		
$db_result = f_db_get_user_list($sql_where, $sql_continue);

$row_count = $db_result['count_total'];
$row_count_activation_on = f_db_select_count('user', $sql_where, ' AND `activation_on` = 1');
$row_arr = $db_result['data_arr'];
?>

<!doctype html>
<html lang="<?php f_echo_html( $WEB_JSON['page_json']['lang'] ); ?>">
<head>

	<title>
		<?php f_echo_html( f_translate('Пользователи') ); ?>
	</title>

	<?php f_template('head_body'); ?>

</head>
<body>

<?php f_template('style'); ?>
<?php f_template('nav_top'); ?>

	
<div class="page">
		
	<div class="box_page">
		
		<div class="d-flex  flex-nowrap  align-items-center    mb-4">
		
			<h1 class="h3  m-0">
				<?php f_echo_html( f_translate('Пользователи') ); ?>
			
				<span class="badge  border  border-secondary   rounded-pill  fw-normal  text-secondary  ms-2">
					<span class="text-success"> <?php f_echo_html( $row_count_activation_on ); ?> </span>
					 / 
					 <span class=""> <?php f_echo_html( $row_count ); ?> </span>
				</span>
				
			</h1>
				
		</div>
		
		
		<div style="overflow-x: auto;">
			
			<table class="table  table-striped  table-hover  table-bordered  rounded  mb-0   table_mobile">
				<thead>
					<tr>
						<th></th>
						
						<th column="_id" desc="1"><?php f_echo_html( '№' ); ?></th>
						<th column="_create_date"><?php f_echo_html( f_translate('Создано') ); ?></th>
						<th column="visit_date"><?php f_echo_html( f_translate('Активность') ); ?></th>
						<th column="auth_date"><?php f_echo_html( f_translate('Вошел') ); ?></th>
						<th column="type"><?php f_echo_html( f_translate('Тип') ); ?></th>
						<th column="name"><?php f_echo_html( f_translate('Имя') ); ?></th>
						<th column="email"><?php f_echo_html( f_translate('Email') ); ?></th>
						<th column="iin"><?php f_echo_html( f_translate('ИИН') ); ?></th>
						<th column="phone"><?php f_echo_html( f_translate('Телефон') ); ?></th>
						
						<th column="count_ads"><?php f_echo_html( f_translate('Объявления') ); ?></th>
						
					</tr>
				</thead>
				<tbody>
					<?php
						
						foreach( $row_arr as $item_json ){
							
							echo("
								<tr  btn_href='/user/". f_html( f_num_encode( $item_json['_id'] ) ) ."'>
									
									<td>
										<div class='w-100  d-flex'>
											<a href='/user-set-auth?uid=". f_html( $item_json['html_uid'] ) ."'  class='btn  btn-success  btn-sm  d-block  w-100'>
												 ". f_translate( 'Войти' ) ."
											</a>
										</div>
									</td>
									
									<td  data-name='". f_html('№') ."'>
										". f_html( f_num_encode( $item_json['_id'] ) ) ."
									</td>
									
									<td  data-name='". f_translate('Создано') ."'>
										". f_datetime_beauty( $item_json['_create_date'] ) ."
										<div class='small text-muted'>". $item_json['html_create_date'] ."</div>
									</td>
									
									<td  data-name='". f_translate('Активность') ."'>
										". f_datetime_beauty( $item_json['visit_date'] ) ."
										<div class='small text-muted'>". $item_json['html_visit_date'] ."</div>
									</td>
									
									<td  data-name='". f_translate('Вошел') ."'>
										". f_datetime_beauty( $item_json['auth_date'] ) ."
										<div class='small text-muted'>". $item_json['html_auth_date'] ."</div>
									</td>
									
									
									<td  data-name='". f_translate('Пользователь') ."'  colspan=5>
										<div>
											<div><b>". f_html( $item_json['name'] ) ." </b> </div>
											<div class='d-flex flex-wrap'>
												<div class='col-12  col-md-6  text-nowrap  small text-muted'> <i class='me-1  bi bi-person-bounding-box'></i> ". f_html( $item_json['html_type'] ?: '-' ) ."</div>
												<div class='col-12  col-md-6  text-nowrap  small text-muted'> <i class='me-1  bi bi-person-gear'></i> ". f_html( $item_json['login'] ?: '-' ) ."</div>
												<div class='col-12  col-md-6  text-nowrap  small text-muted'> <i class='me-1  bi bi-person-vcard'></i> ". f_html( $item_json['iin'] ?: '-' ) ."</div>
												<div class='col-12  col-md-6  text-nowrap  small text-muted'> <i class='me-1  bi bi-telephone'></i> ". f_html( f_phone_beauty( $item_json['phone'] ) ?: '-' ) ."</div>
												<div class='col-12  col-md-6  text-nowrap  small text-muted'> <i class='me-1  bi bi-envelope'></i> ". f_html( $item_json['email'] ?: '-' ) ."</div>
											</div>
										</div>
									</td>
									
									<td  data-name='". f_translate('Объявления') ."'>
										
										<span title='". f_html( f_translate('Сданы') ) ."'  class='text-success'> ". f_html( $item_json['count_success_test'] ) ." </span>
										<span class='mx-2'> / </span>
										
										<span title='". f_html( f_translate('В процессе') ) ."'  class='text-warning'> ". f_html( $item_json['count_processed_test'] ) ." </span>
										<span class='mx-2'> / </span>
										
										<span title='". f_html( f_translate('Провалены') ) ."'  class='text-danger'> ". f_html( $item_json['count_fail_test'] ) ." </span>
										<span class='mx-2'> / </span>
										
										<span title='". f_html( f_translate('Всего') ) ."'  class='fw-bold'> ". f_html( $item_json['count_total_test'] ) ." </span>
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
		</div>
		
		<?php echo( f_html_pagination($page_current, $limit, $row_count) ); ?>
		
	</div>
</div>



<?php f_template('script'); ?>


<script>

</script>

	
</body>
</html>