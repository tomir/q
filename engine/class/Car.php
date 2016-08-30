<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classCar
 *
 * @author t.jurdzinski
 */
class Car {
	private $carPriceMin = false;
	private $carPriceMax = false;

    public $carListCount = false;
    private $filtr = array();
    private $carType = false;
    private $carFuel = false;
    private $carProducer = false;
	private $carLang = 'pl';

    public function carGet($id) {
		
		$sql = "SELECT c.*, cpt.symbol as waluta_symbol, cpt.nazwa as waluta_nazwa, cpo.symbol as pochodzenie, cpo.icon as ikona, cpo.opis_strona as opis,  cp.producer_name as producer, cm.model_name as model, cg.name as gearbox, cc.name as colour, cf.name as fuel, cb.name as body
					FROM ".MyConfig::getValue("dbPrefix")."car c
					JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON cp.producer_id = c.producer_id
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_model cm ON cm.model_id = c.model_id
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_gearbox cg ON cg.gearbox_id = c.gearbox_id
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_colour cc ON cc.colour_id = c.colour_id
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_fuel cf ON cf.fuel_id = c.fuel_id
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_body_list cb ON cb.body_id = c.body_list_id
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_pochodzenie cpo ON cpo.id_pochodzenie = c.car_pochodzenie
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_price_type cpt ON cpt.price_type_id = c.car_price_type
					WHERE 1 AND c.car_id = ".$id." LIMIT 1";
		
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'fetch');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carGet',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function carGetInfo($id) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_info ci 
						 JOIN ".MyConfig::getValue("dbPrefix")."car_info_list cil ON ci.info_id = cil.info_id
						 WHERE 1 AND ci.car_id = ".$id;
		
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carGetInfo',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function carGetEquipment($id) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_equipment ce
						 LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_equipment_list cep ON ce.equipment_id = cep.equipment_id
						 WHERE 1 AND ce.car_id = ".$id;
		
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carGetEquipment',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function carGetPhotos($id) {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_photo 
						 WHERE 1 AND car_id = ".$id." 
						 ORDER by photo_order";
		
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carGetPhotos',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	
	public function getPochodzenieList() {
		
		//$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_pochodzenie WHERE 1 ";
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_kraj WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}

