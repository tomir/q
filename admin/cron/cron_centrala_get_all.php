<?php

include_once realpath(dirname(__FILE__) . '/../../') . '/config/config.php';

ini_set('max_execution_time', 9000);

$xml = file_get_contents('http://www.centralazabawek.pl/xml/XMLdownload.php?login=biuro@subvision.pl&pass=5739658814');
//$aProducts =  new SimpleXMLElement($xml);
$aProducts = simplexml_load_string($xml, null, LIBXML_NOCDATA);

foreach ($aProducts->product as $product) {
    
    switch(intval($product->vat)) {
		case "A": $vat_id = 7; $vat = 1.23; break;
        default : $vat_id = 0;
    }
    
    $array = array(
        "hurtownia_id" => 3,
        "import_others_id" => (int)$product->id,
        "import_ean" => (string)$product->ean,
        "import_producer" => (string)$product->manufacturer,
        "import_category" =>(string) $product->category,
        "import_weight" => (string)$product->weight,
        "import_guarantee" => (int)$product->guarantee,
        "import_name" => (string)$product->name,
        "import_desc" => (string)$product->description,
        "import_price_suggest" => (float)($product->price/$vat),
		"import_price_suggest_gross" => (float)$product->price*1.08,
		"import_inventory" => $product->stock,
        "import_price_buy" => (float)$product->price,
        "vat_id" => $vat_id
    );
  
    $sql = "SELECT import_id FROM shop_hurtownie_import WHERE import_others_id = ".$product->id." AND hurtownia_id = 3";
    
    $import_id = ConnectDB::subQuery($sql,'one');
    
    if($import_id > 0) {
        $result = ConnectDB::subAutoExec('shop_hurtownie_import',$array,'UPDATE', "import_others_id = '".$product->id."' AND hurtownia_id = 3");
    } else {
		echo 'insert';
        $import_id = ConnectDB::subAutoExec('shop_hurtownie_import',$array,'INSERT');
    }
    foreach($product->images->image as $img) {
        ConnectDB::subExec("INSERT IGNORE INTO shop_hurtownie_import_img (import_id, img_url) 
            VALUES(".$import_id.",'".$img."')");
    }
}

