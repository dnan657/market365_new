<?php

f_page_library_add('swiper');

?>


<!-- Swiper -->
<div class="swiper ads_swiper_top">
	<div class="swiper-wrapper">
		<?php
			for($i=0; $i<20; $i++){
		?>
			<div class="swiper-slide  item_ad">
				<a href="#" class="body_item_ad">
					
					<img class="img_item_ad" src="/public/ad_default.jpg">
					<div class="text_item_ad">
						<div class="d-flex">
							<div class="title_item_ad">
								I will sell a new Luxury segment car directly from the salon
							</div>
							<div class="btn_favorite_item_ad   bi bi-heart"></div>
						</div>
						<div class="price_item_ad">
							20 000 $
						</div>
						<div class="city_item_ad">
							London
						</div>
						<div class="date_item_ad">
							Today
							<!--21 июля 2024 г.-->
						</div>
					</div>
				</a>
			</div>
		<?php
			}
		?>
	</div>
	<div class="swiper-button-next"></div>
	<div class="swiper-button-prev"></div>
</div>



<!-- Initialize Swiper -->
<script>
document.addEventListener("DOMContentLoaded", () => {
	var swiper = new Swiper(".ads_swiper_top", {
		slidesPerView: "auto",
		//spaceBetween: 30,
		//loop: true,
		grabCursor: true,
		freeMode: true, // Включаем свободный режим
		freeModeMomentum: true, // Эффект инерции при прокрутке
		freeModeMomentumRatio: 1, // Коэффициент инерции (чем выше, тем дольше будет крутиться)
		freeModeMomentumBounce: true, // Bounce эффект при окончании скролла
		freeModeMomentumBounceRatio: 1, // Настройка скорости отскока
		autoplay: {
			delay: 2500,
			disableOnInteraction: false,
		},
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
		},
	});
});
</script>
