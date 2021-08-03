<?php
require_once 'helpers.php';
require_once 'Form.php';
require_once 'functions.php';
require_once 'session.php';

$layoutContent = null;
$bets = array();

$cats = $repo->getAllCategories();
$navContent = includeTemplate('nav.php', [
    'cats' => $cats
]);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($authorId and $isAuth) {
        $bets = $repo->getUserBets($authorId);
        foreach($bets as $bet) {
            $lot = $repo->getLot($bet['lot_id'], true);
            $bet['dt_expired'] = $lot['dt_expired'];
        }
        $count = count($bets);
        if ($count > 0) {
            $pagesCount = ceil($count / $betsPerPage);
            $offset = ($curPage - 1) * $betsPerPage;
            $pages = range(1, $pagesCount);
            $bets = array_slice($bets, $offset, $betsPerPage);
        }
    }
}

if ($repo->isOk()) {
    $betsContent = includeTemplate('my-bets.php', [
        'bets' => $bets,
        'nav' => $navContent,
        'user_id' => $authorId,
    ]);
    $layoutContent = includeTemplate('layout.php', [
        'nav' => $navContent,
        'is_auth' => $isAuth,
        'content' => $betsContent,
        'cats' => $cats,
        'title' => "Мои ставки",
        'user_name' => $userName,
        'user_id' => $authorId,
        'search' => '',
        'url' => $url,
        'pagesCount' => $pagesCount,
        'curPage' => $curPage,
        'pages' => $pages
    ]);
} else {
    $layoutContent = includeTemplate('error.php', [
        'error' => $repo->getError()
    ]);
}

print($layoutContent);
