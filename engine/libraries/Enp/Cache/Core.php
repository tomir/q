<?php

namespace Enp\Cache;

/**
 * @package Enp_Cache
 * @author  Piotr Flasza
 * @author  Artur Świerc
 */
class Core extends \Zend_Cache_Core 
{	
	const DEFAULT_NAMESPACE = 'Standard';
	
	/**
	 * @var boolean
	 */
	protected $refresh = false;
	
	/**
	 * @var string
	 */
	protected $namespace = self::DEFAULT_NAMESPACE;
	
	/**
	 * @return boolean
	 */
	public function getRefresh() 
	{
		return $this->refresh;
	}

	/**
	 * @param boolean $refresh
	 */
	public function setRefresh($refresh) 
	{
		$this->refresh = $refresh;
	}
	
	public function setDefaultNamespace() 
	{
		$this->namespace = self::DEFAULT_NAMESPACE;
	}
	
	/**
	 * @param string $namespace
	 */
	public function setNamespace($namespace) 
	{
		$this->namespace = $namespace;
	}

	/**
	 * @return string
	 */
	public function getNamespace() 
	{
		return $this->namespace;
	}
	
	/**
	 * @param int $id
	 * @param boolean $doNotTestCacheValidity
	 * @param boolean $doNotUnserialize
	 * @return boolean
	 */
	public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false) 
	{	
		if ($this->getRefresh() == true) {
			return false;
		}	
		return parent::load($id, $doNotTestCacheValidity, $doNotUnserialize);
	}
		
	/**
     * @param  mixed	$data				Data to put in cache (can be another type than string if automatic_serialization is on)
     * @param  string	$id					Cache id (if not set, the last cache id will be used)
     * @param  array	$tags				Cache tags
     * @param  int		$specificLifetime   If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @param  int		$priority			integer between 0 (very low priority) and 10 (maximum priority) used by some particular backends
     * @throws Zend_Cache_Exception
     * @return boolean True if no problem
     */
	public function save($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8)
	{
		$tags[] = $this->namespace;
		return parent::save($data, $id, $tags, $specificLifetime, $priority);
	}
	
	/**
	 * @param type $namespace
	 * @throws \Enp\Exception
	 */
	public function clearByNamespace($namespace) 
	{
		if (empty($namespace)) {
			throw new \Enp\Exception('nie podano przestrzeni nazw dla cache');
		}
		
		$this->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($namespace));
	}
}
