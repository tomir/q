<?php

namespace Enp\Request;

/**
 * @category	Enp
 * @package		Enp_Request
 * @author		Piotr Flasza
 * @author		Artur Świerc
 * @author		Krzysztof Deneka
 */
class Preview
{
	/**
	 * definicja zmiennej w REQUEST pod jaka bedzie
	 * przekazywana tablica potrzebnych parametrow
	 */
	const REQUEST_PARAM_NAME = 'enp';
	
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * 
	 * @return \Enp\Request\Preview
	 */
	public static function getInstance()
	{

		$class = \Enp\Session\Instance::getInstanceOfClass('\Enp\Request\Preview');

		if (isset($_GET[self::REQUEST_PARAM_NAME])) {
			$class->setData($_GET[self::REQUEST_PARAM_NAME]);
		}

		return $class;
	}

	/**
	 * @return bool
	 */
	public function isEnabled()
	{

		if (count($this->data) > 0) {
			return true;
		}
		return false;
	}

	/**
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * Przyklad:
	 * /telewizory?enp[param_1]=1&enp[param_2]='bleee'
	 * 
	 * @param string $key
	 * @return array
	 */
	public function getData($key = null)
	{
		if ($key == null) {
			return (array) $this->data;
		} else {
			if (isset($this->data[$key])) {
				return $this->data[$key];
			} else {
				return null;
			}
		}
	}

}
