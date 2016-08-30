<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdminCategory
 *
 * @author tomi_weber
 * @todo jasy
 * 
 */


class Admin_Category {

	protected $cat_id;
	protected $cat_name;
	protected $cat_url_name;
	protected $cat_parent;
	protected $cat_order;
	protected $cat_seo_title;
	protected $cat_seo_desc;
	
	public $_tree;


	public function __construct($catId = 0) {

		if ($catId > 0) {
			try {

				$sql = "SELECT *
						FROM shop_categories
						WHERE cat_id = ".$catId."
						";

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> cat_id			= $row['cat_id'];
					$this -> cat_name			= $row['cat_name'];
					$this -> cat_url_name		= $row['cat_url_name'];
					$this -> cat_parent		= $row['cat_parent'];
					$this -> cat_order			= $row['cat_order'];
					$this -> cat_active			= $row['cat_active'];
					$this -> cat_seo_title		= $row['cat_seo_title'];
					$this -> cat_seo_desc		= $row['cat_seo_desc'];
				}
			} catch (Exception $e){
				Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
				return false;
			}
		}
	}

	public function getCatId() {
		return $this -> cat_id;
	}
	
	public function setCatId($val) {
		$this -> cat_id = $val;
	}

	public function getCatName() {
		return $this -> cat_name;
	}
	
	public function getCatUrlName() {
		return $this -> cat_url_name;
	}
	
	public function getCatParent() {
		return $this -> cat_parent;
	}
	
	public function getCatOrder() {
		return $this -> cat_order;
	}
	
	public function getCatActive() {
		return $this -> cat_active;
	}
	
	public function getCatSeoTitle() {
		return $this -> cat_seo_title;
	}
	
	public function getCatSeoDesc() {
		return $this -> cat_seo_desc;
	}

	public function getMainCategories() {

		$sql = "SELECT * FROM shop_categories WHERE cat_parent = 0 ORDER BY cat_order ASC";

		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				return $aResult;
			} else return false;
		} catch (Exception $e){

			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}

	}

	public function getCategories($start, $limit = 15, $parent = 0) {
		
		$tmp = array();
		$aResult = array();
		$limit_sql = "";
		
		if(!$parent)
			$limit_sql = " LIMIT ".$start.", ".$limit;
		
		$sql = "SELECT * FROM shop_categories WHERE cat_parent = ".$parent." ORDER BY cat_order ASC".$limit_sql;
		
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				if(is_array($aResult)) {
					foreach($aResult as $row) {
						$tmp[$row['cat_id']] = $row;
						$tmp[$row['cat_id']]['children'] = $this->getCategories(0,0,$row['cat_id']);
					}
				} else return false;
			} else return false;
		} catch (Exception $e){
			
			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}

		return $tmp;
	}

	public function getProductCategories($p_id = 0) {

		$sql = "SELECT DISTINCT cat_id FROM shop_categories_products WHERE p_id = ".$p_id;

		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				if(is_array($aResult)) {
					$result2 = array();
					foreach($aResult as $row) {
						$result2[$row['cat_id']] = $row['cat_id'];
					}
				} else return false;
			} else return false;
		} catch (Exception $e){

			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
		return $result2;
	}

	public function selectAjaxCategory($active, $p_id) {

		try {
			if(!$active)
				$res = ConnectDB::subExec ("DELETE FROM shop_categories_products WHERE cat_id = ".$this -> cat_id." AND p_id = ".$p_id);
			else {
				$aData['p_id'] = $p_id;
				$aData['cat_id'] = $this->cat_id;
				$res = ConnectDB::subAutoExec ("shop_categories_products", $aData, "INSERT");
			}
			if($res)
				return $res;
			else
				return false;
		} catch (Exception $e){
			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}
	
	public function selectMassAjaxCategory($p_id, $cat_id) {

		try {
			
			ConnectDB::subExec("DELETE FROM shop_categories_products WHERE p_id = ".$p_id);
		
			foreach($cat_id as $cat) {
				$aData['p_id'] = $p_id;
				$aData['cat_id'] = $cat;
				$res = ConnectDB::subAutoExec ("shop_categories_products", $aData, "INSERT");
			}
			
			if($res)
				return $res;
			else
				return false;
		} catch (Exception $e){
			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}

	public function save($aData) {
	
		try {
			if($aData['cat_id'] != 0)
				$res = ConnectDB::subAutoExec ("shop_categories", $aData, "UPDATE", "cat_id = ".$aData['cat_id']);
			else
				$res = ConnectDB::subAutoExec ("shop_categories", $aData, "INSERT");

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

		if($this->cat_id) {
			$sql = "DELETE FROM shop_categories WHERE cat_id = ".$this -> cat_id;
			
			try {
				if(ConnectDB::subExec($sql))
					return true;
				else return false;

			} catch (Exception $e) {
				Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
				return false;
			}
		} else return false;
	}
	
	public function sortAjax($order, $parent = 0) {
		
		if($order) {
			$aOrder = explode(",",$order);
			$i = 1;
			
			if($parent) {
				$where = " AND cat_parent = ".$parent;
			}
			$pdo = new ConnectDB() ;
			foreach($aOrder as $row) {
				$sql = "UPDATE shop_categories SET cat_order = ".$i." WHERE cat_id = ".$row.$where;
				ConnectDB::subExec($sql);
				$i++;
			}
		}
	}
	
	public function getCategoryList($filtr, $child = false) {
		
		if($child) {
			$sql = "SELECT	a.cat_name AS parent_name,
							a.cat_url_name AS parent_url_name, 
							a.cat_id AS parent_id,
							b.cat_name AS child_name, 
							b.cat_id AS child_id, 
							b.cat_url_name AS child_url_name
					FROM shop_categories as a LEFT JOIN shop_categories as b on a.cat_id = b.cat_parent
					WHERE 1";
			
	
			$sql .= " ORDER by a.cat_order, b.cat_order";
			
		} else {
			$sql = "SELECT	*
					FROM shop_categories as a
					WHERE 1";

			$sql .= " ORDER by a.cat_order";
		} 
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
	
	public function getTree($root_id = 0, $filtr = NULL) {

		if (isset($this->_tree) && is_array($this->_tree) && count($this->_tree) > 0)
			$all = $this->_tree;
		else
			$all = $this->_tree = $this->getCategoryList($filtr);

		$tree = $this->_getTree($root_id, $all, 0);

		return $tree;
	}

	private function _getTree($id, $tree, $level = 0) {
		foreach ($tree as $item) {
			if ($item['cat_parent'] == $id) {
				$temp_array[] = array(
					'cat_id' => $item['cat_id'],
					'cat_name' => $item['cat_name'],
					'cat_parent' => $item['cat_parent'],
					'cat_order' => $item['cat_order'],
					'cat_url_name' => $item['cat_url_name'],
					'cat_short' => $item['cat_short'],
					'cat_active' => $item['cat_active'],
					'level' => $level
				);
			}
		}

		if (isset($temp_array)) {
			for ($i = 0; $i < sizeof($temp_array); $i++) {
				$element = $temp_array[$i];
				$array[$element['cat_id']] = $element;
				$array[$element['cat_id']]['children'] = $this->_getTree($element['cat_id'], $tree, $level + 1);
			}
		}
		return (isset($array) ? $array : false);
	}
	
	/**
	 * Zwraca plaskie drzewko gotowe do wygenerowania listy
	 *
	 * @param int $parent_id
	 * @return array
	 */
	public function getTreeLI($parent_id = 0) {
		$all = $this->getCategoryList(NULL);

		$flat = $this->_getTreeLI($all, $parent_id);

		return $flat;
	}

	/**
	 * Funkcja wewnetrzna
	 *
	 * @param array $tree
	 * @param int $id
	 * @param int $level
	 * @return array
	 */
	private function _getTreeLI($tree, $id=0, $level = 0) {
		$ret = array();

		foreach ($tree as $item) {
			if ($item['cat_parent'] == $id) {

				$indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);

				$temp = $item;
				$temp['poziom'] = $level;
				$temp['wciecie'] = $indent;

				$ret[] = $temp;
				$ret = array_merge($ret, $this->_getTreeLI($tree, $item['cat_id'], $level + 1));
			}
		}
		return $ret;
	}

}
?>
