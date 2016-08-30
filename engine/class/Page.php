<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Tomasz
 */
class Page {
    public function get($name) {
        $sql = 'SELECT * FROM shop_page WHERE page_shortname = \''.$name.'\'';
        
        try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'fetch');
			return $aResult;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
    }
}
?>
