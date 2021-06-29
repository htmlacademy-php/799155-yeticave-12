<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('Form.php');
require_once('functions.php');
require_once('session.php');

$errors = array();
$layoutContent = null;

//данные полей формы
$lot = [
  'lot-name' => $repo->getEscapeStr(getPostVal('lot-name', '')),
  'lot-date' => $repo->getEscapeStr(getPostVal('lot-date', '')), 
  'lot-step' => $repo->getEscapeStr(getPostVal('lot-step', 0)), 
  'lot-rate' => $repo->getEscapeStr(getPostVal('lot-rate', 0)), 
  'message' => $repo->getEscapeStr(getPostVal('message', '')), 
  'category' => $repo->getEscapeStr(getPostVal('category', '0')),
  'lot-img' => $repo->getEscapeStr(getPostVal('lot_img', '')),
  'new-img' => $repo->getEscapeStr(getPostVal('new_img', ''))
];


//правила верификации для полей формы
$lotRules = [
  'lot-name' => function($lot) {
    $error = validateFilled('lot-name', $lot, 'Введите наименование лота');
    return $error;
  },
  'lot-date' => function($lot) {
    $error = validateFilled('lot-date', $lot, 'Введите дату в формате ГГГГ-ММ-ДД');
    if ($error === null) {
      $error = validateDate('lot-date', $lot, 'Формат даты ГГГГ-ММ-ДД');
    }
    return $error;
  },
  'lot-step' => function($lot) {
    $error = validateFilled('lot-step', $lot, 'Введите шаг ставки');
    if ($error === null) {
      $error = isNumeric('lot-step', $lot);
    }
    return $error;
  },
  'lot-rate' => function($lot) {
    $error = validateFilled('lot-rate', $lot, 'Введите начальную цену');
    if ($error === null) {
      $error = isNumeric('lot-rate', $lot);
    }
    return $error;
  },
  'category' => function($lot) {
    $error = isCorrectId('category', $lot, 'Выберите категорию');
    return $error;
  },
  'message' => function($lot) {
    $error = validateFilled('message', $lot, 'Напишите описание лота, не менее 64 знаков, не более 512');
    if ($error === null) {
      $error = isCorrectLength('message', $lot, 64, 512);
    }
    return $error;
  }
];

if ($isAuth === 0) {
  header("HTTP/1.0 403 Forbidden");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($_POST as $key => $value) {
    $lot[$key] = $repo->getEscapeStr($value);
  }

  //валидация полей формы
  Form::validateFields($lotRules, $lot);
  $errors = Form::getErrors();
  //отдельно валидация файла с изображением и перенос его в папку uploads
  if (empty($lot['lot-img'])) {
    if (Form::validateImageFile('lot-img', 'Укажите файл с изображением')) {
      $lot['lot-img'] = Form::getFileName();
      $lot['new-img'] = Form::getNewFileName();
    } else {
      $errors['lot-img'] = Form::getMessage();
    }
  }
  if ($repo->isOk() and  count($errors) == 0) {
    //запишем данные лота в базу
    $repo->addNewLot($lot, $authorId);
    //если все ок, переместимся на станицу лота
    if ($repo->isOk()) {
      $id = $repo->getLastId();
      header("Location:/lot.php?id=" . $id);
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
    $addContent = include_template('add.php', [
      'nav' => $navContent,
      'lot' => $lot,
      'cats' => $cats,
      'errors' => $errors
    ]);
    $layoutContent = include_template('layout.php', [
      'nav' => $navContent,
      'is_auth' => $isAuth,
      'content' => $addContent,
      'cats' => $cats,
      'title' => $title,
      'user_name' => $userName,
      'user_id' => $authorId
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
