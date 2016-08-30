<?php

namespace Enp\Validate;

class Nip extends \Zend_Validate_Abstract {

	const NOT_VALID = 'not_valid';

	protected $_messageTemplates = array(
		self::NOT_VALID => "'%value%' nie jest poprawnym numerem NIP"
	);

	public function isValid($value) {
		$this->_setValue($value);

		if (!$this->checkNIP($value)) {
			$this->_error(self::NOT_VALID);
			return false;
		}

		return true;
	}

	/**
	 * Metoda wzieta z serwisu phpedia.pl/wiki/
	 * 
	 * @link http://phpedia.pl/wiki/NIP
	 * @param type $str
	 * @return boolean 
	 */
	protected function checkNIP($str) {
		$str = preg_replace("/[^0-9]+/", "", $str);
		if (strlen($str) != 10) {
			return false;
		}

		$arrSteps = array(6, 5, 7, 2, 3, 4, 5, 6, 7);
		$intSum = 0;
		for ($i = 0; $i < 9; $i++) {
			$intSum += $arrSteps[$i] * $str[$i];
		}
		$int = $intSum % 11;

		$intControlNr = ($int == 10) ? 0 : $int;
		if ($intControlNr == $str[9]) {
			return true;
		}
		return false;
	}

}

?>
