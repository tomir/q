<?php

define('APPLICATION_DIR',$_SERVER['DOCUMENT_ROOT'].'/konkurs/');
define('APPLICATION_DIR_TEMPLATES',$_SERVER['DOCUMENT_ROOT'].'/konkurs/templates/');

class MyConfig {

	static protected $wwwPatch = "http://liderzyit.pl/konkurs/";
	static protected $wwwPatchPanel = "http://liderzyit.pl/konkurs/admin/";
	static protected $adminPanel = "http://liderzyit.pl/konkurs/admin/";
	static protected $serverPatch = APPLICATION_DIR;
	static protected $tffPatch = "http://liderzyit.pl/konkurs/tff/";
	static protected $dbDebug = 0;
	static protected $dbCache = 0;
	static protected $serverDebug = 0;
	static protected $memCache = 0;
	static protected $templatePatch = APPLICATION_DIR_TEMPLATES;
	static protected $logPatch = "/log.txt";
	
	//database
	static protected $dbPrefix			= "autosalon_"; // "prefix_"
	static protected $dbHost = "localhost";
	static protected $dbLogin = "lidcon2015";
	static protected $dbDatabase = "lidcon2015";
	static protected $dbPass = "lidconita2015";
	static protected $dbPort = 3306;

	//admin
	static protected $adminEmail = "t.cisowski@gmail.com";
	static protected $adminImie = "Tomasz";
	static protected $adminNazwisko = "Cisowski";
	static protected $admiSiteTitle = "tForm - panel administracyjny | by subVision";
	
	static protected $api_otomoto_login		= "aukcje_angliki@poczta.fm";
	static protected $api_otomoto_pass		= "HyaZtsgh";
	static protected $api_otomoto_key		= "F9AAE7345A463C54E0DEDFAC877B3464";
	static protected $api_otomoto_country	= 1;
	
	//static protected $api_otomoto_login		= "tech4@otomoto.pl";
	//static protected $api_otomoto_pass		= "123456789";
	//static protected $api_otomoto_key		= "3685AB0D2E4D0DB8DF65836E41219CE2";
	//static protected $api_otomoto_country	= 1;
	
	//strona
	static protected $siteWidth = 988;
	
	public function getValue($string) {
		
		if (isset(self::$$string)) {
			return self::$$string;		
		}else{
			return "Błąd konfiguracji dla zmiennej: ".$string." z klasy: ".get_class();
		}	
	}
}
?>