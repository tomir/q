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
		//end of ajax methods
		
		case 'stworz':

			$selected_table = $atr2;
			$selected_table2 = $atr3;
			$database		= "Tables_in_".MyConfig::getValue("dbDatabase");
			$database2		= "Tables_in_".MyConfig::getValue("dbDatabase").' (%_lang)';

			$obPanel -> getTableCreator($atr2, $atr3);
			$aForms				= $obPanel -> getFormDisplay(0);
			$contentList		= $obPanel -> contentList;
			$contentLangList	= $obPanel -> contentLangList;
			$columnList			= $obPanel -> columnList;
			$columnLangList		= $obPanel -> columnLangList;

			$content	= $obPanel -> getTableCreator($atr2);
			$template = 'p_creator.php';
		break;
		
		case 'lista':
			
			$aForms = $obPanel -> getFormDisplay();
			$template = 'p_formList.php';

		break;
		
		case 'pole':
			switch($atr2) {
				case 'edytuj':
					if($_POST['zapisz']) {
					$wynik = $obPanel -> saveField($atr3, $_POST);
					}
					else {
						$aPole = $obPanel -> getFieldDisplay($atr3);
					}
					$number = 1;
				break;
				
				case 'usun':
					$wynik = $obPanel -> deleteField($atr3);
					exit();
						
				break;
			}
			$template = 'p_editor.php';
		break;
		
		case 'podglad':
			$obFormularz = new Formularz($pdo);
			$content = $obFormularz -> start($atr,$atr2);
			$number = 1;
		break;
		
		case 'zapisz':
			
			$save = $obPanel -> saveForm($atr2, $atr3);
			if($save) {
				header("Location: ".MyConfig::getValue("tffPatch"));
			}

		break;
		
		case 'usun':
	
			$content = $obPanel -> deleteForm($atr2);
			$number = 1;
		break;
		
		default:
			
			$content = $obPanel -> getFormDisplay();
			$number = 1;
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
