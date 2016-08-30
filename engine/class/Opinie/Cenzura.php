<?php

namespace Opinie;

class Cenzura extends \Enp\Db\Model {
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('opinie_zabronione_slowa');
	}
	
}
