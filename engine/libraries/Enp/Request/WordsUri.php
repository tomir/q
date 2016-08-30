<?php

namespace Enp\Request;

/**
 * fetch any words from request uri
 * 
 * @author Artur Świerc
 */
class WordsUri
{
	/**
	 * @return array
	 */
	public function get()
	{
		$request = \Enp\Request::getInstance();
		
		$uri = str_replace(
			array('/', ',', '?', '=', '&', "'", ' '), 
			'-', 
			$request->getRequestUri()
		);
		
		return  array_filter(explode('-', $uri));
	}
}
