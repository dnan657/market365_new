<style>


:root{
	--v_c_primary: rgb(38 50 56);
	--v_c_secondary: #92e3a9;
	--v_c_secondary_light: #def7e5;
	--v_c_secondary_dark: #359b42;
	--v_c_red: red;
	--v_c_blue: #0d6efd;
	--v_c_black: rgb(38 50 56);
	--v_c_black_80: rgb(38 50 56 / 80%);
	--v_c_black_70: rgb(38 50 56 / 70%);
	--v_c_black_60: rgb(38 50 56 / 60%);
	--v_c_black_50: rgb(38 50 56 / 50%);
	--v_c_black_40: rgb(38 50 56 / 40%);
	--v_c_black_30: rgb(38 50 56 / 30%);
	--v_c_black_20: rgb(38 50 56 / 20%);
	--v_c_black_20: rgb(38 50 56 / 10%);
	--v_c_black_20: rgb(38 50 56 / 5%);
	--v_c_black_10: rgb(38 50 56 / 10%);
	--v_c_black_5: rgb(38 50 56 / 5%);
	--v_opacity_1: 0.1;
	--v_opacity_2: 0.2;
	--v_opacity_3: 0.3;
	--v_opacity_4: 0.4;
	--v_opacity_5: 0.5;
	--v_opacity_6: 0.6;
	--v_opacity_7: 0.7;
	--v_opacity_8: 0.8;
	--v_opacity_9: 0.9;
	--v_opacity_10: 1;
	--v_c_success: rgb(25 135 84);
	--v_c_danger: rgb(220 53 69);
	--v_c_danger_25: rgb(220 53 69 / 25%);
	--v_c_sidebar: #313a46;
	--v_c_white: rgb(255 255 255);
	--v_c_white_50: rgb(255 255 255 / 50%);
	--v_c_white_80: rgb(255 255 255 / 80%);
	--v_shadow_style: 0px 0px 0px 1px var(--v_c_black), 6px 6px 0px 0px var(--v_c_black);
	
	--v_radius: 5px;
	--v_radius_5: 5px;
	--v_c_border: rgb(0,0,0,0.1);
	--v_c_bg_silver: rgb(0,0,0,0.05);
	--v_c_navbar: rgb(255,255,255);
	--v_c_box_page: #f9fafd;
	--v_navbar_height: 60px;
	--v_sidebar_width: 250px;
	
	--v_p_5: 5px;
	--v_p_10: 10px;
	--v_p_15: 15px;
	--v_p_20: 20px;
	--v_p_25: 25px;
	--v_p_30: 30px;
	--v_p_35: 35px;
	--v_p_40: 40px;
	--v_p_50: 50px;
	--v_p_60: 60px;
	
	--v_font_small_2: 8px;
	--v_font_small_1: 10px;
	--v_font_small_extra: 12px;
	--v_font_small: 14px;
	--v_font_default: 16px;
	--v_font_h4: 18px;
	--v_font_h3: 20px;
	--v_font_h2: 24px;
	--v_font_h1: 28px;
	
	/*
	--v_font_small_2: 10px;
	--v_font_small_1: 12px;
	--v_font_small_extra: 14px;
	--v_font_small: 16px;
	--v_font_default: 18px;
	--v_font_h4: 20px;
	--v_font_h3: 24px;
	--v_font_h2: 28px;
	--v_font_h1: 32px;
	*/
}

body{
	font-size: var(--v_font_default);
	color: rgb(0,0,0);
	font-family: "Nunito", sans-serif;
	/* font-family: "Rubik", sans-serif; */
	background-color: var(--v_c_bg_silver);
}


.page{
	display: flex;
	flex-direction: column;
	flex-wrap: nowrap;
	padding-top: var(--v_navbar_height);
	min-height: 100vh;
	margin-bottom: var(--v_p_60);
	/*padding-bottom: var(--v_navbar_height);*/
}



.page_ads_side_left,
.page_ads_side_right{
	position: fixed;
	top: var(--v_navbar_height);
	height: calc(100vh - var(--v_navbar_height));
	width: calc( (100vw - 1000px) / 2 );
}
.page_ads_side_left{
	left: 0;
}
.page_ads_side_right{
	right: 0;
}


.page_ads_side_x_body{
	width: 100%;
	height: 100%;
	max-width: 200px;
	padding: var(--v_p_20) 0;
	
	margin: 0 auto;
}
.page_ads_side_x_body{
	margin-left: auto;
}


.page_ads_side_x_body  .adsbygoogle{
	margin-left: auto;
	background: #E0F7FA;
	position: relative;
}
.page_ads_side_x_body  .adsbygoogle::after{
	content: 'AD';
	font-size: 30px;
	color: var(--v_c_black_50);
	display: block;
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
}



