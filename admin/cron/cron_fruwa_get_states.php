<?php
require('/home/administrator/www/zambi/www/config/config.php');

ini_set('max_execution_time', 9000);

$sql = "INSERT shop_import_log (log_product) VALUES ('Fruwa states RUN')";
ConnectDB::subExec($sql); 

$xml = file_get_contents('http://mariolafruwa.pl/edi/export-offer.php?client=biuro@subvision.pl&token=4a2e6c5f03c134175512366&shop=1&type=light&format=xml&iof_2_4');
//$xml = file_get_contents('http://sklep.local.pl/stock.xml');
$aProducts = simplexml_load_string($xml);

$aId = array();

foreach ($aProducts->products->product as $product) {
    //var_dump($product);
    $array = array(
        "import_price_buy" => $product->price["net"],
        "import_others_id" => (string)$product['id'],
        "import_price_suggest" => (string)$product->price["net"]*1.09,
        "import_inventory" => (string)$product->sizes->size->stock['quantity'],
        "import_price_suggest_gross" => $product->price["gross"]*1.09,
        "hurtownia_id" => 1
        );
    if($array["import_price_suggest_gross"]%1 == 0) {
        $array["import_price_suggest_gross"] = $array["import_price_suggest_gross"] - 0.01;
    }
    //var_dump($array); exit;
    //print_r($array); echo '<hr>';
    //echo "import_others_id = '".(string)$product['id']."' AND hurtownia_id = 1"; exit;
    $sql = "UPDATE shop_hurtownie_import SET import_price_buy = ".$array['import_price_buy'].",
        import_price_suggest = ".$array['import_price_suggest'].",
        import_inventory = ".$array['import_inventory'].",
        import_price_suggest_gross = ".$array['import_price_suggest_gross']."
        WHERE import_others_id = '".(string)$product['id']."' AND hurtownia_id = 1";
    ConnectDB::subExec($sql); 
    array_push($aId, (string)$product['id']);
}

foreach($aId as $item) {
    $id_string .= ', '.$item;
}

$id_string = substr($id_string, 1);

$sql = "UPDATE shop_hurtownie_import SET import_inventory = 0 WHERE import_others_id NOT IN (".$id_string.") AND hurtownia_id = 1";
ConnectDB::subExec($sql); 

?>
