<?php

// load Smarty library files
require( _SMARTY_DIR . 'Smarty.class.php' );

class SmartyObj {

	static private $instance = null;

	private function __construct() {
		
	}

	/**
	 * 
	 * @return \Smarty
	 */
	static public function getInstance() {
		if (self::$instance == null) {

			if ($dir = @opendir(_TEMP_DIR)) {
				closedir($dir);
			} else {
				mkdir(_TEMP_DIR, 0775);
			}

			if ($dir = @opendir(_SMARTY_COMPILE_DIR)) {
				closedir($dir);
			} else {
				mkdir(_SMARTY_COMPILE_DIR, 0775);
			}

			if ($dir = @opendir(_SMARTY_CACHE_DIR)) {
				closedir($dir);
			} else {
				mkdir(_SMARTY_CACHE_DIR, 0775);
			}

			$smarty = new Smarty();

			$smarty->template_dir = _SMARTY_TEMPLATES_DIR;
			$smarty->compile_dir = _SMARTY_COMPILE_DIR;
			$smarty->config_dir = _SMARTY_CONFIG_DIR;
			$smarty->cache_dir = _SMARTY_CACHE_DIR;
			//$smarty->plugins_dir[] = MODELS_DIR . 'SmartyPlugins/';
			//$smarty->plugins_dir[] = LIBRARY_DIR . 'Enp/SmartyPlugins/';
			$smarty->caching = false;

			self::$instance = $smarty;
		}

		return self::$instance;
	}

}