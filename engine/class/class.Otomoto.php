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
class Otomoto {

	public function getData($id) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."auction WHERE id = ".$id;
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
	}
	
	public function checkAuction($otomoto_id) {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."auction WHERE otomoto_id = ".$otomoto_id;
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result['id'];
	}
	
	public function getAuctionList($filtr) {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."auction WHERE otomoto_id = ".$otomoto_id;
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
		
	}
	
	public function saveAuction($data) {
		
		ConnectDB::subAutoExec( "autosalon_otomoto_log", $data, "INSERT" );
	}
	
	public function getFeaturesList() {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."car_equipment_list WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
	}
	
	public function getExtrasList() {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."car_info_list WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
	}
	
	public function getCountryList() {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."car_countries WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
	}
	
	public function getGearboxList() {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."car_gearbox WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
	}
	
	public function getColourList() {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."car_colour WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
	}
	
	public function getFuelTypeList() {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."car_fuel WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
	}
	
	public function getBodyList() {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."car_body_list WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
		}
		
		return $result;
	}
}
?>
