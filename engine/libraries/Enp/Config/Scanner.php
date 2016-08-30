<?php

namespace Enp\Config;

/**
 * @category Enp
 * @package  Enp_Config
 * @author   Artur Świerc
 */
class Scanner
{
	/**
	 * @var string|array 
	 */
	protected $config;
	
	/**
	 * @param string|array $config	Path to config directory 
	 *								or array with paths to configs
	 */
	public function __construct($config)
	{
		if (is_string($config) && !is_dir($config)) {
			throw new \Enp\Config\Exception(sprintf(
				'Config directory %s does not exists', 
				$config
			));
		}
		$this->config = $config;
	}
	
	public function doRequire()
	{
		if (is_string($this->config)) { 
			$configDir = $this->config;
			$this->config = array();
			
			foreach (new \DirectoryIterator($configDir) as $file) {
			
				$extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
				
				if ($file->isDot() 
					|| !$file->isFile()
					|| $extension != 'php'	
				) { 
					continue;
				}
				$this->config[] = $file->getPathname();
			}
		}
			
		foreach ($this->config as $config) { 
			require_once $config;
		}
	}
}
