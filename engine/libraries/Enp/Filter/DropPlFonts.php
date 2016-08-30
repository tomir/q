<?php

namespace Enp\Filter;

class DropPlFonts implements \Zend_Filter_Interface {

    public function filter($value) {
        $value = strtr(
                $value, array(
            "Ą" => "a",
            "Ć" => "c",
            "Ę" => "e",
            "Ł" => "l",
            "Ń" => "n",
            "Ó" => "o",
            "Ś" => "s",
            "Ż" => "z",
            "Ź" => "z",
            "ą" => "a",
            "ć" => "c",
            "ę" => "e",
            "ł" => "l",
            "ń" => "n",
            "ó" => "o",
            "ś" => "s",
            "ż" => "z",
            "ź" => "z",
            " " => "-",
                )
        );
        return $value;
    }
}
