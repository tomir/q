<?php

/**
 * Klasa obslugujaca zdarzenia podczas rejestracji klienta
 *
 * @package KlientRejestracja
 */
class KlientRejestracja
{

	function emailIstnieje( $email )
	{
		try {
			if( !Zend_Validate::is($email, 'EmailAddress') )
				throw new Exception('nieprawidlowy email');

			$sql = "SELECT COUNT(id) FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email = '".$email."' ";
			$val = ConnectDB::subQuery($sql,'one');
			if( (int)$val > 0 )
				return true;

			return false;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
	}
	
	function hasloIstnieje( $email )
	{
		try {

			$sql = "SELECT haslo FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email = '".$email."' ";
			$row = ConnectDB::subQuery($sql,'fetch');
			if( $row["haslo"] != "" ) {
				return true;
			}
			
			return false;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
	}



}




?>