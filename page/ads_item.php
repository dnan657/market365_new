<?php

$uri_item = $WEB_JSON['uri_dir_arr'][2] ?? '';
$uri_arr_item = explode('-', $uri_item);
$uri_id_item = end($uri_arr_item);
$_id_item = intval($uri_id_item);

$ad_row = f_db_select(
	'SELECT a.*, c.`title_en` AS `city_title_en`
	FROM `ads` a
	LEFT JOIN `city` c ON c.`_id` = a.`city_id`
	WHERE a.`_id` = ' . intval($_id_item) . ' AND a.`delete_on` = 0
	LIMIT 1'
);
$ad_found = !empty($ad_row);
$ad = $ad_found ? $ad_row[0] : [];

$ad_imgs = [];
if( $ad_found ){
	$ad_imgs = f_db_select(
		'SELECT `jpg_path`, `webp_path` FROM `ads_img` WHERE `ads_id` = ' . intval($_id_item) . ' ORDER BY `_id` ASC'
	);
}
if( empty($ad_imgs) ){
	$ad_imgs = [ ['jpg_path' => '', 'webp_path' => ''] ];
}

$item_json = [ 'zoom' => 15, 'lat' => 51.46718251196423, 'lng' => -0.08963604841304516 ];
if( $ad_found ){
	$gp = f_db_select(
		'SELECT ST_Y(`gps_point`) AS `la`, ST_X(`gps_point`) AS `ln` FROM `ads` WHERE `_id` = ' . intval($_id_item) . ' AND `gps_point` IS NOT NULL LIMIT 1'
	);
	if( !empty($gp) && isset($gp[0]['la']) && $gp[0]['la'] !== null && $gp[0]['la'] !== '' ){
		$item_json['lat'] = floatval($gp[0]['la']);
		$item_json['lng'] = floatval($gp[0]['ln']);
	}
}

$page_title = $ad_found ? (string)($ad['title'] ?? '') : f_translate('Ad not found');
$ad_description = $ad_found ? (string)($ad['description'] ?? '') : '';
$ad_price = $ad_found ? floatval($ad['price'] ?? 0) : 0;
$ad_currency = $ad_found ? trim((string)($ad['price_currency'] ?? '')) : '';
if( $ad_currency === '' ){
	$ad_currency = f_page_currency();
}
$city_label = $ad_found ? trim((string)($ad['city_title_en'] ?? '')) : '';
if( $city_label === '' && $ad_found && !empty($ad['city_id']) ){
	$city_label = f_translate('City') . ' #' . intval($ad['city_id']);
}
if( $city_label === '' ){
	$city_label = '—';
}

$seller_json = null;
if( $ad_found && !empty($ad['user_id']) ){
	$seller_json = f_db_get_user(['_id' => intval($ad['user_id'])]);
}
$seller_display = '—';
if( is_array($seller_json) ){
	$seller_display = trim((string)($seller_json['name'] ?? ''));
	if( $seller_display === '' && !empty($seller_json['email']) ){
		$seller_display = explode('@', (string)$seller_json['email'])[0];
	}
}

$me = f_user_get();
$is_logged = is_array($me) && !empty($me['_id']);
$is_owner = $is_logged && $ad_found && intval($ad['user_id'] ?? 0) === intval($me['_id']);
$can_message = $is_logged && $ad_found && !$is_owner && intval($ad['user_id'] ?? 0) > 0;

$is_favorite = false;
if( $is_logged && $ad_found && f_db_table_exists('user_favorite') ){
	$fx = f_db_select(
		'SELECT 1 AS `x` FROM `user_favorite` WHERE `user_id` = ' . intval($me['_id']) . ' AND `ads_id` = ' . intval($_id_item) . ' LIMIT 1'
	);
	$is_favorite = !empty($fx);
}

$login_url = f_page_link('login');
$pub_date_label = '';
if( $ad_found && !empty($ad['publication_date']) ){
	$pub_date_label = f_datetime_beauty($ad['publication_date']);
}

