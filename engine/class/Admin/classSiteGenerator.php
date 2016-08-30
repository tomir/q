<?php
class SiteGenerator {
	
	protected $pdo;
	protected $smarty;
	protected $wwwPatch;
	protected $serverPatch;
	protected $siteWidth;
	
	public function __construct($pdo){
		
		$this -> pdo = $pdo;
		$this -> smarty = new SmartyLoad(MyConfig::getValue("templatePatch"),"admin");
		$this -> wwwPatch = MyConfig::getValue("wwwPatch");
		$this -> serverPatch = MyConfig::getValue("serverPatch");
		$this -> siteWidth = MyConfig::getValue("siteWidth");
	}
	
	public function createSite() {
		
		$this-> smarty-> assign('wwwPatch', $this -> wwwPatch);
		$this-> smarty-> assign('serverPatch', $this -> serverPatch);
		$this-> smarty-> assign('siteWidth', $this -> siteWidth);
		$finalContent = $this->smarty -> fetch("newSite.tpl");
		return $finalContent;
	}
	
}
?>