<?php
namespace Enp\Filter;

class Nip implements \Zend_Filter_Interface {

    public function filter($value) {
        // usuniecie wszystkiego poza cyframi
		$value = preg_replace("/[^0-9]+/", "", $value);
		
		// sformatowanie nipu na ogolny zapis XXX-XXX-XX-XX
		$value = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/i', '$1-$2-$3-$4', $value);
        return $value;
    }

}

?>
