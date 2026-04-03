<?php

f_page_title_set( f_translate('Bulletin Board') );

?>

<style>

</style>


<div class="search_section"  section>
	<div class="container">
		
		<h1 class="h3  text-center  mb-4">
			<?php f_translate_echo('Bulletin Board'); ?>
		</h1>
		
		<?php f_template('box_search'); ?>
		
	</div>
</div>

<script>
</script>






<style>

.category{
	background: var(--v_c_white);
}


.box_category{
	display: flex;
	flex-wrap: wrap;
	gap: var(--v_p_20);
	justify-content: center;
}

.item_category{
	width: 120px;
	max-width: 100%;
	font-size: var(--v_font_small);
	text-decoration: none;
	color: var(--v_c_black);
	font-weight: bold;
	flex-shrink: 0;
	text-align: center;
	line-height: 1.3;
	user-select: none;
}

/* для Hover */
@media (hover: hover) {
	.item_category:hover  .title_item_category{
		background: var(--v_c_black);
		color: var(--v_c_white);
	}
}

.title_item_category{
	padding: 4px 2px;
	white-space: break-spaces;
	border-radius: var(--v_radius);
}



.icon_item_category{
	width: 100px;
	height: 100px;
	font-size: 60px;
	display: block;
	position: relative;
	margin: 0 auto;
}

.icon_item_category::before{
	position: absolute;
	z-index: 2;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
	color: var(--v_tmp_bg);
	filter: brightness(0.7);
}
.icon_item_category::after{
	content: '';
	position: absolute;
	z-index: 1;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
	border-radius: 100%;
	width: 85px;
	height: 85px;
	background: var(--v_tmp_bg);
	opacity: 0.2;
}

/* для Мобилок */
@media (max-width: 1000px) {
	.box_category{
		flex-wrap: nowrap;
		overflow-x: auto;
		justify-content: flex-start;
	}
	.item_category {
		width: 100px;
		font-size: var(--v_font_small_extra);
	}
	
	.icon_item_category{
		width: 80px;
		height: 80px;
		margin: 10px auto;
	}
	
}


</style>

<div class="category"  section>
	<div class="container">
		
		<h1 class="h3  text-center  mb-4">
			<?php f_translate_echo('Categories'); ?>
		</h1>
		
		<div class="box_category  mobile_scroll_hide">
			<?php
				//$arr_color = ["#A200FF","#0033FF","#00FF99","#FFEE00","#7300FF","#3BFF00","#FF0088","#3BFF00","#00FFC8","#FF1A00","#FF00B7","#6AFF00","#0062FF","#00BFFF","#FFEE00","#4400FF","#00FFF7","#FF4800","#FFBF00","#FF00E6","#1BE436","#00FF6A","#FF1A00","#7300FF","#0DFF00","#E1FF00"];
				//shuffle($arr_color);
				$category_arr = f_db_select_smart("ads_category", ["hide_on" => 0, "level" => 1], 100, 0, ["sort"=>-1]);
				$i = 0;
				foreach($category_arr as $category_json){
					//$color = $arr_color[$i];
					$color = $category_json['color_bg'];
					$i += 1;
			?>
					<a href="<?php f_echo( f_page_link('ads_category') . '/' . $category_json['domain']); ?>" class="item_category" style="--v_tmp_bg: <?php f_echo($color); ?>">
						<i class="icon_item_category  mdi  <?php f_echo($category_json['icon_class']); ?>"></i>
						<div class="title_item_category"><?php f_echo($category_json['title_en']); ?></div>
					</a>
			<?php
				}
				
				f_echo( "<script>" . json_encode($arr_color) . "</script>" );
			?>
		</div>
		
	</div>
</div>



<div section  class="pb-0">
	<div class="container">
		
		<h2 class="h3  text-center  mb-4">
			<?php f_translate_echo('Recently viewed'); ?>
		</h2>
		
		<?php f_template('ads_swiper_viewed'); ?>
		
	</div>
</div>




<div section  class="pb-0">
	<div class="container">
		
		<h2 class="h3  text-center  mb-4">
			<?php f_translate_echo('Recent Post'); ?>
		</h2>
		
		<div class="list_card_ad">
			<?php
				for($i=0; $i<32; $i++){
			?>
				<div class="item_ad">
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
		
	</div>
</div>