<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('Form.php');
require_once('functions.php');
require_once('session.php');

//установим связь с репозиторием базы yeticave
$repo = new Repository();

$errors = array();
$layoutContent = null;

$user = [
	'name' => getPostVal('name', ''),
	'email' => getPostVal('email', ''),
	'password' => getPostVal('password', ''),
	'message' => getPostVal('message', '')
];

//правила верификации для полей формы
$userRules = [
  'name' => function($user) {
    $error = validateFilled('name', $user, 'Введите имя');
	if ($error === null) {
		$error = validateLogin('name', $user);
	}
    return $error;
  },
  'email' => function($user) {
    $error = validateFilled('email', $user, 'Введите email');
    if ($error === null) {
      $error = validateEmail('email', $user, '');
    }
    return $error;
  },
  'password' => function($user) {
    $error = validateFilled('password', $user, 'Введите пароль, не менее 6 символов');
    if ($error === null) {
      $error = isCorrectLength('password', $user, 6, 16);
    }
    return $error;
  },
  'message' => function($user) {
    $error = validateFilled('message', $user, 'Напишите, как с вами связаться, не более 255 знаков');
    if ($error === null) {
      $error = isCorrectLength('message', $user, 6, 255);
    }
    return $error;
  }
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  foreach ($_POST as $key => $value) {
    $user[$key] = $value;
  }
  //валидация полей формы
  Form::validateFields($userRules, $user);
  $errors = Form::getErrors();
  if ($repo->isOk() and  count($errors) == 0) {
    //добавим юзера в базу
    $result = $repo->registerNewUser($user);
    if ($result) {
      //переход на страницу авторизации
      header("Location:/login.php");
      exit();
    }
  }
}

//работаем с ошибками формы
if ($repo->isOk()) {
  $cats = $repo->getAllCategories();
  if ($repo->iSOk()) {
    $navContent = include_template('nav.php', [
      'cats' => $cats
    ]);
    $signUpContent = include_template('sign-up.php', [
      'nav' => $navContent,
      'user' => $user,
      'errors' => $errors
    ]);
    $layoutContent = include_template('layout.php', [
      'nav' => $navContent,
      'is_auth' => $isAuth,
      'content' => $signUpContent,
      'cats' => $cats,
      'title' => $title,
      'user_name' => $userName
    ]);
  } 
}

//какая-то ошибка при обработке запроса
if (!$repo->isOk()) {
  $layoutContent = include_template('error.php', [
    'error' => $repo->getError()
  ]);
} 

print($layoutContent);
