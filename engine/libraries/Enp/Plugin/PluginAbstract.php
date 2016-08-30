<?php

namespace Enp\Plugin;

abstract class PluginAbstract 
{
	/**
	 * @var string
	 */
	protected $_module = null;
	
	/**
	 * @var string
	 */
	protected $_action = null;

	/**
	 * @return string
	 */
	public function getAction() {
		return $this->_action;
	}

	/**
	 * @return string
	 */
	public function getModule() {
		return $this->_module;
	}

	/**
	 * @param string $action
	 * @throws InvalidArgumentException
	 */
	public function setAction($action) 
	{
		$action = trim($action);
		if($action == '') {
			throw new \InvalidArgumentException('Nie podano nazwy akcji');
		}
		$this->_action = $action;
	}

	/**
	 * @param string $module
	 * @throws InvalidArgumentException
	 */
	public function setModule($module) 
	{
		$module = trim($module);
		if($module == '') {
			throw new InvalidArgumentException('Nie podano nazwy modułu');
		}
		$this->_module = $module;
	}

	abstract public function doRequest();
}
