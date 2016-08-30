<?php
namespace Enp\Filter;

class Kwota implements \Zend_Filter_Interface {

    public function filter($value) {
        if (preg_match("/^([0-9]+,[0-9]+)$/", $value)) {
            $value = str_replace(",", ".", $value);
        }
        return $value;
    }

}

?>
