<?php
session_start();
include("../engine/class/Admin/classConnectDB.php");

$komunikat = '';
$atr 	= $_GET['action'];
$atr2 	= $_GET['action2'];
$atr3 	= $_GET['action3'];

if($_GET['stan'] != '' && $_GET['result'] != '') {
	$komunikat = Komunikaty::error_info($_GET['result'],$_GET['stan']);
}

$obPanel 	= new Panel();
$obPanelJS 	= new PanelJS();
$aUser = array();

if(isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == 'tak') {

	$aUser['username'] 	= $_SESSION['username'];
	$aUser['imie']		= $_SESSION['imie'];
	$aUser['email'] 	= $_SESSION['email'];
	$aUser['nazwisko']	= $_SESSION['nazwisko'];
	$aUser['group_id']	= $_SESSION['group_id'];
	$aUser['data_log']	= $_SESSION['last_login'];

	switch($atr) {
		//ajax methods
		case 'pobierzKolumnyJS':
			$obPanelJS -> pobierzKolumnyJS();
			exit();
		break;
		
		case 'pobierzFormEditJS':
			$obPanelJS -> pobierzFormEditJS($atr2);
			exit();
		break;
		
		case 'zapiszFormEditJS':
			$obPanelJS -> zapiszFormEditJS($atr2);
			exit();
		break;
		
		case 'pobierzFormDetailsJS':	
			$obPanelJS -> pobierzFormDetailsJS($atr2);
			exit();
		break;

	}
	
	$siteTitle	= MyConfig::getValue("admiSiteTitle");
	$adminPatch = MyConfig::getValue("wwwPatchPanel");
	$tffPatch	= MyConfig::getValue("tffPatch");
	$panelPatch = MyConfig::getValue("wwwPatchPanel");
	$wwwPatch = MyConfig::getValue("wwwPatch");

	include(MyConfig::getValue("templatePatch").'admin/p_template.php');
}
else {
	header("Location: ".MyConfig::getValue("wwwPatchPanel")."blad.html,3,logout_active");
}
?>
