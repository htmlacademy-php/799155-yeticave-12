<?php
require_once 'helpers.php';
require_once 'Form.php';
require_once 'functions.php';
require_once 'session.php';

$lotContent = null;
$layoutContent = null;
$errors = array();
$lotId = 0;
$lot = array();
$betHistory = array();

$bet = [
    'cost' => $repo->getEscapeStr(getPostVal('cost', '0')),
    'lot-id' => $repo->getEscapeStr(getPostVal('lot-id', '0'))
];

//правила верификации для полей формы
$betRules = [
    'cost' => function ($bet) {
        $error = validateFilled('cost', $bet, 'Укажите величину ставки');
        if ($error === null) {
            $error = isNumeric('cost', $bet);
        }
        if ($error === null) {
            $error = validateBet('cost', 'lot-id', $bet);
        }
        return $error;
    }
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $bet[$key] = $repo->getEscapeStr($value);
    }
    $lotId = $bet['lot-id'];
    $lot = $repo->getLot($lotId);

    //валидация полей формы
    Yeticave\Form::validateFields($betRules, $bet);
    $errors = Yeticave\Form::getErrors();
    if ($repo->isOk() and  count($errors) === 0) {
        //запишем данные ставки в базу
        $repo->addNewBet($bet, $lotId, $authorId);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $lotId = intVal($repo->getEscapeStr($_GET['id']));
        $lot = $repo->getLot($lotId);
        if ($repo->isOk()) {
            if ($lot === false) {
                http_response_code(404);
                exit();
            }
        }
    }
}

if ($repo->isOk()) {
    $cats = $repo->getAllCategories();
    $navContent = includeTemplate('nav.php', [
        'cats' => $cats
    ]);
    if ($repo->isOk()) {
        $lotContent = includeTemplate('lot.php', [
            'is_auth' => $isAuth,
            'lot' => $lot,
            'nav' => $navContent,
            'max_bet' => $repo->getMaxBet($lotId),
            'errors' => $errors,
            'bet' => $bet,
            'user_id' => $authorId,
            'bet_history' => $repo->getBetHistory($lotId)
        ]);
        $layoutContent = includeTemplate('layout.php', [
            'nav' => $navContent,
            'is_auth' => $isAuth,
            'content' => $lotContent,
            'cats' => $cats,
            'title' => $lot['name'],
            'user_name' => $userName,
            'user_id' => $authorId
        ]);
    }
}
if (!$repo->isOk()) {
    $layoutContent = includeTemplate('error.php', [
        'error' => $repo->getError()
    ]);
}

print($layoutContent);
