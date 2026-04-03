<?php

/*


	_id
	_create_date
	
	user_id
	
	table
	item_id
	
	moderate_user_id
	moderate_on
	moderate_date
	
	update_user_id
	update_date
	
	delete_on
	delete_date
	delete_user_id
	
	upload_date
	path_date
	basename
	name
	ext
	size
	size_format
	mime
	category
	path_tmp
	hash_sha256
	hash_crc32b
	img_width
	img_height
	img_type
	path_dir
	new_name
	path_file
	
	
CREATE TABLE `upload` (
	`_id` bigint NOT NULL AUTO_INCREMENT,
	`_create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`_create_user_id` bigint DEFAULT NULL,
	`_moderate_user_id` bigint DEFAULT NULL,
	`_moderate_on` tinyint(1) DEFAULT '0',
	`_moderate_date` datetime DEFAULT NULL,
	`_update_user_id` bigint DEFAULT NULL,
	`_update_date` datetime DEFAULT CURRENT_TIMESTAMP,
	`_delete_on` tinyint(1) DEFAULT '0',
	`_delete_date` datetime DEFAULT NULL,
	`_delete_user_id` bigint DEFAULT NULL,
	`_parent_id` bigint DEFAULT NULL,
	`user_id` bigint DEFAULT NULL,
	`item_table` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`item_id` bigint DEFAULT NULL,
	`upload_date` datetime DEFAULT CURRENT_TIMESTAMP,
	`path_date` datetime DEFAULT NULL,
	`basename` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`ext` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`size` bigint DEFAULT '0',
	`size_format` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`mime` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`path_tmp` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`hash_sha256` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`hash_tiger` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`hash_crc32b` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`hash_crc32_int` bigint DEFAULT NULL,
 
	`img_width` int DEFAULT NULL,
	`img_height` int DEFAULT NULL,
	`img_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,

	`img_width_compress` int DEFAULT NULL,
	`img_height_compress` int DEFAULT NULL,
	`img_quality_compress` int DEFAULT NULL,
	`img_px_size_compress` int DEFAULT NULL,
	`img_jpg_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`img_jpg_size` int unsigned DEFAULT NULL,
	`img_webp_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`img_webp_size` int unsigned DEFAULT NULL,

	`metadata_json` json DEFAULT NULL,

	`path_date_str` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`path_dir` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`new_name` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`path_file` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,

	PRIMARY KEY (`_id`),
	KEY `_create_date` (`_create_date`),
	KEY `_create_user_id` (`_create_user_id`),
	KEY `_moderate_user_id` (`_moderate_user_id`),
	KEY `_moderate_on` (`_moderate_on`),
	KEY `_moderate_date` (`_moderate_date`),
	KEY `_update_user_id` (`_update_user_id`),
	KEY `_update_date` (`_update_date`),
	KEY `_delete_on` (`_delete_on`),
	KEY `_delete_date` (`_delete_date`),
	KEY `_delete_user_id` (`_delete_user_id`),
	KEY `_parent_id` (`_parent_id`),
	KEY `path_date` (`path_date`),
	KEY `user_id` (`user_id`),
	KEY `item_table` (`item_table`),
	KEY `item_id` (`item_id`),
	KEY `upload_date` (`upload_date`),
	KEY `mime` (`mime`),
	KEY `hash_sha256` (`hash_sha256`),
	KEY `hash_tiger` (`hash_tiger`),
	KEY `hash_crc32_int` (`hash_crc32_int`),
	KEY `hash_crc32b` (`hash_crc32b`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

*/


// UPLOADER - загрузчик файлов с ПАМЯТЬЮ
ini_set('post_max_size', '2048M'); //  – максимально допустимый размер данных, отправляемых методом POST, его значение должно быть больше upload_max_filesize.
ini_set('memory_limit', '3048M'); //  – значение должно быть больше чем post_max_size.
ini_set('max_uploads', '2'); // – максимальное количество одновременно закачиваемых файлов.
ini_set('upload_max_filesize', '2048M'); // – максимальный размер закачиваемого файла.
/*
На unix хостингах php функция move_uploaded_file() не будут перемещать файлы в директорию если у нее права меньше 777.
Загрузка файлов может быть отключена в настройках PHP директивой uploads.
Не загружаются файлы большого размера, причина в ограничениях хостинга.
Посмотрите в phpinfo() значения директив:
upload_max_filesize – максимальный размер закачиваемого файла.
max_uploads – максимальное количество одновременно закачиваемых файлов.
post_max_size – максимально допустимый размер данных, отправляемых методом POST, его значение должно быть больше upload_max_filesize.
memory_limit – значение должно быть больше чем post_max_size.
*/




