<?php

namespace Enp\Db\ORM;

abstract class EntityAbstract
{
	/**
	 * @var \Zend_Db_Table_Abstract
	 */
	protected $tableDbAdapter	= null;
	
	/**
	 * identyfikator konkretnej encji
	 * 
	 * @var int
	 */
	protected $id = null;
	
	/**
	 * dane z bazy konkretnej encji
	 * 
	 * @var array
	 */
	protected $data = array();
	
	/**
	 * wpisuje db adapter to odpowiedniej zmiennej w obiekcie
	 */
	public function __construct()
	{
		$this->tableDbAdapter = $this->getTableDbAdapter();
	}
	
	
	/**
	 * @return \Zend_Db_Table_Abstract
	 */
	abstract public function getTableDbAdapter();
	
	
	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setData($data)
	{
		$this->data = $data;
	}

}