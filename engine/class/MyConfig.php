<?php

class MyConfig {

	static protected $wwwPatch			= "http://liderzyit.pl/konkurs/";
	static protected $wwwPatchSsl		= "http://liderzyit.pl/konkurs/";
	static protected $gfxPatchSsl		= "http://liderzyit.pl/konkurs/";
	static protected $gfxPatch			= "http://liderzyit.pl/konkurs/";
	static protected $serverPatch		= APPLICATION_DIR;
	static protected $dbDebug			= 1;
	static protected $dbCache			= 0;
	static protected $serverDebug		= 1;
	static protected $memCache			= 0;
	static protected $templatePatch		= TEMPLATES_DIR;
	static protected $logPatch			= LOG_DIR;

	//database
	static protected $dbPrefix			= "autosalon_"; // "prefix_"
	static protected $dbHost = "localhost";
	static protected $dbLogin = "lidcon2015";
	static protected $dbDatabase = "lidcon2015";
	static protected $dbPass = "lidconita2015";
	static protected $dbPort = 3306;
	
	static protected $api_allegro_login		= "";
	static protected $api_allegro_pass		= "";
	static protected $api_allegro_country		= 1;

//	static protected $dbHost = "21655.m.tld.pl";
//	static protected $dbLogin = "admin55_subvisio";
//	static protected $dbDatabase = "baza55_subvisio";
//	static protected $dbPass = "8l1f1Y32";
//	static protected $dbPort = 3306;
	
	//tables
	static protected $__allegro						= "allegro";
	static protected $__allegro_aukcja					= "allegro_aukcja";
	static protected $__allegro_kategorie					= "allegro_kategorie";
	static protected $__allegro_kategorie_parametry			= "allegro_kategorie_parametry";
	static protected $__allegro_komentarze				= "allegro_komentarze";
	static protected $__allegro_komentarze_auto			= "allegro_komentarze_auto";
	static protected $__allegro_konto					= "allegro_konto";
	static protected $__allegro_map_transport				= "allegro_map_transport";
	static protected $__allegro_sprzedane				= "allegro_sprzedane";
	static protected $__allegro_szablony_dostawy			= "allegro_szablony_dostawy";
	static protected $__allegro_szablony_dostawy_wartosci		= "allegro_szablony_dostawy_wartosci";
	static protected $__allegro_szablony_graficzne			= "allegro_szablony_graficzne";
	static protected $__allegro_zdarzenia				= "allegro_zdarzenia";
	static protected $__allegro_zdarzenia_transakcje			= "allegro_zdarzenia_transakcje";

	static protected $__produkty						= "shop_product";
	static protected $__produkty_ceny					= "shop_product_price";
	static protected $__produkty_flagi					= "shop_product_flag";
	static protected $__produkty_pliki					= "shop_product_media";
	static protected $__produkty_opinie					= "shop_product_vote";
	
	static protected $__flagi							= "shop_flag";
	

	static public function getValue($string) {

		if (isset(self::$$string)) {
			return self::$$string;
		}else{
			return "Błąd konfiguracji dla zmiennej: ".$string." z klasy: ".get_class();
		}
	}
}
?>