$arr_breadcump = [ [ 'title' => 'All ads', 'domain' => '' ] ];

f_page_library_add('swiper');
f_page_title_set($page_title);

?>

<style>

</style>

<div class="container">
	
	<?php f_page_breadcump( $arr_breadcump, f_page_link('ads_list') ); ?>
	
	
	<div class="head_page  mb-3  pb-2">
	
		<a class="back_head_page  btn btn-outline-dark"  back_page_link  href="/">
			<i class="bi bi-chevron-left"></i>
		</a>
		
		<h1 class="title_head_page">
			<?php f_translate_echo( $page_title ); ?>
		</h1>
		
	</div>
	

<style>
.box_section{
	margin-top: var(--v_p_20);
}

.gallery_container {
	width: 100%;
	user-select: none;
}
.photo_swiper_ads_item {
	width: 100%;
	height: 400px;
}
.swiper-slide {
	display: flex;
	justify-content: center;
	align-items: center;
}
.swiper-slide img {
	width: 100%;
	height: 100%;
	object-fit: cover;
}
.photo_swiper_thumbs_ads_item {
	margin-top: 10px;
}
.photo_swiper_thumbs_ads_item .swiper-slide {
	width: 60px;
	height: 60px;
	opacity: 0.5;
	cursor: pointer;
}
.photo_swiper_thumbs_ads_item .swiper-slide-thumb-active {
	opacity: 1;
}


.photo_swiper_ads_item  .swiper-pagination{
	background: var(--v_c_black_50);
	left: var(--v_p_10);
	bottom: var(--v_p_10);
	height: var(--v_p_30);
	width: max-content;
	padding: 0px var(--v_p_10);
	border-radius: var(--v_radius);
	color: var(--v_c_white);
	display: flex;
	align-items: center;
	justify-content: center;
	flex-wrap: nowrap;
	gap: var(--v_p_5);
	font-size: var(--v_font_small);
}


	
/* Кнопка полного экрана */
.btn_fullscreen_photo_ads_item {
	position: absolute;
	bottom: 10px;
	right: 10px;
	background-color: var(--v_c_white_50);
	color: var(--v_c_dark);
	border: none;
	cursor: pointer;
	z-index: 10;
	line-height: 1;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 40px;
	height: 40px;
}

.box_section{
	display: flex;
	flex-wrap: nowrap;
	gap: var(--v_p_20);
}

.left_section{
	width: calc(100% - 300px);
	flex-shrink: 1;
}

.right_section{
	width: 300px;
	flex-shrink: 0;
}


.item_section{
	background: var(--v_c_white);
	padding: var(--v_p_20);
	margin-bottom: var(--v_p_20);
	border-radius: var(--v_radius);
}


.title_full_item_ad{
	font-size: var(--v_font_h4);
	font-weight: 500;
	margin-bottom: var(--v_p_15);
}
.price_full_item_ad{
	font-size: var(--v_font_h2);
	margin-bottom: var(--v_p_20);
	font-weight: 600;
}


.label_user_item_ad {
	margin-bottom: var(--v_p_10);
	font-weight: 600;
	font-size: var(--v_font_small);
	color: var(--v_c_black_80);
	text-transform: uppercase;
}


.box_city_full_item_ad,
.box_user_full_item_ad,
.box_date_full_item_ad{
	display: flex;
	align-items: flex-start;
	flex-wrap: nowrap;
	gap: var(--v_p_15);
}
.box_city_full_item_ad  i,
.box_user_full_item_ad  i{
	font-size: var(--v_font_h1);
	line-height: 1;
	border-radius: 50%;
	display: block;
	width: 35px;
	height: 35px;
	text-align: center;
}
.city_full_item_ad,
.name_user_full_item_ad{
	font-weight: 600;
}
.distance_full_item_ad,
.date_user_full_item_ad,
.online_full_item_ad{
	font-size: var(--v_font_small);
	color: var(--v_c_black_80);
	line-height: 1.6;
}


