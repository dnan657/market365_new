<?php

if( session_id() == $WEB_JSON['uri_dir_arr'][2] ){
	f_user_exit();
}else{
	f_redirect('/');
}

?>