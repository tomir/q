<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cron
 *
 * @author Artur Lasota
 */
class Allegro_Cron {
	private $allegro = null;
	private $allegroApi = null;
	private $idSerwis = null;
	private $idKonto = null;
	
	const KUP_TERAZ = 1;
	const AUKCJA = 2;
	const AUKCJA_SKLEPOWA = 3;
	
	const ZAMOWIENIE_STATUS_ALLEGRO = 46;
	
	public function __construct($idSerwis, $idKonto = 0) {
		$idSerwis = (int)$idSerwis;
		
		if($idSerwis <= 0) {
			throw new InvalidArgumentException('Podaj id serwisu');
		}
		
		$this->idSerwis = $idSerwis;
		$this->idKonto= $idKonto;
		$this->allegroApi = new AllegroApi($idSerwis, $idKonto);
		$this->allegro = new Allegro();
	}
	
	/**
	 * Sprawdza i aktualizuje aukcje typu kup teraz
	 * @return type 
	 */
	public function updateKupTeraz() {
		
		$this->log('Analizujemy zdarzenia');
		$zdarzeniaObj = new Allegro_Zdarzenie();
		$zdarzenia = $this->getOstatnieZdarzenia();
		$liczbaZdzarzen = count($zdarzenia);
		$zdarzeniaOstatniIndex = $liczbaZdzarzen - 1;
		foreach ($zdarzenia as $index => $zdarzenie) {
			$zdarzenie = (array) $zdarzenie;
			if ($zdarzenie['change-type'] == Allegro_Zdarzenie_Typ::ZAKUP_PRZEZ_KUP_TERAZ || $index == $zdarzeniaOstatniIndex || $zdarzenie['change-type'] == Allegro_Zdarzenie_Typ::ZAKONCZENIE_AUKCJI) {
				if ($zdarzenie['change-type'] == Allegro_Zdarzenie_Typ::ZAKUP_PRZEZ_KUP_TERAZ) {
					//$this->log('oferta aukcji: ' . $zdarzenie['item-id']);
					$idAukcji = array(
						$zdarzenie['item-id']
					);
					
					$aukcje = $this->getAukcje(array('format_sprzedazy' => self::KUP_TERAZ, 'cron' => 0, 'id_aukcji' => $idAukcji));
					if (count($aukcje) > 0) {
						$aukcja = $aukcje[0];
							$this->analizujOfertyKupTeraz($aukcja);
							//$this->kontrolujMinIloscProduktow($aukcja);


					}
					
					$aukcje = $this->getAukcje(array('format_sprzedazy' => self::AUKCJA_SKLEPOWA, 'cron' => 0, 'id_aukcji' => $idAukcji));
					if (count($aukcje) > 0) {
						$aukcja = $aukcje[0];
							$this->analizujOfertyKupTeraz($aukcja);
							//$this->kontrolujMinIloscProduktow($aukcja);


					}
					
				} elseif ($zdarzenie['change-type'] == Allegro_Zdarzenie_Typ::ZAKONCZENIE_AUKCJI) {
					$idAukcjiZakonczonych = $this->allegro->getIdAllegroByIdAukcji(array($zdarzenie['item-id']));
					if (count($idAukcjiZakonczonych) > 0) {
						foreach ($idAukcjiZakonczonych as $idAukcji) {
							$this->allegro->zakonczAukcje($idAukcji);
							$this->log('zakonczono aukcje(id:' . $idAukcji . ')');
						}
					}
				}

				$data = array(
					'id_zdarzenie' => $zdarzenie['row-id'],
					'id_aukcji' => $zdarzenie['item-id'],
					'id_konto' => $this->idKonto,
					'typ' => $zdarzenie['change-type'],
					'data' => date('Y-m-d H:i:s', $zdarzenie['change-date']),
					'price' => $zdarzenie['current-price'],
					'id_sprzedawca' => $zdarzenie['item-seller-id']
				);
				$zdarzeniaObj->save($data);
			}
		}
		$this->log('Koniec analizy zdarzen');
		/*
		$aukcje = $this->getAukcje(array('format_sprzedazy' => self::KUP_TERAZ, 'cron' => 0, 'id_aukcji' => array(2061958268)));
		echo "aukcje";
		print_r($aukcje);
					if (count($aukcje) > 0) {
						$aukcja = $aukcje[0];
							$this->analizujOfertyKupTeraz($aukcja);
							//$this->kontrolujMinIloscProduktow($aukcja);


					}
		*/
		$aukcje = array();
		$aukcje = $this->getTrwajaceAukcje(array('format_sprzedazy' => self::KUP_TERAZ, 'cron' => 0));
		
		if (count($aukcje) == 0) {
			$this->log('Brak trwajacych aukcji do kontroli minimalnej ilosci produktow');
		} else {
			$this->log('Kontrolujemy minimalna ilosc produktow');
			foreach ($aukcje as $aukcja) {
				//$this->log('id aukcji: ' . $aukcja['id_aukcji']);
				$this->kontrolujMinIloscProduktow($aukcja);
			}
		}
	}
	
