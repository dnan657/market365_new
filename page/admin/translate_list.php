<?php


// Проверка на авторизацию
//f_user_check_redirect('admin');


if ($_SERVER['PHP_AUTH_USER'] != 'dadmin' || $_SERVER['PHP_AUTH_PW'] != 'dadmin') {
    header('WWW-Authenticate: Basic realm="Please login"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Вам нужно авторизоваться';
    exit();
}

$table_name = $_GET['table_name'] ?: "ads_category";
$column_0 = $_GET['column_0'] ?: "_id";
$column_1 = $_GET['column_1'] ?: "title_ru";
$column_2 = $_GET['column_2'] ?: "title_en";



$post_type = $_POST['type'];

if( isset( $post_type ) ){
	
	if( $post_type == 'save_all' ){
		foreach($_POST['data_arr'] as $iten_json){
			
			$json_update = [];
			$json_update[$column_2] = mb_trim( $iten_json[ $column_2 ] );
			
			
			f_db_update_smart(
				$table_name, 
				
				["_id" => intval( $iten_json['_id'] )], 
				
				$json_update
			);
		}
		
	}
	
	f_api_response_exit([]);
}







$search = $_GET['search'];
$page_current = intval($_GET['page']);
$page_current = $page_current < 1 ? 1 : $page_current;
$limit = 100;

$column_order = $_GET['column'];
$column_json_order = [];
$column_json_order[$column_0] = ' `'. $column_0 .'` ';
$column_json_order[$column_1] = ' `'. $column_1 .'` ';
$column_json_order[$column_2] = ' `'. $column_2 .'` ';

$column_order = $column_json_order[$column_order] ? $column_json_order[$column_order] : $column_json_order['_id'];

$desc_order = $_GET['desc'] == "0" ? 'ASC' : 'DESC';


$sql_continue = "
	ORDER BY
		". $column_order ." ". $desc_order . "
		
	LIMIT
		". ( ($page_current - 1) * $limit) .", ". $limit . "
	";

// Перенести все title_ru в которых нет русских символов в title_en
// update `ads_param_value` set title_en = title_ru where `title_ru` NOT REGEXP '[А-Яа-я]';

$sql_where .= '';
$sql_where .= " AND `".$column_1."` REGEXP '[А-Яа-я]' ";
$sql_where .= " AND `".$column_1."` IS NOT NULL AND `".$column_1."` != '' ";

if($search != ''){
	$search_value = f_db_sql_value_only($search);
	$sql_where = " AND
		(
			`".$column_1."` LIKE '%". $search_value ."%'
			OR
			`".$column_2."` LIKE '%". $search_value ."%'
		)";
}

$row_arr = f_db_select("SELECT * FROM `".$table_name."` WHERE 1 " . $sql_where . $sql_continue);


$row_total_count = f_db_select("SELECT COUNT(_id) as 'total_count' FROM `".$table_name."` WHERE 1 " . $sql_where)[0]['total_count'];
$row_all_table_count = f_db_select("SELECT COUNT(_id) as 'total_count' FROM `".$table_name."` WHERE 1")[0]['total_count'];

?>

	
<div class="container  py-4  my-4">
		
	<div class="box_page">
		
		<div class="d-flex  flex-nowrap  align-items-center    mb-4">
		
			<h1 class="h3  m-0">
				<?php f_echo_html( f_translate('Перевод') ); ?>
				
				<span class="badge  border  border-secondary   rounded-pill  fw-normal  text-secondary  ms-2">
					 <span class=""> Найдено: <?php f_echo_html( $row_total_count ); ?> </span>
					 /
					 <span class=""> Всего: <?php f_echo_html( $row_all_table_count ); ?> </span>
				</span>
			</h1>
				
		</div>
		
		<form class="row  mb-3" method="get">
			<div class="col-lg-3  mb-3">
				<label class="form-label">Table Name</label>
				<input type="text" class="form-control" name="table_name" placeholder="<?php f_echo_html( $table_name ); ?>">
			</div>
			<div class="col-lg-2  mb-3">
				<label class="form-label">Column ID</label>
				<input type="text" class="form-control" name="column_0" placeholder="input_column_ID" value="<?php f_echo_html( $column_0 ); ?>">
			</div>
			<div class="col-lg-2  mb-3">
				<label class="form-label">Column 1</label>
				<input type="text" class="form-control" name="column_1" placeholder="column_1" value="<?php f_echo_html( $column_1 ); ?>">
			</div>
			<div class="col-lg-2  mb-3">
				<label class="form-label">Column 2</label>
				<input type="text" class="form-control" name="column_2" placeholder="column_2" value="<?php f_echo_html( $column_2 ); ?>">
			</div>
			<div class="col-lg-2  mb-3  d-flex  align-items-end">
				<button class="btn btn-primary  d-block  w-100" type="submit">SET</button>
			</div>
		</form>
		
		<div class="row">
			<div class="col-lg-4  mb-3">
				<label class="form-label"><?php f_echo_html( $column_1 ); ?></label>
				<textarea  class="form-control form-control-sm   text_<?php f_echo_html( $column_1 ); ?>"></textarea>
			</div>
			<div class="col-lg-4  mb-3">
				<label class="form-label">
					<a translate_link="<?php f_echo_html( $column_2 ); ?>" target="translate"><?php f_echo_html( $column_2 ); ?></a>
				</label>
				<textarea  class="form-control form-control-sm   text_<?php f_echo_html( $column_2 ); ?>"></textarea>
			</div>
		</div>
			
		<div class="d-flex flex-wrap gap-3 mb-4">
			<button field_btn='btn_save_all' class='btn  btn-primary  btn-sm  d-block  w-auto'>
				<?php f_echo_html( f_translate( 'Сохранить все' ) ); ?>
			</button>
		</div>
		
		<div class="mb-4  d-md-flex">
			<div class="input-group   mt-3  col-md-6" style="max-width: 100%; width: 400px;">
				<input field_group="search" field_name="search" class="form-control  flex-shrink-0" type="text" value="<?php f_echo_html($search); ?>" placeholder="<?php f_echo_html( f_translate('Поиск')); ?>">
				<button field_group="search" field_btn="btn_search" class="btn btn-dark  flex-shrink-0" type="button"><i class='bi bi-search'></i></button>
			</div>
			
			<div class="col-md-6">
				<?php echo( f_html_pagination($page_current, $limit, $row_total_count) ); ?>
			</div>
		</div>
		
		<div style="overflow-x: auto;">
			
			<table class="table  table-striped  table-hover  table-bordered  rounded  mb-0">
				<thead>
					<tr>
						
						<th column="<?php f_echo_html( $column_0 ); ?>" desc="1">
							<?php f_echo_html( $column_0 ); ?>
						</th>
						
						<th column="<?php f_echo_html( $column_1 ); ?>">
							<?php f_echo_html( $column_1 ); ?>
						</th>
						
						<th column="<?php f_echo_html( $column_2 ); ?>">
							<?php f_echo_html( $column_2 ); ?>
						</th>
						
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
						
						foreach( $row_arr as $item_json ){
							
							echo("
								<tr  _id='". $item_json[ $column_0 ] ."'>
									<td>". f_html( $item_json[ $column_0 ] ) ."</td>
									<td column='". f_html( $column_1 ) ."'>". f_html( $item_json[ $column_1 ] ) ."</td>
									<td><input  class='form-control form-control-sm'  field_row='". $column_2 ."' value='". f_html( $item_json[ $column_2 ] ) ."'></input></td>
									<td>
										<button field_btn='btn_save' value='true' class='btn  btn-outline-primary  btn-sm'>
											 <i class='bi bi-floppy-fill'></i>
										</button>
									</td>
								</tr>
						");
					}
					?>
				</tbody>
			</table>
		</div>
		
		<?php echo( f_html_pagination($page_current, $limit, $row_total_count) ); ?>
		
	</div>
</div>


<script>

document.addEventListener("DOMContentLoaded", () => {

	let jq_btn_search = $('[field_group="search"][field_btn="btn_search"]');
	let jq_search_input = $('[field_group="search"][field_name="search"]');

	jq_search_input.keypress(function(e) {
		let keycode = e.keyCode || e.which;
		if( keycode == 13 ) {
			jq_btn_search.click();
		}
	});

	jq_btn_search.on('click', function(){
		let jq_elem = $(this);
		
		let query_json = f_url_query_to_json();
		query_json['page'] = 1;
		query_json['search'] = jq_search_input.val();
		
		if(query_json['search'] == ''){
			delete query_json['search'];
		}
		
		location.href = location.pathname + f_url_json_to_query( query_json );
	})


	function f_copy_text_column_1(){
		let arr_text_column_1 = [];
		$("tbody  tr  td[column='<?php f_echo_html( $column_1 ); ?>']").each(function(i, elem) {
			let jq_elem = $(elem);
			arr_text_column_1.push(jq_elem.text())
		})
		let text_column_1 = arr_text_column_1.join('\n--------\n');
		return text_column_1;
	}

	function f_text_set_column(text, column){
		let arr_text = text.split(/---+/g);
		$("tbody  tr  td  [field_row='"+column+"']").each(function(i, elem) {
			$(elem).val( arr_text[i].trim() );
		})
	}


	$('.text_<?php f_echo_html( $column_1 ); ?>').val( f_copy_text_column_1() );
	$('[translate_link="<?php f_echo_html( $column_2 ); ?>"]').attr('href', 'https://translate.yandex.ru/?source_lang=ru&target_lang=en&text=' + encodeURIComponent( $('.text_<?php f_echo_html( $column_1 ); ?>').val() ) );

	$('.text_<?php f_echo_html( $column_2 ); ?>').on( 'change', function(){
		f_text_set_column($(this).val(), '<?php f_echo_html( $column_2 ); ?>')
	});


	$(document).on('click', '[field_btn="btn_save"]', function(){
		
		let jq_tr = $(this).closest('tr[_id]');
		
		let arr_data = [];
		
		let item_json = {
			'<?php f_echo_html( $column_0 ); ?>': jq_tr.attr('_id')
		};
		jq_tr.find('[field_row]').each(function(i_2, el_field){
			let jq_field = $(el_field);
			let tag_field = jq_field.prop("tagName")
			let type_field = jq_field.attr("type")
			if( type_field == 'checkbox' ){
				item_json[ jq_field.attr('field_row') ] = jq_field.is(":checked") ? 1 : 0;
			}else{
				item_json[ jq_field.attr('field_row') ] = jq_field.val();
			}
		})
		arr_data.push( item_json );
		
		$.ajax({
			url: location.href,
			type: "POST",
			dataType: 'json',
			data: {
				type: "save_all",
				data_arr: arr_data
			},
			xhrFields: {
				withCredentials: true
			},
			success: function(data, status) {
				location.reload();
			}
		});

	})

	$(document).on('click', '[field_btn="btn_save_all"]', function(){
		
		let arr_row = $('table  tr[_id]');
		let arr_data = [];
		
		arr_row.each(function(i, el_tr){
			let jq_tr = $(el_tr);
			let item_json = {
				'<?php f_echo_html( $column_0 ); ?>': jq_tr.attr('_id')
			};
			jq_tr.find('[field_row]').each(function(i_2, el_field){
				let jq_field = $(el_field);
				let tag_field = jq_field.prop("tagName")
				let type_field = jq_field.attr("type")
				if( type_field == 'checkbox' ){
					item_json[ jq_field.attr('field_row') ] = jq_field.is(":checked") ? 1 : 0;
				}else{
					item_json[ jq_field.attr('field_row') ] = jq_field.val();
				}
			})
			arr_data.push( item_json );
		})
		
		$.ajax({
			url: location.href,
			type: "POST",
			dataType: 'json',
			data: {
				type: "save_all",
				data_arr: arr_data
			},
			xhrFields: {
				withCredentials: true
			},
			success: function(data, status) {
				location.reload();
			}
		});

	})


})

</script>
