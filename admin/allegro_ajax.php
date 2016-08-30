<?php

require_once('../config/config.php');


$atr1 = $_GET['action'];
$atr2 = $_GET['action2'];
$atr3 = $_GET['action3'];
$atr4 = $_GET['action4'];
$atr5 = $_GET['action5'];

$wwwPatch 	= MyConfig::getValue("wwwPatch");
$wwwPatchSsl 	= MyConfig::getValue("wwwPatchSsl");
$gfxPatch 		= MyConfig::getValue("gfxPatch");
$serverPatch 	= MyConfig::getValue("serverPatch");

$obAllegroAjax = new Allegro_Ajax();

switch($atr1) {

	case 'checkApiKey':

		$res = array();
		$result = $obAllegroAjax -> ajaxTestApiKey($_POST['apikey']);
		
		if(is_string($result) && strlen($result) == 10) {
			$res['date'] = $result;
			$message =  "Połączenie z Web-Api Allegro przebiegło pomyślnie.";
			$message_title =  "Sukces!";
			$msg_template = "_success.php";
		} else {
			$message =  $result['error_string'];
			$message_title =  "Błąd!";
			$msg_template = "_error.php";
		}
		
		$res['info'] = include TEMPLATES_DIR.'admin/_ajax/request/'.$msg_template;
		//$res['info'] = base64_encode($res['info']);
		echo json_encode($res);
		exit();

	break;

}
?>
