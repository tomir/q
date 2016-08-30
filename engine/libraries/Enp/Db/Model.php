<?php

namespace Enp\Db;

/**
 * @author Piotr Flasza
 * @author Krzysztof Deneka
 */
abstract class Model extends \Enp\Instance implements ModelInterface
{

	/**
	 * pole ktore ma byc brane do tworzenia listy z select option
	 *
	 * @var string
	 */
	protected $_select_pola = array('name');

	/**
	 * Mozna dopisywac filtr dla roznych pol w formacie
	 * " pole = ? " , gdzie pod znak ? zostanie podstawiona wartosc
	 *
	 * @var array
	 */
	protected $_filtry = array();
	protected $_pluginy = array();

	public function hasFilterNameReserved($name)
	{
		if (isset($this->_pluginy[$name])) {
			return true;
		}

		if (isset($this->_filtry[$name])) {
			return true;
		}

		return false;
	}

	public function addPlugin(FiltrPluginInterface $plugin, $name)
	{
		if ($this->hasFilterNameReserved($name)) {
			throw new \Enp\Exception("Istnieje juz filtr lub plugin o nazwie $name w modelu !");
		}

		$this->_pluginy[$name] = $plugin;
	}

	/**
	 * @var \Zend_Db_Table_Abstract
	 */
	protected $dbTable = null;

	/**
	 * flaga oznaczajaca czy dla kazdego wyciagnietego wiersza wykonywac metode processGetOneRecord
	 * @var boolean
	 */
	protected $_doProcessGetOneRecord = true;

	/**
	 * @var int
	 */
	public $id = 0;

	/**
	 * @var array
	 */
	public $data = array();

	/**
	 * @var string
	 */
	protected $_firstPrimaryKey = null;

	/**
	 * @var array
	 */
	protected $_lastSQL = array();

	/**
	 * @return \Zend_Db_Table_Abstract
	 */
	abstract public function getDbTableObject();

	/**
	 * @return \Zend_Db_Table_Abstract
	 */
	public function getDbTable()
	{
		return $this->dbTable;
	}

	/**
	 * @param int $id
	 */
	public function __construct($id = 0)
	{
		\Enp\Db\Db::getInstance();
		$this->dbTable = $this->getDbTableObject();
		$this->_firstPrimaryKey = $this->getFirstPrimaryKey();
		$this->load($id);
		$this->init();
	}

	/**
	 * @param int $id
	 */
	public function load($id)
	{
		if ($id > 0) {
			$id = (int) $id;
			$data = $this->getData($id);
			$primaryKey = $this->getFirstPrimaryKey();

			if ((int) $data[$primaryKey] > 0) {
				$this->data = $data;
				$this->id = $id;
			} else {
				$this->data = array();
				$this->id = 0;
			}
		}
	}

	/**
	 * @return string
	 */
	public function getFirstPrimaryKey()
	{
		if (null === $this->_firstPrimaryKey) {
			$primaryKeys = $this->dbTable->info(\Zend_Db_Table::PRIMARY);
			$this->_firstPrimaryKey = array_shift($primaryKeys);
		}
		return $this->_firstPrimaryKey;
	}

	public function init()
	{
		
	}

	/**
	 * @param boolean $boolean
	 */
	public function doProcessGetOneRecord($boolean)
	{
		$this->_doProcessGetOneRecord = (bool) $boolean;
	}

	/**
	 * Uzupelnienie informacj podczas pobierania rekordu
	 * 
	 * @param type $record
	 * @return type
	 */
	public function processGetOneRecord($record)
	{
		return $record;
	}

	/**
	 * dodatkowe akcje przy insercie
	 * 
	 * @param array $record
	 * @param int $id
	 */
	public function processInsertOneRecord($record, $id)
	{
		
	}

	/**
	 * dodatkowe akcje przy updajcie
	 * 
	 * @param array $record
	 * @param int $id
	 */
	public function processUpdateOneRecord($record, $id)
	{
		
	}

	/**
	 * 
	 * @param int $id
	 */
	public function processDeleteOneRecord($id)
	{
		
	}

	/**
	 * @return array
	 */
	public function getLastSQLList()
	{
		return $this->_lastSQL;
	}

	/**
	 * @param int $id
	 * @return array
	 */
	public function getData($id)
	{

		$primaryKey = $this->getFirstPrimaryKey();

		$select = $this->getSelect();
		$select->where("x." . $primaryKey . " = ?", $id);
		$stmt = $select->query();
		$row = $stmt->fetch();

		if (false === $row) {
			return null;
		}
		if ($this->_doProcessGetOneRecord) {
			$row = $this->processGetOneRecord($row);
		}
		return $row;
	}

