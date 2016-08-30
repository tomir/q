<?php

namespace Enp\Tool;

/**
 * @category Enp
 * @package  Enp_Tool
 * @author   Tomasz Cisowski
 */
class NoCamel {
	
	/**
	 * return underline values
	 * 
	 * @param string|array $name
	 */
	public static function format($name) 
	{
		if(is_array($name) && count($name) > 0) {
			foreach($name as $key=>$row) {
				$returnArray[strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key))] = $row;
			}
			
			return $returnArray;
		}
		return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
	}
}

