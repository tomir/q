<?php

$objProduct		= new Product();
$objCategory	= new Category();
$objOpinion		= new \Opinie\Lista();
/*
if(isset($_POST['aData'])) {
	$res = $objOpinion->insert($_POST['aData']);
	if($res > 0)
		Common::redirect('?kom=1');
	else {
		Common::redirect('?kom=2');
	}
}
*/
$aProduct	= $objProduct -> getProduct((int)$_GET['id'], array('active' => 1));
if(count($aProduct) < 1 || !is_array($aProduct)) {
	header("Location:".$wwwPatch);
	exit();
}

/*
 * Podobne produkty
 */
$aPodobne = $objProduct->getProductList(array(
	'cat_id' => $aProduct['cat_id'], 
	'active' => 1,
	'product_not' =>  $aProduct['p_id']
),7,5,0);


$breadcrumbs	= $objCategory->getPath($aProduct['cat_id']);
$k				= $objCategory->getCategory($aProduct['cat_id']);

$head_title = $aProduct['producent_name'].' - '.$aProduct['p_name'].' -';

$aOpinions	= $objOpinion->getOpinionList(array(
	'p_id' => $_GET['id'],
	'status_id' => \Opinie\Lista::OPINION_ACCEPT
));
$countOpinions	= $objOpinion->getOpinionListCount(array(
	'p_id' => $_GET['id'],
	'status_id' => \Opinie\Lista::OPINION_ACCEPT
));

Ogladane::dodaj((int)$_GET['id']);