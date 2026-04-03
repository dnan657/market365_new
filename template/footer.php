
<style>
.footer{
	background: var(--v_c_black);
	width: 100%;
	padding: 50px 0;
	color: var(--v_c_white);
	font-size: var(--v_font_small);
	/* transform: translateY(100%); */
}

.footer  h5{
	font-size: var(--v_font_small);
	color: var(--v_c_white);
}
.footer  a{
	text-decoration: none;
	display: block;
	width: 100%;
	margin-bottom: var(--v_p_10);
	color: var(--v_c_white);
}
.footer  a:hover{
	text-decoration: underline;
}

.footer  .box_pay{
	display: flex;
	gap: var(--v_p_20);
	flex-wrap: nowrap;
}
.footer  .box_pay  img{
	display: block;
	width: 70px;
	height: auto;
}
</style>

<div class="footer  mobile_hide">
	<div class="container">
		<div class="row">
		
			<div class="col-lg-3">
				<a href="<?php f_page_link_echo('page_about_us'); ?>"><?php f_translate_echo('About us'); ?></a>
				<a href="<?php f_page_link_echo('page_payment'); ?>"><?php f_translate_echo('Payment methods'); ?></a>
				<a href="<?php f_page_link_echo('page_sitemap'); ?>"><?php f_translate_echo('Site map'); ?></a>
			</div>
			
			<div class="col-lg-4">
				<a href="<?php f_page_link_echo('page_rules'); ?>">				<?php f_translate_echo('Terms of use'); ?></a>
				<a href="<?php f_page_link_echo('page_faq'); ?>">				<?php f_translate_echo('Frequently Asked Questions'); ?></a>
				<a href="<?php f_page_link_echo('page_recomendations'); ?>">	<?php f_translate_echo('Recommendations'); ?></a>
				<a href="<?php f_page_link_echo('page_safety_tips'); ?>">		<?php f_translate_echo('Safety Tips'); ?></a>
				<a href="<?php f_page_link_echo('page_privacy'); ?>">			<?php f_translate_echo('Privacy policy'); ?></a>
			</div>
			
			<div class="col-lg-4">
				<div class="opacity_5">
					<?php f_translate_echo('Email'); ?>:
				</div>
				<a href="mailto:<?php f_page_link_echo('email_support'); ?>"><?php f_page_link_echo('email_support'); ?></a>
				
				<div class="box_pay">
					<img src="<?php f_page_link_echo('img_logo_stripe'); ?>">
					<img src="<?php f_page_link_echo('img_logo_mastercard'); ?>"  style="width: 50px">
					<img src="<?php f_page_link_echo('img_logo_visa'); ?>">
				</div>
				<div class="box_pay" style="margin-top: -25px;">
					<img src="<?php f_page_link_echo('img_logo_american_express'); ?>"  style="width: 60px">
					<img src="<?php f_page_link_echo('img_logo_google_pay'); ?>">
					<img src="<?php f_page_link_echo('img_logo_apple_pay'); ?>" style="filter: invert(1);">
				</div>
			</div>
		
		</div>
	</div>
</div>

