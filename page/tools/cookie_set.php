<?php

$name = $_GET['name'];
$value = $_GET['value'];
$expired_date = $_GET['expired_date'] ?: "+365 days";
$redirect = $_GET['redirect'] ?: "/";

f_cookie_set($name, $value, strtotime($expired_date));

f_redirect($redirect);

?>