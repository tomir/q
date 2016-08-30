<?php

if ($cookerId > 0) {
	$smarty->assign('enum_cooker', \Przepis\Enum\Cooker::$cookerAlias[$cookerId]);
	$smarty->assign('cookerId', $cookerId);

	$filtr['x.user_id'] = $cookerId;
}

if ($_GET['s'] == 0)
	$page = 1;
else
	$page = $_GET['s'];
if ($_COOKIE['konkurs_limit'] != 0)
	$limit = $_COOKIE['konkurs_limit'];
else
	$limit = 20;

$productsStart = $page * $limit - $limit;

$objPrzepis = new \Przepis\Repository\Przepis();

$filtr['x.active'] = 1;
$list = $objPrzepis->getAll($filtr, null, array(
	'limit' => $limit,
	'start' => $productsStart
));

$countProducts = $objPrzepis->getAllIlosc($filtr);
$smarty->assign('przepisy', $list);

$product_pages = ceil($countProducts/$limit);

$smarty->assign('productsStart', $productsStart);
$smarty->assign('product_pages', $product_pages);
$smarty->assign('page', $page);