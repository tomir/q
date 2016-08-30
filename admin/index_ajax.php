<?php 
session_start();
require_once('../config/config.php');

$atr1 = $_GET['action'];
$atr2 = $_GET['action2'];
$atr3 = $_GET['action3'];
$atr4 = $_GET['action4'];
$atr5 = $_GET['action5'];

$wwwPatch 	= MyConfig::getValue("wwwPatch");
$wwwPatchSsl 	= MyConfig::getValue("wwwPatchSsl");
$gfxPatch 		= MyConfig::getValue("gfxPatch");
$serverPatch 	= MyConfig::getValue("serverPatch");

$obAdminOrder = new Admin_Order();

switch($atr1) {

	case 'edit_order':

		$obAdminOrder -> editOrderAjax($_GET['id'], $_GET['param'], $_POST['value']);
		echo $_POST['value'];
		exit();

	break;

	case 'edit_price':

		$obAdminOrder -> editOrderItemAjax($_GET['id'], 'i_price', $_POST['value']);
		echo $_POST['value'];
		exit();

	break;

	case 'calc_order':
		if(!$_GET['fee'])
			$_GET['fee'] = 0;
		
		$result = $obAdminOrder -> calculateOrder($_GET['id'], $_GET['fee']);
		echo $result;
		exit();
	break;

	case 'calc_order_item':
		$result = $obAdminOrder -> calculateOrderItem($_GET['id']);
		echo $result;
		exit();
	break;

	case 'calc_order_fee':
		$result = $obAdminOrder -> calculateOrderFee($_GET['id']);
		echo $result;
		exit();
	break;

	case 'edit_devilery':

		$obAdminOrder -> editOrderAjax($_GET['id_order'], 'devilery_id', $_POST['value']);
		$edit = $obAdminOrder -> getDevileryName($_POST['value']);
		echo $edit[0]['devilery_name'];
		exit();

	break;

	case 'get_all_devilery':
		
		$aResult = $obAdminOrder -> getDevileryAjax();
		print json_encode($aResult);
		exit();

	break;

	case 'edit_pieces':

		$obAdminOrder -> editOrderItemAjax($_GET['id'], 'i_pieces', $_POST['value']);
		echo $_POST['value'];
		exit();

	break;

	case 'addOrder_ajax':

		$id_order = $obAdminOrder -> addTempOrderAjax();
		echo $id_order;
		exit();

	break;

	case 'addOrderItem_ajax':

		$id_item = $obAdminOrder -> addTempItemAjax($_POST['id_order'], $_POST['id_item'], $_POST['items']);
		echo $id_item;
		exit();

	break;

	case 'addOrderItem_ajax_edit': 
		
		$obAdminOrder -> addOrderItems($_POST['id_order'], $_POST['id_item'], $_POST['items']);
		exit();
		
	break;

	case 'deleteOrderItem_ajax':

		$obAdminOrder -> deleteTempItemAjax($_POST['id_order'], $_POST['id_item']);
		exit();

	break;

	case 'produkty-pop':

		$obProduct = new Product();
		if(isset($_POST['szukaj'])) {
			if($_POST['poczym'] == 2)
				$aResult = $obProduct -> searchProduct('',$_POST['keyword']);
			if($_POST['poczym'] == 1)
				$aResult = $obProduct -> searchProduct($_POST['keyword']);
		} else {
			$aResult = $obProduct -> getProductList(2, 10);
		}
		include($templatePatch."admin/t_popProduct.php");
		exit();

	break;
	
	case 'produkty-pop-edit':

			$obProduct = new Product();
			if(isset($_POST['szukaj'])) {
				if($_POST['poczym'] == 2)
					$aResult = $obProduct -> searchProduct('',$_POST['keyword']);
				if($_POST['poczym'] == 1)
					$aResult = $obProduct -> searchProduct($_POST['keyword']);
			} else {
				$aResult = $obProduct -> getProductList(2, 10);
			}
			include($templatePatch."admin/t_popProduct_edit.php");
			exit();

		break;

	case 'del_order_items':
		print_r($_POST['items']);
	break;

	case 'selectCategoryTreeMass':
		
		$cat_id = $_POST['selected_cat'];
		$id = $_POST['p_id'];

		$obCategory = new Admin_Category();
		echo $aCategories = $obCategory->selectMassAjaxCategory($id, $cat_id);
		exit();
		
	break;

	case 'selectCategoryTree':

		$cat_id = $_GET['cat_id'];
		$id = $_GET['id'];

		$obCategory = new Admin_Category($cat_id);
		echo $aCategories = $obCategory->selectAjaxCategory(true, $id);
		exit();

	break;

	case 'deselectCategoryTree':

		$cat_id = $_GET['cat_id'];
		$id = $_GET['id'];

		$obCategory = new Admin_Category($cat_id);
		echo $aCategories = $obCategory->selectAjaxCategory(false, $id);
		exit();

	break;

	case 'selectMainPhoto':
	
		$m_id = $_GET['m_id'];
		$id = $_GET['id'];

		$obProduct = new Admin_Product($id);
		$aProducts = $obProduct->selectAjaxMainPhoto(1, $m_id);
		
		$message =  "Dane zostały zapisane poprawnie zapisane do bazy.";
		$message_title =  "Dane zostały zapisane";
		$msg_template = "_message.php";

		include(TEMPLATES_DIR."admin/_messages/".$msg_template);
		exit();
		
	break;

	case 'deSelectMainPhoto':
	
		$m_id = $_GET['m_id'];
		$id = $_GET['id'];

		$obProduct = new Admin_Product($id);
		$aProducts = $obProduct->selectAjaxMainPhoto(0, $m_id);
		
		$message =  "Dane zostały zapisane poprawnie zapisane do bazy.";
		$message_title =  "Dane zostały zapisane";
		$msg_template = "_message.php";

		include(TEMPLATES_DIR."admin/_messages/".$msg_template);
		exit();
		
	break;

	case 'deletePhoto':
		
			$id = $_GET['id'];
			$m_id = $_GET['m_id'];
			
			$obProduct = new Admin_Product($id);

			if($obProduct->deleteFile($id, $m_id)) {
				$message =  "Zdjęcie zostało poprawnie usunięte.";
				$message_title =  "Usuwanie zdjęcia";
				$msg_template = "_message.php";
			} else {
				$error= true;
				$message =  "Zdjęcie nie zostało poprawnie usunięte.";
				$message_title =  "Usuwanie zdjęcia";
				$msg_template = "_error.php";
			}

			$aPhotos = $obProduct->getFileList();
			include(TEMPLATES_DIR."admin/_messages/".$msg_template);
			include(TEMPLATES_DIR."admin/_ajax/product/t_add_tab3_files.php");
			exit();
		
	break;

	case 'ajax_searchCustomer':
		
		$obAdminUser = new Admin_Users();
		$result = $obAdminUser->searchUser($_POST['customer']);
		echo Admin_AjaxHtml::searchUser($result);
		exit();
		
	break;

	case 'ajax_getUser':
		
		$obAdminUser = new Admin_Users($_POST['id_user']);
		
		$uData = array();
		$uData['FirstName'] = $obAdminUser->getFirstName();
		$uData['LastName'] = $obAdminUser->getLastName();
		$uData['Street'] = $obAdminUser->getStreet();
		$uData['Adress'] = $obAdminUser->getAdress();
		$uData['Adress2'] = $obAdminUser->getAdress2();
		$uData['City'] = $obAdminUser->getCity();
		$uData['IdCountry'] = $obAdminUser->getIdCountry();
		$uData['Zip'] = $obAdminUser->getZip();
		$uData['Email'] = $obAdminUser->getEmail();
		$uData['Phone'] = $obAdminUser->getPhone();
		$uData['ID'] = $obAdminUser->getId();
		echo json_encode($uData);
		exit();
		
	break;

	case 'ajax_savemapping':
		
		$objSupplierCategoryMapping = new \Admin\Supplier\Mapping();
		
		foreach($_POST['selected_cat'] as $key => $row) {
			foreach($row as $row2) {
				$objSupplierCategoryMapping->deleteByCatImport($key);
				$objSupplierCategoryMapping->save(array(
					'shop_hurtownie_import_category_id' => $key,
					'category_id' => $row2
				));
			}
		}

		echo 'ok';
		exit();
		
	break;
}