/* для ADS */
@media (max-width: 1450px) {
	.page_ads_side_left,
	.page_ads_side_right{
		display: none;
	}
}


.page_margin_top{
	margin-top: 50px;
}

.page_h1{
	margin-top: 50px;
	margin-bottom: 50px;
}

.container{
	padding-left: var(--v_p_15);
	padding-right: var(--v_p_15);
	max-width: 1000px;
	width: 100%;
}
.row {
	--bs-gutter-x: var(--v_p_10);
}

.h-max-100{
	max-height: 100%;
}

[template]{
	display: none;
}

.border_radius{
	border-radius: var(--v_radius);
}

.flex_middle{
	display: flex;
	align-items: center;
}


::selection {
	background: #0d6efd!important;
	color: #fff !important;
}

::placeholder{
	user-select: none;
}



[for]:not(label){
	cursor: pointer;
}


/* для Десктопов */
@media (min-width: 1001px) {
	.desktop_hide{
		display: none!important;
	}
}

/* для Мобилок */
@media (max-width: 1000px) {
	.mobile_hide{
		display: none!important;
	}
	.footer  .container{
		margin-bottom: var(--v_navbar_height);
	}
	
	.page_margin_top{
		margin-top: 30px;
	}
	
	.page{
		margin-bottom: var(--v_p_20);
		padding-bottom: var(--v_navbar_height);
	}
}




.g-recaptcha{
	width: 305px;
	min-height: 78px;
	margin: 0 auto;
}


/*
.page:has(.mini_form_fixed){
	background: var(--v_c_border);
}
.mini_form_fixed{
	width: 100%;
	min-height: calc(100vh - var(--v_navbar_height));
	display: flex;
	align-items: safe center;
	justify-content: safe center;
	padding: var(--v_p_40) 0;
}
*/

.page:has(.mini_form_fixed){
	align-items: safe center;
	justify-content: safe center;
	margin-bottom: 0;
}

.mini_form_fixed{
	width: max-content;
	height: max-content;
	max-width: 100%;
	max-height: 100%;
	padding: var(--v_p_40) 0;
}
.body_mini_form_fixed{
	background: var(--v_c_white);
	max-width: 100%;
	width: 400px;
	position: relative;
	padding: var(--v_p_20);
	border-radius: var(--v_radius);
}


/* для Мобилок */
@media (max-width: 1000px) {
	.body_mini_form_fixed{
		border-radius: 0;
	}
}


.nav-link{
	--bs-nav-pills-link-active-color: var(--v_c_black);
	--bs-nav-link-color: var(--v_c_black);
	--bs-nav-link-hover-color: var(--v_c_black);
	--bs-nav-pills-link-active-bg: var(--v_c_white);
}
.nav-pills{
	padding: var(--v_p_5);
	background: var(--v_c_border);
	border-radius: var(--v_p_10);
}








.page-link{
	padding: 10px 15px;
}

.page-item{
	-webkit-user-select: none;
	   -moz-user-select: none;
	    -ms-user-select: none;
	        user-select: none;
	cursor: pointer;
	line-height: 1;
}


[btn_href]{
	cursor: pointer;
}
[btn_href]:activate{
	-webkit-transform: scale(0.95);
	    -ms-transform: scale(0.95);
	        transform: scale(0.95);
}


.opacity_1{opacity: var(--v_opacity_1)}
.opacity_2{opacity: var(--v_opacity_2)}
.opacity_3{opacity: var(--v_opacity_3)}
.opacity_4{opacity: var(--v_opacity_4)}
.opacity_5{opacity: var(--v_opacity_5)}
.opacity_6{opacity: var(--v_opacity_6)}
.opacity_7{opacity: var(--v_opacity_7)}
.opacity_8{opacity: var(--v_opacity_8)}
.opacity_9{opacity: var(--v_opacity_9)}
.opacity_10{opacity: var(--v_opacity_10)}


.form-control,
.form-check-input,
.form-select {
	/*background-color: rgb(162 162 162 / 10%);*/
	/*background-color: rgb(201 201 201 / 10%);*/
	/*border: 1px solid var(--v_c_black_50);*/
	/*
	border: none;
	border-bottom: 2px solid var(--v_c_black_50);
	*/
	border: 1px solid var(--v_c_black_50);
	opacity: 1;
}


.form-check-input:focus,
.form-control:focus,
.form-select:focus{
	/*box-shadow: unset;*/
	/*border-bottom: 2px solid var(--v_c_black)!important;*/
	border-color: var(--v_c_black);
	box-shadow: 0 0 0 1px var(--v_c_black);
}

/* для Hover
@media (hover: hover) {
	.form-control:not([disabled]):not([readonly]):hover,
	.form-check-input:not([disabled]):not([readonly]):hover,
	.form-select:not([disabled]):not([readonly]):hover{
		border-bottom: 2px solid var(--v_c_black_30);
	}
}
*/




