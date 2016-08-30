<?php

namespace Enp\Date;

/**
 * @category Enp
 * @package Enp_Date
 * @author Artur Świerc
 */
class Date
{
	/**
	 * @var \DateTime
	 */
	protected $dateTimeValue = null;
	
	/**
	 * @var array swieta stale
	 */
	protected $holidays = array(
		'01-01',
		'01-06',
		'03-31',
		'04-01',
		'05-01',
		'05-03',
		'05-19',
		'05-30',
		'08-15',
		'11-01',
		'11-11',
		'12-25',
		'12-26'
	);
	
	/**
	 * @param \DateTime $dateTimeValue
	 */
	public function __construct(\DateTime $dateTimeValue = null) 
	{
		if (null === $dateTimeValue) {
			$this->setDateTimeValue(new \DateTime());
		}
		$this->dateTimeValue = $dateTimeValue;
		
		$this->_initHoliDays(); 
	}
	
	/**
	 * @param \DateTime $dateTimeValue
	 */
	public function setDateTimeValue(\DateTime $dateTimeValue) 
	{
		$this->dateTimeValue = $dateTimeValue;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDateTimeValue() 
	{
		return $this->dateTimeValue;
	}
	
	/**
	 * @param boolean $checkWeekend czy sprawdzamy rowniez sobote, niedziele
	 * @return boolean
	 */
	public function isHoliday($checkWeekend = true) 
	{
		// swieto 
		if (in_array($this->dateTimeValue->format('m-d'), $this->holidays)) { 
			return true;
		}
		// weekend 
		if ($checkWeekend && in_array($this->dateTimeValue->format('w'), array(0, 6))) { 
			return true;
		}
		return false;
	}
	
	/**
	 * @todo do zaimplementowania
	 * @see na oponach mialem to dosyc ciekawie zaimplementowane 
	 * 
	 * @throws \Exception
	 */
	public function getNextWorkDay($checkWeekend = true) 
	{
		if ($this->isHoliday($checkWeekend)) {
			$this->dateTimeValue->modify('+1 day');
			return $this->getNextWorkDay($checkWeekend);
		}
		return $this->dateTimeValue;
	}
	
	/**
	 * Obliczanie swiat ruchomych
	 */
	protected function _initHoliDays() 
	{ 
		$year = $this->dateTimeValue->format('Y');
		
		$easter = date('m-d', easter_date($year));
		$easterDate = strtotime($year . '-' . $easter);
		
		$easterSec = date('m-d', strtotime("+1 day", $easterDate));
		$zieloneSwiatki = date('m-d', strtotime('+50 days', $easterDate));
		$bozeCialo = date('m-d', strtotime('+60 days', $easterDate));
		
		$this->holidays[] = $easter;
		$this->holidays[] = $easterSec; 
		$this->holidays[] = $zieloneSwiatki; 
		$this->holidays[] = $bozeCialo;
	}
}
