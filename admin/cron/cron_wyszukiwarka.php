<?php
/**
 * Aktualizacja fraz wyszukiwania
 */

require('/home/administrator/www/zambi/www/config/config.php');
error_reporting(E_ERROR);
ini_set('display_errors', true);
ini_set('memory_limit', '2000M');

echo '<pre>';

$sql = "SELECT 	
			p.*,
			pr.producent_name as producent,
			p.p_id as id,
			pcp.cat_id as kategoria
		FROM shop_product p
		JOIN shop_categories_products pcp ON p.p_id = pcp.p_id
		LEFT JOIN shop_producents pr on pr.producent_id = p.producent_id
		WHERE 1 
		GROUP BY p.p_id";

try {
	$produkty = array(); 
	$produkty = ConnectDB::subQuery($sql); 
	
}
catch (Exception $e) {
	echo "ok2";
	echo $e->getMessage();
	return null;
}

//$rodzajKupic = KupicMapowanie::getListSelect();

echo 'ilosc : '.count($produkty).'<br />';

foreach ($produkty as $one) {
	
	$objCategory	= new Category();

	$breadcrumbs = $objCategory->getPath($one['kategoria']);
	
	foreach($breadcrumbs as $row) {
		$text .= $row['cat_name']." ";
	}
	
	$text .= $one['producent'];
	
	$sql = "UPDATE shop_product SET p_sphinx = '".trim(addslashes($text))."' WHERE p_id = '".$one['id']."'";
	ConnectDB::subExec($sql);
	
	$breadcrumbs = null;
	$text = null;
}

echo 'zrobione : '.count($produkty);
