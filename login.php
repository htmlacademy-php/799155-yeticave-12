<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('Form.php');
require_once('functions.php');
require_once('session.php');

//установим связь с репозиторием базы yeticave
$repo = new Repository();

$error = null;
$errors = array();
$layoutContent = null;

$user = [
	'email' => getPostVal('email', ''),
	'password' => getPostVal('password', '')
];

//правила верификации для полей формы
$userRules = [
  'email' => function($user) {
    $error = validateFilled('email', $user, 'Введите email');
    if ($error === null) {
      $error = validateEmail('email', $user, true);
    }
    return $error;
  },
  'password' => function($user) {
    $error = validateFilled('password', $user, 'Введите пароль, не менее 6 символов');
    if ($error === null) {
      $error = isCorrectLength('password', $user, 6, 16);
    }
	if ($error === null) {
		$error = validatePassword('password', 'email', $user);
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
	  //поищем юзера с этим email'ом
	  $author = $repo->getUser($user['email']);
	  if ($user) {
      $_SESSION['user_id'] = $author['id'];
      $_SESSION['user_name'] = $author['name'];
      $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
      $_SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR']; 
    
		  header("Location: /index.php");
      exit();
	  } else {
		  $errors['email'] = "Польэователь с этим email не найден";
	  }
  }
}

//работаем с ошибками формы
if ($repo->isOk()) {
  $cats = $repo->getAllCategories();
  if ($repo->iSOk()) {
    $loginContent = include_template('login.php', [
      'cats' => $cats,
      'user' => $user,
      'errors' => $errors
    ]);
    $layoutContent = include_template('layout.php', [
      'is_auth' => $isAuth,
      'content' => $loginContent,
      'cats' => $cats,
      'title' => $title,
      'userName' => $userName
    ]);
  } else {
    $error = $repo->getError();
  }  
}

//какая-то ошибка при обработке запроса
if ($error !== null) {
  $layoutContent = include_template('error.php', [
    'error' => $error
  ]);
} 

print($layoutContent);
