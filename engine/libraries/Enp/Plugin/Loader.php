<?php

namespace Enp\Plugin;

class Loader 
{
	/**
	 * @param \Enp\PluginAbstract $plugin
	 * @param string $module
	 * @param string $action
	 */
	static public function registry(\Enp\Plugin\PluginAbstract $plugin, $module, $action) 
	{ 
		$plugin->setAction($action);
		$plugin->setModule($module);
		$plugin->doRequest();
	}
}