<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReturnPayment
 *
 * @author tcisowski
 */

 
namespace Platnosci\PayByNet;
use Platnosci\PlatnosciInterface;
use Platnosci\Log;

class ReturnPayment implements PlatnosciInterface {
	
	private $_codes = array(
		'1000' => 'Transakcja odrzucona',
		'1001' => 'Przeterminowana',
		'1002' => 'Nieważny certyfikat',
		'1003' => 'Naruszona integralność',
		'1004' => 'Błędy formalne',
		'1005' => 'Nieprawidłowy status Sprzedawcy',
		'1006' => 'Brak Sprzedawcy w bazie klientów',
		'2100' => 'Transakcja przyjęta',
		'2101' => 'Oczekująca na wybór banku',
		'2102' => 'Skierowana do banku',
		'2200' => 'Transakcja przekierowana',
		'2201' => 'Przeterminowana',
		'2202' => 'Odrzucona',
		'2203' => 'Zatwierdzona',
		'2301' => 'Przeterminowana',
		'2302' => 'Odrzucona',
		'2303' => 'Zatwierdzona'
	);
	
	public function getStatus( $text_result = 1 )
    {
		$this->_client = new SoapClient(PAY_HOST_RESULT,
			array(
				'cache_wsdl'=>WSDL_CACHE_NONE,
				'connection_timeout'=>5
				)
		);

		$result = $this->_client->getStatusByPaymentID($this->_id_trans, $this->_id_client);

		if($text_result == 1)
			return $this->_codes[$result];
		else
			return $result;
    }
	
}

?>
