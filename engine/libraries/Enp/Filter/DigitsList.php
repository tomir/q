<?php

namespace Enp\Filter; 

/**
 * @category Enp
 * @package Enp_Filter
 * @author Artur Świerc
 */
class DigitsList implements \Zend_Filter_Interface
{
	/**
	 * @param  array $list
	 * @return array
	 */
	public function filter($list) 
	{
		if (!is_array($list)) { 
			$list = array($list);
		}
		
		$filterDigits = new \Zend_Filter_Digits();
		
		foreach ($list as $k => $value) {
			$list[$k] = $filterDigits->filter($value);
		}
		return $list;
	}
}
