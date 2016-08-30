<?php

namespace Enp\Validate;

class Password extends \Zend_Validate_Abstract {

	const NOT_VALID = 'not_valid';

	protected $_messageTemplates = array(
		self::NOT_VALID => "Hasło musi składać sie z 8 znaków, w tym z przynajmniej jednego znaku innego niż litera"
	);

	public function isValid($value) {
		$this->_setValue($value);

		if (!$this->checkPassword($value)) {
			$this->_error(self::NOT_VALID);
			return false;
		}

		return true;
	}

	/**
	 * Walidacja hasła
	 * @param type $str
	 * @return boolean
	 */
	protected function checkPassword($str) {

		$str = trim($str);
		if (strlen($str) < 8) {
			return false;
		}

		if (preg_match('/[^a-zA-Z]/',$str) == 0) {
			return false;
		}

		return true;
	}

}