	/* stara metoda
	public function updateKupTeraz() {	
		$idAukcji = array();
		$idAukcjiEnd = array();

		
		$zdarzeniaObj = new Allegro_Zdarzenie();
		
		$zdarzenia = $this->getOstatnieZdarzenia();
		$liczbaZdzarzen = count($zdarzenia);
		$zdarzeniaOstatniIndex = $liczbaZdzarzen-1;
		foreach($zdarzenia as $index=>$zdarzenie) {
			$zdarzenie = (array)$zdarzenie;
			if($zdarzenie['change-type'] == 'now' || $index == $zdarzeniaOstatniIndex) {
				$data = array(
					'id_zdarzenie' => $zdarzenie['row-id'],
					'id_aukcji' => $zdarzenie['item-id'],
					'id_konto' => $this->idKonto,
					'typ' => $zdarzenie['change-type'],
					'data' => date('Y-m-d H:i:s', $zdarzenie['change-date']),
					'price' => $zdarzenie['current-price'],
					'id_sprzedawca' => $zdarzenie['item-seller-id']
				);
				$zdarzeniaObj->save($data);
				
				if($zdarzenie['change-type'] == 'now') {
					$idAukcji[] = $zdarzenie['item-id'];
				}
				
				if($zdarzenie['change-type'] == 'end') {
					$idAukcjiEnd[] = $zdarzenie['item-id'];
				}
			}
		}
		
		$aukcje = array();
		if(count($idAukcji) > 0) {
			$aukcje = $this->getTrwajaceAukcje(array('format_sprzedazy' => self::KUP_TERAZ, 'cron'=>0, 'id_aukcji' => $idAukcji));
		}

		if(count($aukcje) == 0) {
			$this->log('Brak aukcji o formie sprzedaży KupTeraz');
		}else {
			$this->log('Analizujemy trwajace aukcje');
			foreach($aukcje as $aukcja){
				$this->log('id aukcji: ' . $aukcja['id_aukcji']);
				$this->analizujOfertyKupTeraz($aukcja);
				$this->kontrolujMinIloscProduktow($aukcja);
			}
		}

		$aukcje = array();
		$aukcje = $this->getTrwajaceAukcje(array('format_sprzedazy' => self::KUP_TERAZ, 'cron'=>0));

		if(count($aukcje) == 0) {
			$this->log('Brak trwajacych aukcji do kontroli minimalnej ilosci produktow');
		}else {
			$this->log('Kontrolujemy minimalna ilosc produktow');
			foreach($aukcje as $aukcja){
				$this->log('id aukcji: ' . $aukcja['id_aukcji']);
				$this->kontrolujMinIloscProduktow($aukcja);
			}
		}
		
		$aukcje = $this->getAukcjePoCzasieAktywne(array('format_sprzedazy' => self::KUP_TERAZ, 'cron'=>0));
		if(count($aukcje) == 0) {
			$this->log('Brak zakonczonych aukcji o formie sprzedazy KupTeraz');
		}else {
			$this->log('Analizujemy zakonczone aukcje');
			foreach($aukcje as $aukcja){
				$this->log('id aukcji: ' . $aukcja['id_aukcji']);
				$this->analizujOfertyKupTeraz($aukcja);
				$this->kontrolujMinIloscProduktow($aukcja);
				$this->zakonczKupTerazPoDacieZakonczenia($aukcja);
			}
		}
		
		$this->zakonczKupTerazPoDacieZakonczenia();
		if(count($idAukcjiEnd) > 0) {
			$this->zakonczKupTerazRecznie($idAukcjiEnd);
		}
	}
	
	 * 
	 */
	public function updateTransakcje() {
		
		global $_gTables;
		$aTransactions = array();
		$_SESSION['adminID'] = 9996;
		
		$zdarzeniaObj = new Allegro_Transakcja();
		
		$zdarzenia = $this->getOstatnieZdarzeniaTransakcji(2);
		$liczbaZdzarzen = count($zdarzenia);
		$zdarzeniaOstatniIndex = $liczbaZdzarzen-1;
		
		foreach($zdarzenia as $index=>$zdarzenie) {
			$zdarzenie = (array)$zdarzenie;
			if($zdarzenie['deal-event-type'] == 2) {
				
				$formsData = $this->allegroApi->getPostBuyFormsData(array($zdarzenie['deal-transaction-id']));

				$db = Db::getInstance();
				foreach($formsData as $form_obj) {

					$aData = array();
					$auctions_id = array();

					$transakcja		= get_object_vars($form_obj);
					$user_id		= $transakcja['post-buy-form-buyer-id'];
					$aData["uwagi"]	= $transakcja['post-buy-form-msg-to-seller'];

					foreach($transakcja['post-buy-form-items'] as $form_item) {
						$form_item		= get_object_vars($form_item);
						$auctions_id[]	= $form_item['post-buy-form-it-id'];
					}

					//faktura
					if($transakcja['post-buy-form-invoice-option'] == 1) {

						$form_item = get_object_vars($transakcja['post-buy-form-invoice-data']);
						$fv_name = explode(" ",  $form_item['post-buy-form-adr-full-name']);

						$aData["fv_firma"] = $form_item['post-buy-form-adr-company'];
						$aData["fv_nip"] = $form_item['post-buy-form-adr-nip'];
						$aData["fv_imie"] = $fv_name[0];
						$aData["fv_nazwisko"] = $fv_name[1];
						$aData["fv_telefon"] = $form_item['post-buy-form-adr-phone'];
						$aData["fv_ulica"] = $form_item['post-buy-form-adr-street'];
						$aData["fv_nr_dom"] = "";
						$aData["fv_nr_lokal"] = "";
						$aData["fv_miejscowosc"] = $form_item['post-buy-form-adr-city'];
						$aData["fv_kod"] = $form_item['post-buy-form-adr-postcode'];
					}


					//transport
					$form_item = get_object_vars($transakcja['post-buy-form-shipment-address']);
					if($form_item['post-buy-form-adr-country'] != 0) {

						$dost_name = explode(" ",  $form_item['post-buy-form-adr-full-name']);

						$aData["dost_firma"] = $form_item['post-buy-form-adr-company'];
						$aData["dost_imie"] = $dost_name[0];
						$aData["dost_nazwisko"] = $dost_name[1];
						$aData["dost_telefon"] = $form_item['post-buy-form-adr-phone'];
						$aData["dost_ulica"] = $form_item['post-buy-form-adr-street'];
						$aData["dost_nr_dom"] = "";
						$aData["dost_nr_lokal"] = "";
						$aData["dost_miejscowosc"] = $form_item['post-buy-form-adr-city'];
						$aData["dost_kod"] = $form_item['post-buy-form-adr-postcode'];
					}


					foreach($auctions_id as $auction) {

						$db->AutoExecute($_gTables[ALLEGRO_SPRZEDANE], array("id_transakcji"=>$transakcja['post-buy-form-id']), 'UPDATE'," id_aukcji = '".$auction."' AND id_uzytkownika = '".$user_id."'");

						$sql = "SELECT id_zamowienia FROM allegro_sprzedane WHERE id_aukcji = ".$auction." AND id_uzytkownika = '".$user_id."' LIMIT 1 ";

						$row_a = $db->getRow($sql);
						$aData["id"] = $row_a['id_zamowienia'];

						ZamowienieAdmin::updateData($aData);

						//$transporty = $this->allegroApi->getShipmentData();
						//print_r($transporty["shipment-data-list"]);
						//if($aData["id"] == 13)
							//echo "<br />".$transakcja['post-buy-form-shipment-id']."<br />";
						$aTransport = array();
						$aTransport = $this->mapTransportAllegro($transakcja['post-buy-form-shipment-id']); 
						if(!is_array($aTransport) || count($aTransport) < 1) {
							$aTransport["id_transport"] = 0;
							$aTransport["id_platnosc"] = 0;	
						}
						echo "<b>".$transakcja['post-buy-form-pay-type']."</b>";
						if($transakcja['post-buy-form-pay-type'] == 'wire_transfer') {
							$aTransport["id_platnosc"] = 1;	
						}

						ZamowienieAdmin::set($aData["id"], "id_transport", $aTransport["id_transport"]);
						ZamowienieAdmin::set($aData["id"], "id_platnosc", $aTransport["id_platnosc"]);
						ZamowienieAdmin::set($aData["id"], "id_transakcji_allegro", $transakcja['post-buy-form-id']);
						ZamowienieAdmin::set($aData["id"], "wartosc_dostawy_allegro", (float)$transakcja["post-buy-form-postage-amount"]);
						ZamowienieAdmin::set($aData["id"], "id_platnosci_allegro", $transakcja["post-buy-form-pay-id"]);

						if($aTransport["id_transport"] > 0 || $aTransport["id_platnosc"] > 0) {
							$obZam = new ZamowienieAdmin($aData["id"]);
							$obZam->dostawaZapiszZmiany($transakcja["post-buy-form-postage-amount"]);
						}

					}
					$aTransport		= null;
					$auctions_id	= null;
				}
				
				$data = array(
					'id_zdarzenie' => $zdarzenie['deal-event-id'],
					'id_aukcji' => $zdarzenie['deal-item-id'],
					'id_konto' => $this->idKonto,
					'typ' => $zdarzenie['deal-event-type'],
					'id_transakcja' => $zdarzenie['deal-transaction-id'],
					'data' => date('Y-m-d H:i:s', $zdarzenie['deal-event-time']),
					'id_kupujacy' => $zdarzenie['deal-buyer-id']
				);
				$aTransactions[] = $zdarzenie['deal-transaction-id'];
				$zdarzeniaObj->save($data);
			}
		}
		
	}
	
