<?php

namespace Enp\Cycle; 

/**
 * @category Enp
 * @package  Enp_Cycle
 * @author   Artur Świerc
 * @author	 Piotr Flasza
 */
class CycleFactory 
{
	/**
	 * Create cycle object by type and data
	 * 
	 * @param int $type
	 * @return \Enp\Cycle\CycleAbstract
	 */
	public static function create($type, array $data = array())
	{
		$typeEnum = new Enums\Type($type);
		$className = $typeEnum->getClassName();
		
		/* @var $cycleInstance  \Enp\Cycle\CycleAbstract */
		$cycleInstance = new $className();
		$cycleInstance->setType($type);
		
		if (isset($data['id'])) { 
			$cycleInstance->setId((int) $data['id']);
			unset($data['id']);
		}
		
		$cycleInstance->setData($data);		
		return $cycleInstance;
	}
	
	/**
	 * Create cycle object by identyficator
	 * 
	 * @param int $id
	 * @return \Enp\Cycle\CycleAbstract
	 */
	public static function createById($id) 
	{
		$repo = new Model\Repository();
		$cykl = $repo->findById($id);

		return $cykl;
	}
}
