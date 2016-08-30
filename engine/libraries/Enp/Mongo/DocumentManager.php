<?php

namespace Enp\Mongo; 

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager as DM;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * @category Enp
 * @package  Enp_Mongo
 * 
 * @author Piotr Flasza
 * @author Tomasz Czaplicki
 * @author Artur Świerc
 */
class DocumentManager
{
	/**
	 * @var \Doctrine\ODM\MongoDB\DocumentManager
	 */
	static protected $dm = null;
	
	static protected $options = array(
		'mongoDocuments' => '', 
		'mongoCache'	 => '', 
		'defaultDb'		 => '', 
		'host'			 => '',
		'enabled'		 => false
	);
	
	protected function __construct() 
	{		
		// do nothing
	}
	
	/**
	 * @return boolean
	 */
	public static function checkMongoExtension() 
	{
		return self::$options['enabled'] && class_exists('Mongo');
	}
	
	/**
	 * @param array $options
	 */
	public static function init(array $options = array())
	{
		self::$options = array_merge(self::$options, $options);
	}
	
	/**
	 * @return \Doctrine\ODM\MongoDB\DocumentManager
	 * @throws \Enp\Exception
	 */
	public static function getInstance() 
	{
		if (null === self::$dm) {
			
			if (!self::checkMongoExtension()) {
				throw new \Enp\Exception('Mongo extension is required');
			}
			
			AnnotationDriver::registerAnnotationClasses();
						
			$config = new Configuration();
			$config->setProxyDir(self::$options['mongoCache']);
			$config->setProxyNamespace('Proxies');
			$config->setHydratorDir(self::$options['mongoCache']);
			$config->setHydratorNamespace('Hydrators');
			$config->setDefaultDB(self::$options['defaultDb']);
			$config->setMetadataDriverImpl(AnnotationDriver::create(self::$options['mongoDocuments']));
			
			$config->setLoggerCallable(function(array $log) {
				//\Common_FirePHP::debug($log);
			});
			
			$conn = new Connection(self::$options['host'], array(), $config);
			self::$dm = DM::create($conn, $config);
		}
		
		return self::$dm;
	}
}
