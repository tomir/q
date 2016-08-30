<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdminProduct
 *
 * @author tomi_weber
 */
class Admin_Product {

    protected $p_id;

    protected $p_name;

    protected $p_name_kat;

    protected $producent_id;

    protected $vat_id;

    protected $p_description;

    protected $p_description_short;

    protected $p_price;

    protected $p_price_gross;

    protected $p_items;

    protected $p_promo;

    protected $p_promo_price;

    protected $p_hit;

    protected $p_new;

    protected $p_active;

    protected $file_id;

    protected $file_name;

    protected $file;


    public function __construct($pId = 0) {

	if ($pId > 0){
	    try {

		$sql = "SELECT p.*, hi.import_category
				FROM " . MyConfig::getValue("__produkty") . " p
				LEFT JOIN shop_hurtownie_import hi ON (p.p_id = hi.product_id)
				WHERE p_id = ".$pId."
				";
		
		$aResult = ConnectDB::subQuery($sql);
		if(!is_array($aResult)){
			return false;
		}
		foreach ($aResult as $row) {
		    $this -> p_id				= $row['p_id'];
		    $this -> p_name				= $row['p_name'];
		    $this -> p_name_kat			= $row['p_name_kat'];
		    $this -> producent_id		= $row['producent_id'];
		    $this -> vat_id				= $row['vat_id'];
		    $this -> p_description			= $row['p_description'];
		    $this -> p_description_short	= $row['p_description_short'];
		    $this -> p_price			= $row['p_price'];
		    $this -> p_price_gross	= $row['p_price_gross'];
		    $this -> p_magazine		= $row['p_magazine'];
		    $this -> p_promo		= $row['p_flag_promo'];
		    $this -> p_active		= $row['p_active'];
		    $this -> p_promo_price		= $row['p_promo_price'];
		    $this -> p_hit		= $row['p_flag_hit'];
		    $this -> kat_org		= $row['import_category'];
		}
	    }catch (PDOException $e){
		echo "Błąd nie można utworzyć obiektu material.";
		return false;
	    }
	}
    }

    public function getId() {
	return $this->p_id;
    }

    public function getCatName() {
	return $this->cat_name;
    }
    
    public function getName() {
	return $this->p_name;
    }

    public function getNameKat() {
	return $this->p_name_kat;
    }

    public function getProducentName() {
	return $this->producent_name;
    }

    public function getProducentId() {
	return $this->producent_id;
    }

    public function getVatId() {
	return $this->vat_id;
    }

    public function getDescriptionShort() {
	return $this->p_description_short;
    }

    public function getDescription() {
	return $this->p_description;
    }

    public function getPrice() {
	return $this->p_price;
    }

    public function getPriceGross() {
	return $this->p_price_gross;
    }

    public function getItems() {
	return $this->p_magazine;
    }

    public function getPromo() {
	return $this->p_promo;
    }

    public function getActive() {
	return $this->p_active;
    }

    public function getHit() {
	return $this->p_hit;
    }

    public function getKatOrg() {
	return $this->kat_org;
    }

