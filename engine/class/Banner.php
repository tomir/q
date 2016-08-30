<?php

class Banner {
	
    public function getBanner($id, $filtr) {
		
		$sql = "SELECT * FROM shop_banner b
					LEFT JOIN shop_product p ON p.p_i = b.p_id
					LEFT JOIN shop_banner_place bp ON bp.place_id = b.place_id
					WHERE 1 AND b.banner_id = ".$id." ";
		$sql .= $this->getFiltr($filtr);
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'fetch');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function getBannerList($filtr, $limit = 0) {

		$sql = "SELECT b.*, bp.*  FROM shop_banner b
					LEFT JOIN shop_banner_place bp ON bp.place_id = b.place_id
					WHERE 1 ";
		$sql .= $this->getFiltr($filtr);

		if($limit > 0)
			$sql .= " LIMIT ".$limit;

		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function getBannerListCount($filtr) {
		
		$sql = "SELECT COUNT(p.banner_id) as ile FROM shop_banner b
					LEFT JOIN shop_banner_place bp ON bp.place_id = b.place_id
					WHERE 1 ";
		$sql .= $this->getFiltr($filtr);
		
		try {
			$ile = ConnectDB::subQuery($sql, 'one');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $ile;
	}
	
	public function getFiltr($filtr) {
		
		$sql = "";
		$this->filtr = $filtr;
		
		if(isset($this->filtr['active']) && is_numeric($this->filtr['active'])) {
			$sql .= " AND b.banner_active = ".$this->filtr['active']." ";
		}
		
		if(isset($this->filtr['action']) && $this->filtr['action'] != '') {
			$sql .= " AND bp.place_action = '".$this->filtr['action']."' ";
		}
		
		if(isset($this->filtr['action2']) && $this->filtr['action2'] != '') {
			$sql .= " AND bp.place_action2 = '".$this->filtr['action2']."' ";
		}
		
		if(isset($this->filtr['place']) && $this->filtr['place'] != '') {
			$sql .= " AND bp.place_name = '".$this->filtr['place']."' ";
		}
		
		if(isset($this->filtr['place_id']) && is_numeric($this->filtr['place_id'])) {
			$sql .= " AND b.place_id = '".$this->filtr['place_id']."' ";
		}
		
		return $sql;
	}
}