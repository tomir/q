<?php

die(__METHOD__.' :: prosze to zaimplementowac');

namespace Enp\Validate;

class NotIdentical extends \Zend_Validate_Identical {

    public function isValid($value) {
        return !parent::isValid($value);
    }

}

?>
