<?php
namespace Sphinx;

class SearchInstance {

	static protected $instance = null;

	/**
	 * Tworzy singleton obiektu obslugujacego Shinxa
	 *
	 * @static
	 *
	 * @param null $indexName
	 * @param null $host
	 * @param int  $port
	 * @param bool $forceInit
	 * @return \Enp\Sphinx\Search
	 */
	static public function init($indexName = null, $host = null, $port = 3312, $forceInit = false) {
		if (self::$instance == null || $forceInit == true) {
			$searchSphinx = new \Sphinx\Search($indexName,$host,$port);
			self::$instance = $searchSphinx;
		}

		return self::$instance;
	}

	/**
	 * @return \Enp\Sphinx\Search
	 */
	static public function getInstance($forceNull = false){

		if (self::$instance == null && !$forceNull) {
			throw new Exception("Najpier musisz zainicjowac obiekt dla uslugi Shpinx. Uzyj metody SearchInstance::init");
		}

		return self::$instance;
	}

}