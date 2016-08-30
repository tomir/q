<?php

namespace Enp\Cycle;

/**
 * @category Enp
 * @package  Enp_Cycle
 * @author Artur Świerc
 */
abstract class CycleAbstract implements \Enp\Cycle\CycleInterface
{
	/**
	 * @var int|null
	 */
	protected $id = null;
	
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var Enums\Type
	 */
	protected $typeEnum;

	public function __construct() 
	{
		$this->typeEnum = new Enums\Type();
	}
	
	/**
	 * @param int $type
	 */
	public function setType($type)
	{
		$this->typeEnum->setValue($type);
	}
	
	/**
	 * @return int
	 */
	public function getType() 
	{
		return $this->typeEnum->getValue();
	}
	
	/**
	 * @param array $data
	 */
	public function setData(array $data)
	{
		$this->data = $data;
	}
	
	/**
	 * @return array
	 */
	public function getData() 
	{
		return $this->data;
	}
	
	/**
	 * @param int $id
	 */
	public function setId($id) 
	{
		$this->id = $id;
	}
	
	/**
	 * 
	 * @return int
	 */
	public function getId() 
	{
		return $this->id;
	}
	
	/**
	 * @param  \DateTime $date
	 * @return boolean
	 * @throws \Enp\Cycle\Exception
	 */
	public function isValidDate(\DateTime $date)
	{
		if (empty($this->data)) { 
			throw new \Enp\Cycle\Exception('cannot check valid object with empty data');
		}
		
		$startDate = new \DateTime($this->data['start_date']);
		
		if ($startDate > $date) { 
			return false;
		}
		
		$startHour = new \DateTime($this->data['start_hour']);
		$stopHour  = new \DateTime($this->data['stop_hour']);
		
		if ($startHour > $date) { 
			return false;	
		}
		
		if ($stopHour < $date) { 
			return false;
		}
		
		return true;
	}
}
