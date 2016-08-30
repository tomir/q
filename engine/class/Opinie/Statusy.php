<?php

namespace Opinie;

class Statusy extends \Enp\Db\Model {
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('opinie_statusy');
	}
	
}

?>
