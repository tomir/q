<?php

/**
 * Klasa obslugujaca wystawianie i pobieranie aukcji allegro.pl
 *
 * @package Allegro
 */
class Allegro_Konto {

	private $aktywny = '';
	private $id = 0;
	private $login = '';
	private $apikey = 0;
	private $apikey_date = '';
	private $id_country = 0;
	private $saldo = 0;
	
	public function __construct($id=0) {
		
		if ((int) $id > 0) {
			try {
			
				$sql = "SELECT k.* FROM " . MyConfig::getValue("__allegro_konto") . " k WHERE k.id = ".$id;

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id				= $row[ 'id' ];
					$this -> login			= $row[ 'login' ];
					$this -> apikey			= $row[ 'apikey' ];
					$this -> apikey_date		= $row[ 'apikey_date' ];
					$this -> aktywny			= $row[ 'aktywny' ];
					$this -> id_country		= $row[ 'id_country' ];
					$this -> saldo			= $row[ 'saldo' ];
					
				}
			}catch (PDOException $e){
				echo "Błąd nie można utworzyć obiektu Allegro_Konto.";
				return false;
			}
		}
	}
	
	public function getId() {
		return $this->id;
	}

	public function getLogin() {
		return $this->login;
	}
    
	public function getApiKey() {
		return $this->apikey;
	}
	
	public function getApiKeyDate() {
		return $this->apikey_date;
	}
	
	public function getAktywny() {
		return $this->aktywny;
	}
	
	public function getIdCountry() {
		return $this->id_country;
	}
	
	public function getSaldo() {
		return $this->saldo;
	}
	
	/**
	 * Zwraca liste kont
	 *
	 * @param int $start	Zawiera limit poczatkowy
	 * @param int $limit	Zawierla ilość elementów do zwrócenia
	 * @return array
	 */
	public function getList($start = 0, $limit = 15) {

		$aResult = array();
		$sql = "SELECT ak.*
				FROM " . MyConfig::getValue("__allegro_konto") . " ak
				ORDER BY ak.login ASC LIMIT ".$start.", ".$limit;
				
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				return $aResult;
			 } else return false;
		} catch (Exception $e){
			//Log::SLog($e->getTraceAsString());
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
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__allegro_konto"), $aData, "UPDATE", "id = ".$aData['id']);
			else 
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__allegro_konto"), $aData, "INSERT");
			
			if($res)
				return $res;
			else
				return false;
		} catch (PDOException $e){
			 return false;
		}
	}

	/**
	 * Ustawia konto aktywne jeżeli może się połączyć z allegro
	 * @param int $id
	 * @param int $id_serwisu
	 * @return type 
	 */
	static public function setKontoAktywne($id, $id_serwisu = 1) {
		
		$id = (int)$id;
		if($id > 0) {
		
			$aData = array('aktywny' => 1);

			// probujemy sie zalogowac
			try {
				$allegroApi = new AllegroApi($id_serwisu, $id);
			} catch(Exception $e) {
				// nie może się zalogować
				$aData['aktywny'] = 0;
			}

			try {
				ConnectDB::subAutoExec (MyConfig::getValue("__allegro_konto"), $aData, "UPDATE", "id = ".$id);
				return true;
			} catch (Exception $e) {
				Common::log(__CLASS__.'::'.__METHOD__,'Błąd przy aktywowaniu konta'."\n".$e->getMessage());
				return $e->getCode();
			}
		}
	}
	
	
	static function getListSelect()
	{
		$list = self::getList();
		$tabData = array();
		
		foreach($list as $item) {
			$tabData[$item['id']] = $item['login'];
		}
		
		return $tabData;
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
			if (isset($filtr['id_serwisu']) && (int)$filtr['id_serwisu'] > 0 && is_numeric($filtr['id_serwisu'])) {
				$sql .= " AND ak.id_serwisu = '" . $filtr['id_serwisu'] . "' ";
			}
			if (isset($filtr['id']) && (int)$filtr['id'] > 0 && is_numeric($filtr['id'])) {
				$sql .= " AND ak.id = " . (int)$filtr['id'];
			}
			if (isset($filtr['aktywny']) && (int)$filtr['aktywny'] > 0 && is_numeric($filtr['aktywny'])) {
				$sql .= " AND ak.aktywny = " . (int)$filtr['aktywny'];
			}
		}
		



		return $sql;
	}

	public function delete() {

		if($this->id) {
			 $sql = "DELETE FROM " . MyConfig::getValue("__allegro_konto") . " WHERE id = ".$this->id;
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
	 * Aktualizuje saldo konta
	 * @param int $idSerwis 
	 * @param int $idSerwis 
	 */
	public function updateSaldo($idSerwis, $idKonto) {
		
		$idSerwis = (int)$idSerwis;
		if($idSerwis == 0) {
			$idSerwis = 1;
		}
		
		$idKonto = (int)$idKonto;
		if($idKonto > 0) {

			$allegroApi = new AllegroApi($idSerwis, $idKonto);
			$saldo = $allegroApi->getMyBilling();
			
			$aData = array();
			$aData = array(
				'id' => $idKonto,
				'saldo' => $this->cleanPriceFromAllegro($saldo)
			);
			
			$this->save($aData);
		}
	}
	
	private function cleanPriceFromAllegro($cena) {
		return str_replace(array(' ', ','), array('', '.'), $cena);
	}
//
//	/**
//	 * Zapisuje podane w tablicy dane (w razie potrzeby jesli id==0 tworzony jest nowy wpis)
//	 *
//	 * @param array $formData
//	 * @return xajaxResponse
//	 */
//	function ajaxAdminEditItem($formData) {
//		$objResponse = new xajaxResponse();
//
//		if (!is_array($formData) || count($formData) == 0)
//			return $objResponse;
//
//		try {
//
//		} catch(Exception $e) {
//			$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
//			$objResponse->script("document.getElementById('btnWystaw').innerHTML = 'Wystaw';
//								  document.getElementById('btnWystaw').disabled = false;");
//		}	
//
//		return $objResponse;
//	}
//
//
//	public function ajaxTab0() {
//		global $serwisy;
//
//		$htmlRes = SmartyObj::getInstance();
//		$htmlRes->assign('obj', $this);
//
//		return $htmlRes->fetch('_ajax/allegro_konto.edit.tab0.tpl');
//	}
//
//	function ajaxGetEditResultHTML($item_id) {
//		$htmlRes = SmartyObj::getInstance();
//		return $htmlRes->fetch('_ajax/allegro_konto.edit.result.tpl');
//	}
	
	/**
	 * Pobiera id serwisu
	 * @global array $_gTables
	 * @param int $idKonto
	 * @return int 
	 */
	public function getIdSerwisuByIdKonta($idKonto) {
		
		$idKonto = (int)$idKonto;
		if($idKonto > 0) {
		
			$sql = "SELECT id_serwisu FROM " . MyConfig::getValue("__allegro_konto") . " WHERE id=" . $idKonto;
			$aResult = ConnectDB::subQuery($sql, "fetch");
			$idSerwis = $aResult['id_serwisu'];

			return $idSerwis;
		} else {
			return false;
		}
	}
	
	/**
	 * Pobiera idKonta na bazie idserwisu
	 * @global array $_gTables
	 * @param type $idSerwis
	 * @return type 
	 */
	static public function getIdKontaByIdSerwisu($idSerwis) {

		$idSerwis = (int)$idSerwis;
		if($idSerwis > 0) {

			$sql = "SELECT id FROM " . MyConfig::getValue("__allegro_konto") . " WHERE id_serwisu=" . $idSerwis;
			$aResult = ConnectDB::subQuery($sql, "fetch");
			$idKonto = $aResult['id'];

			return $idKonto;
		} else {
			return false;
		}
		
		
	}
}

?>