// Набор функций
$gl_api_func_json = [
	"file"			=> "f_api_upload_file",
];










// Сохранение картинки
function f_api_upload_file($ARGS){
	
	$response_json = [
		'error' => '',
		'error_code' => 0,
	];
	
	
	
	
	// ===== Получения данных
	$item_id = f_num_decode($ARGS["item_id"]);
	$item_table = f_db_sql_table($ARGS["item_table"]);
	$item_type = $ARGS["item_type"]; // нужно для полученя декларации в WEB_JSON для валидации
	$upload_file = $_FILES['file'];
	$current_date = date('Y-m-d H:i:s');
	// ===== ===== ===== ===== ===== =====
	
	
	
	
	/* 
	$response_json['tmp_1'] = f_user_get()['_id'];
	$response_json['error'] = "TEST";
	$response_json['error_description1'] = $_FILES;
	$response_json['error_description2'] = $_SERVER['REQUEST_METHOD'];
	return $response_json;
	*/
	
	
	// ===== Валидация загрузки
	// Проверяем, были ли отправлены файлы
	if ($_SERVER['REQUEST_METHOD'] != 'POST' && empty($upload_file)) {
		$response_json['error'] = 'File not found';
		return $response_json;
	}
	
    // Обработка ошибок загрузки
    if ($upload_file['error'] !== UPLOAD_ERR_OK) {
		$response_json['error'] = 'Bad upload';
		$response_json['error_code'] = 1;
		
		$response_json['error_description'] = f_upload_error( $upload_file );
		
		return $response_json;
    }
	
	// Проверяем что это один файл
	if ( gettype($upload_file['name']) == 'array' ){
		$response_json['error'] = 'Several files are specified';
		return $response_json;
	}
	
	// Проверка Декларации
	$declaratio_upload = $GLOBALS['WEB_JSON']['upload_json'][$item_table][$item_type];
	if ( !isset( $declaratio_upload ) ){
		$response_json['error'] = 'File upload declaration not found';
		$response_json['error_code'] = 1;
		return $response_json;
	}
	
	// Проверка Декларации MIME
	if ( !preg_match($declaratio_upload['mime_regex'], $upload_file['type']) ){
		$response_json['error'] = 'Mime file is not suitable';
		$response_json['error_code'] = 1;
		return $response_json;
	}
	
	// Проверка Декларации FILESIZE
	if ( $upload_file['size'] > $declaratio_upload['file_size_max'] ){
		$response_json['error'] = 'File size exceeds ' . f_byte_format( $declaratio_upload['file_size_max'] );
		$response_json['error_code'] = 1;
		return $response_json;
	}
	
	$img_compress_on = $declaratio_upload['img_compress_on'] ?: false;
	$img_min_px_size = $declaratio_upload['img_min_px_size'];
	$img_max_px_size = $declaratio_upload['img_max_px_size'];
	$img_compress_px_size = $declaratio_upload['img_compress_px_size'];
	$img_compress_quality = $declaratio_upload['img_compress_quality'];
	$img_thumb_compress_px_size = $declaratio_upload['img_thumb_compress_px_size'];
	$img_thumb_compress_quality = $declaratio_upload['img_thumb_compress_quality'];
	
	// Проверка, регистрации пользователя
	if( !f_user_get() ){
		$response_json['error'] = 'No access';
		$response_json['error_code'] = 1;
		return $response_json;
	}
	
	// Проверка родительской записи в БД для прикрепления
	$item_json = f_db_select_id($item_table, $item_id);
	
	if( !isset($item_json) ){
		$response_json['error'] = 'No record found';
		$response_json['error_code'] = 1;
		return $response_json;
	}

	if( $item_table === 'user' && (int)$item_id !== (int)f_user_get()['_id'] ){
		$response_json['error'] = 'No access for you';
		$response_json['error_code'] = 1;
		return $response_json;
	}
	
	// Проверка привзяки Записи к пользователю
	/*
	if( $item_json['user_id'] != f_user_get()['_id'] ){
		$response_json['error'] = 'No access for you';
		$response_json['error_code'] = 1;
		return $response_json;
	}
	*/
	// ===== ===== ===== ===== ===== =====
	
	
	
	
	
	// ===== Получения информации
	
	// Шаблон записи для БД
	$file_json = [
		//'_id',
		'_create_date'			=> $current_date,
		'_create_user_id'		=> f_user_get()['_id'],
		'user_id'				=> f_user_get()['_id'],
		'item_table'			=> $item_table,
		'item_id'				=> $item_id,
		//'_moderate_user_id',
		//'_moderate_on',
		//'_moderate_date',
		'_update_user_id'		=> f_user_get()['_id'],
		'_update_date'			=> $current_date,
		'_parent_id'			=> NULL,
		//'_delete_on',
		//'_delete_date',
		//'_delete_user_id',
		'upload_date'			=> $current_date,
		'basename'				=> NULL,
		'name'					=> NULL,
		'ext'					=> NULL,
		'size'					=> NULL,
		'size_format'			=> NULL,
		'mime'					=> NULL,
		'category'				=> NULL,
		'path_tmp'				=> NULL,
		'hash_sha256'			=> NULL,
		'hash_tiger'			=> NULL,
		'hash_crc32b'			=> NULL,
		'hash_crc32_int'		=> NULL,
		'metadata_json'			=> NULL,

		'img_width'				=> NULL,
		'img_height'			=> NULL,
		'img_type'				=> NULL,
		
		'img_quality_compress'	=> NULL,
		'img_px_size_compress'	=> NULL,
		'img_width_compress'	=> NULL,
		'img_height_compress'	=> NULL,
		'img_jpg_path'			=> NULL,
		'img_jpg_size'			=> NULL,
		'img_webp_path'			=> NULL,
		'img_webp_size'			=> NULL,
		
		'img_thumb_quality_compress'	=> NULL,
		'img_thumb_px_size_compress'	=> NULL,
		'img_thumb_width_compress'		=> NULL,
		'img_thumb_height_compress'		=> NULL,
		'img_thumb_jpg_path'			=> NULL,
		'img_thumb_jpg_size'			=> NULL,
		'img_thumb_webp_path'			=> NULL,
		'img_thumb_webp_size'			=> NULL,

		'path_date'				=> NULL,
		'path_date_str'			=> NULL,
		'path_dir'				=> NULL,
		'path_file'				=> NULL,
	];
	
	// Определение расширения на основе точек
	$ext = explode('.', $upload_file['name']);
	$ext = count( $ext ) == 1 ? '' : end($ext);
	
	$file_json['name'] = $upload_file['name'];
	$file_json['basename'] = basename($upload_file['name']);
	$file_json['ext'] = $ext;
	$file_json['size'] = filesize($upload_file['tmp_name']);
	$file_json['size_format'] = f_byte_format( $file_json['size'] );
	$file_json['mime'] = $upload_file['type'];
	
	$file_json['category'] = f_file_category( $file_json['mime'] );
	$file_json['path_tmp'] = $upload_file['tmp_name'];
	
	$file_json['hash_sha256'] = hash_file('sha256', $upload_file['tmp_name']);
	$file_json['hash_crc32b'] = hash_file('crc32b', $upload_file['tmp_name']);
	$file_json['hash_tiger'] = hash_file('tiger', $upload_file['tmp_name']);
	$file_json['hash_crc32_int'] = crc32(file_get_contents( $upload_file['tmp_name'] ));
	
	$file_json['new_name'] = $file_json['hash_sha256'] . ( $ext ? '.'.$ext : '');
	
	$file_json['path_date'] = $current_date;
	$file_json['path_date_str'] = date('/Y/m/d/H/i', strtotime($current_date));
	$file_json['path_dir'] = $GLOBALS['WEB_JSON']['dir_upload_file'] . $file_json['path_date_str'];
	$file_json['path_file'] = $file_json['path_dir'] . '/' . $file_json['new_name'] ;
	
	$file_json['metadata_json'] = [];
	
	$is_image_jpg_png = false;
	$file_json['img_type'] = null;
	if ( in_array($file_json['mime'], ['image/jpeg', 'image/png'] ) ) {
		$is_image_jpg_png = true;
		$tmp_image_size = getimagesize( $file_json['path_tmp'] );
		$file_json['img_width'] = $tmp_image_size[0];
		$file_json['img_height'] = $tmp_image_size[1];
		$file_json['img_type'] = $tmp_image_size[2];
		
		$file_json['path_dir'] = $GLOBALS['WEB_JSON']['dir_upload_img'] . '/orig' . $file_json['path_date_str'];
		$file_json['path_file'] = $file_json['path_dir'] . '/' . $file_json['new_name'];
	}
	
	// ===== ===== ===== ===== ===== =====
	
	
	
	
	
	

	// ===== Поиск дубликата файла в БД по хэшу
	$is_dublicate = false;
	$parent_item = f_db_select_smart("upload", ["_parent_id" => NULL, "hash_crc32_int" => $file_json['hash_crc32_int'], "hash_sha256" => $file_json['hash_sha256']])[0];
	//$response_json['asds'] = $parent_item;
	if( isset( $parent_item ) ){ // Проверка дубликата в базе
		if ( file_exists( $parent_item['path_file'] ) ) { // Проверка сущестовования файла
			$is_dublicate = true;
			$file_json = $parent_item;
			unset( $file_json['_id'] );
			$file_json['user_id'] = f_user_get()['_id'];
			$file_json['_create_date'] = $current_date;
			$file_json['_update_date'] = $current_date;
			$file_json['_create_user_id'] = f_user_get()['_id'];
			$file_json['_update_user_id'] = f_user_get()['_id'];
			$file_json['item_table'] = $file_json['item_table'];
			$file_json['item_id'] = $file_json['item_id'];
			$file_json['_parent_id'] = $parent_item['_id'];
			
			// Проверка
			if ( $is_image_jpg_png && $img_compress_on == true ){
				
			}
		}
	}
	
	// ===== ===== ===== ===== ===== =====
	
	
	
	// ===== Каринки - сохранение и сжатие картинки + формирования JPG, WEBP
	if( $is_image_jpg_png && $img_compress_on == true && $is_dublicate == false){
		
		if ($file_json['img_width'] > $img_max_px_size || $file_json['img_height'] > $img_max_px_size ) {
			$result_json['error'] = 'Image resolution is more than ' . $img_max_px_size . ' px';
			return $result_json;
		}
		
		if ($file_json['img_width'] < $img_min_px_size || $file_json['img_height'] < $img_min_px_size ) {
			$result_json['error'] = 'Image resolution is less than ' . $img_min_px_size . ' px';
			return $result_json;
		}
		
		$img_save_json = f_image_compress_save($upload_file, $file_json['path_date'], $img_compress_px_size, $img_compress_quality, $img_thumb_compress_px_size, $img_thumb_compress_quality);
		if ( $img_save_json['error'] !== "" ) {
			$response_json['error'] = 'Error saving image';
			$response_json['error_code'] = 1;
			$response_json['error_description'] = $img_save_json['error'];
			return $response_json;
		}
		
		
		$file_json['img_px_size_compress'] = $img_compress_px_size;
		$file_json['img_quality_compress'] = $img_compress_quality;
		$file_json['img_jpg_path'] = $img_save_json['img_jpg_path'];
		$file_json['img_jpg_size'] = $img_save_json['img_jpg_size'];
		$file_json['img_webp_path'] = $img_save_json['img_webp_path'];
		$file_json['img_webp_size'] = $img_save_json['img_webp_size'];
		$file_json['img_width_compress'] = $img_save_json['img_width_compress'];
		$file_json['img_height_compress'] = $img_save_json['img_height_compress'];
		
		$file_json['img_thumb_px_size_compress'] = $img_thumb_compress_px_size;
		$file_json['img_thumb_quality_compress'] = $img_thumb_compress_quality;
		$file_json['img_thumb_jpg_path'] = $img_save_json['img_thumb_jpg_path'];
		$file_json['img_thumb_jpg_size'] = $img_save_json['img_thumb_jpg_size'];
		$file_json['img_thumb_webp_path'] = $img_save_json['img_thumb_webp_path'];
		$file_json['img_thumb_webp_size'] = $img_save_json['img_thumb_webp_size'];
		$file_json['img_thumb_width_compress'] = $img_save_json['img_thumb_width_compress'];
		$file_json['img_thumb_height_compress'] = $img_save_json['img_thumb_height_compress'];
		
		
	
	// ===== Файлы - сохранение
	} else if ( $is_dublicate == false ){
	
		if (!is_dir( $file_json['path_dir'] )) {
			mkdir( $file_json['path_dir'], 0777, true);
		}
		
		if ( !move_uploaded_file( $upload_file['tmp_name'], $file_json['path_file'] ) ) {
			$result_json['error'] = 'Error saving file';
			return $result_json;
		}
		
	}
	
	// ===== ===== ===== ===== ===== =====
	
	if( $is_image_jpg_png && $img_compress_on == true ){
		$response_json['link_jpg'] = 'https://'. $_SERVER['HTTP_HOST'] . explode('public_html', $GLOBALS['WEB_JSON']['dir_upload_img'].$file_json['img_jpg_path'])[1];
		$response_json['link_webp'] = 'https://'. $_SERVER['HTTP_HOST'] . explode('public_html', $GLOBALS['WEB_JSON']['dir_upload_img'].$file_json['img_webp_path'])[1];
	}
	
	$response_json['_id'] = f_db_insert('upload', $file_json);
	$response_json['tmp_file_json'] = $file_json;
	$response_json['link_orig'] = 'https://'. $_SERVER['HTTP_HOST'] . explode('public_html', $file_json['path_file'])[1];
	
	//$file_data['file_link'] = 'https://'. $_SERVER['HTTP_HOST'] . f_file_gen_link( $file_json['path_file'] );
	
	return $response_json;
}




