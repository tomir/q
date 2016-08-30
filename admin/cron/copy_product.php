<?php

require('/home/administrator/www/zambi/www/config/config.php');

$sql = "SELECT * FROM shop_hurtownie_import WHERE product_id = 0";
$all = ConnectDB::subQuery($sql);

$objImport = new Admin_Import();
$objProduct = new Admin_Product();
$objProducer = new Admin_Producents();
$objCategory = new Admin_Category();
$objSupplierCategory = new \Admin\Supplier\Category();
$objSupplierCategoryMapping = new \Admin\Supplier\Mapping();

foreach($all as $row) {
	
	if($row['producer_id'] == 0) {
		$objProducer->setProducentName($row['import_producer']);
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
		
		$objSupplierCategory->setName($row['import_category']);
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
	
	$productId = $objProduct->save(array(
		'p_name' => $row['import_name'],
		'producent_id' => $row['producer_id'],
		'p_description' => ($row['import_desc'] == null ? '' : $row['import_desc']),
		'p_price_buy' => $row['import_price_buy'],
		'p_price' => $row['import_price_suggest'],
		'vat_id' => $row['vat_id'],
		'p_price_gross' => $row['import_price_suggest_gross'],
		'p_magazine' => $row['import_inventory'],
		'p_active' => 0,
		'p_age_id' => ($row['import_age_id'] == null ? 0 : $row['import_age_id'])
	)); 
	
	//category
	$aCat = $objSupplierCategoryMapping->getAll(array(
		'category_import_id' => $row['category_id']
	));
	
	if(is_array($aCat) && count($aCat) > 0) {
		$objCategory->setCatId($aCat[0]['category_id']);
		$objCategory->selectAjaxCategory(true, $productId);
	}
	
	//photos
	$aImport = $objImport->importGet($row['import_id']);

	if(is_array($aImport['img']) && count($aImport['img']) > 0) {

		$i = 0;
		foreach($aImport['img'] as $img) {

			if(strrpos($img['img_url'], 'jpg') > 0) {
				$fData = array(
					'm_jpg' => 1,
					'p_id' => $productId,
					'url' => $img['img_url']
				);

				if($i == 0) {
					$fData['m_main'] = 1;
				}
				$i++;
				$objProduct->saveFileUrl($fData);
			}
		}
	}
	
	 ConnectDB::subExec('UPDATE shop_hurtownie_import SET product_id = '.$productId.' WHERE import_id = '.$row['import_id']); 
	 
}
