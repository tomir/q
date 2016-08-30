<?php

$objOrder = new Order();
$tempOrder = $objOrder->getTempOrder($_SESSION['order_id']);

if(!$_SESSION['user_id'] && $tempOrder['joining'] == 1) {
	$atr2 = 'login';
} else {
	if($_SESSION['user_id'] > 0) {
		$objOrder->updateTempOrder(array('temp_id' => $_SESSION['order_id'],
			'u_id' => $_SESSION['user_id']
		));
	}
	
	$obProfile = new Profile();
	
	if(isset($_POST['aData']) && count($_POST['aData']) > 0) {
		$objOrder->saveTempStep3($_SESSION['order_id'], $_SESSION['user_id'], $_POST['aData']);
		Common::redirect("koszyk-3.html");
		
	}
	$aCountry = $obProfile->getCountryListSelect();
	
	if(!isset($_GET['joining']) || $_GET['joining'] != 0) {
		$aAddressBook = $obProfile->getAddressBookList($_SESSION['user_id']);
		$aUserData = $obProfile->getAddressBook($_SESSION['user_id']);
	}

}

$head_title = 'Koszyk -';