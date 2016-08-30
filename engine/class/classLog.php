<?php
include_once('Zend/Log.php');                // Podstawowa klasa Zend_Log
include_once('Zend/Log/Adapter/File.php');   // Adapter rejestrowania do pliku

error_reporting(E_ERROR);

function error_handler ($error_level, $error_message, $file, $line) {
	$trescBledu = " -- ".$error_level." -- ".$error_message." -- ".$file." -- ".$line." -- ".date('Y.m.d')." -- <br>\n";
	switch ($errno) {
	  case E_USER_ERROR:
	   echo "Przepraszamy serwis chwilowo niedostępny. Błąd krytyczny. ".$trescBledu;
	   break;
	  case E_USER_WARNING:
	  echo $trescBledu;
	   break;
  	}
}
// do dopracowania exception ha

function exception_handler( $e )
    {
        echo '<b>Exception :</b><br />';
        echo 'komunikat => '.$e->getMessage().'<br />';
        echo 'plik => '.$e->getFile().'<br />';
        echo 'linia => '.$e->getLine().'<br />';

    }
set_exception_handler('exception_handler');
set_error_handler('error_handler');

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
class Log
{   
	public static function SLog($trescBledu, $wlacz=1)
	{
		$dataWpisu = date("Y-m-d H:i:s");
		$trescBledu = "------------------------\n".$dataWpisu." : ".$trescBledu;
		try{
		   // rejestrujemy adapter w tym przpadku plik 
		  // if(!self::hasLogger())
		  // {
			//	self::registerLogger(new Zend_Log_Adapter_File(MyConfig::getValue('logPatch')));		
		  // }
		   // pakujemy do loga informacje o błedzie
		    $obLog = new Zend_Log();
			$obLog->log($trescBledu); echo $trescBledu;
		    if (MyConfig::getValue('serverDebug') == 1 && $wlacz == 1) 
		    {
				echo "<div style='width: 400px; text-align: center;'>- START DEBUG -</div>" ;
		    	echo "<div style='width: 400px;'> Wykryto następujące błędy<br />".$trescBledu."</div>" ;
		    	echo "<div style='width: 400px; text-align: center;'>- END DEBUG -</div>" ;
		    }
		}
		catch (Zend_Log_Exception $e)
		{
			// Jeżeli składowa Debug ustawiona na jeden
			// czyli trwa proces testowania
			// to wyświetl ewentualne wyjątki
			if (MyConfig::getValue('serverDebug') == 1) 
				echo $e ;
		}
	}
}

?>