<?php

namespace Enp\Date;

/**
 * @category Enp
 * @package  Enp_Date
 * @author   Artur Świerc
 */
class DateRange
{
	/**
	 * 
	 * @param \DateTime $from
	 * @param \DateTime $to
	 * @param int $rangeDays
	 * @param null|string $format
	 * @return array
	 */
	public function generateList(\DateTime $from, \DateTime $to, $rangeDays, $format = null)
	{
		$rangeDays = (int) $rangeDays;
		// do not modify param object 
		$from  = clone $from;
		$range = array();
		
		if (null === $format) {
			$range[] = clone $from; 
		} else { 
			$range[] = $from->format($format);
		}
		
		while ($from->getTimestamp() <= $to->getTimestamp()) {
			$from->modify('+' . $rangeDays . ' days');
			
			if (null === $format) {
				$range[] = clone $from; 
			} else { 
				$range[] = $from->format($format);
			}
		}
		return $range;
	}
}