.btn_profile_user_full_item_ad{
	text-align: center;
	display: block;
	width: 100%;
	margin-top: var(--v_p_10);
	text-decoration: none;
	color: var(--v_c_black);
}


.box_date_full_item_ad{
	align-items: center;
	margin-bottom: var(--v_p_15);
}
.date_full_item_ad{
	font-size: var(--v_font_small_extra);
	color: var(--v_c_black_70);
	width: 100%;
	flex-shrink: 1;
}
.btn_favorite_full_item_ad{
	font-size: var(--v_font_h3);
	line-height: 1;
	cursor: pointer;
}


.box_info_full_item_ad{
	display: flex;
	align-items: center;
	justify-content: space-between;
	font-size: var(--v_font_small);
	color: var(--v_c_black_80);
	margin-top: var(--v_p_15);
	padding-top: var(--v_p_15);
	border-top: 1px solid var(--v_c_border);
}

.btn_report_full_item_ad{
	color: var(--v_c_red);
	font-size: var(--v_font_small);
}

/* для Мобилок */
@media (max-width: 1000px) {
	.box_section{
		flex-wrap: wrap;
		gap: 0;
	}
	.left_section,
	.right_section{
		width: 100%;
	}
}

</style>

	<div class="box_section">
		<div class="left_section">
			
			<div class="item_section">
				<!--
				<h1 class="title_item_ad">
					Жесткая сцепка для буксировки авто без 2-го водите
				</h1>
				
				-->
				
				<div class="gallery_container">
					<!-- Основной слайдер -->
					<div class="swiper photo_swiper_ads_item">
						<div class="swiper-wrapper">
							<?php
								foreach( $ad_imgs as $im ){
									$src = f_db_ads_img_public_url($im['jpg_path'] ?? '', $im['webp_path'] ?? '');
							?>
								<div class="swiper-slide">
									<img class="img_ads_item" src="<?php f_echo_html($src); ?>" alt="">
								</div>
							<?php
								}
							?>
						</div>
						
						 <!-- If we need pagination -->
						<div class="swiper-pagination"></div>
						
						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
						
						<!-- Кнопка для открытия на весь экран -->
						<button class="btn btn-dark   btn_fullscreen_photo_ads_item">
							<i class="bi bi-arrows-fullscreen"></i> <!-- Иконка для входа в полный экран -->
						</button>
					</div>
					

					<!-- Слайдер миниатюр -->
					<div class="swiper photo_swiper_thumbs_ads_item">
						<div class="swiper-wrapper">
							<?php
								foreach( $ad_imgs as $im ){
									$src = f_db_ads_img_public_url($im['jpg_path'] ?? '', $im['webp_path'] ?? '');
							?>
								<div class="swiper-slide">
									<img class="img_ads_item" src="<?php f_echo_html($src); ?>" alt="">
								</div>
							<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>
			
			<div class="item_section">
				<div class="label_user_item_ad"  style="font-size: var(--v_font_h4);"><?php f_translate_echo('Description'); ?></div>
				<div class="description_full_item_ad">
					<?php
						echo $ad_found
							? nl2br(htmlspecialchars($ad_description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'))
							: htmlspecialchars(f_translate('This ad is not available.'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
					?>
				</div>
				
				<div class="box_info_full_item_ad">
					<div class="id_full_item_ad">ID: <?php f_echo_html($_id_item ? (string)$_id_item : '—'); ?></div>
					<div class="views_full_item_ad"><?php f_translate_echo('Views'); ?>: 123123</div>
					<div class="btn_report_full_item_ad  btn"><i class="bi bi-flag  me-2"></i> <?php f_translate_echo('Report'); ?></div>
				</div>
			</div>
			
			<div class="item_section">
				<div class="label_user_item_ad"><?php f_translate_echo('Contact the seller'); ?></div>
				
				<div class="box_user_full_item_ad">
					<div class="box_user_full_item_ad">
						<div>
							<i class="bi bi-person"></i>
						</div>
						<div>
							<div class="name_user_full_item_ad"><?php f_echo_html($seller_display); ?></div>
							<?php if( is_array($seller_json) && !empty($seller_json['visit_date']) ){ ?>
							<div class="online_full_item_ad"><?php f_translate_echo('Last visit'); ?>: <?php f_echo_html(f_datetime_beauty($seller_json['visit_date'])); ?></div>
							<?php } ?>
						</div>
					</div>
					<?php if( $can_message ){ ?>
					<button type="button" class="btn btn-warning btn-lg ms-auto px-5" data-bs-toggle="modal" data-bs-target="#modal_write_seller"><?php f_translate_echo('Message'); ?></button>
					<?php } elseif( !$is_logged && $ad_found ){ ?>
					<a class="btn btn-warning btn-lg ms-auto px-5" href="<?php f_echo_html($login_url); ?>"><?php f_translate_echo('Message'); ?></a>
					<?php } ?>
				</div>
			</div>
			
		</div>
		
		<div class="right_section">
			<div class="item_section">
				
				<div class="box_date_full_item_ad">
					<div class="date_full_item_ad">
						<?php if( $pub_date_label !== '' ){ ?>
							<?php f_translate_echo('Published'); ?>: <?php f_echo_html($pub_date_label); ?>
						<?php } ?>
					</div>
					<div
						class="btn_favorite_full_item_ad bi <?php echo $is_favorite ? 'bi-heart-fill text-danger' : 'bi-heart'; ?> <?php echo $ad_found ? 'js-ads-favorite-toggle' : ''; ?>"
						role="button"
						tabindex="0"
						data-ads-id="<?php f_echo_html((string)$_id_item); ?>"
						data-login-url="<?php f_echo_html($login_url); ?>"
						data-logged="<?php echo $is_logged ? '1' : '0'; ?>"
						aria-label="<?php f_translate_echo('Favorites'); ?>"
					></div>
				</div>
				
				<h1 class="title_full_item_ad">
					<?php f_translate_echo( $page_title ); ?>
				</h1>
				
				<h2 class="price_full_item_ad">
					<?php
						if( $ad_found ){
							f_echo_html(f_number_space($ad_price) . ' ' . $ad_currency);
						}else{
							f_echo_html('—');
						}
					?>
				</h2>
				
				<?php if( $can_message ){ ?>
				<button type="button" class="btn btn-warning btn-lg w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modal_write_seller"><?php f_translate_echo('Message'); ?></button>
				<?php } elseif( !$is_logged && $ad_found ){ ?>
				<a class="btn btn-warning btn-lg w-100 mb-2" href="<?php f_echo_html($login_url); ?>"><?php f_translate_echo('Message'); ?></a>
				<?php } ?>
				
				<div class="btn btn-outline-primary btn-lg  w-100"><?php f_translate_echo('Show phone'); ?></div>

				<?php if( $is_owner && $ad_found ){ ?>
				<a class="btn btn-outline-dark btn-lg w-100 mt-2" href="<?php f_echo_html(f_page_link('ads_promote') . '/' . f_seo_text_to_url((string)($ad['title'] ?? 'ad'), 100) . '-' . intval($_id_item)); ?>"><?php f_translate_echo('Promote'); ?></a>
				<?php } ?>
				
			</div>
			
			<div class="item_section">
				
				<div class="label_user_item_ad">
					<?php f_translate_echo('User'); ?>
				</div>
				
				<div class="box_user_full_item_ad">
					<div>
						<i class="bi bi-person"></i>
					</div>
					<div>
						<div class="name_user_full_item_ad"><?php f_echo_html($seller_display); ?></div>
						<?php if( is_array($seller_json) && !empty($seller_json['visit_date']) ){ ?>
						<div class="online_full_item_ad"><?php f_translate_echo('Last visit'); ?>: <?php f_echo_html(f_datetime_beauty($seller_json['visit_date'])); ?></div>
						<?php } ?>
					</div>
				</div>
				
				<a href="#" class="btn_profile_user_full_item_ad">
					<?php f_translate_echo('All ads by the author'); ?>
					<i class="bi bi-chevron-right "></i>
				</a>
				
				
			</div>
			
			
			<div class="item_section">
				
				<div class="label_user_item_ad">
					<?php f_translate_echo('Location'); ?>
				</div>
				
				<div class="box_city_full_item_ad">
					<div>
						<i class="bi bi-geo-alt"></i>
					</div>
					<div>
						<div class="city_full_item_ad"><?php f_echo_html($city_label); ?></div>
					</div>
				</div>
				
				<!-- Изображение карты -->
				<!--
				<img id="mapImage" width="100%" height="250" alt="Google Static Map" src="https://maps.googleapis.com/maps/api/staticmap?center=<?php f_echo($item_json['lat']); ?>,<?php f_echo($item_json['lng']); ?>&zoom=<?php f_echo($item_json['zoom']); ?>&size=600x450&markers=color:red%7Clabel:C%7C<?php f_echo($item_json['lat']); ?>,<?php f_echo($item_json['lng']); ?>&key=YOUR_API_KEY">
				<img id="mapImage" width="100%" height="250" alt="Yandex Static Map" src="https://static-maps.yandex.ru/1.x/?ll=<?php f_echo($item_json['lng']); ?>,<?php f_echo($item_json['lat']); ?>&z=<?php f_echo($item_json['zoom']); ?>&size=600,450&l=map&pt=<?php f_echo($item_json['lng']); ?>,<?php f_echo($item_json['lat']); ?>,pm2rdl">
				<img id="mapImage" width="100%" height="250" alt="OpenStreet Static Map" src="https://www.openstreetmap.org/export/embed.html?bbox=<?php f_echo($item_json['lng']-0.01); ?>,<?php f_echo($item_json['lat']-0.01); ?>,<?php f_echo($item_json['lng']+0.01); ?>,<?php f_echo($item_json['lat']+0.01); ?>&layer=mapnik&marker=<?php f_echo($item_json['lat']); ?>,<?php f_echo($item_json['lng']); ?>">
				<iframe width="100%" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?marker=<?php f_echo($item_json['lat']); ?>,<?php f_echo($item_json['lng']); ?>&layer=mapnik&zoom=<?php f_echo($item_json['zoom']); ?>" style="border: none"></iframe>
				-->
				
				<!-- Iframe для отображения Google Maps -->
				<iframe src="https://www.google.com/maps?q=<?php f_echo($item_json['lat']); ?>,<?php f_echo($item_json['lng']); ?>&hl=es;z=<?php f_echo($item_json['zoom']); ?>&output=embed" id="mapIframe" width="100%" height="250" style="border:0;margin-left:-20px;margin-bottom: -27px;width: calc(100% + var(--v_p_40)); margin-top: var(--v_p_10); border-radius: 0 0 var(--v_radius) var(--v_radius);" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
				
			</div>
		</div>
	</div>
	
	<div class="mt-4">
		<h2 class="h3  text-center  mb-4"><?php f_translate_echo("Author's ads"); ?></h2>
		<?php f_template('ads_swiper_top'); ?>
	</div>
	
	<div class="mt-4">
		<h2 class="h3  text-center  mb-4"><?php f_translate_echo('Similar ads'); ?></h2>
		<?php f_template('ads_swiper_top'); ?>
	</div>
</div>

<?php if( $can_message && $ad_found ){ ?>
<div class="modal fade" id="modal_write_seller" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php f_translate_echo('Message to the seller'); ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<textarea class="form-control" id="ads_item_message_text" rows="4" placeholder="<?php f_translate_echo('Your message'); ?>"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php f_translate_echo('Cancel'); ?></button>
				<button type="button" class="btn btn-warning" id="ads_item_message_send"><?php f_translate_echo('Send'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php } ?>


<!-- Initialize Swiper -->
<script>
document.addEventListener("DOMContentLoaded", () => {
	
	 var galleryThumbs = new Swiper(".photo_swiper_thumbs_ads_item", {
        spaceBetween: 10,
        slidesPerView: 5,
        freeMode: true,
        watchSlidesProgress: true,
    });
    var galleryTop = new Swiper(".photo_swiper_ads_item", {
        spaceBetween: 10,
		
		pagination: {
		  el: '.swiper-pagination',
		  type: "fraction",
		},
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
		zoom: {
			maxRatio: 3, // Максимальный зум 3x
		},
        thumbs: {
            swiper: galleryThumbs,
        },
    });

	let jq_btn_fullscreen_ads_photo = $(".btn_fullscreen_photo_ads_item")
	
	// Функция для работы с полным экраном и смены иконки
    jq_btn_fullscreen_ads_photo.on("click", function() {
        var el_photo_swiper_ads_item = $(".photo_swiper_ads_item")[0]; // Получаем элемент контейнера
        var is_fullscreen = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;

        if (is_fullscreen) {
            // Выход из полноэкранного режима
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            // Меняем иконку на "вход в полный экран"
            jq_btn_fullscreen_ads_photo.find("i").removeClass("bi-arrows-angle-contract").addClass("bi-arrows-fullscreen");
        } else {
            // Вход в полноэкранный режим
            if (el_photo_swiper_ads_item.requestFullscreen) {
                el_photo_swiper_ads_item.requestFullscreen();
            } else if (el_photo_swiper_ads_item.mozRequestFullScreen) { // Firefox
                el_photo_swiper_ads_item.mozRequestFullScreen();
            } else if (el_photo_swiper_ads_item.webkitRequestFullscreen) { // Chrome, Safari и Opera
                el_photo_swiper_ads_item.webkitRequestFullscreen();
            } else if (el_photo_swiper_ads_item.msRequestFullscreen) { // IE/Edge
                el_photo_swiper_ads_item.msRequestFullscreen();
            }
            // Меняем иконку на "выход из полного экрана"
            jq_btn_fullscreen_ads_photo.find("i").removeClass("bi-arrows-fullscreen").addClass("bi-arrows-angle-contract");
        }
    });

    // Слушаем событие изменения полноэкранного режима для изменения иконки
    $(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange', function() {
        var is_fullscreen = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;

        if (!is_fullscreen) {
            // Если вышли из полного экрана — меняем иконку на "вход в полный экран"
            jq_btn_fullscreen_ads_photo.find("i").removeClass("bi-arrows-collapse").addClass("bi-arrows-fullscreen");
        }
    });

	$(document).on('click', '.js-ads-favorite-toggle', function () {
		var $el = $(this);
		if ($el.data('logged') != '1') {
			window.location.href = $el.data('login-url') || '/login';
			return;
		}
		var aid = parseInt($el.data('ads-id'), 10);
		if (!aid) {
			return;
		}
		f_ajax('favorite', 'toggle', { ads_id: aid }, function (res) {
			res = res.data;
			if (res.error) {
				return;
			}
			if (res.is_favorite) {
				$el.removeClass('bi-heart').addClass('bi-heart-fill text-danger');
			} else {
				$el.removeClass('bi-heart-fill text-danger').addClass('bi-heart');
			}
		});
	});

	$('#ads_item_message_send').on('click', function () {
		var aid = <?php echo json_encode(intval($_id_item)); ?>;
		var text = $('#ads_item_message_text').val().trim();
		if (!text || !aid) {
			return;
		}
		f_ajax('chat', 'send', { ads_id: aid, message_text: text }, function (res) {
			res = res.data;
			if (res.error) {
				return;
			}
			var modalEl = document.getElementById('modal_write_seller');
			if (modalEl && window.bootstrap) {
				bootstrap.Modal.getOrCreateInstance(modalEl).hide();
			}
			window.location.href = <?php echo json_encode(f_page_link('user_messages')); ?>;
		});
	});

});
</script>