.control_error  .form-control,
.control_error  .form-select {
	border: 1px solid var(--v_c_danger);
}
.control_error  .form-control:focus,
.control_error  .form-select:focus {
	box-shadow: 0 0 0 .25rem var(--v_c_danger_25);
}

.form-control:disabled ,
.form-select[disabled] {
	background-color: var(--v_c_border);
	opacity: 1;
}
.form-floating label::after {
	display: none!important;
}
.form-floating>label{
	border: none;
}

.btn[disabled]{
	opacity: 0.5;
	pointer-events: none;
}

.btn_sending{
	opacity: 0.5;
	pointer-events: none;
	display: flex;
	align-items: center;
}
.btn_sending::before{
	content: '';
	display: block;
	width: 20px;
	height: 20px;
	border-radius: 50%;
	border: 2px solid;
	border-right-color: transparent;
	margin-right: 10px;
	animation: kf_rotate 1s linear infinite;
}


.text_error{
	display: none;
	font-size: var(--v_font_small_extra);
}

.control_error  .text_error{
	display: inherit;
}


a.btn{
	width: -webkit-max-content;
	width: -moz-max-content;
	width: max-content;
	max-width: 100%;
}

/*
.modal{
	z-index: 500;
}
*/

.modal[type]  [type_show]{
	display: none;
}
.modal[type="edit"]  [type_show="edit"],
.modal[type="add"]  [type_show="add"]{
	display: initial;
}



.modal[status="load"]  .modal-body:before,
.modal[status="saving"]  .modal-body:before{
	content: '';
	display: block;
	width: 100%;
	height: 100%;
	position: absolute;
	left: 0px;
	top: 0px;
	z-index: 100;
	user-select: none;
}
.modal[status="load"]  .modal-body:before{
	backdrop-filter: blur(10px);
}
.modal[status="saving"]  .modal-body:before{
	background: var(--v_c_black_20);
}
.modal[status="saving"]  .modal-body{
	pointer-events: none;
}

.modal[status="load"]  .modal-footer  .btn,
.modal[status="saving"]  .modal-footer  .btn{
	opacity: 0.5;
	pointer-events: none;
}


.modal[status="saving"]  .modal-footer  [field_btn="save"]::before{
	content: '';
	display: inline-block;
	width: 15px;
	height: 15px;
	border-radius: 50%;
	margin-right: 10px;
	border: 2px solid;
	border-right: 2px solid transparent;
	animation: kf_rotate linear infinite 1s;
}


.modal[status="load"]  .modal-body:after{
	content: '';
	display: block;
	width: 50px;
	height: 50px;
	border-radius: 50%;
	border: 5px solid var(--v_c_black);
	border-right: 5px solid transparent;
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
	animation: kf_rotate_center linear infinite 2s;
	z-index: 101;
}

@keyframes kf_rotate {
    from {transform: rotate(0deg)}
    to {transform: rotate(360deg)}
}
@keyframes kf_rotate_center {
    from {transform: translate(-50%, -50%) rotate(0deg)}
    to {transform: translate(-50%, -50%) rotate(360deg)}
}







.modal-title{
	font-size: 18px;
	display: flex;
	flex-wrap: nowrap;
}

.fancybox__container{
	z-index: 10000;
}

.w-auto{
	width: -webkit-max-content!important;
	width: -moz-max-content!important;
	width: max-content!important;
	max-width: 100%!important;
}
.text-justify{
	text-align: justify;
	text-justify: inter-word;
}

.grecaptcha-badge{
	visibility: hidden;
}

.text-nowrap{
	white-space: nowrap;
}


[template]{
	display: none;
}


.form-label{
	margin-bottom: 3px;
}

.my_control{
	margin-bottom: 10px;
}


<?php
/*

.modal {
	padding-right: 0 !important;
	overflow: hidden;
}
.modal-dialog {
	max-height: 100%;
	margin: 0px;
	height: 100svh;
	height: 100vh;
	margin-left: auto;
	width: max-content;
	max-width: 100%;
}
.modal-header{
	height: var(--v_navbar_height);
	padding: 0 var(--v_p_20);
}
.modal-footer{
	padding: var(--v_p_20);
}
.modal.show .modal-dialog{
	transform: none !important;
}
.modal.fade .modal-dialog {
	transition: -webkit-transform .15s ease-out;
	transition: transform .1s ease-out;
	transition: transform .1s ease-out, -webkit-transform .1s ease-out;
	-webkit-transform: translate(100%, 0);
	transform: translate(100%, 0);
	/*
	height: calc(100vh - var(--v_navbar_height));
	margin-top: var(--v_navbar_height);
	/
}
.modal-content {
	height: 100%;
	max-height: 100%;
	padding-right: 10px;
	border-radius: 10px 0px 0px 10px !important;
}
@media (max-width: 600px) {
	.modal.fade  .modal-dialog {
		-webkit-transform: translate(0, 100%);
		transform: translate(0, 100%);
	}
	.modal-content {
		padding-right: 0px;
		height: max-content;
		border-radius: 10px 10px 0 0 !important;
		position: relative;
		overflow: visible !important;
	}
	.modal-dialog {
		justify-content: end !important;
		align-items: flex-end;
		margin: 0;
		padding-top: var(--v_navbar_height);
		margin: 0 auto;
		width: 100%;
	}
}

*/
?>