	public function updatePlatnosci($id_konto = 0) {
		
		$_SESSION['adminID'] = 9996;
		
		$zdarzeniaObj = new Allegro_Transakcja();
		
		$zdarzenia = $this->getOstatnieZdarzeniaTransakcji(4);
		$liczbaZdzarzen = count($zdarzenia);
		$zdarzeniaOstatniIndex = $liczbaZdzarzen-1;
		
		foreach($zdarzenia as $index=>$zdarzenie) {
			$zdarzenie = (array)$zdarzenie;
			if($zdarzenie['deal-event-type'] == 4) {
				
				try {
					$payData = $this->allegroApi->getMyIncomingPayments(array("buyer-id" => $zdarzenie['deal-buyer-id'], "item-id" => $zdarzenie['deal-item-id']));
				} catch (SoapFault $e) {
					echo $soapFault->faultstring;
				}
				
				$sql = "SELECT z.id FROM zamowienia z WHERE z.id_transakcji_allegro = ".$zdarzenie['deal-transaction-id'];
				$aList = Db::getInstance()->GetAll($sql);
				
				foreach($payData as $pay_obj) {
					
					$platnosc		= get_object_vars($pay_obj);
					$_SESSION['adminID'] = 9996;

					foreach($aList as $row) {
						$z = new ZamowienieAdmin($row["id"]);
						if($platnosc["pay-trans-status"] == "Zakończona") {
							ZamowienieAdmin::set( $row["id"], "zaplacone", 1 );
							ZamowienieAdmin::set( $row["id"], "zaplacone_kwota", $platnosc["pay-trans-amount"] );

							$compare = bccomp($platnosc["pay-trans-amount"], bcadd($z->data['wartosc'], $z->data['wartosc_dostawy'], 2), 2);
							if($compare == 0) {

								ZamowienieAdmin::ustawPlatnosciPlStatus( $row["id"], 99 );
								ZamowienieAdmin::set( $row["id"], "zaplacone", 1 );
								ZamowienieAdmin::set( $row["id"], "zaplacone_kwota", (float)$platnosc["pay-trans-amount"] );
								ZamowienieAdmin::set( $row["id"], "id_platnosci_allegro", (float)$platnosc["pay-trans-id"] );

								if($row["erp_synchro"] == 1 && $platnosc["pay-trans-incomplete"] == 0) {
									ERPlog::setTask("order", "payment_ok", $row["id"]);
								}
							} elseif($compare == -1) {
								ZamowienieAdmin::ustawPlatnosciPlStatus(  $row["id"], 88 );
							} elseif($compare == 1) {
								ZamowienieAdmin::ustawPlatnosciPlStatus(  $row["id"], 77 );
							}

						} elseif($platnosc["pay-trans-status"] == "Rozpoczęta") {
							ZamowienieAdmin::ustawPlatnosciPlStatus( $row["id"], 4 );
						} elseif($platnosc["pay-trans-status"] == "Anulowana") {
							ZamowienieAdmin::ustawPlatnosciPlStatus( $row["id"], 2 );
						} elseif($platnosc["pay-trans-status"] == "Odrzucona") {
							ZamowienieAdmin::ustawPlatnosciPlStatus( $row["id"], 3 );
						}
					}
				}
			
				$data = array(
						'id_zdarzenie' => $zdarzenie['deal-event-id'],
						'id_aukcji' => $zdarzenie['deal-item-id'],
						'id_konto' => $this->idKonto,
						'typ' => $zdarzenie['deal-event-type'],
						'id_transakcja' => $zdarzenie['deal-transaction-id'],
						'data' => date('Y-m-d H:i:s', $zdarzenie['deal-event-time']),
						'id_kupujacy' => $zdarzenie['deal-buyer-id']
					);
				$zdarzeniaObj->save($data);
			}
		}
		
		
		/* --------------------- STARE ALE DZIAŁAJĄCE ROZWIĄZANIE --------------------- 

		global $_gTables;
		
		$aList= array();

		$sql = "SELECT z.id, z.id_transakcji_allegro, z.erp_synchro, asp.id_uzytkownika, z.id_aukcji FROM zamowienia z
					LEFT JOIN allegro_sprzedane asp ON asp.id_zamowienia = z.id
					WHERE z.id_transakcji_allegro <> 0 AND (z.id_status_platnosci < 99 || z.id_status_platnosci IS NULL) AND z.zaplacone = 0 AND z.id_platnosc = 17";
	
		$aList = Db::getInstance()->GetAll($sql);
		
		foreach($aList as $row) {
			
			if($row["id_aukcji"] > 0) {
				echo $row["id_aukcji"]."<br />";
				try {
					$payData = $this->allegroApi->getMyIncomingPayments(array("buyer-id" => $row['id_uzytkownika']));
				} catch (SoapFault $e) {
					echo $soapFault->faultstring;
				}
				print_r($payData); //, "item-id" => $row["id_aukcji"]
				foreach($payData as $pay_obj) {
					
					$platnosc		= get_object_vars($pay_obj);
					if($platnosc["pay-trans-buyer-id"] == $row["id_uzytkownika"]) {
						
						$_SESSION['adminID'] = 9996;
						
						$z = new ZamowienieAdmin($row["id"]);
						echo $row["id"]."<br />";
						if($platnosc["pay-trans-status"] == "Zakończona") {
							ZamowienieAdmin::set( $row["id"], "zaplacone", 1 );
							ZamowienieAdmin::set( $row["id"], "zaplacone_kwota", $platnosc["pay-trans-amount"] );
							
							$compare = bccomp($platnosc["pay-trans-amount"], bcadd($z->data['wartosc'], $z->data['wartosc_dostawy'], 2), 2);
							if($compare == 0) {
								
								ZamowienieAdmin::ustawPlatnosciPlStatus( $row["id"], 99 );
								ZamowienieAdmin::set( $row["id"], "zaplacone", 1 );
								ZamowienieAdmin::set( $row["id"], "zaplacone_kwota", (float)$platnosc["pay-trans-amount"] );
								
								if($row["erp_synchro"] == 1 && $platnosc["pay-trans-incomplete"] == 0) {
									ERPlog::setTask("order", "payment_ok", $row["id"]);
								}
							} elseif($compare == -1) {
								ZamowienieAdmin::ustawPlatnosciPlStatus(  $row["id"], 88 );
							} elseif($compare == 1) {
								ZamowienieAdmin::ustawPlatnosciPlStatus(  $row["id"], 77 );
							}

						} elseif($platnosc["pay-trans-status"] == "Rozpoczęta") {
							ZamowienieAdmin::ustawPlatnosciPlStatus( $row["id"], 4 );
						} elseif($platnosc["pay-trans-status"] == "Anulowana") {
							ZamowienieAdmin::ustawPlatnosciPlStatus( $row["id"], 2 );
						} elseif($platnosc["pay-trans-status"] == "Odrzucona") {
							ZamowienieAdmin::ustawPlatnosciPlStatus( $row["id"], 3 );
						}
					}
				}
			}
			
		}*/
		
	}
	
