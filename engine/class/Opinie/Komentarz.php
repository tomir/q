<?php

namespace Opinie;

class Komentarz extends \Enp\Db\Model {
	
	protected $_filtry = array(

		'has_odpowiedzi' => ' x.id IN (SELECT id_producent FROM produkty) '

	);
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('opinie_komentarze');
	}
	
	public function getOdpowiedzi($pytania) {
		
		$new = array();
		foreach($pytania as $row) {
			$new[$row['id']]['pytanie'] = $row;
			$new[$row['id']]['odpowiedz'] = $this->getAll(array('id_parent' => $row['id'], 'id_opinia' => $row['id_opinia']));
		}
		
		return $new;
	}
	 
}

?>