.dots_lot{
	-webkit-box-flex: 1;
	    -ms-flex-positive: 1;
	        flex-grow: 1;
	margin: 0px 5px;
	margin-top: 20px;
	background: -o-linear-gradient(left, transparent, transparent 3px, #575b5e 3px, #575b5e 5px, transparent 5px);
	background: linear-gradient(to right, transparent, transparent 3px, #575b5e 3px, #575b5e 5px, transparent 5px);
	background-size: 8px 1px;
	height: 2px;
}



#toast-container>div{
	opacity: 1;
}


th[column]{
	-webkit-user-select: none;
	   -moz-user-select: none;
	    -ms-user-select: none;
	        user-select: none;
	cursor: pointer;
	white-space: nowrap;
	font-size: var(--v_font_small_extra);
}
th[column]::after{
	display: inline-block;
	font-family: bootstrap-icons !important;
	font-style: normal;
	font-weight: 400 !important;
	font-variant: normal;
	text-transform: none;
	line-height: 1;
	vertical-align: -.125em;
	-webkit-font-smoothing: antialiased;
	margin-left: 5px;
}
th[column][desc="1"]::after{
	content: "\f286";
}
th[column][desc="0"]::after{
	content: "\f282";
}






	






.head_page{
	display: flex;
	/*flex-wrap: wrap;*/
	flex-wrap: nowrap;
	align-items: center;
	gap: 15px;
	margin-top: 40px;
	margin-bottom: 50px;
}

.title_head_page{
	font-size: calc(20px + .3vw);
	margin-bottom: 0;
	order: 2;
}
.sub_title_head_page{
	font-size: calc(16px + .3vw);
	margin-bottom: 0;
}

.back_head_page{
	order: 1;
}

.save_head_page{
	margin-left: auto;
	order: 3;
}


/* для Мобилок */
@media (max-width: 1000px) {
	/*
	.head_page{
		margin-top: 20px;
	}
	.title_head_page{
		width: 100%;
		order: 3;
	}
	.save_head_page{
		order: 2;
	}
	*/
	
	.head_page{
		margin-top: 20px;
	}
}





[select2]{
	display:none;
}

div.form-select:not([select2]){
	height: 37.6px;
	padding: 0;
	flex-shrink: 0;
	width: 100%;
}

.select2-container{
	z-index: 100;
	color: var(--v_c_control_font)!important;
}
.select2-dropdown{
	box-shadow: 0px 0px 0px 2px var(--v_c_black);
}
.form-select  .select2-selection__arrow{
	display: none!important;
}
.form-select  .select2-container{
	width: 100%!important;
	height: 100%!important;
}
.form-select  .select2-selection__rendered{
	line-height: 100% !important;
	height: 100% !important;
	display: -webkit-box !important;
	display: -ms-flexbox !important;
	display: flex !important;
	-webkit-box-align: center !important;
	    -ms-flex-align: center !important;
	        align-items: center !important;
	
	padding-left: 12px !important;
	padding-right: 0px !important;
	
	color: var(--v_c_control_font)!important;
}
.form-select  .select2-selection{
	background-color: transparent!important;
	border: none!important;
	border-radius: 0!important;
	height: 100%!important;
	padding-right: 32px!important;
}


.select2-container:has(.select2-dropdown){
	-webkit-transform: translateY(5px);
	    -ms-transform: translateY(5px);
	        transform: translateY(5px);
}
.select2-search--dropdown{
	padding: 0;
}
.select2-dropdown{
	/* border: 1px solid #dee2e6!important; */
	border-radius: 5px!important;
	overflow-x: auto!important;
	border: none;
}
.select2-search--dropdown  input{
	border: none!important;
	border-bottom: 1px solid var(--v_c_black) !important;
	outline: none!important;
}
.select2-results__options  [role="alert"]{
	color: rgb(0,0,0,0.5);
	font-size: 12px;
}
.select2-results__option--selectable{
	overflow: hidden;
	white-space: nowrap;
	-o-text-overflow: ellipsis;
	   text-overflow: ellipsis;
}
.select2-results__option {
	color: var(--v_c_control_font_default);
}
.select2-results__option:not(:last-of-type){
	border-bottom: 1px solid var(--v_c_black_10);
}
.select2-container--default .select2-results__option--selected{
	background-color: rgb(88 151 251 / 30%);
	padding-right: 28px;
	position: relative;
}

.select2-container--default .select2-results__option--selected::after{
	content: '';
	display: block;
	position: absolute;
	border: 2px solid var(--v_c_black);
	border-top-color: transparent;
	border-right-color: transparent;
	transform: rotate(-45deg);
	
	right: 10px;
	bottom: 16px;
	width: 14px;
	height: 8px;
}


.form-select  .select2-selection--multiple{
	padding-bottom: 0px;
	/* padding-right: 15px !important; */
	padding-right: 0px !important;
}

.form-select  .select2-selection--multiple .select2-selection__rendered{
	margin: 0;
	display: flex !important;
	align-items: flex-start;
	justify-content: flex-start;
	flex-wrap: wrap;
	gap: 10px;
}
.form-select  .select2-selection--multiple .select2-selection__rendered:not(:empty){
	padding-top: 10px;
	
	padding-bottom: 10px;
	padding-right: 12px !important;
	border-bottom: 1px solid var(--v_c_border);
}

.form-select  .select2-selection--multiple .select2-selection__choice__display{
	padding: 0px 10px;
}

.form-select  .select2-selection--multiple .select2-selection__choice {
	margin: 0px;
}

.form-select  .select2-selection--multiple .select2-selection__choice{
	padding: 0;
	margin: 0;
}

.form-select  .select2-selection--multiple .select2-selection__choice__remove{
	position: relative;
	padding: 4px 8px;
	/*padding-bottom: 6px;*/
}
.form-select  .select2-selection--multiple .select2-search--inline .select2-search__field{
	margin-left: 0px;
	margin-top: 10px;
	margin-bottom: 10px;
	padding-left: 10px;
	width: 100%!important;
}

.form-select:has(select[multiple]){
	height: max-content!important;
	background-position: right .75rem bottom 11px;
}



.leaflet-control-attribution{
	display: none;
}


*. :before, :after{
	white-space: break-spaces;
	word-break: break-word;
}

.filter_custom  a{
	text-decoration: none;
	border-radius: 10px;
	font-size: 15px;
	font-weight: normal;
	font-weight: bold;
	white-space: nowrap;
	padding: 3px 10px;
}
.filter_custom  a  span{
	margin-right: 5px;
	font-weight: normal;
}






.empty::before{
	content: '-';
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	-webkit-box-align: center;
	    -ms-flex-align: center;
	        align-items: center;
	-webkit-box-pack: center;
	    -ms-flex-pack: center;
	        justify-content: center;
	width: 100%;
	height: 100px;
	background: rgb(0 0 0 / 10%);
}


table{
	font-size: 14px;
}

.table-bordered>:not(caption)>*>* {
	border: none;
}

@media (max-width: 700px) {
	.table_mobile{
		display: block;
	}
	
	.table_mobile  thead{
		display: block;
	}
	.table_mobile  thead  tr{
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-ms-flex-wrap: nowrap;
		    flex-wrap: nowrap;
		width: 100%;
		overflow-x: auto;
		gap: 10px;
		border: none;
	}
	.table_mobile  thead  tr  th{
		display: block;
		border: 1px solid #dee2e6;
		border-radius: 5px;
		padding: 5px 12px;
	}
	.table_mobile  thead  tr  th:empty{
		display: none;
	}
	
	.table_mobile  tbody{
		display: block;
		border: none;
	}
	.table_mobile  tbody  tr{
		display: block;
		width: 100%;
		padding: 15px 10px;
		border: 1px solid #dee2e6;
		border-width: 1px !important;
		margin-top: 20px;
		border-radius: 5px;
	}
	.table_mobile  tbody  tr  td{
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-ms-flex-wrap: nowrap;
		flex-wrap: nowrap;
		gap: 10px;
		padding: 0 !important;
		background: transparent !important;
		-webkit-box-shadow: none !important;
		box-shadow: none !important;
		border: none;
		margin-bottom: 10px;
		align-items: baseline;
	}
	.table_mobile  tbody  tr  td:last-of-type{
		margin-bottom: 0px;
	}
	.table_mobile  tbody  tr  td[data-name]::before{
		content: attr(data-name)':';
		display: block;
		margin-right: 10px;
		opacity: 0.5;
		width: 100px;
	}
}

</style>




<style>


[section]{
	width: 100%;
	padding: var(--v_p_40) 0;
}

/* для Мобилок */
@media (max-width: 1000px) {
	[section]{
		padding: var(--v_p_30) 0;
	}
	
	
	@media (hover: none), (pointer: coarse) {
		/* Для полной скрытия скроллбара, но с возможностью прокрутки */
		.mobile_scroll_hide::-webkit-scrollbar {
			display: none; /* для Webkit (Chrome, Safari) */
		}

		.mobile_scroll_hide{
			-ms-overflow-style: none;  /* для Internet Explorer и Edge */
			scrollbar-width: none;  /* для Firefox */
		}
    }
}

.box_search{
	display: flex;
	flex-wrap: nowrap;
	gap: var(--v_p_10);
}

.input_search{
	background: var(--v_c_white);
	position: relative;
	/* overflow: hidden; */
	border-radius: var(--v_radius);
	width: 100%;
	flex-shrink: 1;
	height: 60px;
}

.input_search  i{
	position: absolute;
	left: 0px;
	top: 0px;
	width: 50px;
	height: 100%;
	font-size: 25px;
	display: flex;
	align-items: center;
	justify-content: center;
	opacity: var(--v_opacity_5);
	pointer-events: none;
	z-index: 101;
}


.input_search  input,
.input_search  .select2-selection__rendered{
	width: 100%;
	height: 100%;
	background: transparent;
	border: none;
	outline: none;
	padding-left: 55px;
}

.input_search  .form-select{
	height: 100% !important;
	background: transparent;
	border: none;
	outline: none;
	padding-left: 33px !important;
}

.input_search:has(input:focus)::after{
	content: '';
	position: absolute;
	left: 0;
	bottom: 0;
	width: 100%;
	height: 2px;
	background: var(--v_c_black)!important;
}
.input_search:has(input:focus) i{
	opacity: 1;
}

/* для Hover */
@media (hover: hover) {
	.input_search:hover:after{
		content: '';
		position: absolute;
		left: 0;
		bottom: 0;
		width: 100%;
		height: 2px;
		background: var(--v_c_black_30);
	}
}

.btn_search{
	display: flex;
	height: 60px;
	justify-content: center;
	align-items: center;
	min-width: 100px;
	flex-shrink: 0;
	padding-left: var(--v_p_40);
	padding-right: var(--v_p_40);
}


/* для Мобилок */
@media (max-width: 1000px) {
	
	.input_search{
		flex-shrink: 0;
		height: 40px;
	}
	.input_search  i{
		font-size: 20px;
		width: 45px;
	}
	.input_search  input{
		padding-left: 45px;
		font-size: 18px;
	}
	.box_search {
		flex-wrap: wrap;
	}
	.btn_search{
		width: 100%;
		height: 40px;
	}
	
}






/* для select City */
.search_section  .select2-container{
	
	top: 0!important;
	left: 0!important;
	width: 100%!important;
	
	transform: translateY(0px);
	-webkit-transform: translateY(0px);
	-ms-transform: translateY(0px);
}

.search_section   .input_search .form-select {
	padding-left: 0px!important;
}

.search_section   .select2-selection{
	padding-right: 0 !important;
}

.search_section  .select2-dropdown{
	width: 100% !important;
}

.search_section   .select2-selection__rendered{
	padding-left: 55px!important;
	font-size: var(--v_font_default);
}
.search_section  .select2-search--dropdown input{
	padding-left: 55px;
	height: 60px;
}

.search_section  .select2-results__option {
	padding-left: 55px;
	padding-right: 0px;
}

.search_section  .select2-container--default .select2-results__option--selected::after{
	right: unset;
	left: 17px;
	bottom: 18px;
}


/* для Мобилок */
@media (max-width: 1000px) {
	.search_section  .select2-search--dropdown input{
		height: 40px;
	}
	
	.search_section   .select2-selection__rendered{
		padding-left: 45px!important;
	}
	.search_section  .select2-search--dropdown input{
		padding-left: 45px;
	}

	.search_section  .select2-results__option {
		padding-left: 45px;
	}
}


</style>




<style>

.list_card_ad{
	display: flex;
	align-items: flex-start;
	justify-content: center;
	flex-wrap: wrap;
	margin: 0 -5px;
}

.item_ad{
	display: block!important;
	width: 25%!important;
	user-select: none;
	padding: var(--v_p_5);
}

.list_card_ad  .title_item_ad{
	padding-right: var(--v_p_5);
}

.list_card_ad  .body_item_ad{
	background: var(--v_c_white);
	border: 1px solid var(--v_c_border);
	border-radius: var(--v_radius);
}
.body_item_ad{
	display: flex;
	flex-direction: column;
	flex-wrap: nowrap;
	height: 330px;
	text-decoration: none;
	color: var(--v_c_black);
	/*
	*/
	overflow: hidden;
	position: relative;
}

.btn_tool_item_ad{
	height: var(--v_p_25);
	width: var(--v_p_25);
	margin-bottom: var(--v_p_5);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: var(--v_p_20);
	padding-top: 1px;
	flex-shrink: 0;
	cursor: pointer;
	user-select: none;
}

.img_item_ad{
	display: block;
	width: 100%;
	height: 200px;
	object-fit: cover;
	flex-shrink: 0;
}

.text_item_ad{
	line-height: 1.2;
	padding: var(--v_p_15);

	display: flex;
	flex-direction: column;
	flex-wrap: nowrap;
	justify-content: space-between;
	height: 100%;
}

.title_item_ad{
	font-size: var(--v_font_small);
	margin-bottom: var(--v_p_5);
	
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
	text-overflow: ellipsis;
}

.price_item_ad{
	margin-bottom: var(--v_p_10);
	font-weight: bold;
	opacity: var(--v_opacity_50);
	font-size: var(--v_font_small);
}
.city_item_ad{
	margin-top: auto;
	margin-bottom: 2px;
	font-size: var(--v_font_small_extra);
}
.date_item_ad{
	font-size: var(--v_font_small_extra);
}



/* для Мобилок */
@media (max-width: 1000px) {
	.item_ad{
		width: 50%!important;
	}
	
	.list_card_ad  .body_item_ad{
		/* height: 280px; */
		height: 260px;
	}
	
	.text_item_ad{
		padding: var(--v_p_10);
	}
	
	
	.title_item_ad{
		font-size: var(--v_font_small_extra);
	}
	.btn_tool_item_ad {
		height: var(--v_p_20);
		width: var(--v_p_20);
		font-size: var(--v_p_15);
	}
	.img_item_ad{
		height: 140px;
	}
}





.swiper-button-next,
.swiper-button-prev{
	background: var(--v_c_white_50);
	width: 40px;
	height: 40px;
	border-radius: 50%;
}

.swiper-button-next:after, .swiper-button-prev:after {
	font-size: 20px;
	color: var(--v_c_black);
}
.swiper-button-next:after{
	margin-left: 3px;
}
.swiper-button-prev:after {
	margin-right: 3px;
}





.ads_swiper_top{
	margin-left: -5px!important;
	margin-right: -5px!important;
	width: 100%;
}
.ads_swiper_top  .swiper-wrapper {
	width: 100%;
	height: 100%;
}

.ads_swiper_top  .swiper-slide {
	height: max-content;
	width: max-content;
	max-width: 100%;
	max-height: 100%;
}

.ads_swiper_top  .swiper-button-next,
.ads_swiper_top  .swiper-button-prev {
	background: var(--v_c_white);
	color: var(--v_c_black);
	border-radius: 50%;
	width: 30px;
	height: 30px;
	opacity: 0.8;
	border: 1px solid var(--v_c_black_50);
}

/* для Мобилок */
@media (max-width: 1000px) {
	.ads_swiper_top  .swiper-button-next,
	.ads_swiper_top  .swiper-button-prev {
		display: none;
	}
}

.ads_swiper_top  .swiper-button-next::after,
.ads_swiper_top  .swiper-button-prev::after{
	transform: scale(0.3);
	font-size: 40px;
}


.ads_swiper_top  .swiper-button-next:hover,
.ads_swiper_top  .swiper-button-prev:hover {
	opacity: 1;
}

.ads_swiper_top  .title_item_ad {
	-webkit-line-clamp: 1;
}


.ads_swiper_top  .body_item_ad{
	height: 225px;
	background: none;
	border: none;
}
.ads_swiper_top  .text_item_ad {
	padding: var(--v_p_10);
	padding-bottom: 0px;
	padding-left: 0px;
}

.ads_swiper_top  .city_item_ad,
.ads_swiper_top  .date_item_ad{
	display: none;
}
.ads_swiper_top  .img_item_ad{
	height: 160px;
}

/* для Мобилок */
@media (max-width: 1000px) {
	.ads_swiper_top  .body_item_ad{
		height: 190px;
	}
	.ads_swiper_top  .img_item_ad{
		height: 130px;
	}
	.ads_swiper_top  .text_item_ad {
		padding-bottom: 0px;
		padding-left: 0px;
	}
}

</style>



<style>

[ads_list_type].loader:after{
	content: '';
	margin: var(--v_p_20) auto;
	width: var(--v_p_50);
	height: var(--v_p_50);
	border: var(--v_p_5) solid var(--v_c_black);
	border-bottom-color: transparent;
	border-radius: 50%;
	display: inline-block;
	box-sizing: border-box;
	animation: kf_loader_rotate 1s linear infinite;
}

@keyframes kf_loader_rotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
} 

