<?php

require('/home/administrator/www/zambi/www/config/config.php');
error_reporting(E_ERROR);

echo '<pre>';

$sql = "SELECT p_magazine, p_id, p_on_stock
				FROM shop_product
				WHERE 1 ";

$all = ConnectDB::subQuery($sql);
foreach($all as $row) {
	if($row['p_magazine'] > 0) {
		$sql = "UPDATE shop_product SET p_on_stock = 1 WHERE p_id = ".$row['p_id'] ;
	} elseif($row['p_on_stock'] != 0) {
		$sql = "UPDATE shop_product SET p_on_stock = 0, p_on_stock_date = now() WHERE p_id = ".$row['p_id'] ;
	}
	
	ConnectDB::subExec($sql);
}
