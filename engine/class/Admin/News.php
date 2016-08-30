<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdminNews
 *
 * @author tomi_weber
 */
class Admin_News {

    protected $news_id;

    protected $news_title;

    protected $news_description;

    protected $news_active;


    public function __construct($newsId = 0) {

	if ($newsId > 0){
	    try {

		$sql = "SELECT *
				FROM shop_news
				WHERE news_id = ".$newsId."
				";

		$aResult = ConnectDB::subQuery($sql);
		if(!is_array($aResult)){
			return false;
		}
		foreach ($aResult as $row) {
		    $this -> news_id		= $row['news_id'];
		    $this -> news_title		= $row['news_title'];
		    $this -> news_active	= $row['news_active'];
		    $this -> news_description	= $row['news_description'];
		}
	    }catch (PDOException $e){
		//echo "Błąd nie można utworzyć obiektu material.";
		return false;
	    }
	}
	else {
	    $this -> news_id		= 0;
	    $this -> news_title		= '';
	    $this -> news_active	= 0;
	    $this -> news_description	= '';
	}
    }

    public function setId($id) {
	$this->news_id = $id;
    }

    public function setTitle($val) {
	$this->news_title = $val;
    }

    public function setDescription($val) {
	$this->news_description = $val;
    }

    public function setActive($val) {
	$this->news_active = $val;
    }

     public function getId() {
	return $this->news_id;;
    }

    public function getTitle() {
	return $this->news_title;;
    }

    public function getDescription() {
	return $this->news_description;;
    }

    public function getActive() {
	return $this->news_active;;
    }

    public function getNewsList($start, $limit = 15) {

	$aResult = array();
	$sql = "SELECT * FROM shop_news ORDER BY news_add_date DESC LIMIT ".$start.", ".$limit;
	try {
	    if($aResult = ConnectDB::subQuery($sql)) {
		return $aResult;
	    } else return false;
	} catch (PDOException $e){

	    return false;
	}
    }

    public function save() {

	if($this->p_id)
	    $sql = "UPDATE shop_news SET	news_title =	'".$this->news_title."',
						news_active =	".$this->news_active.",
						news_description = '".$this->news_description."',
		    WHERE news_id = ".$this->news_id;
	else
	    $sql = "INSERT INTO shop_news (news_title,news_active,news_description,news_add_date)
		    VALUES ('".$this->news_title."',".$this->news_active.",'".$this->news_description."',now())";

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
	    $sql = "DELETE FROM shop_news WHERE news_id = ".$this->news_id;
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