</style>



<style>
.list_line_ads{
	display: flex;
	align-items: flex-start;
	justify-content: center;
	flex-wrap: wrap;
	/*
	margin: 0 -5px;
	padding: 0 var(--v_p_5);
	*/
	margin-bottom: var(--v_p_50);
}


.list_line_ads  .item_ad{
	width: 100%!important;
	padding: 0px;
	color: var(--v_c_black);
	background: var(--v_c_white);
	border: 1px solid var(--v_c_border);
	border-radius: var(--v_radius);
	overflow: hidden;
	position: relative;
	margin-bottom: 10px;
}

.list_line_ads  .item_ad  .btn_item_ad{
	position: absolute;
	top: var(--v_p_15);
	right: var(--v_p_15);
}

.list_line_ads  .item_ad  .settings_item_ad{
	border-top: 1px solid var(--v_c_border);
	padding: var(--v_p_10) var(--v_p_15);
	gap: var(--v_p_10) var(--v_p_25);
	font-size: var(--v_font_small);
	line-height: 1;
	display: flex;
	align-items: center;
	justify-content: flex-end;
	flex-wrap: wrap;
}

.list_line_ads  .item_ad  .settings_item_ad  .btn_settings_item_ad{
	gap: var(--v_p_10) var(--v_p_20);
	display: flex;
	flex-wrap: nowrap;
}

