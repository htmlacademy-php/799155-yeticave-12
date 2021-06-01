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
  'category' => getPostVal('category', ''),
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
    $error = validateFilled('category', $lot, 'Выберите категорию');
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

  //проверим поле категории, которое может содержать плейсхолдер
  if ($lot['category'] === 'Выберите категорию') {
    //заменим его на пустую строку
    $lot['category'] = "";
  }
  
  //валидация полей формы
  Form::validateFields($lotRules, $lot);
  $errors = Form::getErrors();
  //отдельно валидация файла с изображением и перенос его в папку uploads
  if (empty($lot['lot-img'])) {
    if (Form::validateFile('lot-img', $errMessages['lot-img'])) {
      $lot['lot-img'] = Form::getFileName();
      $lot['new-img'] = Form::getNewFileName();
    } else {
      $errors['lot-img'] = Form::getMessage();
    }
  }

  if ($repo->isOk() and  count($errors) == 0) {
    //поищем дубль лота в БД
    $sql = "SELECT id, name FROM lots WHERE name LIKE " . "'" . $lot['lot-name'] . "'";
    $result = $repo->query($sql);
    if ($result) {
      $row = mysqli_fetch_assoc($result);
      if (isset($row)) {
        header("Location:/lot.php?id=" . $row['id']);
      }
    }

    $catId = $repo->getCatId($repo->getEscapeStr($lot['category']));
    //запишем данные лота в базу
    $sql = "INSERT INTO lots (dt_add, name, descr, img_url, price, dt_expired, bet_step, cat_id, author_id)" . 
    " VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
    $data = [$lot['lot-name'], $lot['message'], 'uploads/' . $lot['new-img'], $lot['lot-rate'], $lot['lot-date'],
            $lot['lot-step'], $catId, $authorId];
    $stmt = $repo->prepare($sql, $data);
    $result = mysqli_stmt_execute($stmt);
    //если все ок, переместимся на страницу лота
    if ($result) {
      $id = $repo->getLastId();
      header("Location:/lot.php?id=" . $id);
    } else {
      $error = $repo->getError();
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
