<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('Form.php');
require_once('functions.php');
require_once('session.php');

//установим связь с репозиторием базы yeticave
$repo = new Repository();

$layoutContent = null;
$navContent = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$catId = $_GET['cat'];
  $cat = $repo->getCat($catId);
	//заполним список лотов из репозитория
	if ($repo->isOk()) {
    $lots = $repo->getLots($lotsPerPage, $offset, $catId);
    $count = count($lots);
    $pagesCount = ceil($count / $lotsPerPage); 
    $offset = ($curPage - 1) * $lotsPerPage;
    $pages = range(1, $pagesCount);
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
    $lotsContent = include_template('all-lots.php', [
        'cat' => $cat,
        'cats' => $cats,
        'lots' => $lots,
        'bets' => $bets
    ]);
  } else {
    $lotsContent = include_template('error.php', [
    'error' => $repo->getError()
    ]);    
  }

  $layoutContent = include_template('layout.php', [
    'nav' => $navContent,
    'is_auth' => $isAuth,
    'content' => $lotsContent,
    'cats' => $cats,
    'title' => $title,
    'user_name' => $userName,
    'url' => $url,
    'pagesCount' => $pagesCount,
    'curPage' => $curPage,
    'pages' => $pages
  ]);
}

print($layoutContent);
