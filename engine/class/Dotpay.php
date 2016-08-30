<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classDotpay
 *
 * @author t.jurdzinski
 */
class Dotpay {
    public function addDotpayInfo($id,$status, $control,$t_id,$amount,$email,$t_status,$desc,$md5,$info,$email2,$t_date,$channel) {
		$sql = "INSERT INTO shop_dotpay (id,`status`,`control`,`t_id` ,`amount` ,`email` ,
			`t_status` ,`description` ,`md5` ,`p_info` ,`p_email` ,`t_date` ,`channel`)
			VALUES (".$id.", '".$status."', '".$control."', '".$t_id."', '".$amount."', '".$email."',
				'".$t_status."', '".$desc."', '".$md5."', '".$info."', '".$email2."', '".$t_date."', '".$channel."');";
		//echo $sql;
		try {
			ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return true;
	}

	public function saveDotpayStatus($control, $status) {
		switch($status) {
			case 1: $status_txt = 'NOWA';
			break;
			case 2: $status_txt = 'WYKONANA';
			break;
			case 3: $status_txt = 'ODMOWA';
			break;
			case 4: $status_txt = 'ANULOWANA';
			break;
			case 5: $status_txt = 'REKLAMACJA';
			break;
		}
		if($status == 1) {
				$more_update = ", status_id = 3 ";
			}
		$sql = "UPDATE shop_order SET o_payment_status = '".$status_txt."', ".$more_update." WHERE control = '".$control."'";
		try {
			ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return true;
	}
}
?>
