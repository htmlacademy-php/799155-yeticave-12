<?php
require_once('helpers.php');
require_once('Repository.php');
require_once('Form.php');
require_once('functions.php');
require_once('session.php');

$layoutContent = null;
$navContent = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$whatToSerach = trim($repo->getEscapeStr($_GET['search']));
	if ($repo->isOk() and !empty($whatToSerach)) {
		//заполним список лотов c помощью полнотекстового поиска
		$lots = $repo->findSimilarLots('name', 'descr', $whatToSerach);
		//начальные значения в session.php
		$count = count($lots);
		$pagesCount = ceil($count / $lotsPerPage); 
		$offset = ($curPage - 1) * $lotsPerPage;
		$pages = range(1, $pagesCount);
	
		//максимальные ставки для каждого лота
		foreach ($lots as $lot) {
			$bets[$lot['id']]['price'] = $repo->getMaxBet($lot['id'])['price'];
		}
	} 
	//заполним список категорий из репозитория
	if ($repo->isOk()) {
		$cats = $repo->getAllCategories();
		$navContent = include_template('nav.php', [
		'cats' => $cats
		]);
	} 
	if ($repo->isOk()) {
		$lotsContent = include_template('search.php', [
			'search' => $whatToSerach,
      'cats' => $cats,
      'lots' => $lots,
      'bets' => $bets,
			'nav' => $navContent
		]);
		$layoutContent = include_template('layout.php', [
			'search' => $whatToSerach,
			'nav' => $navContent,
			'is_auth' => $isAuth,
			'content' => $lotsContent,
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
}

print($layoutContent);
