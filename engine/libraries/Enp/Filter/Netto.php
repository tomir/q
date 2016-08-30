<?php

namespace Enp\Filter;

class Netto implements \Zend_Filter_Interface {

    public function filter($value) {
        
        $vat = 23;
        $vatMnoznik = (100 + $vat ) / 100;
        $netto = $value / $vatMnoznik;
        return round($netto,2);
    }
}
