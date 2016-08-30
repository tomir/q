<?php

/**
 * Description of Dane
 *
 * @author ArturLasota
 */
class AllegroApi_Komentarz_Dane {
	
	public $idAukcji = null;
	public $idOdbiorcy = null;
	public $komentarz = null;
	public $ocena = null;
	public $typOdbiorcy = null;
	
	public function setIdAukcji($idAllegro) {
		$idAllegro = (int)$idAllegro;
		if($idAllegro == 0) {
			throw new InvalidArgumentException('Podaj numer aukcji');
		}
		
		$this->idAukcji = $idAllegro;
	}
	
	public function setIdOdbiorcy($idOdbiorcy) {
		$idOdbiorcy = (int)$idOdbiorcy;
		if($idOdbiorcy == 0) {
			throw new InvalidArgumentException('Podaj id odbiorcy');
		}
	
		$this->idOdbiorcy = $idOdbiorcy;
	}
	
	public function setTypOdbiorcy($idTypOdbiorcy) {
		$idTypOdbiorcy = (int)$idTypOdbiorcy;
		if($idTypOdbiorcy == 0) {
			throw new InvalidArgumentException('Podaj id typ odbiorcy');
		}
		
		$this->typOdbiorcy = $idTypOdbiorcy;
	}
	
	public function setKomentarz($komentarz) {
		$komentarz = trim($komentarz);
		if($komentarz == '') {
			throw new InvalidArgumentException('Podaj komentarz');
		}
		
		$this->komentarz = $komentarz;
	}
	
	public function setOcena($ocena) {
		$ocena = trim($ocena);
		if($ocena == '') {
			throw new InvalidArgumentException('Podaj ocenę');
		}
		
		$this->ocena = $ocena;
	}
}

?>
