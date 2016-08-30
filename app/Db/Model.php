<?php

namespace Db;

abstract class Model implements ModelInterface {

	protected $sql;
	protected $id;
	protected $data;
	protected $dbTable;
	protected $dbColumn = null;
	
	protected $_doProcessGetOneRecord = true;
	
	/**
	 * Example of use
	 * " field = ? " , when char ? will be replace to value
	 *
	 * @var array
	 */
	protected $_filter = array();

	/**
         * Table of registered plugins in model
         * 
	 * @var  \Db\FiltrPluginInterface[]
	 */
	protected $_plugin = array();

        
        /**
         * Construdtor setting the db connection and creating instance of this
         * Set db model databese table and main column
         * 
         * @param int $id
         */
	public function __construct($id = null) {

		if (!is_null($id)) {
			$this->id = $id;
		}
		
		$adapter = \Db\Db::getInstance();

		$this->dbTable = $this->getDbTable();
		$this->dbTable->setOptions(array(\Zend_Db_Table_Abstract::ADAPTER => $adapter));
		$this->dbColumn = $this->setPrimaryColumn();
		
	}

	abstract public function getDbTable();

	abstract public function setPrimaryColumn();

        /**
         * Metod to refreshing model data
         * @param int $id
         */
	public function load($id) {
		
		if ($id !== null) {
			$data = $this->getOne($id);

			if ((int) $data[$this->dbColumn] == $id) {
				$this->data = $data;
				$this->id = $id;
			} else {
				$this->data = array();
				$this->id = null;
			}
		}
	}
	
	/**
         * If primary dbcolumn is not set method checking the primary column of main table
         * 
	 * @return string
	 */
	public function getFirstPrimaryKey()
	{
		if (null === $this->dbColumn) {
			$primaryKeys = $this->dbTable->info(\Zend_Db_Table::PRIMARY);
			$this->dbColumn = array_shift($primaryKeys);
		}
		return $this->dbColumn;
	}

	
	/**
	 * Primary method using by others to generate sql query by Zend_Db_Select
	 * @param array $filtr
	 * @return \Zend_Db_Select
	 */
	protected function getSelect($filtr = array())
	{
		$adapter = \Db\Db::getInstance();

		$mainTableName = $this->dbTable->info(\Zend_Db_Table_Abstract::NAME);
		
		$select = new \Zend_Db_Select($adapter);
		$select->from(array('x' => $mainTableName));
		$select = $this->setFiltr($select, $filtr);

		$this->_lastSQL[] = $select->__toString();
		return $select;
	}
	
	/**
	 *
	 * Additional filters set in var
	 * $this->__filter
	 *
	 * @param Zend_Db_Select $select
	 * @param array $filtr
	 * @return Zend_Db_Select
	 */
	protected function setFiltr(\Zend_Db_Select $select, $filter = array())
	{
		$selectBuilder = new \Db\Select\Builder($select, $this->_filter, $this->_plugin, $this);
		$select = $selectBuilder->setFilter($filter);

		return $select;
	}

	/**
         * Method to get one record
         * 
	 * @param array $filtr
	 * @param array|null $sort
	 * @return null|array
	 */
	public function getOne($filtr = array(), $sort = null)
	{
		$res = $this->getAll($filtr, $sort, array(
			'start' => 0,
			'limit' => 1
		));

		if (isset($res[0])) {
			return $res[0];
		}

		return null;
	}

	/**
         * Method to get all records by conditions in method paremetr
         * 
	 * @param array $filtr
	 * @param array|null $sort
	 * @param array|null $limit
	 * @return array
	 */
	public function getAll($filtr = array(), $sort = null, $limit = null)
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

        /**
         * Method to count records by filter condition
         * 
         * @param array $filtr
         * @return int
         */
	public function getAllCount($filtr = array()) {

		$select = $this->getSelect($filtr);

		$paginator = new \Zend_Paginator_Adapter_DbSelect($select);

		return $paginator->count();
	}

