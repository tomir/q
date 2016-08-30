<?php

/**
 * Do pobierania kosztow
 *
 * @package Allegro_Koszt
 */
class Allegro_Koszt {

	/**
	 * Opcje wystawienia przedmiotu
	 * 1 - pogrubienie aukcji 2 PLN
	 * 2 - miniaturka 0.15 PLN
	 * 4 - podswietlenie aukcji 6 PLN
	 * 8 - wyroznienie aukcji 19 PLN
	 * 16 - promowane w kategorii 18 PLN
	 * 32 - strona glowna allegro 149 PLN
	 * 64 - znak wodny  - NIE wiadomo(mozliwe ze aktywne tylko na testowym webapi)
	 */
	private $optionsCost = array(
		1 => 2, 
		2 => 0,
		4 => 6, 
		8 => 19,
		16 => 18,
		32 => 149,
		64 => 0
	);
	
	/**
	 * Koszt czasu wystawienia aukcji
	 * 0 - 3 dni
	 * 1 - 5 dni
	 * 2 - 7 dni
	 * 3 - 10 dni
	 * 4 - 14 dni
	 * 5 - 30 dni (nie wiadomo jaka cena)
	 * @var array 
	 */
	private $lengthCost = array(
		0 => 0, 
		1 => 0,
		2 => 0, 
		3 => 0, 
		4 => 0.25,
		5 => 0,
	);
	
	/**
	 * Tabela kosztow wystawienia przedmiotu w zaleznosci od ceny poczatkowej
	 * (klucz = do czyli jesli cena < 10 to oplata wynosi 0.15)
	 * dla konta sklep
	 * @var array
	 */
	private $itemCost = array(
		25 => 0.05, 
		250 => 0.10, 
		9999999 => 0.20
	);
	
	/**
	 * Oblicza koszt aukcji
	 * @param array $data dane w postaci array('idPolaFid'=>'wartosc')
	 * @return type 
	 */
	public function getKoszt($data) {
		
		$koszt = 0;
		//przegladamy wszystkie pola
		foreach($data as $fid=>$wartosci) {
			
			$wartosci = (array)$wartosci;
			foreach($wartosci as $wartosc) {
				
				switch ($fid) {
					case 15: //opcje
						$koszt+= $this->optionsCost[$wartosc];
						break;
					case 4: //czas trwania aukcji
						$koszt+= $this->lengthCost[$wartosc];
						break;
					case 8: //cena kup teraz
						foreach ($this->itemCost as $k => $v) {
							if ($wartosc < $k) {
								$koszt+= $v;
								break;
							}
						}
						break;
					default:
						break;
				}	
				
			}
		}
		return $koszt;
	}
	
	/**
	 * Oblicza prowizje od wartosci produktu/kup teraz
	 * @param float $wartosc 
	 */
	public function getProwizja($wartosc){
		
		$prowizja = 0;
		if ($wartosc <= 100) {
			$prowizja = $wartosc * 0.05;
		} elseif ($wartosc >= 100.01 && $wartosc <= 1000) {
			$prowizja = 5 + ($wartosc - 100) * 0.03;
		} elseif ($wartosc >= 1000.01 && $wartosc <= 5000) {
			$prowizja = 32 + ($wartosc - 1000) * 0.015;
		} elseif ($wartosc > 5000) {
			$prowizja = 92 + ($wartosc - 5000) * 0.05;
		}

		return $prowizja;
	}
}

?>