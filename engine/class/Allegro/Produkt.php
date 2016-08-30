<?php

/**
 * Klasa pobiera dane o produkcie na potrzeby allegro
 *
 * @package Allegro_Produkt
 */
class Allegro_Produkt {

	public $id;
	public $data;
	function __construct($idProdukt=0) {
		$idProdukt = (int)$idProdukt;
		if($idProdukt > 0) {
			$produkt = new Produkt($idProdukt);
			$obAllegro = new Produkt_Allegro($idProdukt);
			$produkt->data['allegro_data'] = $obAllegro->data; 
			$this->data = $produkt->data;
			$this->id = $idProdukt;
		}
	}
	
//	private $dataAllegro = array();

	/**
	 * Pobiera zdjęcia BDK dla allegro
	 * @param type $idSerwis
	 * @return array 
	 */
	public function getImageBdk($idSerwis = 0) {
				
		$idSerwis = (int)$idSerwis;
		if($this->id <= 0) {
			throw new InvalidArgumentException('Podaj id produktu');
		}
		if($idSerwis <= 0) {
			throw new InvalidArgumentException('Podaj id serwisu');
		}
		
		$prefix = '';
		if($idSerwis > 1) {
			$prefix = $idSerwis;
		}

		$katalog_img = 'kupic_img' . $prefix . '/' . $this->data['zdjecie_katalog'] . '/';
		
		$zdjeciaBdk = Produkt::pobierzDodatkoweZdjecia($this->id, $idSerwis);
		
		$zdjecia = array();
		foreach($zdjeciaBdk as $val) {
			//tworzymy zdjecie dla allegro 
			
			$file = $katalog_img . '600x600_' . $val;
//			$zdjecieAllegro = APPLICATION_DIR . $file;
//			if(!file_exists($zdjecieAllegro) || (time() - filemtime($zdjecieAllegro)) > 86400) {
//				$zdjecieBdk = APPLICATION_DIR . $katalog_img . '600x600_' . $val;
//				$zdjecieAllegro = APPLICATION_DIR . $katalog_img . 'a_' . $val;
//				//$xt = new ZnakWodny($zdjecieBdk, APPLICATION_DIR.'common'.$prefix.'/allegro-img/watermark.gif');
//				//$xt->generuj('CB', $zdjecieAllegro);
//				
//				$im = imagecreatefromjpeg($zdjecieBdk);
//				$destinationSize  =  array(imagesx($im)+10, imagesy($im)+120);
//				$destination = imagecreatetruecolor($destinationSize[0],$destinationSize[1]);
//				$white = imagecolorallocate($destination, 242, 242, 242);
//				$border = imagecolorallocate($destination, 255, 255, 255);
//				$black = imagecolorallocate($destination, 30, 30, 30);
//				imagefill($destination, 0, 0, $white);
//				imagecopyresampled($destination, $im, 5 ,5 , 0, 0, imagesx($im),imagesy($im),imagesx($im),imagesy($im));
//				$layer = imagecreatefromgif(APPLICATION_DIR.'common'.$prefix.'/allegro-img/watermark.gif');
//				imagecopyresampled($destination, $layer, ($destinationSize[0]-imagesx($layer)-200)/2 ,$destinationSize[1]-imagesy($layer)-10 , 0, 0, imagesx($layer)+200,imagesy($layer),imagesx($layer),imagesy($layer));
//				imagejpeg($destination, $zdjecieAllegro, 100);
//				
////				copy($zdjecieBdk, $zdjecieAllegro);
//			}
			
			$zdjecia[] = $file;
		}
		
		
		return $zdjecia;
	}
	
