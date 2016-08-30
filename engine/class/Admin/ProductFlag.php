<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductFlag
 *
 * @author tomaszcisowski
 */
class Admin_ProductFlag extends AbstractObject {
	
	private $id = 0;
	private $p_id = 0;
	private $flag_name = '';
	private $flag_symbol = '';
	
	public function __construct($id=0) {
		
		if ((int) $id > 0) {
			try {
			
				$sql = "SELECT pf.* FROM " . MyConfig::getValue("__produkty_flagi") . " pf WHERE pf.flag_id = ".$id;

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult) || empty($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id				= $row[ 'flag_id' ];
					$this -> p_id			= $row[ 'p_id' ];
					$this -> flag_name		= $row[ 'flag_name' ];
					$this -> flag_symbol	= $row[ 'flag_symbol' ];
					
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

	public function getProductId() {
		return $this -> p_id;
	}
    
	public function getFlagName() {
		return $this -> flag_name;
	}

	public function getFlagSymbol() {
		return $this -> flag_symbol;
	}
	
	static public function delete($id) {

		$sql = "DELETE FROM " . MyConfig::getValue("__produkty_flagi") . " WHERE flag_id = ".$id;

		ConnectDB::subExec($sql);
		return true;
	}
	
	public function save($aData) {

		try {

			if($aData['flag_id'])
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__produkty_flagi"), $aData, "UPDATE", "flag_id = ".$aData['flag_id']);
			else
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__produkty_flagi"), $aData, "INSERT");

			if($res)
				return true;
			else
				return false;

		}catch (PDOException $e){

			return false;
		}

	} 
	
}

?>
