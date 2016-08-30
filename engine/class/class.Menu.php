<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author tomi
 */
class Menu {

	static public function getMenu($id_kat) {
		
		$sql = "SELECT c.category_url_name, c_lang.* FROM " . MyConfig::getValue("__formind_category") . " c
			   LEFT JOIN " . MyConfig::getValue("__formind_category_lang") . " c_lang ON c.cat_id = c_lang.cat_id
			   WHERE c.category_parent = ".(int)$id_kat." AND c.cat_publish = 1
			   ORDER BY c.cat_order ASC";
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
		
	}
	
	static public function mapCategory($url_name) {
		
		$sql = "SELECT cat_id FROM " . MyConfig::getValue("__formind_category") . " WHERE category_url_name = '".(string)$url_name."' ";
		
		try {
			$result = 0;
			$result = ConnectDB::subQuery($sql, 'one');
		}
		catch(PDOException $e) {
			
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $result;
		
	}
	
}
?>
