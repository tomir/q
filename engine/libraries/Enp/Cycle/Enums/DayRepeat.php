<?php

namespace Enp\Cycle\Enums;

/**
 * @category Enp
 * @package  Enp_Cycle_Enums
 * @author   Artur Świerc
 */
class DayRepeat extends \Enp\Enum
{
	const FIRST  = 1; 
	const SECOND = 2; 
	const THIRD	 = 3;
	const FOURTH = 4;
	const LAST	 = 5;
	
	/**
	 * @var array
	 */
	protected $labels = array(
		self::FIRST		=> 'pierwszy(a)', 
		self::SECOND	=> 'drugi(a)', 
		self::THIRD		=> 'trzeci(a)', 
		self::FOURTH	=> 'czwarty(a)', 
		self::LAST		=> 'ostatni(a)'
	);
}
