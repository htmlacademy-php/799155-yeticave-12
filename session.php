<?php
$userName = '';
$isAuth = 0;
$authorId = 0;

$title = 'YetiCave';
session_start();
if (isset($_SESSION['user_id']) and 
  $_SESSION['user_agent'] == $_SERVER['HTTP_USER_AGENT'] and
  $_SESSION['remote_addr'] == $_SERVER['REMOTE_ADDR'] and 
  $_SESSION['http_x'] == $_SERVER['HTTP_X_FORWARDED_FOR']) {
  $authorId = $_SESSION['user_id'];
  $userName = $_SESSION['user_name'];
  if (isset($_SESSION['page'])) {
	  unset($_SESSION['page']);
  }
  $isAuth = 1;
}else {
  //запомним страницу, на которую юзер заходил до авторизации
  //чтобы вернуть ее после авторизации
	if (!strstr($_SERVER['REQUEST_URI'], 'login.php') and 
		!strstr($_SERVER['REQUEST_URI'], 'sign-up.php')) {
		$_SESSION['page'] = $_SERVER['REQUEST_URI'];
	}
}
