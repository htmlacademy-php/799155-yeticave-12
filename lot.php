<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('functions.php');

$lot_content = null;
$layout_content = null;
$error = null;
$user_name = 'Alex';
$is_auth = 0;

//установим связь с репозиторием базы yeticave
$repo = new Repository();

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  if ($repo->isOk()) {
    $cats = $repo->getAllCategories();
    $lot = $repo->getLot($id);
    if ($repo->isOk()) {
      $lot_content = include_template('lot.php', [
        'lot' => $lot,
        'cats' => $cats
      ]);

      $layout_content = include_template('layout.php', [
        'is_auth' => $is_auth,
        'content' => $lot_content,
        'cats' => $cats,
        'title' => $lot['name'],
        'user_name' => $user_name
      ]);
    } else {
      http_response_code(404);
    }
  } else {
    $error = $repo->getError();
  }
}
if ($error != null) {
  $layout_content = include_template('error.php', [
    'error' => $error
  ]);
} 

print($layout_content);