	public function mapTransportAllegro($id_allegro_transport) {
		
		$sql = "SELECT id_transport, id_platnosc FROM allegro_map_transport 
				WHERE id_allegro = " . (int)$id_allegro_transport;
	
		$row = Db::getInstance()->GetRow($sql);
		return $row;
		
	}

	public function getOstatnieZdarzenia() {
		$zdarzeniaObj = new Allegro_Zdarzenie();
		return $this->allegroApi->getZdarzenia($zdarzeniaObj->getIdNajnowszeZdarzenie($this->idKonto));
	}
	
	public function getOstatnieZdarzeniaTransakcji($typ = 2) {
		$zdarzeniaObj = new Allegro_Transakcja();
		return $this->allegroApi->doGetJournalDeals($zdarzeniaObj->getIdNajnowszeZdarzenie($this->idKonto, $typ));
	}
	
	public function updateLicytacja() {
		$aukcje = $this->getTrwajaceAukcje(array('format_sprzedazy' => self::AUKCJA, 'cron'=>0));
		if(count($aukcje) == 0) {
			$this->log('Brak aukcji o formie sprzedaży Licytacja');
		}else {
			foreach($aukcje as $aukcja){
				$this->sprawdzIZapiszOfertyLicytacji($aukcja);
			}
		}
		
		$aukcje = $this->getAukcjePoCzasieAktywne(array('format_sprzedazy' => self::AUKCJA, 'cron'=>0));
		if(count($aukcje) == 0) {
			$this->log('Brak zakonczonych aukcji o formie sprzedazy Licytacja');
		}else {
			foreach($aukcje as $aukcja){
				$this->wybierzNajwyzszaOferteLicytacjiIZapiszZamowienie($aukcja);	
			}
		}
	}
	
