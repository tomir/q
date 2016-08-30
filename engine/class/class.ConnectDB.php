<?php

/*
ini_set("include_path", MyConfig::getValue("serverPatch").'engine/class/');
include_once("classLog.php");
include_once("classProduct.php");
include_once("classBasket.php");
include_once("classMisc.php");
include_once("classProfile.php");
include_once("classOrder.php");
include_once("classDotpay.php");
include_once("classMail.php");

//admin
include_once("admin/classAdminOrder.php");
include_once("admin/classAdminUsers.php");
include_once("admin/classAdminNews.php");
include_once("admin/classAdminAds.php");
include_once("admin/classAdminVat.php");
include_once("admin/classAdminProduct.php");
include_once("admin/classAdminProducents.php");
include_once("admin/classAdminCategory.php");
include_once("admin/classAdminDevilery.php");
*/

class ConnectDB 
{
	private static $db_name;
	private static $db_user;
 	private static $db_pass;
	private static $db_host;
	private static $db_port;

	private static $instance;
	
	static private function set_connection_vars($baza)
	{
		switch($baza) {
			case 1:
				self::$db_name = MyConfig::getValue("dbDatabase");
				self::$db_user = MyConfig::getValue("dbLogin");
				self::$db_pass = MyConfig::getValue("dbPass");
				self::$db_host = MyConfig::getValue("dbHost");
				self::$db_port = MyConfig::getValue("dbPort");
			break;

		}	
	}

	public function subQuery($sql, $fetch = "fetchAll", $kodowanie = "utf8") {

		if(MyConfig::getValue("memCache") == 1 && $memCache) {
			if($result = $memCache->get($cacheName))
				return $result;
		}

		$db = ConnectDB::singleton($kodowanie);
	
		switch($fetch) {
			case 'fetchAll':
				$aResult = $db -> getAll($sql);
			break;
		
			case 'fetch':
				$aResult = $db -> getRow($sql);
			break;
		
			case 'assoc':
				$aResult = $db -> getAssoc($sql);
			break;
		
			case 'one':
				$aResult = $db -> getOne($sql);
			break;
		
			case 'col':
				$aResult = $db -> getCol($sql);
			break;
		
			default:
				$aResult = $db -> getAll($sql);
			break;
		}

		if(!$aResult) $aResult = ' ';

		if(MyConfig::getValue("memCache") == 1 && $memCache) {
		 	$memCache->set($cacheName, $aResult, 0, MyConfig::getValue("cacheTime"));
		}
		return $aResult;

	}

	static public function subExec($sql, $kodowanie = 'utf8') {

		$db = ConnectDB::singleton($kodowanie);
		$result = $db -> Execute($sql);
		if($id = $db -> Insert_ID)
		    return $id;
		elseif(count($result) > 0) return true;
	}

	static public function subAutoExec($table, $records, $action, $where = null, $kodowanie = 'utf8') {

		$db = ConnectDB::singleton($kodowanie);
		if($where)
			$result = $db -> AutoExecute($table,$records,$action,$where);
		else
			$result = $db -> AutoExecute($table,$records,$action);
		if($id = $db -> Insert_ID())
		    return $id;
		elseif(count($result) > 0) return true;
	}

	static private function singleton($kodowanie)
	{
		if(self::$instance == null)
		{
			try {
				self::set_connection_vars(1);

				$db = ADONewConnection('mysql');
				$db->PConnect( self::$db_host, self::$db_user, self::$db_pass, self::$db_name);
				$db->SetFetchMode(ADODB_FETCH_ASSOC);
				//$db->debug = true;
				$db -> Execute("SET names ".$kodowanie);

				self::$instance = $db;

			}catch (Exception $e){
				echo "Wystąpł błąd połączenia z bazą danych (".print_r($e->gettrace()).")";
			}
		}
		return self::$instance;
	}

}


?>