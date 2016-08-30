<?php

class Allegro_SzablonDostawy  {

	protected $id;
	protected $nazwa;
	protected $fid;

	public function __construct($id=0) {
		
		if ($id > 0) {
			try {

				$sql = "SELECT sd.* FROM " . MyConfig::getValue("__allegro_szablony_dostawy") . " sd WHERE sd.id = ".$id;

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id			= $row['id'];
					$this -> nazwa		= $row['nazwa'];
					
					$sql = "SELECT * FROM " . MyConfig::getValue("__allegro_szablony_dostawy_wartosci") . " WHERE id_szablonu=" . $this->id;
					$wartosci = ConnectDB::subQuery($sql);
		
					$this -> fid = array();
					foreach($wartosci as $wartosc) {
						
						if(!array_key_exists($wartosc['fid'], $this -> fid)) {
							$this -> fid[$wartosc['fid']] = array();
						}
						$this -> fid[$wartosc['fid']][] = $wartosc['wartosc'];
					}
				}
			}catch (Exception $e){
				echo "Błąd nie można utworzyć obiektu SzablonDostawy.";
				return false;
			}
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getNazwa() {
		return $this->nazwa;
	}
    
	public function getFid() {
		return $this->fid;
	}
	
	/**
	 * Zwraca liste szablonów graficznych
	 *
	 * @param int $start	Zawiera limit poczatkowy
	 * @param int $limit	Zawierla ilość elementów do zwrócenia
	 * @return array
	 */
	public function getList($start = 0, $limit = 15) {

		$aResult = array();
		$sql = "SELECT sd.*
				FROM " . MyConfig::getValue("__allegro_szablony_dostawy") . " sd
				ORDER BY sd.nazwa ASC LIMIT ".$start.", ".$limit;
				
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				return $aResult;
			 } else return false;
		} catch (Exception $e){
			echo $e->getTraceAsString();
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
			if($thi ->id != 0) {
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__allegro_szablony_dostawy"), $aData, "UPDATE", "id = ".$aData['id']);
			}
			else {
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__allegro_szablony_dostawy"), $aData, "INSERT");
				$this->id = $res;
			}
			// dodajemy wartosci do szablonu
			if (isset($aData['fid']) && is_array($aData['fid']) && count($aData['fid']) > 0) {
				$this->addWartosci($aData['fid'], $this->id);
			}
			
			if($res)
				return $res;
			else
				return false;
		} catch (Exception $e){
			 return false;
		}
	}

	private function addWartosci(array $wartosci, $idSzablonu = 0) {

		$idSzablonu = (int) $idSzablonu;
		if ($idSzablonu == 0) {
			throw new InvalidArgumentException('Podaj id szablonu przy dodawaniu wartości');
		}
		
		//kasujemy stare
		$sql = "DELETE FROM " . MyConfig::getValue("__allegro_szablony_dostawy_wartosci") . " WHERE id_szablonu = " . $idSzablonu;
		ConnectDB::subExec($sql);

		//dodajemy nowe
		$sqlVal = '';
		$coma = '';
		foreach ($wartosci as $fid => $wartosc) {
			if (is_array($wartosc)) {
				$wartosci2 = $wartosc;
				foreach ($wartosci2 as $wartosc2) {
					$wartosc2 = trim($wartosc2);
					if (!empty($wartosc)) {
						$sqlVal.= $coma . "(" . $idSzablonu . " ," . (int) $fid . ", '" . $wartosc2 . "')";
						$coma = ',';
					}
				}
			} else {
				$wartosc = trim($wartosc);
				if (!empty($wartosc)) {
					$sqlVal.= $coma . "(" . $idSzablonu . " ," . (int) $fid . ", '" . $wartosc . "')";
					$coma = ',';
				}
			}
		}

		if (!empty($sqlVal)) {
			$sql = "INSERT INTO " . MyConfig::getValue("__allegro_szablony_dostawy_wartosci") . " (id_szablonu, fid, wartosc) VALUES" . $sqlVal;
			ConnectDB::subExec($sql);
		}
		return $sql;
	}

	
	/**
	 * Usuwa dane z bazy
	 *
	 * @param int $id
	 * @return bool,errorCode
	 */
	public function deleteData( $id )
	{
		try {

			$sql = "DELETE FROM " . MyConfig::getValue("__allegro_szablony_dostawy") . " WHERE id = " . (int)$id;
			ConnectDB::subExec($sql);
			
			// i usuwamy wartości powiązane z szablonem
			$sql = "DELETE FROM " . MyConfig::getValue("__allegro_szablony_dostawy_wartosci") . " WHERE id_szablonu = " . (int)$id;
			ConnectDB::subExec($sql);

			return true;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return $e->getCode();
		}
	}
	
	
	
	static public function getListSelect($filtr=NULL, $sort = NULL, $limit = NULL) {
		// zamieniamy id na klucze
		$return = array();
		foreach(self::getList($filtr, $sort, $limit) as $val) {
			$return[$val['id']] = $val['nazwa']; 
		}
			
		return $return;
	}
	
	/**
	 * Sprawdza czy fid istnieje w fid dostawy
	 */
	public function checkFidDostawyExist(array $fids, $index) {
		$fidDostawa = Allegro::getFidDostawa();	
		if(isset($fidDostawa[$index])) {
			foreach($fidDostawa[$index]['fid'] as $fid) {
				if(array_key_exists($fid, $fids)) {			
					return true;
				}
			}
		}
		return false;
		
	}

}

?>