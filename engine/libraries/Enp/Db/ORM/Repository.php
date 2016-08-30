<?php

namespace Enp\Db\ORM;

class Reposiotry
{
	/**
	 * @var EntityAbstract
	 */
	protected $entity = null;
	
	public function __construct(EntityAbstract $entity)
	{
		$this->entity = $entity;
	}
	
	public function getEntity()
	{
		return $this->entity;
	}

	public function setEntity(EntityAbstract $entity)
	{
		$this->entity = $entity;
	}

	/**
	 * @return \Zend_Db_Select
	 */
	protected function getSelectStatement() {
		
	}


	public function getAll() {
		
	}
	
	public function getFirst() {
		
	}
	
	public function getCol() {
		
	}

	public function getAssoc() {
		
	}
	
	public function insert(EntityAbstract $entity) {
		
	}
	
	public function update(EntityAbstract $entity) {
		
	}
	
	public function save(EntityAbstract $entity) {
		
	}
	
	
	
}