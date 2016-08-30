<?php
namespace Enp\Validate;

class Kwota extends \Zend_Validate_Abstract {
    const FLOAT = 'float';

    protected $_messageTemplates = array(
        self::FLOAT => "'%value%' is not a floating point value"
    );

    public function isValid($value) {
        $this->_setValue($value);

        if (!preg_match("/^[0-9]+((\.|,)[0-9]+)*$/", $value)) {
            $this->_error(self::FLOAT);
            return false;
        }

        return true;
    }

}

?>
