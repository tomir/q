<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author tcisowski
 */
class Category {

	public function getCategory($id) {
		
		$sql = "SELECT	a.*
				FROM shop_categories AS a
				WHERE 1 AND cat_id = ".$id;
		$sql .= $this->getFiltr($filtr);
		$sql .= " ORDER by a.cat_order";
		
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

	public function getCategoryList($filtr) {
		
		$sql = "SELECT	a.*
				FROM shop_categories AS a
				WHERE 1";
		$sql .= $this->getFiltr($filtr);
		$sql .= " ORDER by a.cat_order";
		
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
	
	public function getFiltr($filtr) {
		
		$sql = "";
		$this->filtr = $filtr;
		
		if(isset($this->filtr['cat_parent']) && is_numeric($this->filtr['cat_parent'])) {
			$sql .= " AND a.cat_parent = ".$this->filtr['cat_parent']." ";
		}
		
		if(isset($this->filtr['active']) && is_numeric($this->filtr['active'])) {
			$sql .= " AND a.cat_active = ".$this->filtr['active']." ";
		}
	
		return $sql;
	}
	
	public function getAllChildren($parent_id, $filtr = array()) {
		
		if (isset($this->_tree) && is_array($this->_tree) && count($this->_tree) > 0)
			$all = $this->_tree;
		else
			$all = $this->_tree = $this->getCategoryList($filtr);
		
		$flat = $this->_getFlatTree($all, $parent_id);

		return $flat;
	}

	/**
	 * Funkcja wewnetrzna, generuje rekurencyjnie liste kategorii podrzednych wzgledem podanej
	 *
	 * @param array $tree
	 * @param int $id
	 * @return array
	 */
	private function _getFlatTree($tree, $id=0) {
		$ret = array();

		foreach ($tree as $item) {
			if ($item['cat_parent'] == $id) {
				$ret[] = $item['cat_id'];
				$ret = array_merge($ret, $this->_getFlatTree($tree, $item['cat_id']));
			}
		}
		return $ret;
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
					'cat_ilosc' => $item['cat_ilosc'],
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
	 * zwraca sciezke do podanej kategorii w postaci tablicy
	 * jezeli nie podalismy kategorii to zwraca pusta tablice
	 * @param int $id id kategorii
	 * @return array
	 */
	function getPath($id) {

		if (isset($this->_tree) && is_array($this->_tree) && count($this->_tree) > 0)
			$all = $this->_tree;
		else
			$all = $this->_tree = self::getCategoryList(array('cat_active' => 1));

		$path = $this->_getPath($all, $id);

		return array_reverse($path);
	}

	/**
	 * Funkcja wewnetrzna, generuje rekurencyjnie drzewko
	 *
	 * @param array $tree
	 * @param int $id
	 * @return array
	 */
	private function _getPath($tree, $id) {
		$ret = array();

		foreach ($tree as $item) {
			if ($item['cat_id'] == $id) {
				$item['link'] = $item['cat_url_name'];
				$item['nazwa'] = $item['cat_name'];
				$ret[] = $item;
				if ($item['cat_parent'] > 0) {
					$ret = array_merge($ret, $this->_getPath($tree, $item['cat_parent']));
				}
			}
		}

		return $ret;
	}
	
}
?>
