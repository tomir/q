<?php

class Template {
	
 	/**
 	 * Template name
 	 *
 	 * @var string
 	 */
 	protected $templateName ;
 	/**
 	 * Template content
 	 *
 	 * @var string
 	 */
 	protected $siteContent ;
 	/**
 	 * Path to template
 	 *
 	 * @var string
 	 */
 	protected $templatePath;
 	/**
 	 * Name of site
 	 *
 	 * @var string
 	 */
 	protected $siteTitle;
 	/**
 	 * Array with language
 	 *
 	 * @var array
 	 */
 	protected $lang;
 	/**
 	 * title menu
 	 *
 	 * @var string
 	 */
 	protected $menuTitle;
 	protected $menuTitle2;
 	protected $komunikat;
 	protected $showMenu;
 	protected $zalogowany;
 	
 	public function __construct($templatePath, $templateName, $siteContent, $siteTitle)
 	{
 		$this -> templatePath  		= $templatePath;
 		$this -> templateName		= $templateName;
 		$this -> siteContent		= $siteContent;
 		$this -> siteTitle			= $siteTitle;	
 	}
 	
	public function showMenu($stan) {
		$this -> showMenu = $stan;
	}
	
	public function setKomunikat($komunikat) {
		$this -> komunikat = $komunikat;
	}

	public function setZalogowany($stan) {
		$this -> zalogowany = $stan;
	}
	
	public function setButton($menu = 1) {
		$this -> menuBase = $menu;
	}

	public function setActualSite($title) {
		$this -> menuTitle = $title;
	}
	
 	public function renderSiteAdmin() {
 		
 		$this -> sprawdzSciezke();
 		$template = new SmartyLoad($this -> templatePath, "admin/");
 		
 		$template -> assign("komunikat",		$this -> komunikat);
 		$template -> assign("siteTitle",		$this -> siteTitle);
 		$template -> assign("siteContent",		$this -> siteContent);
 		$template -> assign("wwwPatch",			MyConfig::getValue("wwwPatch"));
 		$template -> assign("wwwPatchPanel",	MyConfig::getValue("wwwPatchPanel"));
 		$template -> assign("tffPatch",			MyConfig::getValue("tffPatch"));
 		$template -> assign("zalogowany",		$this -> zalogowany);
 		$template -> assign("showMenu",			$this -> showMenu);
 		$template -> assign("actSite",			$this -> menuTitle);
 		$template -> assign("actSite2",			$this -> menuTitle2);
 		$template -> assign("aMenu",			$this -> getMenu());
 		
 		$template -> display($this -> templateName);

 	}
 	public function sprawdzSciezke() {
 		
 		try {
	 		$pdo = new ConnectDB();
			$pdo -> exec("SET names utf8");
			$q = $pdo ->query ("SELECT id_rodzina
								FROM ".MyConfig::getValue("dbPrefix")."cms_menu
								WHERE nazwa_url = '".$this -> menuTitle."' LIMIT 1");	
			$recordTable = $q -> fetchAll();
			$id_menu = $recordTable[0]['id_rodzina'];
			
			$q = $pdo ->query ("SELECT nazwa_url
								FROM ".MyConfig::getValue("dbPrefix")."cms_menu
								WHERE id_menu = ".$id_menu." LIMIT 1");
			$recordTable2 = $q -> fetchAll();
			if(count($recordTable2) > 0) {
				$this -> menuTitle2 = $this -> menuTitle;
				$this -> menuTitle = $recordTable2[0]['nazwa_url'];
			}
 		}
 		catch(PDOException $e) {}
 	}
 	
 	public function getMenu()
 	{
		$menuArray = array();
		$i = 0;
		$j = 0;
		try
        {
			$pdo = new ConnectDB();
			$pdo -> exec("SET names utf8");
			$q = $pdo ->query ("SELECT id_menu, nazwa_menu, nazwa_url, id_rodzina, rodzic
								FROM ".MyConfig::getValue("dbPrefix")."cms_menu
								WHERE active = 1
								ORDER BY kolejnosc, rodzic");	
			$recordTable = $q -> fetchAll();
			$aMenu = array();
			foreach($recordTable as $row) {
				if($row['rodzic'] == 1) {
					$aMenu[$i]['id_menu'] 	= $row['id_menu'];
					$aMenu[$i]['nazwa'] 	= $row['nazwa_menu'];
					$aMenu[$i]['nazwa_url'] = $row['nazwa_url'];
					$i++;
				}
			}
			$i = 0;
			foreach($aMenu as $row) {
				$j = 0;
				foreach($recordTable as $row2) {
					if($row2['id_rodzina'] && $row2['id_rodzina'] == $row['id_menu']) {
						if($j == 0)
							$aMenu[$i]['podmenu'] = array();
						$aMenu[$i]['podmenu'][$j]['id_menu'] 	= $row2['id_menu'];
						$aMenu[$i]['podmenu'][$j]['nazwa'] 		= $row2['nazwa_menu'];
						$aMenu[$i]['podmenu'][$j]['nazwa_url'] 	= $row2['nazwa_url'];
						$j++;
					}
						
				}
				$i++;
			}
        }
 		catch(PDOException $e)
		{
			opdErrorHandler($e); 
		}
		return $aMenu;
 	}
}
?>