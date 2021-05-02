<?php
require_once('helpers.php');
require_once('functions.php');

$is_auth = rand(0, 1);
$title = 'YetiCave';
$user_name = 'Alex'; // укажите здесь ваше имя

$cats = [
    "Доски и лыжи", 
    "Крепления", 
    "Ботинки", 
    "Одежда", 
    "Инструменты", 
    "Разное"
];
$lots = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'cat' => 'Доски и лыжи',
        'price' => '1099',
        'url' => 'img/lot-1.jpg',
        'expired' => '2021-05-02',
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'cat' => 'Доски и лыжи',
        'price' => '159999',
        'url' => 'img/lot-2.jpg',
        'expired' => '2021-05-09',
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'cat' => 'Крепления',
        'price' => '8000',
        'url' => 'img/lot-3.jpg',
        'expired' => '2021-05-08',
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'cat' => 'Ботинки',
        'price' => '10999',
        'url' => 'img/lot-4.jpg',
        'expired' => '2021-05-07',
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'cat' => 'Одежда',
        'price' => '7500',
        'url' => 'img/lot-5.jpg',
        'expired' => '2021-05-06',
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'cat' => 'Разное',
        'price' => '5400',
        'url' => 'img/lot-6.jpg',
        'expired' => '2021-05-04',
    ]
];

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

