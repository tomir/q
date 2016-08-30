<?php

namespace Db;

class Db extends \Zend_Db
{
	/**
	 * @var \Zend_Db_Adapter_Abstract 
	 */
	protected static $instance = null;

	/**
	 * Sigleton method to get instance of db connection 
         * 
	 * @return \Zend_Db_Adapter_Abstract
	 */
	public static function getInstance()
	{
		if (self::$instance == null) {

			$db = self::getConnectionAdapter();
			\Zend_Db_Table::setDefaultAdapter($db);

			self::$instance = $db;
		}

		return self::$instance;
	}
	
	/**
         * Method to get mysql adapter by Zend
         * Config data are in MyConfig Class in main directory app
         * 
	 * @return \Zend_Db_Adapter_Mysqli
	 */
	protected static function getConnectionAdapter()
	{
		$config = array(
			'fetchMode' => \Zend_Db::FETCH_ASSOC,
			'host' => \MyConfig::getValue('dbHost'),
			'username' => \MyConfig::getValue('dbLogin'),
			'password' => \MyConfig::getValue('dbPass'),
			'dbname' => \MyConfig::getValue('dbDatabase'),
			'charset'  => 'utf8',
		);

		$db = new Adapter\Mysqli($config);
		return $db;
	}
	
        /**
         * Method to making db transaction; using instance od db adapter
         */
	protected static function beginTrans()
	{
		$db = self::getInstance();
		$db->beginTransaction();
	}

        /**
         * Method to accpeting db transaction; using instance od db adapter
         */
	protected static function commitTrans()
	{
		$db = self::getInstance();
		$db->commit();
	}

        /**
         * Method to rolllback db transaction; using instance od db adapter
         */
	protected static function rollbackTrans()
	{
		$db = self::getInstance();
		$db->rollBack();
	}
}
