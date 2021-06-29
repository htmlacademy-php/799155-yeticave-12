<?php
require_once('helpers.php');
require_once('functions.php');
require_once('Repository.php');
require_once('session.php');

$cats = array();
$lots = array();
$bets = array();

$layoutContent = null;
$navContent = null;
$bets = array();

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
        $bets[$lot['id']]['price'] = $repo->getMaxBet($lot['id'])['price'];
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
    $layoutContent = include_template('layout.php', [
        'nav' => $navContent,
        'is_auth' => $isAuth,
        'content' => $mainContent,
        'cats' => $cats,
        'title' => $title,
        'user_name' => $userName,
        'user_id' => $authorId,
        'url' => $url,
        'pagesCount' => $pagesCount,
        'curPage' => $curPage,
        'pages' => $pages
    ]);
}
 
if (!$repo->isOk()) {
    $layoutContent = include_template('error.php', [
		'error' => $repo->getError()
    ]);    
}


print($layoutContent);
