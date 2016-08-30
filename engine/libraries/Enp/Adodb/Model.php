<?php
namespace Enp\Adodb;

/**
 * Nakladka zastepujaca obecny class.Object.php
 * 
 */

abstract class Model
{
	public $id;
	public $data;

	protected $tableName = null;
	protected $filtryStale = array();
	
	protected function init() {
		$this->tableName = $this->getTableName();
	}
	
	public function setFiltryStale(array $filtry) {
		$this->filtryStale = $filtry;
	}
	
	abstract public function getTableName();
	
	protected function getSqlSelect() {
		return "SELECT x.* FROM $this->tableName x WHERE 1 = 1 ";
	}

	protected function getRowFeatures($row, \Adodb $db) {
		return $row;
	}
	
	protected function saveRowFeatures($row, \Adodb $db) {
		
	}
	
	protected function deteleRowFeatures($id, \Adodb $db) {
		
	}
	
	protected function validacjaDanychWejsciowych($row, \Adodb $db) {
		
	}
	
	/**
	 * Konstruktor obiektu, jesli przekazemy ID wtedy do tablicy $data zostana pobrane dane obiektu
	 *
	 * @param int $id
	 */
	public function __construct($id = null)
	{
		$this->init();
		
		$id = (int)$id;
		if ($id > 0) {
			$this->id	= (int) $id;
			$this->data = $this->getData();

			if (count($this->data) < 1) {
				$this->id = null;
				$this->data = array();
			}
		}
	}
	
	/**
	 * Pobiera dane obiektu i zwraca w postaci tablicy
	 *
	 * @return array
	 *
	 */
	public function getData()
	{
		$db = \Db::getInstance();

		$sql = $this->getSqlSelect();
		
		$sql .= " AND x.id = ".(int)$this->id;

		$sql .= $this->filterGetList($this->filtryStale);
		
		$row = $db->GetRow( $sql );
		
		$row = $this->getRowFeatures($row, $db);
		
		return $row;
	}
	/**
	 * Zapisuje nowy rekord w bazie
	 *
	 * @param array $dane
	 * @return true lub ExceptionCode
	 */
	public function saveData( $data )
	{
		$db = \Db::getInstance();
		$db->StartTrans();

		$data = \Common::clean_input($data);
		
		// walidacja danych
		$this->validacjaDanychWejsciowych($data, $db);
		
		$db->AutoExecute($this->tableName,$data,'INSERT');

		$data['id'] = $db->Insert_ID();
		$this->saveRowFeatures($data, $db);
		
		$db->CompleteTrans();
		
		
		$this->id = (int)$data['id'];
		
		return true;
	}
	/**
	 * Aktualizuje dane obiektu w bazie. Tablica wejsciowa musi zawierac klucz 'id'
	 *
	 * @param array $dane
	 * @return true lub ExceptionCode
	 */
	public function updateData( $data )
	{
		$db = \Db::getInstance();
		
		$db->StartTrans();

		$data = \Common::clean_input($data);
		
		// walidacja danych
		$this->validacjaDanychWejsciowych($data, $db);
		
		$id = (int)$data['id'];
		
		$db->AutoExecute($this->tableName,$data,'UPDATE'," id = '$id' ");
		
		$this->saveRowFeatures($data, $db);
				
		$db->CompleteTrans();
		
		return true;
	}
	
