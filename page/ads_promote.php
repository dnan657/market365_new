<?php

$title_page = f_translate('Promote ad');

f_page_title_set( $title_page );


?>

<style>

.item_section{
	background: var(--v_c_white);
	padding: var(--v_p_20);
	margin-bottom: var(--v_p_20);
	border-radius: var(--v_radius);
}


</style>


<style>


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

.service_item .service_title {
	font-weight: 500;
}

.service_item .service_options {
	display: flex;
	gap: 5px;
}

.service_item .btn_option {
	padding: 2px 10px;
	font-size: 0.85rem;
}

.service_item .btn_option.active {
	background-color: #17c0c0;
	color: #fff;
	border-color: #17c0c0;
}

.service_item .price {
	display: flex;
	align-items: center;
	gap: 20px;
	text-align: right;
	font-weight: bold;
}

.service_item .discount {
	color: #000;
	background: orange;
	padding: 5px 10px;
	border-radius: 5px;
	font-size: 0.9rem;
}

.service_item .old_price {
	text-decoration: line-through;
	color: rgba(0,0,0,0.5);
	font-size: 0.85rem;
}

.service_item:has(input[type="checkbox"]:checked){
	border-color: var(--v_c_blue);
}

/* для Мобилок */
@media (max-width: 1000px) {
	.service_item {
		display: flex;
		flex-direction: column;
		align-items: flex-start;
	}
}

</style>

<div class="container">
	
	<div class="head_page  mb-3  pb-2">
	
		<a class="back_head_page  btn btn-outline-dark"  href="<?php f_echo_html( $is_admin ? f_page_link('admin_ads_list') : f_page_link('user_ads'));  ?>">
			<i class="bi bi-chevron-left"></i>
		</a>
		
		<h1 class="title_head_page">
			<?php f_echo( $title_page ); ?>
			<?php f_echo_html( $is_admin ? ' - #' . f_num_encode( $item_json['_id'] ) : '') ?>
		</h1>
		
	</div>
	
	<div class="item_section">
		
		<div class="sub_title_head_page  mb-3"><?php f_translate_echo( 'Individual services' ); ?></div>
		
		<div class="service_item" for="top-placement">
			<div>
				<div class="form-check  flex_middle">
					<input class="form-check-input  me-3  mt-0" type="checkbox" id="top-placement">
					<label class="form-check-label service_title" for="top-placement"><?php f_translate_echo( 'Placement in the TOP' ); ?></label>
				</div>
				<div class="service_options mt-2">
					<button class="btn btn-outline-secondary btn_option active">7 <?php f_translate_echo( 'days' ); ?></button>
					<button class="btn btn-outline-secondary btn_option">30 <?php f_translate_echo( 'days' ); ?></button>
				</div>
			</div>
			<div class="price">
				<div class="discount">-50%</div>
				<div class="old_price">1 <?php f_echo_html( f_page_currency() ); ?></div>
				<div>0.5 <?php f_echo_html( f_page_currency() ); ?></div>
			</div>
		</div>

		<div class="service_item" for="raise_ad">
			<div>
				<div class="form-check  flex_middle">
					<input class="form-check-input  me-3  mt-0" type="checkbox" id="raise_ad">
					<label class="form-check-label service_title" for="raise_ad"><?php f_translate_echo( 'Raise 7 times' ); ?><i class="bi bi-info-circle  ms-3"></i></label>
				</div>
			</div>
			<div class="price">
				<div class="discount">-50%</div>
				<div class="old_price">4 <?php f_echo_html( f_page_currency() ); ?></div>
				<div>2 <?php f_echo_html( f_page_currency() ); ?></div>
			</div>
		</div>

		<div class="service_item" for="vip_ad">
			<div>
				<div class="form-check  flex_middle">
					<input class="form-check-input  me-3  mt-0" type="checkbox" id="vip_ad">
					<label class="form-check-label service_title" for="vip_ad"><?php f_translate_echo( 'VIP announcement' ); ?><i class="bi bi-info-circle  ms-3"></i></label>
				</div>
			</div>
			<div class="price">
				<div class="discount">-50%</div>
				<div class="old_price">8 <?php f_echo_html( f_page_currency() ); ?></div>
				<div>4 <?php f_echo_html( f_page_currency() ); ?></div>
			</div>
		</div>
		
	</div>
	
	
	<div class="item_section">
		<div class="row">
			<div class="col-md-6"></div>
			<div class="col-md-6  d-flex  gap-3">
				<a href="<?php f_echo_html( f_page_link('user_ads') ); ?>" class="btn btn-outline-dark  py-2  w-100"><?php f_translate_echo('Cancel'); ?></a>
				<div class="btn btn-primary  py-2  w-100"><?php f_translate_echo('Add promote'); ?></div>
			</div>
		</div>
	</div>
	
</div>

