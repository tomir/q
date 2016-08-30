<?php
namespace Enp\Validate;

class Time extends \Zend_Validate_Abstract
{
    const INVALID        = 'timeInvalid';
    const INVALID_TIME   = 'timeInvalidTime';
    const FALSEFORMAT    = 'timeFalseFormat';

    protected $_messageTemplates = array(
        self::INVALID        => "Invalid type given. String expected",
        self::INVALID_TIME   => "'%value%' does not appear to be a valid time",
        self::FALSEFORMAT    => "'%value%' does not fit the time format '%format%'",
    );

    protected $_format;

	protected $_messageVariables = array(
        'format'  => '_format'
    );

	
    public function isValid( $value )
    {
        if (!is_string( $value )) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue( $value );

		if (!preg_match('/^\d{2}:\d{2}$/', $value)) {
			$this->_format = 'HH:MM';
			$this->_error(self::FALSEFORMAT);
			$this->_format = null;
			return false;
		}

		list( $hour, $min ) = sscanf($value, '%d:%d');

		if (!$this->_checkTime($hour, $min)) {
			$this->_error(self::INVALID_TIME);
			return false;
		}

        return true;
    }

	private function _checkTime( $hour, $min ){
		if($hour<0 || $hour>23){
			return false;
		}
		if($min<0 || $min>60){
			return false;
		}
		return true;
	}

}
