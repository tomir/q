<?php
require('/home/administrator/www/zambi/www/config/config.php');

ini_set('max_execution_time', 9000);

$xml = file_get_contents('http://mariolafruwa.pl/edi/export-offer.php?client=biuro%40subvision.pl&language=pol&token=c7273defad8277d6749afb9&shop=1&type=full&format=xml&iof_2_4');
//$xml = file_get_contents('http://sklep.local.pl/fruwa.xml');
//$aProducts =  new SimpleXMLElement($xml);
$aProducts = simplexml_load_string($xml);

foreach ($aProducts->products->product as $product) {
    //print_r($product);
    //echo (string) $product->description->name[0];
    
    //echo '<hr>';
    
    switch(intval($product->price["vat"])) {
        case 23: $vat_id = 7; break;
        case 5: $vat_id = 8; break;
        case 8: $vat_id = 9; break;
        default : $vat_id = 0;
    }
    
    $array = array(
        "hurtownia_id" => 1,
        "import_others_id" => (int)$product['id'],
        "import_ean" => (string)$product['code_producer'],
        "import_producer" => (string)$product->producer['name'],
        "import_category" =>(string) $product->category['name'],
        "import_age" => (string)$product->series['name'],
        "import_age_id" => (int)$product->series['id'],
        "import_name" => (string)$product->description->name[0],
        "import_desc" => (string)$product->description->long_desc[0],
        "import_price_suggest" => (float)$product->price["gross"],
        "import_price_buy" => $product->price["net"] - $product->price["net"]*0.08,
        "vat_id" => $vat_id
    );
    
    if(strlen((string)$product->description->name[1]) > 10) {
        $array["import_name"] = (string)$product->description->name[1];
    }
    
    if(strlen((string)$product->description->long_desc[1]) > 10) {
        $array["import_desc"] = (string)$product->description->long_desc[1];
    }
    
    $sql = "SELECT import_id, product_id FROM shop_hurtownie_import WHERE import_others_id = ".$product['id']." AND hurtownia_id = 1 ORDER BY import_id ASC";
    $importRows = ConnectDB::subQuery($sql);
	
	$kk =0;
	if(is_array($importRows) && count($importRows) > 0) {
		foreach($importRows as $importRow) {

			if($kk == 0) {
				$result = ConnectDB::subAutoExec('shop_hurtownie_import',$array,'UPDATE', "import_id = '".$importRow['import_id']."'");
				$import_id = $importRow['import_id'];

			}  else {
				ConnectDB::subExec('DELETE FROM shop_hurtownie_import WHERE import_id = '.$importRow['import_id']);
				ConnectDB::subExec('DELETE FROM shop_product WHERE p_id = '.$importRow['product_id']);
				ConnectDB::subExec('DELETE FROM shop_categories_products WHERE p_id = '.$importRow['product_id']);
				ConnectDB::subExec('DELETE FROM shop_product_media WHERE p_id = '.$importRow['product_id']);
				ConnectDB::subExec('DELETE FROM shop_hurtownie_import_img WHERE import_id = '.$importRow['import_id']);
			}
			$kk++;
		}
	} else {
		$import_id = ConnectDB::subAutoExec('shop_hurtownie_import',$array,'INSERT');
	}

    foreach($product->images->large->image as $img) {
        ConnectDB::subExec("INSERT IGNORE INTO shop_hurtownie_import_img (import_id, img_url) 
            VALUES(".$import_id.",'".$img['url']."')");
    }
	

}

