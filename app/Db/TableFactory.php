<?php

namespace Db;

class TableFactory {

	/**
	 * Return zend table object
	 * 
	 * @param string type $primary defailt id
	 * @return Zend_Db_Table 
	 */
	static public function get($name, $primary = 'id') {

		$config = array(
			\Zend_Db_Table::PRIMARY => $name.'_'.$primary,
			\Zend_Db_Table::NAME => $name
		);

		return new \Zend_Db_Table($config);
	}

}
