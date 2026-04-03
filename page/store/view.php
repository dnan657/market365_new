<?php

$raw_slug = isset($WEB_JSON['uri_dir_arr'][1]) ? (string)$WEB_JSON['uri_dir_arr'][1] : '';
$slug = strtolower(trim($raw_slug));
$slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug);
$slug = trim($slug, '-');

$store_row = null;
if( $slug !== '' && f_db_table_exists('store') ){
	$sr = f_db_select(
		'SELECT s.*, u.`name` AS `owner_name` FROM `store` s
		LEFT JOIN `user` u ON u.`_id` = s.`user_id`
		WHERE s.`slug` = ' . f_db_sql_value($slug) . ' LIMIT 1'
	);
	if( !empty($sr) ){
		$store_row = $sr[0];
	}
}

if( !$store_row ){
	header('HTTP/1.1 404 Not Found');
	require($WEB_JSON['dir_page_tools'] . '404.php');
	return;
}

$name = (string)($store_row['name'] ?? '');
f_page_title_set($name);

$logo_url = '';
$banner_url = '';
if( !empty($store_row['logo_upload_id']) ){
	$upr = f_db_select_get('upload', ['_id' => intval($store_row['logo_upload_id'])], 1);
	if( !empty($upr[0]) ){
		$logo_url = f_db_ads_img_public_url((string)($upr[0]['img_jpg_path'] ?? ''), '');
	}
}
if( !empty($store_row['banner_upload_id']) ){
	$upr = f_db_select_get('upload', ['_id' => intval($store_row['banner_upload_id'])], 1);
	if( !empty($upr[0]) ){
		$banner_url = f_db_ads_img_public_url((string)($upr[0]['img_jpg_path'] ?? ''), '');
	}
}

?>

<div class="container">
	<?php if( $banner_url !== '' && $banner_url !== '/public/ad_default.jpg' ){ ?>
		<div class="rounded overflow-hidden mb-3" style="max-height:220px;">
			<img src="<?php f_echo_html($banner_url); ?>" alt="" class="w-100 object-fit-cover" style="max-height:220px;object-fit:cover;">
		</div>
	<?php } ?>

	<div class="d-flex flex-wrap gap-3 align-items-start mb-4">
		<?php if( $logo_url !== '' && $logo_url !== '/public/ad_default.jpg' ){ ?>
			<img src="<?php f_echo_html($logo_url); ?>" alt="" class="rounded border" style="width:96px;height:96px;object-fit:cover;">
		<?php } ?>
		<div>
			<h1 class="h3 mb-1"><?php f_echo_html($name); ?></h1>
			<?php if( trim((string)($store_row['description'] ?? '')) !== '' ){ ?>
				<p class="text-muted mb-0"><?php echo nl2br(f_html(trim($store_row['description']))); ?></p>
			<?php } ?>
			<?php if( trim((string)($store_row['phone'] ?? '')) !== '' ){ ?>
				<p class="small mb-0 mt-2"><i class="bi bi-telephone"></i> <?php f_echo_html($store_row['phone']); ?></p>
			<?php } ?>
		</div>
	</div>

	<h2 class="h5 mb-3"><?php f_translate_echo('Объявления магазина'); ?></h2>
	<div class="row g-3" id="store_ads_grid"></div>
	<p class="text-muted d-none mt-3" id="store_ads_empty"><?php f_translate_echo('Нет активных объявлений'); ?></p>
</div>

<script>
$(function () {
	var slug = <?php echo json_encode($slug, JSON_UNESCAPED_UNICODE); ?>;
	f_ajax('store', 'get_list', { slug: slug }, function (res) {
		res = res.data || {};
		var $g = $('#store_ads_grid');
		$g.empty();
		if (res.error) {
			$('#store_ads_empty').removeClass('d-none').text(res.error);
			return;
		}
		var arr = res.arr_item || [];
		if (!arr.length) {
			$('#store_ads_empty').removeClass('d-none');
			return;
		}
		arr.forEach(function (item) {
			var col = $('<div class="col-md-6 col-lg-4"></div>');
			col.append(f_ads_item_line_make(item));
			$g.append(col);
		});
	});
});
</script>
