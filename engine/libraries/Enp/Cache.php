<?php

namespace Enp;

/**
 * @package Enp
 * @author  Piotr Flasza
 * @author  Artur Świerc
 */
class Cache
{
	/**
	 * @var \Enp\Cache\Core
	 */
	static protected $instance = null;

	/**
	 * @param type $flag
	 * @throws \Enp\Exception
	 */
	static public function setRefresh($flag)
	{
		$cacheCore = self::get();

		if ($cacheCore instanceof \Enp\Cache\Core) {
			$cacheCore->setRefresh((bool) $flag);
		} else {
			throw new \Enp\Exception('Obiekt cacha to typ ' . get_class($cacheCore) . ' a zeby korzystac z tej metody to musi to byc obiekt typu \Enp\Cache\Core.' . "\n" .
			'Aby zmienic swoj obiekt cacha w miejscu gdzie go inicjujesz (np . config.cache.php) zamien kod na taki :' . "\n" .
			'$enpCacheCore = new \Enp\Cache\Core(array("automatic_serialization" => true));' . "\n" .
			'$zendCache = Zend_Cache::factory($enpCacheCore, "YOUR_TYPE", array(), array("YOUR_ARRAY_OF_BACKEND_OPTIONS"));');
		}
	}

	/**
	 * @return \Enp\Cache\Core
	 * @throws \Enp\Exception
	 */
	static public function get()
	{
		if (self::$instance === null) {
			if (\Zend_Registry::isRegistered('Zend_Cache')) {
				self::$instance = \Zend_Registry::get('Zend_Cache');
			} else {
				throw new \Enp\Exception('Zend_Cache nie zostal zdefiniowany w registry');
			}
		}
		
		self::$instance->setDefaultNamespace();
		return self::$instance;
	}
}
