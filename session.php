<?php
$userName = '';
$isAuth = 0;
$authorId = 0;

$title = 'YetiCave';
session_start();

//данные для авторизации
if (isset($_SESSION['user']['id']) and 
  $_SESSION['serv']['agent'] == $_SERVER['HTTP_USER_AGENT'] and
  $_SESSION['serv']['addr'] == $_SERVER['REMOTE_ADDR']) {
    $authorId = $_SESSION['user']['id'];
    $userName = $_SESSION['user']['name'];
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

//данные для пагинации
$lotsPerPage = 9;
$pagesCount = 1;
$curPage = $_GET['page'] ?? 1;
$offset = 0;
$pages = array(1);
$url = explode('?', $_SERVER['REQUEST_URI'])[0];
