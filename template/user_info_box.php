

<style>



.page{
	overflow-x: hidden;
}

.user_info_box{
	position: relative;
	width: 100%;
	height: max-content;
	padding-top: var(--v_p_30);
	margin: var(--v_p_40) 0;
}

.user_info_box::after{
	content: '';
	border: 1px solid var(--v_c_border);
	background: white;
	display: block;
	position: absolute;
	left: calc(-1 * var(--v_p_30));
	top: 0;
	width: calc(100% + var(--v_p_30) * 2);
	height: 100%;
	border-radius: var(--v_radius);
	z-index: -1;
}



.head_page{
	margin: 0px;
	flex-wrap: nowrap;
	align-items: flex-start;
	justify-content: space-between;
}

/* Mobile */
@media (max-width: 1000px) {
	.head_page{
		flex-wrap: none;
	}
	.user_info_box{
		margin: var(--v_p_20) 0;
	}
	.title_head_page {
		width: 100%;
		order: unset;
	}
	.back_head_page {
		order: unset;
	}
	
	.nav_user_info_box{
		flex-wrap: wrap;
	}
	
	.nav_user_info_box{
		width: 100%;
	}
	
	.mini_user_info_box{
		width: 100%;
	}
}


.mini_user_info_box{
	
}


.mini_user_info_box  .name_user_info_box{
	font-size: var(--v_font_h4);
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
	max-width: 140px;
	width: max-content;
	margin-left: auto;
}
.mini_user_info_box  .balance_user_info_box{
	font-size: var(--v_font_small);
	color: var(--v_c_black_50);
	max-width: 140px;
	width: max-content;
	margin-left: auto;
	line-height: 1;
}


.menu_user_info_box{
	display: flex;
	flex-wrap: nowrap;
	align-items: center;
	margin-top: var(--v_p_40);
	gap: var(--v_p_20);
	overflow-x: auto;
	user-select: none;
}

.menu_user_info_box  a{
	display: block;
	text-decoration: none;
	color: var(--v_c_black_50);
	border-bottom: 2px solid transparent;
	font-size: var(--v_font_h4);
	padding-right: var(--v_p_20);
	padding-left: var(--v_p_20);
	padding-bottom: var(--v_p_10);
}


.menu_user_info_box  a.active{
	color: var(--v_c_black);
	border-bottom: 2px solid;
	border-color: var(--v_c_black);
	font-weight: 600;
}

.nav_user_info_box{
	display: flex;
	flex-wrap: nowrap;
	gap: var(--v_p_15);
	align-items: center;
}

/* для Hover */
@media (hover: hover) {
	.menu_user_info_box  a:hover{
		color: var(--v_c_black_80);
		border-bottom: 2px solid;
		border-color: var(--v_c_black_80);
	}
}


/* Mobile */
@media (max-width: 1000px) {
	.menu_user_info_box  a{
		font-size: var(--v_font_default);
		padding-bottom: var(--v_p_10);
		padding-right: var(--v_p_15);
		padding-left: var(--v_p_15);
	}
}

</style>

<div class="user_info_box">
	
	<div class="head_page">
		
		<div class="nav_user_info_box">
			<a class="back_head_page  btn btn-outline-dark"  back_page_link  href="<?php f_echo_html( $is_admin ? f_page_link('admin_notifications_list') : '/');  ?>">
				<i class="bi bi-chevron-left"></i>
			</a>
			
			<h1 class="title_head_page">
				<?php f_echo( $GLOBALS['title_page'] ); ?>
				<?php f_echo_html( $is_admin ? ' - #' . f_num_encode( $GLOBALS['item_json']['_id'] ) : '') ?>
			</h1>
		</div>
		
		<div class="mini_user_info_box">
			<div class="name_user_info_box"><?php f_echo_html( f_user_get()['name'] ); ?></div>
			<div class="balance_user_info_box">Balance: <?php f_echo_html( f_user_get()['html_count_balance'] ); ?></div>
		</div>
		
	</div>
	
	<div class="menu_user_info_box  mobile_scroll_hide">
		<?php
			$arr_menu = [
				[ 'title' => 'Ads',						'link' => f_page_link('user_ads') ],
				[ 'title' => 'Messages',				'link' => f_page_link('user_messages') ],
				[ 'title' => 'Favorites',				'link' => f_page_link('user_favorites') ],
				[ 'title' => 'Notifications',			'link' => f_page_link('user_notifications') ],
				[ 'title' => 'Payments',				'link' => f_page_link('user_pays') ],
				[ 'title' => 'Settings',				'link' => f_page_link('user_settings') ],
			];
			
			$uri_path_current = '/' . $GLOBALS['WEB_JSON']['uri_clean'];
			
			foreach($arr_menu as $json_menu){
				$class_name = '';
				if( $uri_path_current == $json_menu['link'] ){
					$class_name = 'active  scroll_to_left';
				}
		?>
			<a href="<?php f_echo( $json_menu['link'] ); ?>" class="<?php f_echo( $class_name ); ?>">
				<?php f_echo( $json_menu['title'] ); ?>
			</a>
		<?php
			}
		?>
	</div>
	
</div>


<script>

document.addEventListener("DOMContentLoaded", function(event){
	
	
	
});

</script>