	/**
	 * Sprawdza oferty
	 * @param array $aukcja
	 */
	private function analizujOfertyKupTeraz($aukcja) {
		
		$oferty = $this->getOferty($aukcja['id_aukcji']);
		
		$allegroInfo = $this->getAllegroInfo($aukcja['id_aukcji']);
		foreach($oferty as $oferta) {
			$oferta = get_object_vars($oferta);
			print_r($oferta);
			$oferta = $oferta['bids-array'];
			if($oferta[1] > 0) {
				$iloscZamowionychWczesniej = $this->getLiczbeProduktowOfertyZapisanej($aukcja['id_aukcji'], $oferta[1]);
				echo "<br />Ilosc zamowionych w tej aukcji przez tego usera: ".$iloscZamowionychWczesniej."<br />";
				if($iloscZamowionychWczesniej < $oferta[5]) {
					$iloscZamowionych = $oferta[5] - $iloscZamowionychWczesniej; 
					$uzytkownik = $this->getUser($aukcja, $oferta); 
					if($uzytkownik != false) {
					echo "próba dodania zamówienia";
						$zamowienie = new Allegro_Cron_Zamowienie();
						$zamowienie->setAukcja($aukcja);
						$zamowienie->setOferta($oferta);
						$zamowienie->setUzytkownik($uzytkownik);
						$zamowienie->setAukcjaInfo($allegroInfo);
						$zamowienie->setIloscProduktow($iloscZamowionych);
						$idZamowienie = $zamowienie->save();
						$this->log('Tworzymy zamówienie(' . $idZamowienie .')');

						$dataSprzedane = array(
							'id_aukcji' => $aukcja['id_aukcji'],
							'id_uzytkownika' => $oferta[1],
							'id_konto' => $aukcja["id_konto"],
							'allegro_login' => $oferta[2],
							'oferta' => $oferta[6],
							'sztuk' => $iloscZamowionych,
							'data_oferty' => date("Y-m-d H:i:s", $oferta[7]),
							'id_zamowienia' => $idZamowienie
						);

						$this->saveSprzedane($dataSprzedane);
						$this->allegro->updateSold($iloscZamowionych, $aukcja['id']);
					}
				}
			}
		}
		$this->updateLiczbeWyswietlen($aukcja['id'], $allegroInfo['it-hit-count']);
	}
	
