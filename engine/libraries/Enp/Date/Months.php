<?php

namespace Enp\Date;

/**
 * @category Enp
 * @package  Enp_Date
 * @author   Artur Świerc
 */
class Months extends \Enp\Enum
{
	const STYCZEN		= 1; 
	const LUTY			= 2; 
	const MARZEC		= 3; 
	const KWIECIEN		= 4; 
	const MAJ			= 5; 
	const CZERWIEC		= 6;
	const LIPIEC		= 7;
	const SIERPIEN		= 8; 
	const WRZESIEN		= 9; 
	const PAZDZIERNIK	= 10; 
	const LISTOPAD		= 11; 
	const GRUDZIEN		= 12; 
		
	/**
	 * @var array
	 */
	protected $labels = array(
		self::STYCZEN		=> 'Styczeń', 
		self::LUTY			=> 'Luty', 
		self::MARZEC		=> 'Marzec', 
		self::KWIECIEN		=> 'Kwiecień', 
		self::MAJ			=> 'Maj', 
		self::CZERWIEC		=> 'Czerwiec', 
		self::LIPIEC		=> 'Lipiec', 
		self::SIERPIEN		=> 'Sierpień', 
		self::WRZESIEN		=> 'Wrzesień', 
		self::PAZDZIERNIK	=> 'Październik', 
		self::LISTOPAD		=> 'Listopad',
		self::GRUDZIEN		=> 'Grudzień'
	);
}
