<?php

namespace Platnosci\PayByNet;
use Platnosci\PlatnosciInterface;
//use Platnosci\Log;

require_once(CLASS_DIR. 'Platnosci/paybynetapi.php');

class GeneratePayment implements PlatnosciInterface {

	protected $_idClient;
	protected $_idTrans;
	protected $_dateValid;
	protected $_amount;
	protected $_currency = "PLN";
	protected $_email;
	protected $_account;
	protected $_accname = array();
	protected $_backpage;
	protected $_backpageReject;
	protected $_password;
	public $_hashPayment;

	public function setIdClient( $val ) {
		$this->_idClient = $val;
	}
    public function setIdTrans( $val ) {
		$this->_idTrans = sprintf("%010d", $val);
	}
	public function setDateValid( $val ) {
		$this->_dateValid = time()+$val;
	}
	public function setAmount( $val ) {
		$this->_amount = str_replace( '.', ',', $val );
	}
	public function setEmail( $val ) {
		$this->_email = $val ;
	}
	public function setAccount( $val ) {
		$this->_account = $val ;
	}
	public function setAccname( $val ) {
		$this->_accname = $val[0] . '^NM^'.$val[1].'^ZP^'.$val[2].'^CI^'.$val[3].'^ST^'.$val[4].'^CT^';
	}
	public function setBackpage( $val ) {
		$this->_backpage = $val;
	}
	public function setBackpageReject( $val ) {
		$this->_backpageReject = $val;
	}
	public function setPassword( $val ) {
		$this->_password = $val;
	}
	
	private function preparePayment() {
		
		$this->setIdClient(PAY_NIP);
		$this->setDateValid(PAY_TIME);
		$this->setAccount(PAY_ACCOUNT);
		$this->setAccname(array(PAY_DATA_NM, PAY_DATA_ZP, PAY_DATA_CI, PAY_DATA_ST, PAY_DATA_CT));
		$this->setBackpage('http://'.DOMENA.'/koszyk-koniec.html?partner=paybynet');
		$this->setBackpageReject('http://'.DOMENA.'/koszyk-koniec.html?partner=paybynet');
		$this->setPassword(PAY_PASS_KEY);
	}
	
	public function generatePayment() {
		
		//uzupełnia parametry o pozostałe dane
		$this->preparePayment();
		
		$str1 =  '<id_client>'.$this->_idClient.'</id_client>';
		$str1 .= '<id_trans>'.$this->_idTrans.'</id_trans>';
		$str1 .= '<date_valid>'.date("d-m-Y H:i:s", $this->_dateValid).'</date_valid>';
		$str1 .= '<amount>'.$this->_amount.'</amount>';
		$str1 .= '<currency>'.$this->_currency.'</currency>';
		$str1 .= '<email>'.$this->_email.'</email>';
		$str1 .= '<account>'.$this->_account.'</account>';
		$str1 .= '<accname>'.$this->_accname.'</accname>';
		$str1 .= '<backpage>'.$this->_backpage.'</backpage>';
		$str1 .= '<backpagereject>'.$this->_backpageReject.'</backpagereject>';
		
		$str2 = $str1;
		$str2 .= '<password>'.$this->_password.'</password>';

		$encoded_sha1_str = sha1($str2);
		$str1 .= '<hash>'.$encoded_sha1_str.'</hash>';

		$encoded_base64_str = base64_encode($str1);
		$this->_hashPayment = $encoded_base64_str;
	}
}

?>