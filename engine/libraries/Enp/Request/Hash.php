<?php

namespace Enp\Request;

/**
 * @category Enp
 * @package  Enp_Request
 * @author   Krzysztof Deneka
 */
class Hash
{
	/**
	 * @return string|null
	 */
	public static function getHash() 
	{
		if(isset($_GET['hash']) && strlen($_GET['hash'])==32){
			return $_GET['hash'];
		}
		return null;
	}
}
