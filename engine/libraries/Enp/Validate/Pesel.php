<?php

namespace Enp\Validate;

class Pesel extends \Zend_Validate_Abstract {

	const NOT_VALID = 'not_valid';

	protected $_messageTemplates = array(
		self::NOT_VALID => "'%value%' nie jest poprawnym numerem PESEL, który powinien się składać z 11 cyfr"
	);

	public function isValid($value) {
		$this->_setValue($value);

		if (!$this->checkPESEL($value)) {
			$this->_error(self::NOT_VALID);
			return false;
		}

		return true;
	}

	/**
	 * Metoda wzieta z serwisu phpedia.pl/wiki/
	 * 
	 * @link http://phpedia.pl/wiki/PESEL
	 * @param type $str
	 * @return boolean 
	 */
	protected function checkPESEL($str) {
		if (!preg_match('/^[0-9]{11}$/', $str)) { //sprawdzamy czy ciąg ma 11 cyfr
			return false;
		}

		$arrSteps = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3); // tablica z odpowiednimi wagami
		$intSum = 0;
		for ($i = 0; $i < 10; $i++) {
			$intSum += $arrSteps[$i] * $str[$i]; //mnożymy każdy ze znaków przez wagć i sumujemy wszystko
		}
		$int = 10 - $intSum % 10; //obliczamy sumć kontrolną
		$intControlNr = ($int == 10) ? 0 : $int;
		if ($intControlNr == $str[10]) { //sprawdzamy czy taka sama suma kontrolna jest w ciągu
			return true;
		}
		return false;
	}

}