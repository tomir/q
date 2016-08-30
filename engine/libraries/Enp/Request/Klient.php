<?php

namespace Enp\Request;

/**
 * @category Enp
 * @package  Enp_Request
 * @author   Piotr Flasza
 * @author   Artur Świerc
 */
class Klient
{
	/**
	 * @return int|null
	 */
	public static function getId() 
	{
		$klient = (int)$_SESSION['klientID'];
		if ($klient > 0) { 
			return $klient;
		}
		return null;
	}
}
