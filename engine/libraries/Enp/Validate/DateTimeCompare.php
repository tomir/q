<?php

namespace Enp\Validate; 

/** 
 * Porownywanie dat, dwoch osobnych pol. 
 * 
 * @example 
 * 
 *	 $this->addElement('time', 'data_do', array(
 *		'label'	=> 'Aktywna Do', 
 *		'validators' => array(new DateTimeCompare(array(
 *			'compareTo'				=> 'data_od',
 *			'compareType'			=> DateTimeCompare::EARLIER, 
 *			'compareToLabel'		=> 'Aktywna Od', 
 *			'failIfCompareToEmpty'	=> false
 *		)))
 *	));
 * 
 * @author Artur Świerc 
 */
class DateTimeCompare extends \Zend_Validate_Abstract
{
	/** 
	 * Element do ktorego porownujemy date
	 */
	private $_compareTo = null;
	
	/** 
	 * Opis elementu do ktorego porownujemy date. 
	 * Opcjonalne. Tylko do formatu bledow.
	 */
	protected $_compareToLabel = '';
	
	/** 
	 * Data nie moze byc pozniejsza od daty do ktorej porownujemy 
	 */
	const LATER		= 'later'; 
	
	/** 
	 * Data nie moze byc wczesniejsza od daty do ktorej porownujemy 
	 */
	const EARLIER	= 'earlier'; 
	
	/** 
	 * Lista dostepnych typow porownan.
	 * @var array 
	 */
	private $_allowComparetypes = array(
		self::LATER, self::EARLIER
	);
	
	/** 
	 * Typ walidacji, porownywania dat. 
	 * @var string 
	 */
	private $_compareType = self::EARLIER;
	
	/** 
	 * Flaga oznaczajaca czy zwrocic blad jesli parametr do ktorego porownujemy jest pusty. 
	 * @var boolean
	 */
	private $_failIfCompareToEmpty = true;
	
	const INVALID_COMPARE_EMPTY = 'compareEmpty';
	const INVALID_LATER			= 'invalidLater';
	const INVALID_EARLIER		= 'invalidEarlier';
	
	protected $_messageTemplates = array(
		self::INVALID_COMPARE_EMPTY => 'Nie uzupełniono pola "%compareToLabel%"', 
		self::INVALID_EARLIER		=> 'Data nie może być wcześniejsza od "%compareToLabel%"', 
		self::INVALID_LATER			=> 'Data nie może być późniejsza od "%compareToLabel%"'
	);
	
	protected $_messageVariables = array(
		'compareToLabel' => '_compareToLabel'
	);
	
	/** 
	 * Opcje: compareTo, compareType, failIfCompareToEmpty
	 * @param Zend_Config|array|null $options 
	 */
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
			if (isset($options['failIfCompareToEmpty'])) { 
				$this->setFailIfCompareToEmpty($options['failIfCompareToEmpty']);
			}
		}
	}
	
	/**
	 * @param Zend_Form_Element|string $element
	 * @return \Enp\Validate\DateTimeCompare
	 * @throws \Enp\Exception\ApplicationError 
	 */
	public function setCompareTo($element) { 
		
		if ($element instanceof \Zend_Form_Element) { 
			$this->_compareTo = $element->getName();
		} elseif (is_string($element)) { 
			$this->_compareTo = $element;
		} else { 
			throw new \Enp\Exception\ApplicationError(sprintf("Parametr musi byc instancja \Zend_Form_Element lub stringiem. Podano %s", gettype($element)));
		}
		return $this;
	}
	
	/**
	 * @param string $type
	 * @throws \Enp\Exception\ApplicationError 
	 */
	public function setCompareType($type) {
		
		if (!in_array($type, $this->_allowComparetypes)) { 
			throw new \Enp\Exception\ApplicationError(sprintf("Wybrano bledny typ porownania %s. Możliwe opcje: %s.", $type, implode(", ", $this->_allowComparetypes)));
		}
		$this->_compareType = $type;
	}
	
	/**
	 * @param boolean $boolean 
	 */
	public function setFailIfCompareToEmpty($boolean) { 
		$this->_failIfCompareToEmpty = (bool) $boolean;
	}
	
	/**
	 * @param string $label
	 */
	public function setCompareToLabel($label) { 
		$this->_compareToLabel = $label;
	}
	
	public function isValid($value, $context = null) { 
		
		if (null === $this->_compareTo) { 
			throw new \Enp\Exception\ApplicationError("Nie podano elementu do porownania");
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

		if ($this->_compareType == self::EARLIER) { 
			if (strtotime($value) <  strtotime($compareToValue)) { 
				$this->_error(self::INVALID_EARLIER);
				return false;
			}
		}
		if ($this->_compareType == self::LATER) { 
			if (strtotime($value) >  strtotime($compareToValue)) { 
				$this->_error(self::INVALID_LATER);
				return false;
			}
		}
		return true;
	}
}
