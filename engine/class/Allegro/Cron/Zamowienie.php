<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cron
 *
 * @author Artur Lasota
 */
class Allegro_Cron_Zamowienie {
	
	public $aukcja = array();
	public $oferta = array();
	public $uzytkownik = array();
	public $aukcjaInfo = array();
	public $iloscProduktow = 0;
	public $cena = 0;
	
	const ZAMOWIENIE_STATUS_ALLEGRO = 56;
	
	
	/** 
	 * Zapisuje zamowienie
	 * @return int 
	 */
	public function save() {
		
		if(count($this->aukcja) == 0) {
			throw new InvalidArgumentException('Podaj dane aukcji');
		}
		
		if($this->cena == 0) {
			throw new InvalidArgumentException('Podaj dane oferty');
		}
		
		if(count($this->uzytkownik) == 0) {
			throw new InvalidArgumentException('Podaj dane użytkownika');
		}

		if(count($this->aukcjaInfo) == 0) {
			throw new InvalidArgumentException('Podaj dane aukcji z allegro');
		}
		
		if($this->iloscProduktow <= 0) {
			throw new InvalidArgumentException('Podaj ilosc produktow');
		}

		
		$zamowienie = new Zamowienie();
		$zamowienie->enableSourceAllegro();
		$zamowienie->data = $this->getGeneralDataToSave();
		$zamowienie->data['platnik'] = $this->getUserToSave();
		$zamowienie->data['pozycje'][] = $this->getProductToSave();
		$zamowienie->zapiszZamowienie();
		$zamowienieAdmin = new ZamowienieAdmin($zamowienie->id);
		ZamowienieAdmin::set($zamowienie->id, 'wartosc_dostawy', $zamowienieAdmin->obliczWartoscDostawy($zamowienieAdmin->data['id_platnosc'], $zamowienieAdmin->data['id_transport']));
		return $zamowienie->id;
	}
	
	/**
	 * Dodaje dane glowne do zamowienia
	 * @return array 
	 */
	private function getGeneralDataToSave() {
		if(count($this->aukcja) == 0) {
			throw new InvalidArgumentException('Podaj dane aukcji');
		}
		
		if($this->cena == 0) {
			throw new InvalidArgumentException('Podaj dane oferty');
		}
		
		if(count($this->uzytkownik) == 0) {
			throw new InvalidArgumentException('Podaj dane użytkownika');
		}

		if(count($this->aukcjaInfo) == 0) {
			throw new InvalidArgumentException('Podaj dane aukcji z allegro');
		}
		
		if($this->iloscProduktow <= 0) {
			throw new InvalidArgumentException('Podaj ilosc produktow');
		}
		
		$transport = Transport::pobierzKosztProdukt($this->aukcja['id_produktu']);
		
		return array(
			'id_platnosc' => 1,
			'id_transport' => 13,
			'wartosc' => $this->iloscProduktow * $this->cena,
			'wartosc_dostawy' => $this->aukcjaInfo['it-wire-transfer'],
			'wartosc_dostawy_allegro' => $this->aukcjaInfo['it-wire-transfer'] . ';' . $this->aukcjaInfo['it-post-delivery'],
			'uwagi' => 'Login allegro: ' . $this->uzytkownik['user-login'],
			'allegro' => $this->uzytkownik['user-login'],
			'id_aukcji' => $this->aukcja["id_aukcji"],
			'referer_id' => 14,
			'konto_allegro' => $this->aukcja["konto_allegro"]
		);

	}
	
	/**
	 * Dodaje produkt do zamowienia
	 * @return array 
	 */
	private function getProductToSave() {
		if(count($this->aukcja) == 0) {
			throw new InvalidArgumentException('Podaj dane aukcji');
		}
		
		if($this->cena == 0) {
			throw new InvalidArgumentException('Podaj cene aukcji');
		}
		
		if($this->iloscProduktow <= 0) {
			throw new InvalidArgumentException('Podaj ilosc produktow');
		}
		
		return  array(
//			'obj' => new Produkt($this->aukcja['id_produktu']),
			'id' => $this->aukcja['id_produktu'],
			'ilosc' => $this->iloscProduktow,
			'cena' => $this->cena,
			'wartosc' => $this->iloscProduktow * $this->cena
		);
	}
	
	
	/**
	 * Dodaje uzytkownika do zamowienia
	 * @return array 
	 */
	private function getUserToSave() {
		
		if(count($this->aukcja) == 0) {
			throw new InvalidArgumentException('Podaj dane aukcji');
		}
		
		if(count($this->uzytkownik) == 0) {
			throw new InvalidArgumentException('Podaj dane użytkownika');
		}
		
		return array(
			'email' => $this->uzytkownik['user-email'],
			'imie' => $this->uzytkownik['user-first-name'],
			'nazwisko' => $this->uzytkownik['user-last-name'],
			'telefon' => $this->uzytkownik['user-phone'],
			'ulica' => $this->uzytkownik['user-address'],
			'miejscowosc' => $this->uzytkownik['user-city'],
			'kod' => $this->uzytkownik['user-postcode'],
			'firma' => $this->uzytkownik['user-company'],
			'id_serwisu' => $this->aukcja['id_serwisu'],
			'id_statusu' => self::ZAMOWIENIE_STATUS_ALLEGRO
		);
	}
	

	public function setAukcja(array $data) {
		if($data <= 0) {
			throw new InvalidArgumentException('Podaj dane');
		}
		$this->aukcja = $data;
	}
	public function setOferta(array $data) {
		if($data <= 0) {
			throw new InvalidArgumentException('Podaj dane');
		}
		$this->oferta = $data;
		$this->cena = $this->oferta[6];
	}
	public function setUzytkownik(array $data) {
		if($data <= 0) {
			throw new InvalidArgumentException('Podaj dane');
		}
		$this->uzytkownik = $data;
	}
	public function setAukcjaInfo(array $data) {
		if($data <= 0) {
			throw new InvalidArgumentException('Podaj dane');
		}
		$this->aukcjaInfo = $data;
	}
	public function setIloscProduktow(array $data) {
		if($data <= 0) {
			throw new InvalidArgumentException('Podaj ilość produktów');
		}
		$this->iloscProduktow = $data;
	}
	
	public function setCena($cena) {
		$cena = (float)$cena;
		if($cena <= 0) {
			throw new InvalidArgumentException('Podaj cenę');
		}
		
		$this->cena = $cena;
	}
	

}

?>
