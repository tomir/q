<?php

namespace Enp\Admin;

class Controller {
	
	static public function run($controllerName, $actionName, $controllerPath, $templateDir) {
		
		/**
		 * konwersja nazwy na ClassName
		 */
		$controllerName = str_replace(array('_','-'), ' ', $controllerName);
		$controllerName = ucwords(strtolower($controllerName));
		$controllerName = str_replace(' ', '', $controllerName);
		
		$actionName = strtolower($actionName);
		
		$controllerFile = $controllerPath.$controllerName.'.php';
		if (file_exists($controllerFile)) {
			
			require_once $controllerFile;
			
			/**
			 * sprawdzenie czy istnieje config
			 */
			$config = self::getConfig($controllerName, $controllerPath);
			
			$controllerObj = new $controllerName($config);
			
			/* @var $controllerObj \Enp\Admin\Controller\Controller */
			$controllerObj->setTemplateDir($templateDir);
			
			/**
			 * pierwszy raz jezeli uruchamiana metoda miala by wyswietalc widok 
			 * wykorzystywane w popupach kiedy w metodzie jest exit;
			 */
			$controllerObj->setTemplateView($actionName);
			
			/**
			 * uruchomienie metody z controllera
			 */
			$controllerObj->runAction($actionName);
						
			/**
			 * uruchamiane drugi raz jezeli uruchomiona metoda miala by
			 * wplynac na renderowany na koncu szablon widoku
			 */
			$controllerObj->setTemplateView($actionName);
			
			return true;
		}
		return false;
		
	}
	
	static public function getConfig($controllerName, $controllerPath) {
		
		$configName = $controllerName.'Config';
		
		$configFile = $controllerPath.$configName.'.php';
		if (file_exists($configFile)) {
			
			require_once $configFile;
			
			$config = new $configName();
			
			return $config;
		}
		
		return null;
	}
	
}
?>
