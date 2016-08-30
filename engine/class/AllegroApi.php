<?php

/**
 * Klasa obslugujaca wystawianie i pobieranie aukcji allegro.pl
 *
 * @package Allegro
 */
class AllegroApi {

	public $id;
	public $data;
	public $faultcode;
	public $faultstring;
	public $client = false;
	public $session = null;
	private $version = false;
	private $id_serwisu = null;
	private $login_data = array(
		'login' => '',
		'pass' => '',
		'apikey' => ''
	);
	private $idKonto = 0;

	/**
	 * Opcje wystawienia przedmiotu
	 * bold 		- pogrubienie aukcji 2 PLN
	 * thumb		- miniaturka 0.15 PLN
	 * highlight	- podswietlenie aukcji 6 PLN
	 * premium		- wyroznienie aukcji 12 PLN
	 * category		- promowane w kategorii 29 PLN
	 * home			- strona glowna allegro 99 PLN
	 *
	 * @var array
	 */
	private $options_cost = array('bold' => array('val' => 1, 'cost' => 2), 'thumb' => array('val' => 2, 'cost' => 0.15), 'highlight' => array('val' => 4, 'cost' => 6), 'premium' => array('val' => 8, 'cost' => 12), 'category' => array('val' => 16, 'cost' => 29), 'home' => array('val' => 32, 'cost' => 99));
	
	/**
	 * Zmienna która trzyma dane do walidacji fid przy wystawianiu aukcji array($fid=>dane)
	 * @var array $fidValidacja
	 */
	private $fidValidator = array();
	
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

	function __construct($idSerwis = 0, $idKonto = 0) {
		$this->client = new SoapClient('https://webapi.allegro.pl/uploader.php?wsdl', array('trace' => 0));
		if ( ($this->session == null || (isset($this->login_data['id_serwisu']) && $this->login_data['id_serwisu'] != $idSerwis)) ) {
			if($idSerwis > 0) {
				$this->login($idSerwis, $idKonto);
			}
		}
	}

