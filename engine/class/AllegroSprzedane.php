<?php

/**
 * Klasa obslugujaca pojedynczy sprzedany przedmiot w aukcjach allegro.pl
 *
 * @package AllegroSprzedane
 */

global $db;
$db = Db::getInstance();

class AllegroSprzedane extends ObjectAjax
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
		global $_gTables, $db;

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
		global $_gTables, $db;

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
		global $_gTables, $db;

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
		global $_gTables, $db;

		$sql = "SELECT
					asprz.*
					FROM $_gTables[ALLEGRO_SPRZEDANE] asprz
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
		global $_gTables, $db;

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
			if( isset($filtr['id_aukcji']) && is_numeric($filtr['id_aukcji']) )
				$sql.= " AND asprz.id_aukcji = '".(int)$filtr['id_aukcji']."'";

			if( isset($filtr['id_uzytkownika']) && is_numeric($filtr['id_uzytkownika']) )
				$sql.= " AND asprz.id_uzytkownika = '".(int)$filtr['id_uzytkownika']."'";

		}

		return $sql;
	}



}




?>