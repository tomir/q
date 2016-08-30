<?php

/**
 * Do wystawiania aukcji
 *
 * @package Allegro_Sprzedaz
 */
class Allegro_Sprzedaz {
	/**
	 * Obiekt allegro
	 * @var type 
	 */
	private $allegro = null;
	private $allegroApi = null;
	
	/**
	 * Tablica zawierajaca czas trwania gdzie klucz to id allegro czasu a wartosc to ilosc dni
	 * @var type 
	 */
	private $czasTrwania = array();
	/**
	 * Tablica zawierajaca pola allegro glowne
	 * @var type 
	 */
	private $fidGeneralAll = array();
	
	private $idSerwis = null;
	public $idKonto = null;
	
	/**
	 * Konstruktor zapisuje sobie obiekt allegro
	 * @param Allegro $allegro 
	 */
	public function __construct() {
		$this->allegro = new Allegro();
		$this->czasTrwania = $this->allegro->getFidGeneralVal(4);
		$this->fidGeneralAll = $this->allegro->getFidGeneralAll();
	}
	
	/**
	 * Zalacza allegro api (tylko raz sie loguje)
	 * @param type $idSerwis 
	 */
	public function setAllegroApi($idSerwis = 0, $idKonto = 0) {
		$idSerwis = (int)$idSerwis;
		if($idSerwis == 0) {
			throw new InvalidArgumentException('Podaj Id Serwisu');
		}
		
		$this->idSerwis = $idSerwis;
		if($idKonto == 0) {
			$this->setIdKontoByIdSerwis($idSerwis);
		} else {
			$this->idKonto = $idKonto;
		}
	
		// laczy sie z allegro
		$this->allegroApi = new AllegroApi($this->idSerwis, (int)$this->idKonto);
		$this->allegroApi->setFidValidator($this->fidGeneralAll);
	}
	
	public function setIdKontoByIdSerwis($idSerwis) {
		$idSerwis = (int)$idSerwis;
		if($idSerwis == 0) {
			throw new InvalidArgumentException('Podaj Id Serwisu');
		}
		
		$allegroKonto = new Allegro_Konto();
		$this->idKonto = $allegroKonto->getIdKontaByIdSerwisu($idSerwis);
	} 
	