	/**
	 * Pobiera zdjęcie produktu dla allergo
	 * @param type $idSerwis
	 * @return string 
	 */
	public function getImage($idSerwis = 0) {
		$idSerwis = (int)$idSerwis;
		if($this->id <= 0) {
			throw new InvalidArgumentException('Podaj id produktu');
		}
		if($idSerwis <= 0) {
			throw new InvalidArgumentException('Podaj id serwisu');
		}
		
		$prefix = '';
		if($idSerwis > 1) {
			$prefix = $idSerwis;
		}
		
		// zdjecie
		$katalog_img = 'gfx/';

		if ($this->data['kupic_zdjecie'] == '' || $this->data['kupic_zdjecie'] == 'nopic.jpg') {
			$zdj = '';
		} else {
			$file = $katalog_img . $this->data['kupic_zdjecie'];
			$zdjecie = str_replace('//', '/', APPLICATION_DIR) . $katalog_img . $this->data['kupic_zdjecie'];
			
			$zdjecieAllegro = str_replace('//', '/', APPLICATION_DIR) . $katalog_img.'a_'.$this->data['kupic_zdjecie'];
			if( Thumb::make( $zdjecie, $zdjecieAllegro, 640, 360, 'FFFFFF', 'jpg' ) )
				chmod($zdjecieAllegro, 0777);
			
			//d($zdjecie);
			//d($zdjecieAllegro);
			
			$zdjecieAllegro = APPLICATION_DIR . $katalog_img . 'a_' . $this->data['kupic_zdjecie'];
			if(!file_exists($zdjecieAllegro) || (time() - filemtime($zdjecieAllegro)) > 86400) {
				//$xt = new ZnakWodny($zdjecie, APPLICATION_DIR . 'common' . $prefix . '/allegro-img/watermark.gif');
				//$xt->generuj('CB', $zdjecieAllegro);
				
				$im = imagecreatefromjpeg($zdjecieAllegro);
				$destinationSize  =  array(imagesx($im), imagesy($im)+120);
				
				//$destinationSize[0] = 640;
				//$destinationSize[1] = 480;
				
				//if( $destinationSize[0] > $destinationSize[1] )
				//	$destinationSize[1] = $destinationSize[0];
				
				//if( $destinationSize[1] > $destinationSize[0] )
				//	$destinationSize[0] = $destinationSize[1];
				
				$destination = imagecreatetruecolor($destinationSize[0],$destinationSize[1]);
				$white = imagecolorallocate($destination, 255, 255, 255);
				//$white = imagecolorallocate($destination, 242, 242, 242);
				$border = imagecolorallocate($destination, 255, 255, 255);
				$black = imagecolorallocate($destination, 30, 30, 30);
				imagefill($destination, 0, 0, $white);
				imagecopyresampled($destination, $im, ($destinationSize[0]-imagesx($im))/2 ,5 , 0, 0, imagesx($im),imagesy($im),imagesx($im),imagesy($im));
				$layer = imagecreatefromgif(APPLICATION_DIR.'common_bd/allegro-img/watermark_new.gif');
				imagecopyresampled($destination, $layer, ($destinationSize[0]-imagesx($layer)-50)/2 ,$destinationSize[1]-imagesy($layer)-30 , 0, 0, imagesx($layer)+50,imagesy($layer)+30,imagesx($layer),imagesy($layer));
				imagejpeg($destination, $zdjecieAllegro, 100);
			}

			return $file;
		}
	}
	
	/**
	 * Pobiera parametry produktu
	 * @param type $idSerwis
	 * @return type 
	 */
	public function getParametry($idSerwis = 0) {
		$idSerwis = (int)$idSerwis;
		if($idSerwis <= 0) {
			throw new InvalidArgumentException('Podaj id serwisu');
		}
		

		if ((int) $this->data['kupic_id'] > 0) {
			$kupicObj = new KupicPlApi($idSerwis);
			$kupic = $kupicObj->getProdukt($this->data['kupic_id']);
		} else if ($produkt->data['action_id'] != '') {
			$parametry = Produkt::getParams($this->id);
			$kupic = array();
			foreach ($parametry as $i) {
				if (trim($i['wartosc']) != '')
					$kupic['attributes']['Ogólne'][] = array('atrybut' => $i['nazwa'], 'wartosc' => $i['wartosc']);
			}
		}
		
		return $kupic['attributes'];
	}
	
