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
		
		case 'menu':
			//do napisania dopiero w momencie kiedy będziemy mieli uniwersalną klase do wyświetlania podglądu z bazy
			header("Location: ".MyConfig::getValue("wwwPatchPanel")."blad.html,3,underconstruction");
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
		
		case 'uzytkownicy':
			if($zalogowany['id_grupy'] != 1) header("Location: ".MyConfig::getValue("wwwPatchPanel")."blad.html,1,access");

			switch($atr2) {
				case 'dodaj':
					$obFormularz = new Formularz($pdo);
					$content = $obFormularz -> start($atr,1,$atr2);
				break;
				
				case 'edytuj':
					//with ajax used
					$obProfile->getUserAjax($atr3);
					exit();
				break;
				
				case 'zapisz':
					//with ajax used
					$obProfile->saveUserAjax($atr3);
					exit();
				break;
				
				case 'zmien':
					//with ajax used
					$obProfile->changeUserAjax($atr3);
					exit();
				break;
				
				case 'usun':
					$wynik = $obProfile->usunUsera($atr3);
					if($wynik) header("Location: ".MyConfig::getValue("wwwPatchPanel")."uzytkownicy.html,2,del_success");
					else  header("Location: ".MyConfig::getValue("wwwPatchPanel")."uzytkownicy.html,1,del_error");
				break;
				
				default:
					$content = $obProfile->pobierzListeUserow();
				break;
			}
		break;
		
		case 'zmienhaslo':

			if(!$_POST['zapisz']) {
				$template = 'a_changePass.php';
			}
			else {
				$wynik = $obProfile->zmienHaslo($_POST, $aUser['username']);
				if($wynik) header("Location: ".MyConfig::getValue("wwwPatchPanel").",2,edit_success");
				else header("Location: ".MyConfig::getValue("wwwPatchPanel")."zmienhaslo.html,1,edit_error");
			}
		break;
		
		case 'wyloguj':
			$obProfile -> wyloguj();
			header("Location: ".MyConfig::getValue("wwwPatchPanel")."zaloguj.html,2,logout_success");
		break;
		
		case 'blad':
			$content = "";
		break;

		case 'addpopup':
		    include(MyConfig::getValue("templatePatch").'admin/a_form_pop.php');
		    exit();
		break;
		
		//dynamiczne casy
		default:
			$obMain = new AdminMain($atr);
			switch($atr2) {
				case 'add':
					$obFormularz = new Formularz();
					if($atr4) {
						$aPow = $obMain -> showList(false, $atr4);
						$content = $obMain -> showPow($aPow);
					}
					$obFormularz -> start($atr, $obMain->id_form, $atr2, $atr4);
					$aPola = $obFormularz -> aPola;
					$akcja = $atr;
					$akcja2 = $atr2;
					$akcja4 = $atr4;
					$formId = $obMain->id_form;
					$lang_table = $obMain -> lang_table;

					if($lang_table) {
						$aLangs = $obFormularz -> getAvalibleLanguages();
						$template = 'a_formLang.php';
					}
					else
						$template = 'a_form.php';

				break;
				
				case 'change':
					//with ajax used, show/hide
					$obMain -> changeAjax($atr3);
					exit();
				break;
				
				case 'sort':
					//with ajax used, sort
					$obMain -> sortAjax($_POST['order']);
					exit();
				break;
				
				case 'delete':
					//delete
					$wynik = $obMain -> deleteAjax($atr3);
					$aHttp = explode(".html",$_SERVER['HTTP_REFERER']);
					if($wynik) header("Location: ".$aHttp[0].".html,2,del_success");
					else  header("Location:  ".$aHttp[0].".html,1,del_error");
					exit();
				break;
				
				case 'edit':
					$obFormularz = new Formularz();
					$aDane = $obFormularz -> start($atr, $obMain->id_form, $atr2, $atr4);
					$aPola = $obFormularz -> aPola;
					$akcja = $atr;
					$akcja2 = $atr2;
					$akcja4 = $atr4;
					$formId = $obMain->id_form;
					$lang_table = $obMain -> lang_table;

					if($lang_table) {
						$aLangs = $obFormularz -> getAvalibleLanguages();
						$template = 'a_formLang.php';
					}
					else
						$template = 'a_form.php';
					
				break;

				case 'view':
				    $obFormularz    = new Formularz();
				    //$aLangs	    = $obFormularz  -> getAvalibleLanguages();
				    $aData	    = $obFormularz  -> start($atr, $obMain->id_form, 'edit', $atr4);
				    $aColumns	    = $obFormularz  -> aPola;
				    if(isset($_POST['dodaj_pow'])) {
					$obFormularz  -> saveRelated($_POST['form_id'], $_POST);
				    }
				    if(isset($_POST['dodaj_pow_array'])) {
					$obFormularz  -> saveRelatedArray($_POST['form_id'], $_POST);
				    }
				    if(isset($_POST['zapisz_foto'])) {
					$obFormularz  -> saveRelatedPhotos($_POST['form_id'], $_POST);
				    }
				    $aRelatedData   = $obMain -> getMultiRelatedData($atr4);
				    //echo '<pre>';
				    //print_r($aRelatedData);
				    //echo '</pre>';
				    $lang_table = $obMain -> lang_table;


				    if($lang_table)
						$template = 'a_showLang.php';
					else
						$template = 'a_show.php';
				break;
				
				default:
			
					if($obMain->id_form == 12) {
						header("Location:  /admin/car.html");
					}

					//ile sms wyswietlamy na stronie
					$show_number = 100;
					$list_count = $obMain -> listCount($atr3);
					$show_pages = ceil($list_count/$show_number);

					if(intval($atr2)) {
						$show_page = $atr2;
					} else $show_page = 1;

					$show_start = ($show_page * $show_number) - $show_number;
					
					if($_POST["model"])
						$obMain -> searchModel($_POST["model"]);
					else
						$obMain -> showList($atr3, $show_number, $show_start);
					if($obMain -> pow_table['table'] != '')
						$aPow = $obMain -> getPowTable();
					
					$aColumns 		= $obMain -> getColumnList();
					$aResult 		= $obMain -> aWyniki;
					$dodActions 	= $obMain -> getDodActions();
					$idForm 		= $obMain -> id_form;
					$button 		= $obMain -> button;
					$filtr 			= $obMain -> pow_table;
					$menu_url 		= $obMain -> atr;
					$idColumn 		= $obMain -> idColumn;
					$visibleColumn 	= $obMain -> visibleColumn;
					
					if($menu_url == 'pojazdy') {
						foreach($aResult as $key => $row) {
							$obCar = new Admin_Car($row['car_id']);
							$aResult[$key]['photos'] = $obCar->carGetPhotos($row['car_id']);
						}
					}
					
					if($obMain -> sort)
						$template = 'a_listSort.php';
					else
						$template = 'a_list.php';
				break;
			}
		break;
	}
	
	$main_template = 'a_template.php';

	$obTemplate = new Template();
	$aMenu = $obTemplate->getMenu();
} 
	include(MyConfig::getValue("templatePatch").'admin/'.$main_template);

?>
