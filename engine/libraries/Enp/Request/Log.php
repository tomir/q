<?php

namespace Enp\Request;

/**
 * Logowanie requestow 
 * np bledy 404 i 500
 * 
 * @author aswierc
 */
class Log
{
	/**
	 * @var string
	 */
	protected $path;
	
	public function __construct() 
	{
		$path = APPLICATION_DIR . 'request_logs/';
		if (!is_dir($path)) { 
			mkdir($path);
		}
		$this->path = $path . 'logs.csv';
	}
	
	/**
	 * @param \Zend_Controller_Request_Http $request
	 */
	public function doLog(\Zend_Controller_Request_Http $request) 
	{	
		$csvrow = '"' . implode('";"', array(
			date('Y-m-d H:i:s'), 
			$request->getClientIp(),
			$request->getPathInfo(), 
			$request->getHeader('USER_AGENT')
		)) . '"';
		
		file_put_contents($this->path, $csvrow . PHP_EOL, FILE_APPEND);
	}
}
