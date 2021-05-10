<?php
require_once('helpers.php');
require_once('functions.php');
require_once('Repository.php');

$is_auth = rand(0, 1);
$title = 'YetiCave';
$user_name = 'Alex'; // укажите здесь ваше имя
$cats = array();
$lots = array();
$sql = "";
$error = null;
$main_content = null;

//установим связь с репозиторием базы yeticave
$repo = new Repository();

//заполним список категорий из репозитория
if ($repo->isOk()) {
    $cats = $repo->getAllCategories();
} else {
    $error = $repo->getError();
}
//заполним список лотов из репозитория
if ($repo->isOk()) {
    //для тестирования результатов добавления нового лота в базу раскомментировать следующие строки
    /*
    $sql = "INSERT INTO lots SET dt_add = NOW(), name = 'Зимний Шлем Alpina 2020-21 Chute Night Blue Matt'," . 
    " descr = 'Универсальный шлем для катания на горных лыжах или сноуборде, созданный на базе легкой и прочной конструкции inmold'," .
    " img_url = 'img/lot-7.jpg', dt_expired = DATE_ADD(NOW(), INTERVAL 11 DAY), price = 4299, bet_step = '100', cat_id = '6', author_id = '2'";
    $repo->addNewLot($sql);
    */
    $lots = $repo->getAllLots();
} else {
    $error = $repo->getError();
}

if ($error == null) {
    $main_content = include_template('main.php', [
        'cats' => $cats,
        'lots' => $lots
    ]);
} else {
    $main_content = include_template('error.php', [
		'error' => $error
    ]);    
}

$layout_content = include_template('layout.php', [
    'content' => $main_content,
    'cats' => $cats,
    'title' => $title,
    'user_name' => $user_name
]);

print($layout_content);
