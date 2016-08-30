<?php

@include_once LIBRARY_DIR . 'adodb/drivers/adodb-mysqli.inc.php';

class Adodb extends ADODB_mysqli {

	protected $_my_selectHost = '';
	protected $_my_selectHost_port = '';
	protected $_my_host;
	protected $_my_user;
	protected $_my_pass;
	protected $_my_db_name;
	protected $_my_connection_select = null;
	protected $_is_trans = false;
	protected $_is_trans_2 = false;

	public function __construct($host, $user, $pass, $dbName, $selectHost = false) {
		parent::ADODB_mysqli();

		$errorfn = (defined('ADODB_ERROR_HANDLER')) ? ADODB_ERROR_HANDLER : false;
		if ($errorfn) {
			$this->raiseErrorFn = $errorfn;
		}

		// rozbicie na port i host
		$tab = explode(':', $selectHost);
		$selectHost = $tab[0];
		$port = $tab[1];

		$this->_my_selectHost = $selectHost;
		$this->_my_selectHost_port = $port;
		$this->_my_host = $host;
		$this->_my_user = $user;
		$this->_my_db_name = $dbName;
		$this->_my_pass = $pass;

		$this->connectDb();
		if ($selectHost !== false) {
			$this->_my_connection_select = $this->klasterSelectDbConstruct();
		}
	}

	protected function connectDb() {
		$this->Connect($this->_my_host, $this->_my_user, $this->_my_pass, $this->_my_db_name);

		$this->SetFetchMode(ADODB_FETCH_ASSOC);
		$this->Execute("SET NAMES 'utf8'");
	}

	protected function klasterSelectDbConstruct() {
		$dbSelect = new ADODB_mysqli();

		$errorfn = (defined('ADODB_ERROR_HANDLER')) ? ADODB_ERROR_HANDLER : false;
		if ($errorfn) {
			$dbSelect->raiseErrorFn = $errorfn;
		}

		$dbSelect->port = $this->_my_selectHost_port;
		$dbSelect->Connect($this->_my_selectHost, $this->_my_user, $this->_my_pass, $this->_my_db_name);

		$dbSelect->SetFetchMode(ADODB_FETCH_ASSOC);
		$dbSelect->Execute("SET NAMES 'utf8'");
		$dbSelect->cacheSecs = 300;

		//$debugDb = Debugger_DebugDb::getInstance($dbSelect);

		return $dbSelect;
	}
	
	/**
	 * @return \ADODB_mysqli
	 */
	public function getConnectionSelect() { 
		return $this->_my_connection_select;
	}

	public function GetAll($sql, $inputarr = false) {
		if (!$this->_is_trans)
			return $this->_my_connection_select->GetAll($sql, $inputarr);
		else
			return parent::GetAll($sql, $inputarr);
	}

	public function GetCol($sql, $inputarr = false, $trim = false) {
		if (!$this->_is_trans)
			return $this->_my_connection_select->GetCol($sql, $inputarr, $trim);
		else
			return parent::GetCol($sql, $inputarr, $trim);
	}

	public function GetRow($sql, $inputarr = false) {
		if (!$this->_is_trans)
			return $this->_my_connection_select->GetRow($sql, $inputarr);
		else
			return parent::GetRow($sql, $inputarr);
	}

	public function GetOne($sql, $inputarr = false) {
		if (!$this->_is_trans)
			return $this->_my_connection_select->GetOne($sql, $inputarr);
		else
			return parent::GetOne($sql, $inputarr);
	}

	public function Execute($sql, $inputarr = false) {

		$sqlCopy = strtolower($sql);
		$sqlCopy = str_replace('(', '', $sqlCopy);

		//echo $sqlCopy.'<br />';
		
		// czy polecenie zaczyna sie od slowa select
		if (preg_match('/^select/', $sqlCopy) > 0 && !$this->_is_trans && !$this->_is_trans_2) {
			$this->_my_connection_select->debug = $this->debug;
			return $this->_my_connection_select->Execute($sql, $inputarr);
		} else {
			return parent::Execute($sql, $inputarr);
		}
	}

	public function BeginTrans() {
		$this->_is_trans = true;
		parent::BeginTrans();
	}

	public function CommitTrans($ok = true) {
		$res = parent::CommitTrans($ok);
		$this->_is_trans = false;

//		echo '<pre>';
//		echo "TUTAJ na false";
//		debug_print_backtrace();
//		echo '</pre>';

		return $res;
	}

	public function RollbackTrans() {
		parent::RollbackTrans();

//		echo '<pre>';
//		echo "TUTAJ na rolback";
//		debug_print_backtrace();
//		echo '</pre>';

		$this->_is_trans = false;
	}

	public function Insert_ID($table = '', $column = '') {

		$this->_is_trans_2 = true;
		$res = parent::GetOne('SELECT LAST_INSERT_ID()');
		$this->_is_trans_2 = false;

		return $res;
	}

	public function isTransOn() {
		return $this->_is_trans;
	}

}

?>
