<?php

namespace Enp\Db\Select;

/**
 * @package  Enp
 * @category Enp_Db_Select
 * @author   Piotr Flasza
 * @author   Artur Świerc
 * 
 * @TODO Trzeba się zastanowić czy wrzucamy również tutaj obsługę filtrów własnych oraz pluginów.
 */
class Builder
{
	/**
	 * @var \Zend_Db_Select
	 */
	protected $select;

	/**
	 * @param \Zend_Db_Select $select
	 */
	public function __construct(\Zend_Db_Select $select)
	{
		$this->select = $select;
	}
	
	/**
	 * @param array $filters
	 * @return \Zend_Db_Select
	 */
	public function setFilter(array $filters) 
	{	
		foreach ($filters as $key => $val) {		
			
			if (null === $val) {
				$this->select->where("$key is null");
				
			// like
			} elseif (preg_match('/_like$/', $key) == 1) {
				$key = preg_replace('/_like$/', '', $key);
				$this->select->where("$key LIKE '%?%' ", new \Zend_Db_Expr($val));
				
			// od
			} elseif (preg_match('/_od$/', $key) == 1) {
				$key = preg_replace('/_od$/', '', $key);
				$this->select->where("$key >= '?' ", new \Zend_Db_Expr($val));
				
			// do
			} elseif (preg_match('/_do$/', $key) == 1) {
				$key = preg_replace('/_do$/', '', $key);
				$this->select->where("$key <= '?' ", new \Zend_Db_Expr($val));
				
			// tab negacja
			} elseif (preg_match('/_not$/', $key) == 1) {
				if (!is_array($val)) {
					$val = (array) $val;
				}
				$key = preg_replace('/_not$/', '', $key);
				$this->select->where("$key NOT IN (?) ", $val);
				
			// tab
			} elseif (is_array($val)) {
				//$val[] = 0; // jednak nie mozna tu dodawac 0 poniewaz moze zwrocic dodoatkowe wyniki
				$this->select->where("$key IN (?) ", $val);
				
			// bind 
			} elseif (preg_match('/\?/', $key)) {
				$this->select->where($key, $val);
				
			// normal
			} elseif (!is_array($val)) {
				$val = trim($val);
				$this->select->where("$key = '?' ", new \Zend_Db_Expr($val));
			}
		}
		return $this->select;
	}
}