	/**
	 * Sprzedaje ponownie aukcji
	 * @global array $_gTables
	 * @param array $data
	 * @return type apiResult,error
	 */
	public function sprzedazPonowna($idAukcja, $data) {
		global $_gTables;
		
		if($this->allegroApi == null) {
			throw new InvalidArgumentException('Nie można połączyć się z allegro');
		}
		
		$db = Db::getInstance();
		
		$result = array();

		try {
			$aukcja = new Allegro($idAukcja);
			$aukcja->data['id_serwisu'] = $data['id_serwisu'];
			// pobieramy aktualne dane produktu
			$produkt = new Allegro_Produkt($aukcja->data['id_produktu']);
			$aukcja = $produkt->updateProdukt($aukcja, $aukcja->data['id_serwisu']);
			//formularz
			$aukcja->data['fid']['4'] = $data['fid']['4']; // czas trwania


			$aukcja->data['data_cron'] = '';
			if (trim($data['data_cron']) != '' && trim($data['data_cron_time']) != '' && $data['czas_wyswietlenia'] == 1) {
				$aukcja->data['data_cron'] = $data['data_cron'] . ' ' . $data['data_cron_time'] . ':00';
				$apiResult['item-id'] = 0;
				$apiResult['item-info'] = '';
			}else{
				// wystawiamy aukcje
				$apiResult = $this->wystawNaAllegro($aukcja);
			}
			
			// i zapisujemy do bazy
			$aukcja->data['id_aukcji'] = $apiResult['item-id'];
			$this->zapiszAukcje($aukcja);
			
			return $apiResult;

		} catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/**
	 * Sprzedaje aukcji
	 * @global array $_gTables
	 * @param int $idProdukt
	 * @param array $data
	 * @return type apiResult,error
	 */
	public function sprzedaz($data) {
		$result = array();

		try {
			
			// tworzymy nowa aukcje na bazie produktu
			$aukcja = new Allegro();
			$aukcja->data = $data;			
			foreach($this->getSzablonDostawyFids($data['id_szablon_dostawy']) as $fid=>$val) {
				$aukcja->data['fid'][$fid] = $val;
			}
			
			//zamiana encji na znaki
			$aukcja->data['fid'][1] = html_entity_decode($aukcja->data['fid'][1]);

			$apiResult = $this->wystawNaAllegro($aukcja);
			
			// i zapisujemy do bazy
			$aukcja->data['id_aukcji'] = $apiResult['item-id'];
			$this->zapiszAukcje($aukcja);

			return $apiResult;

		} catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function wystawNaAllegro(Allegro $aukcja) {
		if($this->allegroApi == null) {
			mail("tomasz.cisowski@enp.pl", "Nie można połączyć się z allegro", "");
			throw new InvalidArgumentException('Nie można połączyć się z allegro');
		}
		
		if(!$aukcja instanceof Allegro) {
			throw new InvalidArgumentException('Podaj aukcję do wystawienia');
		}
		
		$allegro = $this->zostawTylkoGlowneZdjecieWAukcji($aukcja);
		
		// wystawiamy aukcje
		return $this->allegroApi->sell($aukcja->data);
	}
	
	private function zostawTylkoGlowneZdjecieWAukcji(Allegro $aukcja) {
		if(!$aukcja instanceof Allegro) {
			throw new InvalidArgumentException('Podaj aukcję do wystawienia');
		}
		
		if(isset($aukcja->data['photos']) && array_key_exists(0, $aukcja->data['photos'])) {
			$aukcja->data['photos'] = array(
				$aukcja->data['photos'][0]
			);
		}
		
		return $aukcja;
	}
	
	
	public function getSzablonDostawyFids($idSzablonDostawy = 0) {
		$idSzablonDostawy = (int)$idSzablonDostawy;
		
		$fid = array();
		
		if ($idSzablonDostawy > 0) {
			$szablonDostawy = new Allegro_SzablonDostawy($idSzablonDostawy);
			$fid = $szablonDostawy->data['fid'];
		}
		
		return $fid;
	}
	
	/**
	 * Zapisz aukcje do bazy
	 * @global array $_gTables
	 * @param Allegro $aukcja
	 * 
	 * @return bool 
	 */
	public function zapiszAukcje(Allegro $aukcja, $dataWystawienia = null) {
		global $_gTables;

		if($this->idKonto == null) {
			throw new InvalidArgumentException('Podaj id konta');
		}
		
		try {
			$db = Db::getInstance();

			$teraz = time();
			if($dataWystawienia !== null) {
				$teraz = strtotime($dataWystawienia);
			}
			
			// no i zapisujemy
			unset($aukcja->data['aukcja']);
			unset($aukcja->data['objProduct']);

			$aukcja->data['fid'][24] = base64_encode($aukcja->data['fid'][24]);
			$serialize = serialize($aukcja->data);
			$data = array(
						'id_aukcji' => (int) $aukcja->data['id_aukcji'],
						'id_serwisu' => (int) $aukcja->data['id_serwisu'],
						'id_produktu' => (int) $aukcja->data['id_produktu'],
						'aukcja' => $serialize,
						'id_rodzaju' => (int) $aukcja->data['id_rodzaju'],
						'auto_renew' => (int) $aukcja->data['fid'][30],
						'nazwa_aukcji' => $aukcja->data['fid'][1],
						'data_wystawienia' => date("Y-m-d H:i:s", $teraz),
						'data_zakonczenia' => date("Y-m-d H:i:s", $teraz + ($this->czasTrwania[$aukcja->data['fid'][4]] * 86400)),
						'sztuk' => $aukcja->data['fid'][5], 
						'm_kto' => $_SESSION['adminID'],
						'cena_kup_teraz' =>$aukcja->data['fid'][8],
						'id_konto' => $this->idKonto);
			
			if(isset($aukcja->data['data_cron']) && !empty($aukcja->data['data_cron'])) {
				$data['data_cron'] = $aukcja->data['data_cron'];
			}
			
			$this->allegro->saveData($data);
			$this->addAukcjaToProduktNakladka($aukcja, $this->allegro->id);
			return true;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
		
	}
	
	private function addAukcjaToProduktNakladka(Allegro $aukcja, $idAllegro) {
		
		if(!$aukcja instanceof Allegro) {
			throw new InvalidArgumentException('Podaj aukcje');
		}
		
		
		$idAllegro = (int)$idAllegro;
		if($idAllegro <= 0) {
			throw new InvalidArgumentException('Podaj id allegro');
		}

		$data = array(
			'id_nakladki' => $this->getProduktNakladkaIdByAukcja($aukcja),
			'id_allegro' => $idAllegro,
			'aukcja_zakonczona' => 0
		);
	
		Produkt::updateNakladkaData($data);
	}
	
	private function getProduktNakladkaIdByAukcja(Allegro $aukcja){
		
		if(!$aukcja instanceof Allegro) {
			throw new InvalidArgumentException('Podaj aukcje');
		}
		
		global $_gTables;
		
		$sql = "SELECT id_nakladki FROM " . $_gTables['PRODUKTY_NAKLADKA'] . " WHERE id_produktu=" . $aukcja->data['id_produktu'] . " AND id_serwisu=" . $aukcja->data['id_serwisu'];
		$row = Db::getInstance()->GetCol($sql);
		
		if(count($row) > 0) {
			return $row[0];
		}
		return 0;
	} 
	
	
	

}



?>