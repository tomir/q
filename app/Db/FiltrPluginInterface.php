<?php

namespace Db;

/**
 * @package \Db
 * @author Tomasz Cisowski
 */
interface FiltrPluginInterface
{
	/**
         * Implementation of method to service model plugins
         * Plugins are models which making additional operation on Zend db model and app db model
         * Result is in Zend db model as additional query string
         * 
	 * @param mixed|null			$value
	 * @param \Zend_Db_Select|null	$select
	 * @param \Db\Model|null	$model
	 */
	public function processSelect($value = null, \Zend_Db_Select $select = null, Db\Model $model = null);
}
