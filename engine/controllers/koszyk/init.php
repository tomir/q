<?php
if ($_SERVER['HTTPS'] != 'on' && SSL == 1){
	Common::Redirect('https://'.$_SERVER['HTTP_HOST'].$x);
}
$objOrder = new Order();

if(isset($_GET['joining'])) {
	$objOrder->saveTempJoining($_SESSION['order_id'], $_GET['joining']);
}

$tempOrder = $objOrder->getTempOrder($_SESSION['order_id']);

if($atr2 >= 2) {
	if((!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) && $tempOrder['joining'] == 1) 
		Common::redirect("/koszyk-login.html");
}

$main_template		= "template_order.php";