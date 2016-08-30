<?php

namespace Enp\Date;

/**
 * @category Enp
 * @package  Enp_Date
 * @author   Artur Świerc
 */
class WeekDay extends \Enp\Enum
{
	const MONDAY	= 1; 
	const TUESDAY	= 2; 
	const WEDNESDAY	= 3; 
	const THURSDAY	= 4; 
	const FRIDAY	= 5; 
	const SATURDAY	= 6;
	const SUNDAY	= 0;
	
	/**
	 * @var array
	 */
	protected $labels = array(
		self::MONDAY	=> 'Poniedziałek', 
		self::TUESDAY	=> 'Wtorek', 
		self::WEDNESDAY	=> 'Środa', 
		self::THURSDAY	=> 'Czwartek', 
		self::FRIDAY	=> 'Piątek', 
		self::SATURDAY	=> 'Sobota', 
		self::SUNDAY	=> 'Niedziela'
	);
	
	/**
	 * @return string
	 */
	public function getCurrentDay() 
	{
		return $this->labels[date('w')];
	}
	
	/**
	 * @return int
	 */
	public function getCurrentDayNumeric() 
	{
		return date('w');
	}
}
