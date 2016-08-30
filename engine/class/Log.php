<?php

ini_set("include_path", MyConfig::getValue("serverPatch").'engine/libraries/');
include_once('Zend/Log.php');                // Podstawowa klasa Zend_Log
include_once('Zend/Log/Adapter/File.php');   // Adapter rejestrowania do pliku
ini_set("include_path", MyConfig::getValue("serverPatch").'engine/');

/**
 * Klasa do obsługi wszystkich błędów na stronach
 * 
 * Klasa zapisuje wykryte błędy w logach, i jeżeli istnieje taka konieczność 
 * wyświetla komunikat zaistniałego błędu dla użytkownika
 *
 * Klasa korzysta z klasy frameworka Zenda, jej jedynym zadaniem jest ustawienie 
 * domyslne Logerra, żeby za kazdym razem nie trzeba było tej części kodu powtarzać.
 * 
 */
error_reporting(E_ALL);

function error_handler ($error_level, $error_message, $file, $line) {
	$trescBledu = " -- ".$error_level." -- ".$error_message." -- ".$file." -- ".$line." -- ".date('Y.m.d')." -- <br>\n";
	switch ($error_level) {
		case E_USER_ERROR:
			echo "Przepraszamy serwis chwilowo niedostępny. Błąd krytyczny. ".$trescBledu;
		break;
	
		case E_USER_WARNING:
			echo $trescBledu;
		break;
  	}
}
// do dopracowania exception ha

function exception_handler( $e ) {
        echo '<b>Exception :</b><br />';
        echo 'komunikat => '.$e->getMessage().'<br />';
        echo 'plik => '.$e->getFile().'<br />';
        echo 'linia => '.$e->getLine().'<br />';
}

set_exception_handler('exception_handler');
set_error_handler('error_handler');

class Log extends Zend_Log 
{   
	public static function SLog($miejsce, $trescBledu, $wlacz=1)
	{
//		$dataWpisu = date("Y-m-d H:i:s");
//		$path = MyConfig::getValue("logPatch").date("Y-m-d")."/";
//		
//		if(file_exists($path)) {
//			if(!file_exists($path.date("H").":00.txt")) 
//				fopen($path.date("H").":00.txt", "w+");
//			
//			$path = $path.date("H").":00.txt";
//		} else {
//			mkdir($path,"777");
//			fopen($path.date("H").":00.txt", "w+");
//			$path = $path.date("H").":00.txt";
//			
//		}
//
//		$trescBledu = "------------------------\n".$miejsce."\n".$dataWpisu." : ".$trescBledu;
//		try{
//			// rejestrujemy adapter w tym przpadku plik 
//			//if(!self::hasLogger()) {
//			//	self::registerLogger(new Zend_Log_Adapter_File($path));		
//			//}
//			
//			// pakujemy do loga informacje o błedzie
//			self::log($trescBledu) ;
//			if (MyConfig::getValue('serverDebug') == 1 && $wlacz == 1)  {
//				echo "<div style='width: 400px; text-align: center;'>- START DEBUG -</div>" ;
//				echo "<div style='width: 400px;'> Wykryto następujące błędy<br />".$trescBledu."</div>" ;
//				echo "<div style='width: 400px; text-align: center;'>- END DEBUG -</div>" ;
//			}
//		}
//		catch (Zend_Log_Exception $e) {
//			// Jeżeli składowa Debug ustawiona na jeden
//			// czyli trwa proces testowania
//			// to wyświetl ewentualne wyjątki
//			if (MyConfig::getValue('serverDebug') == 1) 
//				echo $e ;
//		}
	}
}

?>