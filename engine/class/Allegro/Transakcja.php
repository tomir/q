<?php

class Allegro_Transakcja {
	
	public function save(array $aData) {
		
		ConnectDB::subAutoExec ("allegro_zdarzenia_transakcje", $aData, "INSERT");
	}
	
	public function getIdNajnowszeZdarzenie($id_konto, $typ) {
		
		$sql = "SELECT max(id_zdarzenie) as id_zdarzenie FROM allegro_zdarzenia_transakcje WHERE id_konto = ".$id_konto." AND typ = ".$typ;
		$res = ConnectDB::subQuery($sql, "fetch");
		return $res['id_zdarzenie'];
	}
}
?>
