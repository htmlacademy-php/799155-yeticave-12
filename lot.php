<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('functions.php');

$lot_content = null;
$error = null;

//установим связь с репозиторием базы yeticave
$repo = new Repository();

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  if ($repo->isOk()) {
    $cats = $repo->getAllCategories();
    $lot = $repo->getLot(12);
    if ($repo->isOk()) {
      $lot_content = include_template('lot.php', [
        'lot' => $lot,
        'cats' => $cats
      ]);
    } else {
      http_response_code(404);
    }
  } else {
    $error = $repo->getError();
  }
}
if ($error != null) {
  $lot_content = include_template('error.php', [
    'error' => $error
  ]);
} 

print($lot_content);