.list_line_ads  .item_ad  .settings_item_ad  > * {
	
}

.list_line_ads  .item_ad  .settings_item_ad  i {
	margin-right: var(--v_p_10);
}

.list_line_ads  .item_ad  .settings_item_ad  .id_settings_item_ad{
	margin-right: auto;
	color: var(--v_c_black_50);
}


.list_line_ads  .body_item_ad {
	display: flex;
	flex-direction: row;
	flex-wrap: nowrap;
	height: auto;
}

.list_line_ads  .img_item_ad {
	display: block;
	width: 200px;
	height: 160px;
	object-fit: cover;
	margin: var(--v_p_10);
	margin-right: 0;
	border-radius: var(--v_radius);
}

.list_line_ads  .title_item_ad {
	font-size: var(--v_font_default);
	padding-right: var(--v_p_30);
}
.list_line_ads  .text_item_ad {
	padding: var(--v_p_15);
	justify-content: space-between;
	height: 180px;
	width: 100%;
}


/* для Мобилок */
@media (max-width: 1000px) {
	.list_line_ads {
		margin-bottom: 0;
	}
	.list_line_ads  .item_ad  .settings_item_ad {
		font-size: var(--v_font_small_extra);
	}
	.list_line_ads  .item_ad  .settings_item_ad  > * {
		text-align: center;
	}
	
	.list_line_ads  .item_ad  .settings_item_ad  .btn_settings_item_ad{
		width: 100%;
		flex-wrap: wrap;
	}

	.list_line_ads  .item_ad  .settings_item_ad  > *:not(.btn) >  i{
		display: block;
		margin-bottom: var(--v_p_5);
		margin-right: 0;
	}
	.list_line_ads  .item_ad  .settings_item_ad  .btn{
		display: block;
		margin-bottom: var(--v_p_5);
		margin-right: 0;
		width: calc(50% - var(--v_p_10));
		/*width: 50%;*/
	}
	.list_line_ads .img_item_ad {
		width: 150px;
		margin: 0;
		border-radius: 0;
	}
	.list_line_ads .text_item_ad {
		height: 160px;
		padding: var(--v_p_10);
	}
	.list_line_ads  .title_item_ad {
		font-size: var(--v_font_small);
	}
}
</style>



