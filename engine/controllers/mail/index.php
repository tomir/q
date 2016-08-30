<?php

$obMail = new Mail();
$obOrder = new Order();
if($obOrder -> checkOrder($_GET['o'], $_GET['c'])) {
	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
	if($_GET['s'] == 1) {
		$obMail -> generateMailTemplate('potwierdzenie', $_GET['o'], 1);
	}
	elseif($_GET['s'] == 3) {
		$obMail -> generateMailTemplate('realizacja', $_GET['o'], 1);
	}
	elseif($_GET['s'] == 2) {
		$obMail -> generateMailTemplate('przygotowane', $_GET['o'], 1);
	}
	elseif($_GET['s'] == 4) {
		$obMail -> generateMailTemplate('wyslano', $_GET['o'], 1);
	}
}
echo '</body></html>';
exit();