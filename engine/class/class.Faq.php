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
class Faq {

	public function getList() {
		
		$sql = "SELECT fs_lang.* FROM " . MyConfig::getValue("__formind_faq_section") . " fs
					    LEFT JOIN " . MyConfig::getValue("__formind_faq_section_lang") . " fs_lang ON fs.section_id= fs_lang.section_id AND fs_lang.lang_prefix = '" . LANG_PREFIX . "'
			   WHERE 1
			   ORDER BY fs.section_order ASC";
		
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
	
	static public function getOne($section_id) {
		
		$sql = "SELECT f_lang.* FROM " . MyConfig::getValue("__formind_faq") . " f
					    LEFT JOIN " . MyConfig::getValue("__formind_faq_lang") . " f_lang ON f.faq_id= f_lang.faq_id AND f_lang.lang_prefix = '" . LANG_PREFIX . "'
			   WHERE f.faq_publish =1 AND f.faq_section = " . $section_id . "
			   ORDER BY f.faq_order ASC";
		
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
	
}
?>
