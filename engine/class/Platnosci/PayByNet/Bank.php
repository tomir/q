<?php

namespace Platnosci\PayByNet;

class Bank extends \Object 
{
	public $id;
	public $data;

	/**
	 * Pobiera dane obiektu i zwraca w postaci tablicy
	 *
	 * @return array
	 *
	 */
	function getData()
	{
		global $_gTables;
	
		$db = \Db::getInstance();
		$sql = "SELECT a.*
				FROM $_gTables[PLATNOSCI_BANKI] a
				WHERE a.id = '".(int)$this->id."'";

		$row = $db->GetRow( $sql );	
		return $row;
		
	}
	/**
	 * Zapisuje nowe dane do bazy
	 *
	 * @param array $dane
	 * @return true lub ExceptionCode
	 */
	function saveData( $data ) {
		
		global $_gTables;
		
		$db = \Db::getInstance();
		if( (int)$data['id'] > 0 )
			return self::updateData($data);

		if (class_exists('Common')) {
			$data = \Common::clean_input($data);
		}
		
		$db->AutoExecute($_gTables[PLATNOSCI_BANKI],$data,'INSERT');

		return true;
		
	}
	/**
	 * Aktualizuje dane obiektu w bazie. Tablica wejsciowa musi zawierac klucz 'id'
	 *
	 * @param array $dane
	 * @return true lub ExceptionCode
	 */
	static function updateData( $data )
	{
		global $_gTables;
	
		$db = \Db::getInstance();

		if (class_exists('Common')) {
			$data = \Common::clean_input($data);
		}

		$id = $data['id'];
		$db->AutoExecute($_gTables[PLATNOSCI_BANKI],$data,'UPDATE'," id = '$id' ");

		return true;
		
	}

	/**
	 * Usuwa dane z bazy. W celu zachowania integralnosci metoda
	 * ta sprawdza rowniez powiazania producenta z produktem
	 *
	 * @param unknown_type $id
	 * @return true lub ExceptionCode
	 */
	static public function deleteData($id)
	{
		global $_gTables;
	
		$db = \Db::getInstance();
		$sql = "DELETE FROM $_gTables[PLATNOSCI_BANKI] WHERE id = '$id' ";
		$db->Execute($sql);

		return true;
		
	}
	/**
	 * Zwraca liste obiektow odfiltrowana wg tablicy $filtr, posortowana wg tablicy $sort oraz w przedziale podanym w tablicy $limit
	 *
	 * @param array $filtr	Tablica asocjacyjna filtrujaca w postaci $filtr['pole'] = 'wartosc'
	 * @param array $sort	Tablica asocjacyjna okreslajaca sortowanie, musi zawierac klucz 'sort' okreslajacy po ktorym polu sortujemy oraz klucz 'order' okreslajacy kolejnosc sortowania (ASC/DESC)
	 * @param array $limit	Tablica asocjacyjna okreslajaca przedzial zwracanych wynikow, musi zawierac klucz 'start' okreslajacy od ktorego rekordu pobieramy wyniki oraz klucz 'limit' okreslajacy ile rekordow ma zostac zwroconych
	 * @return array
	 */
	static function getList( $filtr=NULL, $sort = NULL, $limit = NULL )
	{
		global $_gTables;
		
		$db = \Db::getInstance();

		$sql = "SELECT
					b.*
					FROM $_gTables[PLATNOSCI_BANKI] b
					WHERE 1 ";

		$sql.= self::filterGetList($filtr);

		$sql.= self::getSort($sort);

		$sql.= self::getLimit($limit);

		//Common::Debug($sql);
		$all = $db->getAll($sql);

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
	
		$db = \Db::getInstance();

		$sql = "SELECT COUNT(a.id) AS ilosc FROM $_gTables[PLATNOSCI_BANKI] a WHERE 1 ";

		$sql.= self::filterGetList($filtr);

		$val = $db->getOne($sql);

		return (int)$val;
		
	}
	/**
	 * Generuje ciag SQL filtrujacy wg podanej tablicy $filtr
	 *
	 * @param array $filtr
	 */
	function filterGetList($filtr=NULL)
	{

		$sql = '';

		if( isset($filtr) && is_array($filtr) )
		{

			if( isset($filtr['widoczny']) && is_numeric($filtr['widoczny']) )
				$sql.= " AND b.widoczny = '".(int)$filtr['widoczny']."'";
			
			if( isset($filtr['paybynet']) && is_numeric($filtr['paybynet']) )
				$sql.= " AND b.id_paybynet > 0 ";

		}

		return $sql;
	}
}

?>
