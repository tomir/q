<?php

error_reporting(E_ERROR);
ini_set('display_errors', true);
ini_set('display_error', true);

require dirname(__FILE__) . '/../../vendor/autoload.php';

ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.$_SERVER['DOCUMENT_ROOT'].'vendor/');
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.$_SERVER['DOCUMENT_ROOT'].'app/');

require dirname(__FILE__) . '/autoload.php';

class MyConfig {

	//database
	static protected $dbPrefix			= ""; // "prefix_"
	static protected $dbHost			= "localhost";
	static protected $dbLogin			= "konkurs";
	static protected $dbDatabase		= "konkurs";
	static protected $dbPass			= "Wnw8ARTuOytfmZLY";
	static protected $dbPort			= 3306;
	
	static protected $memcacheTime		= 3600;
	static protected $adminPass		    = 'admin2016!';
	static protected $adminLogin		= 'admin';

	static public function getValue($string) {

		if (isset(self::$$string)) {
			return self::$$string;
		}else{
			return "Błąd konfiguracji dla zmiennej: ".$string." z klasy: ".get_class();
		}
	}
}
