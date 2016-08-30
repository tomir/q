<?php

namespace Enp\Db;

class TableFactory {

	/**
	 * Zwraca obiekt dla tabeli o podanej nazwie
	 * 
	 * @param string type $primary
	 * @return Zend_Db_Table 
	 */
	static public function get($nazwa, $primary = 'id') {

		$config = array(
			\Zend_Db_Table::PRIMARY => $primary,
			\Zend_Db_Table::NAME => $nazwa
		);

		return new \Zend_Db_Table($config);
	}

}

?>
