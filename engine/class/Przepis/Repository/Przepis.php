<?php

namespace Przepis\Repository;


/**
 * Description of Przepis
 *
 * @author tomi
 */
class Przepis extends \Enp\Db\Model {
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('przepis');
	}

	public function getSelect($filtr = null) {
		
		$select = parent::getSelect($filtr);
		$select->joinLeft(array('r'=>'przepis_rodzaj'), 'x.przepis_rodzaj_id = r.id', array('r.name AS rodzaj', 'r.name_url AS rodzaj_url'));
		$select->joinLeft(array('cz'=>'przepis_czas'), 'x.przepis_czas_id = cz.id', array('cz.name AS czas'));
		$select->joinLeft(array('d'=>'przepis_dificult'), 'x.przepis_dificult_id = d.id', array('d.name AS poziom_trudnosci'));
		$select->joinLeft(array('p'=>'shop_product'), 'x.product_id = p.p_id', array('p.p_name AS produkt_nazwa'));
		$select->joinLeft(array('u'=>'shop_users'), 'x.user_id = u.user_id', array('u.user_first_name AS nick', 'u.user_email AS email'));
		
		$select->columns('x.id AS id');
		
		return $select;
	}
	
	public function processGetOneRecord($record) {
		
		$record['link'] = "przepis,".\Common::url($record['title']). ",id-".$record['id'];
		return $record;
	}
	
}
