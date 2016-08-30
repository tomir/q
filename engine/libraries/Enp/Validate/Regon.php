<?php

namespace Enp\Validate;

class Regon extends \Zend_Validate_Abstract {

	const NOT_VALID = 'not_valid';

	protected $_messageTemplates = array(
		self::NOT_VALID => "'%value%' nie jest poprawnym numerem REGON, który powinien sie składac z 9 cyfr"
	);

	public function isValid($value) {
		$this->_setValue($value);

		if (!$this->checkREGON($value)) {
			$this->_error(self::NOT_VALID);
			return false;
		}

		return true;
	}

	/**
	 * Metoda wzieta z serwisu phpedia.pl/wiki/
	 * 
	 * @link http://phpedia.pl/wiki/REGON
	 * @param type $str
	 * @return boolean 
	 */
	protected function checkREGON($str) {
		if (strlen($str) != 9) {
			return false;
		}

		$arrSteps = array(8, 9, 2, 3, 4, 5, 6, 7);
		$intSum = 0;
		for ($i = 0; $i < 8; $i++) {
			$intSum += $arrSteps[$i] * $str[$i];
		}
		$int = $intSum % 11;
		$intControlNr = ($int == 10) ? 0 : $int;
		if ($intControlNr == $str[8]) {
			return true;
		}
		return false;
	}

}