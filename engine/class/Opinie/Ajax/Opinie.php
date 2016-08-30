<?php

namespace Opinie\Ajax;

/**
 * @AŚ 
 * @todo wyrzucic global, na rzecz Zend_Db -> $this->dbTable->update('', $where)
 * @todo wyrzucic catch z metod
 * @todo zmienic metody statyczne zwykla instancje  
 * @todo mozna zrobic throw \InvalidArgumentException  jesli ktos poda inny parametr niz int
 * Przykład metod z update wraz z expresion: glos = glos +1 w \Promocja\Adrotator.php
 */
class Opinie extends \Enp\Db\Model {
	
	public function getDbTableObject() {
		return \Enp\Db\TableFactory::get('opinie');
	}
	
	public function glosujNaOpinie($column, $id_opinia) {
		
		$db = $this->dbTable->getAdapter();

		if ($id_opinia > 0) { 
	
			$dataUpdate = array(
				$column => new \Zend_Db_Expr($column.' + 1'), 
			);
			$where = array(
				'id = ?'  => $id_opinia
			);
			$db->update('opinie', $dataUpdate, $where);
		}
	}
	
	public function glosujHtml($column = 'glos_tak', $id = 0) {
		
		$objResponse = new \xajaxResponse();

		if( $_COOKIE['opinie_glosowane_'.$id] > 0 ) {
			$objResponse->assign('opKomunikat_'.$id, 'innerHTML', '<span style="color: red; font-weight: bold;">Już wcześniej oddałeś głos na tą opinię.</span>');
			return $objResponse;
		}
		if( (int)$id == 0 )
			return $objResponse;

		$this->glosujNaOpinie($column, $id);

		$o = new \Opinie\Lista($id);
		$objResponse->assign('opTak_'.$id, 'innerHTML', $o->data['glos_tak'] );
		$objResponse->assign('opNie_'.$id, 'innerHTML', $o->data['glos_nie'] );
		$objResponse->assign('opProcent_'.$id, 'innerHTML', $o->data['ocena_opinii'] );
		
		$objResponse->assign('opKomunikat_'.$id, 'innerHTML', '<span style="color: green; font-weight: bold;">Dziękujemy za oddanie głosu.</span>');

		setcookie("opinie_glosowane_".$id, $id, time()+3600*24*7);

		return $objResponse;
	}
	
}
