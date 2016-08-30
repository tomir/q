<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Tomasz Cisowski <tomasz.cisowski@enp.local>
 */
class Producent {

	public function getProducer($id) {
		
		$sql = 'SELECT * FROM shop_producents 
			WHERE 1 AND producent_id = '.$id;
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'fetch');

			return $aResult;
			
		} catch (PDOException $e){

			return false;
		}
		
	}
	
	public function getProducentList($filtr) {

		
		$sql = 'SELECT * FROM shop_producents WHERE 1 ';
		$sql .= $this->getFiltr($filtr);
		$sql .= ' ORDER BY producent_name ASC';
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);

			return $aResult;
			
		} catch (PDOException $e){

			return false;
		}
    }
	
	public function getFiltr($filtr) {
		
		$sql = "";
		$this->filtr = $filtr;
		
		if(isset($this->filtr['active']) && is_numeric($this->filtr['active'])) {
			$sql .= " AND p.p_active = ".$this->filtr['active']." ";
		}
		
		if(is_array($this->filtr['cat_id']) && count($this->filtr['cat_id']) > 0) {
			$sql .= " AND pc.cat_id IN (".implode(",",$this->filtr['cat_id']).") ";
		}
		
		if(isset($this->filtr['promocja']) && is_numeric($this->filtr['promocja'])) {
			$sql .= " AND pf.flag_id = ".$this->filtr['promocja']." ";
		}
		
		
		return $sql;
	}
	
	public function getProducentListSelect($filtr) {
		
		$all = $this->getProducentList($filtr);
		$new = array();
			
		foreach($all as $row) {
			$new[$row['producent_id']] = $row['producent_name'];
		}
		return $new;
		
	}
	
	public function findProducent($string) {

		$sql = "SELECT producent_id FROM shop_producents WHERE producent_url = '".Misc::utworzSlug($string)."'";
		
		$aResult = array();
		$aResult = ConnectDB::subQuery($sql, 'one');
		
		return (int)$aResult;
	}
}

