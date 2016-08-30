<?php
namespace Enp\Filter;

class Regon implements \Zend_Filter_Interface {

    public function filter($value) {
        // usuniecie wszystkiego poza cyframi
		$value = preg_replace("/[^0-9]+/", "", $value);
		
		return $value;
    }

}

?>
