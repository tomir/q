<?php

namespace Enp;

class Request {

	/**
	 * @var \Zend_Controller_Request_Http
	 */
	static protected $_request = null;

	/**
	 * @return \Zend_Controller_Request_Http
	 */
	static public function getInstance() { 
		if (null === self::$_request) {
			self::$_request = new \Zend_Controller_Request_Http();
		}
		return self::$_request;
	}
	
	/**
	 * @return boolean
	 */
	static public function isAjaxRequest() { 
		return self::getInstance()->isXmlHttpRequest();
	}
}