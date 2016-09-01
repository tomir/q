<?php
session_start();

date_default_timezone_set("Europe/Warsaw");
include("../engine/class/Admin/classConnectDB.php");

$komunikat = '';
$atr 	= $_GET['action'];
$atr2 	= $_GET['action2'];
$atr3 	= $_GET['action3'];
$atr4 	= $_GET['action4'];

$siteTitle	= MyConfig::getValue("admiSiteTitle");
$adminPatch	= MyConfig::getValue("wwwPatchPanel");
$tffPatch	= MyConfig::getValue("tffPatch");
$panelPatch	= MyConfig::getValue("wwwPatchPanel");
$wwwPatch	= MyConfig::getValue("wwwPatch");

if($_GET['stan'] != '' && $_GET['result'] != '')
	$komunikat = Komunikaty::error_info($_GET['result'],$_GET['stan']);

$obProfile = new Profile();
$aUser = array();

if(!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] != 'tak') {
	
	switch($atr) {
		case 'zaloguj':
			if(isset($_POST['login']) && isset($_POST['haslo'])) {
				
				$login = $obProfile -> zaloguj();
				if(!$login) {
					header("Location: ".MyConfig::getValue("wwwPatchPanel")."zaloguj.html,1,login_error");
					exit();
				} else {
					header("Location: ".MyConfig::getValue("wwwPatchPanel"));
					exit();
				}
			}
		break;
	}
	$main_template = 'a_templateLogowanie.php';
	
}
else {

	$aUser['username'] 	= $_SESSION['username'];
	$aUser['imie']		= $_SESSION['imie'];
	$aUser['email'] 	= $_SESSION['email'];
	$aUser['nazwisko']	= $_SESSION['nazwisko'];
	$aUser['group_id']	= $_SESSION['group_id'];
	$aUser['data_log']	= $_SESSION['last_login'];

	switch($atr) {
		//stale casy
		case 'kontakt':
				
			switch($atr2) {
				case 'send':
					$wynik = $obProfile -> sendForm($_POST, $zalogowany['email'], $zalogowany['login'], $zalogowany['imie'], $zalogowany['nazwisko']);
					if($wynik) header("Location: ".MyConfig::getValue("wwwPatchPanel")."kontakt.html,2,send_success");
					else  header("Location: ".MyConfig::getValue("wwwPatchPanel")."kontakt.html,1,send_error");
				break;
				
				default:
					$content = $obProfile -> showContactForm();
				break;
			}
			
			;
		break;
	
		case 'klienci':
			
			if($atr2 == 'edit') {
				$obKlient = new Klient($atr3); ;
				$aData = $obKlient->getDataAdmin();
				
				if(isset($_POST['formData'])) {
					$data = $_POST['formData'];
					$data['id'] = $atr3;
					Klient::updateData($data);
					
					if($aData['saldo'] != $data['saldo']) {
						$obMail = new Mail();
						$obMail->setReceiver($data['email']);
						$obMail->setFrom('kontakt@autolicytacje.pl');
						$obMail->setSubject("Aktualizacja salda w serwisie autolicytacje.pl");
						$obMail->generateMailTemplate('saldo', $data);
						$obMail->send();
					}
					
					header("Location: ".MyConfig::getValue("wwwPatchPanel")."klienci.html,2,edit_success");
				}
				
				$template = 'klienci/edit.php';
			} else {
			
				$obKlient = new Klient();
				
				if(!$_GET['records_number']) $rec_number = 50;
				else $rec_number = $_GET['records_number'];

				$list_count = Klient::getListQty();
				$show_pages = ceil($list_count/$rec_number);

				if(intval($atr2)) {
					$show_page = $atr2;
				} else $show_page = 1;

				$show_start = ($show_page * $rec_number) - $rec_number;
				$klienciList	= Klient::getList(null, null, array('start'=> $show_start, 'limit' => $rec_number));	

				$template = 'klienci/list.php';
			}
				
		break;
	
		case 'newsletter':
		
			$obNewsletter = new Admin_Newsletter();
			
			if($atr2 == 'activate') {
				$aData = $obNewsletter->getData($atr3);
				if($aData['blokada'] == 0) {
					$obNewsletter->blockEmail($atr3);
					echo MyConfig::getValue("wwwPatch").'images/admin/icons/main_off.png';
				}
				else {
					$obNewsletter->unblockEmail($atr3);
					echo MyConfig::getValue("wwwPatch").'images/admin/icons/main_on.png';
				}
				exit();
			}
			
			elseif($atr2 == 'view') {
				$aData = $obNewsletter->getData($atr3);
				
				$template = 'newsletter/newsletter_view.php';
			} else {
			
				if(!$_GET['records_number']) $rec_number = 50;
				else $rec_number = $_GET['records_number'];

				$list_count = $obNewsletter->getListCount();
				$show_pages = ceil($list_count/$rec_number);

				if(intval($atr2)) {
					$show_page = $atr2;
				} else $show_page = 1;

				$show_start = ($show_page * $rec_number) - $rec_number;
				$newsletterList	= $obNewsletter -> getList($show_start,$rec_number);	

				$template = 'newsletter/newsletter_list.php';
			}
				
		break;
	
		case 'car':
		
			$filtr = $_GET['filtr'];

			if(!$_GET['records_number']) $rec_number = 100;
			else $rec_number = $_GET['records_number'];

			$obCar = new Admin_Car();
			$obCar->setFiltr($filtr);
	
			$list_count = $obCar->carCount();
			$show_pages = ceil($list_count/$rec_number);

			if(intval($atr2)) {
				$show_page = $atr2;
			} else $show_page = 1;

			$show_start = ($show_page * $rec_number) - $rec_number;
			$carList	= $obCar -> carList($show_start,$rec_number,$_GET['records_order']);	
			
			$obOtomoto = new Admin_Otomoto();
			$aAdds = $obOtomoto->getAuctionAdds($_GET['filtr']['car_producer'], $_GET['filtr']['car_model']);
			
			if(is_array($filtr)) {
				$filtr_url = "?".http_build_query(array('filtr' => $filtr));
			}
			$template = 'car/car_list.php';
				
		break;
	
		case 'otomoto':
                    
			include(MyConfig::getValue("serverPatch").'admin/controllers/ctr_otomoto.php');
		break;

	}
	
	$main_template = 'a_template.php';

	$obTemplate = new Template();
	$aMenu = $obTemplate->getMenu();
} 
	include(MyConfig::getValue("templatePatch").'admin/'.$main_template);

