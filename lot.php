<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('functions.php');

$lotContent = null;
$layoutContent = null;
$error = null;
$userName = 'Alex';
$isAuth = 0;

//установим связь с репозиторием базы yeticave
$repo = new Repository();

if (isset($_GET['id'])) {
  $lotId = intval($_GET['id']);
  if ($repo->isOk()) {
    $cats = $repo->getAllCategories();
    $lot = $repo->getLot($lotId);
    $bet = $repo->getMaxBet($lotId);
    if ($repo->isOk()) {
      $lotContent = include_template('lot.php', [
        'lot' => $lot,
        'cats' => $cats,
        'bet' => $bet
      ]);

      $layoutContent = include_template('layout.php', [
        'isAuth' => $isAuth,
        'content' => $lotContent,
        'cats' => $cats,
        'title' => $lot['name'],
        'userName' => $userName
      ]);
    } else {
      http_response_code(404);
    }
  } else {
    $error = $repo->getError();
  }
}
if ($error != null) {
  $layoutContent = include_template('error.php', [
    'error' => $error
  ]);
} 

print($layoutContent);
