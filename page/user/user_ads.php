<?php

$title_page = f_translate('My Ads');

f_page_title_set( $title_page );

?>

<style>

</style>


<div class="container">
	
	<?php f_template('user_info_box'); ?>
	
	
	
	<div class="list_line_ads">
		<?php
			for($i=0; $i<20; $i++){
		?>
			<div class="item_ad">
				<a href="#" class="body_item_ad">
					
					<img class="img_item_ad" src="/public/ad_default.jpg">
					<div class="text_item_ad">
						<div class="title_item_ad">
							I will sell a new Luxury segment car directly from the salon
						</div>
						<!--
						<div class="btn_item_ad">
							<div class="btn_tool_item_ad   bi bi-pencil-square"></div>
							<div class="btn_tool_item_ad   bi bi-x-lg"></div>
						</div>
						-->
						<div class="price_item_ad">
							20 000 $
						</div>
						<div class="city_item_ad">
							<i class="bi bi-geo-alt  me-2"></i>
							London
						</div>
						<div class="date_item_ad">
							<i class="bi bi-calendar-week  me-2"></i>
							13.07.2024 - 12.08.2024
							<!--21 июля 2024 г.-->
						</div>
					</div>
				</a>
				<div class="settings_item_ad">
					<div class="id_settings_item_ad">ID: 12423523</div>
					<div class="views_settings_item_ad"><i class="bi bi-eye"></i>123</div>
					<div class="likes_settings_item_ad"><i class="bi bi-heart"></i>123</div>
					<div class="calls_settings_item_ad"><i class="bi bi-telephone"></i>123</div>
					
					<div class="btn_settings_item_ad">
						<a href="#" class="btn btn-outline-dark  chats_settings_item_ad"><i class="bi bi-chat"></i>123</a>
						<a href="<?php f_echo_html( f_page_link('ads_promote') . '/' . 123); ?>" class="btn btn-warning  promote_settings_item_ad"><i class="bi bi-capslock-fill"></i><?php f_translate_echo('Promote'); ?></a>
						
						<div class="btn btn-outline-danger"  click_delete_my_item_ad><i class="bi bi-trash"></i><?php f_translate_echo('Delete'); ?></div>
						<div class="btn btn-dark"><i class="bi bi-pencil-square"></i><?php f_translate_echo('Edit'); ?></div>
					</div>
				</div>
			</div>
		<?php
			}
		?>
	</div>
	
</div>

