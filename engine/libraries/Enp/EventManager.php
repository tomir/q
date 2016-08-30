<?php

namespace Enp;

class EventManager {

	static protected $instance = null;

	/**
	 * Zwraca instancje Doctrinowskiego EventManager
	 * 
	 * @return \Doctrine\Common\EventManager
	 */
	static public function getInstance() {

		if (self::$instance === null) {
			self::$instance = new \Doctrine\Common\EventManager();
		}
		return self::$instance;
	}
}
