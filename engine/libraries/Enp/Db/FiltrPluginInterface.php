<?php

namespace Enp\Db;

interface FiltrPluginInterface {
	public function processSelect($value = null, \Zend_Db_Select $select = null, \Enp\Db\Model $model = null);
}

?>
