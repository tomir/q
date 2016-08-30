<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdminUsers
 *
 * @author tomi_weber
 */
class Admin_Users {

    protected $user_id;

    protected $user_login;

    protected $user_pass;

    protected $user_email;

    protected $user_first_name;

    protected $user_last_name;

    protected $user_phone;

    protected $user_street;

    protected $user_adress;

    protected $user_adress2;

    protected $user_city;

    protected $id_country;

    protected $country;

    protected $user_zip;

    public function __construct($userId = 0) {

	if ($userId > 0){
	    try {

		$sql = "SELECT *
				FROM shop_users LEFT JOIN shop_country USING (id_country)
				WHERE user_id = ".$userId."
				";

		$aResult = ConnectDB::subQuery($sql);
		if(!is_array($aResult)){
			return false;
		}
		foreach ($aResult as $row) {
		    $this -> user_id		= $row['user_id'];
		    $this -> user_login		= $row['user_login'];
		    $this -> user_email		= $row['user_email'];
		    $this -> user_first_name	= $row['user_first_name'];
		    $this -> user_last_name	= $row['user_last_name'];
		    $this -> user_phone		= $row['user_phone'];
		    $this -> user_street	= $row['user_street'];
		    $this -> user_adress	= $row['user_adress'];
		    $this -> user_adress2	= $row['user_adress2'];
		    $this -> user_city		= $row['user_city'];
		    $this -> id_country		= $row['id_country'];
		    $this -> country		= $row['country'];
		    $this -> user_zip		= $row['user_zip'];

		}
	    }catch (PDOException $e){
		//echo "Błąd nie można utworzyć obiektu material.";
		return false;
	    }
	}
	else {
	    $this -> user_id		= 0;
	    $this -> user_login		= '';
	    $this -> user_email		= '';
	    $this -> user_first_name	= '';
	    $this -> user_last_name	= '';
	    $this -> user_phone		= '';
	    $this -> user_street	= '';
	    $this -> user_adress	= '';
	    $this -> user_adress2	= '';
	    $this -> user_city		= '';
	    $this -> id_country		= 0;
	    $this -> country		= '';
	    $this -> user_zip		= '';
	}
    }

    public function getId() {
	return $this->user_id;
    }

    public function getLogin() {
	return $this->user_login;
    }

    public function getPass() {
	return $this->user_pass;
    }

    public function getEmail() {
	return $this->user_email;
    }

    public function getFirstName() {
	return $this->user_first_name;
    }

    public function getLastName() {
	return $this->user_last_name;
    }

    public function getPhone() {
	return $this->user_phone;
    }

    public function getStreet() {
	return $this->user_street;
    }

    public function getAdress() {
	return $this->user_adress;
    }

    public function getAdress2() {
	return $this->user_adress2;
    }

    public function getCity() {
	return $this->user_city;
    }

    public function getIdCountry() {
	return $this->id_country;
    }

    public function getCountry() {
	return $this->country;
    }

    public function getZip() {
	return $this->user_zip;
    }

    public function getUserList($start, $limit = 15, $admin = "0") {

	$aResult = array();
	$sql = "SELECT * FROM shop_users LEFT JOIN shop_country USING (id_country) WHERE user_admin = ".$admin." ORDER BY user_add_date DESC LIMIT ".$start.", ".$limit;
	
	try {
	    if($aResult = ConnectDB::subQuery($sql)) {
		return $aResult;
	    } else return false;
	} catch (PDOException $e){

	    return false;
	}
    }

    public function getCountryList() {

	$aResult = array();
	$sql = "SELECT * FROM shop_country ORDER BY country ASC";
	try {
	    if($aResult = ConnectDB::subQuery($sql)) {
		return $aResult;
	    } else return false;
	} catch (PDOException $e){

	    return false;
	}
    }

    public function save($aData) {

	if(($aData['user_pass'] != "" && $aData['user_id'] > 0) || $aData['user_id'] == 0) {

		$salt = substr(md5($aData['user_email'].$aData['user_last_name']), 0,4);
		$pass = md5($salt.$aData['user_pass']);

		$aData['user_pass'] = $pass;
		$aData['user_salt'] = $salt;

	}
	try {
		if($aData['user_id'] != 0)
			$res = ConnectDB::subAutoExec ("shop_users", $aData, "UPDATE", "user_id = ".$aData['user_id']);
		else
			$res = ConnectDB::subAutoExec ("shop_users", $aData, "INSERT");

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

	if($this->user_id) {
	    $sql = "DELETE FROM shop_users WHERE user_id = ".$this->user_id;
	    try {
		if(ConnectDB::subExec($sql))
		    return true;
		else return false;

	    }catch (PDOException $e){

		return false;
	    }
	} else return false;
    }
	public function searchUser($search) {

		$aResult = array();
		$sql = "SELECT * FROM shop_users LEFT JOIN shop_country USING (id_country) WHERE (user_first_name LIKE '%".$search."%' AND user_last_name LIKE '%".$search."%' ) OR user_last_name LIKE '%".$search."%' ORDER BY user_last_name DESC";

		try {
			if($aResult = ConnectDB::subQuery($sql)) {
			return $aResult;
			} else return false;
		} catch (PDOException $e){

			return false;
		}
	}

}
?>
