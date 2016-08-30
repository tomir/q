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
class Admin_Otomoto {

	public function getData($id) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."auction WHERE id = ".$id;
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function checkAuction($otomoto_id) {
		
		$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."auction WHERE otomoto_id = ".$otomoto_id;
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result['id'];
	}
	
	public function getAuctionList($filtr) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."auction WHERE otomoto_id = ".$otomoto_id;
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
		
	}
	
	public function saveAuction($data) {
		
		ConnectDB::subAutoExec( "autosalon_otomoto_log", $data, "INSERT" );
	}
        
        public function getAuctionAdds() {
            
            $aResult = array();
            $aResult['features'] = $this->getFeaturesList();
            $aResult['extras'] = $this->getExtrasList();
            $aResult['country'] = $this->getCountryList();
            $aResult['gearbox'] = $this->getGearboxList();
            $aResult['colour'] = $this->getColourList();
            $aResult['fuel'] = $this->getFuelTypeList();
            $aResult['body'] = $this->getBodyList();
            
            return $aResult;
        }
	
	public function getFeaturesList() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_equipment_list WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getExtrasList() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_info_list WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getCountryList() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_countries WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getGearboxList() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_gearbox WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getColourList() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_colour WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getFuelTypeList() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_fuel WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getBodyList() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_body_list WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function findModelId($model) {
		
		$sql = "SELECT model_id FROM ".MyConfig::getValue("dbPrefix")."car_model WHERE model_name = '".$model."%' ";
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result['model_id'];
	}
	
	public function findMarkalId($marka) {
		
		$sql = "SELECT producer_id FROM ".MyConfig::getValue("dbPrefix")."car_producer WHERE producer_name = '".$marka."' ";
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result['producer_id'];
	}

}
?>
