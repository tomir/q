<?php

namespace Enp\Filter;

class Podatek implements \Zend_Filter_Interface {

    public function filter($value) {
		$nettoFilter = new Netto();
        $netto = $nettoFilter->filter($value);
        return round($value - $netto,2);
    }

}
