<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductPrices
 *
 * @author tomaszcisowski
 */
class Admin_ProductPrice extends AbstractObject {
	
	private $id = 0;
	private $p_id = 0;
	private $price = 0;
	private $price_gross = 0;
	private $quantity = 0;
	private $active = 0;
	
	public function __construct($id=0) {
		
		if ((int) $id > 0) {
			try {
			
				$sql = "SELECT pc.* FROM " . MyConfig::getValue("__produkty_ceny") . " pc WHERE pc.price_id = ".$id;

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult) || empty($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id				= $row[ 'price_id' ];
					$this -> p_id			= $row[ 'p_id' ];
					$this -> price			= $row[ 'price' ];
					$this -> price_gross	= $row[ 'price_gross' ];
					$this -> quantity		= $row[ 'quantity' ];
					$this -> active			= $row[ 'active' ];
					
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
    
	public function getPrice() {
		return $this -> price;
	}
	
	public function getPriceGross() {
		return $this -> price_gross;
	}
	
	public function getQuantity() {
		return $this -> quantity;
	}
	
	public function getActive() {
		return $this -> active;
	}

	
	static public function delete($id) {

		$sql = "DELETE FROM " . MyConfig::getValue("__produkty_ceny") . " WHERE price_id = ".$id;

		ConnectDB::subExec($sql);
		return true;
	}
	
	public function save($aData) {

		try {

			if($aData['price_id'])
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__produkty_ceny"), $aData, "UPDATE", "price_id = ".$aData['price_id']);
			else
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__produkty_ceny"), $aData, "INSERT");

			if($res)
				return true;
			else
				return false;

		}catch (PDOException $e){

			return false;
		}

	} 
	
	public function getList($p_id = 0) {
		
		if($p_id > 0) {
			$aResult = array();
			$sql = "SELECT * FROM ".MyConfig::getValue("__produkty_ceny")." WHERE 1 AND p_id = ".$p_id." ORDER BY quantity ASC";
			try {
				if($aResult = ConnectDB::subQuery($sql)) {
				return $aResult;
				} else return false;
			} catch (PDOException $e){

				return false;
			}
		}
	}
	
}

?>
