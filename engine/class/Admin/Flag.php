<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Flag
 *
 * @author tomi
 */
class Admin_Flag extends AbstractObject {
	
	private $id = 0;
	private $flag_name = '';
	private $flag_symbol = '';
	
	public function __construct($id=0) {
		
		if ((int) $id > 0) {
			try {
			
				$sql = "SELECT f.* FROM " . MyConfig::getValue("__flagi") . " f WHERE f.f_id = ".$id;

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult) || empty($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id				= $row[ 'f_id' ];
					$this -> flag_name		= $row[ 'f_name' ];
					$this -> flag_symbol	= $row[ 'f_symbol' ];
					
				}
			}catch (Exception $e){
				echo "Błąd nie można utworzyć obiektu ".__CLASS__;
				return false;
			}
		}
	}
	
	public function getId() {
		return $this -> id;
	}

    
	public function getFlagName() {
		return $this -> f_name;
	}

	public function getFlagSymbol() {
		return $this -> f_symbol;
	}
	
	static public function delete($id) {

		$sql = "DELETE FROM " . MyConfig::getValue("__flagi") . " WHERE f_id = ".$id;

		ConnectDB::subExec($sql);
		return true;
	}
	
	public function save($aData) {

		try {

			if($aData['flag_id'])
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__flagi"), $aData, "UPDATE", "f_id = ".$aData['f_id']);
			else
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__flagi"), $aData, "INSERT");

			if($res)
				return true;
			else
				return false;

		}catch (PDOException $e){

			return false;
		}

	}
	
	public function getList($flagi = array()) {
		
		
		if(!empty($flagi) && count($flagi) > 0) {
			foreach($flagi as $row) {
				$new[] = $row['f_id'];
			}
			
			$sql2 = ' AND f_id NOT IN (' . implode(",", $new) . ')';
		}
		
		$aResult = array();
		$sql = "SELECT * FROM ".MyConfig::getValue("__flagi")." WHERE 1".$sql2;
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
			return $aResult;
			} else return false;
		} catch (Exception $e){

			return false;
		}
	}
	
	
}

?>
