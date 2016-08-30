<?php

class MyConfig {

	static protected $wwwPatch = "http://www.zambi.pl/";
	static protected $wwwPatchSsl = "http://www.zambi.pl/";
	static protected $gfxPatch = "https://www.zambi.pl/";
	static protected $gfxPatchSsl = "https://www.zambi.pl/";
	static protected $serverPatch = APPLICATION_DIR;
	static protected $dbDebug = 1;
	static protected $dbCache = 0;
	static protected $serverDebug = 1;
	static protected $memCache = 0;
	static protected $templatePatch = TEMPLATES_DIR;
	static protected $logPatch = LOG_DIR;

	//database
	static protected $dbHost = "s1.monkeystudio.pl";
	static protected $dbLogin = "root";
	static protected $dbDatabase = "zambi";
	static protected $dbPass = "Sub677.1";
	static protected $dbPort = 3306;


	public function getValue($string) {

		if (isset(self::$$string)) {
			return self::$$string;
		}else{
			return "B��d konfiguracji dla zmiennej: ".$string." z klasy: ".get_class();
		}
	}
}
?>