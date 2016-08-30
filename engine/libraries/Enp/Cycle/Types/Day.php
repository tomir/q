<?php

namespace Enp\Cycle\Types;

use Enp\Cycle\Enums\EndCondition;

/**
 * @category Enp
 * @package  Enp_Cycle_Types
 * @author   Artur Świerc
 */
class Day extends \Enp\Cycle\CycleAbstract
{
	const PATTERN_EVERY_OTHER = 1; 
	const PATTERN_EVERY = 2; 
	
	/**
	 * @param  \DateTime $date
	 * @return boolean
	 */
	public function isValid(\DateTime $date) 
	{
		
		if (!$this->isValidDate($date)) { 
			return false;
		}
		
		$dateFrom = new \DateTime($this->data['start_date']);
		
		// check day patterns
		switch ($this->data['day_pattern']) {
			case self::PATTERN_EVERY_OTHER: 
				
				$day = $this->data['day_number_of'];
				
				$dateTo = clone $date; 
				$dateTo->modify('+1 days');
							
				$dateRange		= new \Enp\Date\DateRange();
				$dateRangeList  = $dateRange->generateList($dateFrom, $dateTo, $day, 'Y-m-d');
				
				if (!in_array($date->format('Y-m-d'), $dateRangeList)) {
					return false;
				}
				
				break;
				
			case self::PATTERN_EVERY: 
			default: 
				// its ok, every day 
				break;
		}
		
		// check end conditions 
		switch ($this->data['end_condition']) { 	
			
			case EndCondition::END_DATA:
				
				$endConditiondate = new \DateTime($this->data['end_condition_date']);
				$endConditiondate->modify($endConditiondate->format('Y-m-d') . ' 23:59:59');
				
				if ($endConditiondate <= $date) {
					return false;
				}
				
				break;
				
			case EndCondition::NUMBER_OCCURRENCE:
				
				$dayPattern = 1; 
				if ($this->data['day_pattern'] == self::PATTERN_EVERY_OTHER) { 
					$dayPattern = $this->data['day_number_of'];
				}

				$numberOccurrence	= $this->data['end_condition_number_occurrence'];				
				$interval			= $date->diff($dateFrom);
				$daysInterval		= $interval->format('%d days');
				$currentOccurrence  = $daysInterval / $dayPattern;
				
				if ($currentOccurrence > $numberOccurrence) { 
					return false;
				}
				
				break;
				
			case EndCondition::NO_END_DATA:
				break;
		}
		
		return true;
	}
}
