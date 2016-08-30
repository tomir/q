<?php
/**
 * Created by PhpStorm.
 * User: Tomek
 * Date: 30.08.16
 * Time: 15:29
 */

namespace App\Controller;


class Controller {

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }


    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

} 