<?php

namespace Enp\Date;

/**
 * @category Enp
 * @package  Enp_Date
 * @author   Artur Świerc
 */
class HoursRange
{
	/**
	 * @param  \DateTime	$from
	 * @param  \DateTime	$to
	 * @param  int			$range
	 * @param  string		$format  If format parameter is null, 
	 *								 then function returns a collection of DateTime objects
	 * @return \DateTime[]
	 */
	public function generateList(\DateTime $from, \DateTime $to, $rangeMinutes, $format = null) 
	{
		$rangeMinutes = (int) $rangeMinutes;
		$range		  = array();
		
		if (null === $format) { 
			$range[] = clone $from;;
		} else {
			$range[] = $from->format('H:i');
		}
		
		while ($from->getTimestamp() != $to->getTimestamp()) { 
			$from->modify('+' . $rangeMinutes . ' minutes');
			
			if (null === $format) { 
				$range[] = clone $from;
			} else { 
				$range[] = $from->format($format);
			}
		}
		
		return $range;
	}

	/**
	 * Rounded down to the numbers of minutes. 
	 * 
	 * For example: 
	 * 
	 *		roundFloorMinutes(new \DateTime('12:36'), 10)
	 *		will return \DateTime object with 12:30 time set
	 *		
	 * @param	\DateTime	$dateTime
	 * @param	int			$minutes
	 * @return  \DateTime
	 */
	public function roundFloorMinutes(\DateTime $dateTime, $minutes) 
	{
		$dateTimeMinutes = $dateTime->format('i');
		$ceilMinutes	 = $dateTimeMinutes - ($dateTimeMinutes % $minutes);
		
		return new \DateTime($dateTime->format('Y-m-d H:' . $ceilMinutes));
	}
}
