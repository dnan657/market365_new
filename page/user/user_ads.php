<?php

$title_page = f_translate('My Ads');
f_page_title_set($title_page);

$me = f_user_get();
$uid = is_array($me) && !empty($me['_id']) ? intval($me['_id']) : 0;

$rows = [];
if( $uid > 0 ){
	$rows = f_db_select(
		'SELECT * FROM `ads` WHERE `user_id` = ' . $uid . ' AND `delete_on` = 0 ORDER BY `_create_date` DESC LIMIT 100'
	);
}

?>

<div class="container">
	
	<?php f_template('user_info_box'); ?>
	
	<?php if( empty($rows) ){ ?>
		<p class="text-muted mt-3"><?php f_translate_echo('У вас пока нет объявлений'); ?></p>
		<a class="btn btn-primary" href="<?php f_echo_html(f_page_link('ads_create')); ?>"><?php f_translate_echo('Разместить объявление'); ?></a>
	<?php } else { ?>
	<div class="list_line_ads mt-3">
		<?php foreach( $rows as $ad ){
			$ad_id = intval($ad['_id'] ?? 0);
			$title = (string)($ad['title'] ?? '');
			$slug = f_seo_text_to_url($title, 100);
			$link_item = f_page_link('ads_item') . '/' . $slug . '-' . $ad_id;
			$link_promote = f_page_link('ads_promote') . '/' . $slug . '-' . $ad_id;
			$img_row = f_db_select(
				'SELECT `jpg_path`, `webp_path` FROM `ads_img` WHERE `ads_id` = ' . $ad_id . ' ORDER BY `_id` ASC LIMIT 1'
			);
			$img_src = '/public/ad_default.jpg';
			if( !empty($img_row[0]) ){
				$img_src = f_db_ads_img_public_url((string)($img_row[0]['jpg_path'] ?? ''), (string)($img_row[0]['webp_path'] ?? ''));
			}
			$city_title = '';
			if( !empty($ad['city_id']) ){
				$cr = f_db_select_get('city', ['_id' => intval($ad['city_id'])], 1);
				if( !empty($cr[0]['title_en']) ){
					$city_title = (string)$cr[0]['title_en'];
				}
			}
			$price = isset($ad['price']) ? floatval($ad['price']) : 0;
			$curr = trim((string)($ad['price_currency'] ?? ''));
			if( $curr === '' ){
				$curr = f_page_currency();
			}
			$html_date = f_html_date_to_last_day((string)($ad['_create_date'] ?? ''));
			$pub_label = intval($ad['publication_on'] ?? 0) === 1 ? f_translate('Опубликовано') : f_translate('Черновик');
			$now_ts = time();
			$promo_line = '';
			if( !empty($ad['is_top_until']) && strtotime((string)$ad['is_top_until']) > $now_ts ){
				$promo_line = f_translate('TOP until') . ' ' . f_datetime_beauty((string)$ad['is_top_until']);
			}elseif( !empty($ad['is_vip_until']) && strtotime((string)$ad['is_vip_until']) > $now_ts ){
				$promo_line = f_translate('VIP until') . ' ' . f_datetime_beauty((string)$ad['is_vip_until']);
			}
		?>
			<div class="item_ad mb-3 border rounded overflow-hidden bg-white">
				<a href="<?php f_echo_html($link_item); ?>" class="body_item_ad text-decoration-none text-dark d-flex flex-wrap">
					<img class="img_item_ad flex-shrink-0" src="<?php f_echo_html($img_src); ?>" alt="" style="width:140px;height:105px;object-fit:cover;">
					<div class="text_item_ad p-3 flex-grow-1">
						<div class="title_item_ad fw-medium mb-1"><?php f_echo_html($title); ?></div>
						<div class="small text-muted mb-1"><?php f_echo_html($pub_label); ?></div>
						<?php if( $promo_line !== '' ){ ?>
							<div class="small text-warning mb-1"><?php f_echo_html($promo_line); ?></div>
						<?php } ?>
						<div class="price_item_ad fw-bold"><?php f_echo_html(f_number_space($price) . ' ' . $curr); ?></div>
						<?php if( $city_title !== '' ){ ?>
							<div class="city_item_ad small text-muted"><i class="bi bi-geo-alt me-1"></i><?php f_echo_html($city_title); ?></div>
						<?php } ?>
						<div class="date_item_ad small text-muted"><i class="bi bi-calendar-week me-1"></i><?php f_echo_html($html_date); ?></div>
					</div>
				</a>
				<div class="settings_item_ad border-top px-3 py-2 bg-light d-flex flex-wrap gap-2 align-items-center">
					<div class="id_settings_item_ad small text-muted">ID: <?php f_echo_html((string)$ad_id); ?></div>
					<div class="ms-auto btn_settings_item_ad d-flex flex-wrap gap-2">
						<a href="<?php f_echo_html($link_promote); ?>" class="btn btn-warning btn-sm"><i class="bi bi-capslock-fill"></i> <?php f_translate_echo('Promote'); ?></a>
						<a href="<?php f_echo_html($link_item); ?>" class="btn btn-dark btn-sm"><i class="bi bi-pencil-square"></i> <?php f_translate_echo('View / Edit'); ?></a>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php } ?>
	
</div>