<style>
	
	body :has(.breadcump_page) .head_page{
		margin-top: var(--v_p_20);
	}
	
	.breadcump_page {
		margin-top: var(--v_p_20);
		font-size: var(--v_font_small_extra);
	}
	
	.breadcump_page ul {
		list-style: none;
		padding: 0;
		margin: 0;
		display: flex;
		align-items: flex-start;
		flex-wrap: wrap;
	}

	.breadcump_page ul li {
		position: relative;
		margin-right: var(--v_p_10);
	}

	.breadcump_page ul li:not(:last-child)::after {
		content: "";
		width: 8px;
		height: 8px;
		margin-left: var(--v_p_10);
		border: 1px solid var(--v_c_black_50);
		border-left: none;
		border-top: none;
		display: inline-block;
		transform: translate(-2px, -1px) rotate(-45deg);
	}

	.breadcump_page a {
		text-decoration: none;
		color: var(--v_c_black_50);
	}
	
	.breadcump_page ul li:last-child a{
		color: var(--v_c_black);
	}
	
	.breadcump_page a:hover{
		color: var(--v_c_black);
		text-decoration: underline;
	}
	
	/* для Мобилок */
	@media (max-width: 1000px) {
		.breadcump_page ul {
			flex-wrap: nowrap;
			overflow-x: auto;
		}
		.breadcump_page ul::-webkit-scrollbar {
			display: none;
		}
		.breadcump_page ul li {
			flex-shrink: 0;
		}
	}

</style>