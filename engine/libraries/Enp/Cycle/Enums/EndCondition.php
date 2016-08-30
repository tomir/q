<?php

namespace Enp\Cycle\Enums;

/**
 * @category Enp
 * @package  Enp_Cycle_Enums
 * @author   Artur Świerc
 */
class EndCondition extends \Enp\Enum
{
	const NO_END_DATA = 1; 
	const NUMBER_OCCURRENCE = 2;
	const END_DATA = 3;
	
	/**
	 * @var array
	 */
	protected $labels = array(
		self::NO_END_DATA		=> 'Bez daty końcowej', 
		self::NUMBER_OCCURRENCE => 'Koniec po n wystąpieniach', 
		self::END_DATA			=> 'Data końcowa'
	);
}