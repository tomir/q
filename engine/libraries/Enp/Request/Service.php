<?php

namespace Enp\Request;

/**
 * @category Enp
 * @package  Enp_Request
 * @author   Piotr Flasza
 * @author   Artur Świerc
 */
class Service
{
	/**
	 * @return int|null
	 */
	public static function getId() 
	{
		if (defined('ID_SERWISU')) { 
			return ID_SERWISU;
		}
		return null;
	}
}
