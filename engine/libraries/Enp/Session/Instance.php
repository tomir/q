<?php

namespace Enp\Session;

class Instance extends \Enp\Instance {

    static public function getInstanceOfClass($className) {
		if (!isset($_SESSION['instance'][$className])) {
			$_SESSION['instance'][$className] = new $className();
		}
		
		return $_SESSION['instance'][$className];
	}
	
	static public function delInstanceOfClass($className) {
		unset($_SESSION['instance'][$className]);
	}

}

?>
