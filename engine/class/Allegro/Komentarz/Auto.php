<?php


class Allegro_Komentarz_Auto {

	
	public function __construct($id=0) {
		
		if ((int) $id > 0) {
			try {
			
				$sql = "SELECT aka.* FROM allegro_komentarz_auto aka WHERE aka.id = ".$id;

				$aResult = ConnectDB::subQuery($sql, "fetch");
				if(!is_array($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id				= $row[ 'id' ];
					$this -> aktywny			= $row[ 'aktywny' ];
					$this -> komentarz		= $row[ 'komentarz' ];
					$this -> typ				= $row[ 'typ' ];
					$this -> id_konto			= $row[ 'id_konto' ];
					
				}
			}catch (PDOException $e){
				Log::SLog($e->getTraceAsString());
				echo "Błąd nie można utworzyć obiektu Allegro_Konto.";
				return false;
			}
		}
	}
	
	public function getId() {
		return $this->id;
	}

	public function getAktywny() {
		return $this->aktywny;
	}
    
	public function getKomentarz() {
		return $this->komentarz;
	}
	
	public function getTyp() {
		return $this->typ;
	}
	
	public function getIdKonto() {
		return $this->id_konto;
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
		$sql = "SELECT aka.*
				FROM allegro_komentarz_auto aka
				ORDER BY aka.id_konto ASC LIMIT ".$start.", ".$limit;
				
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
				$res = ConnectDB::subAutoExec ("allegro_komentarz_auto", $aData, "UPDATE", "id = ".$aData['id']);
			else
				$res = ConnectDB::subAutoExec ("allegro_komentarz_auto", $aData, "INSERT");

			if($res)
				return $res;
			else
				return false;
		} catch (Exception $e){
			 Log::SLog($e->getTraceAsString());
			 return false;
		}
	}
}

?>