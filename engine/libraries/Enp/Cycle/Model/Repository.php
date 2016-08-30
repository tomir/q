<?php

namespace Enp\Cycle\Model;

use Enp\Cycle\CycleAbstract;
use Enp\Cycle\CycleFactory;

/**
 * @category Enp
 * @package  Enp_Cycle_Model
 * @author   Artur Świerc
 */
class Repository extends \Enp\Db\Model
{
	/**
	 * @var array
	 */
	protected $_filtry = array(
		'start_date'	=> "x.start_date <= '?'",
		'hour_between'	=> "'?' between start_hour and stop_hour",
	);
	
	/**
	 * @return \Zend_Db_Table
	 */
	public function getDbTableObject() 
	{
		return \Enp\Db\TableFactory::get('cycle');
	}

	/**
	 * @param \Enp\Cycle\CycleAbstract $cycle
	 */
	public function save(CycleAbstract $cycle)
	{
		if ((int) $cycle->getId() <= 0) { 	
			$data = $cycle->getData(); 
			$data['cycle_type'] = $cycle->getType();
			
			$id = $this->insert($data);
			$cycle->setId($id);
			
		} else {
			$this->update($cycle->getData(), $cycle->getId());
		}
	}

	/**
	 * @param  int $id
	 * @return null|\Enp\Cycle\CycleAbstract
	 * @throws \InvalidArgumentException
	 */
	public function findById($id) 
	{
		if ((int) $id <= 0) { 
			throw new \InvalidArgumentException('an invalid parameter id');
		}
		
		$data = $this->getData($id);
		if (empty($data)) {
			return null;
		}
		return $this->map($data);
	}
	
	/**
	 * @param  array $filtr
	 * @param  array $sort
	 * @param  array $limit
	 * @return \Enp\Cycle\CycleAbstract[]
	 */
	public function findAll($filtr = null, $sort = null, $limit = null) 
	{
		$rows = $this->getAll($filtr, $sort, $limit);
		
		if (empty($rows)) { 
			return array();
		}
		
		$collections = array();
		foreach ($rows as $row) { 
			$collections[] = $this->map($row);
		}
		return $collections;
	}
		
	/*
	 * @param array $record
	 * @return array
	 */
	public function processGetOneRecord($record)
	{
		$startHour = new \DateTime($record['start_hour']);		
		$record['start_hour'] = $startHour->format('H:i');
		
		if ($record['stop_hour'] == '23:59:59') {
			$record['calydzien'] = 1;
		} else {
			$stopHour = new \DateTime($record['stop_hour']);		
			$record['stop_hour'] = $stopHour->format('H:i');
		}
		return $record;
	}
	
	/**
	 * @param array $row
	 * @return \Enp\Cycle\CycleAbstract
	 */
	protected function map(array $row) 
	{
		return CycleFactory::create($row['cycle_type'], $row);
	}
}
