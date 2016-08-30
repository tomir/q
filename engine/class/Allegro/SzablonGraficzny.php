<?php

class Allegro_SzablonGraficzny {

	protected $nazwa;
	protected $opis;

	function __construct($pId=0) {
		
		if ($pId > 0) {
			try {

				$sql = "SELECT sg.* FROM allegro_szalony_graficzne sg WHERE sg.id = ".$pId;

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id			= $row['id'];
					$this -> nazwa		= $row['nazwa'];
					$this -> opis		= base64_decode($row['opis']);
				}
			}catch (PDOException $e){
				echo "Błąd nie można utworzyć obiektu SzablonGraficzny.";
				return false;
			}
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getNazwa() {
		return $this->nazwa;
	}
    
	public function getOpis() {
		return $this->opis;
	}
	
	/**
	 * Zwraca liste szablonów graficznych
	 *
	 * @param int $start	Zawiera limit poczatkowy
	 * @param int $limit	Zawierla ilość elementów do zwrócenia
	 * @return array
	 */
	public function getList($start = 0, $limit = 15) {

		$aResult = array();
		$sql = "SELECT sg.*
				FROM allegro_szalony_graficzne sg
				ORDER BY sg.nazwa ASC LIMIT ".$start.", ".$limit;
				
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				return $aResult;
			 } else return false;
		} catch (Exception $e){
			Log::SLog($e->getTraceAsString());
			return false;
		}
	 }

	/**
	 * Zapisuje dane w bazie
	 *
	 * @param array $aData	Zawiera tablicę z danymi do zapisania, gdzie indeksami tablicy sa kolumny tabeli
	 * @return bool			Zwraca id ostatnio dodanego rekordu lub false jesli nie udalo sie zapisac
	 */
	  public function save($aData) {

		$aData['opis'] = base64_encode($aData['opis']);
		  
		try {
			if($aData['id'] != 0)
				$res = ConnectDB::subAutoExec ("allegro_szalony_graficzne", $aData, "UPDATE", "id = ".$aData['id']);
			else
				$res = ConnectDB::subAutoExec ("allegro_szalony_graficzne", $aData, "INSERT");

			if($res)
				return $res;
			else
				return false;
		} catch (PDOException $e){
			 return false;
		}
	}
	
	public function getListSelect($filtr=NULL, $sort = NULL, $limit = NULL) {
		// zamieniamy id na klucze
		$return = array();
		foreach (self::getList($filtr, $sort, $limit) as $val) {
			$return[$val['id']] = $val['nazwa'];
		}

		return $return;
	}

	/**
	 * Usuwa dane z bazy
	 *
	 * @param int $id
	 * @return bool,errorCode
	 */
	 public function delete() {

		if($this->id) {
			 $sql = "DELETE FROM allegro_szalony_graficzne WHERE id = ".$this->id;
			try {
				if(ConnectDB::subExec($sql))
					return true;
				else return false;

			}catch (PDOException $e){

				return false;
			}
		} else return false;
	}

	/**
	 * Edycja w xajaxie
	 * @global type $serwisy
	 * @return type 
	 */
	public function ajaxTab0() {
		$htmlRes = SmartyObj::getInstance();
		$htmlRes->assign('obj', $this);
		$htmlRes->assign('serwisy', Website::getListSelect());

		$oFCKeditor = new FCKeditor('FCKeditor');
		$oFCKeditor->BasePath = FCK_DIR;
		$oFCKeditor->Width = '900';
		$oFCKeditor->Height = '500';
		$oFCKeditor->Value = $this->data['opis'];
		$output = $oFCKeditor->CreateHtml();
		$htmlRes->assign('FCKeditor', $output);

		$htmlRes->assign('blokiDynamiczne', $this->getBlokiDynamiczne());

		return $htmlRes->fetch('_ajax/allegro_szablony_graficzne.edit.tab0.tpl');
	}

	/**
	 * Pobiera opis istniejacego szablonu
	 * @param int $idSzablon
	 * @return xajaxResponse 
	 */
	public function ajaxGetOpis($idSzablon=0) {

		$idSzablon = (int) $idSzablon;
		if ($idSzablon == 0) {
			return false;
		}

		$objResponse = new xajaxResponse();
		$szablon = new Allegro_SzablonGraficzny($idSzablon);
		$objResponse->call('FCKeditorContent', $szablon->data['opis']);


		return $objResponse;
	}

	/**
	 * Pobiera opis z danymi produktu
	 * @param int $idSzablon
	 * @param int $idProdukt 
	 */
	public function ajaxGetOpisProduktu($idSzablon=0, $idProdukt=0, $idSerwis=0) {
		$objResponse = new xajaxResponse();

		try {
			
			$tresc = $this->replaceBloki($idSzablon, $idProdukt, $idSerwis);
			$objResponse->call('FCKeditorContent', $tresc);
		} catch (Exception $e) {
			$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
		}

		return $objResponse;
	}

	/**
	 * Zamienia bloki dynamiczne na wartosci
	 * @param int $idSzablon szablon
	 * @param int $idProdukt produkt
	 * @param int $idSerwis serwis
	 */
	public function replaceBloki($idSzablon=0, $idProdukt=0, $idSerwis=0) {
		$idSzablon = (int) $idSzablon;
		$idProdukt = (int) $idProdukt;
		$idSerwis = (int) $idSerwis;
		
		if ($idSzablon == 0) {
			throw new InvalidArgumentException('Nie istnieje taki szablon graficzny');
		}
		if ($idSzablon == 0) {
			throw new InvalidArgumentException('Nie istnieje taki produkt ', $code, $previous);
		}
		if ($idSerwis == 0) {
			throw new InvalidArgumentException('Wybierz serwis');
		}

		
		$szablon = new Allegro_SzablonGraficzny($idSzablon);
		
		// zastepowanie blokow dynamicznych
		return str_replace(
				$this->getBlokiDynamiczne(), $this->getWartosciBlokowDynamicznych($idProdukt, $idSerwis), $szablon->data['opis']
		);
	}

	/**
	 * Pobiera blogi dynamiczne
	 */
	private function getBlokiDynamiczne() {
		return array(
			'{__MINIATURKA__}',
			'{__ZDJECIA_BDK__}',
			'{__NAZWA_PRODUKTU__}',
			'{__OPIS__}',
			'{__DANE_TECHNICZNE__}',
			'{__GLOWNE_PARAMETRY__}',
			'{__GALERIA_AUKCJI__}'
		);
	}

	/**
	 * Pobiera wartosci blokow dynamicznych 
	 * @param int $idSzablon
	 * @param int $idProdukt
	 * @param int $idSerwis
	 * @return type 
	 */	
	private function getWartosciBlokowDynamicznych($idProdukt=0, $idSerwis=0) {

		$produkt = new Allegro_Produkt($idProdukt);
		$web = new Website($idSerwis);

		//opis
		$opis = '<table class="table-allegro">';
		foreach ($produkt->getParametry($idSerwis) as $k => $v) {
			$opis .= '<tr><th colspan="2">' . $k . '</th></tr>';
			foreach ($v as $v2) {
				$opis .= '<tr>';
				if (is_array($v2['wartosc']))
					$wartosc = implode(', ', $v2['wartosc']);
				else
					$wartosc = $v2['wartosc'];

				$opis.= '<td class="name-l">' . $v2['atrybut'] . '</td><td class="name-r">' . $wartosc . '</td>';
				$opis .= '</tr>';
			}
		}
		$opis.= '</table>';

		// zdjecie
		$img_host = $web->data['img_host'];
		
		$zdj = $produkt->getImage($idSerwis);
		if(!empty($zdj)) {
			$zdj = '<img src="http://' . $img_host . '/' . $zdj . '" border="0">';
		}
		
		
		// zdjecia bdk
		$zdjeciaBdkHtml = '';
		foreach($produkt->getImageBdk($idSerwis) as $val) {
			$zdjeciaBdkHtml.= '<br /><img src="http://' . $img_host . '/' . $val .'" border="0">';
		}
		
		// pobieramy konta do galerii aukcji 
		$allegroKonto = new Allegro_Konto();
		$konto = Allegro_Konto::getList(array('id_serwisu'=>$idSerwis));
		$konto = $konto[0];

		return array(
			$zdj,
			$zdjeciaBdkHtml,
			$produkt->data['nazwa'],
			$produkt->data['kupic_opis'],
			$opis,
			$this->getParametryGlowneHtml($produkt->id, $produkt->data['id_kategoria']),
			stripslashes(html_entity_decode($konto['galeria_aukcji']))
		);
	}
	
	/**
	 * pobieramy glowne parametry techniczne - te ktore sa wybrane w filtrach
	 * @param int $idProdukt
	 * @param int $idKategoria 
	 */
	public function getParametryGlowneHtml($idProdukt = 0, $idKategoria = 0) {
		$idProdukt = (int)$idProdukt;
		$idKategoria = (int)$idKategoria;
		if($idProdukt == 0) {
			throw new InvalidArgumentException('Podaj id produktu');
		}
		if($idKategoria == 0) {
			throw new InvalidArgumentException('Podaj id kategorii');
		}
		
		$filtryPolaWartosci = KupicFiltry::getWartosciPolProduktu($idProdukt, $idKategoria);
		
		// i tworzymy wyglad
		$opis = '';
		if(count($filtryPolaWartosci) > 0) {
			$opis = '<table class="table-allegro">';
			foreach ($filtryPolaWartosci as $pole) {
				$opis .= '<tr>';
				$opis.= '<td class="name-l">' . $pole['nazwa'] . '</td><td class="name-r">' . implode(' ,', $pole['wartosci']) . '</td>';
				$opis .= '</tr>';
			}
			$opis.= '</table>';
		}
		
		return $opis;
		
	}
	
	/**
	 * Pobiera szablony pogrupowane po serwisie
	 * @global array $_gTables
	 * @return array 
	 */	
	static public function getListGroupBySerwis() {
		$list = self::getList(false, array('sort'=>'nazwa', 'order'=>'ASC'), false);
		// i grupujemy
		$group = array();
		foreach($list as $val) {
			if(!array_key_exists($val['id_serwisu'], $group)) {
				$group[$val['id_serwisu']] = array();
			}
			
			$group[$val['id_serwisu']][$val['id']] = $val;
		}
		
		return $group;
	}
	


}

?>