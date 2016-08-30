<?php

/**
 * Klasa obslugujaca wystawianie i pobieranie aukcji Otomoto
 *
 * Możliwe typy pojazdów CAR, MOTORBIKE, TRUCK, FORKLIFT, BIKE, CONSTRUCTION
 * @package Otomotot
 */
class Otomoto_Api extends Otomoto_Observer {

	public $id;
	public $data;
	public $faultcode;
	public $faultstring;
	public $client = false;
	public $session = null;
	public $method;
	private $login_data;
	
	private $idKonto = 0;
	
	/**
	 * Typ danych dla fid
	 * @var array 
	 */
	private $fidValueType = array(
		1 => 'fvalue-string',
		2 => 'fvalue-int',
		3 => 'fvalue-float',
		7 => 'fvalue-image',
		9 => 'fvalue-datetime'
	);

	function __construct() {
		
		$this->attach(new Log_Logger());
		
		$this->login_data = array(
			'dealer-login'		=> MyConfig::getValue("api_otomoto_login"),
			'dealer-password'	=> MyConfig::getValue("api_otomoto_pass"),
			'country-code'		=> (string)MyConfig::getValue("api_otomoto_country"),
			'webapi-key'		=> MyConfig::getValue("api_otomoto_key")
		);
		
		$this->client = new SoapClient('http://otomoto.pl/webapi/server.php?wsdl', array('trace' => 1, 'exceptions' => true));
		$this->client->soap_defencoding = 'UTF-8';
		$this->client->decode_utf8		= false;

		//if ( $this->session == null ) {
			try {
				$this->login();
			} catch (SoapFault $soapFault) {
				Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
				return 100;
			}
		//}
		
	}
	
	public function getLoginData() {
		return $this->login_data;
	}