function f_upload_error( $file_upload_json ){
	$error = "";
	switch ($file_upload_json['error']) {
		case UPLOAD_ERR_INI_SIZE:
			$error = 'File size exceeds the maximum allowed size set in php.ini';
		case UPLOAD_ERR_FORM_SIZE:
			$error = 'File size exceeds the maximum allowed size set in the HTML form';
		case UPLOAD_ERR_PARTIAL:
			$error = 'File was only partially uploaded';
		case UPLOAD_ERR_NO_FILE:
			$error = 'File was not uploaded';
		case UPLOAD_ERR_NO_TMP_DIR:
			$error = 'There is no temporary folder for uploading files';
		case UPLOAD_ERR_CANT_WRITE:
			$error = 'Error writing a file to disk';
		case UPLOAD_ERR_EXTENSION:
			$error = 'File download stopped due to invalid extension';
		default:
			$error = 'Unknown file upload error';
	}
	return $error;
}


function f_image_compress_save($file_upload_json, $upload_date_str, $img_max_px_size=1000, $img_quality=80, $img_thumb_max_px_size=300, $img_thumb_quality=80){
	
	$result_json = [
		'error' => '',
		
        'img_jpg_path' => '',
        'img_jpg_filesize' => 0,
		
        'img_webp_path' => '',
        'img_webp_filesize' => 0,
		
        'img_width_compress' => 0,
        'img_height_compress' => 0,
		
		
        'img_thumb_jpg_path' => '',
        'img_thumb_jpg_filesize' => 0,
		
        'img_thumb_webp_path' => '',
        'img_thumb_webp_filesize' => 0,
		
        'img_thumb_width_compress' => 0,
        'img_thumb_height_compress' => 0
    ];

    // Получение размеров исходного изображения
    $image_info = getimagesize($file_upload_json['tmp_name']);
    if ($image_info === false) {
		$result_json['error'] = 'Failed to get information about the image';
        return $result_json;
    }
    $orig_width = $image_info[0];
    $orig_height = $image_info[1];

    // Определение новых размеров с сохранением пропорций
    if ($orig_width > $orig_height) {
        $compress_width = min($orig_width, $img_max_px_size);
        $compress_height = ($orig_height * $compress_width) / $orig_width;
		
        $compress_thumb_width = min($orig_width, $img_thumb_max_px_size);
        $compress_thumb_height = ($orig_height * $compress_thumb_width) / $orig_width;
		
    } else {
        $compress_height = min($orig_height, $img_max_px_size);
        $compress_width = ($orig_width * $compress_height) / $orig_height;
		
        $compress_thumb_height = min($orig_height, $img_thumb_max_px_size);
        $compress_thumb_width = ($orig_width * $compress_thumb_height) / $orig_height;
    }

    // Создание ресурсов изображений
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($file_upload_json['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($file_upload_json['tmp_name']);
            break;
        default:
			$result_json['error'] = 'Unsupported image type';
			return $result_json;
    }

    if (!$source_image) {
		$result_json['error'] = 'Error when creating an image resource';
        return $result_json;
    }

    // Создание нового изображения с измененными размерами
    $resized_image = imagecreatetruecolor($compress_width, $compress_height);
    $resized_thumb_image = imagecreatetruecolor($compress_thumb_width, $compress_thumb_height);

    // Сохранение альфа-канала для PNG
    if ($image_info[2] === IMAGETYPE_PNG) {
		foreach ([$resized_image, $resized_thumb_image] as $image) {
			imagealphablending($image, false);
			imagesavealpha($image, true);
			$transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
			imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $transparent);
		}
    }
	
    // Изменение размера изображения
    imagecopyresampled($resized_image, $source_image, 0, 0, 0, 0, $compress_width, $compress_height, $orig_width, $orig_height);
    imagecopyresampled($resized_thumb_image, $source_image, 0, 0, 0, 0, $compress_thumb_width, $compress_thumb_height, $orig_width, $orig_height);

    // Создание путей для сохранения изображений
	$date_path = date('Y/m/d/H/i', strtotime($upload_date_str));
	
	$upload_dir = $GLOBALS['WEB_JSON']['dir_upload_img'];
	
    $orig_dir = "/orig/" . $date_path;
    $jpg_dir = "/jpg/". $img_max_px_size ."px/". $date_path;
    $webp_dir = "/webp/". $img_max_px_size ."px/" . $date_path;
    $jpg_thumb_dir = "/jpg/". $img_thumb_max_px_size ."px/". $date_path;
    $webp_thumb_dir = "/webp/". $img_thumb_max_px_size ."px/" . $date_path;

    if (!is_dir( $upload_dir . $orig_dir)) {
        mkdir( $upload_dir . $orig_dir, 0777, true);
    }
    if (!is_dir( $upload_dir . $jpg_dir)) {
        mkdir( $upload_dir . $jpg_dir, 0777, true);
    }
    if (!is_dir( $upload_dir . $webp_dir)) {
        mkdir( $upload_dir . $webp_dir, 0777, true);
    }
    if (!is_dir( $upload_dir . $jpg_thumb_dir)) {
        mkdir( $upload_dir . $jpg_thumb_dir, 0777, true);
    }
    if (!is_dir( $upload_dir . $webp_thumb_dir)) {
        mkdir( $upload_dir . $webp_thumb_dir, 0777, true);
    }
	
	
    // Определение расширения на основе точек
    $ext = explode('.', $file_upload_json['name']);
	$ext = count( $ext ) == 1 ? '' : end($ext);
	
	$hash_sha256 = hash_file('sha256', $file_upload_json['tmp_name']);

    $orig_path = $orig_dir .'/'. $hash_sha256 . ( $ext ? '.'.$ext : '');
    $jpg_path = $jpg_dir .'/'. $hash_sha256 . '.jpg';
    $webp_path = $webp_dir .'/'. $hash_sha256 . '.webp';
    $jpg_thumb_path = $jpg_thumb_dir .'/'. $hash_sha256 . '.jpg';
    $webp_thumb_path = $webp_thumb_dir .'/'. $hash_sha256 . '.webp';

    // Сохранение изображений
	/*
	$move_status = move_uploaded_file( $file_upload_json['tmp_name'], $upload_dir . $orig_path);
	if ( !$move_status ) {
		$result_json['error'] = 'Error when saving an image';
        return $result_json;
    }
	*/
	$copy_status = copy($file_upload_json['tmp_name'], $upload_dir . $orig_path);
	if (!$copy_status) {
		$result_json['error'] = 'Error when copying the image';
		return $result_json;
	}
	
	// Удаление временного файла
	if (file_exists($file_upload_json['tmp_name'])) {
		unlink($file_upload_json['tmp_name']);
	}
	
	
    if (!imagejpeg($resized_image, $upload_dir . $jpg_path, $img_quality)) {
		$result_json['error'] = 'Error when saving an image in JPG format';
        return $result_json;
    }
    if (!imagewebp($resized_image, $upload_dir . $webp_path, $img_quality)) {
		$result_json['error'] = 'Error when saving an image in WEBP format';
        return $result_json;
    }
    if (!imagejpeg($resized_thumb_image, $upload_dir . $jpg_thumb_path, $img_thumb_quality)) {
		$result_json['error'] = 'Error when saving an thumb image in JPG format';
        return $result_json;
    }
    if (!imagewebp($resized_thumb_image, $upload_dir . $webp_thumb_path, $img_thumb_quality)) {
		$result_json['error'] = 'Error when saving an thumb image in WEBP format';
        return $result_json;
    }

    // Освобождение ресурсов
    imagedestroy($source_image);
    imagedestroy($resized_image);
    imagedestroy($resized_thumb_image);
	
    $result_json['img_path'] = $orig_path;
    $result_json['img_jpg_path'] = $jpg_path;
    $result_json['img_jpg_size'] = filesize($upload_dir . $jpg_path);
    $result_json['img_webp_path'] = $webp_path;
    $result_json['img_webp_size'] = filesize($upload_dir . $webp_path);
    $result_json['img_width_compress'] = $compress_width;
    $result_json['img_height_compress'] = $compress_height;
	
    $result_json['img_thumb_jpg_path'] = $jpg_thumb_path;
    $result_json['img_thumb_jpg_size'] = filesize($upload_dir . $jpg_thumb_path);
    $result_json['img_thumb_webp_path'] = $webp_thumb_path;
    $result_json['img_thumb_webp_size'] = filesize($upload_dir . $webp_thumb_path);
    $result_json['img_thumb_width_compress'] = $compress_thumb_width;
    $result_json['img_thumb_height_compress'] = $compress_thumb_height;
	
	return $result_json;
}



