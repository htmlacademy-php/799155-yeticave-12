<?php
require_once 'helpers.php';
require_once 'Form.php';
require_once 'functions.php';
require_once 'session.php';

$errors = array();
$layoutContent = null;

$user = [
    'email' => $repo->getEscapeStr(getPostVal('email', '')),
    'password' => $repo->getEscapeStr(getPostVal('password', ''))
];

//правила верификации для полей формы
$userRules = [
    'email' => function ($user) {
        $error = validateFilled('email', $user, 'Введите email');
        if ($error === null) {
            $error = validateEmail('email', $user, true);
        }
        return $error;
    },
    'password' => function ($user) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $user[$key] = $repo->getEscapeStr($value);
    }
    //валидация полей формы
    Yeticave\Form::validateFields($userRules, $user);
    $errors = Yeticave\Form::getErrors();
    if ($repo->isOk() and  count($errors) === 0) {
        //поищем юзера с этим email'ом
        $author = $repo->getUser($user['email']);
        if ($user) {
            //начинаем новую сессия с пользователем
            $_SESSION['user'] = [
                'id' => $author['id'],
                'name' => $author['name']
            ];
            //запомним некоторые поля для доп.контроля сессии
            $_SESSION['serv'] = [
                'agent' => $_SERVER['HTTP_USER_AGENT'],
                'addr' => $_SERVER['REMOTE_ADDR']
            ];
            if (isset($_SESSION['page'])) {
                //возврат на страницу, на которой юзер был до авторизации
                header("Location:" . $_SESSION['page']);
                exit();
            }
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
    $navContent = includeTemplate('nav.php', [
        'cats' => $cats
    ]);
    if ($repo->iSOk()) {
        $loginContent = includeTemplate('login.php', [
            'nav' => $navContent,
            'cats' => $cats,
            'user' => $user,
            'errors' => $errors
        ]);
        $layoutContent = includeTemplate('layout.php', [
            'is_auth' => $isAuth,
            'content' => $loginContent,
            'cats' => $cats,
            'title' => $title,
            'user_name' => $userName,
            'nav' => $navContent,
            'search' => '',
            'url' => $url,
            'pagesCount' => $pagesCount,
            'curPage' => $curPage,
            'pages' => $pages
        ]);
    }
}
//какая-то ошибка при обработке запроса
if (!$repo->isOk()) {
    $layoutContent = includeTemplate('error.php', [
        'error' => $repo->getError()
    ]);
}

print($layoutContent);
