<?php

namespace Enp\Validate;

class Telefon extends \Zend_Validate_Abstract {

	const NOT_VALID = 'not_valid';

	protected $_messageTemplates = array(
		self::NOT_VALID => "'%value%' nie jest poprawnym numerem telefonu"
	);

	public function isValid($value) {
		$this->_setValue($value);

		if (!$this->CheckTelefon($value)) {
			$this->_error(self::NOT_VALID);
			return false;
		}

		return true;
	}

	protected function CheckTelefon($str) {
		
		$str = preg_replace("/[^0-9]+/", "", $str); // usuniecie znakow oddzielajacych cyfry
		
		if (preg_match('/^[0-9]{9}$/', $str)) { // czy numer ma 9 cyfr (standardowy) 
			return true;
		}
		if (preg_match('/^[0-9]{11}$/', $str)) { // czy numer ma 11 cyfr (kierunkowy panstawa + standardowy) 
			return true;
		}

		return false;
	}

}
