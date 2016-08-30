<?php

require('/home/administrator/www/zambi/www/config/config.php');

$sql = "SELECT * FROM shop_hurtownie_import WHERE category_id = 0";
$all = ConnectDB::subQuery($sql);

$objImport = new Admin_Import();
$objProduct = new Admin_Product();
$objProducer = new Admin_Producents();
$objCategory = new Admin_Category();
$objSupplierCategory = new \Admin\Supplier\Category();
$objSupplierCategoryMapping = new \Admin\Supplier\Mapping();

foreach($all as $row) {
	
	if($row['producer_id'] == 0) {
		$objProducer->setProducentName(addslashes($row['import_producer']));
		$id = $objProducer->search();
		if($id > 0) {
			$row['producer_id'] = $id;
		} else {
			$row['producer_id'] = $objProducer->save(array(
				'name' => $row['import_producer']
			));
		}
		
		$sql = "UPDATE shop_hurtownie_import SET producer_id = ".$row['producer_id']." WHERE import_id = ".$row['import_id'];
		ConnectDB::subExec($sql);
	}
	
	if($row['category_id'] == 0) {
		
		$objSupplierCategory->setName(addslashes($row['import_category']));
		$category_id = $objSupplierCategory->search();
		if($category_id > 0) {
			$row['category_id'] = $category_id;
		} else {
			
			$row['category_id'] = $objSupplierCategory->save(array(
				'name' => $row['import_category']
			));
		} 
		
		$sql = "UPDATE shop_hurtownie_import SET category_id = ".$row['category_id']." WHERE import_id = ".$row['import_id'];
		ConnectDB::subExec($sql);
	}
}

$sql = "SELECT sp.p_id, sh.category_id FROM shop_product sp "
		. "LEFT JOIN shop_hurtownie_import sh ON sh.product_id = sp.p_id "
		. "LEFT JOIN shop_categories_products sc ON sc.p_id = sp.p_id "
		. "WHERE sc.cp_id IS NULL";
$all2 = ConnectDB::subQuery($sql);
		

foreach($all2 as $row2) {
	
	$aCat = $objSupplierCategoryMapping->getAll(array(
		'category_import_id' => $row2['category_id']
	));

	if(is_array($aCat) && count($aCat) > 0 && !empty($aCat)) {
		
		$objCategory = new Admin_Category();
		$objCategory->setCatId($aCat[0]['category_id']);
		$res = $objCategory->selectAjaxCategory(true, $row2['p_id']);

		$objCategory->setCatId = 0;
		unset($objCategory);
		
		if($res > 0) {
			$sql = "UPDATE shop_product SET p_active = 1 WHERE p_id = ".$row2['p_id'];
			ConnectDB::subExec($sql);
		}
	}
}