	/**
	 * @param array $data
	 * @return int
	 */
	public function insert($data)
	{

		/*
		 * nie mozna tego usuwac bo ja chce zrobic inserta
		 * podajac wartosci wszystkich kolumn
		 */
		//$primary = $this->getFirstPrimaryKey();
		//unset($data[$primary]);

		$dataToDb = $this->getDataToDb($data);

		\Enp\Db\Db::startTrans();

		$id = $this->dbTable->insert($dataToDb);

		$this->processInsertOneRecord($data, $id);

		\Enp\Db\Db::completeTrans();

		return $id;
	}

	/**
	 * @param array $data
	 * @param int $id
	 */
	public function update($data, $id = null)
	{

		if ($id === null) {
			$id = $this->id;
		}

		$id = (int) $id;
		$dataToDb = $this->getDataToDb($data);

		if (count($dataToDb) > 0) {

			\Enp\Db\Db::startTrans();

			$primaryKey = $this->getFirstPrimaryKey();

			$this->dbTable->update($dataToDb, $primaryKey . " = '$id' ");

			$this->processUpdateOneRecord($data, $id);
			\Enp\Db\Db::completeTrans();
		}
	}

	/**
	 * Zapisuje zmiany jakie zostalu wykonane w tablicy $this->data
	 * Wykonuje update lub insert w zaleznosci od tego czy jest uzupelnione ID. 
	 * Zapisuje nowe ID w przypadku operacji insert.
	 */
	public function save()
	{
		if ($this->id > 0) {
			$this->update($this->data);
		} else {
			$this->id = $this->insert($this->data);
		}
	}

	public function getDataToDb($data, $cleanInput = true)
	{
		if ($cleanInput == true) {
			$data = \Common::clean_input($data);
		}

		$columns = $this->dbTable->info(\Zend_Db_Table::COLS);
		foreach ($data as $column => $value) {
			if (!in_array($column, $columns)) {
				unset($data[$column]);
			}
		}

		return $data;
	}

	public function delete($id = null)
	{
		if ($id === null) {
			$id = $this->id;
		}

		$id = (int) $id;

		\Enp\Db\Db::startTrans();

		$this->processDeleteOneRecord($id);

		$primaryKey = $this->getFirstPrimaryKey();
		$this->dbTable->delete($primaryKey . " = '$id' ");

		\Enp\Db\Db::completeTrans();
	}

	public function deleteWhere($where)
	{

		$this->dbTable->delete($where);
	}

	public function setOnOff($pole, $id = null)
	{
		if ($id === null) {
			$id = $this->id;
		}

		$one = new $this($id);
		$x = $one->data[$pole];


		if ((int) $x == 0) {
			$x = 1;
		} else {
			$x = 0;
		}

		$data = array($pole => (int) $x);
		$this->update($id, $data);
	}

	public function set($pole, $wartosc, $id = null)
	{
		if ($id === null) {
			$id = $this->id;
		}

		$data = array($pole => $wartosc);

		$this->update($data, $id);
	}

	protected function setSort($select, $sort = null)
	{
		if ($sort !== null) {
			// order
			if (isset($sort['sort'])) {
				$sortX[] = $sort;
			} else {
				$sortX = $sort;
			}

			foreach ($sortX as $key => $sortOne) {
				if ($sortOne['sort'] != '' && $sortOne['order'] != '')
					$select->order($sortOne['sort'] . ' ' . $sortOne['order']);
				else if ($sortOne['sort'] == 'rand')
					$select->order(new \Zend_Db_Expr('RAND()'));
			}
		}

		return $select;
	}

	protected function setLimit($select, $limit = null)
	{
		if ($limit !== null) {

			// limit
			if ($limit['limit'] !== '' && $limit['start'] !== '') {
				$select->limit((int) $limit['limit'], (int) $limit['start']);
			}
		}

		return $select;
	}

	public function getAll($filtr = null, $sort = null, $limit = null)
	{
		$select = $this->getSelect($filtr);
		$select = $this->setSort($select, $sort);
		$select = $this->setLimit($select, $limit);

		$stmt = $select->query();
		$result = $stmt->fetchAll();

		if ($this->_doProcessGetOneRecord) {
			foreach ($result as $key => $row) {
				$result[$key] = $this->processGetOneRecord($row);
			}
		}
		return $result;
	}

	public function getOne($filtr = null, $sort = null)
	{
		$res = $this->getAll($filtr, $sort, array(
			'start' => 0,
			'limit' => 1
		));
		;

		if (isset($res[0])) {
			return $res[0];
		}

		return null;
	}

