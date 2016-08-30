<?php
$objProduct		= new Product();

$aProductList = $objProduct->getProductList(array('active' => 1 ), 1);
$countProducts = $objProduct->getProductListCount(array('active' => 1));

$smarty->assign('produkty', $aProductList);