	/**
	 * Pobiera dane produktu do wystawenia na allegro
	 * @param type $idSerwis
	 * @return type 
	 */
	public function getDataAllegro($idSerwis = 0, $idKonto = 0) {
		$idSerwis = (int)$idSerwis;
		if($idSerwis <= 0) {
			throw new InvalidArgumentException('Podaj id serwisu');
		}
		
		if((int)$idKonto == 0) {
			$idKonto = Allegro_Konto::getIdKontaByIdSerwisu($idSerwis);
		}
		$web = new Website($idSerwis);
		$img_host = $web->data['img_host'];
		
		$photos = array();
		$zdj = $this->getImage($idSerwis);
		if(!empty($zdj)) {
			//$photos[] = 'http://' . $img_host . '/' . $zdj;
			$photos[] =APPLICATION_DIR . $zdj;
		}

		$zdjeciaBdk = $this->getImageBdk($idSerwis);
		foreach($zdjeciaBdk as $val) {
			$photos[] = 'http://'.$img_host.'/'.$val;
		}

		$kategoriaAllegro = new Kategoria_Allegro();
		$kategoria = $kategoriaAllegro->getByKategoriaAndKontoCheckParent($this->data['id_kategoria'], $idKonto);
		
		$szablonGraficzny = '';
		if(isset($kategoria['id_szablon_graficzny']) && $kategoria['id_szablon_graficzny'] > 0) {
				// pobieramy szablon graficzny z przetworzonymi blokami dynamicznymi
				$szablonGraficzny = new Allegro_SzablonGraficzny($kategoria['id_szablon_graficzny']);
				$szablonGraficzny = $szablonGraficzny->replaceBloki($kategoria['id_szablon_graficzny'], $this->id, $idSerwis);
		}
		
//sprawdzamy czy nie maosobnych danych dla Allegro
		if($this->data['allegro_data']['allegro_nazwa'] != '') $nazwa_aukcji = $this->data['allegro_data']['allegro_nazwa'];
		else $nazwa_aukcji = $this->data['nazwa'];
		
		if($this->data['allegro_data']['allegro_cena'] != '') $cena_aukcji = $this->data['allegro_data']['allegro_cena'];
		else $cena_aukcji = $this->data['nakladki'][$idSerwis]['cena'];
		
		
	    $return = array(
	        'title'=>$nazwa_aukcji,
	        'price_buynow'=>$cena_aukcji,
	        'id_category'=>$kategoria['id_allegro'],
	        'photos'=> $photos,
	        'objProduct'=>$this,
	        'id_szablon_dostawy'=>$kategoria['id_szablon_dostawy'],
	        'id_szablon_graficzny'=>$kategoria['id_szablon_graficzny'],
	        'szablon_graficzny'=> $szablonGraficzny,
			'format_sprzedazy' => $kategoria['format_sprzedazy'],
			'id_czas_trwania' => $kategoria['id_czas_trwania']
	    );
		
	    if($return['id_category'] > 0) {
	      $cat = Allegro::getCat($return['id_category']);
	      $return['category_name'] = $cat['nazwa'];
	    }
		
		return $return ;
	}
	
	
	/**
	 * Aktualizuje dane produktu obiektu Allegro (cena, kategoria, zdjecia)
	 * @param Allegro $aukcja
	 * @param int $idSerwis 
	 */
	public function updateProdukt(Allegro $aukcja, $idSerwis, $idKonto = 0) {
		$produktDane = $this->getDataAllegro($idSerwis, $idKonto);
		$aukcja->data['objProduct'] = $this;
		$aukcja->data['photos'] = $produktDane['photos'];
		$aukcja->data['fid'][8] = $produktDane['price_buynow'];
		$aukcja->data['category_name'] = $produktDane['category_name'];
		return $aukcja;
	}
	
	/**
	 * Aktualizuje dane aukcji Allergo (aktualne szablony, tytul, format sprzedazy) plus aktualizacja produktu
	 * @param Allegro $aukcja
	 * @param type $idSerwis 
	 */
	public function updateAuction(Allegro $aukcja, $idSerwis, $idKonto = 0) {
		$produktDane = $this->getDataAllegro($idSerwis, $idKonto );
		
		$aukcja = $this->updateProdukt($aukcja, $idSerwis, $idKonto);
		$aukcja->data['fid'][2] = $produktDane['id_category'];
		$aukcja->data['fid'][1] = $produktDane['title'];
		$aukcja->data['fid'][24] = $produktDane['szablon_graficzny'];
		$aukcja->data['fid'][4] = $produktDane['id_czas_trwania'];
		$aukcja->data['id_szablon_graficzny'] = $produktDane['id_szablon_graficzny'];
		$aukcja->data['id_szablon_dostawy'] = $produktDane['id_szablon_dostawy'];
		$aukcja->data['id_rodzaju'] = $produktDane['format_sprzedazy'];
		
		return $aukcja;
	}
}

?>