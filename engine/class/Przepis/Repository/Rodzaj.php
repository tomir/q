<?php

namespace Przepis\Repository;

/**
 * Description of Rodzaj
 *
 * @author tomi
 */
class Rodzaj extends \Enp\Db\Model {
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('przepis_rodzaj');
	}
}
