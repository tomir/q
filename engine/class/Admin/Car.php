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

	public $carListCount = false;
    private $filtr = array();
	
	static public function checkCar($id, $id_pochodzenie = 1) {
		
		$sql = "SELECT car_id FROM ".MyConfig::getValue("dbPrefix")."car WHERE car_zew_id = ".$id." AND car_pochodzenie = ".$id_pochodzenie;
		try {
			$result = ConnectDB::subQuery($sql, 'fetch');
			
		} catch (Exception $e) {
			//Log::SLog($e->getTraceAsString());
		}
		if(isset($result['car_id']) && $result['car_id'] > 0) {
			return $result['car_id'];
		} else {
			return false;
		}

	}
	
	public function carGet($id) {
		$sql = "SELECT c.*, ck.symbol_kraju, cpo.opis_otomoto as opis FROM autosalon_car c LEFT JOIN autosalon_car_pochodzenie cpo ON cpo.id_pochodzenie = c.car_pochodzenie LEFT JOIN autosalon_car_kraj ck ON ck.id_kraj = cpo.id_kraj WHERE 
			c.car_id = ".$id." LIMIT 1";
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql,'','','fetch');
			
			return $aResult;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','aa',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function carGetInfo($id) {
		$sql = "SELECT * FROM autosalon_car_info JOIN autosalon_car_info_list on autosalon_car_info.info_id = autosalon_car_info_list.info_id
					WHERE car_id = ".$id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql,'','','fetchAll');
			
			$new = array();
			foreach($aResult as $row) {
				$new[$row['info_id']] = $row;
			}
			
			return $new;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','aa',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function carGetEquipment($id) {
		$sql = "SELECT * FROM autosalon_car_equipment JOIN autosalon_car_equipment_list on autosalon_car_equipment.equipment_id = autosalon_car_equipment_list.equipment_id
					WHERE car_id = ".$id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql,'','','fetchAll');
			$new = array();
			foreach($aResult as $row) {
				$new[$row['equipment_id']] = $row;
			}
			
			return $new;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','aa',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function carGetPhotos($id) {
		$sql = "SELECT * FROM autosalon_car_photo WHERE car_id = ".$id." ORDER by photo_order";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql,'','','fetchAll');
			return $aResult;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','aa',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	
	public function carList($start = 0, $ile = 10, $order = 'default') {
		
		switch($order) {
			case 'price': $sql_order = 'c.car_price'; break;
			case 'year': $sql_order = 'c.car_year'; break;
			case 'mileage': $sql_order = 'c.car_mileage'; break;
			case 'random': $sql_order = 'RAND()'; break;
			default: $sql_order = 'c.car_id DESC';
		}

		$sql = "SELECT c.*, au.id as id_auction, cp.producer_name, cm.model_name, cf.name, cph.photo_id, cph.photo_filename , cpo.nazwa as pochodzenie
						FROM ".MyConfig::getValue("dbPrefix")."car c
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON cp.producer_id = c.producer_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_model cm ON cm.model_id = c.model_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_fuel cf ON cf.fuel_id = c.fuel_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_pochodzenie cpo ON cpo.id_pochodzenie = c.car_pochodzenie
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_photo cph ON (cph.car_id = c.car_id AND cph.photo_order = 1)
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."auction au ON au.car_id = c.car_id
						WHERE 1 ";
		
		$sql .= $this->getFiltr();
		$sql .= "ORDER by ".$sql_order."
				 LIMIT ".$start.", ".$ile;
		
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'','','fetchAll');
			
			$new = array();
			if(is_array($aResult) && count($aResult) > 0 && $aResult[0]['car_id'] > 0) {
				foreach($aResult as $row) {
					$new[$row['car_id']] = $row;
					$new[$row['car_id']]['photos']	= $this->carGetPhotos($row['car_id']);
				}

				return $new;
			}
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carList',$sql);
		}
	}
	
	public function carCount() {

		$sql = "SELECT count(c.car_id) as count_car 
					FROM ".MyConfig::getValue("dbPrefix")."car c 
					WHERE 1";
		$sql .= $this->getFiltr();
		
		try {
	
			$ile = (array)ConnectDB::subQuery($sql,'','','fetch');
			$this->carListCount = $ile['count_car'];
			
			return $this->carListCount;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carCount',$sql);
		}
	}
	
	public function getFiltr() {
		
		$sql = "";
		/*
		 * Marka
		 */
		if(is_array($this->filtr['car_producer']) && count($this->filtr['car_producer']) > 0) {
			$sql .= " AND c.producer_id IN (".implode(",",$this->filtr['car_producer']).") ";
		}
		
		if(isset($this->filtr['car_producer']) && is_numeric($this->filtr['car_producer']) && $this->filtr['car_producer'] > 0) {
			$sql .= " AND c.producer_id = ".$this->filtr['car_producer']." ";
		}
		
		/*
		 * Model
		 */
		if(is_array($this->filtr['car_model']) && count($this->filtr['car_model']) > 0) {
			$sql .= " AND c.model_id IN (".implode(",",$this->filtr['car_model']).") ";
		}
		
		if(isset($this->filtr['car_model']) && is_numeric($this->filtr['car_model']) && $this->filtr['car_model'] > 0) {
			$sql .= " AND c.model_id = ".$this->filtr['car_model']." ";
		}
		
		/*
		 * Paliwo
		 */
		if(is_array($this->filtr['car_fuel']) && count($this->filtr['car_fuel']) > 0) {
			$sql .= " AND c.fuel_id IN (".implode(",",$this->filtr['car_fuel']).") ";
		}
		
		if(isset($this->filtr['car_fuel']) && is_numeric($this->filtr['car_fuel']) && $this->filtr['car_fuel'] > 0) {
			$sql .= " AND c.fuel_id = ".$this->filtr['car_fuel']." ";
		}
		
		/*
		 * Typy
		 */
		if(is_array($this->filtr['car_type']) && count($this->filtr['car_type']) > 0) {
			$sql .= " AND c.car_type IN ('".implode("','",$this->filtr['car_type'])."') ";
		}
		
		if(isset($this->filtr['car_type']) && $this->filtr['car_type'] != '') {
			$sql .= " AND c.car_type = '".$this->filtr['car_type']."' ";
		}
		
		/*
		 * Przebieg
		 */
		if(isset($this->filtr['car_mileage_od']) && is_numeric($this->filtr['car_mileage_od']) && $this->filtr['car_mileage_od'] > 0) {
			$sql .= " AND c.car_mileage >= ".$this->filtr['car_mileage_od']." ";
		}
		
		if(isset($this->filtr['car_mileage_do']) && is_numeric($this->filtr['car_mileage_do']) && $this->filtr['car_mileage_do'] > 0) {
			$sql .= " AND c.car_mileage <= ".$this->filtr['car_mileage_do']." ";
		}
		
		/*
		 * Roczniki
		 */
		if(isset($this->filtr['car_year_od']) && is_numeric($this->filtr['car_year_od']) && $this->filtr['car_year_od'] > 0) {
			$sql .= " AND c.car_year >= ".$this->filtr['car_year_od']." ";
		}
		
		if(isset($this->filtr['car_year_do']) && is_numeric($this->filtr['car_year_do']) && $this->filtr['car_year_do'] > 0) {
			$sql .= " AND c.car_year <= ".$this->filtr['car_year_do']." ";
		}
		
		if($this->filtr['data_do'] != '' && $this->filtr['data_do'] != 'now') {
			$sql .= ' AND c.sale_date <= "'.$this->filtr['data_do'].'" ';
		}
		
		if($this->filtr['data_do'] == 'now') {
			$sql .= ' AND c.sale_date <= "'.date("Y-m-d H:i:s").'" ';
		}
		
		if(isset($this->filtr['pochodzenie']) && is_numeric($this->filtr['pochodzenie']) && $this->filtr['pochodzenie'] > 0) {
			$sql .= " AND c.car_pochodzenie = ".$this->filtr['pochodzenie']." ";
		}
		
		if(isset($this->filtr['id_not']) && is_array($this->filtr['id_not'])) {
			$sql .= " AND c.car_id NOT IN (".implode(",",$this->filtr['id_not']).") ";
		}
		
		return $sql;
	}
	
	public function deleteCar($id) {
		
		$this->clearData('autosalon_car_equipment', array('car_id' => $id));
		$this->clearData('autosalon_car_info', array('car_id' => $id));
		$this->clearData('autosalon_car_photo', array('car_id' => $id));
		$this->clearData('autosalon_car', array('car_id' => $id));
		return true;
		
	}
	
	public function clearData($table, $fields) {
		
		if($table != '') {
			$key = array_keys($fields);
			ConnectDB::subExec("DELETE FROM ".$table." WHERE ".$key[0]." = ".$fields[$key[0]]);
		}
		
		return true;
	}
	
	public function setFiltr($filtr) {
		$this->filtr = $filtr;
	}
}
?>
