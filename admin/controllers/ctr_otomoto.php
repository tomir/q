<?php

$aTime = array(0, 15, 30, 45);
define('LIMIT_AUKCJI_NA_CRONA', 25);

$obOtomoto = new Admin_Otomoto();
switch($atr2) {

	case 'edit':

		if($_POST['form_action'] == 'save' || $_POST['form_action'] == 'wystaw') {

			$komunikat = false;
			/*
			 * zapisujemy/edytujemy wpisane dane
			 */
			if($_POST['formData']['id'] > 0) {
				$obOtomoto->updateAuction($_POST['formData']);
				$id_auction = $_POST['formData']['id'];
			} else {
				$id_auction = $obOtomoto->saveAuction($_POST['formData']);

			}

			if($_POST['form_action'] == 'wystaw') {

				/*
				* po zapisanu/wyedytowaniu wystawiamy/edytujemy na Otomoto
				*/
				$obOtomotoApi = new Otomoto_Api();
				if($_POST['formData']['otomoto_id'] != '') {
					$_POST['formData']['id'] = $_POST['formData']['otomoto_id'];
				} else {
					unset($_POST['formData']['id']);
				}

				unset($_POST['formData']['car_id']);
				unset($_POST['formData']['otomoto_id']);

				$result = $obOtomotoApi->newOffer($_POST['formData']);

				if($result['blad'] == 1) {
					$komunikat = Komunikaty::error_info('otomoto_new', 1, $result['tresc']);
				} elseif($result['status'] == 'FORM_ERROR') {
					foreach($result['error-list'] as $error) {
						$tresc_bledu .= $error->desc."<br />";
					}
					$komunikat = Komunikaty::error_info('otomoto_new', 1, $tresc_bledu);
				} else {
					$_POST['formData']['otomoto_id'] = $result['insertion']->id;
					$obOtomoto->updateAuction(array('active' => 1, 'otomoto_id' => $result['insertion']->id, 'id' => $id_auction), false);

					//$_POST['formData']['photos'] = array_reverse($_POST['formData']['photos']);
					$i = 1;
					foreach($_POST['formData']['photos'] as $row) {
						$result2 = $obOtomotoApi->addPhoto(MyConfig::getValue("serverPatch").$_POST['formData']['photo_files'][$row], $_POST['formData']['otomoto_id'], $i);
						if($result2['blad'] == 1) {
							$komunikat = Komunikaty::error_info('otomoto_new', 1, $result2['tresc']);
						}
						$i++;
					}
				}
			}

			if(!$komunikat) {
				header("Location: ".MyConfig::getValue("wwwPatchPanel")."otomoto.html,2,edit_success");
			}

			$aData = $obOtomoto->getData($id_auction);
			$aData['info']		= $obOtomoto->getAuctionInfo($id_auction);
			$aData['equipment']	= $obOtomoto->getAuctionEquipment($id_auction);
			$aData['photo']		= $obOtomoto->getAuctionPhotos($id_auction);

			//print_r($aData);

			$obCar = new Admin_Car();
			$aCar['photo'] = $obCar->carGetPhotos($atr4);

		} else {

			$obCar = new Admin_Car();

			$aCar = array();
			$aCar['photo']		= $obCar->carGetPhotos($atr4);


			if($atr3 > 0) {
				$aData = $obOtomoto->getData($atr3);
				$aData['info']		= $obOtomoto->getAuctionInfo($atr3);
				$aData['equipment']	= $obOtomoto->getAuctionEquipment($atr3);
				$aData['photo']		= $obOtomoto->getAuctionPhotos($atr3);
				$aCar				= $obCar->carGet($atr4);
			} else {
				$aCar				= $obCar->carGet($atr4);
				$aCar['photo']		= $obCar->carGetPhotos($atr4);
				$aCar['info']		= $obCar->carGetInfo($atr4);
				$aCar['equipment']	= $obCar->carGetEquipment($atr4);
				
			}

		}

		if(is_array($aData) && count($aData) > 1) {
			$aAdds = $obOtomoto->getAuctionAdds(0,0,$aData['make-id'], $aData['model-id'],'CAR', $aData['allegro-cat-id']); 
			$aAddsMoto = $obOtomoto->getAuctionAdds(0,0,$aData['make-id'], $aData['model-id'],'MOTORBIKE', $aData['allegro-cat-id']); 
			$aAddsTruck = $obOtomoto->getAuctionAdds(0,0,$aData['make-id'], $aData['model-id'],'TRUCK', $aData['allegro-cat-id']); 
			$aAddsConst = $obOtomoto->getAuctionAdds(0,0,$aData['make-id'], $aData['model-id'],'CONSTRUCTION', $aData['allegro-cat-id']); 
			$aAddsAgro = $obOtomoto->getAuctionAdds(0,0,$aData['make-id'], $aData['model-id'],'AGRO', $aData['allegro-cat-id']); 
		} else {
			$aAdds = $obOtomoto->getAuctionAdds($aCar['producer_id'], $aCar['model_id'], 0,0,'CAR', $aData['allegro-cat-id']); 
			$aAddsMoto = $obOtomoto->getAuctionAdds($aCar['producer_id'], $aCar['model_id'], 0,0,'MOTORBIKE', $aData['allegro-cat-id']); 
			$aAddsTruck = $obOtomoto->getAuctionAdds($aCar['producer_id'], $aCar['model_id'], 0,0,'TRUCK', $aData['allegro-cat-id']); 
			$aAddsConst = $obOtomoto->getAuctionAdds($aCar['producer_id'], $aCar['model_id'], 0,0,'CONSTRUCTION', $aData['allegro-cat-id']); 
			$aAddsAgro = $obOtomoto->getAuctionAdds($aCar['producer_id'], $aCar['model_id'], 0,0,'AGRO', $aData['allegro-cat-id']); 
		}
		if($atr3 > 0) {
			$aCar = null;
			$aCar['photo']		= $obCar->carGetPhotos($atr4);
		}
		$template = 'otomoto/otomoto_edit.php';

	break;

	case 'activate':
		$aData = $obOtomoto->getData($atr3);
		if($aData['active'] == 1) {
			$obOtomoto->deactivateAuction($atr3);
			echo MyConfig::getValue("wwwPatch").'images/admin/icons/main_off.png';
		}
		else {
			$obOtomoto->activateAuction($atr3);
			echo MyConfig::getValue("wwwPatch").'images/admin/icons/main_on.png';
		}
		exit();
	break;


	case 'delete':
		$aData = $obOtomoto->getData($atr3); 
		if(strlen($aData['otomoto-id']) < 3) { 
			$atr4 = 'do_wystawienia';
		}
		$obOtomoto->deleteAuction($atr3);
		if($atr4 > 1 || strlen($atr4) > 1)
			header("Location: ".MyConfig::getValue("wwwPatchPanel")."otomoto,".$atr4.".html,2,del_success");
		else
			header("Location: ".MyConfig::getValue("wwwPatchPanel")."otomoto.html,2,del_success");
	break;

	case 'ajax_marka':
		$aAdds = $obOtomoto->getAuctionAdds(0,0,$_GET['marka'], $_GET['type']);
		$return['model'] = $aAdds['model'];
		$return['version'] = $aAdds['version'];

		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	break;

	case 'ajax_model':
		$aAdds = $obOtomoto->getAuctionAdds(0,0,$_GET['marka'], $_GET['model']);
		$return['version'] = $aAdds['version'];

		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	break;

	case 'ajax_allegro':
		$return = $obOtomoto->getAllegroCatsList($_GET['parent_id']);

		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	break;

	case 'ajax_podtyp':
		$obOtomotoApi	= new Otomoto_Api();
	
		$aResult		= array(); 
		if($_GET['kategoria'] == 'AGRO')
			$aResult		= $obOtomotoApi->getAgroDictionary('' ,'type', $_GET['nadwozie']);
		else {
			$aResult		= $obOtomotoApi->getConstructionDictionary('type', $_GET['nadwozie']);
		}
		foreach($aResult as $row) {

			$tmp = get_object_vars($row);
			$return['key'][] = array(
				'key' => $tmp['key'],
				'name' => $tmp['name']
			);
		}
		
		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	break;

	case 'ajax_podtyp2':
		$obOtomotoApi	= new Otomoto_Api();
	
		$aResult		= array(); 
		if($_GET['kategoria'] == 'AGRO') {
			$aResult		= $obOtomotoApi->getAgroDictionary($_GET['type'] ,'subtype1', $_GET['nadwozie']);
		} 
		
		foreach($aResult as $row) {

			$tmp = get_object_vars($row);
			$return['key'][] = array(
				'key' => $tmp['key'],
				'name' => $tmp['name']
			);
		}
		
		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	break;
	
	case 'ajax_podtyp3':
		$obOtomotoApi	= new Otomoto_Api();
	
		$aResult		= array(); 
		if($_GET['kategoria'] == 'AGRO') {
			$aResult		= $obOtomotoApi->getAgroDictionary($_GET['type'] ,'subtype2', $_GET['nadwozie'], $_GET['subtype']);
		} 
		
		foreach($aResult as $row) {

			$tmp = get_object_vars($row);
			$return['key'][] = array(
				'key' => $tmp['key'],
				'name' => $tmp['name']
			);
		}
		
		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	break;
	
	case 'ajax_podtyp4':
		$obOtomotoApi	= new Otomoto_Api();
	
		$aResult		= array(); 
		if($_GET['kategoria'] == 'AGRO') {
			$aResult		= $obOtomotoApi->getAgroDictionary($_GET['type'] ,'subtype3', $_GET['nadwozie'], $_GET['subtype'], $_GET['subtype2']);
		} 
		
		foreach($aResult as $row) {

			$tmp = get_object_vars($row);
			$return['key'][] = array(
				'key' => $tmp['key'],
				'name' => $tmp['name']
			);
		}
		
		header('Content-type: application/json');
		echo json_encode($return);
		exit();
	break;

	case 'delete_grupowo':
	
		foreach($_POST['item'] as $item) {
			$obOtomoto->deleteAuction($item);
		}
		
		header("Location: ".MyConfig::getValue("wwwPatchPanel")."otomoto,do_wystawienia.html");
		
	break;

	case 'wystaw_grupowo':

		$i = 1; $times = 0;
		foreach($_POST['item'] as $item) {

			if(is_int($i/LIMIT_AUKCJI_NA_CRONA)) $times += 900;
			$change = false;
			
			$aData['auction_id'] = $item;
			$aData['data_add'] = date("Y-m-d H:i:s");
			
			if(date("i") > $aTime[3]) { $min = '00'; $change = true; }
			elseif(date("i") > $aTime[2]) $min = $aTime[3];
			elseif(date("i") > $aTime[1]) $min = $aTime[2];
			elseif(date("i") > $aTime[0]) $min = $aTime[1];
			
			$aData['date_to_send'] = date("Y-m-d")." ".date("H").":".$min.":00";
			if($change) {
				$aData['date_to_send'] = date('Y-m-d H:i:s', strtotime($aData['date_to_send'])+3600);
			}
			
			if($times > 0) {
				$aData['date_to_send'] = date('Y-m-d H:i:s', strtotime($aData['date_to_send'])+$times);
			}
			
			Admin_Otomoto::addTask($aData);
			$aData = null;
			$i++;
		}

		header("Location: ".MyConfig::getValue("wwwPatchPanel")."otomoto,do_wystawienia.html");

	break;

	case 'do_wystawienia':

		$show_number = 150;
		$list_count = $obOtomoto -> auctionCount(false);
		$show_pages = ceil($list_count/$show_number);

		if(intval($atr3)) {
			$show_page = $atr3;
		} else $show_page = 1;

		$show_start = ($show_page * $show_number) - $show_number;

		$content = $obOtomoto->getAuctionList(false, $show_number, $show_start);
		$template = 'otomoto/otomoto_list.php';

	break;

	default:

		$show_number = 40;
		$list_count = $obOtomoto -> auctionCount(true);
		$show_pages = ceil($list_count/$show_number);

		if(intval($atr2)) {
			$show_page = $atr2;
		} else $show_page = 1;

		$show_start = ($show_page * $show_number) - $show_number;

		$content = $obOtomoto->getAuctionList(true, $show_number, $show_start);
		$template = 'otomoto/otomoto_list.php';
	break;
}

?>
