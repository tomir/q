<?php

/**
 * Klasa obslugujaca pojedynczy sprzedany przedmiot w aukcjach allegro.pl
 *
 * @package AllegroSprzedane
 */

global $db;
$db = Db::getInstance();

class Allegro_Sprzedane extends ObjectAjax
{
	var $id;
	var $data;


	/**
	 * Pobiera dane obiektu i zwraca w postaci tablicy
	 *
	 * @return array
	 *
	 */
	function getData()
	{
		global $_gTables;

		$db = Db::getInstance();
		
		$sql = "SELECT asprz.*
				FROM $_gTables[ALLEGRO_SPRZEDANE] asprz
				WHERE asprz.id = '".(int)$this->id."'";

		$row = $db->GetRow( $sql );

		if ( $row===false )
			Common::log( __METHOD__, $sql );

		//Debug($row);

		return $row;
	}
	/**
	 * Zapisuje obiekt w bazie, zmienna $id obiektu bedzie zawierac ID rekordu z bazy jesli zapis sie powiedzie. Jesli klucz id w przekazywanej tablicy bedzie wiekszy od 0 wtedy wykonana zostanie aktualizacja istniejacego rekordu
	 *
	 * @param array $dane
	 * @return bool	Zwraca true jesli udalo sie zapisac obiekt (zmienna $id zawiera wtedy ID rekordu z bazy) lub false jesli nie udalo sie zapisac
	 */
	function saveData( $data )
	{
		global $_gTables;
		
		$db = Db::getInstance();

		if( (int)$data['id'] > 0 )
		{
			return $this->updateData( $data );
		}

		$data = Common::clean_input($data);

		$db->StartTrans();

		$sql = $db->GetInsertSQL( $_gTables[ALLEGRO_SPRZEDANE], $data );
		$db->Execute( $sql );

		if( $db->CompleteTrans() )
		{
			$this->id = $db->Insert_ID();
			return true;
		}else{
			Common::log( __METHOD__, $sql );
		}
		return false;
	}
	/**
	 * Aktualizuje dane obiektu w bazie. Tablica wejsciowa musi zawierac klucz 'id'
	 *
	 * @param array $dane
	 * @return bool
	 */
	static function updateData( $data )
	{
		global $_gTables;
		
		$db = Db::getInstance();

		if( !isset($data['id']) || !is_numeric($data['id']) || (int)$data['id'] <= 0 )
			return false;

		$data = Common::clean_input($data);

		$sql = "SELECT * FROM $_gTables[ALLEGRO_SPRZEDANE] WHERE id='".$data['id']."'";
		$result = $db->Execute($sql);
		if( $result===false )
		{
			Common::log( __METHOD__, $sql);
			return false;
		}

		$sql = $db->GetUpdateSQL( $result, $data, true );

		$db->StartTrans();

		$result = $db->Execute($sql);

		if ( $db->CompleteTrans() )
		{
			return true;
		}else{
			Common::log( __METHOD__, $sql );
		}
		return false;
	}
	/**
	 * Zwraca liste obiektow odfiltrowana wg tablicy $filtr, posortowana wg tablicy $sort oraz w przedziale podanym w tablicy $limit
	 *
	 * @param array $filtr	Tablica asocjacyjna filtrujaca w postaci $filtr['pole'] = 'wartosc'
	 * @param array $sort	Tablica asocjacyjna okreslajaca sortowanie, musi zawierac klucz 'sort' okreslajacy po ktorym polu sortujemy oraz klucz 'order' okreslajacy kolejnosc sortowania (ASC/DESC)
	 * @param array $limit	Tablica asocjacyjna okreslajaca przedzial zwracanych wynikow, musi zawierac klucz 'start' okreslajacy od ktorego rekordu pobieramy wyniki oraz klucz 'limit' okreslajacy ile rekordow ma zostac zwroconych
	 * @return array
	 */
	function getList( $filtr=NULL, $sort = NULL, $limit = NULL )
	{
		global $_gTables;
		
		$db = Db::getInstance();
		
		$sql = "SELECT a.nazwa_aukcji, a.id_serwisu,
					asprz.*
					FROM $_gTables[ALLEGRO_SPRZEDANE] asprz 
						JOIN " . $_gTables['ALLEGRO'] ." a ON a.id_aukcji = asprz.id_aukcji
					WHERE 1 ";

		$sql.= self::filterGetList($filtr);

		$sql.= self::getSort($sort);

		$sql.= self::getLimit($limit);
		$all = $db->getAll($sql); // Debug($sql);
	
		if ( $all===false )
		{
			Common::log( __METHOD__, $sql );
		}

		return $all;
	}
	
