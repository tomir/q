<?php

namespace Enp;

/**
 * Zwraca oraz przechowuje instancje klas
 * Realizuje wzorzec Singleton dla tych klas
 * 
 * @author Piotr Flasza
 */
class Instance
{
	/**
	 * @var array
	 */
	static protected $instancesOfClasses = array();

	/**
	 * @param string $className
	 * @return object
	 */
	static public function getInstanceOfClass($className) 
	{
		if (!isset(self::$instancesOfClasses[$className])) {
			self::$instancesOfClasses[$className] = new $className();
		}

		return self::$instancesOfClasses[$className];
	}

}
