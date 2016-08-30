<?php

namespace Enp;

/** 
 * Wrapper dla \Zend_Filter, pozwala na używanie filtrów Enp z przestrzeniami nazw. 
 * @author Artur Świerc
 */
class Filter extends \Zend_Filter 
{
	protected static $_defaultNamespaces = array(
		'\Enp\Filter'
	);
	
	/**
     * @param  mixed        $value
     * @param  string       $classBaseName
     * @param  array        $args          OPTIONAL
     * @param  array|string $namespaces    OPTIONAL
     * @return mixed
     * @throws Zend_Filter_Exception
	 */
	public static function filterStatic($value, $classBaseName, array $args = array(), $namespaces = array()) 
	{
		$namespaces = array_merge((array) $namespaces, self::$_defaultNamespaces, array('Zend_Filter'));
		foreach ($namespaces as $namespace) {

			$className = self::_getExistClassName($namespace, $classBaseName);
			if (false === $className) {
				continue;
			}
			
			$class = new \ReflectionClass($className);
			if ($class->implementsInterface('Zend_Filter_Interface')) {
				if ($class->hasMethod('__construct')) {
					$object = $class->newInstanceArgs($args);
				} else {
					$object = $class->newInstance();
				}
				return $object->filter($value);
			}
		}
		throw new \Zend_Filter_Exception("Filter class not found from basename '$classBaseName'");
	}

	/**
	 * W pierwszej próbie zwracana jest nazwa klasy w konwecji ZF, w drugiej z przestrzeniami nazw. 
	 * Jeśli żadna z prób nie zwróci nazwy klasy to zwracany jest false.
	 * 
	 * @param String $namespace
	 * @param String $classBaseName
	 * @return String|false 
	 */
	private static function _getExistClassName($namespace, $classBaseName) 
	{			
		$className = $namespace . '_' . ucfirst($classBaseName);
		if (class_exists($className, false)) {
			return $className;
		}		
		try { 
			$file = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
			if (\Zend_Loader::isReadable($file)) { 
				\Zend_Loader::loadClass($className);
				return $className;
			}
		} catch (\Exception $e) {}
		
		$className = $namespace . '\\' . ucfirst($classBaseName);
		try { 
			if (class_exists($className, true)) {
				return $className;
			}
		} catch (\Exception $e) {} 
		
		return false;
	}
}
