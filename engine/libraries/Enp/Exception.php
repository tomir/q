<?php

namespace Enp;

class Exception extends \Exception
{
    protected $title = 'Exception';
	protected $code = 500;
	
	public function getTitle() {
		return $this->title;
	}
}

