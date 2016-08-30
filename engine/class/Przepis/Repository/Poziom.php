<?php

namespace Przepis\Repository;

/**
 * Description of Czas
 *
 * @author tomi
 */
class Poziom extends \Enp\Db\Model {
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('przepis_dificult');
	}

}
