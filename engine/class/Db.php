<?php

class Db {

	public $gTables;
	static private $instance = null;

	private function __construct() {
		
	}

	/**
	 *
	 * @return ADODB_mysqlt
	 */
	static public function getInstance() {
		if (self::$instance == null) {

			/**
			 * utworzenie katalogu $ADODB_CACHE_DIR
			 */
			if (trim(ADODB_CACHE_DIR) != '' && !file_exists(ADODB_CACHE_DIR)) {
				mkdir(ADODB_CACHE_DIR, 0777, true);
			}

			$db = new Adodb(\MyConfig::getValue("dbHost"),
							\MyConfig::getValue("dbLogin"),
							\MyConfig::getValue("dbPass"),
							\MyConfig::getValue("dbDatabase"),
							\MyConfig::getValue("dbHost"));
			/*
			if (defined('MEMCACHE_ENABLE') && MEMCACHE_ENABLE === true && defined('MEMCACHE_HOST')) {
				$db->memCache = MEMCACHE_ENABLE;
				$db->memCacheHost = MEMCACHE_HOST;
			}*/

			self::$instance = $db;

			\Enp\Db\Db::setAdodb($db);
			
//			if(TEST_SERV) {
//				$debugDb = Debugger_DebugDb::getInstance($db);
//				self::$instance = $debugDb;
//			}
		}

		return self::$instance;
	}

}
