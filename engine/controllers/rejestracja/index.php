<?php

$obProfile = new Profile();

if(isset($_POST['aData'])) {
	
	$register = $obProfile->rejestruj();
	if($register) {
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";

		$html = "Nowa rejestracja:\r\n\n<br /><br />";
		$html .= 'Imię: '.$_POST['aData']['user_first_name'].",\r\n<br />";
		$html .= 'Nazwisko: '.$_POST['aData']['user_last_name'].",\r\n<br />";
		$html .= 'Stanowisko: '.$_POST['aData']['user_phone'].",\r\n<br />";
		$html .= 'Firma: '.$_POST['aData']['user_street'].",\r\n<br />";
		$html .= 'Telefon: '.$_POST['aData']['user_adress'].",\r\n<br />";
		$html .= 'Email: '.$_POST['aData']['user_email']."\r\n<br />";
		$html .= 'IP: '.get_client_ip()."\r\n";

		mail('biuro@pureexpo.pl', 'Dokonano rejestracji na liderzyit.pl/konkurs', $html, $headers);
		//mail('t.cisowski@gmail.com', 'Dokonano rejestracji na liderzyit.pl/konkurs', $html, $headers);
		header("Location: /konkurs/index.php?action=glosuj&id=".$_POST['aData']['projekt']);
		exit();
	}
	else {
		header("Location: /konkurs/index.php?error=1");
		exit();
	}
	
}

header("Location: /konkurs/index.php?error=1");