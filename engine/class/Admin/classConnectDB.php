<?php

error_reporting(E_ERROR);
ini_set('display_errors', 1);
include_once("classConfig.php");

ini_set("include_path", MyConfig::getValue("serverPatch").'engine/');
ini_set("include_path", ini_get('include_path').PATH_SEPARATOR.MyConfig::getValue("serverPatch").'engine/class/');
ini_set("include_path", ini_get('include_path').PATH_SEPARATOR.MyConfig::getValue("serverPatch").'engine/libraries/');

define('APPLICATION_DIR',MyConfig::getValue("serverPatch"));

define('CLASS_DIR', APPLICATION_DIR.'engine/class/');
define('LIBRARY_DIR', APPLICATION_DIR.'engine/libraries/');


require_once('OPD/opd.class.php');
include_once("Admin/classTemplate.php");
//include_once("admin/classSmartyLoad.php");
include_once("classLog.php");

include_once("Admin/classKomunikaty.php");
include_once("Admin/classProfile.php");
include_once("Admin/Otomoto.php");
include_once("Admin/Newsletter.php");
include_once("Admin/Car.php");
include_once("Admin/Common.php");
include_once("class.Klient.php");
include_once("class.Mail.php");

include_once("Otomoto/Observer.php");
include_once("Otomoto/Api.php");
include_once("Log/Logger.php");
//include_once("Common.php");

include_once("Admin/classAdminMain.php");
include_once("Admin/classSiteGenerator.php");

include_once("Tff/classFormularz.php");
include_once("Tff/classPanel.php");
include_once("Tff/classPanelJS.php");

class ConnectDB extends opdClass
{
	private static $db_name;
	private static $db_user;
 	private static $db_pass;
	private static $db_host;
	private static $db_port;

	private static $instance2;
	
	public function __construct($baza = 1)
	{	

		try {
			$driverOpt = array(
					'options' => array(PDO::ATTR_PERSISTENT => false),
					'cache' => MyConfig::getValue("serverPatch")."_cache/",
					'debugConsole' => MyConfig::getValue("dbDebug") );
			$this -> set_connection_vars($baza);
			@parent::__construct("mysql:host=".$this -> db_host.";port=".$this -> db_port.";dbname=".$this -> db_name, $this -> db_user, $this -> db_pass, $driverOpt);
										
			return $this -> dbHandle;
		}catch (PDOException $e){
			echo "Wystąpł błąd połączenia z bazą danych (".$e->getMessage().")";
		}
	} 

	private function set_connection_vars($baza)
	{
		switch($baza) {
			case 1:
				$this->db_name = MyConfig::getValue("dbDatabase");
				$this->db_user = MyConfig::getValue("dbLogin");
				$this->db_pass = MyConfig::getValue("dbPass");
				$this->db_host = MyConfig::getValue("dbHost");
				$this->db_port = MyConfig::getValue("dbPort");
			break;

		}	
	}
	
	static private function set_connection_vars2($baza)
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

	static public function subQuery($sql, $cacheName, $memCache, $fetch = "fetchAll", $kodowanie = "utf8") {
		
		if(MyConfig::getValue("memCache") == 1 && $memCache) {
			if($result = $memCache->get($cacheName))
				return $result;
		}

		$pdo = ConnectDB::singleton($kodowanie); 
		$result = $pdo -> query($sql);
		if($fetch == 'fetchAll')
			$aResult = $result -> fetchAll();
		else
			$aResult = $result -> fetch();

		if(!$aResult) $aResult = ' ';


		if(MyConfig::getValue("memCache") == 1 && $memCache) {
		 	$memCache->set($cacheName, $aResult, 0, MyConfig::getValue("cacheTime"));
		}
		return $aResult;

	}

	public function subExec($sql, $kodowanie = 'utf8') {

		$pdo = ConnectDB::singleton($kodowanie);
		$count = $pdo -> exec($sql);
		return $count;
	}

	private function singleton($kodowanie)
	{
		static $instance;
		if(!isset($instance))
		{
			$instance = new ConnectDB();
			$instance -> exec("SET names ".$kodowanie);
		}
		return $instance;
	}
	
	static public function subAutoExec($table, $records, $action, $where = null, $kodowanie = 'utf8') {

		$db = ConnectDB::singleton2($kodowanie);
		if($where)
			$result = $db -> AutoExecute($table,$records,$action,$where);
		else
			$result = $db -> AutoExecute($table,$records,$action);
		if($id = $db -> Insert_ID())
		    return $id;
		elseif(count($result) > 0) return true;
	}

	static private function singleton2($kodowanie)
	{
		
		require_once( 'adodb/adodb-exceptions.inc.php');
		require_once( 'adodb/adodb.inc.php' );

		if(self::$instance2 == null)
		{
			try {
				self::set_connection_vars2(1);

				$db = ADONewConnection('mysql');
				$db->PConnect( self::$db_host, self::$db_user, self::$db_pass, self::$db_name);
				$db->SetFetchMode(ADODB_FETCH_ASSOC);
				//$db->debug = true;
				$ADODB_QUOTE_FIELDNAMES = true;
				$db -> Execute("SET names ".$kodowanie);

				self::$instance2 = $db;

			}catch (Exception $e){
				echo "Wystąpł błąd połączenia z bazą danych (".print_r($e->gettrace()).")";
			}
		}
		return self::$instance2;
	}

}


?>