	/**
	 * Zwraca ilosc wszystkich pozycji odpowiadajacych podanemu filtrowi $filtr z pominieciem ograniczen $limit
	 *
	 * @param array $filtr
	 * @return int
	 */
	function getListQty( $filtr )
	{
		global $_gTables;
		
		$db = Db::getInstance();

		$sql = "SELECT COUNT(asprz.id) AS ilosc FROM $_gTables[ALLEGRO_SPRZEDANE] asprz WHERE 1 ";

		if( isset($filtr) && is_array($filtr) )
		{
			$sql.= self::filterGetList($filtr);
		}

		$val = $db->getOne($sql);
		
		if ( $val===false )
		{
			return 0;
		}else{
			return (int)$val;
		}

		return 0;
	}
	/**
	 * Generuje ciag SQL filtrujacy wg podanej tablicy $filtr
	 *
	 * @param array $filtr
	 */
	function filterGetList($filtr=NULL)
	{
		global $_gTables; //Debug($filtr);

		$sql = '';

		if( isset($filtr) && is_array($filtr) )
		{
			if( isset($filtr['id_aukcji']) && is_numeric($filtr['id_aukcji']) && (int)$filtr['id_uzytkownika']>0)
				$sql.= " AND asprz.id_aukcji = '".(int)$filtr['id_aukcji']."'";

			if( isset($filtr['id_uzytkownika']) && is_numeric($filtr['id_uzytkownika']) && (int)$filtr['id_uzytkownika']>0)
				$sql.= " AND asprz.id_uzytkownika = '".(int)$filtr['id_uzytkownika']."'";
			
			if( isset($filtr['komentarz_wystawiony']) && is_numeric($filtr['komentarz_wystawiony']))
				$sql.= " AND asprz.komentarz_wystawiony = '".(int)$filtr['komentarz_wystawiony']."'";
			
			if( isset($filtr['id']) ) {
				if(is_array($filtr['id'])) {
					$sql.= " AND (asprz.id='" . implode("' OR asprz.id='", $filtr['id']) . "')";
				}else{
					if(is_numeric($filtr['id']) && (int)$filtr['id']>0) {
						$sql.= " AND asprz.id = '".(int)$filtr['id']."'";
					}
				}
			}
			
			if( isset($filtr['id_transakcji']) && $filtr['id_transakcji'] == 0)
				$sql.= " AND asprz.id_transakcji = 0";
		}

		return $sql;
	}
	
	/**
	 * Pobiera sprzedane aukcji
	 * @param int $idAllegro
	 * @return array 
	 */
	public function getSprzedaneAukcji($idAllegro) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro == 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$filtr = array(
			'id_aukcji' => $idAllegro
		);
		
		return $this->getList($filtr);
	}

	/**
	 * Do masowej aktualizacji wystawienie komentarza forma $sprzedazParamMulti = array( array('id_uzytkownika'=>12312312,'id_aukcji'=>23423423), array('id_uzytkownika'=>12312312,'id_aukcji'=>23423423) )
	 * @global type $_gTables
	 * @param array $sprzedazParam
	 * @return type 
	 */
	public function setWystawionoKomentarzMulti(array $sprzedazParamMulti) {		
		global $_gTables;
		
		$data = array(
			'komentarz_wystawiony' => 1
		);
		
		$where = '';
		$or = '';
		foreach($sprzedazParamMulti as $sprzedazParam) {
			$where.= $or . http_build_query($sprzedazParam, '', ' AND ');
			$or = ' OR ';
		}
	
		if(empty($where)) {
			return false;
		}
		
		$db = Db::getInstance();
		try {
			$db->StartTrans();
			$db->AutoExecute($_gTables['ALLEGRO_SPRZEDANE'], $data, 'UPDATE', $where);
			$db->CompleteTrans();
			return true;
		} catch(Exception $e) {
			$db->RollbackTrans();
			Common::log( __METHOD__, $e);
			return false;
		}
	}



}




?>