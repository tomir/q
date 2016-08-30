<?php

namespace Db\Adapter;


class Mysqli extends \Zend_Db_Adapter_Mysqli
{

	/**
	 * Overwrite default method _sleep
         * We have problems with this method
	 */
	public function __sleep()
	{
		//return parent::__sleep();
	}
}
