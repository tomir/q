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
class Admin_Car {

	static public function checkCar($id) {
		
		$sql = "SELECT car_id FROM ".MyConfig::getValue("dbPrefix")."car WHERE car_zew_id = ".$id;
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		if(isset($result['id']) && $result['id'] > 0) {
			return true;
		} else {
			return false;
		}

	}
}
?>
