<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('Form.php');
require_once('functions.php');
require_once('session.php');

$lotContent = null;
$layoutContent = null;
$error = null;
$errors = array();
$lotId = 0;
$lot = array();

//установим связь с репозиторием базы yeticave
$repo = new Repository();

$cats = $repo->getAllCategories();
$navContent = include_template('nav.php', [
  'cats' => $cats
]);

$bet = [
  'cost' => getPostVal('cost', '0'),
  'lot-id' => getPostVal('lot-id', '0')
];

//правила верификации для полей формы
$betRules = [
  'cost' => function($bet) {
    $error = validateFilled('cost', $bet, 'Укажите величину ставки');
    if ($error === null) {
      $error = validateBet('cost', 'lot-id', $bet);
    }
    return $error;
  }
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($_POST as $key => $value) {
    $bet[$key] = $value;
  }
  $lotId = $bet['lot-id'];
  $maxBet = $repo->getMaxBet($lotId);
  $lot = $repo->getLot($lotId);

  //валидация полей формы
  Form::validateFields($betRules, $bet);
  $errors = Form::getErrors();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['id'])) {
    $lotId = intval($_GET['id']);
    $lot = $repo->getLot($lotId);
    if ($repo->isOk()) {
      if ($lot) {
        $maxBet = $repo->getMaxBet($lotId);
      } else {
        http_response_code(404);
        exit();
      }
    } else {
      $error = $repo->getError();
    }
  }
}

if ($error === null) {
  $lotContent = include_template('lot.php', [
    'is_auth' => $isAuth,
    'lot' => $lot,
    'nav' => $navContent,
    'max_bet' => $maxBet,
    'errors' => $errors,
    'bet' => $bet
  ]);

  $layoutContent = include_template('layout.php', [
    'nav' => $navContent,
    'is_auth' => $isAuth,
    'content' => $lotContent,
    'cats' => $cats,
    'title' => $lot['name'],
    'userName' => $userName
  ]);
} else {
  $layoutContent = include_template('error.php', [
    'error' => $error
  ]);
} 

print($layoutContent);
