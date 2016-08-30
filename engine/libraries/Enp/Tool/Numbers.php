<?php

namespace Enp\Tool;

/**
 * @category Enp
 * @package  Enp_Tool
 * @author   Artur Świerc
 */
class Numbers 
{
	/**
	 * return true if both $from and $to are null
	 * 
	 * @param float|null $from
	 * @param float|null $to
	 * @param float $value
	 */
	public static function isBetween($from, $to, $value) 
	{
		if (null !== $from && $value < $from) {
			return false;
		}
		
		if (null !== $to && $to < $value) { 
			return false;
		}
		
		return true;
	}
}
