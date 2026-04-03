<?php




// Подключаемся к базе данных MySQL
function f_db_link(){
	$login_json = [
		"host" => "localhost", // адрес сервера
		"database" => "market", // имя базы данных
		"user" => "market", // имя пользователя
		"password" => "zsrzrLOnwCq/!kBQ" // пароль
	];
	$db_link = new mysqli($login_json['host'], $login_json['user'], $login_json['password'], $login_json['database']);
	
	$error_link = mysqli_connect_error();
	
    // Проверяем наличие ошибки при подключении
    if ( $error_link ) {
		die("Ошибка подключения: " . $error_link);
    }
	
	mysqli_set_charset($db_link, 'utf8');
	
	return $db_link;
}
function f_db_query($sql_query=""){
	$db_link = f_db_link();
	
	mysqli_options($db_link, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
	$db_result = mysqli_query($db_link, $sql_query);
	
	$error_query = mysqli_error($db_link);
	
	mysqli_close($db_link);
	
    if ( $error_query ) {
        die("Ошибка запроса: " . $error_query);
    }
	
	return $db_result;
}

function f_db_select_id($table="", $_id){
	return f_db_select_smart($table, ['_id' => $_id])[0];
}

function f_db_select_smart($table="", $where_json=[], $limit=1, $page=0, $order_json=false){
	
	$table_sql = f_db_sql_column($table);
	
	$limit_sql = intval($limit);
	
	$offset_sql = intval($page * $limit);
	
	$where_arr = ['1'];
    foreach ($where_json as $key => $value) {
		$key_sql = f_db_sql_column($key);
		$value_sql = f_db_sql_value($value);
		$if_sql = f_db_sql_where_if($value);
        $where_arr[] = $key_sql . $if_sql . $value_sql;
    }
	
	$sql_select = "SELECT * FROM " . $table_sql;
	$sql_select .= " WHERE " . implode(' AND ', $where_arr);
	
	
	
	if( f_is_json($order_json) ){
		$order_arr = [];
		foreach ($order_json as $key => $value) {
			$key_sql = f_db_sql_column($key);
			$value_sql = $value == 1 ? ' DESC ' : ' ASC ';
			$order_arr[] = $key_sql . ' ' . $value_sql;
		}
	
		$sql_select .= " ORDER BY ". implode(' , ', $order_arr);
	}
	
	$sql_select .= " LIMIT ". $offset_sql .', '. $limit_sql .";";
	
	//if( $table == 'type_item' && $where_json['_id']['_type'] == 'multy_id'){ var_dump($sql_select); }
	
	$db_result = f_db_select($sql_select);
	
	return $db_result;
}
function f_db_select($sql_query=""){
	$db_result = f_db_query($sql_query);
	$rows_arr = array();
	while($row_item = mysqli_fetch_array($db_result, MYSQLI_ASSOC)) {
		$rows_arr[] = $row_item;
	}
	return $rows_arr;
}
function f_db_select_get($table="", $data_json=[], $limit=1, $page=0, $order_json=false){
	
	$table_sql = f_db_sql_column($table);
	
	$limit_sql = intval($limit);
	
	$offset_sql = intval($page * $limit);
	
	if($data_json['_id_str']){
		$data_json['_id'] = f_num_decode($data_json['_id_str']);
		unset($data_json['_id_str']);
	}
	
	$where_arr = ['1'];
    foreach ($data_json as $key => $value) {
		$key_sql = f_db_sql_column($key);
		$value_sql = f_db_sql_value($value);
		$if_sql = f_db_sql_where_if($value);
        $where_arr[] = $key_sql . $if_sql . $value_sql;
    }
	
	$sql_select = "SELECT * FROM " . $table_sql;
	$sql_select .= " WHERE " . implode(' AND ', $where_arr);
	
	
	
	if( f_is_json($order_json) ){
		$order_arr = [];
		foreach ($order_json as $key => $value) {
			$key_sql = f_db_sql_column($key);
			$value_sql = $value == 1 ? ' DESC ' : ' ASC ';
			$order_arr[] = $key_sql . ' ' . $value_sql;
		}
	
		$sql_select .= " ORDER BY ". implode(' , ', $order_arr);
	}
	
	$sql_select .= " LIMIT ". $offset_sql .', '. $limit_sql .";";
	
	$db_result = f_db_select($sql_select);
	
	return $db_result;
}
// Количество
function f_db_select_count($table_name, $sql_where=""){
	$sql_query = "SELECT COUNT(`_id`) as 'total_count' FROM ". f_db_sql_column($table_name) ." WHERE 1 " . $sql_where;
	$count = f_db_select($sql_query)[0]['total_count'];
	return $count;
}
function f_db_update($sql_query=""){
	$db_result = f_db_query($sql_query);
	return $db_result;
}



function f_db_sql_column($name = null) {
    return '`' . preg_replace('/[^a-zA-Z0-9_]/', '', $name) . '`';
}

function f_db_sql_table($name = null) {
    return preg_replace('/[^a-zA-Z0-9_]/', '', $name);
}

function f_db_sql_string_escape($str) {
    return str_replace(
        ["\\", "\0", "\x08", "\x09", "\x1a", "\n", "\r", '"', "%", "'"],
        ["\\\\", "\\0", "\\b", "\\t", "\\z", "\\n", "\\r", '\\"', "\\%", "\\'"],
        $str
    );
}

function f_db_sql_where_if($value = null) {
    if (is_null($value)) {
        return " IS ";
    } elseif (is_numeric($value)) {
        return " = ";
    } elseif (is_array($value) ) {
        return ' IN ';
    } elseif (is_string($value)) {
        return ' LIKE ';
    }
    return " = ";
}

function f_db_sql_value_only($value = null) {
	return trim( f_db_sql_value($value), '"');
}

function f_db_value_str_date($str_date="", $format_date='Y-m-d H:i:s') {
	$timestamp = strtotime($str_date);
	// Проверяем, удалось ли корректно создать объект DateTime
	if ($timestamp === false) {
		// Если дата не валидна, возвращаем NULL
		return NULL;
	} else {
		// Если дата валидна, возвращаем её в формате для БД
		return date($format_date, $timestamp);
	}
}

function f_db_sql_value($value = null) {
	
	if (is_null($value)) {
		return "NULL";
	} elseif (is_int($value) || is_float($value)) {
		return $value;
	} elseif (is_array($value) || is_object($value)) {
		
		// Проверяем, является ли значение массивом с ключами 'lat' и 'lng'
		if (isset($value['lat']) && isset($value['lng'])) {
			// Формируем строку POINT из широты и долготы
			return "ST_PointFromText('POINT(" . $value['lat'] . " " . $value['lng'] . ")')";
		
		}elseif ( $value['_type'] == 'multy_id' ) {
			// value = "1,2,3" - id
			return "(" . (implode(',', array_filter(explode(',', ($value['value'] ?: '')), 'is_numeric')) ?: 'NULL') . ")";
			
		}
		
		return '"' . addslashes(json_encode($value)) . '"';
	}
    return '"' . f_db_sql_string_escape($value) . '"';
}

function f_db_insert($table, $value_json_arr, $column_arr=false, $on_return_full_data=false) {
    $table_sql = f_db_sql_column($table);
	
	if( f_is_json($value_json_arr) ){
		$value_json_arr = [$value_json_arr];
	}
	
	if( $column_arr === false ){
		$column_arr = array_keys($value_json_arr[0]);
	}
    
    $column_arr_sql = array_map('f_db_sql_column', $column_arr);
    
    $value_row_arr = [];
    foreach ($value_json_arr as $json_value) {
        $value_item_arr = [];
        foreach ($column_arr as $name_column) {
            $val = f_db_sql_value($json_value[$name_column]);
            $value_item_arr[] = $val;
        }
        $value_row_arr[] = '(' . implode(',', $value_item_arr) . ')';
    }
    
    $sql_query = "INSERT INTO " . $table_sql . " (" . implode(',', $column_arr_sql) . ") VALUES " . implode(', ', $value_row_arr);
	
	$db_link = f_db_link();
	
	$db_result = mysqli_query($db_link, $sql_query);
	
	if( $db_result == false ){
		$error_query = mysqli_error($db_link);
		mysqli_close($db_link);
        die("Ошибка запроса: " . $error_query);
	}
	
	$new_id = mysqli_insert_id($db_link);
	mysqli_close($db_link);
	
	if( $on_return_full_data == true ){
		$db_result = f_db_select_get($table, ["_id" => $new_id], 1);
		return $db_result;
	}
	
	return $new_id;
}



function f_db_update_smart($table="", $where_json=[], $value_json){
	
	$table_sql = f_db_sql_column($table);
	
	$set_arr = [];
	foreach (array_keys($value_json) as $name_column) {
		$key_sql = f_db_sql_column($name_column);
		$value_sql = f_db_sql_value($value_json[$name_column]);
        $set_arr[] = $key_sql . ' = ' . $value_sql;
    }
	
	$where_arr = [];
    foreach ($where_json as $key => $value) {
		$key_sql = f_db_sql_column($key);
		$value_sql = f_db_sql_value($value);
		$if_sql = f_db_sql_where_if($value);
        $where_arr[] = $key_sql . $if_sql . $value_sql;
    }
	
    $sql_update = "UPDATE " . $table_sql . " SET " . implode(', ', $set_arr) . " WHERE " . implode(' AND ', $where_arr) . ";";
	
	//if( $table == "ads" ){ f_test($sql_update); }
	
	$db_result = f_db_update($sql_update);
	
	return $db_result;
}


function f_db_delete_id($table="", $_id=false){
	
	if( $_id === false ) {
		return false;
	}
	
	$_id = intval($_id);
	
	if( $_id < 1 ) {
		return false;
	}
	
	$table_sql = f_db_sql_column($table);
	
    $sql_delete = "DELETE FROM " . $table_sql . " WHERE `_id` = " . $_id . ";";
	
	//f_test($sql_update);
	
	$db_result = f_db_update($sql_delete);
	
	return $db_result;
}






/*
function f_num_conv($numberInput, $fromBaseInput, $toBaseInput){
    if ($fromBaseInput==$toBaseInput) return $numberInput;
    $fromBase = str_split($fromBaseInput,1);
    $toBase = str_split($toBaseInput,1);
    $number = str_split($numberInput,1);
    $fromLen=strlen($fromBaseInput);
    $toLen=strlen($toBaseInput);
    $numberLen=strlen($numberInput);
    $retval='';
    if ($toBaseInput == '0123456789')
    {
        $retval=0;
        for ($i = 1;$i <= $numberLen; $i++)
            $retval = bcadd($retval, bcmul(array_search($number[$i-1], $fromBase),bcpow($fromLen,$numberLen-$i)));
        return $retval;
    }
    if ($fromBaseInput != '0123456789')
        $base10=convBase($numberInput, $fromBaseInput, '0123456789');
    else
        $base10 = $numberInput;
    if ($base10<strlen($toBaseInput))
        return $toBase[$base10];
    while($base10 != '0')
    {
        $retval = $toBase[bcmod($base10,$toLen)].$retval;
        $base10 = bcdiv($base10,$toLen,0);
    }
    return $retval; 
}

function f_num_encode($num){
	return str_replace("=", "", base64_encode(f_num_conv( $num."", "0123456789", "23456789acefghkmnrstwxyzABCEFGHKLMNPRSTWXYZ")));
}

function f_num_decode($num){
	return (int)(f_num_conv(base64_decode($num), "23456789acefghkmnrstwxyzABCEFGHKLMNPRSTWXYZ", "0123456789"));
}
*/

function f_num_encode($old_num) {
	
	/*
  	$old_num = $old_num."";
  	$num = '';
  	for($i=0; $i < strlen($old_num); $i++){
		$num = $old_num[$i] . $num;
	}
    if(strlen((string)(int)$old_num) != strlen((string)(int)$num)){
		$num = ($old_num);
    }
    $num = intval($num);
    
    $chars = "23456789acefghkmnrstwxyzABCEFGHKLMNPRSTWXYZ";
    $len_chars = strlen($chars);
    $str = "";
    $r = 0;
    while ($num) {
        $r = $num % $len_chars;
        $num -= $r;
        $num /= $len_chars;
        $str = $chars[$r] . $str;
    }
	
    return str_replace("=", "", base64_encode( $str ));
    //return $str;
	*/
	
    return $old_num;
}

function f_num_decode($str) {
	/*
	$str = base64_decode($str);
	
    $chars = "23456789acefghkmnrstwxyzABCEFGHKLMNPRSTWXYZ";
    $str = preg_replace('/[^'.$chars.']/', '', $str);
    $len_chars = strlen($chars);
    $num = 0;
    $r = 0;
    while (strlen($str)) {
        $r = strpos($chars, $str[0]);
        $str = substr($str, 1);
      	$num *= $len_chars;
      	$num += $r;
    }
  	
  	$old_num = $num."";
  	$num = '';
  	for($i=0; $i < strlen($old_num); $i++){
		$num = $old_num[$i] . $num;
	}
    if(strlen((string)(int)$old_num) != strlen((string)(int)$num)){
		$num = ($old_num);
    }
    $num = intval($num);
    return $num;
	*/
	
	return (int)$str;
}



?>