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
class Admin_Banner {

    public $data;
	public $id;
	
    public function __construct($bannerId = 0) {

		if ($newsId > 0){
			try {

			$sql = "SELECT b.*, p.p_name, p.link, bp.*
					FROM shop_banner b
					LEFT JOIN shop_product p ON p.p_i = b.p_id
					LEFT JOIN shop_banner_place bp ON bp.place_id = b.place_id
					WHERE b.banner_id = ".$bannerId."
					";

			$aResult = ConnectDB::subQuery($sql, 'fetch');
			if(!is_array($aResult)){
				return false;
			}

			$this->data = $aResult;
			$this->id = $bannerId;
			
			}catch (PDOException $e){
				//echo "Błąd nie można utworzyć obiektu material.";
				return false;
			}
		}
    }

    public function getBannerList($start, $limit = 15) {

		$aResult = array();
		$sql = "SELECT b.*, p.p_name, p.link, bp.* FROM shop_banner b
				LEFT JOIN shop_product p ON p.p_i = b.p_id
				LEFT JOIN shop_banner_place bp ON bp.place_id = b.place_id
				WHERE 1 
				ORDER BY banner_date_to 
				DESC LIMIT ".$start.", ".$limit;
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
			if($aData['banner_id'] != 0)
				$res = ConnectDB::subAutoExec ( 'shop_banner', $aData, "UPDATE", "banner_id = ".$aData['banner_id']);
			else
				$res = ConnectDB::subAutoExec ( 'shop_banner', $aData, "INSERT");

			if($res)
				return $res;
			else
				return false;
		} catch (PDOException $e){

			return false;
		}

    }

    public function delete() {

		if($this->vat_id) {
			$sql = "DELETE FROM shop_banner WHERE banner_id = ".$this->id;
			try {
			if(ConnectDB::subExec($sql))
				return true;
			else return false;

			}catch (PDOException $e){

			return false;
			}
		} else return false;
    }
	
	 public function getBannerPlace($bannerPlace = 0) {

		if ($bannerPlace > 0){
			try {

			$sql = "SELECT b.*
					FROM shop_banner_place b
					WHERE b.place_id = ".$bannerPlace."
					";

			$aResult = ConnectDB::subQuery($sql, 'fetch');
			if(!is_array($aResult)){
				return false;
			}

			$this->data = $aResult;
			$this->id = $bannerId;
			
			}catch (PDOException $e){
				//echo "Błąd nie można utworzyć obiektu material.";
				return false;
			}
		}
    }
	
	public function getBannerPlaceList() {

		$aResult = array();
		$sql = "SELECT b.* FROM shop_banner_place b
				WHERE 1";
		
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
			return $aResult;
			} else return false;
		} catch (PDOException $e){

			return false;
		}
    }
	
	public function getBannerPlaceListSelect() {
		
		$aResult = array();
		$aResult = $this->getBannerPlaceList();
		
		foreach($aResult as $row) {
			$aNew[$row['place_id']] = $row['place_name'];
		}
		
		return $aNew;
	}

}
