<?php

class Komunikaty {
	
	public function error_info($key, $error = 1,$text = '') {
	
		//****************** Komunikaty *************************
		//error		1
		//success	2
		//info		3
		//*******************************************************
		
		$komunikat = '<div class="komunikat_box_zew">';
		
		if($error == 1) {
			$komunikat .= '<img style="float: left;" src="'.MyConfig::getValue("wwwPatch").'images/admin/icons/alert.png" alt="" /> <div class="komunikat_box_wew" style="color: #EC832B;">';
		}
		elseif($error == 2) {
			$komunikat .= '<img style="float: left;" src="'.MyConfig::getValue("wwwPatch").'images/admin/icons/success.png" alt="" /> <div class="komunikat_box_wew" style="color: #17a808;">';
		}
		elseif($error == 3) {
			$komunikat .= '<img style="float: left;" src="'.MyConfig::getValue("wwwPatch").'images/admin/icons/info.png" alt="" /> <div class="komunikat_box_wew" style="color: #1A6DC9;">';
		}
		
		//ustawianie błędów
		switch ($key) {
			case 'login_error':
				$komunikat .= 'Błędny login lub hasło.<br /><span style="font-size: 12px; font-weight: normal;">Spróbuj ponownie.</span>';
			break;
			
			case 'video_empty':
				$komunikat .= 'Brak filmów do wyświetlenia<br />w wybranej kategorii.';
			break;
			
			case 'logout_success':
				$komunikat .= 'Zostałeś poprawnie wylogowany.<br /><span style="font-size: 12px; font-weight: normal;">Zapraszamy ponownie.</span>';
			break;
			
			case 'logout_active':
				$komunikat .= 'Osiągnięto maksymalny czas bezczynności.<br /><span style="font-size: 12px; font-weight: normal;">Prosimy ponownie się zalogować.</span>';
			break;
			
			case 'add_success':
				$komunikat .= 'Dane zostały poprawnie dodane do bazy.';
			break;
			
			case 'add_error':
				$komunikat .= 'Błąd dodawania danych do bazy.';
			break;
			
			case 'edit_success':
				$komunikat .= 'Dane zostały poprawnie zaktualizowane.';
			break;
			
			case 'edit_error':
				$komunikat .= 'Błąd zmiany danych.<br /><span style="font-size: 12px; font-weight: normal;">Spróbuj ponownie!</span>';
			break;
			
			case 'del_success':
				$komunikat .= 'Dane zostały poprawnie usunięte z bazy.';
			break;
			
			case 'del_error':
				$komunikat .= 'Błąd usuwania danych.';
			break;
			
			case 'brak_danych':
				$komunikat .= 'Nie wypełniono wszystkich wymaganych pól.';
			break;
			
			case 'access':
				$komunikat .= 'Podana strona nie istnieje lub nie masz uprawnień do jej oglądania.<br /><span style="font-size: 12px; font-weight: normal;">W razie potrzeby skontaktuj się z administratorem.</span>';
			break;
			
			case 'active_error':
				$komunikat .= 'Błąd logowania.<br /><span style="font-size: 12px; font-weight: normal;">Konto zostało zablokowane</span>';
			break;
			
			case 'send_success':
				$komunikat .= 'Wiadomość została wysłana.<br /><span style="font-size: 12px; font-weight: normal;">Dziękujemy</span>';
			break;
			
			case 'send_error':
				$komunikat .= 'Błąd wysyłania wiadomości.<br /><span style="font-size: 12px; font-weight: normal;">Spróbuj ponownie/span>';
			break;
			
			case 'underconstruction':
				$komunikat .= 'Wybrana funkcjonalność jest jeszcze w trakcie tworzenia.<br /><span style="font-size: 12px; font-weight: normal;">Zostaniesz poinformowany po zakończeniu prac.</span>';
			break;
		
			case 'otomoto_new':
				$komunikat .= 'Błąd wystawiania aukcji.<br /><span style="font-size: 12px; font-weight: normal;">'.$text.'.</span>';
			break;
		}
		
		$komunikat .= '</div><br style="clear: both;" /></div>';
		return $komunikat;
	}
}
?>