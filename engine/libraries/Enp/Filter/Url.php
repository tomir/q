<?php

namespace Enp\Filter;

class Url implements \Zend_Filter_Interface {

    public function filter($value) {

        $value = \Enp\Filter::filterStatic($value, 'StripTags');
        $value = \Enp\Filter::filterStatic($value, 'DropPlFonts');
        $value = preg_replace("/[^a-zA-Z0-9_-]/", '', $value);
        $value = \Enp\Filter::filterStatic($value, 'StringToLower');		
        return $value;
    }
}
