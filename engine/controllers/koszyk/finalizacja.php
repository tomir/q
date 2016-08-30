<?php

if(!isset($_SESSION['order_id']) || $_SESSION['order_id'] == 0) {
	Common::redirect("/");
}

$objOrder = new Order();

$aResult = $objOrder -> getTempOrder($_SESSION['order_id']);
$aPozycje = $objBasket->getBasketElements();

$obProfile = new Profile();
$aCountry = $obProfile->getCountryListSelect();

/*
 * Część właściwa
 */
$res = $objOrder->copyOrderFromTemp($_SESSION['order_id']);
if($res > 0) {
	
	$objOrderAdmin = new Admin_Order($res);
	$control	= $objOrderAdmin->getControl();
	
	$obMail = new Mail();
	$obMail -> setSubject("Potwierdzenie przyjęcia zamówienia ZAM_".$res . date("Y")." - Zambi.pl");
	$obMail -> generateMailTemplate('potwierdzenie', $res);
	$obMail -> send();
	$obMail -> send('biuro@zambi.pl');
}

$head_title = 'Koszyk -';