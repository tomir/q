<?php

namespace Admin\Supplier;

/**
 * Description of Mapping
 *
 * @author tomi
 */
class Mapping {

	public function getAll($filtr) {

		$aResult = array();
		$sql = "SELECT ci.*, c.cat_name as cat_name, i.name as import_name FROM shop_hurtownie_import_category_mapping ci "
				. "LEFT JOIN shop_categories c ON ci.category_id = c.cat_id "
				. "LEFT JOIN shop_hurtownie_import_category i ON ci.shop_hurtownie_import_category_id = i.id "
				. "WHERE 1 ";
		$sql .= $this->getFilter($filtr);
	
		try {
			if ($aResult = \ConnectDB::subQuery($sql)) {
				return $aResult;
			} else
				return false;
		} catch (\PDOException $e) {

			return false;
		}
	}

	public function getFilter($filtr) {

		$sql = "";
		$this->filtr = $filtr;

		if (isset($this->filtr['category_import_id']) && is_numeric($this->filtr['category_import_id'])) {
			$sql .= " AND ci.shop_hurtownie_import_category_id = " . $this->filtr['category_import_id'];
		}

		if (isset($this->filtr['category_id']) && is_numeric($this->filtr['category_id'])) {
			$sql .= " AND ci.category_id = " . $this->filtr['category_id'];
		}
		
		return $sql;
	}

	public function deleteByCatImport($id) {
		\ConnectDB::subExec('DELETE FROM shop_hurtownie_import_category_mapping WHERE shop_hurtownie_import_category_id = ' . $id);
	}

	public function save($aData) {

		try {
			if ($aData['id'] != 0)
				$res = \ConnectDB::subAutoExec('shop_hurtownie_import_category_mapping', $aData, "UPDATE", "id = " . $aData['id']);
			else
				$res = \ConnectDB::subAutoExec('shop_hurtownie_import_category_mapping', $aData, "INSERT");

			if ($res)
				return $res;
			else
				return false;
		} catch (\PDOException $e) {

			return false;
		}
	}

}
