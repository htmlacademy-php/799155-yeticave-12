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
  $isAuth = 1;
}
