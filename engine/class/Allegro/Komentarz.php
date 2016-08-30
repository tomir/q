<?php


class Allegro_Komentarz {
	
	public $id					= 0;
	public $id_uzytkownik			= 0;
	public $odbiorca_id_uzytkownik	= '';
	public $id_typ				= 0;
	public $tresc				= '';
	public $id_aukcja				= 0;
	public $id_komentarz			= 0;
	public $odpowiedz_tresc			= '';
	public $odpowiedz_data			= '';
	public $transakcja_strona		= '';
	public $uzytkownik_nazwa		= '';
	public $punkty_liczba			= 0;
	public $id_kraj				= 0;
	public $id_konto				= 0;
	public $typ_wystawienia			= 0;
	
	public function __construct($id=0) {
		
		if ((int) $id > 0) {
			try {
			
				$sql = "SELECT k.* FROM allegro_komentarze k WHERE k.id = ".$id;

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id					= $row[ 'id' ];
					$this -> id_uzytkownik			= $row[ 'id_uzytkownik' ];
					$this -> odbiorca_id_uzytkownik	= $row[ 'odbiorca_id_uzytkownik' ];
					$this -> id_typ				= $row[ 'id_typ' ];
					$this -> tresc				= $row[ 'tresc' ];
					$this -> id_aukcja				= $row[ 'id_aukcja' ];
					$this -> id_komentarz			= $row[ 'id_komentarz' ];
					$this -> odpowiedz_tresc		= $row[ 'odpowiedz_tresc' ];
					$this -> odpowiedz_data			= $row[ 'odpowiedz_data' ];
					$this -> transakcja_strona		= $row[ 'transakcja_strona' ];
					$this -> uzytkownik_nazwa		= $row[ 'uzytkownik_nazwa' ];
					$this -> punkty_liczba			= $row[ 'punkty_liczba' ];
					$this -> id_kraj				= $row[ 'id_kraj' ];
					$this -> id_konto				= $row[ 'id_konto' ];
					$this -> typ_wystawienia			= $row[ 'typ_wystawienia' ];
					
				}
			}catch (PDOException $e){
				echo "Błąd nie można utworzyć obiektu SzablonGraficzny.";
				return false;
			}
		}
	}
	
	public function getId() {
		return $this->id;
	}

	public function getIdUzytkownik() {
		return $this->id_uzytkownik;
	}
    
	public function getOdbiorcaIdUzytkownik() {
		return $this->odbiorca_id_uzytkownik;
	}
	
	public function getIdTyp() {
		return $this->id_typ;
	}
	
	public function getTresc() {
		return $this->tresc;
	}
	
	public function getIdAukcja() {
		return $this->id_aukcja;
	}
	
	public function getIdKomentarz() {
		return $this->id_komentarz;
	}
	
	public function getOdpowiedzTresc() {
		return $this->odpowiedz_tresc;
	}
	
	public function getOdpowiedzData() {
		return $this->odpowiedz_data;
	}
	
	public function getTransakcjaStrona() {
		return $this->transakcja_strona;
	}
	
	public function getUzytkownikNazwa() {
		return $this->uzytkownik_nazwa;
	}
	
	public function getPunktyLiczba() {
		return $this->punkty_liczba;
	}
	
	public function getIdKraj() {
		return $this->id_kraj;
	}
	
	public function getIdKonto() {
		return $this->id_konto;
	}
	
	public function getTypWystawienia() {
		return $this->typ_wystawienia;
	}

	/**
	 * Zwraca liste komentarzy
	 *
	 * @param int $start	Zawiera limit poczatkowy
	 * @param int $limit	Zawierla ilość elementów do zwrócenia
	 * @return array
	 */
	public function getList($start = 0, $limit = 15) {

		$aResult = array();
		$sql = "SELECT ak.*
				FROM allegro_komentarze ak
				LEFT JOIN allegro a ON a.id_aukcji = ak.id_aukcja
				ORDER BY ak.data ASC LIMIT ".$start.", ".$limit;
				
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				return $aResult;
			 } else return false;
		} catch (Exception $e){
			Log::SLog($e->getTraceAsString());
			return false;
		}
	 }
	 
	 /**
	 * Zapisuje dane w bazie
	 *
	 * @param array $aData	Zawiera tablicę z danymi do zapisania, gdzie indeksami tablicy sa kolumny tabeli
	 * @return bool			Zwraca id ostatnio dodanego rekordu lub false jesli nie udalo sie zapisac
	 */
	  public function save($aData) {

		try {
			if($aData['id'] != 0)
				$res = ConnectDB::subAutoExec ("allegro_komentarze", $aData, "UPDATE", "id = ".$aData['id']);
			else
				$res = ConnectDB::subAutoExec ("allegro_komentarze", $aData, "INSERT");

			if($res)
				return $res;
			else
				return false;
		} catch (PDOException $e){
			 return false;
		}
	}
	

	/**
	 * Generuje ciag SQL filtrujacy wg podanej tablicy $filtr
	 *
	 * @param array $filtr
	 */
	function filterGetList($filtr=NULL) {
		global $_gTables; //Debug($filtr);

		$sql = '';
		if (isset($filtr) && is_array($filtr)) {
			if (isset($filtr['id_konto']) && (int)$filtr['id_konto'] > 0 && is_numeric($filtr['id_konto'])) {
				$sql .= " AND ak.id_konto = '" . (int)$filtr['id_konto'] . "' ";
			}
			if (isset($filtr['id_komentarz']) && (int)$filtr['id_komentarz'] > 0 && is_numeric($filtr['id_komentarz'])) {
				$sql .= " AND ak.id_komentarz = '" . (int)$filtr['id_komentarz'] . "' ";
			}
			if (isset($filtr['typ_wystawienia']) && (int)$filtr['typ_wystawienia'] > 0 && is_numeric($filtr['typ_wystawienia'])) {
				$sql .= " AND ak.typ_wystawienia = '" . (int)$filtr['typ_wystawienia'] . "' ";
			}
		}
		



		return $sql;
	}

	public function delete() {

		if($this->id) {
			 $sql = "DELETE FROM allegro_komentarze WHERE id = ".$this->id;
			try {
				if(ConnectDB::subExec($sql))
					return true;
				else return false;

			}catch (PDOException $e){

				return false;
			}
		} else return false;
	}
	
	/**
	 * Kasuje komentarze kontaa
	 * @global array $_gTables
	 * @param int $idKonto
	 * @return type 
	 */
	public function deleteFromKonto($idKonto) {
		
		$idKonto = (int)$idKonto;
		if($idKonto > 0) {
			try {
				$sql = "DELETE FROM allegro_komentarze WHERE id_konto = ".$idKonto;
				if(ConnectDB::subExec($sql))
					return true;
				else return false;
			}
			catch (Exception $e) {
				Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
				return $e->getCode();
			}
		} else {	
			return false;
		}
	}
	
	
	/**
	 * Sprawdza czy komentarz o podanym id istnieje
	 * @param int $idKomentarz
	 * @return bool
	 */
	public function isCommentExist($idKomentarz) {
		
		$idKomentarz = (int)$idKomentarz;
		if($idKomentarz > 0) {
		
			$obNew = Allegro_Komentarz($idKomentarz);
			if($obNew->getId() > 0) {
				return true;
			}
	
		} else {
			return false;
		}
	}
	
	/**
	 * Pobiera typy komentarzy
	 * @param int $idTyp
	 * @return string 
	 */
	public function getTypeName($idTyp) {
		
		$typy = $this->getType();
		if(!key_exists($idTyp, $typy)) {
			return 'Typ nie został zdefiniowany';
		}
		
		return $typy[$idTyp];
	}
	
	public function getType() {
		return array(
			1 => 'pozytywny',
			2 => 'negatywny',
			3 => 'neutralny'
		);
	}
	
	public function getTypeShortNameKey() {
		
		return array(
			'POS' => 'pozytywny',
			'NEG' => 'negatywny',
			'NEU' => 'neutralny'
		);
	}
	
	
	public function ajaxAddComment($data) {
		$objResponse = new xajaxResponse();

		$htmlRes = SmartyObj::getInstance();
		// api
		try {
			
			$allegroSprzedane = new Allegro_Sprzedane();
			$filtr = array(
				'id' => $data['id']
			);
			$lista = $allegroSprzedane->getList($filtr);
			$data['sprzedane'] = $lista;
			
			$apiResult = $this->addComment($data);
			$apiResult = Allegro::replaceDashInKey($apiResult);
			
			foreach($data['sprzedane'] as $key=>$sprzedane) {
				if($apiResult[$key]['fe_fault_desc'] == '') {
					$this->setDodanoKomentarz($sprzedane['id']);
				}
			}
			
			$htmlRes->assign('apiResult', $apiResult);
			$htmlRes->assign('sprzedane', $lista);

			$objResponse->assign('editTabs', "innerHTML", $htmlRes->fetch('_ajax/allegro/komentarze/add.result.tpl'));
			
		} catch (Exception $e) {
			$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
		}

		return $objResponse;
	}
	
	
	public function addComment($data, AllegroApi $allegroApi = null) {
		//if(!$allegroApi != null && !$allegroApi instanceof AllegroApi) {
			//throw new InvalidArgumentException('Podaj allegroApi');
		//}
		
		if($allegroApi == null){
			$allegroApi = new AllegroApi(1);
		}
		
		$allegroApiKomentarz = new AllegroApi_Komentarz($allegroApi);
		$allegroApiKomentarzDane = new AllegroApi_Komentarz_Dane();
		
		
		$data['typ_odbiorcy'] = 2;
		foreach($data['sprzedane'] as $sprzedane) {
			$allegroApiKomentarzDane->setIdAukcji($sprzedane['id_aukcji']);
			$allegroApiKomentarzDane->setIdOdbiorcy($sprzedane['id_uzytkownika']);
			$allegroApiKomentarzDane->setKomentarz($data['komentarz']);
			$allegroApiKomentarzDane->setOcena($data['ocena']);
			$allegroApiKomentarzDane->setTypOdbiorcy($data['typ_odbiorcy']);
			$allegroApiKomentarz->addDane($allegroApiKomentarzDane);
		}
		$apiResult = $allegroApiKomentarz->addKomentarze();
		return $apiResult;
	}
	
	public function setDodanoKomentarz($idSprzedane) {
		if($idSprzedane == 0) {
			throw new InvalidArgumentException('Podaj id sprzedane');
		}
		
		$data = array(
			'id' => $idSprzedane,
			'komentarz_wystawiony' => 1
		);
		Allegro_Sprzedane::updateData($data);
	}
	
	public function getDataFromAuction($idAllegro) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro == 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$allegro = new Allegro();
		$filtr = array(
			'id_aukcji' => $idAllegro
		);
		$aukcja = $allegro->getList($filtr);
		$aukcja = $aukcja[0];


		$allegroSprzedane = new Allegro_Sprzedane();
		$aukcja['sprzedane'] = $allegroSprzedane->getSprzedaneAukcji($idAllegro);
		
		return $aukcja;
	}
	
	public function getDataFromAuctionWithUserComment($idAllegro, array $idUsers) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro == 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		if(!is_array($idUsers) || count($idUsers) <=0) {
			throw new InvalidArgumentException('Podaj id użytkowników');
		}
		
		$dataAll = $this->getDataFromAuction($idAllegro);
		
		foreach($dataAll['sprzedane'] as $key => $sprzedane) {
			if(!in_array($sprzedane['id_uzytkownika'], $idUsers)) {
				unset($dataAll['sprzedane'][$key]);
			}
		}
		
		return $dataAll;
	}
	
	public function isKomentarzWystawiono($idUzytkownik, $idAukcja) {
		$idUzytkownik = (int)$idUzytkownik;
		if($idUzytkownik == 0) {
			throw new InvalidArgumentException('Podaj id uzytkownika');
		}
		$idAukcja = (int)$idAukcja;
		if($idAukcja == 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$filtr = array(
			'id_aukcji' => $idAukcja,
			'id_uzytkownika' => $idUzytkownik,
			'komentarz_wystawiony' => 0
		);		
		$count = Allegro_Sprzedane::getListQty($filtr);
		
		if($count > 0) {
			return false;
		}
		return true;
	}
		
		
		
		
//	}
	
}



?>