	/**
	 * Przeprowadza logowanie do allegro
	 *
	 * @return bool
	 */
	public function login($id_serwisu = 0, $id_konto = 0, $api_key = "") {
		try {
			
			if($id_serwisu > 0) {
				// pobieramy dane do logowania - piersze z brzegu konto powiazane z serwisem
				$allegroKonto = new Allegro_Konto();
				$konto = $allegroKonto->getList(0,1);

				if(empty($konto)) {
					throw new AllegroApi_Exception('Nie ma konta allegro powiązanego z serwisem');
				}
				$konto = $konto[0];

				$this->login_data = array(
					'id' => $konto['id'],
					'id_serwisu' => $id_serwisu,
					'login' => MyConfig::getValue("api_allegro_login"),
					'pass' => MyConfig::getValue("api_allegro_pass"),
					'apikey' => $konto['apikey'],
					'id_country' => MyConfig::getValue("api_allegro_country")
				);
			} else {
				$this->login_data = array(
					'login' => MyConfig::getValue("api_allegro_login"),
					'pass' => MyConfig::getValue("api_allegro_pass"),
					'apikey' => $api_key,
					'id_country' => MyConfig::getValue("api_allegro_country")
				);
			}
			
	
			// pobieranie wersji WebAPI
			$this->version = $this->client->doQuerySysStatus(1, $this->login_data['id_country'], $this->login_data['apikey']);

			// właściwe logowanie do serwisu
			$this->session = $this->client->doLogin($this->login_data['login'], $this->login_data['pass'], $this->login_data['id_country'], $this->login_data['apikey'], $this->version['ver-key']);
			
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
		return true;
	}

	/**
	 * Zwraca id_allegro pomyslnie zakonczonych
	 * @param array $idsAukcji
	 * @return array
	 */
	public function finishItems(array $idsAukcji) {
		if (!is_array($idsAukcji) || count($idsAukcji) == 0) {
			throw new Exception('Podaj identyfikator aukcji');
		}

		$data = array();
		foreach ($idsAukcji as $id_aukcji) {
			$data[] = array(
				'finish-item-id' => $id_aukcji,
				'finish-cancel-all-bids' => 0, //czy odwolac juz zlozone oferty
				'finish-cancel-reason' => ''
			);
		}
		try {
			$result = $this->client->doFinishItems($this->session['session-handle-part'], $data);
			return $result;
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
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

	/**
	 * Ustawia dane do walidacji 
	 * @param array $data 
	 */
	public function setFidValidator(array $data) {
		if (count($data) > 0) {
			$this->fidValidator = $data;
		}
	}

	/**
	 * Sprawdza jaki typ wartości przyjmuje fid
	 * @param int $fid 
	 */
	private function getFidValueType($fid) {
		$fid = (int) $fid;

		$fidValueType = array(
			1 => 'fvalue-string',
			2 => 'fvalue-int',
			3 => 'fvalue-float',
			7 => 'fvalue-image',
			9 => 'fvalue-datetime'
		);

		if (count($this->fidValidator) == 0) {
			throw new AllegroApi_Exception('Ustaw dane do walidacji');
		}

		if (!array_key_exists($fid, $this->fidValidator)) {
			throw new AllegroApi_Exception('Nie istnieje pole o numerze fid: ' . $fid);
		}

		$data = $this->fidValidator[$fid];
		return $this->fidValueType[$data['form_res_type']];
	}

	/**
	 * Parsuje zmienne na odpowiedni typ
	 * @param string $type typ pola
	 * @param type $val
	 * @return $val 
	 */
	private function parseFidValueType($type, $val) {

		switch ($type) {
			case 'fvalue-string' :
				return (string) $val;
				break;
			case 'fvalue-int' :
				return (int) $val;
				break;
			case 'fvalue-float':
				return (float) $val;
				break;
			case 'fvalue-image':
				return $val;
				break;
			case 'fvalue-datetime':
				return $val;
				break;
			default:
				throw new AllegroApi_Exception('Nie ma takiego typu' . __METHOD__ . '(type: ' . $type . ',  val:' . $val . ')');
				break;
		}
	}

	/**
	 * Wystawia przedmiot na podstawie podanej tablicy parametrow
	 *
	 * @param array $data
	 * @return bool || array
	 */
	public function sell($data) {
		
		// field	
		$empty = array(
			'fvalue-string' => '',
			'fvalue-int' => '',
			'fvalue-float' => '',
			'fvalue-image' => '',
			'fvalue-image' => '',
			'fvalue-datetime' => '',
			'fvalue-date' => '',
			'fvalue-range-int' => array(
				'fvalue-range-int-min' => '',
				'fvalue-range-int-max' => '',
			),
			'fvalue-range-float' => array(
				'fvalue-range-float-min' => '',
				'fvalue-range-float-max' => '',
			),
			'fvalue-range-date' => array(
				'fvalue-range-date-min' => '',
				'fvalue-range-date-max' => '',
			)
		);
		$empty = (object) $empty;

		$form = array();

		// Format sprzedaży
		// jezeli uzytkownik wybral kup teraz to zaznacza to pole kup teraz jest odblokowane a pola cena wywolawcza,minimalna sa blokowane, wartosc pola fid jest rowne 0
		// jezeli uzytkownik wybral aukcja to zaznacza to pola cena wywolawcza,minimalna sa odblokowane a pole kup teraz jest blokowanee, wartosc pola fid jest rowne 0
		// jezeli uzytkownik wybral aukcja sklepowa to pole kup teraz jest odblokowane a pola cena wywolawcza,minimalna sa blokowane, wartosc pola fid jest rowne 1, a ilosc dni jest rowna 30
		switch((int)$data['id_rodzaju']) {
			case 1: // kup teraz
				$data['fid'][29] = 0;
				break;
			case 2: //aukcja
				$data['fid'][29] = 0;
				break;
			case 3: //aukcja sklepowa
				$data['fid'][29] = 1;
				// ustawiamy czas trwania aukcji na 30
				$data['fid'][4] = 5;
				break;
		}


		// zdjecia
		$i = 0;
		
		foreach ($data['photos'] as $image) { 
			$field = clone $empty;
			$field->{'fid'} = 16 + $i;
			$field->{'fvalue-image'} = Allegro::resize($image); //file_get_contents($image);
			$form[] = $field;
			$i++;
		}
		
		// kraj
		$field = clone $empty;
		$field->{'fid'} = 9;
		$field->{'fvalue-int'} = $this->login_data['id_country'];
		$form[] = $field;

		// fid - pola
		foreach ($data['fid'] as $fid => $val) {

			// jezeli jest pusta wartosc to pomijamy
			if (!is_array($val)) {
				$val = trim($val);
				if (empty($val) && $val != '0') {
					continue;
				}
			}

			$val = (array) $val;

			$field = clone $empty;
			$field->{'fid'} = $fid;

			// sprawdzamy do jakiego pola wedlug typu danych powinien zostac umieszczony
			$valType = $this->getFidValueType($fid);
			// jezeli jest zbior wartosci to robimy sume (z liczb, bo ich wartosci 
			// sa potega dwojki wiec potrafia sobie to rozszyfrowac)
			if (count($val) > 1) {
				$valTmp = 0;
				foreach ($val as $option) {
					$valTmp+= $this->parseFidValueType($valType, $option);
				}
				$val = $valTmp;
			} else {
				$val = $this->parseFidValueType($valType, $val[0]);
			}

			$field->{$valType} = $val;
			$form[] = $field;
		}

		//dodatkowe pola
		foreach ($data['field'] as $f_id => $v) {
			$f = Allegro::getAuctionField($f_id);

			if (($f['form_type'] == 1 || $f['form_type'] == 2 || $f['form_type'] == 3 || $f['form_type'] == 4 || $f['form_type'] == 6) && $v != '') {
				$field = clone $empty;
				$field->{'fid'} = $f_id;

				if ($f['form_res_type'] == 1)
					$field->{'fvalue-string'} = (string) $v;
				if ($f['form_res_type'] == 2 && $f['form_type'] != 6)
					$field->{'fvalue-int'} = intval($v);
				if ($f['form_res_type'] == 3)
					$field->{'fvalue-float'} = floatval($v);
				if ($f['form_res_type'] == 4)
					$field->{'fvalue-int'} = intval($v);
				if ($f['form_res_type'] == 2 && $f['form_type'] == 6) {
					$x = 0;
					foreach ($v as $v2)
						$x += $v2;
					$field->{'fvalue-int'} = intval($x);
				}
				$form[] = $field;
			}
		}

		$local = uniqid();
		//print_r($form);
		
		try {

			$item = $this->client->doNewAuctionExt($this->session['session-handle-part'], $form);

			return $item;
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			$this->faultcode = $soapFault->faultcode;
			$this->faultstring = $soapFault->faultstring;

			Tresc::wyslijMail( "Nie wystawiła sie aukcja ".$data["id"], "tomasz.cisowski@enp.pl", $soapFault->faultstring."<br />".$this->faultcode."<br />".$data["fid"][1]."<br /><b>Prosimy wstrzymać błędną aukcję.</b>", "", true, "system@bdsklep.pl");
			Tresc::wyslijMail( "Nie wystawiła sie aukcja ".$data["id"], "gba@bdsklep.pl", $soapFault->faultstring."<br />".$this->faultcode."<br />".$data["fid"][1]."<br /><b>Prosimy wstrzymać błędną aukcję.</b>", "", true, "system@bdsklep.pl");
			Tresc::wyslijMail( "Nie wystawiła sie aukcja ".$data["id"], "dku@bdsklep.pl", $soapFault->faultstring."<br />".$this->faultcode."<br />".$data["fid"][1]."<br /><b>Prosimy wstrzymać błędną aukcję.</b>", "", true, "system@bdsklep.pl");
			//throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
		return false;
	}

	/**
	 * Zwraca liste wszystkich mozliwych pol do wypelnienia przy wystawianiu aukcji wraz z opisem
	 *
	 * @return array
	 */
	function doGetSellFormFieldsExt() {
		try {
			$res = $this->client->doGetSellFormFieldsExt(
							$this->login_data['id_country'], $this->version['ver-key'], $this->login_data['apikey']);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}
	
	/**
	 * Zwraca liste wszystkich mozliwych pol do wypelnienia przy wystawianiu aukcji wraz z opisem
	 *
	 * @return array
	 */
	function doGetSellFormFieldsExtLimit($options) {
		try {
			$res = $this->client->doGetSellFormFieldsExtLimit(
							$this->login_data['id_country'], $this->version['ver-key'], $this->login_data['apikey'], $options['start'], $options['limit']);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}

	/**
	 * Zwraca informacje o podanej aukcji
	 *
	 * @param int $auction_id
	 * @return array
	 */
	public function getAuction($auction_id) {
		try {
			$res = $this->client->doGetItemsInfo(
							$this->session['session-handle-part'], array($auction_id), 0, 0);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}

	/**
	 * Zwraca informacje o podanej aukcji
	 *
	 * @param int $auction_id
	 * @return array
	 */
	public function getItemInfo($auction_id) {
		try {
			$res = $this->client->doShowItemInfoExt($this->session['session-handle-part'], $auction_id, 0, 0, 0, 1, 1);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}

	/**
	 * Zwraca liste ofert dla podanej aukcji
	 *
	 * @param int $auction_id
	 * @return array
	 */
	public function getAuctionBids($auctionId) {
		return $this->client->doGetBidItem2($this->session['session-handle-part'], $auctionId, 0, 0);
	}

	/**
	 * Zwraca aukcje sprzedane
	 *
	 * @return array
	 */
	public function getSold($limit = 25) {
		try {
			$res = $this->client->doMyAccount2($this->session['session-handle-part'], 'sold', 0, array(), $limit);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}

	/**
	 * Zwraca informacje o komentarzu do podanej aukcji
	 *
	 * @param int $auction_id
	 * @return unknown
	 */
	public function getFeedback($auction_id) {
		try {
			$_arrArguments[] = $auction_id;
			$res = $this->client->doMyFeedback2($this->session['session-handle-part'], 'fb_recvd', 0, 0, $_arrArguments);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}

	/**
	 * Ustawia komentarz do podanej aukcji
	 *
	 * @param int $auction_id
	 * @return unknown
	 */
	public function setFeedback($auction_id, $to_user_id, $text, $type = 'POS') {
		try {
			$res = $this->client->doFeedback($this->session['session-handle-part'], $auction_id, NULL, $text, $type, 2);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}

	/**
	 * Zwraca liste uzytkownikow ktorzy kupili przedmiot
	 *
	 * @param int $auction_id
	 * @return unknown
	 */
	public function getWonUsers($auction_id) {
		try {
			$res = $this->client->doMyContact($this->session['session-handle-part'], array($auction_id), 0, 0);
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}

	/**
	 * Pobiera kategorie
	 */
	public function doGetCatsData() {
		try {
			$res = $this->client->doGetCatsData($this->login_data['id_country'], $this->version['ver-key'], $this->login_data['apikey']);
			return $res;
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}
	}

	/**
	 * Pobiera wszystkie dane z wypełnionych przez kupujących
	 *
	 * @param int $auction_id
	 * @return unknown
	 */
	public function getUsersFormsData($auction_id) {
		try {
			$res = $this->client->doGetPostBuyFormsData($this->session['session-handle-part'], array($auction_id));
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}
	
	public function getPostBuyData($options) {
		try {
			$res = $this->client->doGetPostBuyData(
				$this->session['session-handle-part'],
				$options
			);
		
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			return false;
		}

		return $res;
	}

	/**
	 * Dodaje opis do wybranych aukcji
	 * @param array $auctionsId
	 * @param string $desc
	 * @return ApiResult 
	 */
	public function doAddDescToItems(array $auctionsId, $desc) {
		try {
			$res = $this->client->doAddDescToItems(
							$this->session['session-handle-part'], $auctionsId, $desc
			);
			return $res;
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
	}

	/**
	 * Wystaw ponownie
	 */
	public function doSellSomeAgain($dataIn) {
		try {

			$data = array(
				'session-handle' => $this->session['session-handle-part'],
				'sell-items-array' => array(),
				'sell-starting-time' => 0,
				'sell-auction-duration' => 0,
			);

			#validacja
			if (isset($dataIn['sell-items-array']) && is_array($dataIn['sell-items-array']) && count($dataIn['sell-items-array']) > 0) {
				$data['sell-items-array'] = $dataIn['sell-items-array'];
			} else {
				throw new InvalidArgumentException('Podaj id aukcji');
			}

			if (isset($dataIn['sell-auction-duration']) && (int) $dataIn['sell-auction-duration'] > 0) {
				$data['sell-auction-duration'] = $dataIn['sell-auction-duration'];
			} else {
				throw new InvalidArgumentException('Podaj czas trwania aukcji');
			}

			if (isset($dataIn['sell-starting-time'])) {
				$data['sell-starting-time'] = $dataIn['sell-starting-time'];
			} else {
				$data['sell-starting-time'] = 0;
			}

			if (isset($dataIn['sell-options'])) {
				$optionAvail = array(1, 2, 3);
				if (in_array($dataIn['sell-options'], $optionAvail)) {
					$data['sell-option'] = $dataIn['sell-options'];
				}
			}

			$res = $this->client->doSellSomeAgain(
							$this->session['session-handle-part'], $data['sell-items-array'], $data['sell-starting-time'], $data['sell-auction-duration'], $data['sell-option']
			);

			return $res;
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
	}

	/**
	 * Pobiera aukcje uzytkownika (max 25 najnowszych)
	 */
	public function doGetUserItems() {
		try {
			$res = $this->client->doGetUserItems(
							$this->session['user-id'], $this->login_data['apikey'], $this->login_data['id_country']
			);
			return $res;
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
	}

	/**
	 * Pobiera saldo konta na basie session
	 */
	public function getMyBilling() {
		try {
			$saldo = $this->client->doMyBilling($this->session['session-handle-part']);
			$saldo = str_replace(array('zł', 'crd.'), '', $saldo);
			return $saldo;
		} catch (SoapFault $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
	}
	
	public function getZdarzenia($startPoint = 0) {
		return $this->client->doGetSiteJournal($this->session['session-handle-part'], $startPoint, 0);
	}
	
	public function getTransactionsIDs($options) {

		return $this->client->doGetTransactionsIDs(
			$this->session['session-handle-part'],
			$options['items-id-array'],
			$options['user-role']
		);
	}
	
	public function getPostBuyFormsData($options) {

		return $this->client->doGetPostBuyFormsDataForSellers(
			$this->session['session-handle-part'],
			$options
		);
	}

	public function getShipmentData() {
		return $this->client->doGetShipmentData(
			$this->login_data['id_country'],
			$this->login_data['apikey']
		);
	}
	
	public function getMyIncomingPayments($options) {

		return $this->client->doGetMyIncomingPayments(
			$this->session['session-handle-part'],
			$options['buyer-id'],
			$options['item-id'],
			$options['trans-recv-date-from'],
			$options['trans-recv-date-to'],
			$options['trans-page-limit'],
			$options['trans-offset']
		);
	}
	
	public function doGetJournalDeals($start = 0) {
		return $this->client->doGetSiteJournalDeals(
			$this->session['session-handle-part'],
			$start
		);
	}
	
	/**
	 *
	 * Metoda pozwala na pobranie przez zalogowanego użytkownika daty ważności licencji,
	 * która została mu udzielona dla klucza podanego przy logowaniu.
	 * (http://allegro.pl/webapi/documentation.php/show/id,161)
	 *
	 * @return array
	 */
	public function doGetUserLicenceDate() {

		return $this->client->doGetUserLicenceDate(
			$this->session['session-handle-part']
		);
	}
}

?>