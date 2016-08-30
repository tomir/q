<?php
if ($_SERVER['HTTPS'] != 'on' && SSL == 1){
	Common::Redirect('https://'.$_SERVER['HTTP_HOST'].$x);
}
$obProfile = new Profile();

if(isset($_POST['aData'])) {
	
	$data		= $_POST['aData'];
	foreach($data as &$row) {
		$row = trim($row);
	}
		
	$login = $obProfile -> zaloguj($data['user_email'], $data['user_pass']);
	if($data['koszyk'] == 1) {
		if(!$login) {
			header("Location: ".$wwwPatchSsl."koszyk-login.html?kom=1");
			exit();
		} else {
			header("Location: ".$wwwPatchSsl."koszyk-2.html");
			exit();
		}
	} else {
		if(!$login) {
			header("Location: /konkurs/index.php");
			exit();
		} else {
			header("Location: /konkurs/index.php?action=glosuj&id=".$_POST['aData']['projekt']);
			exit();
		}
	}
}
$head_title = 'Logowanie -';
