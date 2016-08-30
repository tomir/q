<?php

namespace Opinie;

class Dzialy extends \Enp\Db\Model {
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('opinie_parametry_dzialy');
	}
	
}
