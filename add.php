<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('Form.php');
require_once('functions.php');

$authorId = 1;
$userName = 'Alex';
$isAuth = 1;
$title = 'YetiCave';

//установим связь с репозиторием базы yeticave
$repo = new Repository();

$error = null;
$errors = array();
$layoutContent = null;

//данные полей формы
$lot = [
  'lot-name' => getPostVal('lot-name', ''),
  'lot-date' => getPostVal('lot-date', ''), 
  'lot-step' => getPostVal('lot-step', 0), 
  'lot-rate' => getPostVal('lot-rate', 0), 
  'message' => getPostVal('message', ''), 
  'category' => getPostVal('category', '0'),
  'lot-img' => getPostVal('lot_img', ''),
  'new-img' => getPostVal('new_img', '')
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  foreach ($_POST as $key => $value) {
    $lot[$key] = $value;
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
    //поищем дубль лота в БД
    $row = $repo->findSimilarLot('lot-name', $lot);
    if (isset($row['id'])) {
      //переместимся на станицу лота
      header("Location:/lot.php?id=" . $row['id']);
      exit();
    } else {
      //запишем данные лота в базу
      $result = $repo->addNewLot($lot, $authorId);
      //если все ок, переместимся на станицу лота
      if ($result) {
        $id = $repo->getLastId();
        header("Location:/lot.php?id=" . $id);
        exit();
      } else {
        $error = $repo->getError();
      }
    }
  } 
}

//работаем с ошибками формы
if ($repo->isOk()) {
  $cats = $repo->getAllCategories();
  if ($repo->iSOk()) {
    $addContent = include_template('add.php', [
      'cats' => $cats,
      'lot' => $lot,
      'errors' => $errors
    ]);
    $layoutContent = include_template('layout.php', [
      'isAuth' => $isAuth,
      'content' => $addContent,
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
