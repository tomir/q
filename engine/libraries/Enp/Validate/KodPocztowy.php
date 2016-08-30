<?php

namespace Enp\Validate;

class KodPocztowy extends \Zend_Validate_Abstract {

	const NOT_VALID = 'not_valid';

	protected $_messageTemplates = array(
		self::NOT_VALID => "'%value%' nie jest poprawnym kodem pocztowym, który powinien się składac z 5 cyfr oddzielonych myślnikiem (XX-XXX)"
	);

	public function isValid($value) {
		$this->_setValue($value);

		if (!$this->CheckKodPocztowy($value)) {
			$this->_error(self::NOT_VALID);
			return false;
		}

		return true;
	}

	protected function CheckKodPocztowy($str) {
		if (!preg_match('/^[0-9]{2}-[0-9]{3}$/', $str)) {
			return false;
		}
		return true;
	}

}

?>
