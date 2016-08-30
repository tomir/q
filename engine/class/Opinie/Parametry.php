<?php

namespace Opinie;

class Parametry extends \Enp\Db\Model {
	
	 protected $_filtry = array(
		 
		'id_opinia' => ' x.id IN (SELECT id_parametr FROM opinie_parametry_produkty WHERE id_opinia = ?) '
	);
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('opinie_parametry');
	}
	
	protected function getSelect($filtr = null) {
		
		$select = parent::getSelect($filtr);
		$select->joinleft(array('od'=>'opinie_parametry_dzialy'), 'x.id_parametry_dzialy = od.id', array('od.nazwa AS dzial'));
		
		$select->columns('x.id AS id');
		
		return $select;
	}
	
	public function getGroupByDzial($filtr) {
		
		$aNew = array();
		
		$dzialyObj = $this->getInstanceOfClass('\Opinie\Dzialy');
		$aDzialy = $dzialyObj->getAllSelect();
		foreach($aDzialy as $key=>$dzial) {
			$aNew[$key]['dzial'] = $dzial;
			$aNew[$key]['dzial_id'] = $key;
		}
		
		$rows = $this->getAll($filtr); 
		foreach($rows as $row) {
			$aNew[$row['id_parametry_dzialy']]['parametry'][] = $row;
		}
		
		return $aNew;
	}
	
	public function addToProduct(array $aParametry) {
		
		$db = $this->dbTable->getAdapter();
		
		$dzialyObj = $this->getInstanceOfClass('\Opinie\Dzialy');
		$aDzialy = $dzialyObj->getAllSelect();
		
		foreach($aDzialy as $key_d=>$dzial) {
			foreach($aParametry['parametr'][$key_d] as $key=>$row) {

				if($row == 1) {
					$db->insert('opinie_parametry_produkty', array(
						'id_produkt'	=> $aParametry['id_produkt'], 
						'id_opinia'		=> $aParametry['id_opinia'],
						'id_parametr'	=> $key
					));
				}
			}
		}
	}
	
	public function addToProductAdmin(array $aParametry) {
		
		$db = $this->dbTable->getAdapter();

		$db->insert('opinie_parametry_produkty', array(
			'id_produkt'	=> $aParametry['id_produkt'], 
			'id_opinia'		=> $aParametry['id_opinia'],
			'id_parametr'	=> $aParametry['id_parametr']
		));

	}
	
}