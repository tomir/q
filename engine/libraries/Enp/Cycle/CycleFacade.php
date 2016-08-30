<?php

namespace Enp\Cycle;

/**
 * @category Enp
 * @package  Enp_Cycle
 * @author   Artur Świerc
 */
class CycleFacade
{
	/**
	 * @param  \DateTime $dateTime
	 * @return array
	 */
	public function getList(\DateTime $dateTime)
	{
		$repository = new Model\Repository();
		
		$collection = $repository->findAll(array(
			'start_date'	=> $dateTime->format('Y-m-d'), 
			'hour_between'	=> $dateTime->format('H:i:s')
		));
		
		if (empty($collection)) { 
			return array();
		}
		
		$results = array();
		foreach ($collection as $instance) {
			if ($instance->isValid($dateTime)) {
				$results[] = $instance->getId();
			}
		}
		
		return $results;
	}
}
