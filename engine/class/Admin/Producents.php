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
class Admin_Producents {

	protected $producent_id;
	protected $producent_name;
	protected $producent_logo;

    public function __construct($producentId = 0) {

	if ($producentId > 0){
	    try {

		$sql = "SELECT *
				FROM shop_producents
				WHERE producent_id = ".$producentId."
				";
		
		$aResult = ConnectDB::subQuery($sql);
		if(!is_array($aResult)){
			return false;
		}
		foreach ($aResult as $row) {
		    $this -> producent_id			= $row['producent_id'];
		    $this -> producent_name		= $row['producent_name'];
		    $this -> producent_logo		= $row['producent_logo'];
		}
	    }catch (PDOException $e){
		//echo "Błąd nie można utworzyć obiektu material.";
		return false;
	    }
	}
    }

    public function getProducentId() {
	return $this->producent_id;
    }

    public function getProducentName() {
	return $this->producent_name;
    }

    public function setProducentName($val) {
	$this->producent_name = $val;
    }

    public function getProducentLogo() {
	return $this->producent_logo;
    }

    public function getProducents() {

	$aResult = array();
	$sql = "SELECT * FROM shop_producents ORDER BY producent_name ASC";
	try {
	    if($aResult = ConnectDB::subQuery($sql)) {
		return $aResult;
	    } else return false;
	} catch (PDOException $e){

	    return false;
	}
    }

    public function save($aData) {

	try {

		if($aData['producent_id'])
			$res = ConnectDB::subAutoExec ("shop_producents", $aData, "UPDATE", "producent_id = ".$aData['producent_id']);
		else
			$res = ConnectDB::subAutoExec ("shop_producents", $aData, "INSERT");

		if($res)
			return $res;
		else
			return false;

	}catch (PDOException $e){

	    return false;
	}
	
    }
    
    public function search() {
        if($this->producent_name) {
            try {
                $sql = "SELECT producent_id as id FROM shop_producents WHERE producent_name = '".$this->producent_name."'";
                $aResult = ConnectDB::subQuery($sql,'one');
                if(intval($aResult)) {
                    return $aResult;
                } else {
                    return false;
                }
            } catch (PDOException $e){
		return false;
	    }
        } else {
            return false;
        }
    }

    public function delete() {

	if($this->vat_id) {
	    $sql = "DELETE FROM shop_producents WHERE producent_id = ".$this->producent_id;
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
