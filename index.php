<?php
require_once('helpers.php');
require_once('functions.php');
require_once('Repository.php');
require_once('session.php');

$cats = array();
$lots = array();
$bets = array();
$sql = "";
$error = null;
$mainContent = null;

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
    $lots = $repo->getAllLots();
    //максимальные ставки для каждого лота
    foreach ($lots as $lot) {
        $bet['id'] = $lot['id'];
        $bet['price'] = $repo->getMaxBet($lot['id']);
        $bets[] = $bet;
    }
} else {
    $error = $repo->getError();
}

if ($error == null) {
    $mainContent = include_template('main.php', [
        'cats' => $cats,
        'lots' => $lots,
        'bets' => $bets
    ]);
} else {
    $mainContent = include_template('error.php', [
		'error' => $error
    ]);    
}

$layoutContent = include_template('layout.php', [
    'is_auth' => $isAuth,
    'content' => $mainContent,
    'cats' => $cats,
    'title' => $title,
    'user_name' => $userName
]);

print($layoutContent);