	/**
	 * usuwa dane z bazy, jednak ze wzgldeu na brak mozliwosci utworzenia polaczen
	 * miedzy tabelami dostepnosc i produkty (InnoDb nie obsluguje FuullText Search)
	 * W metodzie tej wykonywane sa czynnosci umozliwiajace zachowanie integralnosci danych w bazie
	 * - czy jest powiazanie z jakims produktem
	 *
	 * @param unknown_type $id
	 * @return true lub ExceptionCode
	 */
	public function deleteData( $id )
	{
		$db = \Db::getInstance();
		
		$db->StartTrans();
		
		$id = (int)$id;
		
		$this->id = $id;
		$this->data = $this->getData();
		
		$sql = "DELETE FROM $this->tableName WHERE id = '$id' ";
		$db->Execute($sql);

		$this->deteleRowFeatures($id, $db);
		
		$db->CompleteTrans();
		
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
	public function getList( $filtr=NULL, $sort = NULL, $limit = NULL, $withFeatures = true )
	{
		
		$db = \Db::getInstance();

		$sql = $this->getSqlSelect();
		
		
		$sql.= $this->filterGetList($this->filtryStale);
		$sql.= $this->filterGetList($filtr);

		$sql.= $this->getSort($sort);

		$sql.= $this->getLimit($limit);
		
		$all = $db->getAll($sql); 
		
		if ($withFeatures == true) {
			
			foreach($all as $k=>$v)
			{
				$all[$k] = $this->getRowFeatures($v, $db);
			}
		}

		return $all;
	}
	
	/**
	 * Zwraca ilosc wszystkich pozycji odpowiadajacych podanemu filtrowi $filtr z pominieciem ograniczen $limit
	 *
	 * @param array $filtr
	 * @return int
	 */
	public function getListQty( $filtr )
	{
		$db = \Db::getInstance();

		$sql = $this->getSqlSelect();
		
		// podmiana wszystkiego miedzy SELECT i FROM na count(x.id)
		$sql = preg_replace('/SELECT([^FROM])*/', 'SELECT count(x.id) AS ilosc ', $sql);
		
		$sql.= $this->filterGetList($filtr);
		
		$val = $db->getOne($sql);

		return (int)$val;

	}
	
	/**
	 * Generuje ciag SQL filtrujacy wg podanej tablicy $filtr
	 *
	 * @param array $filtr
	 */
	protected function filterGetList($filtr=NULL)
	{
		$sql = '';

		foreach($filtr as $key => $val) {
			
			/**
			 * Filtry dla danych tablicowych
			 */
			if (is_array($val)) {
				/**
				 * Obsluga tablicy > parsowanie do IN
				 */
				$valArray = array();
				foreach($val as $keyOne=>$valOne) {
					$valOne = \Common::clean_input($valOne);
					$valArray[] = "'$valOne'";
				}
				$valString = implode(',', $valArray);
				$sql .= " AND $key IN ($valString) ";
				continue;;
			}
			
			$val = \Common::clean_input($val);
			
			/**
			 * filtry dla danych scalarnych
			 */
			if (preg_match('/_like$/', $key) == 1) {
				$key = preg_replace('/_like$/', '', $key);
				$sql .= " AND $key LIKE '%$val%' ";
			}
			elseif (preg_match('/_od$/', $key) == 1) {
				$key = preg_replace('/_od$/', '', $key);
				$sql .= " AND $key >= '$val' ";
			}
			elseif (preg_match('/_do$/', $key) == 1) {
				$key = preg_replace('/_do$/', '', $key);
				$sql .= " AND $key <= '$val' ";
			}
			else {
				/**
				 * Inna obsluga, porownanie z =
				 */
				$sql .= " AND $key = '$val' ";
			}
		}
		
		return $sql;
	}
	
	/**
	 * Zwraca tablice gdzie klucz to ID obiektu a wartosc jego nazwa. Uzywane do generowania SELECT w Smarty
	 *
	 * @param array $filtr
	 * @param array $sort
	 * @param array $limit
	 * @return array
	 */
	function getListSelect( $filtr=NULL, $sort = NULL, $limit = NULL )
	{
		$tabData = array();

		$all = $this->getList( $filtr, $sort, $limit );

		if (is_array($all)) {
			foreach($all as $item) {
				$tabData[ $item['id'] ] = $item['nazwa'];
			}
	
			return $tabData;
		}
		else {
			return null;
		}
	}
	

		/**
	 * Generuje kod SQL sortujacy wynik zapytania wg podanej tablicy asocjacyjnej $sort
	 *
	 * @param array $sort Tablica asocjacyjna okreslajaca sortowanie, musi zawierac klucz 'sort' okreslajacy po ktorym polu sortujemy oraz klucz 'order' okreslajacy kolejnosc sortowania (ASC/DESC)
	 * @return string
	 */
	protected function getSort( $sort = NULL )
    {
		$sql = '';

        if( isset($sort) && is_array($sort) )
        {
            if( isset($sort['sort']) && preg_match('/^[a-zA-Z0-9_\.()]*$/i', $sort['sort']) &&
                isset($sort['order']) && preg_match('/^[a-zA-Z0-9]*$/i', $sort['order']) )
            {
               $sql.= " ORDER BY ".$sort['sort']." ".$sort['order'];
            } elseif( is_array($sort) && count($sort) > 0 ){
               $sql.= " ORDER BY ";
               foreach ($sort as $v)
               {
               		$sql.= $v['sort']." ".$v['order'].", ";
               }
               $sql = substr($sql, 0, -2);
             }
         }
         
         return $sql;

	}
	/**
	 * Generuje kod SQL ograniczajacy ilosc zwracanych wynikow wg tablicy asocjacyjnej $limit
	 *
	 * @param array $limit Tablica asocjacyjna okreslajaca przedzial zwracanych wynikow, musi zawierac klucz 'start' okreslajacy od ktorego rekordu pobieramy wyniki oraz klucz 'limit' okreslajacy ile rekordow ma zostac zwroconych
	 * @return string
	 */
	protected function getLimit( $limit = NULL )
	{
		$sql = '';

		if( isset($limit) && is_array($limit) )
		{
			if( isset($limit['start']) && is_numeric($limit['start']) &&
				isset($limit['limit']) && is_numeric($limit['limit']) )
			{
				$sql.= " LIMIT ".$limit['start'].", ".$limit['limit'];
			}
		}
		return $sql;
	}


	
	public function set($column, $value,  $whereCondition = '1 != 1') {
		
		$column =			htmlentities($column, ENT_QUOTES);
		$value =			htmlentities($value, ENT_QUOTES);
		$whereCondition =	htmlentities($whereCondition, ENT_QUOTES);
		
		
		
		$db = Db::getInstance();
		
		$sql = "UPDATE $this->tableName SET $column = '$value' WHERE $whereCondition ";
		
		$db->Execute($sql);
		
	}
	
	static public function clearFiltryFromEmpty($filtry) {
		foreach($filtry as $key=>$val) {
			if (empty($val)) {
				unset($filtry[$key]);
			}
		}
		
		return $filtry;
	}
}



?>