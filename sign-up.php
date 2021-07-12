<?php
require_once 'helpers.php';
require_once 'Form.php';
require_once 'functions.php';
require_once 'session.php';

$errors = array();
$layoutContent = null;

$user = [
    'name' => $repo->getEscapeStr(getPostVal('name', '')),
    'email' => $repo->getEscapeStr(getPostVal('email', '')),
    'password' => $repo->getEscapeStr(getPostVal('password', '')),
    'message' => $repo->getEscapeStr(getPostVal('message', ''))
];

//правила верификации для полей формы
$userRules = [
    'name' => function ($user) {
        $error = validateFilled('name', $user, 'Введите имя');
        return $error;
    },
    'email' => function ($user) {
        $error = validateFilled('email', $user, 'Введите email');
        if ($error === null) {
            $error = validateEmail('email', $user, '');
        }
        return $error;
    },
    'password' => function ($user) {
        $error = validateFilled('password', $user, 'Введите пароль, не менее 6 символов');
        if ($error === null) {
            $error = isCorrectLength('password', $user, 6, 16);
        }
        return $error;
    },
    'message' => function ($user) {
        $error = validateFilled('message', $user, 'Напишите, как с вами связаться, не более 255 знаков');
        if ($error === null) {
            $error = isCorrectLength('message', $user, 6, 255);
        }
        return $error;
    }
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        $user[$key] = $repo->getEscapeStr($value);
    }
    //валидация полей формы
    Yeticave\Form::validateFields($userRules, $user);
    $errors = Yeticave\Form::getErrors();
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
        $navContent = includeTemplate('nav.php', [
            'cats' => $cats
        ]);
        $signUpContent = includeTemplate('sign-up.php', [
            'nav' => $navContent,
            'user' => $user,
            'errors' => $errors
        ]);
        $layoutContent = includeTemplate('layout.php', [
            'nav' => $navContent,
            'is_auth' => $isAuth,
            'content' => $signUpContent,
            'cats' => $cats,
            'title' => $title,
            'user_name' => $userName
        ]);
    }
}

if (!$repo->isOk()) {
    //какая-то ошибка при обработке запроса
    $layoutContent = includeTemplate('error.php', [
        'error' => $repo->getError()
    ]);
}

print($layoutContent);
