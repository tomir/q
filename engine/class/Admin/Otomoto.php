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
			$result = ConnectDB::subQuery($sql, '', '', 'fetch');
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getAuctionPhotos($id, $select = true) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."auction_photos WHERE id_auction = ".$id." ORDER BY photo_id";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		if($select) {
			$new = array();
			foreach($result as $row) {
				$new[$row['photo_id']] = $row;
			}

			return $new;
		} else {
			return $result;
		}
	}
	
	public function getAuctionEquipment($id) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."auction_equipment WHERE id_auction = ".$id;
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		$new = array();
		foreach($result as $row) {
			$new[$row['equipment_id']] = $row;
		}
		
		return $new;
	}
	
	public function getAuctionInfo($id) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."auction_info WHERE id_auction = ".$id;
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		$new = array();
		foreach($result as $row) {
			$new[$row['info_id']] = $row;
		}
		
		return $new;
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
	
	public function getAuctionList($id_jest, $number, $start, $filtr) {
		
		
		
		$sql = "SELECT c.sale_date, au.*, at.date_to_send, au.otomoto_id as `otomoto-id`, cp.producer_name, cm.model_name FROM ".MyConfig::getValue("dbPrefix")."auction au
				LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON au.`make-id` = cp.otomoto_id AND LOWER(cp.type) = au.type
				LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_model cm ON au.`model-id` = cm.otomoto_id
				LEFT JOIN ".MyConfig::getValue("dbPrefix")."car c ON au.car_id = c.car_id
				LEFT JOIN ".MyConfig::getValue("dbPrefix")."auction_tasks at ON au.id = at.auction_id
				WHERE 1";
		
		$order = '';
		if($id_jest) {
			$sql .= " AND au.otomoto_id != '' ";
		} else {
			$order = "at.date_to_send ASC, ";
			$sql .= " AND au.otomoto_id = '' ";
		}
		
		if($filtr['data_do'] != '' && $filtr['data_do'] != 'now') {
			$sql .= ' AND c.sale_date <= "'.$filtr['data_do'].'" ';
		}
		
		if($filtr['data_do'] == 'now') {
			$sql .= ' AND c.sale_date <= now() ';
		}
		
		if(isset($filtr['idList']) && count($filtr['idList']) > 0) {
			$sql .= " AND au.otomoto_id NOT IN ('".  implode("','", $filtr['idList'])."') ";
		}
		
		$sql .= " ORDER BY ".$order." cp.producer_name LIMIT ".$start.", ".$number;
	
		try {
			$result = ConnectDB::subQuery($sql);
			
			foreach($result as $row) {
				$new[$row['id']] = $row;
				$new[$row['id']]['photos']	= $this->getAuctionPhotos($row['id'], false);
				$new[$row['id']]['errors']	= $this->getAuctionErrors($row['id']);
			}
			
		} catch (Exception $e) {
			echo $e->getTraceAsString();
		}
		//print_r($new);
		return $new;
		
	}
	
	public function auctionCount($id_jest) {
		
		if($id_jest) {
			$sql2 = " AND otomoto_id != '' ";
		} else {
			$sql2 = " AND otomoto_id = '' ";
		}
		
		$sql = "SELECT count(*) as ile FROM ".MyConfig::getValue("dbPrefix")."auction WHERE 1 ".$sql2;
		try {
			$result = ConnectDB::subQuery($sql);

		} catch (Exception $e) {
			echo $e->getTraceAsString();
		}
		
		return $result[0]['ile'];
	}
	
	public function saveAuction($data) {
		
		$id_auction = ConnectDB::subAutoExec( "autosalon_auction", $data, "INSERT");
		
		if(count($data['extras']) > 0 && is_array($data['extras'])) {
			foreach($data['extras'] as $row_f) {
				$id_key = $this -> getFeaturesList($row_f);
				ConnectDB::subAutoExec( "autosalon_auction_equipment",	array('equipment_id' => $id_key[0]['equipment_id'], 'id_auction' => $id_auction), "INSERT");
			}
		}
	
		if(count($data['features']) > 0 && is_array($data['features'])) {
			foreach($data['features'] as $row_e) {
				$id_key = $this -> getExtrasList($row_e);
				ConnectDB::subAutoExec( "autosalon_auction_info",	array('info_id' => $id_key[0]['info_id'], 'id_auction' => $id_auction), "INSERT");
			}
		}
		
		if(count($data['photos']) > 0 && is_array($data['photos'])) {
			foreach($data['photos'] as $row_p) {
				ConnectDB::subAutoExec( "autosalon_auction_photos",	array('photo_id' => $row_p, 'id_auction' => $id_auction), "INSERT");
			}
		}

		return $id_auction;
	}
	
	public function updateAuction($data, $clear = true) {
		
		ConnectDB::subAutoExec( "autosalon_auction", $data, "UPDATE", "id = ".$data['id']);
		
		if($clear) {
			$this->clearData('autosalon_auction_equipment', array('id_auction' => $data['id']));
			$this->clearData('autosalon_auction_info', array('id_auction' => $data['id']));
			$this->clearData('autosalon_auction_photos', array('id_auction' => $data['id']));

			try {
				if(count($data['extras']) > 0 && is_array($data['extras'])) {
					foreach($data['extras'] as $row_f) {
						$id_key = $this -> getFeaturesList($row_f);
						$id = ConnectDB::subAutoExec( "autosalon_auction_equipment",	array('equipment_id' => $id_key[0]['equipment_id'], 'id_auction' => $data['id']), "INSERT");
					}
				}

				if(count($data['features']) > 0 && is_array($data['features'])) {
					foreach($data['features'] as $row_e) {
						$id_key = $this -> getExtrasList($row_e);
						print_r($id_key);
						ConnectDB::subAutoExec( "autosalon_auction_info",	array('info_id' => $id_key[0]['info_id'], 'id_auction' => $data['id']), "INSERT");
					}
				}

				if(count($data['photos']) > 0 && is_array($data['photos'])) {
					foreach($data['photos'] as $row_p) {
						ConnectDB::subAutoExec( "autosalon_auction_photos",	array('photo_id' => $row_p, 'id_auction' => $data['id']), "INSERT");
					}
				}
			
			} catch(Exception $e) {
				Common::log(__METHOD__, $e->getTraceAsString() . ' code ' . $e->getCode());
			}
		}
		return true;
	}
	
	public function updateAuctionOtomoto($data) {
		
		ConnectDB::subAutoExec( "autosalon_auction", $data, "UPDATE", "otomoto_id = '".$data['otomoto_id']."'");
		return true;
	}
	
	public function clearData($table, $fields) {
		
		if($table != '') {
			$key = array_keys($fields);
			ConnectDB::subExec("DELETE FROM ".$table." WHERE ".$key[0]." = ".$fields[$key[0]]);
		}
		
		return true;
	}
        
    public function getAuctionAdds($marka_id = 0, $model_id = 0, $marka_otomoto_id = 0, $model_otomoto_id = 0, $type = 'CAR', $allegro_id = 0) {
  
		$aResult = array();
		$aResult['features'] = $this->getFeaturesList();
		$aResult['extras'] = $this->getExtrasList();
		$aResult['country'] = $this->getCountryList();
		$aResult['gearbox'] = $this->getGearboxList();
		$aResult['colour'] = $this->getColourList();
		$aResult['fuel'] = $this->getFuelTypeList();
		$aResult['body'] = $this->getBodyList($type);
		$aResult['make'] = $this->getMakeList($type);
		$aResult['model'] = $this->getModelList($marka_id, $marka_otomoto_id);
		$aResult['version'] = $this->getVersionList($marka_id, $model_id, $marka_otomoto_id, $model_otomoto_id);
		$aResult['allegro'] = $this->getAllegroCatsList(self::getAllegroParrent($allegro_id));
		$aResult['pochodzenie'] = $this->getPochodzenieList();

		return $aResult;
    }
	
	static public function getAllegroParrent($id_allegro) {
		
		$sql = "SELECT parent_id FROM ".MyConfig::getValue("dbPrefix")."allegro_cats WHERE otomoto_id = ".$id_allegro;
		try {
			$result = ConnectDB::subQuery($sql, '', '', 'fetch');
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result['parent_id'];
		
	}
	
	public function getPochodzenieList() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_pochodzenie WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getAllegroCatsList($parent_id = 0) {
		
		if($parent_id > 0) {
			$sql2 = " AND parent_id = ".$parent_id;
		} else {
			$sql2 = " AND parent_id = 3 ";
		}
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."allegro_cats WHERE 1 ".$sql2;
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
		
	public function getMakeList($type = 'CAR') {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_producer WHERE 1 AND type = '".$type."'";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getModelList($marka_id = 0, $marka_otomoto_id = 0) {
		
		if($marka_id > 0) {
			$join = " LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cr ON cr.producer_id = ".$marka_id;
			$sql2 = " AND cm.producer_id = cr.otomoto_id";
		}
		
		if($marka_otomoto_id > 0) {
			$sql2 = " AND cm.producer_id = ".$marka_otomoto_id;
			$join = '';
		}
		$sql = "SELECT cm.* FROM ".MyConfig::getValue("dbPrefix")."car_model cm ".$join." WHERE 1 ".$sql2;
		
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getVersionList($marka_id = 0, $model_id = 0, $marka_otomoto_id = 0, $model_otomoto_id = 0) {
		
		if($marka_id > 0) {
			$join = " LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cr ON cr.producer_id = ".$marka_id;
			$sql2 = " AND cv.producer_id = cr.otomoto_id";
		}
		
		if($model_id > 0) {
			$join .= " LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_model cm ON cm.model_id = ".$model_id;
			$sql2 .= " AND cv.model_id = cm.otomoto_id";
		}
		
		if($marka_otomoto_id > 0) {
			$sql2 = " AND cv.producer_id = ".$marka_otomoto_id;
			$join = '';
		}
		
		if($marka_otomoto_id > 0) {
			$sql2 .= " AND cv.model_id = ".$model_otomoto_id;
			$join = '';
		}
		
		$sql = "SELECT cv.* FROM ".MyConfig::getValue("dbPrefix")."car_version cv ".$join." WHERE 1 ".$sql2;
		
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
		
	public function getFeaturesList($key = "") {
		
		if($key != "") {
			$sql2 = " AND otomoto_key = '".$key."'";
		}
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_equipment_list WHERE 1 ".$sql2;
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function getExtrasList($key = "") {
		
		if($key != "") {
			$sql2 = " AND otomoto_key = '".$key."'";
		}
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_info_list WHERE 1 ".$sql2;
	
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
	
	public function getBodyList($type = 'car') {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_body_list WHERE type = '".strtolower($type)."' ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	public function findModelId($model, $producent, $type='CAR') {
		
		if($producent == 9) {
			$model = str_replace("d", '', strtolower($model));
			$model = str_replace("i", '', strtolower($model));
			$model = str_replace("ti", '', strtolower($model));
			$model = str_replace("CI", '', $model);
			$model = str_replace("CD", '', $model);
			$model = str_replace("ld", '', strtolower($model));
		}
		
		$sql = "SELECT c.model_id 
				FROM ".MyConfig::getValue("dbPrefix")."car_model c 
				LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON cp.producer_id = ".$producent." AND cp.type = '".$type."'
				WHERE c.producer_id = cp.otomoto_id AND REPLACE(LOWER(c.model_name),' ','') LIKE '".strtolower($model)."%' ";
		
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			//Log::SLog($e->getTraceAsString());
		}
		
		if(!$result['model_id'] || $result['model_id'] == 0) {
			$sql = "SELECT c.model_id 
				FROM ".MyConfig::getValue("dbPrefix")."car_model c 
				LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON cp.producer_id = ".$producent." AND cp.type = '".$type."'
				WHERE c.producer_id = cp.otomoto_id AND REPLACE(LOWER(c.model_name),' ','') LIKE '%".strtolower($model)."%' ";
	
			try {
				$result = ConnectDB::subQuery($sql, 'fetch');

			} catch (Exception $e) {
				//Log::SLog($e->getTraceAsString());
			}
		}
		
		if(!$result['model_id'] || $result['model_id'] == 0) {
			$model = 'inny';
			$sql = "SELECT c.model_id 
				FROM ".MyConfig::getValue("dbPrefix")."car_model c 
				LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON cp.producer_id = ".$producent." AND cp.type = '".$type."'
				WHERE c.producer_id = cp.otomoto_id AND REPLACE(LOWER(c.model_name),' ','') LIKE '".strtolower($model)."%' ";

			try {
				$result = ConnectDB::subQuery($sql, 'fetch');

			} catch (Exception $e) {
				//Log::SLog($e->getTraceAsString());
			}
		}
		
		return $result['model_id'];
	}
	
	public function findMarkalId($marka, $type='CAR', $bez_dod = '%') {
		
		$sql = "SELECT producer_id FROM ".MyConfig::getValue("dbPrefix")."car_producer WHERE LOWER(producer_name) = '".strtolower($marka).$bez_dod."' AND type = '".$type."'"; 
		
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
		} catch (Exception $e) {
			//Log::SLog($e->getTraceAsString());
		}
		
		return $result['producer_id'];
	}
	
	public function findColourId($value) {
		
		$sql = "SELECT colour_id FROM ".MyConfig::getValue("dbPrefix")."car_colour WHERE LOWER(otomoto_key) = '".strtolower($value)."' ";
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
		} catch (Exception $e) {
			//Log::SLog($e->getTraceAsString());
		}
		
		return $result['colour_id'];
	}
	
	public function findBodyId($value) {
		
		$sql = "SELECT body_id FROM ".MyConfig::getValue("dbPrefix")."car_body_list WHERE LOWER(otomoto_key) = '%".strtolower($value)."%' ";
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
		} catch (Exception $e) {
			//Log::SLog($e->getTraceAsString());
		}
		
		return $result['body_id'];
	}
	
	public function findFuelId($value) {
		
		$sql = "SELECT fuel_id FROM ".MyConfig::getValue("dbPrefix")."car_fuel WHERE LOWER(otomoto_key) = '".strtolower($value)."' ";
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
		} catch (Exception $e) {
			//Log::SLog($e->getTraceAsString());
		}
		
		return $result['fuel_id'];
	}
	
	public function activateAuction($id) {
		
		$auction = $this->getData($id);
		$obApi = new Otomoto_Api();
		$obApi->publicOffer(array('id' => $auction['otomoto_id']));
		
		$this->updateAuction(array('active' => 1, 'id' => $id));
		
		return true;
		
	}
	
	public function deactivateAuction($id) {
		
		$auction = $this->getData($id);
		$obApi = new Otomoto_Api();
		$obApi->hideOffer(array('id' => $auction['otomoto_id']));
		
		$this->updateAuction(array('active' => 0, 'id' => $id));
		
		return true;
		
	}
	
	public function deleteAuction($id) {
		
		$auction = $this->getData($id);
		if(strlen($auction[0]['otomoto_id']) > 3) {
			$obApi = new Otomoto_Api();
			$res1 = $obApi->hideOffer(array('id' => $auction[0]['otomoto_id']));
			$res2 = $obApi->deleteOffer(array('id' => $auction[0]['otomoto_id']));
			print_r($res1); print_r($res2);
		}
		
		$this->clearData('autosalon_auction_equipment', array('id_auction' => $id));
		$this->clearData('autosalon_auction_info', array('id_auction' => $id));
		$this->clearData('autosalon_auction_photos', array('id_auction' => $id));
		$this->clearData('autosalon_auction', array('id' => $id));
		
		return true;
		
	}
	
	static public function addTask($data) {
		
		ConnectDB::subAutoExec( "autosalon_auction_tasks", $data, "INSERT");
		return true;
	}
	
	static public function updateTask($data) {
		
		ConnectDB::subAutoExec( "autosalon_auction_tasks", $data, "UPDATE", "id = ".$data['id']);
		return true;
	}
	
	static public function deleteTask($id) {
		
		self::clearData('autosalon_auction_tasks', array('id' => $id));
		return true;
	}
	
	public function getTaskList($date) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."auction_tasks WHERE date_to_send = '".$date."' ";
		
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	static public function addAuctionError($data) {
		
		ConnectDB::subAutoExec( "autosalon_auction_errors", $data, "INSERT");
		return true;
	}
	
	public function getAuctionErrors($id) {

		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."auction_errors WHERE id_auction = ".$id;
		
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	

}
?>
