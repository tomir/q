<?php

$obOrder = new Order();
$aPozycje = $objBasket->getBasketElements();

if(!isset($_SESSION['order_id']) || $_SESSION['order_id'] < 1) {
	$order_id = $obOrder -> addTempStep1($idBasket);
	$_SESSION['order_id'] = $order_id;
} else {
	$aTemp = $obOrder->getTempOrder($_SESSION['order_id']); 
	if(!is_array($aTemp) || count($aTemp) < 1 || empty($aTemp)) {
		$order_id = $obOrder -> addTempStep1($idBasket);
		$_SESSION['order_id'] = $order_id;
	}
}

$head_title = 'Koszyk -';