    public function getProductList($filtr, $start, $limit = 15) {

		$aResult = array();
		$sql = "SELECT p.*, pr.producent_name, vl.vat_level, hi.import_category, hi.import_code, hi.import_ean,
					(SELECT cat_name FROM shop_categories tmp_cat LEFT JOIN shop_categories_products tmp_cat_p ON (tmp_cat.cat_id = tmp_cat_p.cat_id) WHERE tmp_cat_p.p_id = p.p_id LIMIT 1) as cat_name
					FROM " . MyConfig::getValue("__produkty") . " p
					LEFT JOIN shop_categories_products pcp ON p.p_id = pcp.p_id
					LEFT JOIN shop_categories pc ON pc.cat_id = pcp.cat_id 
					LEFT JOIN shop_producents pr ON (p.producent_id = pr.producent_id)
					LEFT JOIN shop_hurtownie_import hi ON (p.p_id = hi.product_id)
					LEFT JOIN shop_vat_levels vl ON (p.vat_id = vl.vat_id)
					WHERE 1";
		$sql .= $this->getFiltr($filtr);		
		$sql .= " GROUP by p.p_id ";
		$sql .= " ORDER BY p_edit_date DESC LIMIT ".$start.", ".$limit;
		
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
			return $aResult;
			} else return false;
		} catch (Exception $e){
			Log::SLog($e->getTraceAsString());
			return false;
		}
    }
	
	public function getFiltr($filtr) {
		
		$sql = "";
		$this->filtr = $filtr;
		
		if(isset($this->filtr['active']) && is_numeric($this->filtr['active'])) {
			$sql .= " AND p.p_active = 1 AND (p.p_magazine > 0 OR (p.p_magazine = 0 AND DATE_SUB(CURDATE(), INTERVAL 31 DAY) <= p.p_edit_date)) ";
		}
		
		if(isset($this->filtr['noactive']) && is_numeric($this->filtr['noactive'])) {
			$sql .= " AND p.p_active = 0 ";
		}
		
		if(is_array($this->filtr['cat_id']) && count($this->filtr['cat_id']) > 0) {
			$sql .= " AND pc.cat_id IN (".implode(",",$this->filtr['cat_id']).") ";
		}
		
		if (isset($filtr['sphinx']) && is_array($filtr['sphinx']) && count($filtr['sphinx']) > 0) {
			$prodSQL = implode(',', $filtr['sphinx']);
			$sql .= " AND p.p_id IN ($prodSQL) ";
		}
		
		if(isset($this->filtr['cat_id']) && is_numeric($this->filtr['cat_id']) && $this->filtr['cat_id'] > 0) {
			
			$objCategory = new Category();
			$drzewko = $objCategory->getAllChildren($filtr['cat_id']);

			$drzewko[] = $filtr['cat_id'];
			$drzewko = implode(',', $drzewko);
			if( substr($drzewko, strlen($drzewko)-1, 1)==',' )
				$drzewko = substr($drzewko, 0, strlen($drzewko)-1);
			
			$sql.= " AND pc.cat_id IN (".$drzewko.") ";

		}
		
		if(isset($this->filtr['promocja']) && is_numeric($this->filtr['promocja'])) {
			$sql .= " AND pf.flag_id = ".$this->filtr['promocja']." ";
		}
		
		if(isset($this->filtr['bestseller']) && is_numeric($this->filtr['bestseller'])) {
			$sql .= " AND pf.flag_id = ".$this->filtr['bestseller']." ";
		}
		
		if(isset($this->filtr['nowosc']) && is_numeric($this->filtr['nowosc'])) {
			$sql .= " AND pf.flag_id = ".$this->filtr['nowosc']." ";
		}
		
		if(is_array($this->filtr['producent_id']) && count($this->filtr['producent_id']) > 0) {
			$sql .= " AND p.producent_id IN (".implode(",",$this->filtr['producent_id']).") ";
		}
		
		if(isset($this->filtr['producent_id']) && is_numeric($this->filtr['producent_id']) && $this->filtr['producent_id'] > 0) {
			$sql .= " AND p.producent_id = ".$this->filtr['producent_id']." ";
		}
		
		if(isset($this->filtr['product_not']) && is_numeric($this->filtr['product_not'])) {
			$sql .= " AND p.p_id != ".$this->filtr['product_not']." ";
		}
		
		
		return $sql;
	}
	
	public function getProductCount($filtr = null) {

		$aResult = array();
		$sql = "SELECT count(*) as ile
					FROM " . MyConfig::getValue("__produkty") . " p
					LEFT JOIN shop_categories_products pcp ON p.p_id = pcp.p_id
					LEFT JOIN shop_categories pc ON pc.cat_id = pcp.cat_id 
					WHERE 1 ";
		
		$sql .= $this->getFiltr($filtr);
		//$sql .= " GROUP by p.p_id WITH ROLLUP";
		//echo $sql;
		try {
			if($aResult = ConnectDB::subQuery($sql, 'one')) {
				return $aResult;
			} else return false;
		} catch (Exception $e){
			echo $e->getMessage();
			return false;
		}
    }
    
    public function saveFile($fData) {
	
	try {
		$fData['m_filename'] = "..".$fData['m_filename'];
		$res = ConnectDB::subAutoExec ( MyConfig::getValue("__produkty_pliki"), $fData, "INSERT");

		if($res)
			return $res;
		else
			return false;
	} catch (PDOException $e){

	    return false;
	}
    }
    
    public function saveFileUrl($fData) {
        try {
            $image = file_get_contents($fData["url"]);
            $res = ConnectDB::subAutoExec ( MyConfig::getValue("__produkty_pliki"), $fData, "INSERT");
            file_put_contents(MyConfig::getValue('serverPatch').'temp/product_images/'.$res.'.jpg',$image);
        } catch (PDOException $e){
	    return false;
	}
    }
    
    public function getFileList() {
		
		$aResult = array();
		$sql = "SELECT * FROM " . MyConfig::getValue("__produkty_pliki") . " WHERE p_id = ".$this->p_id." ORDER BY m_order ASC";
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
			return $aResult;
			} else return false;
		} catch (Exception $e){
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
    }

    public function addFile() {

	if ($this->file['name']) {

	    $file_name = $this->file['name'];
	    $rozszerzenie = explode(".",$file_name);
	    $cnt = count($rozszerzenie);
	    $file_name = $this->file_id.".".$rozszerzenie[$cnt-1];

	    $uploadFile = MyConfig::getValue("serverPatch")."files/".$file_name;
	    if (move_uploaded_file ($_FILES['Filedata']['tmp_name'], $uploadFile))
		return true;
	    else return false;

	} else return false;
    }

    public function save($aData) {

		try {
			if($aData['p_id'] != 0)
				$res = ConnectDB::subAutoExec ( MyConfig::getValue("__produkty"), $aData, "UPDATE", "p_id = ".$aData['p_id']);
			else
				$res = ConnectDB::subAutoExec ( MyConfig::getValue("__produkty"), $aData, "INSERT");

			if($res)
				return $res;
			else
				return false;
		} catch (PDOException $e){

			return false;
		}
    }
	
	public function getFlagList() {
		try {
			
			$sql = "SELECT pf.added_date, f.* FROM " . MyConfig::getValue("__produkty_flagi") . " pf 
					LEFT JOIN " . MyConfig::getValue("__flagi") . " f ON f.f_id = pf.flag_id
					WHERE pf.p_id = ".$this->p_id;

			$aResult = ConnectDB::subQuery($sql);
			if(!is_array($aResult) || empty($aResult)){
				return false;
			}
			
			return $aResult;
		}catch (Exception $e){
			echo "Błąd nie można utworzyć obiektu ".__CLASS__;
			return false;
		}
	}
	
	 public function addFlag($aData) {

		try {
			$res = ConnectDB::subAutoExec ( MyConfig::getValue("__produkty_flagi"), $aData, "INSERT");

			if($res)
				return $res;
			else
				return false;
		} catch (PDOException $e){
			echo $e->getMessage();
			return false;
		}
    }
	
	public function selectAjaxMainPhoto($active, $m_id) {

		try {
		
			ConnectDB::subExec("UPDATE shop_product_media SET m_main = ".$active." WHERE m_i = ".$m_id);
			return true;
			
		} catch (Exception $e){
			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}
	
	public function deleteFile($id, $m_id) {

		try {
		
			ConnectDB::subExec("DELETE FROM ".MyConfig::getValue("__produkty_pliki")." WHERE m_i = ".$m_id);
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/".$m_id.".jpg");
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/120x100/".$m_id.".jpg");
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/158x132/".$m_id.".jpg");
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/250x240/".$m_id.".jpg");
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/51x51/".$m_id.".jpg");
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/70x70/".$m_id.".jpg");
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/77x77/".$m_id.".jpg");
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/800x800/".$m_id.".jpg");
			unlink(MyConfig::getValue("serverPatch")."temp/product_images/98x98/".$m_id.".jpg");
			
			return true;
			
		} catch (Exception $e){
			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}
}
?>