	private function sprawdzIZapiszOfertyLicytacji($aukcja) {
		$oferty = $this->getOferty($aukcja['id_aukcji']);

		foreach($oferty as $oferta) {
			$oferta = get_object_vars($oferta);
			$oferta = $oferta['bids-array'];
			$uzytkownik = $this->getUser($aukcja, $oferta);
			$ofertaZapisana = $this->getOfertyZapisane($idAllegro, $idUser);
			if($uzytkownik != false) {
				if(count($ofertaZapisana) > 0) {
					$ofertaZapisana = $ofertaZapisana[0];
					if($ofertaZapisana['oferta'] != $oferta[6]) {
						$data = array(
							'id' => $ofertaZapisana['id'],
							'oferta' => $oferta[6],
							'data_oferty' => date("Y-m-d H:i:s", $oferta[7])
						);
						$this->updateSprzedane($data);
					}
				}else{
					$dataSprzedane = array(
						'id_aukcji' => $aukcja['id_aukcji'],
						'id_uzytkownika' => $oferta[1],
						'allegro_login' => $oferta[2],
						'oferta' => $oferta[6],
						'sztuk' => $oferta[5],
						'data_oferty' => date("Y-m-d H:i:s", $oferta[7])
					);
					$this->saveSprzedane($dataSprzedane);
				}
			}
			
			
		}
	}
	
	private function wybierzNajwyzszaOferteLicytacjiIZapiszZamowienie($aukcja) {
		$ofertaNajwyzsza = $this->getNajwyzszaOferte($aukcja['id_aukcji']);
		if(count($ofertaNajwyzsza) > 0) {
			$iloscProduktow = 1;
			$user = $this->getUser($aukcja, array(), true);
			$zamowienie = new Allegro_Cron_Zamowienie();
			$zamowienie->setAukcja($aukcja);
			$zamowienie->setUzytkownik($user);
			$zamowienie->setAukcjaInfo($this->getAllegroInfo($aukcja['id_aukcji']));
			$zamowienie->setCena($ofertaNajwyzsza['oferta']);
			$zamowienie->setIloscProduktow($iloscProduktow);
			$zamowienie->save();
			
			$this->allegro->updateSold($iloscProduktow, $aukcja['id']);
			$this->zakonczAukcje($aukcja['id']);	
		}
	}
	
	/**
	 * Pobiera najwyzsza oferte
	 * @param type $idAukcji
	 * @return array 
	 */
	private function getNajwyzszaOferte($idAukcji) {
		$oferty = Allegro_Sprzedane::getList(
				array(
					'id_aukcji' => $idAukcji
				),
				array(
					'sort'=>'oferta', 
					'order'=>'desc'
				), 
				array(
					'start'=>0, 
					'limit'=>1
				) 
			);
		
		if(count($oferty) == 0) {
			return array();
		}
		
		return $oferty[0];
	}
	
	
	
	/**
	 * Pobiera uzytkownika powiazanego z oferta
	 * @param array $aukcja
	 * @param array $oferta
	 * @param array $first Pobiera pierwszego uzytkownika
	 * @param int $iloscProduktow 
	 */
	private function getUser($aukcja, $oferta = array(), $first = false) {
		$aData = $this->allegroApi->getPostBuyData(array($aukcja['id_aukcji']));
		$aData = get_object_vars($aData[0]);
		
		foreach($aData['users-post-buy-data'] as $user) {
			$user = get_object_vars($user);
			$i = 0;
			
			foreach($user as $row) {
			
				if($first == true) {
					return $row;
				}
				
				if($i == 0) {
					$row = get_object_vars($row);
					if($row["user-id"] == $oferta[1]) {
						return $row;
					}
				}
				$i++;
			}
		} 
		return false;
	}
	
