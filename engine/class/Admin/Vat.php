<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdminVat
 *
 * @author tomi_weber
 */
class Admin_Vat {

    protected $vat_id;

    protected $vat_level;

    public function __construct($vatId = 0) {

	if ($vatId > 0){
	    try {

		$sql = "SELECT *
				FROM shop_vat_levels
				WHERE vat_id = ".$vatId."
				";
		
		$aResult = ConnectDB::subQuery($sql);
		if(!is_array($aResult)){
			return false;
		}
		foreach ($aResult as $row) {
		    $this -> vat_id		= $row['vat_id'];
		    $this -> vat_level		= $row['vat_level'];
		}
	    }catch (PDOException $e){
		//echo "Błąd nie można utworzyć obiektu material.";
		return false;
	    }
	}
	else {
	    $this -> vat_id		= 0;
	    $this -> vat_level		= 0;
	}
    }

    public function setVatId($id) {
	$this->vat_id = $id;
    }

    public function setVatLevel($level) {
	$this->vat_level = $level;
    }

    public function getVatId($id) {
	return $this->vat_id;
    }

    public function getVatLevel($level) {
	return $this->vat_level;
    }

    public function getVatLevels() {

	$aResult = array();
	$sql = "SELECT * FROM shop_vat_levels ORDER BY vat_level ASC";
	try {
	    if($aResult = ConnectDB::subQuery($sql)) {
		return $aResult;
	    } else return false;
	} catch (PDOException $e){

	    return false;
	}
    }

    public function save() {
	
	if($this->vat_id)
	    $sql = "UPDATE shop_vat_levels SET vat_level = ".$this->vat_level." WHERE vat_id = ".$this->vat_id;
	else
	    $sql = "INSERT INTO shop_vat_levels (vat_level) VALUES (".$this->vat_level.")";

	try {
	    if(ConnectDB::subExec($sql))
		return true;
	    else return false;

	}catch (PDOException $e){

	    return false;
	}
	
    }

    public function delete() {

	if($this->vat_id) {
	    $sql = "DELETE FROM shop_vat_levels WHERE vat_id = ".$this->vat_id;
	    try {
		if(ConnectDB::subExec($sql))
		    return true;
		else return false;

	    }catch (PDOException $e){

		return false;
	    }
	} else return false;
    }

}
?>
