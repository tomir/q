<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdminAds
 *
 * @author tomi_weber
 */
class Admin_Ads {

    protected $ads_id;

    protected $ads_name;

    protected $file = array();

    public function __construct($adsId = 0) {

	if ($adsId > 0){
	    try {

		$sql = "SELECT *
				FROM shop_ads
				WHERE ads_id = ".$adsId."
				";

		$aResult = ConnectDB::subQuery($sql);
		if(!is_array($aResult)){
			return false;
		}
		foreach ($aResult as $row) {
		    $this -> ads_id		= $row['ads_id'];
		    $this -> ads_name		= $row['ads_name'];
		}
	    }catch (PDOException $e){
		//echo "Błąd nie można utworzyć obiektu material.";
		return false;
	    }
	}
	else {
	    $this -> ads_id		= 0;
	    $this -> ads_name		= '';
	}
    }

    public function setId($id) {
	$this->ads_id = $id;
    }

    public function setName($val) {
	$this->ads_name = $val;
    }

    public function setFile($val) {
	$this->file = $val;
    }

    public function getid() {
	return $this->ads_id;
    }

    public function getName() {
	return $this->ads_name;
    }

    public function getAdsList($start, $limit = 15) {

	$aResult = array();
	$sql = "SELECT * FROM shop_ads ORDER BY ads_order DESC LIMIT ".$start.", ".$limit;
	try {
	    if($aResult = ConnectDB::subQuery($sql)) {
		return $aResult;
	    } else return false;
	} catch (PDOException $e){

	    return false;
	}
    }

    public function addFile() {

	if ($this->file['name']) {

	    $file_name = $this->file['name'];
	    $rozszerzenie = explode(".",$file_name);
	    $cnt = count($rozszerzenie);
	    $file_name = $this->ads_id.".".$rozszerzenie[$cnt-1];

	    $uploadFile = MyConfig::getValue("serverPatch")."ads/".$file_name;
	    if (move_uploaded_file ($_FILES['Filedata']['tmp_name'], $uploadFile))
		return true;
	    else return false;

	} else return false;
    }

    public function sortAjax($order) {
	if($order) {
	    $aOrder = explode(",",$order);
	    $i = 1;

	    foreach($aOrder as $row) {
		$sql = "UPDATE shop_ads SET ads_order = ".$i." WHERE ads_id = ".$row;
		ConnectDB::subExec($sql);
		$i++;
	    }
	}
    }

    public function save() {

	if($this->ads_id)
	    $sql = "UPDATE shop_ads SET ads_name = ".$this->ads_name." WHERE ads_id = ".$this->ads_id;
	else
	    $sql = "INSERT INTO shop_ads (ads_name, ads_order, ads_add_date) VALUES ('".$this->ads_name."', 0, now())";

	try {
	    $id = ConnectDB::subExec($sql);
	    if(is_int($id)) {
		$this -> ads_id = $id;
		if(is_array($this -> file)) {
		    if($this -> addFile())
			return true;
		    else return false;
		}
	    } elseif($id) {
		if(is_array($this -> file)) {
		    if($this -> addFile())
			return true;
		    else return false;
		}
	    }

	}catch (PDOException $e){

	    return false;
	}

    }

    public function delete() {

	if($this->ads_id) {
	    $sql = "DELETE FROM shop_ads WHERE ads_id = ".$this->ads_id;
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