	/**
         * Method to insert data to database
         * Key of array is column name, value of array is value of column
         * 
	 * @param array $data
	 * @return int
	 */
	public function insert($data)
	{
		$dataToDb = $this->getDataToDb($data);
		$id = $this->dbTable->insert($dataToDb);

		$this->processInsertOneRecord($data, $id);

		return $id;
	}

	/**
         * Method to update data
         * 
	 * @param  array $data
	 * @param  int $id
	 * @return int
	 */
	public function update($data, $id = null)
	{
		if ($id === null) {
			$id = $this->id;
		}

		$affected = 0;
		$id = (int)$id;
		$dataToDb = $this->getDataToDb($data);

		if (count($dataToDb) > 0) {

			$primaryKey = $this->getFirstPrimaryKey();
			$affected = $this->dbTable->update($dataToDb, $primaryKey . " = '$id' ");

			$this->processUpdateOneRecord($data, $id);

		}

		return $affected;
	}

        /**
         * Method to delete record by id
         * 
         * @param int $id
         * @return void
         */
	public function delete($id = null) {

		if (!is_null($id)) {
			$this->id = $id;
		}

		return $this->sql->DeleteRows($this->dbTable, array(
			$this->dbColumn => $this->id,
		));
	}

        /**
         * Method to delete record by array condition
         * 
         * @param array $whereArray
         * @return void
         */
	public function deleteWhere($whereArray) {
		
		return $this->sql->DeleteRows($this->dbTable, $whereArray);
	}

	/**
         * Method setting processing records
	 * @param boolean $boolean
	 */
	public function doProcessGetOneRecord($boolean) {
		$this->_doProcessGetOneRecord = (bool) $boolean;
	}

	/**
         * Method used after getting record from database
         * Every record will be processing by this method if _doProcessGetOneRecord id true
	 * @param array $record
	 * @return array
	 */
	public function processGetOneRecord($record) {
		return $record;
	}
	
	/**
         * MEthod checking the structure od database
         * If key of database (column name) is wrond, rocord will be unsetted
         * 
	 * @param array $data
	 * @param boolean $cleanInput
	 * @return array
	 */
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
	
	/**
         * Method check additional filter or plugin name
         * 
	 * @param string $name
	 * @return boolean
	 */
	public function hasFilterNameReserved($name)
	{
		if (isset($this->_plugin[$name])) {
			return true;
		}

		if (isset($this->_filter[$name])) {
			return true;
		}

		return false;
	}
	
	/**
         * Method adding plugins to array
         * 
	 * @param \Db\FiltrPluginInterface $plugin
	 * @param string $name
	 * @throws \Exception
	 */
	public function addPlugin(FiltrPluginInterface $plugin, $name)
	{
		if ($this->hasFilterNameReserved($name)) {
			throw new \Exception("Filter or plugin already exist at this name: $name");
		}

		$this->_plugin[$name] = $plugin;
	}
	
	/**
         * Method to set sorting in sql query by Zend_Db_Select
         * 
	 * @param \Zend_Db_Select $select
	 * @param mixed $sort
	 * @return \Zend_Db_Select
	 */
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
				if ($sortOne['sort'] != '' && $sortOne['order'] != '') {
					$select->order($sortOne['sort'] . ' ' . $sortOne['order']);
				} elseif ($sortOne['sort'] == 'rand') {
					$select->order(new \Zend_Db_Expr('RAND()'));
				}
			}
		}

		return $select;
	}
	
	/**
         * Method to set limit in sql query by Zend_Db_Select
         * 
	 * @param \Zend_Db_Select $select
	 * @param array|null $limit
	 * @return \Zend_Db_Select
	 */
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
	
	/**
	 * additional actions after insert
	 *
	 * @param array $record
	 * @param int $id
	 */
	public function processInsertOneRecord($record, $id)
	{
	}

	/**
	 * additional actions after update
	 *
	 * @param array $record
	 * @param int $id
	 */
	public function processUpdateOneRecord($record, $id)
	{
	}


}
