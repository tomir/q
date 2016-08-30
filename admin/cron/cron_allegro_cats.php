<?php
/**
 * Import kategorii z allegro
 */
require('../../config/config.php');
ini_set('max_execution_time', 9000);


echo '<pre>';
$allegroApi = new AllegroApi(1, 1);
$res = $allegroApi->doGetCatsData();

$kategorie = array();
foreach ($res['cats-list'] as $r) {
	$v = get_object_vars($r);
	$kategorie[$v['cat-id']] = array('id' => $v['cat-id'], 'nazwa' => $v['cat-name'], 'parent_id' => $v['cat-parent']);
}

$sql = "TRUNCATE TABLE " . MyConfig::getValue("__allegro_kategorie");
ConnectDB::subExec($sql);

foreach($kategorie as $k) {
	$sql = "INSERT INTO " . MyConfig::getValue("__allegro_kategorie") ."(id,nazwa,parent_id) VALUES ('".$k['id']."','".addslashes($k['nazwa'])."','".$k['parent_id']."')";
	ConnectDB::subExec($sql);
}



?>