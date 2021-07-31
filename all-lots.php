<?php
require_once 'helpers.php';
require_once 'Form.php';
require_once 'functions.php';
require_once 'session.php';

$layoutContent = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $bets = array();
    if ($repo->isOk()) {
        $catId = $repo->getEscapeStr($_GET['cat']);
        $cat = $repo->getCat($catId);
        //заполним список категорий из репозитория
        $cats = $repo->getAllCategories();
    }
    //заполним список лотов из репозитория
    if ($repo->isOk()) {
        $lots = $repo->getLots($lotsPerPage, $offset, $catId);
        $count = count($lots);
        $pagesCount = ceil($count / $lotsPerPage);
        $offset = ($curPage - 1) * $lotsPerPage;
        $pages = range(1, $pagesCount);
        //максимальные ставки для каждого лота
        foreach ($lots as $lot) {
            $bets[$lot['id']]['price'] = $repo->getMaxBet($lot['id'])['price'];
        }
    }

    if ($repo->isOk()) {
        $navContent = includeTemplate('nav.php', [
            'cats' => $cats
        ]);
        $lotsContent = includeTemplate('all-lots.php', [
            'nav' => $navContent,
            'cat' => $cat,
            'cats' => $cats,
            'lots' => $lots,
            'bets' => $bets
        ]);
        $layoutContent = includeTemplate('layout.php', [
            'nav' => $navContent,
            'is_auth' => $isAuth,
            'content' => $lotsContent,
            'search' => '',
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
}

if (!$repo->isOk()) {
    $layoutContent = includeTemplate('error.php', [
        'error' => $repo->getError()
    ]);
}

print($layoutContent);
