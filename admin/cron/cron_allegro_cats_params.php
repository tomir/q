<?php
/**
 * Import kategorii z allegro
 */
require('../../config/config.php');
error_reporting(E_ALL);
ini_set('memory_limit', '4000M');
ini_set('max_execution_time', 9000);

$sql = "TRUNCATE TABLE " . MyConfig::getValue("__allegro_kategorie_parametry");
ConnectDB::subExec($sql);
$allegroApi = new AllegroApi(1, 1);

$pola = array();
//for($i=0; $i<400;$i++) {
	
	
	$pola = $allegroApi->doGetSellFormFieldsExt();
		
	foreach($pola['sell-form-fields'] as $f)
	{
		$p = get_object_vars($f);
		$sql = "INSERT INTO " . MyConfig::getValue("__allegro_kategorie_parametry") . "
				(`id_kategorii`, `form_id`, `form_title`, `form_type`, `form_res_type`, `form_def_value`, `form_opt`, `form_pos`, `form_length`,
				`min_value`, `max_value`, `form_desc`, `form_opts_values`, `form_field_desc`)
				VALUES
				('".$p['sell-form-cat']."', '".$p['sell-form-id']."', '".$p['sell-form-title']."', '".$p['sell-form-type']."', '".$p['sell-form-res-type']."',
				'".$p['sell-form-def-value']."', '".$p['sell-form-opt']."', '".$p['sell-form-pos']."', '".$p['sell-form-length']."', '".$p['sell-min-value']."',
				'".$p['sell-max-value']."', '".addslashes($p['sell-form-desc'])."', '".$p['sell-form-opts-values']."', '".$p['sell-form-field-desc']."')";
		ConnectDB::subExec($sql);
	}
	$pola = null;
	$pola = array();
	sleep(1);
	
//}





?>