	/**
	 * @param int $cachelifetime
	 * @param array $filtr
	 * @param array $sort
	 * @param array $limit
	 * @return array
	 */
	public function getCacheAll($cachelifetime, $filtr = null, $sort = null, $limit = null)
	{

		$calledclass = preg_replace("/[^A-Za-z0-9 ]/", '_', get_called_class());
		$cacheid = $calledclass . 'get_cache_all_' . $this->getCacheHash(func_get_args());
		$cache = \Enp\Cache::get();

		if (false === ($results = $cache->load($cacheid))) {
			$results = $this->getAll($filtr, $sort, $limit);
			$cache->save($results, $cacheid, array(), $cachelifetime);
		}
		return $results;
	}

	public function getAllIlosc($filtr = null)
	{
		$select = $this->getSelect($filtr);

		$paginator = new \Zend_Paginator_Adapter_DbSelect($select);

		return $paginator->count();
	}

	public function getAllSelect($filtr = null, $sort = null)
	{
		$result = $this->getAll($filtr, $sort);

		$res = array();

		foreach ($result as $key => $one) {
			$res[$one['id']] = $this->getAllSelectString($one);
		}
		return $res;
	}

	/**
	 * @param array $filtr
	 * @param array $sort
	 * @return array|null
	 */
	public function getFirst(array $filtr = null, array $sort = null)
	{
		$results = $this->getAll($filtr, $sort, array('start' => 0, 'limit' => 1));
		return (isset($results[0])) ? $results[0] : null;
	}

	/**
	 *  Zwraca wartosci z podanej kolumny
	 * @param string $name
	 * @param array $filtr
	 * @param array $sort
	 * @param array $limit
	 * @return array
	 */
	public function getCol($name, $filtr = null, $sort = null, $limit = null)
	{
		$select = $this->getSelect($filtr);
		$select->reset('columns');
		$select->columns($name);
		$select->distinct(true);

		$select = $this->setSort($select, $sort);
		$select = $this->setLimit($select, $limit);

		$stmt = $select->query();
		$result = $stmt->fetchAll(\Zend_Db::FETCH_COLUMN);

		return $result;
	}

	/**
	 * @param array $row
	 * @return string
	 */
	protected function getAllSelectString($row)
	{
		$pieces = array();

		foreach ($this->_select_pola as $field) {
			$pieces[] = $row[$field];
		}

		return implode(' ', $pieces);
	}

	/**
	 * Metoda domyslnie wstawia filtry do obiektu Zend_Db_Select
	 *
	 * rozroznia filtry typu:
	 * LIKE - nazwa konczy sie na _like
	 * IN - wartoscia jest tablica identyfikatorow
	 * = - standardowe zachowanie
	 *
	 * Dodatkowow uwzglednia filtry ustawione dla danej klasy
	 * w zmiennej $this->__filtry
	 *
	 * @param Zend_Db_Select $select
	 * @param array $filtr
	 * @return Zend_Db_Select
	 */
	protected function setFiltr(\Zend_Db_Select $select, $filtr)
	{
		$selectBuilder = new \Enp\Db\Select\Builder($select);
		
		// filtr
		foreach ($filtr as $key => $val) {

			// jezeli zdefiniowano dla tego klucza filtr
			if (key_exists($key, $this->_filtry)) {

				if (!is_array($val)) { 
					$val = new \Zend_Db_Expr($val);
				}
				$select->where($this->_filtry[$key], $val);
			}
			// jezeli zdefiniowano dla tego klucza plugin
			elseif (key_exists($key, $this->_pluginy)) {

				$plugin = $this->_pluginy[$key];
				/* @var $plugin FiltrPluginInterface */
				$plugin->processSelect($val, $select, $this);
			} else {
				$selectBuilder->setFilter(array($key => $val));
			}
		}
		return $select;
	}

	/**
	 *
	 * @param array $filtr
	 * @return \Zend_Db_Select
	 */
	protected function getSelect($filtr = null)
	{
		$mainTableName = $this->dbTable->info(\Zend_Db_Table_Abstract::NAME);

		$adapter = \Enp\Db\Db::getRightInstance();

		// select
		$select = new \Zend_Db_Select($adapter);
		$select->from(array('x' => $mainTableName));
		$select = $this->setFiltr($select, $filtr);

		$this->_lastSQL[] = $select->__toString();
		return $select;
	}

	/**
	 * @param array $args
	 * @return string
	 */
	protected function getCacheHash(array $funcargs)
	{
		return md5(get_called_class() . serialize($funcargs));
	}

}