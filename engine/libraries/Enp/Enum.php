<?php

namespace Enp;

/**
 * Abstract dla Enumów. Niestety w SPL nadal nie ma dostepnego typu ENUM
 * 
 * @author Artur Świerc
 * @author Piotr Flasza
 */
abstract class Enum
{
	/**
	 * @var string|int
	 */
	protected $value = null;
	
	/**
	 * @var array
	 */
	protected $constants = array(); 
	
	/**
	 * @var array
	 */
	protected $labels = array();
	
	/**
	 * @param string|int $value
	 */
	public function __construct($value = null) 
	{
		if (null !== $value) {
			$this->setValue($value);
		}
	}
	
	/**
	 * @param string|int $value
	 * @throws \UnexpectedValueException
	 */
	public function setValue($value) 
	{
		$consts = $this->getConstants();
		if (! in_array($value, $consts)) { 
			throw new \UnexpectedValueException(sprintf(
				"Wartosc %s nie jest dozwolona. Dozwolone wartosci to %s", 
				$value, 
				implode(', ', $consts)
			));
		}
		$this->value = $value;
	}
	
	/**
	 * @return string|int
	 */
	public function getValue() 
	{
		return $this->value;
	}
	
	/**
	 * @return array
	 */
	public function getConstants() 
	{
		if (empty($this->constants)) { 
			$reflection = new \ReflectionClass($this);
			$this->constants = $reflection->getConstants();
		}
		return $this->constants;
	}
	
	/**
	 * @return array
	 */
	public function getLabels() 
	{
		return $this->labels;
	}
	
	public function getLabel($enum = null) {
		if ($enum === null) {
			$enum = $this->value;
		}
		
		if (array_key_exists($enum, $this->labels)) {
			return $this->labels[$enum];
		}
		
		throw new \UnexpectedValueException("Wartość $enum nie posiada etykiety !");
	}
	
	/**
	 * @param  string $value
	 * @return string
	 */
	public function getConstantNameByValue($value)
	{
		$constants = $this->getConstants();
		$values = array_flip($constants);
		
		return (isset($values[$value])) ? $values[$value] : null;
	}
}
