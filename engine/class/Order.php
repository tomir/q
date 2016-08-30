<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classOrder
 *
 * @author t.jurdzinski
 */
class Order {
    public function addTempStep1($basket_id) {
		$sql = "SELECT temp_id
			FROM shop_temp_order
			WHERE basket_id = ".$basket_id;

		$temp_id = ConnectDB::subQuery($sql, 'one');
		if($temp_id != 0)
			return $temp_id;

		$sql = "INSERT INTO shop_temp_order (basket_id)
			VALUES(".$basket_id.")";

		$wynik = ConnectDB::subExec($sql);
		return $wynik;
	}

	public function saveTempStep1($temp_order_id, $basket_id) {
		$sql = "UPDATE shop_temp_order
			SET temp_sum_all = (SELECT price_sum FROM shop_basket WHERE basket_id = ".$basket_id.") + devilery_price,
				temp_count_all = (SELECT count_elements FROM shop_basket WHERE basket_id = ".$basket_id.")
			WHERE temp_id = ".$temp_order_id; 
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
			return true;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function saveTempStep2($transport_id, $payment_id, $temp_order_id, $basket_id, $koszt = 0) {
		
		if($transport_id > 0) {
			$transport = "transport_id = ".$transport_id.",
				transport_price = (SELECT transport_price FROM shop_devilery_transport WHERE transport_id = ".$transport_id."), ";
		}
		
		if($payment_id > 0) {
			
			if($koszt > 0) {
				$payment = " payment_id = ".$payment_id.",
					payment_price = ".$koszt.", ";
			} else {
				$payment = " payment_id = ".$payment_id.",
					payment_price = (SELECT payment_price FROM shop_devilery_payment WHERE payment_id = ".$payment_id."), ";
			}
		}

		$sql = "UPDATE shop_temp_order
			SET ".$transport.$payment."
				
				temp_sum_all = (SELECT price_sum FROM shop_basket WHERE basket_id = ".$basket_id.")
			WHERE temp_id = ".$temp_order_id;
		//echo $sql; exit;
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
			return true;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function saveTempStep3($temp_order_id, $id_user, $aData) {
		
		$aData['user_id'] = $id_user;
		if($aData['temp_fv_address'] != 1) {
			$aData['temp_fv_first_name'] = '';
			$aData['temp_fv_last_name'] = '';
			$aData['temp_fv_phone'] = '';
			$aData['temp_fv_street'] = '';
			$aData['temp_fv_house_number'] = '';
			$aData['temp_fv_zip'] = '';
			$aData['temp_fv_city'] = '';
			$aData['temp_fv_country'] = '';
		}
		
		if($aData['temp_devilery_address'] != 1) {
			$aData['temp_delivery_first_name'] = '';
			$aData['temp_delivery_last_name'] = '';
			$aData['temp_delivery_phone'] = '';
			$aData['temp_delivery_street'] = '';
			$aData['temp_delivery_house_number'] = '';
			$aData['temp_delivery_zip'] = '';
			$aData['temp_delivery_city'] = '';
			$aData['temp_devilery_country'] = '';
		}

		try {

			ConnectDB::subAutoExec('shop_temp_order', $aData, 'UPDATE', 'temp_id = '.$temp_order_id);
			return true;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function saveTempStep4($temp_order_id, $temp_delivery_com) {
		$sql = "UPDATE shop_temp_order
			SET temp_comments = '".$temp_delivery_com."'
			WHERE temp_id = ".$temp_order_id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
			return true;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}

	public function saveTempJoining($temp_order_id, $joining) {
		$sql = "UPDATE shop_temp_order
			SET joining = ".$joining."
			WHERE temp_id = ".$temp_order_id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
			return true;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	public function copyOrderFromTemp($temp_order_id = 0) {

		if($temp_order_id != 0) {
			
			$aOrderTemp = $this->getTempOrder($temp_order_id);
			
			$obUser = new Profile();
			$aUser = $obUser->getUser($aOrderTemp['u_id']);
			$control = md5($aOrderTemp['u_id'].$aUser[0]['user_email'].time());
			
			if($aOrderTemp['joining'] == 0) {
				$control = md5($aOrderTemp['u_id'].$aOrderTemp['temp_user_email'].time());
				$email = $aOrderTemp['temp_user_email'];
			} else {
				$email = $aUser[0]['user_email'];
			}
			if (!$aUser['id_country']) {
				$aUser['id_country'] = 49;
			}
			
			$aData['user_id']					= $aOrderTemp['u_id'];
			
			$aData['o_customer_name']			= $aOrderTemp['temp_user_first_name'];
			$aData['o_customer_surname']		= $aOrderTemp['temp_user_last_name'];
			$aData['o_street']					= $aOrderTemp['temp_user_street'];
			$aData['o_house_number']			= $aOrderTemp['temp_user_house_number'];
			$aData['o_zip']						= $aOrderTemp['temp_user_zip'];
			$aData['o_city']					= $aOrderTemp['temp_user_city'];
			$aData['o_country']					= $aOrderTemp['temp_user_country'];
			$aData['o_phone']					= $aOrderTemp['temp_user_phone'];
			$aData['o_email']					= $aUser[0]['user_email'];
			
			if($aData['user_id'] > 0) {
				$adresy = $obUser->getAddressBookList($aData['user_id']	);
				if(!is_array($adresy) || count($adresy) < 1 || empty($adresy)) {
					$this->prepareAddressBook($aData);
				}
			}
			
			if ($aOrderTemp['temp_delivery_street'] != '') {
				$aData['d_customer_name']		= $aOrderTemp['temp_delivery_first_name'];
				$aData['d_customer_surname']	= $aOrderTemp['temp_delivery_last_name'];
				$aData['d_street']				= $aOrderTemp['temp_delivery_street'];
				$aData['d_house_number']		= $aOrderTemp['temp_delivery_house_number'];
				$aData['d_zip']					= $aOrderTemp['temp_delivery_zip'];
				$aData['d_city']				= $aOrderTemp['temp_delivery_city'];
				$aData['d_country']				= $aOrderTemp['temp_delivery_country'];
			} else {
				$aData['d_customer_name']		= $aOrderTemp['temp_user_first_name'];
				$aData['d_customer_surname']	= $aOrderTemp['temp_user_last_name'];
				$aData['d_street']				= $aOrderTemp['temp_user_street'];
				$aData['d_house_number']		= $aOrderTemp['temp_user_house_number'];
				$aData['d_zip']					= $aOrderTemp['temp_user_zip'];
				$aData['d_city']				= $aOrderTemp['temp_user_city'];
				$aData['d_country']				= $aOrderTemp['temp_user_country'];
			}
			
			if ($aOrderTemp['temp_fv_company'] != '') {
				$aData['f_company']				= $aOrderTemp['temp_fv_company'];
				$aData['f_nip']					= $aOrderTemp['temp_fv_nip'];
				$aData['f_street']				= $aOrderTemp['temp_fv_street'];
				$aData['f_house_numer']			= $aOrderTemp['temp_fv_house_number'];
				$aData['f_zip']					= $aOrderTemp['temp_fv_zip'];
				$aData['f_city']				= $aOrderTemp['temp_fv_city'];
				$aData['f_country']				= $aOrderTemp['temp_fv_country'];
			}
			
			$aData['o_fee']						= $aOrderTemp['payment_price']+$aOrderTemp['transport_price'];
			$aData['o_payment_status']			= 0;
			$aData['o_comments']				= $aOrderTemp['temp_comments'];
			$aData['o_datetime']				= date('Y-m-d H:i:s');
			$aData['o_items']					= $aOrderTemp['temp_count_all'];
			$aData['o_sum']						= $aOrderTemp['temp_sum_all'];
			$aData['devilery_id']				= $this->mapDevilery($aOrderTemp['payment_id'], $aOrderTemp['transport_id']);
			
			$aData['status_id']					= 1;
			$aData['control']					= $control;
			
			
			$order_id = ConnectDB::subAutoExec('shop_order', $aData, 'INSERT');
			if ($order_id > 0) {
				
				$obBasket = new Basket();
				$obBasket->setBasketId($aOrderTemp['basket_id']);
				$aBasketItems = $obBasket -> getBasketElements();

				foreach ($aBasketItems as $row) {
					
					if ($row['p_promo_price'] != 0) {
						$price = $row['p_promo_price']; 
					} else {
						$price = $row['p_price_gross'];
					}
					
					$aElement['o_id']		= $order_id;
					$aElement['i_name']		= $row['p_name'];
					$aElement['i_pieces']	= $row['items'];
					$aElement['i_price']	= $price;
					$aElement['i_sum']		= floatval($row['items']*$price);
					$aElement['p_id']		= $row['p_id'];
					
					$order_item_id = ConnectDB::subAutoExec('shop_order_items', $aElement, 'INSERT');
					Product::incrementSale($row['p_id']);
					$aElement	= null;
					$price		= 0;
				}
				
				if ($order_item_id > 0) {
					
					$obBasket->deteleAllBasketItems();
					$this->deleteTempOrder($temp_order_id);
					
					unset($_SESSION['order_id']);
					return $order_id;
				}
			}
		} 
		
		return false;

	}
	public function prepareAddressBook($aData) {
		
		$newData['address_main']		= 1;
		$newData['user_id']				= $aData['user_id'];
		$newData['address_name']		= strtolower($aData['o_city']).'_'.strtolower($aData['o_street']);
		$newData['type_id']				= 1;
		$newData['address_first_name']	= $aData['o_customer_name'];
		$newData['address_last_name']	= $aData['o_customer_surname'];
		$newData['address_street']		= $aData['o_street'];
		$newData['address_house_number']= $aData['o_house_number'];
		$newData['address_zip']			= $aData['o_zip'];
		$newData['address_city']		= $aData['o_city'];
		$newData['address_country']		= $aData['o_country'];
		$newData['address_phone']		= $aData['o_phone'];
		$newData['address_m_date']		= date('Y-m-d H:i:s');
		
		$objProfile = new Profile();
		$objProfile->saveAddressBook($newData);
		
		return true;
	}

	public function getOrderElements($order_id) {

		$sql = "SELECT * FROM shop_order_items o
			LEFT JOIN shop_product_media pm ON (pm.m_i = (SELECT m_i FROM shop_product_media AS pm2 WHERE pm2.p_id = o.p_id AND pm2.m_jpg = 1 ORDER BY pm2.m_main DESC, pm2.m_order ASC LIMIT 1) )
			WHERE o.o_id = ".$order_id;
	
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;

	}

	public function getOrderList($user_id) {
		$sql = "SELECT * FROM shop_order o
			LEFT JOIN shop_order_status s ON o.status_id = s.status_id
			LEFT JOIN shop_devilery d ON o.devilery_id = d.devilery_id
			LEFT JOIN shop_devilery_transport t ON d.devilery_transport = t.transport_id
			LEFT JOIN shop_devilery_payment p ON d.devilery_payment = p.payment_id
			WHERE o.user_id = ".$user_id." 
			ORDER by o.o_datetime DESC";

		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
			foreach($aResult as &$row) {
				$row['pozycje'] = $this->getOrderElements($row['o_id']);
			}
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
		
	}

	public function getDevilery($id_country = false, $devilery_payment = 1) {
		if(!$id_country)
			$id_country = 49;
		if($devilery_payment == 0)
			$devilery_payment = 1;
		$sql = "SELECT * FROM shop_devilery WHERE id_country = ".$id_country." AND devilery_active = 1";
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
			foreach($aResult as $row) {
				$aPayment[$row['devilery_payment']] = $row['devilery_payment'];
			}
			$aResult['payment'] = $this->getPayment($aPayment);
			
			$sql = "SELECT * FROM shop_devilery WHERE id_country = ".$id_country." AND devilery_active = 1 AND devilery_payment = ".$devilery_payment;
			
			$aResult2 = array();
			$aResult2 = ConnectDB::subQuery($sql);
				
			foreach($aResult2 as $row) {
				$aTransport[$row['devilery_transport']] = $row['devilery_transport'];
			}
			
			$aResult['transport'] = $this->getTransport($aTransport);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
		return $aResult;
	}
	
	public function getTransport($ids) {
		
		$sql = "SELECT * FROM shop_devilery_transport WHERE transport_id IN (".implode(',', $ids).")";
	
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}
	
	public function getPayment($ids) {
		
		$sql = "SELECT * FROM shop_devilery_payment WHERE payment_id IN (".implode(',', $ids).")";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}
	
	public function mapDevilery($paymentId, $transportId) {
		
		$sql = "SELECT devilery_id FROM shop_devilery WHERE devilery_transport = {$transportId} AND devilery_payment = {$paymentId}";
	
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'one');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function getPaymentAjax($id) {
		$sql = "SELECT *, format(devilery_price,2) as cena_ok FROM shop_devilery WHERE devilery_id = ".$id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, '', '', 'fetch');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return json_encode($aResult);
	}

	public function updateTempOrder($aData) {
		
		try {
			if($aData['temp_id'] != 0)
				$res = ConnectDB::subAutoExec ("shop_temp_order", $aData, "UPDATE", "temp_id = ".$aData['temp_id']);
		
			if($res)
				return $res;
			else
				return false;
		} catch (Exception $e){
			Log::SLog(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}

	public function getTempOrder($temp_order_id) {
		$sql = "SELECT o.*, p.payment_name, t.transport_name, p.payment_id as payment_id
			FROM shop_temp_order o
			LEFT JOIN shop_devilery_transport t ON o.transport_id = t.transport_id
			LEFT JOIN shop_devilery_payment p ON o.payment_id = p.payment_id
			WHERE o.temp_id = ".intval($temp_order_id);
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'fetch');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function deleteTempOrder($temp_order_id) {
		$sql = "DELETE FROM shop_temp_order WHERE temp_id = ".$temp_order_id." LIMIT 1";
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return true;
	}

	public function checkOrder($id_user, $control) {
		$sql = "SELECT *
			FROM shop_order
			WHERE user_id = ".intval($id_user)." AND control = '".$control."'";
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'fetch');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
		return $aResult;
		
	}
	
	public function getPaymentBank($paybynet = 0) {
		
		if($paybynet == 1) {
			$sql = "SELECT * FROM shop_devilery_payment_bank WHERE id_paybynet != 0 AND bank_active = 1";
		} else {
			$sql = "SELECT * FROM shop_devilery_payment_bank WHERE id_paybynet = 0";
		}
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

}
?>
