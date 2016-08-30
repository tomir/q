<?php

$obProfile = new Profile();
$aResult = $obProfile->getAddressBookList($_SESSION['user_id']);
$aCountry = $obProfile->getCountryListSelect();

if(isset($_POST['aData']) && count($_POST['aData']) > 0) {
	
	$aData = $_POST['aData'];
	$aData['user_id'] = $_SESSION['user_id'];
	$aData['address_name'] = strtolower($aData['address_city']).'_'.strtolower($aData['address_street']);
	
	$obProfile->saveAddressBook($aData);
	Common::redirect("konto-ksiazka.html");
}

if($_GET['action3'] == 'delete') {
	$obProfile->deleteAddressBook($_SESSION['user_id'], (int)$_GET['id']);
	Common::redirect("konto-ksiazka.html");
}

if($_GET['action3'] == 'setmain') {
	$obProfile->setMainAddressBook((int)$_GET['id'], $_SESSION['user_id']);
	Common::redirect("konto-ksiazka.html");
}