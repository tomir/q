<?php

namespace Enp\Request; 

/**
 * @category Enp
 * @package Enp_Request
 * @author Artur Świerc
 * @author Piotr Flasza
 */
class Referer extends \Enp\Db\Model
{	
	/**
	 * @var int
	 */
	protected static $refererId = null;
	
	/**
	 * @var array
	 */
	protected $_select_pola = array('opis');
		
	protected $_filtry = array(
		'kampania_id' => ' x.id IN (SELECT referer_id FROM kampania_referer WHERE kampania_id IN (?)) '
	);
	
	/**
	 * @return Referer
	 */
	public static function getInstance()
	{
		return self::getInstanceOfClass(__CLASS__);
	}
	
	/**
	 * @return boolean
	 */
	public function hasRegistered()
	{
		return (null !== self::$refererId) || 
			(isset($_COOKIE['request_referer']) && !empty($_COOKIE['request_referer']));
	}
	
	public function clearRegistry() 
	{
		if ($this->hasRegistered()) { 
			unset($_COOKIE['request_referer']);
			unset($_COOKIE['request_referer_id']);
			self::$refererId = null;
		}
	}
	
	/**
	 * @return null|int
	 */
	public function getRefererId() 
	{
		if (null !== self::$refererId) { 
			return (int) self::$refererId;
		}
		
		if (isset($_COOKIE['request_referer_id']) && !empty($_COOKIE['request_referer_id'])) {
			return (int) $_COOKIE['request_referer_id'];
		}
		
		return null;
	}
	
	public function register() 
	{		
		if ($this->hasRegistered()) { 
			return null;
		}
		
		$request = \Enp\Request::getInstance();
		$referer = $request->getServer('HTTP_REFERER');
		
		// direct 
		if (null === $referer) { 
			return null;
		}
		
		$sessionid = session_id();
		setcookie('request_referer', $sessionid, null, '/');
		
		$criteria = $this->getCriteria();
		
		if (empty($criteria)) { 
			return $criteria;
		}
		
		
		
		$refererId = null;
		foreach ($criteria as $crit) {
			if (preg_match("/" . $crit['warunek_url'] . "/i", $referer)) {
				$refererId = $crit['id'];
				break;
			}
		}
		
		self::$refererId = $refererId;
		setcookie('request_referer_id', $refererId, null , '/');
	}
	
	/**
	 * @param  int $cachelifetime
	 * @return array
	 */
	public function getCriteria($cachelifetime = CACHE_TIME_BIG)
	{
		$rows = $this->getCacheAll(
			$cachelifetime, 
			array(), 
			array('sort' => 'kolejnosc', 'order' => 'asc')
		);
		
		return $rows;
	}
	
	/**
	 * @return \Zend_Db_Table
	 */
	public function getDbTableObject() 
	{
		return \Enp\Db\TableFactory::get('referers');
	}
}
