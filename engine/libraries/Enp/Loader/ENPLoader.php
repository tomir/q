<?php

namespace Enp\Loader;

use \Zend\Loader\StandardAutoloader;

class ENPLoader extends StandardAutoloader 
{
	public function autoload($class) 
	{
		if (file_exists(CLASS_DIR . $class . '.php')) {
			require_once(CLASS_DIR . $class . '.php');
			return true;
		}

		if (strtoupper($class) == 'XAJAX') {
			require_once(LIBRARY_DIR . "xajax/xajax_core/xajax.inc.php");
		} else {
			parent::autoload($class);
		}
	}
}
