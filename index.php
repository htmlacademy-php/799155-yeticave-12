<?php
require_once('helpers.php');
require_once('functions.php');

$is_auth = rand(0, 1);
$title = 'YetiCave';
$user_name = 'Alex'; // укажите здесь ваше имя
$cats = array();
$lots = array();
$sql = "";

//установим связь с БД
$base = new Database("yeticave");

//заполним список категорий из БД
if ($base->is_ok()) {
    $sql = "SELECT name, code FROM cats";
    $result = $base->query($sql);
    if ($base->is_ok()) {
        $cats = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } 
}

$sql = "INSERT INTO lots SET dt_add = NOW(), name = 'Зимний Шлем Alpina 2020-21 Chute Night Blue Matt'," . 
" descr = 'Универсальный шлем для катания на горных лыжах или сноуборде, созданный на базе легкой и прочной конструкции inmold'," .
" img_url = 'img/lot-7.jpg', dt_expired = DATE_ADD(NOW(), INTERVAL 11 DAY), price = 4299, bet_step = '100', cat_id = '6', author_id = '2'";

//заполним список лотов из БД
if ($base->is_ok()) {
    //для добавления нового лота в базу раскомментировать следующую строку
    //$base->query($sql);
    if ($base->is_ok()) {
        $sql = "SELECT l.name, descr, price, img_url, dt_add, dt_expired, c.name as cat_name, cat_id" . 
        " FROM lots l JOIN cats c ON c.id = l.cat_id WHERE dt_expired > NOW() ORDER BY dt_add DESC";
        $result = $base->query($sql);
        if ($base->is_ok()) {
            $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } 
    }
}

$main_content = include_template('main.php', [
    'cats' => $cats,
    'lots' => $lots
]);

$layout_content = include_template('layout.php', [
    'content' => $main_content,
    'cats' => $cats,
    'title' => $title,
    'user_name' => $user_name
]);

print($layout_content);

