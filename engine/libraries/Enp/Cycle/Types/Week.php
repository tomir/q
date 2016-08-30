<?php

namespace Enp\Cycle\Types;

use Enp\Cycle\Enums\EndCondition;

/**
 * @category Enp
 * @package  Enp_Cycle_Types
 * @author   Artur Świerc
 */
class Week extends \Enp\Cycle\CycleAbstract
{
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
	 * @param \DateTime $date
	 * @return boolean
	 */
	public function isValid(\DateTime $date) 		
	{
		if (!$this->isValidDate($date)) { 
			return false;
		}
		
		$selectedDay = $this->getSelectedDays();
		$currDay	 = $this->dayWeek->getCurrentDayNumeric();
		$weekPeriod  = (int) $this->data['week_number_of'];
		
		if (!in_array($currDay, $selectedDay)) { 
			return false;
		}
		
		$dateFrom = new \DateTime($this->data['start_date']);
		// move cursor to current day
		$dateFrom->modify($date->format('l'));
		
		// create range weeks 
		$interval = new \DateInterval("P{$weekPeriod}W");
		$period = new \DatePeriod($dateFrom, $interval, $date);
		
		$periodList = array();
		foreach ($period as $pDate) {
			$periodList[] = $pDate->format('Y-m-d');
		}
		
		if (!in_array($date->format('Y-m-d'), $periodList)) { 
			return false;
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
				
				if (count($periodList) > $numberOccurrence) { 
					return false;
				}
				
				break;
				
			case EndCondition::NO_END_DATA:
				break;
		}
		
		return true;
	}
	
	/**
	 * @return array
	 * @throws \Enp\Cycle\Exception
	 */
	public function getSelectedDays() 
	{
		if (empty($this->data)) { 
			throw new \Enp\Cycle\Exception('cannot fetch days with empty data');
		}
		
		$days = $this->dayWeek->getLabels();
		$data = $this->data; 
		return array_filter(array_keys($days), function($value) use ($data) {
			return ($data['week_day_' . $value] == 1);
		});
	}
}
