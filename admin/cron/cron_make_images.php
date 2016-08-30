<?php

require('/home/administrator/www/zambi/www/config/config.php');
error_reporting(E_ERROR);

echo '<pre>';

$sql = "SELECT * FROM shop_hurtownie_import WHERE product_id > 0 and hurtownia_id != 2";
$import = ConnectDB::subQuery($sql);

$objImport = new Admin_Import();
$objProduct = new Admin_Product();

foreach($import as $row) {
		$aImport = $objImport->importGet($row['import_id']);
		
		$sql = "DELETE FROM shop_product_media WHERE p_id = ".$row['product_id'];
		ConnectDB::subExec($sql);
			
		if(is_array($aImport['img']) && count($aImport['img']) > 0) {

			$i = 0;
			foreach($aImport['img'] as $img) {
				
				if(strrpos($img['img_url'], 'jpg') > 0) {
					$fData = array(
						'm_jpg' => 1,
						'p_id' => $row['product_id'],
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
	}