	/**
	 * Przeprowadza logowanie do otomoto
	 *
	 * @return bool
	 */
	public function login() {
		
		try {	
			
			$this->method = "doDealerLogin";
			$this->notify();

			// właściwe logowanie do serwisu
			$return = $this->client->doDealerLogin($this->login_data['dealer-login'], $this->login_data['dealer-password'], $this->login_data['country-code'], $this->login_data['webapi-key']);
			$this->session = $return['session-id'];
			
		} catch (SoapFault $soapFault) {
			echo $soapFault->faultstring;
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $return;
	}
	
	/*
	 * Dodatkowe informacje
	 */
	public function getFeatures($type = "CAR") {
		
		// CAR, MOTORBIKE, TRUCK, FORKLIFT, BIKE, CONSTRUCTION
		$this->method = "getVehicleFeatures";
		$this->notify();
		
		$parameters = array(
			'type' => $type,
			'webapi-key' => $this->login_data['webapi-key']
		);
		return $this->client->getVehicleFeatures($parameters['type'], $parameters['webapi-key']);
	}
	
	/*
	 * Dodatkowe wyposażenie
	 */
	public function getExtras($type = "CAR") {
		
		$this->method = "getVehicleExtras";
		$this->notify();
		
		$parameters = array(
			'type' => $type,
			'webapi-key' => $this->login_data['webapi-key']
		);
		
		return $this->client->getVehicleExtras($parameters['type'], $parameters['webapi-key']);
	}
	
	public function getFuelTypes($type = "CAR") {
		
		$this->method = "getFuelTypes";
		$this->notify();
		
		$parameters = array(
			'webapi-key' => $this->login_data['webapi-key'],
			'object-type' => $type
		);
		
		return $this->client->getFuelTypes($parameters['webapi-key'], $parameters['object-type']);
	}
	
	public function getGearBoxTypes() {
		
		$this->method = "getGearBoxTypes";
		$this->notify();
		
		$parameters = array(
			'webapi-key' => $this->login_data['webapi-key']
		);
		
		return $this->client->getGearBoxTypes($parameters['webapi-key']);
	}
	
	public function getColours() {
		
		$this->method = "getColours";
		$this->notify();
		
		$parameters = array(
			'webapi-key' => $this->login_data['webapi-key']
		);
		
		return $this->client->getColours($parameters['webapi-key']);
	}
	
	
	public function getCountries() {
		
		$this->method = __METHOD__;
		$this->notify();
		
		$parameters = array(
			'webapi-key' => $this->login_data['webapi-key']
		);
		
		return $this->client->getCountries($parameters['webapi-key']);
	}
	
	public function getAllegroCategories() {
		
		$this->method = __METHOD__;
		$this->notify();
		
		$parameters = array(
			'webapi-key' => $this->login_data['webapi-key']
		);
		
		return $this->client->getAllegroCategories($parameters['webapi-key']);
	}
	
	public function getVehicleCategories($type = "CAR") {
		
		$this->method = __METHOD__;
		$this->notify();
		
		$parameters = array(
			'type' => $type,
			'webapi-key' => $this->login_data['webapi-key']
		);
		
		return $this->client->getVehicleCategories($parameters['type'], $parameters['webapi-key']);
	}
	
	public function getAgroDictionary($type = '', $attributeName = 'category', $category = '', $subtype = '', $subtype2 = '') {
		$this->method = __METHOD__;
		$this->notify();
		
		$parameters = array(
			"attribute-name" => $attributeName,
			"category" => $category,
			"type" => $type,
			"subtype1" => $subtype,
			"subtype2" => $subtype2,
			"subtype3" => '',
			"webapi-key" => $this->login_data['webapi-key']
		);
		
		return $this->client->__soapCall('getAgroDictionary', $parameters);
	}
	
	public function getConstructionDictionary($attributeName = 'type', $category = '') {
		$this->method = __METHOD__;
		$this->notify();
		
		$parameters = array(
			"attribute-name" => $attributeName,
			"category" => $category,
			"webapi-key" => $this->login_data['webapi-key']
		);
		
		return $this->client->getConstructionDictionary($parameters['attribute-name'], $parameters['category'], $parameters['webapi-key']);
	}
	 
	
	public function getMakes($type = "CAR") {
		
		$this->method = "getMakes";
		$this->notify();
		
		$parameters = array(
			'type' => $type,
			'webapi-key' => $this->login_data['webapi-key']
		);
		try {
			$result = $this->client->getMakes($parameters['type'], $parameters['webapi-key']);
			
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		return $result;
	}
	
	public function getModels($id_marka, $type = "CAR") {
		
		$this->method = "getModels";
		$this->notify();
		
		$parameters = array(
			'make-id' => $id_marka,
			'webapi-key' => $this->login_data['webapi-key'],
			'country-code' => $this->login_data['country-code'],
			'type' => $type
		);
		
		try {
			$result = $this->client->getModels($parameters['make-id'], $parameters['webapi-key'], $parameters['country-code'], $parameters['type']);
			
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $result;
	}
	
	
	public function getVersions($id_marka, $id_model, $type = "CAR") {
		
		$this->method = "getVersions";
		$this->notify();
		
		$parameters = array(
			'make-id' => $id_marka,
			'model-id' => $id_model,
			'webapi-key' => $this->login_data['webapi-key'],
			'country-code' => $this->login_data['country-code'],
			'type' => $type
		);
		
		try {
			$result = $this->client->getVersions($parameters['make-id'],$parameters['model-id'],$parameters['webapi-key'],$parameters['country-code'],$parameters['type']);
			
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $result;
	}
	
	public function getList() {
		
		$this->method = "getDealerInsertions";
		$this->notify();
		
		$parameters = array(
			'type' => 'ALL',
			'session-id' => $this->session,
			'webapi-key' => $this->login_data['webapi-key'],
			'offset' => 0,
			'limit' => 10000
		);
		
		try {
			$result = $this->client->getDealerInsertions($parameters['type'], $parameters['session-id'], $parameters['webapi-key'], $parameters['offset'], $parameters['limit']);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $result;
	}
	
	public function newOffer($aData) {
		
		$vehicle_features_list = array();
		$i = 0;
		
		foreach($aData['features'] as $row_f) {
			$vehicle_features_list[$i]['key'] = $row_f;
			$vehicle_features_list[$i]['name'] = '';
			$i++;
		}
		
		$vehicle_extras_list = array();
		$i = 0;
		
		foreach($aData['extras'] as $row_e) {
			$vehicle_extras_list[$i]['key'] = $row_e;
			$vehicle_extras_list[$i]['name'] = '';
			$i++;
		}
		
		
		$insertion = array();
		foreach($aData as $key => $row) {
		 	if(!is_array($row) && !is_numeric($key)) {
				$insertion[$key] = $row;
		 	}
		}
		
		$insertion['vehicle-extras-list'] = $vehicle_extras_list;
		$insertion['vehicle-features-list'] = $vehicle_features_list;
		$insertion['contact-list'] = $contact_list;
		
		/*
		 * 
		 * $vehicle_features_list = array(
				array(’key’=>’aso_service’, ’name’ => ’serwisowany w ASO’),
				array(’key’=>’no_accident’, ’name’ => ’bezwypadkowy’)
			);
		 
		 * $vehicle_extras_list = array(
				array(’key’=>’abs’, ’name’ => ’ABS’),
				array(’key’=>’air_conditioning’, ’name’ => ’klimatyzacja’),
				array(’key’=>’sunroof’, ’name’ => ’szyberdach’),
				array(’key’=>’lpg’, ’name’ => ’instalacja gazowa’)
			);
				
		 * $contact_list = array(
				array(
				’type’ => ’phone’,
				’country-code’ => 48,
				’area-code’ => 60,
				’number’ => 1234567 // max 16 znaków
				)
			);
		 
		 $insertion = array();
		 * 
		 foreach($aData as $key => $row) {
		 	if(!is_array($row)) {
				$insertion[$key] = $row;
		 	}
				’type’ => ’car’,
				’vehicle-category-key’ => ’combi’,
				’price’ => ’40000’,
				’price-negotiable’ => ’y’,
				’price-currency’ => ’PLN’,
				’make-id’ => "3",
				’model-name’ => "Vectra",
				’version-id’ => "563",
				’build-year’ => ’2001’,
				’cubic-capacity’ => ’2100’,
				’fuel-type-key’ => ’petrol’,
				’country-id’ => ’PL’,
				’province-id’ => 17,
				’city-name’ => ’Pozna´n’,
				’vehicle-extras-list’ => $vehicle_extras_list,
				’vehicle-features-list’ => $vehicle_features_list,
				’contact-list’ => $contact_list,
			);
		 * 
		 */
		
		$parameters = array(
			'insertion' => $insertion,
			'session-id'=> $this->session,
			'webapi-key' => MyConfig::getValue("api_otomoto_key")
		);
		//print_r($parameters);
		$this->method = "doInsertionEdit";
		$this->notify();
		
		try {
			$return = $this->client->doInsertionEdit($parameters['insertion'], $parameters['session-id'], $parameters['webapi-key']);
		} catch (SoapFault $soapFault) {
			return array("blad" => 1, "tresc" => $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $return;
	}
	
	public function addPhoto($photo, $offer_id, $i) {
		
		echo $photo;
		$file = fopen($photo, 'r');
		$imageData = fread($file, filesize($photo));
		fclose($file);
		
		$photo = array(
			'number' => $i, 
			'body' => new SoapVar($imageData, XSD_BASE64BINARY),
			'link' => '',
			'body-content-type' => ''
		);
		
		$parameters = array (
			'insertion-id' => $offer_id,
			'photo' => $photo,
			'session-id'=> $this->session,
			'webapi-key' => MyConfig::getValue("api_otomoto_key")
		);
		
		$this->method = "doPhotoEdit";
		$this->notify();
		
		try {
			$return = $this->client->doPhotoEdit($parameters['insertion-id'], $parameters['photo'], $parameters['session-id'], $parameters['webapi-key']);
		} catch (SoapFault $soapFault) {
			return array("blad" => 1, "tresc" => $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $return;
	}
	
	public function publicOffer(array $offers) {
		
		$parameters = array(
			'insertion' => $offers,
			'session-id'=> $this->session,
			'webapi-key' => MyConfig::getValue("api_otomoto_key")
		);
		
		$this->method = "doDealerInsertionActivate";
		$this->notify();

		try {
			$return = $this->client->doDealerInsertionActivate($parameters['insertion'], $parameters['session-id'], $parameters['webapi-key']);
		} catch (SoapFault $soapFault) {
			return array("blad" => 1, "tresc" => $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $return;
	}
	
	public function hideOffer(array $offers) {
		
		$parameters = array(
			'insertion' => $offers,
			'session-id'=> $this->session,
			'webapi-key' => MyConfig::getValue("api_otomoto_key")
		);
		
		$this->method = "doDealerInsertionInactivate";
		$this->notify();

		try {
			$return = $this->client->doDealerInsertionInactivate($parameters['insertion'], $parameters['session-id'], $parameters['webapi-key']);
		} catch (SoapFault $soapFault) {
			return array("blad" => 1, "tresc" => $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $return;
	}
	
	public function deleteOffer(array $offers) {
		
		$parameters = array(
			'insertion' => $offers,
			'session-id'=> $this->session,
			'webapi-key' => MyConfig::getValue("api_otomoto_key")
		);
		
		$this->method = "doInsertionDelete";
		$this->notify();

		try {
			$return = $this->client->doInsertionDelete($parameters['insertion'], $parameters['session-id'], $parameters['webapi-key']);
		} catch (SoapFault $soapFault) {
			return array("blad" => 1, "tresc" => $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $return;
	}

	public function getOffers() {
		$parameters = array(
			'type' => 'ALL',
			'session-id' => $this->session,
			'webapi-key' => MyConfig::getValue("api_otomoto_key"),
			'offset' => 0,
			'limit' => 100
		);
		
		try {
			$return = $this->client->getDealerInsertions($parameters['type'], $parameters['session-id'], $parameters['webapi-key'], $parameters['offset'], $parameters['limit']);
		} catch (SoapFault $soapFault) {
			return array("blad" => 1, "tresc" => $soapFault->faultstring . ' code ' . $soapFault->faultcode);
		}
		
		return $return;

	} 

	/**
	 * Redukuje obraz do wielkości nadającej się do przesyłu.
	 *
	 * @param string $url URL obrazka (lokalne, albo sieciowe).
	 * @return string Binarna zawartość obrazka w formacie JPEG.
	 */
	public static function resize($url) {
		$image = file_get_contents($url);

		while (strlen(base64_encode($image)) > 200000) {
			$temp = imagecreatefromstring($image);
			$x = ceil(0.9 * imagesx($temp));
			$y = ceil(0.9 * imagesy($temp));

			$image = imagecreatetruecolor($x, $y);
			imagecopyresized($image, $temp, 0, 0, 0, 0, $x, $y, imagesx($temp), imagesy($temp));

			$filename = 'temp/' . time() . microtime(true) . rand(1, 999999) . '.jpg';
			imagejpeg($image, $filename, 75);
			$image = file_get_contents($filename);
			unlink($filename);
		}

		return $image;
	}

}

?>