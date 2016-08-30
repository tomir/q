<?php

include_once('Smarty/Smarty.class.php') ;

class SmartyLoad extends Smarty {
	
    public function __construct($path, $path2 = '')
	{
		@parent::__construct();
		$this->template_dir = $path.$path2;
		$this->compile_dir  = $path.'templates_c/';
		$this->config_dir   = $path.'configs/';
		$this->cache_dir    = $path.'cache/';
	}
}
?>