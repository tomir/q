<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Newsletter
 *
 * @author tomaszcisowski
 */
class Admin_Newsletter {
	
	public function getData($id) {
		
		$sql = "SELECT *
					FROM ".MyConfig::getValue("dbPrefix")."newsletter_emails 
					WHERE id = ".$id;
		
		try {
	
			$aResult = (array)ConnectDB::subQuery($sql,'','','fetch');
			$aResult['wariants'] = $this->getEmailWariants($id);
			
			return $aResult;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','admin newsletter getData',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
	}
	
	public function getEmailWariants($id) {
		
		$sql = "SELECT ns.*, cm.model_name, cp.producer_name
					FROM ".MyConfig::getValue("dbPrefix")."newsletter_spec ns
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_producer cp ON cp.producer_id = ns.producer_id
					LEFT JOIN ".MyConfig::getValue("dbPrefix")."car_model cm ON cm.model_id = ns.model_id
					WHERE ns.email_id = ".$id;
		
		try {
	
			$aResult = (array)ConnectDB::subQuery($sql,'','','fetchAll');
			return $aResult;
			
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','admin newsletter getData',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
	}
	
	public function getList($start = 0, $ile = 10) {
		
		$sql = "SELECT *
					FROM ".MyConfig::getValue("dbPrefix")."newsletter_emails 
					WHERE 1 ORDER BY email LIMIT ".$start.", ".$ile;
		
		try {
	
			$aResult = (array)ConnectDB::subQuery($sql,'','','fetchAll');
			
			return $aResult;
		} catch(PDOException $e) {
			mail('t.cisowski@gmail.com','admin newsletter getList',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
	}
	
	public function getListCount() {
		
		$sql = "SELECT count(*) as ile FROM ".MyConfig::getValue("dbPrefix")."newsletter_emails WHERE 1 ";
		try {
			$result = ConnectDB::subQuery($sql);

		} catch (Exception $e) {
			echo $e->getTraceAsString();
		}
		
		return $result[0]['ile'];
	}
	
	public function blockEmail($id) {
		
		ConnectDB::subAutoExec( MyConfig::getValue("dbPrefix")."newsletter_emails ", array("blokada" => 1), "UPDATE", "id = ".$id);
		return true;
		
	}
	
	public function unblockEmail($id) {
		
		ConnectDB::subAutoExec( MyConfig::getValue("dbPrefix")."newsletter_emails ", array("blokada" => 0), "UPDATE", "id = ".$id);
		return true;
	}
	
}

?>
