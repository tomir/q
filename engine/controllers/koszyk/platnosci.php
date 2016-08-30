<?php

$objOrder = new Order();
if(isset($_GET['secret']) && strlen($_GET['secret']) > 0) {
	$order = $objOrder->checkOrder($_SESSION['user_id'], $_GET['secret']);
	if(is_array($order) && count($order) > 0 && !empty($order)) {
		$bankPayByNet = $objOrder->getPaymentBank(1);
		$bankPrzelewy24 = $objOrder->getPaymentBank();

		$obPay = new \Platnosci\PayByNet\GeneratePayment();
		$obPay->setIdTrans($order['o_id']);
		$obPay->setEmail($order['o_email']);
		$obPay->setAmount($order['o_sum']+$order['o_fee']);
		$obPay->generatePayment();
	} elseif(!isset($_GET['kom'])) {
		Common::redirect("koszyk-platnosci.html?kom=1");
	}
} elseif(!isset($_GET['kom'])) {
	Common::redirect("koszyk-platnosci.html?kom=1");
}


