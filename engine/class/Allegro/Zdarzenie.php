<?php

class Allegro_Zdarzenie {
	
	public function save(array $aData) {

		ConnectDB::subAutoExec ("allegro_zdarzenia", $aData, "INSERT");
	}
	
	public function getIdNajnowszeZdarzenie($id_konto) {
		
		$sql = "SELECT max(id_zdarzenie) as id_zdarzenie FROM allegro_zdarzenia WHERE id_konto = ".$id_konto;
		$res = ConnectDB::subQuery($sql, "fetch");
		return $res['id_zdarzenie'];
	}
}