function f_file_category($mime_type) {
    $types = [
		'document' => [
            'application/pdf',
            'application/json',
            'application/xhtml+xml',
            'application/xml-dtd',
            'application/xop+xml',
		
		],
		'code' => [
            'application/javascript',
		
		],
		'archive' => [
            'application/zip',
            'application/gzip',
		],
        'application' => [
            // Список MIME-типов для приложений
            'application/octet-stream',
            'application/ogg',
            // и многие другие...
        ],
        'audio' => [
            // Список MIME-типов для аудио
            'audio/basic',
            'audio/L24',
            'audio/mp4',
            'audio/aac',
            'audio/mpeg',
            'audio/ogg',
            'audio/vorbis',
            'audio/x-ms-wma',
            'audio/x-ms-wax',
            'audio/vnd.rn-realaudio',
            // и многие другие...
        ],
        'image' => [
            // Список MIME-типов для изображений
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/svg+xml',
            'image/tiff',
            'image/vnd.microsoft.icon',
            'image/vnd.wap.wbmp',
            'image/webp',
            // и многие другие...
        ],
        'message' => [
            // Список MIME-типов для сообщений
            'message/http',
            'message/imdn+xml',
            'message/partial',
            'message/rfc822',
            // и многие другие...
        ],
        'model' => [
            // Список MIME-типов для 3D моделей
            'model/example',
            'model/iges',
            'model/mesh',
            'model/vrml',
            'model/x3d+binary',
            'model/x3d+vrml',
            // и многие другие...
        ],
        'multipart' => [
            // Список MIME-типов для многокомпонентных данных
            'multipart/mixed',
            'multipart/alternative',
            'multipart/related',
            'multipart/form-data',
            'multipart/signed',
            'multipart/encrypted',
            // и многие другие...
        ],
        'text' => [
            // Список MIME-типов для текста
            'text/css',
            'text/csv',
            'text/html',
            'text/javascript', // JavaScript файлы
            'text/plain', // Текстовые файлы
            'text/xml', // XML файлы
            // и многие другие...
        ],
        'video' => [
            // Список MIME-типов для видео
            'video/mpeg',
            'video/mp4',
            'video/quicktime',
            'video/webm',
            'video/x-msvideo', // AVI файлы
            'video/x-ms-wmv', // Windows Media Video файлы
            // и многие другие...
        ],
        // Добавьте другие категории и соответствующие им MIME-типы
    ];

    $type = 'other'; // По умолчанию тип файла - 'other'

    foreach ($types as $key => $value) {
        if (in_array($mime_type, $value)) {
            $type = $key;
            break;
        }
    }

    return $type;
}




?>
