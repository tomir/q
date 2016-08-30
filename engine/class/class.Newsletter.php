<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author tomi
 */
class Newsletter {

	public function zapisz($email, $filtr) {
		
		if($this->sprawdzCzyIstnieje($email) > 0) {
			return false;
		} else {
			
			$aData = array();
			$aData['email']				= $email;
			$aData['data_zapisania']	= date("Y-m-d H:i:s");
			$aData['aktywny']			= 0;
			
			$hash = md5($emai.time());
			$aData['hash']				= $hash;

			$id = ConnectDB::subAutoExec(MyConfig::getValue("dbPrefix")."newsletter_emails", $aData, 'INSERT');
			
			$i = 0;
			
			foreach($filtr['car_producer'] as $row) {
				
				if($row > 0 && ($filtr['car_model'][$i] > 0 || $filtr['car_model_str'][$i] != '')) {
					$aData2['email_id']		= $id;
					$aData2['producer_id']	= $row;
					$aData2['model_id']		= $filtr['car_model'][$i];
					$aData2['sphinx']		= $filtr['car_model_str'][$i];
					$aData2['rok_od']		= $filtr['car_year_od'][$i];
					$aData2['rok_do']		= $filtr['car_year_do'][$i];
					$aData2['car_type']		= $filtr['car_type'][$i];
					
					ConnectDB::subAutoExec(MyConfig::getValue("dbPrefix")."newsletter_spec", $aData2, 'INSERT');
					$aData2 = null;
				
				
					$filtr2['car_producer'] = $row;
					$filtr2['car_model'] = $filtr['car_model'][$i];
					if($filtr['car_year_od'][$i] > 0)
						$filtr2['car_year_od'] = $filtr['car_year_od'][$i];
					
					if($filtr['car_year_do'][$i] > 0)
						$filtr2['car_year_do'] = $filtr['car_year_do'][$i];

					$this->setResult($id, $filtr2);
					
					$filtr2 = null;
				}
				
				$i++;
			}
			
			$this->mailRejestracja($email, $hash);
			
			return true;
			
		}
	}
	
	public function setResult($id, $filtr2 = null) {
		
		if($filtr2 == null) {
			
		}
		
		$obCar = new Car();
		$obCar->setFiltr($filtr2);
		$aResult = $obCar->carList(0,25);

		if(is_array($aResult) && count($aResult) > 0) {
			foreach($aResult as $row_car) {
				if($row_car['car_id'] > 0)
					ConnectDB::subAutoExec(MyConfig::getValue("dbPrefix")."newsletter_result", array('car_id' => $row_car['car_id'], 'email_id' => $id), 'INSERT');
			}
		}
	}
	
	public function mailRejestracja($email, $hash) {
		
		$obMail = new Mail();
		$obMail->setReceiver($email);
		$obMail->setSubject("Potwierdzenie rejestracji usługi newslettera auto-licytacje.pl");
		$obMail->generateMailTemplate('potwierdzenie', array('email' => $email, 'hash' => $hash));
		$obMail->send();
			
	}
	
	public function activate($email, $hash) {
		
		$sql = "SELECT count(id) 
					FROM ".MyConfig::getValue("dbPrefix")."newsletter_emails 
					WHERE email = '".$email."' AND hash = '".$hash."' AND aktywny = 0";
		
		try {
	
			$ile = ConnectDB::subQuery($sql,'one');
			if($ile > 0) {
				ConnectDB::subAutoExec(MyConfig::getValue("dbPrefix")."newsletter_emails", array('aktywny' => 1, 'data_aktywacji' => date("Y-m-d H:i:s")), 'UPDATE', ' hash = "'.$hash.'"');
				return true;
			}
			
			return false;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','sprawdzCzyIstnieje',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
	}
	
	public function sprawdzCzyIstnieje($email) {
		
		$sql = "SELECT count(id) 
					FROM ".MyConfig::getValue("dbPrefix")."newsletter_emails 
					WHERE email = '".$email."'";
		
		try {
	
			$ile = ConnectDB::subQuery($sql,'one');
			
			return $ile;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','sprawdzCzyIstnieje',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
	}
	
	public function getEmails() {
		
		$sql = "SELECT *
					FROM ".MyConfig::getValue("dbPrefix")."newsletter_emails 
					WHERE aktywny = 1 AND blokada = 0";
		
		try {
	
			$all = ConnectDB::subQuery($sql);
			
			return $all;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','getEmails',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
	}
	
	public function getSpec($id) {
		
		$sql = "SELECT *
					FROM ".MyConfig::getValue("dbPrefix")."newsletter_spec 
					WHERE email_id = ".$id;
		
		try {
	
			$all = ConnectDB::subQuery($sql);
			return $all;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','getEmailCars',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
	}
	
	public function getEmailCars($id, $send = 0) {
		
		if($send == 0)
			$where = " AND send = 0";
		
		$sql = "SELECT car_id
					FROM ".MyConfig::getValue("dbPrefix")."newsletter_result 
					WHERE email_id = ".$id.$where;
		
		try {
	
			$all = ConnectDB::subQuery($sql);
			foreach($all as $row) {
				$new[$row['car_id']] = $row['car_id'];
			}
			
			return $new;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','getEmailCars',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
	}
	
}
?>