	public function getOferty($idAllegro) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro <= 0) {
			throw new InvalidArgumentException('Podaj nr aukcji');
		}

		return $this->allegroApi->getAuctionBids($idAllegro);
	}
	
	/**
	 * Pobiera trwajace aukcje
	 * @return array 
	 */
	private function getTrwajaceAukcje(array $filtr) {	
		$filtr = (array)$filtr;
		
		$filtrAllegro = array(
			'typ' => 3,
			'id_serwisu' => $this->idSerwis,
		);
		$filtrAllegro = array_merge($filtr, $filtrAllegro);
		return $this->allegro->getList($filtrAllegro);
	}
	
	
	
	/**
	 * Pobiera aktualne dane o aukcji po podaniu nr aukcji
	 * @param int $auctionNumber
	 * @return array 
	 */
	private function getDataAuctionFromAllegro($idAllegro) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro <= 0) {
			throw new InvalidArgumentException('Podaj numer aukcji');
		}
		
		$data = array();
		$data['users'] = $this->allegroApi->getWonUsers($idAllegro);
		$data['bids'] = $this->allegroApi->getAuctionBids($idAllegro);
		$data['info'] = $this->allegroApi->getItemInfo($idAllegro);
		$data['info'] = get_object_vars($data['info']['item-list-info-ext']);
		
		return $data;
	}
	
	private function getAllegroInfo($idAllegro) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro <= 0) {
			throw new InvalidArgumentException('Podaj numer aukcji');
		}
		
		$info = $this->allegroApi->getItemInfo($idAllegro);
		$info = get_object_vars($info['item-list-info-ext']);
		
		return $info;
	}
	
	
	/**
	 * Pobiera liczbę produktow oferty zapisanej
	 * @param int $idAllegro
	 * @param int $idUser
	 * @return int 
	 */
	private function getLiczbeProduktowOfertyZapisanej($idAllegro, $idUser) {
		$liczbaProduktow = 0;
		$oferty = $this->getOfertyZapisane($idAllegro, $idUser);
		if(count($oferty) == 0) {
			return $liczbaProduktow;
		}
		
		foreach($oferty as $oferta) {
			$liczbaProduktow+= $oferta['sztuk'];
		}
		
		return $liczbaProduktow;
	}
	
	/**
	 * Pobiera zapisana oferte
	 * @param int $idAllegro
	 * @param int $idUser
	 * @return type 
	 */
	private function getOfertyZapisane($idAllegro, $idUser) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro <= 0) {
			throw new InvalidArgumentException('Podaj numer aukcji');
		}
		
		$idUser = (int)$idUser;
		if($idUser <= 0) {
			throw new InvalidArgumentException('Podaj id użytkownika');
		}

		$filtr = array(
			'id_uzytkownika' => $idUser,
			'id_aukcji' => $idAllegro,
		);
		
		return Allegro_Sprzedane::getList( $filtr );	
	}
	
	
	/**
	 * Zapisuje oferte
	 * @param array $data 
	 */
	private function saveSprzedane(array $data=array()) {
		if(!is_array($data) && count($data)==0) {
			throw new InvalidArgumentException('Podaj dane do zapisania');
		}
		
		$sprzedane = new Allegro_Sprzedane();
		$sprzedane->saveData($data);
	}
	
	/**
	 * Aktualizuje oferte
	 * @param array $data 
	 */
	private function updateSprzedane(array $data) {
		if(!is_array($data) && count($data)==0) {
			throw new InvalidArgumentException('Podaj dane do aktualizacji');
		}
		
		$sprzedane = new Allegro_Sprzedane();
		$sprzedane->updateData($data);
	}
	
	
	
	/**
	 * Pobiera aukcje po date zakonczenia ale aktywne, czyli flaga zakonczone = 0
	 * @param type $filtr
	 * @return type 
	 */
	private function getAukcjePoCzasieAktywne($filtr) {
		$filtr = (array)$filtr;
		
		$filtrAllegro = array(
			'po_czasie_aktywne' => 1,
			'id_serwisu' => $this->idSerwis
		);
		$filtrAllegro = array_merge($filtr, $filtrAllegro);
		
		return $this->allegro->getList($filtrAllegro);
	} 
	
	private function zakonczAukcje($id) {
		$id = (int)$id;
		if($id <= 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$this->log('zakonczono aukcje(id:' . $id. ')');
		$data = array(
			'flaga_zakonczona' => 1,
			'id' => $id
		);
		
		$this->allegro->updateData($data);
		$this->allegro->zakonczAukcjeInProdukt($id);
	}
	
	public function zakonczAukcjeAllegro($idAukcji) {
		$idAukcji = (int)$idAukcji;
		if($idAukcji <= 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$idsAukcji = array($idAukcji);
		$this->allegroApi->finishItems($idsAukcji);
	}
	

	private function zakonczKupTerazPoDacieZakonczenia($allegro) {
		
		if($allegro) {
			global $_gTables;
			$sql = "UPDATE " . $_gTables['ALLEGRO'] . " 
					SET flaga_zakonczona=1 
					WHERE (data_zakonczenia < NOW() OR sprzedano >=sztuk) 
					AND flaga_zakonczona=0 AND id_rodzaju=" . self::KUP_TERAZ ;

			//if($allegro != 0) {

				$sql.= " AND id = " . (int) $allegro['id'];

				$this->log('zakonczono aukcje(id:' . $allegro['id']. ')');
				Db::getInstance()->Execute($sql);

				$this->allegro->zakonczAukcjeInProdukt($allegro['id']);
			//}
		}
	}
	
	private function zakonczKupTerazRecznie($aAukcje) {
		
		if(count($aAukcje) > 0) {
			
			global $_gTables;
			$aukcje = $this->getTrwajaceAukcje(array('format_sprzedazy' => self::KUP_TERAZ, 'cron'=>0, 'id_aukcji' => $aAukcje));
			
			foreach($aukcje as $row_a) {
				
				if((int)$row_a['id_aukcji'] > 0) {
					$sql = "UPDATE " . $_gTables['ALLEGRO'] . " 
							SET flaga_zakonczona = 1 
							WHERE flaga_zakonczona = 0 AND id_rodzaju = " . self::KUP_TERAZ ;


					$sql.= " AND id_aukcji = " . (int) $row_a['id_aukcji'];

					$this->log('zakonczono aukcje(id:' . $row_a['id']. ')');
					Db::getInstance()->Execute($sql);
				}
				$this->allegro->zakonczAukcjeInProdukt($row_a['id']);
			}
		}
	}
	
	
	private function updateLiczbeWyswietlen($idAukcja, $liczbaWyswietlen) {
		
		$idAukcja = (int)$idAukcja;
		if($idAukcja <= 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$data = array(
			'id' => $idAukcja,
			'wyswietlen' => $liczbaWyswietlen
		);
		$this->allegro->updateData($data);
	} 
	
	/**
	 * Zakaczna aukcje jezeli min ilosc produktow zostala osiagnieta
	 * @param array $aukcja 
	 */
	private function kontrolujMinIloscProduktow($aukcja) {
		if(!is_array($aukcja) || count($aukcja) <=0) {
			throw new InvalidArgumentException('Podaj aukcje');
		}
		
		if($aukcja['alert_mag_sent'] == 0 && $aukcja['alert_mag'] > 0) {
			$minLiczbaProduktow = $aukcja['alert_mag'];
			if($minLiczbaProduktow != null && ($aukcja['sztuk']-$aukcja['sprzedano']) <= $minLiczbaProduktow ) {
				$this->log('Osiagnieto minimalna liczbe produktow(min:' . $minLiczbaProduktow . ', stan magazynowy: ' . ($aukcja['sztuk']-$aukcja['sprzedano']) . ')');
				//$this->zakonczAukcje($aukcja['id']);
				//$this->zakonczAukcjeAllegro($aukcja['id_aukcji']);
				Tresc::wyslijMail( "Alert stanu magazynowego ".$aukcja['id_aukcji'], "tomasz.cisowski@enp.pl,gba@bdsklep.pl,dku@bdsklep.pl", "Osiągnięto minimalną liczbę '".$minLiczbaProduktow."' sztuk dla aukcji '".$aukcja['nazwa_aukcji']."' (numer: ".$aukcja['id_aukcji'].") - aktualna ilość wystawionych sztuk: '".($aukcja['sztuk']-$aukcja['sprzedano'])."'", "", true, "system@bdsklep.pl");
				$obAllegro = new Allegro($aukcja['id']);
				$obAllegro->setValue("alert_mag_sent", 1);
				
			}	
		}
	}
	
	public function getMinProduktWKategorii($idKategoria, $idKonto) {
		
		$idKategoria = (int)$idKategoria;
		$idKonto = (int)$idKonto;

		if($idKategoria > 0 && $id_komto > 0) {
			$kategoriaAllegroObj = new Kategoria_Allegro();
			$kategoriaAllegro = $kategoriaAllegroObj->getByKategoriaAndKontoCheckParent($idKategoria,$idKonto);
			if(empty($kategoriaAllegro)) {
				//$this->log('Ustaw dane allegro dla kategorii (idKategorii: ' . $idKategoria . '; idKonto ' . $idKonto . ')');
				return null;
			}
		} else {
			return null;
		}
		
		return $kategoriaAllegro['koncz_sztuk'];
	}
	
	/**
	 * Pobierz aukcje
	 * @return array 
	 */
	private function getAukcje(array $filtr) {
		return $this->allegro->getList($filtr);
	}
	
	//
	public function log($message) {
		echo "\n" . '---' . $message . '---';
	}
	
}

?>
