<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdminCategory
 *
 * @author tomi_weber
 * @todo jasy
 * 
 */


class Admin_Devilery {

	protected $devilery_id;
	protected $devilery_name;
	protected $devilery_price;
	protected $id_country;
	protected $devilery_order;
	protected $devilery_active;

	public function __construct($devileryId = 0) {

		if ($devileryId > 0) {
			try {

				$sql = "SELECT *
						FROM shop_devilery
						WHERE devilery_id = ".$devileryId."
						";

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> devilery_id			= $row['devilery_id'];
					$this -> devilery_name			= $row['devilery_name'];
					$this -> devilery_price			= $row['devilery_price'];
					$this -> id_country			= $row['id_country'];
					$this -> devilery_order			= $row['devilery_order'];
					$this -> devilery_active		= $row['devilery_active'];
				}
			} catch (Exception $e){
				Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
				return false;
			}
		}
	}

	public function getDevileryId() {
		return $this -> devilery_id;
	}

	public function getDevileryName() {
		return $this -> devilery_name;
	}
	
	public function getDevlieryPrice() {
		return $this -> devilery_price;
	}
	
	public function getIdCountry() {
		return $this -> id_country;
	}
	
	public function getDevileryOrder() {
		return $this -> devilery_order;
	}
	
	public function getDevileryActive() {
		return $this -> devilery_active;
	}

	public function getDevileryList($start, $limit = 15) {
		
		$aResult = array();
		$limit_sql = "";
		
		if(!$parent)
			$limit_sql = " LIMIT ".$start.", ".$limit;
		
		$sql = "SELECT *, c.country as devilery_country FROM shop_devilery LEFT JOIN shop_country c ON c.id_country = shop_devilery.id_country ORDER BY devilery_order ASC".$limit_sql;
		
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				return $aResult;
			} else return false;
		} catch (Exception $e){
			
			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}

	}

	public function getCountryList() {

		$sql = "SELECT * FROM shop_country";

		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				if(is_array($aResult)) {
					$result2 = array();
					foreach($aResult as $row) {
						$result2[$row['id_country']] = $row;
					}
				} else return false;
			} else return false;
		} catch (Exception $e){

			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
		return $result2;
	}

	public function save($aData) {
	
		try {
			if($aData['devilery_id'] != 0)
				$res = ConnectDB::subAutoExec ("shop_devilery", $aData, "UPDATE", "devilery_id = ".$aData['devilery_id']);
			else
				$res = ConnectDB::subAutoExec ("shop_devilery", $aData, "INSERT");

			if($res)
				return $res;
			else
				return false;
		} catch (Exception $e){
			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}

	public function delete() {

		if($this->cat_id) {
			$sql = "DELETE FROM shop_categories WHERE cat_id = ".$this -> cat_id;
			
			try {
				if(ConnectDB::subExec($sql))
					return true;
				else return false;

			} catch (Exception $e) {
				Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
				return false;
			}
		} else return false;
	}
	
	public function sortAjax($order, $parent = 0) {
		
		if($order) {
			$aOrder = explode(",",$order);
			$i = 1;
			
			if($parent) {
				$where = " AND cat_parent = ".$parent;
			}
			$pdo = new ConnectDB() ;
			foreach($aOrder as $row) {
				$sql = "UPDATE shop_categories SET cat_order = ".$i." WHERE cat_id = ".$row.$where;
				ConnectDB::subExec($sql);
				$i++;
			}
		}
	}

}
?>
