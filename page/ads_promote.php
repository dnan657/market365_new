<?php

$uri_seg = $WEB_JSON['uri_dir_arr'][2] ?? '';
$uri_parts = explode('-', $uri_seg);
$ad_id = intval(end($uri_parts));

$ad_row = [];
if( $ad_id > 0 ){
	$ad_row = f_db_select(
		'SELECT `_id`, `title`, `user_id`, `delete_on` FROM `ads` WHERE `_id` = ' . $ad_id . ' LIMIT 1'
	);
}
$ad_ok = !empty($ad_row) && intval($ad_row[0]['delete_on'] ?? 1) === 0;
$ad = $ad_ok ? $ad_row[0] : [];

$me = f_user_get();
$is_logged = is_array($me) && !empty($me['_id']);
$is_admin = $is_logged && (($me['type'] ?? '') === 'admin');
$is_owner = $is_logged && $ad_ok && intval($ad['user_id'] ?? 0) === intval($me['_id']);

if( !$ad_ok ){
	header('HTTP/1.1 404 Not Found');
	$title_page = f_translate('Ad not found');
	f_page_title_set($title_page);
	echo '<div class="container py-5"><p class="text-muted">' . f_html(f_translate('Объявление не найдено')) . '</p>';
	echo '<a class="btn btn-outline-primary" href="' . f_html(f_page_link('user_ads')) . '">' . f_html(f_translate('Мои объявления')) . '</a></div>';
	return;
}

if( !$is_owner && !$is_admin ){
	f_redirect(f_page_link('login'));
}

$item_json = $ad;
$title_page = f_translate('Promote ad');
f_page_title_set($title_page . ' — ' . (string)($ad['title'] ?? ''));

$pay_add_top = f_page_link('user_pays_add') . '?ads_id=' . intval($ad['_id']) . '&service_type=top';
$pay_add_vip = f_page_link('user_pays_add') . '?ads_id=' . intval($ad['_id']) . '&service_type=vip';

?>

<style>
.item_section{
	background: var(--v_c_white);
	padding: var(--v_p_20);
	margin-bottom: var(--v_p_20);
	border-radius: var(--v_radius);
}
.service_item {
	display: flex;
	align-items: center;
	justify-content: space-between;
	border: 1px solid #ddd;
	border-radius: 8px;
	padding: 15px;
	background-color: #fff;
	margin-bottom: 10px;
	gap: 10px;
}
.service_item .service_title { font-weight: 500; }
.service_item .price { font-weight: bold; text-align: right; }
@media (max-width: 1000px) {
	.service_item { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="container">

	<div class="head_page mb-3 pb-2">
		<a class="back_head_page btn btn-outline-dark" href="<?php f_echo_html($is_admin ? f_page_link('admin_ads_list') : f_page_link('user_ads')); ?>">
			<i class="bi bi-chevron-left"></i>
		</a>
		<h1 class="title_head_page">
			<?php f_echo_html($title_page); ?>
			<?php if( $is_admin ){ ?> — #<?php f_echo_html(f_num_encode($item_json['_id'])); ?><?php } ?>
		</h1>
	</div>

	<p class="text-muted mb-3"><?php f_echo_html((string)($ad['title'] ?? '')); ?></p>

	<div class="item_section">
		<div class="sub_title_head_page mb-3"><?php f_translate_echo('Платное продвижение'); ?></div>

		<div class="service_item">
			<div>
				<div class="service_title"><?php f_translate_echo('TOP — 7 дней'); ?></div>
				<div class="small text-muted"><?php f_translate_echo('Объявление выше в списке поиска'); ?></div>
			</div>
			<div class="price">£4.99</div>
			<div>
				<a class="btn btn-primary" href="<?php f_echo_html($pay_add_top); ?>"><?php f_translate_echo('Оплатить'); ?></a>
			</div>
		</div>

		<div class="service_item">
			<div>
				<div class="service_title"><?php f_translate_echo('VIP — 30 дней'); ?></div>
				<div class="small text-muted"><?php f_translate_echo('Выделение карточки в ленте'); ?></div>
			</div>
			<div class="price">£9.99</div>
			<div>
				<a class="btn btn-primary" href="<?php f_echo_html($pay_add_vip); ?>"><?php f_translate_echo('Оплатить'); ?></a>
			</div>
		</div>
	</div>

	<div class="item_section">
		<a href="<?php f_echo_html(f_page_link('user_ads')); ?>" class="btn btn-outline-dark"><?php f_translate_echo('Отмена'); ?></a>
	</div>
</div>
