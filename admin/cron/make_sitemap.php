<?php

include_once realpath(dirname(__FILE__) . '/../') . '/config/config.php';

# Produkty
$objProduct		= new Product();
$aProductList = $objProduct->getProductList(array('active' => 1));
$urls = array();
foreach($aProductList as $i)
{
	$urls[] = 'p-'.Misc::utworzSlug($i['p_name']) . ',' . $i['p_id'];
}


# Kategorie
$objCategory	= new Category();
$aCategoriesProduct = $objCategory->getTree();

foreach($aCategoriesProduct as $i) {
	if($i['cat_ilosc'] > 0) {
		$urls[] = $i['cat_url_name'].','.$i['cat_id'];
	}
}

# Producenci
$objProducent	= new Producent();
$aProducentList = $objProducent->getProducentList();

foreach($aProducentList as $i) {
	if($i['producent_url'] != '') {
		$urls[] = $i['producent_url'];
	}
}

ob_start();

include(MyConfig::getValue("serverPatch")."admin/cron/google_sitemap.php");

$xml = ob_get_contents();
ob_end_clean();
echo APPLICATION_DIR . 'sitemap.xml';
file_put_contents( APPLICATION_DIR . 'sitemap.xml', $xml );
