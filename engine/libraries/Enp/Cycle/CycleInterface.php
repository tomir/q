<?php

namespace Enp\Cycle;

/**
 * @category Enp
 * @package  Enp_Cycle
 * @author   Artur Świerc
 */
interface CycleInterface 
{
	/**
	 * @param \DateTime $date
	 * @return boolean
	 */
	public function isValid(\DateTime $date);
}