	public function carList($start = 0, $ile = 10, $order = 'default') {
		
		switch($order) {
			case 'price': $sql_order = 'c.car_price'; break;
			case 'year': $sql_order = 'c.car_year'; break;
			case 'mileage': $sql_order = 'c.car_mileage'; break;
			case 'random': $sql_order = 'c.car_id'; $start = rand(0, 7500); break;
			default: $sql_order = 'cp.producer_name, cm.model_name';
		}

		$sql = "SELECT c.*, cpt.symbol as waluta_symbol, cpt.nazwa as waluta_nazwa, cp.producer_name, cm.model_name, cf.name as paliwo, cph.photo_id, cpo.symbol as pochodzenie, cpo.icon as ikona, cph.photo_filename 
						FROM ".MyConfig::getValue("dbPrefix")."car c
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON cp.producer_id = c.producer_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_model cm ON cm.model_id = c.model_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_fuel cf ON cf.fuel_id = c.fuel_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_photo cph ON (cph.car_id = c.car_id AND cph.photo_order = 1)
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_pochodzenie cpo ON cpo.id_pochodzenie = c.car_pochodzenie
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_price_type cpt ON cpt.price_type_id = c.car_price_type
						WHERE 1 ";
		
		$sql .= $this->getFiltr();
		$sql .= "ORDER by ".$sql_order."
				 LIMIT ".$start.", ".$ile;
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			echo $e->getMessage(); exit();
			mail('t.cisowski@gmail.com','carList',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	
	public function carListSearch($start = 0, $ile = 10, $order = 'default', $query = "") {
		
		switch($order) {
			case 'price': $sql_order = ',c.car_price'; break;
			case 'year': $sql_order = ',c.car_year'; break;
			case 'mileage': $sql_order = ',c.car_mileage'; break;
			case 'random': $sql_order = ',RAND()'; break;
			default: $sql_order = ',cp.producer_name, cm.model_name';
		}

		$sql = "SELECT c.*, cpt.symbol as waluta_symbol, cpt.nazwa as waluta_nazwa, cp.producer_name, cm.model_name, cf.name as paliwo, cph.photo_id, cpo.symbol as pochodzenie, cpo.icon as ikona, cph.photo_filename,
				MATCH (c.wyszukiwarka) AGAINST ('".Wyszukiwarka::getFtSearch($query, true)."' IN BOOLEAN MODE) AS score
						FROM ".MyConfig::getValue("dbPrefix")."car c
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON cp.producer_id = c.producer_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_model cm ON cm.model_id = c.model_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_fuel cf ON cf.fuel_id = c.fuel_id
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_photo cph ON (cph.car_id = c.car_id AND cph.photo_order = 1)
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_pochodzenie cpo ON cpo.id_pochodzenie = c.car_pochodzenie
						LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_price_type cpt ON cpt.price_type_id = c.car_price_type
						WHERE 1 AND
						MATCH (c.wyszukiwarka) AGAINST ('".Wyszukiwarka::getFtSearch($query)."' IN BOOLEAN MODE) ";
		
		$sql .= $this->getFiltr();
		$sql .= "ORDER by score DESC ".$sql_order."
				 LIMIT ".$start.", ".$ile;
		if($_GET['debug'] == 1) {
			var_dump($sql); exit();
		}
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carList',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	
	public function getFiltr() {
		
		$sql = "";
		
		
		/*
		 * Car
		 */
		if(is_array($this->filtr['car_id']) && count($this->filtr['car_id']) > 0) {
			$sql .= " AND c.car_id IN (".implode(",",$this->filtr['car_id']).") ";
		}
		
		if(is_array($this->filtr['not_car_id']) && count($this->filtr['not_car_id']) > 0) {
			$sql .= " AND c.car_id NOT IN (".implode(",",$this->filtr['not_car_id']).") ";
		}
		
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
		 * Pochodzenie
		 */
		if(is_array($this->filtr['car_pochodzenie']) && count($this->filtr['car_pochodzenie']) > 0) {
			$sql .= " AND c.car_pochodzenie IN (".implode(",",$this->filtr['car_pochodzenie']).") ";
		}
		
		if(isset($this->filtr['car_pochodzenie']) && is_numeric($this->filtr['car_pochodzenie']) && $this->filtr['car_pochodzenie'] > 0) {
			$sql .= " AND c.car_pochodzenie = ".$this->filtr['car_pochodzenie']." ";
		}
		
		/*
		 * Kraj
		 */
		if(is_array($this->filtr['car_kraj']) && count($this->filtr['car_kraj']) > 0) {
			$sql .= " AND c.car_pochodzenie IN (SELECT id_pochodzenie FROM ".MyConfig::getValue("dbPrefix")."car_pochodzenie WHERE id_kraj IN (".implode(",",$this->filtr['car_kraj']).") ) ";
		}
		
		if(isset($this->filtr['car_kraj']) && is_numeric($this->filtr['car_kraj']) && $this->filtr['car_kraj'] > 0) {
			$sql .= " AND c.car_pochodzenie IN (SELECT id_pochodzenie FROM ".MyConfig::getValue("dbPrefix")."car_pochodzenie WHERE id_kraj = ".$this->filtr['car_kraj'].") ";
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
		
		if(isset($this->filtr['last24']) && is_numeric($this->filtr['last24']) && $this->filtr['last24'] > 0) {
			$sql .= " AND c.car_add >= now() - INTERVAL 1 DAY ";
		}
		
		
		return $sql;
	}

	public function carCount() {
		
		
		echo 1;
		$sql = "SELECT count(c.car_id) as count_car 
					FROM ".MyConfig::getValue("dbPrefix")."car c 
					WHERE 1";
		$sql .= $this->getFiltr();
		
		try {
	
			$ile = ConnectDB::subQuery($sql,'one');
			$this->carListCount = $ile;
			
			return $this->carListCount;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carCount',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	
	public function carCountSearch($query) {
		
		

		$sql = "SELECT count(c.car_id) as count_car 
					FROM ".MyConfig::getValue("dbPrefix")."car c 
					WHERE MATCH (c.wyszukiwarka) AGAINST ('".Wyszukiwarka::getFtSearch($query)."' IN BOOLEAN MODE) >= ".Wyszukiwarka::getFtSearchIlosc($query)." ";
		$sql .= $this->getFiltr();
		
		try {
	
			$ile = ConnectDB::subQuery($sql,'one');
			$this->carListCount = $ile;
			
			return $this->carListCount;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carCount',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function carProducersList($type = false, $ilosc = false, $order = false) {
		
		if($type) $where_type = " AND type = '".$type."' ";
		
		if($ilosc) {
			$where_type .= ' HAVING ile > 15 ';
		} else {
			$where_type .= ' HAVING ile > 0 ';
		}
		
		if($order) {
			$order_type .= ' ile DESC ';
		} else {
			$order_type .= ' cp.producer_name ';
		}
		
		$sql = "SELECT cp.producer_name, cp.producer_id, (SELECT count(c2.car_id) FROM ".MyConfig::getValue("dbPrefix")."car c2 WHERE cp.producer_id = c2.producer_id) as ile FROM ".MyConfig::getValue("dbPrefix")."car_producer cp WHERE cp.type = 'CAR' ".$where_type." ORDER BY ".$order_type;

		try {
			$aResult = (array)ConnectDB::subQuery($sql, 'fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carProducersList',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	
	public function carModelsList($producer_list, $ile = true) {
		
		if($ile) {
			$select_1 = " , (SELECT count(c2.car_id) FROM ".MyConfig::getValue("dbPrefix")."car c2 WHERE c2.model_id = cm.model_id) as ile";
			$where_1 = "HAVING ile > 0";
		}
		$sql = "SELECT cm.model_name, cm.model_id ".$select_1." FROM ".MyConfig::getValue("dbPrefix")."car_model cm WHERE cm.producer_id IN (SELECT otomoto_id FROM ".MyConfig::getValue("dbPrefix")."car_producer cr WHERE cr.producer_id IN (".implode(",",$producer_list).") ) ".$where_1." ORDER BY cm.producer_id, cm.model_name";

		try {
			$aResult = (array)ConnectDB::subQuery($sql, 'fetchAll');
			if(!is_array($aResult)) return null;
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carModelsList',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	
	public function getFuelTypeList($main = 0) {
		
		if($main == 1) {
			$where = " AND main = 1";
		}
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car_fuel WHERE 1 ".$where;
		try {
			$result = ConnectDB::subQuery($sql);
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}

	public function carGetSimilar($price) {

		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."car JOIN ".MyConfig::getValue("dbPrefix")."car_producer USING (producer_id)
					JOIN ".MyConfig::getValue("dbPrefix")."car_photo on (".MyConfig::getValue("dbPrefix")."car_photo.car_id = ".MyConfig::getValue("dbPrefix")."car.car_id AND photo_order = 1)
					WHERE 1 AND car_price <= ".($price*1.1)."
					ORDER by car_price DESC
					LIMIT 3";
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carGetSimilar',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function carGetPromo($lang) {

		$sql = "SELECT * FROM autosalon_car_promo JOIN autosalon_car USING (car_id) JOIN autosalon_car_lang USING (car_id) JOIN autosalon_car_producer USING (producer_id)
					JOIN autosalon_car_photo on (autosalon_car_photo.car_id = autosalon_car.car_id AND photo_order = 1)
					WHERE 1 AND autosalon_car_lang.lang_prefix = '".$lang."'
                    GROUP by autosalon_car.car_id
					ORDER by promo_order
					LIMIT 4";
		try {
			$aResult = (array)ConnectDB::subQuery($sql,'fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','carGetPromo',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function setFiltr($filtr) {
		$this->filtr = $filtr;
	}
	
	public function getProducerFiltr() {
		
		if(is_array($this->filtr['car_producer'])) {
			foreach($this->filtr['car_producer'] as $row) {
				$new[$row] = $row;
			}
		} elseif($this->filtr['car_producer'] > 0) {
			return array($this->filtr['car_producer'] => $this->filtr['car_producer']);
		}
		return $new;
	}
	
	public function getModelFiltr() {
		
		if(is_array($this->filtr['car_model'])) {
			foreach($this->filtr['car_model'] as $row) {
				$new[$row] = $row;
			}
		} elseif($this->filtr['car_model'] > 0) {
			return array($this->filtr['car_model'] => $this->filtr['car_model']);
		}
		return $new;
	}
	
	public function getFuelFiltr() {
		foreach($this->filtr['car_fuel'] as $row) {
			$new[$row] = $row;
		}
		
		return $new;
	}
	
	public function getPochodzenieFiltr() {
		foreach($this->filtr['car_kraj'] as $row) {
			$new[$row] = $row;
		}
		
		return $new;
	}
	
	public function getSortedFiltr($fitered, $list, $only = false) {
		
		if(count($fitered) > 0 && is_array($fitered)) {
			$i=0;
			foreach($list as $row) {
				if(in_array($row['producer_id'], $fitered)) {
					$new[] = $list[$i];
					unset($list[$i]);
				}
				$i++;
			}

			if($only)
				return $new;
			else
				return $new+$list;
		} else {
			return $list;	
		}
	}
	
	static public function mapProducer($name, $type) {
		
		$obCarTmp = new Car();
		$aProducers = $obCarTmp -> carProducersList($type);
		foreach($aProducers as $row) {
			if($name == Misc::makeSlug($row['producer_name']))
				return $row['producer_id'];
		}
		
		return false;
	}
	
	static public function mapModel($name, $producer_id) {
		
		$obCarTmp = new Car();
		$aModels = $obCarTmp -> carModelsList(array(0 => $producer_id), false);
		foreach($aModels as $row) {
			if($name == Misc::makeSlug($row['model_name']))
				return $row['model_id'];
		}
		
		return false;
		
	}
	
	static public function getProducerName($id) {
		
		if($id > 0) {
			$sql = "SELECT producer_name FROM ".MyConfig::getValue("dbPrefix")."car_producer WHERE producer_id = ".$id;
			try {
				$result = ConnectDB::subQuery($sql, "one");
				
			} catch (Exception $e) {
				Log::SLog($e->getTraceAsString());
			}
			
			return $result;
		}
	}
	
	static public function getModelName($id) {
		
		$sql = "SELECT model_name FROM ".MyConfig::getValue("dbPrefix")."car_model WHERE model_id = ".$id;
		try {
			$result = ConnectDB::subQuery($sql, "one");
			
		} catch (Exception $e) {
			Log::SLog($e->getTraceAsString());
		}
		
		return $result;
	}
	
	static public function sortTypes($types, $type) {
		
		$new = array();
		foreach($types as $key => $row) {
			if($key != $type) {
				$new[$key] = $row;
			}
		}
		
		$new[$type] = $types[$type];
		
		return $new;
	}
}
?>