<?php
require_once('helpers.php');
require_once('functions.php');
require_once('Repository.php');
require_once('session.php');

$cats = array();
$lots = array();
$bets = array();

$mainContent = null;
$navContent = null;

//установим связь с репозиторием базы yeticave
$repo = new Repository();

if ($repo->isOk()) {
	$count = $repo->getLotsCount();
	$pagesCount = ceil($count / $lotsPerPage); 
	$offset = ($curPage - 1) * $lotsPerPage;
	$pages = range(1, $pagesCount);
}

//заполним список лотов из репозитория
if ($repo->isOk()) {
    $lots = $repo->getLots($lotsPerPage, $offset);
    //максимальные ставки для каждого лота
    foreach ($lots as $lot) {
        $bet['id'] = $lot['id'];
        $bet['price'] = $repo->getMaxBet($lot['id']);
        $bets[] = $bet;
    }
}

//заполним список категорий из репозитория
if ($repo->isOk()) {
    $cats = $repo->getAllCategories();
}

if ($repo->isOk()) {
    $mainContent = include_template('main.php', [
        'cats' => $cats,
        'lots' => $lots,
        'bets' => $bets
    ]);
} else {
    $mainContent = include_template('error.php', [
		'error' => $repo->getError()
    ]);    
}

$layoutContent = include_template('layout.php', [
    'nav' => $navContent,
    'is_auth' => $isAuth,
    'content' => $mainContent,
    'cats' => $cats,
    'title' => $title,
    'user_name' => $userName,
    'url' => $url,
    'pagesCount' => $pagesCount,
    'curPage' => $curPage,
    'pages' => $pages
]);

print($layoutContent);
