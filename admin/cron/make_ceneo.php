<?php

include_once realpath(dirname(__FILE__) . '/../../') . '/config/config.php';

# Produkty
$objCategory	= new Category();
$objProduct		= new Product();
$aProductList = $objProduct->getProductList(array('active' => 1));
$urls = array();
foreach($aProductList as &$i)
{
	$i['url'] = 'p-'.Misc::utworzSlug($i['p_name']) . ',' . $i['p_id'];
	foreach($objCategory->getPath($i['cat_id']) as $row) {
		$cats[] = $row['cat_name'];
	}
	$i['cats'] = implode('/', $cats);
	$cats = null;
	
}


ob_start();

include(MyConfig::getValue("serverPatch")."admin/cron/ceneo_xml.php");

$xml = ob_get_contents();
ob_end_clean();

file_put_contents( APPLICATION_DIR . 'ceneo_xml.xml', $xml );
