<?php

namespace Enp\Validate;

/**
 * Walidacja przedzialow liczbowych
 *
 * @example
 *  	$digitsIntervalValidator = new \Enp\Validate\DigitsInterval(array(
 *			'compareTo'				=> 'od',
 * 			'compareToLabel'		=> 'Wartość OD',
 * 			'compareType'			=> \Enp\Validate\DigitsInterval::LESS_THAN,
 *			'failIfCompareToEmpty'	=> true
 *		));
 *
 * @TODO stworzyc abstract dla DigitsInterval i DateTimeCompare, byc moze tez innych...
 * @author Artur Świerc
 */
class DigitsInterval extends \Zend_Validate_Abstract
{
	/**
	 * Element do ktorego porownujemy
	 * @var string
	 */
	private $_compareTo = null;

	/**
	 * Opis elementu do ktorego porownujemy.
	 * Opcjonalne. Tylko do formatu bledow.
	 */
	protected $_compareToLabel = '';

	/**
	 * Wartosc nie moze byc mniejsza od wartosci do ktorej porownujemy
	 */
	const LESS_THAN		= 'lessThan';

	/**
	 * wartosc nie moze byc wieksza od wartosci do ktorej porownujemy
	 */
	const GREATER_THAN	= 'greaterThan';

	/**
	 * Lista dostepnych typow porownan.
	 * @var array
	 */
	private $_allowComparetypes = array(
		self::LESS_THAN, self::GREATER_THAN
	);

	/**
	 * Flaga oznaczajaca czy zwrocic blad jesli parametr do ktorego porownujemy jest pusty.
	 * @var boolean
	 */
	private $_failIfCompareToEmpty = true;

	/**
	 * Typ walidacji, porownywania wartosci
	 * @var string
	 */
	private $_compareType = self::LESS_THAN;

	const INVALID_COMPARE_EMPTY = 'compareEmpty';
	const INVALID_LESS			= 'invalidLess';
	const INVALID_GREATER		= 'invalidGreater';

	protected $_messageTemplates = array(
		self::INVALID_COMPARE_EMPTY => 'Nie uzupełniono pola "%compareToLabel%"',
		self::INVALID_LESS			=> 'Wartość nie może być mniejsza od "%compareToLabel%"',
		self::INVALID_GREATER		=> 'Wartość nie może być większa od "%compareToLabel%"'
	);

	protected $_messageVariables = array(
		'compareToLabel' => '_compareToLabel'
	);

	public function __construct($options = null) {

		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		}

		if (is_array($options)) {
			if (isset($options['compareTo'])) {
				$this->setCompareTo($options['compareTo']);
			}
			if (isset($options['compareToLabel'])) {
				$this->setCompareToLabel($options['compareToLabel']);
			}
			if (isset($options['compareType'])) {
				$this->setCompareType($options['compareType']);
			}
			if (isset($options['compareType'])) {
				$this->setCompareType($options['compareType']);
			}
			if (isset($options['failIfCompareToEmpty'])) {
				$this->setFailIfCompareToEmpty($options['failIfCompareToEmpty']);
			}
		}
	}

	/**
	 * @param Zend_Form_Element|string $element
	 * @return \Enp\Validate\DigitsInterval
	 * @throws \Enp\Exception
	 */
	public function setCompareTo($element) {

		if ($element instanceof \Zend_Form_Element) {
			$this->_compareTo = $element->getName();
		} elseif (is_string($element)) {
			$this->_compareTo = $element;
		} else {
			throw new \Enp\Exception(sprintf("Parametr musi byc instancja \Zend_Form_Element lub stringiem. Podano %s", gettype($element)));
		}
		return $this;
	}

	/**
	 * @param string $label
	 */
	public function setCompareToLabel($label) {
		$this->_compareToLabel = $label;
	}

	/**
	 * @param string $type
	 * @throws \Enp\Exception
	 */
	public function setCompareType($type) {

		if (!in_array($type, $this->_allowComparetypes)) {
			throw new \Enp\Exception(sprintf("Wybrano bledny typ porownania %s. Możliwe opcje: %s.", $type, implode(", ", $this->_allowComparetypes)));
		}
		$this->_compareType = $type;
	}

	/**
	 * @param boolean $boolean
	 */
	public function setFailIfCompareToEmpty($boolean) {
		$this->_failIfCompareToEmpty = (bool) $boolean;
	}

	public function isValid($value, $context = null) {

		if (null === $this->_compareTo) {
			throw new \Enp\Exception("Nie podano elementu do porownania");
		}

		$compareToValue	= $context[$this->_compareTo];

		if (empty($this->_compareToLabel)) {
			$this->_compareToLabel = $this->_compareTo;
		}

		if (empty($compareToValue) && $this->_failIfCompareToEmpty) {
			$this->_error(self::INVALID_COMPARE_EMPTY);
			return false;
		} elseif (empty($compareToValue) && !$this->_failIfCompareToEmpty) {
			return true;
		}

		$filterDigits 	= new \Zend_Filter_Digits();
		$compareToValue = $filterDigits->filter($compareToValue);
		$value 			= $filterDigits->filter($value);

		if ($this->_compareType == self::LESS_THAN) {
			if ($value < $compareToValue) {
				$this->_error(self::INVALID_LESS);
				return false;
			}
		}
		if ($this->_compareType == self::GREATER_THAN) {
			if ($value > $compareToValue) {
				$this->_error(self::INVALID_GREATER);
				return false;
			}
		}
		return true;
	}
}