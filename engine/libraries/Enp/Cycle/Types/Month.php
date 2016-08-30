<?php

namespace Enp\Cycle\Types;

use Enp\Cycle\Enums\EndCondition;
use Enp\Cycle\Enums\DayRepeat;

/**
 * @category Enp
 * @package  Enp_Cycle_Types
 * @author   Artur Świerc
 */
class Month extends \Enp\Cycle\CycleAbstract
{
	const PATTERN_SIMPLE	= 1; 
	const PATTERN_COMPLEX	= 2;
	
	/**
	 * @var \Enp\Date\WeekDay
	 */
	protected $dayWeek; 
	
	public function __construct() 
	{
		parent::__construct();		
		$this->dayWeek = new \Enp\Date\WeekDay();
	}
	
	/**
	 * @param  \DateTime $date
	 * @return boolean
	 */
	public function isValid(\DateTime $date) 
	{
		if (!$this->isValidDate($date)) { 
			return false;
		}
		
		$datePeriods = array();
		$monthPeriod = $this->data['month_number_of'];
		$interval = new \DateInterval("P{$monthPeriod}M");
		
		switch ($this->data['month_pattern']) {
			
			case self::PATTERN_SIMPLE: 
				
				$dateTo = clone $date;
				$monthDayNumber = sprintf("%02d", $this->data['month_number']);
				
				//$dateRepeatFrom = $this->getDateByRepeat($startDate);
				$startDate = new \DateTime($this->data['start_date']);
				$from = new \DateTime($startDate->format('Y-m-' . $monthDayNumber));
				$period = new \DatePeriod($from, $interval, $dateTo);
				
				foreach ($period as $p) {
					$datePeriods[] = $p->format('Y-m-d');
				}
				
				if (!in_array($date->format('Y-m-d'), $datePeriods)) { 
					return false;
				}
				
			case self::PATTERN_COMPLEX:
				
				$dateTo = clone $date;
				$dateTo->modify('+1 month');
				
				$startDate = new \DateTime($this->data['start_date']);
				$dateRepeatFrom = $this->getDateByRepeat($startDate);
				$period = new \DatePeriod($dateRepeatFrom, $interval, $dateTo);
				
				foreach ($period as $p) {
					$period = $this->getDateByRepeat($p);
					$datePeriods[] = $period->format('Y-m-d');
				}
				
				if (!in_array($date->format('Y-m-d'), $datePeriods)) { 
					return false;
				}
				
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
				
				$numberOccurrence = $this->data['end_condition_number_occurrence'];
				
				if (count($datePeriods) > $numberOccurrence) { 
					return false;
				}
				
				break;
				
			case EndCondition::NO_END_DATA:
				break;
		}

		return true;		
	}
	
	/**
	 * @param  \DateTime
	 * @return \DateTime
	 */
	protected function getDateByRepeat($startDate) 
	{
		$from = new \DateTime($startDate->format('Y-m-01'));
		$from->modify('-1 day');
		
		$dayConstant = $this->dayWeek->getConstantNameByValue($this->data['month_day_name']);
		$dayFirst	 = ucfirst($dayConstant);
		$from->modify('first ' . $dayFirst);
		
		switch ($this->data['month_day_number']) {
			
			case DayRepeat::FIRST:
				// do nothing - current date is first
				break;
			
			case DayRepeat::SECOND:
				$from->modify('+1 week');
				break;
			
			case DayRepeat::THIRD:
				$from->modify('+2 week');
				break;
			
			case DayRepeat::FOURTH:
				$from->modify('+3 week');
				break;
			
			case DayRepeat::LAST:
				$from = new \DateTime($startDate->format('Y-m-31'));
				$from->modify('last ' . date('l', $this->data['month_day_name']));
				break;
		}
		return $from;
	}
}
