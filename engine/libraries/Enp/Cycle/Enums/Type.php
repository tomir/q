<?php

namespace Enp\Cycle\Enums;

/**
 * @category Enp
 * @package  Enp_Cycle_Enums
 * @author   Artur Świerc
 */
class Type extends \Enp\Enum
{
	const DAY	= 'day';
	const WEEK	= 'week';
	const MONTH = 'month';
	const YEAR  = 'year';
	
	/**
	 * @var array
	 */
	protected $labels = array(
		self::DAY	=> 'Dzienny', 
		self::WEEK	=> 'Tygodniowy', 
		self::MONTH => 'Miesięczny', 
		self::YEAR  => 'Roczny'
	);
	
	/**
	 * @var array
	 */
	protected $classMap = array(
		self::DAY	=> 'Enp\Cycle\Types\Day',
		self::WEEK	=> 'Enp\Cycle\Types\Week',
		self::MONTH => 'Enp\Cycle\Types\Month',
		self::YEAR	=> 'Enp\Cycle\Types\Year'
	);
	
	/**
	 * @return string
	 * @throws \LogicException
	 */
	public function getClassName()
	{
		if (null === $this->value) { 
			throw new \LogicException('cannot return class name, without knowing type');
		}

		return $this->classMap[$this->value];
	}
}
