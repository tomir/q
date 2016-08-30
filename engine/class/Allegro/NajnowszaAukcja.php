<?php

/**
 * Description of NajnowszaAukcja
 *
 * @author arturlasota
 */
class Allegro_NajnowszaAukcja {
	private $idProdukt = null;
	private $idAllegro = null;
	private $idSerwis = null;
	private $test = null;
	
	public function __construct() {
		
	}
	
	private function getIdProdukt() {
		if($this->idProdukt == null) {
			throw new InvalidArgumentException('Podaj id produktu');
		}
		return $this->idProdukt;
	}

	public function setIdProdukt($idProdukt) {
		$idProdukt = (int)$idProdukt;
		if($idProdukt <= 0) {
			throw new InvalidArgumentException('Podaj id produkt');
		}
		$this->idProdukt = $idProdukt;
	}

	private function getIdAllegro() {
		if($this->idProdukt == null) {
			throw new InvalidArgumentException('Podaj id produktu');
		}
		return $this->idAllegro;
	}

	public function setIdAllegro($idAllegro) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro <= 0) {
			throw new InvalidArgumentException('Podaj id allegro');
		}
		$this->idAllegro = $idAllegro;
	}

	private function getIdSerwis() {
		if($this->idProdukt == null) {
			throw new InvalidArgumentException('Podaj id produktu');
		}
		return $this->idSerwis;
	}

	public function setIdSerwis($idSerwis) {
		$idSerwis = (int)$idSerwis;
		if($idSerwis <= 0) {
			throw new InvalidArgumentException('Podaj id serwisu');
		}
		$this->idSerwis = $idSerwis;
	}
	
	public function add() {
		global $_gTables;
		
		$this->deleteOlderAuction();
		
		$data = array(
			'id_produkt' => $this->getIdProdukt(),
			'id_serwis' => $this->getIdSerwis(),
			'id_allegro' => $this->getIdAllegro()
		);
		Db::getInstance()->AutoExecute($_gTables['ALLEGRO_NAJNOWSZE_AUKCJE'], $data, 'INSERT');
	}
	
	private function deleteOlderAuction() {		
		global $_gTables;
		
		$sql = "DELETE FROM " . $_gTables['ALLEGRO_NAJNOWSZE_AUKCJE'] . " WHERE id_produkt=" . $this->getIdSerwis() . " AND id_serwis=" . $this->getIdSerwis();
		Db::getInstance()->Execute($sql);
	}

		
}

?>
