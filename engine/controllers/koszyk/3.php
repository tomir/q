<?php

$objOrder = new Order();

if(isset($_POST['aData']) && count($_POST['aData']) > 0) {
	
	$aData = $_POST['aData'];
	if ($aData['payment_id'] < 1 || $aData['transport_id'] < 1) {
		Common::redirect("koszyk-3.html?kom=1");
	}
	
	$aData['temp_id'] = $_SESSION['order_id'];
	$objOrder->updateTempOrder($aData);

	Common::redirect("koszyk-4.html");
}

$objOrder->saveTempStep1($_SESSION['order_id'], $idBasket);
$aResult = $objOrder -> getTempOrder($_SESSION['order_id']);
$aDevilery = $objOrder -> getDevilery($_SESSION['id_country'], (int)$aResult['payment_id']);

if($_SESSION['user_id'] > 0) {
	$objProfile = new Profile();
	$aUser = $objProfile -> getUser($_SESSION['user_id']);
}

$head_title = 'Koszyk -';