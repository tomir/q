<?php

/**
 * Klasa obslugujaca wystawianie i pobieranie aukcji allegro.pl
 *
 * @package Allegro
 */
class Allegro {

	public $id				= 0;
	public $id_serwisu			= 0;
	public $id_aukcji			= 0;
	public $id_produktu		= 0;
	public $id_rodzaju			= 0;
	public $data_wystawienia	= '';
	public $data_zakonczenia	= '';
	public $data_cron			= '';
	public $sztuk			= 0;
	public $sprzedano			= 0;
	public $nazwa_aukcji		= '';
	public $cena_kup_teraz		= 0;
	public $auto_renew		= 0;
	public $flaga_zakonczona	= 0;
	public $m_kto			= 0;
	public $wyswietlen		= 0;
	public $id_konto			= 0;
	public $zawieszona		= 0;
	public $alert_mag			= 0;
	public $alert_mag_sent		= 0;
	public $fidGeneral = array();

	public function __construct($id=0) {
		
		if ((int) $id > 0) {
			try {
			
				$sql = "SELECT a.*, aa.dane AS aukcja
						FROM " . MyConfig::getValue("__allegro") . " a
						LEFT JOIN " . MyConfig::getValue("__allegro_aukcja") . " aa ON aa.id_allegro = a.id
						WHERE a.id = " . $id;

				$aResult = ConnectDB::subQuery($sql);
				if(!is_array($aResult)){
					return false;
				}
				foreach ($aResult as $row) {
					$this -> id					= $row[ 'id' ];
					
					if (isset($row['aukcja'])) {
						$aukcja = unserialize(html_entity_decode($row['aukcja'], ENT_QUOTES));
						$aukcja['fid']['24'] = base64_decode($aukcja['fid']['24']);
						foreach($aukcja as $key=>$val) {
								$this -> fid[$key] = $val;
						}
					}
					
					if(isset($this -> fid[2])) {
						$kategoria = Allegro::getCat($this -> fid[2]);
						$this -> category_name = $kategoria['nazwa'];
					}

					if(isset($this -> fid[1])) {
						$this -> fid[1] = html_entity_decode($this -> fid[1]);
					}
		
					$this -> id_serwisu			= $row[ 'id_serwisu' ];
					$this -> id_aukcji				= $row[ 'id_aukcji' ];
					$this -> id_produktu			= $row[ 'id_produktu' ];
					$this -> id_rodzaju				= $row[ 'id_rodzaju' ];
					$this -> data_wystawienia		= $row[ 'data_wystawienia' ];
					$this -> data_zakonczenia		= $row[ 'data_zakonczenia' ];
					$this -> data_cron				= $row[ 'data_cron' ];
					$this -> sztuk				= $row[ 'sztuk' ];
					$this -> sprzedano			= $row[ 'sprzedano' ];
					$this -> nazwa_aukcji			= $row[ 'nazwa_aukcji' ];
					$this -> cena_kup_teraz			= $row[ 'cena_kup_teraz' ];
					$this -> auto_renew			= $row[ 'auto_renew' ];
					$this -> flaga_zakonczona		= $row[ 'flaga_zakonczona' ];
					$this -> m_kto				= $row[ 'm_kto' ];
					$this -> wyswietlen			= $row[ 'wyswietlen' ];
					$this -> id_konto				= $row[ 'id_konto' ];
					$this -> zawieszona			= $row[ 'zawieszona' ];
					$this -> alert_mag				= $row[ 'alert_mag' ];
					$this -> alert_mag_sent			= $row[ 'alert_mag_sent' ];
					
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
	
	public function getFid() {
		return $this->fid;
	}
	
	public function getIdSerwisu() {
		return $this->id_serwisu;
	}
	
	public function getIdAukcji() {
		return $this->id_aukcji;
	}
	
	public function getIdProduktu() {
		return $this->id_produktu;
	}
	
	public function getIdRodzaju() {
		return $this->id_rodzaju;
	}
	
	public function getDataWystawienia() {
		return $this->data_wystawienia;
	}
	
	public function getDataZakonczenia() {
		return $this->data_zakonczenia;
	}
	
	public function getDataCron() {
		return $this->data_cron;
	}
	
	public function getSztuk() {
		return $this->sztuk;
	}
	
	public function getSprzedano() {
		return $this->sprzedano;
	}
	
	public function getNazwaAukcji() {
		return $this->nazwa_aukcji;
	}
	
	public function getCenaKupTeraz() {
		return $this->cena_kup_teraz;
	}
	
	public function getAutoRenew() {
		return $this->auto_renew;
	}
	
	public function getFlagaZakonczona() {
		return $this->flaga_zakonczona;
	}
	
	public function getMKto() {
		return $this->m_kto;
	}
	
	public function getWyswietlen() {
		return $this->wyswietlen;
	}
	
	public function getIdKonto() {
		return $this->id_konto;
	}
	
	public function getZawieszona() {
		return $this->zawieszona;
	}
	
	public function getAlertMag() {
		return $this->alert_mag;
	}
	
	public function getAlertMagSent() {
		return $this->alert_mag_sent;
	}
	
	/**
	 * Zapisuje dane w bazie
	 *
	 * @param array $aData	Zawiera tablicę z danymi do zapisania, gdzie indeksami tablicy sa kolumny tabeli
	 * @return bool			Zwraca id ostatnio dodanego rekordu lub false jesli nie udalo sie zapisac
	 */
	  public function save($aData) {

		try {
			if($aData['id'] != 0)
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__allegro"), $aData, "UPDATE", "id = ".$aData['id']);
			else
				$res = ConnectDB::subAutoExec (MyConfig::getValue("__allegro"), $aData, "INSERT");

			if($res) {
				$this->id = $res;
				$this->saveAuctionData($this->id, $aData['aukcja']);
				return true;
			}
			else
				return false;
		} catch (PDOException $e){
			 return false;
		}
	}
	
	public function saveAuctionData($id, $dane) {

		if($id > 0) {
		
			$sql = "DELETE FROM ". MyConfig::getValue("__allegro_aukcja") ." WHERE id_allegro = " . $id;
			ConnectDB::subExec($sql);

			$aData = array(
				'id_allegro' => $id,
				'dane' => $dane
			);
			ConnectDB::subAutoExec (MyConfig::getValue("__allegro_aukcja"), $aData, 'INSERT');
			return true;
			
		} else {
			return false;
		}
	}


	/**
	 * Zwraca liste aukcji
	 *
	 * @param int $start	Zawiera limit poczatkowy
	 * @param int $limit	Zawierla ilość elementów do zwrócenia
	 * @return array
	 */
	
	public function getList($start = 0, $limit = 15) {

		$liczbaOfertSql = "(SELECT count(id) FROM " . MyConfig::getValue('__allegro_sprzedane') . " asp WHERE asp.id_aukcji= a.id_aukcji) AS liczba_ofert,";

		$sql = "SELECT " . $liczbaOfertSql . " 
					a.*, p.nazwa, p.nr_katalogowy, DATEDIFF(a.data_zakonczenia,CURDATE()) AS do_konca,  TIMEDIFF(a.data_zakonczenia, NOW()) AS do_konca_czas , CONCAT(ad.first_name,' ',ad.last_name) AS admin,
					IF (a.data_zakonczenia_recznego is not null && a.data_zakonczenia_recznego<a.data_zakonczenia, 1,0) as zakonczone_przed_czasem,
					p.stan_magazynowy,
					ak.login as konto_allegro
					FROM " . MyConfig::getValue('__allegro') . " a
					LEFT JOIN " . MyConfig::getValue('__allegro_konto') . " ak ON ak.id = a.id_konto
					LEFT JOIN " . MyConfig::getValue('__produkty') . " p ON p.id = a.id_produktu
					LEFT JOIN " . MyConfig::getValue('__users') . " ad ON ad.id = a.m_kto
					WHERE 1 ";
		
		try {
			if($aResult = ConnectDB::subQuery($sql)) {
				
				foreach ($aResult as $key => $val) {
					
					list($godzin, $minut, $sekund) = explode(':', $val['do_konca_czas']);
					$aResult[$key]['do_konca_czas_opis'] = '';

					//jezeli minal termin zakonczenia
					if ($val['do_konca_czas'] < '00:00:00') {
						continue;
					}

					if ((int) $godzin > 0) {
						$aResult[$key]['do_konca_czas_opis'] = (int) $godzin . ' godz.';
					} elseif ((int) $minut > 0) {
						$aResult[$key]['do_konca_czas_opis'] = (int) $minut . ' min.';
					} elseif ((int) $sekund > 0) {
						$aResult[$key]['do_konca_czas_opis'] = (int) $sekund . ' sek.';
					}
				}
				
				return $aResult;
			 } else return false;
		} catch (Exception $e){
			Log::SLog($e->getTraceAsString());
			return false;
		}
	}

	/**
	 * Generuje ciag SQL filtrujacy wg podanej tablicy $filtr
	 *
	 * @param array $filtr
	 */
	function filterGetList($filtr=NULL) {
		global $_gTables; //Debug($filtr);

		$sql = '';

		if (isset($filtr) && is_array($filtr)) {
			if (isset($filtr['nazwa_aukcji']) && $filtr['nazwa_aukcji'] != '')
				$sql.= " AND a.nazwa_aukcji LIKE '%" . Common::clean_input($filtr['nazwa_aukcji']) . "%' ";

			if (isset($filtr['id_serwisu']) && $filtr['id_serwisu'] > 0 && is_numeric($filtr['id_serwisu'])) {
				$sql .= " AND a.id_serwisu = '" . $filtr['id_serwisu'] . "' ";
			}

			if (isset($filtr['website_ids']) && is_array($filtr['website_ids']))
				$sql.= " AND a.id_serwisu IN (" . implode(',', $filtr['website_ids']) . ") ";

			if (isset($filtr['typ']) && (int) $filtr['typ'] > 0) {
				switch ($filtr['typ']) {
					case 3: //trwajace
						$sql.= " AND ( a.data_zakonczenia > NOW() AND a.flaga_zakonczona=0 )";
						break;
					case 4: //zakonczone
						$sql.= " AND ( a.data_zakonczenia <= NOW() OR a.flaga_zakonczona=1 )";
						break;
				}
			}
			
			if(isset($filtr['po_czasie_aktywne'])) {
				$sql.= " AND data_zakonczenia < NOW() AND flaga_zakonczona = '0'";
			}

			if (isset($filtr['sprzedano'])) {
				if ($filtr['sprzedano'] == 0) {
					//niesprzedano
					$sql.= " AND a.sprzedano = 0 ";
				} elseif ($filtr['sprzedano'] == 1) {
					$sql.= " AND a.sprzedano > 0 ";
				}
			}
			
			if (isset($filtr['brak_ofert'])) {
				if ($filtr['brak_ofert'] == 0) {
					//niesprzedano
					$sql.= " AND a.sprzedano > 0 AND a.flaga_zakonczona = 1";
				} elseif ($filtr['brak_ofert'] == 1) {
					$sql.= " AND a.sprzedano = 0 AND a.flaga_zakonczona = 1";
				}
			}

			if ($filtr['oczekujace'])
				$sql.= " AND a.data_cron > NOW() ";
			if (isset($filtr['cron']) && $filtr['cron']==0) {
				$sql.= " AND a.id_aukcji > 0";
			}

			if ($filtr['wystawione'])
				$sql.= " AND a.data_wystawienia < NOW() ";
			

			if (isset($filtr['id_kategoria']) && is_numeric($filtr['id_kategoria']) && (int) $filtr['id_kategoria'] > 0) {
				$kat = new Kategoria();
				$drzewko = $kat->getAllChildren($filtr['id_kategoria']);
				$drzewko[] = $filtr['id_kategoria'];
				$drzewko = implode(',', $drzewko);
				if (substr($drzewko, strlen($drzewko) - 1, 1) == ',')
					$drzewko = substr($drzewko, 0, strlen($drzewko) - 1);
				$sql.= " AND p.id_kategoria IN (" . $drzewko . ") ";
			}

			if (isset($filtr['id_kategoria']) && is_array($filtr['id_kategoria']) && count($filtr['id_kategoria']) > 0) {
				$kat = new Kategoria();
				foreach ($filtr['id_kategoria'] as $tmp) {
					$drzewko = $kat->getAllChildren($tmp);
					$drzewko[] = $tmp;
				}
				$drzewko = implode(',', $drzewko);
				if (substr($drzewko, strlen($drzewko) - 1, 1) == ',')
					$drzewko = substr($drzewko, 0, strlen($drzewko) - 1);
				$sql.= " AND p.id_kategoria IN (" . $drzewko . ") ";
			}

			if (isset($filtr['admin']) && !empty($filtr['admin'])) {
				$admin = explode(' ', trim($filtr['admin']));
				if (count($admin) > 0) {
					if (count($admin) > 1) {//wiecej niz jedno slowo to musi byc imie i nazwisko
						$sql.= " AND ( ad.first_name LIKE '%" . implode("%' OR ad.first_name LIKE '%", $admin) . "%' ) AND ( ad.last_name LIKE '%" . implode("%' OR ad.last_name LIKE '%", $admin) . "%' )";
					} else { //tylko jedno slowo to albo imie albo nazwisko
						$sql.= " AND ( ad.first_name LIKE '%" . implode("%' OR ad.first_name LIKE '%", $admin) . "%' OR ad.last_name LIKE '%" . implode("%' OR ad.last_name LIKE '%", $admin) . "%' )";
					}
				}
			}

			if (isset($filtr['ids']) && is_array($filtr['ids']) && !empty($filtr['ids'])) {
				$sql.= " AND (a.id = '" . implode("' OR a.id='", $filtr['ids']) . "')";
			}
			
			if ($filtr['id_aukcji'] && (int) $filtr['id_aukcji'] > 0 && !is_array($filtr['id_aukcji']))
				$sql.= " AND a.id_aukcji=" . (int)$filtr['id_aukcji'];
			
			if (isset($filtr['id_aukcji']) && is_array($filtr['id_aukcji']) && !empty($filtr['id_aukcji'])) {
				$sql.= " AND (a.id_aukcji = '" . implode("' OR a.id_aukcji='", $filtr['id_aukcji']) . "')";
			}
			
			if( isset($filtr['format_sprzedazy']) && is_numeric($filtr['format_sprzedazy']) ) {
				$filtr['format_sprzedazy'] = (int)$filtr['format_sprzedazy'];
				$sql.= " AND id_rodzaju=" . $filtr['format_sprzedazy'];
			}
			
			
		}

		return $sql;
	}

	/**
	 * Redukuje obraz do wielkości nadającej się do przesyłu.
	 *
	 * @param string $url URL obrazka (lokalne, albo sieciowe).
	 * @return string Binarna zawartość obrazka w formacie JPEG.
	 */
	public static function resize($url) {
		$image = file_get_contents($url);
		
		$temp = imagecreatefromstring($image);
		$x = ceil(0.9 * imagesx($temp));
		$y = ceil(0.9 * imagesy($temp));

		$image = imagecreatetruecolor($x, $y);
		imagecopyresized($image, $temp, 0, 0, 0, 0, $x, $y, imagesx($temp), imagesy($temp)); 
		$filename = _TEMP_DIR . time() . rand(1, 999999) . '.jpg'; 
		imagejpeg($image, $filename, 75);
		$image = file_get_contents($filename); 
		unlink($filename);
		

		return $image;
	}

	/**
	 * Zwraca dane pojedynczego pola dodatkowego
	 *
	 * @return array
	 */
	static public function getAuctionField($id) {

		$sql = "SELECT * FROM " . MyConfig::getValue('__allegro_kategorie_parametry') . " WHERE form_id = '" . $id . "' ";
		$field = ConnectDB::subQuery($sql, "fetch");

		return $field;
	}

	/**
	 * Zwieksza ilosc sprzedanych przedmiotow
	 *
	 * @param int $sprzedanych
	 */
	public function updateSold($sprzedanych, $idAukcja = 0) {

		if($idAukcja == 0) {
			$idAukcja = $this->id;
		}
		
		$sql = "UPDATE " . MyConfig::getValue('__allegro') . " SET sprzedano = sprzedano + '" . (int)$sprzedanych . "' WHERE id =" . $idAukcja;
		if (!ConnectDB::subExec($sql)) {
			Common::log(__METHOD__, $db->ErrorMsg() . "\n" . $sql);
		}
	}

	/**
	 * Zwraca drzewko kategorii z allegro
	 *
	 * @return array
	 */
	function getCatTree() {

		$sql = "SELECT * FROM " . MyConfig::getValue('__allegro_kategorie') . " ORDER BY id ASC";
		$all = ConnectDB::subQuery($sql);
		
		if (!is_array($all) && count($all) < 1)
			Common::log(__METHOD__, $db->ErrorMsg() . "\n" . $sql);

		$tree = $this->_getTree(0, $all);
		return $tree;
	}

	/**
	 * Wewnetrzna funkcja generujaca rekurencyjnie drzewko kategorii
	 *
	 * @param int $id
	 * @param array $tree
	 * @param int $level
	 * @return array
	 */
	private function _getTree($id, $tree, $level = 0) {
		foreach ($tree as $item) {
			if ($item['parent_id'] == $id) {
				$temp_array[] = array(
					'id' => $item['id'],
					'nazwa' => $item['nazwa'],
					'parent_id' => $item['parent_id'],
					'level' => $level
				);
			}
		}

		if (isset($temp_array)) {
			for ($i = 0; $i < sizeof($temp_array); $i++) {
				$element = $temp_array[$i];
				$array[$element['id']] = $element;
				$array[$element['id']]['children'] = $this->_getTree($element['id'], $tree, $level + 1);
			}
		}
		return (isset($array) ? $array : false);
	}

	/**
	 * Dzieli aukcje ze względu na serwisy
	 * @global array $_gTables
	 * @param array $ids
	 * @return array 
	 */
	public function getAuctionBySerwisy(array $ids) {

		if (count($ids) == 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}

		# pobieramy id_allegro, id_serwisu no i dzielimy ze wzgledu na serwisy
		$sql = "SELECT id, id_serwisu, id_aukcji 
				FROM " . MyConfig::getValue("__allegro") . "
				WHERE id='" . implode("' OR id='", $ids) . "'";
		
		$aukcje = ConnectDB::subQuery($sql);
		$aukcjeSerwisy = array();

		foreach ($aukcje as $aukcja) {

			if (!array_key_exists($aukcja['id_serwisu'], $aukcjeSerwisy)) {
				$aukcjeSerwisy = array(
					$aukcja['id_serwisu'] => array(
						'id' => array(),
						'id_aukcji' => array(),
						'parse' => array()
					)
				);
			}

			$aukcjeSerwisy[$aukcja['id_serwisu']]['id'][] = $aukcja['id'];
			$aukcjeSerwisy[$aukcja['id_serwisu']]['id_aukcji'][] = $aukcja['id_aukcji'];
			$aukcjeSerwisy[$aukcja['id_serwisu']]['parse'][$aukcja['id_aukcji']] = $aukcja['id'];
		}

		return $aukcjeSerwisy;
	}

	/**
	 * Uwaga, multi sam laczy sie z kontem z zaleznosci od id_serwisu aukcji
	 * @global type $_gTables
	 * @param array $ids
	 * @return type 
	 */
	public function zakonczAukcjeMulti(array $ids) {
		global $_gTables;
		$db = Db::getInstance();

		try {
			if (count($ids) == 0) {
				throw new InvalidArgumentException('Podaj id aukcji');
			}

			$aukcjeSerwisy = $this->getAuctionBySerwisy($ids);
			# i zakanczamy dla poszczegolnych serwisow
			$result = array();
			foreach ($aukcjeSerwisy as $idSerwisu => $aukcje) {
				$allegroApi = new AllegroApi($idSerwisu);
				if ($allegroApi == false) {
					throw new InvalidArgumentException('Nie można połączyć się z serwisem');
				}

				#pobieramy wynik soap
				$resultApi = $allegroApi->finishItems($aukcje['id_aukcji']);


				//zamiana na id z tabeli z id_aukcji
				$key = 'finish-items-succeed';
				if (array_key_exists($key, $resultApi)) {
					$resultApi[$key] = $this->parseIdAllegroToId($resultApi[$key], $aukcje['parse']);
				}

				$key = 'finish-items-failed';
				if (array_key_exists($key, $resultApi)) {
					$resultApi[$key] = (array) $resultApi[$key];
					foreach ($resultApi[$key] as $failedKey => $failedItem) {
						$failedItem = (array) $failedItem;
						$resultApi[$key][$failedKey] = $failedItem;
						$resultApi[$key][$failedKey]['finish-item-id'] = $aukcje['parse'][$failedItem['finish-item-id']];
					}
				}

				$result = array_merge_recursive($resultApi, $result);

				# zakanczamy aukcje w bazie
				if (count($resultApi['finish-items-succeed']) > 0) {
					foreach ($resultApi['finish-items-succeed'] as $id) {
						$this->zakonczAukcjeRecznie($id);
					}
				}
			}
			return $result;
		} catch (Exception $e) {
			return array('error' => $e->getMessage());
		}
	}

	
	public function zakonczAukcjeRecznie($id) {
		
		$aData = array(
			'id' => $id,
			'flaga_zakonczona' => '1',
			'data_zakonczenia_recznego' => date("Y-m-d H:i:s", time())
		);
		$this->save($aData);
		$this->zakonczAukcjeInProdukt($id);
	}
	
	public function zakonczAukcje($id) {

		if($id <= 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$aData = array(
			'id' => $id,
			'flaga_zakonczona' => '1'
		);
		$this->save($aData);
		$this->zakonczAukcjeInProdukt($id);
	}

	public function idToKey($lista) {
		
		$result = array();
		foreach ($lista as $key => $val) {
			$result[$val['id']] = $val;
		}

		return $result;
	}

	public function idAukcjiToKey($lista) {
		
		$result = array();
		foreach ($lista as $key => $val) {
			$result[$val['id_aukcji']] = $val;
		}

		return $result;
	}

	/**
	 * Zamienia znaki - na _ w kluczach bo smart - nie akceptuje w nazwie zmiennej/klucza
	 * @param array $data 
	 */
	static public function replaceDashInKey(array $data) {
		$result = array();
		foreach ($data as $key => $val) {
			$key = str_replace('-', '_', $key);
			if (is_object($val)) {
				$val = (array)$val;
			}
			if (is_array($val)) {
				$val = self::replaceDashInKey($val);
			}
			$result[$key] = $val;
		}

		return $result;
	}

	function getCat($id) {

		$sql = "SELECT *
			   FROM " . MyConfig::getValue("__allegro_kategorie") . "
			   WHERE id = $id";

		return ConnectDB::subQuery($sql, "fetch");
	}

	function checkChildrens($parent) {

		$sql = "SELECT COUNT(*) as ile
				FROM " . MyConfig::getValue("__allegro_kategorie") . "
				WHERE parent_id = $parent";
		
		$count = ConnectDB::subQuery($sql, "fetch");
		if ($count['ile'] > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getCatFields($id) {

		$filelds = array();
		$sql = "SELECT *
			   FROM " . MyConfig::getValue("__allegro_kategorie_parametry") . "
			   WHERE id_kategorii = $id";

		$filelds = ConnectDB::subQuery($sql);

		if (count($fields) == 0) {
			$sql = "SELECT parent_id
				FROM " . MyConfig::getValue("__allegro_kategorie") . "
				WHERE id = $id";

			$parent_id = ConnectDB::subQuery($sql, "fetch");

			if ($parent_id['parent_id'] > 0) {
				$sql = "SELECT *
					   FROM " . MyConfig::getValue("__allegr_kategorie_parametry") . "
					   WHERE id_kategorii = $parent_id";

				$fields = ConnectDB::subQuery($sql);

				if (count($fields) == 0) {
					$sql = "SELECT parent_id
						FROM " . MyConfig::getValue("__allegro_kategorie") . "
						WHERE id = $parent_id";

					$parent_id = ConnectDB::subQuery($sql, "fetch");

					if ($parent_id['parent_id'] > 0) {
						$sql = "SELECT *
							   FROM " . MyConfig::getValue("__allegro_kategorie_parametry") . "
							   WHERE id_kategorii = $parent_id";

						$fields = ConnectDB::subQuery($sql);

						if (count($fields) == 0) {
							$sql = "SELECT parent_id
								FROM " . MyConfig::getValue("__allegro_kategorie") . "
								WHERE id = $parent_id";

							$parent_id = ConnectDB::subQuery($sql, "fetch");

							if ($parent_id['parent_id'] > 0) {
								$sql = "SELECT *
									   FROM " . MyConfig::getValue("__allegro_kategorie_parametry") . "
									   WHERE id_kategorii = $parent_id";

								$fields = ConnectDB::subQuery($sql);
							}
						}
					}
				}
			}
		}

		foreach ($fields as $k => $v) {
			if ($v['form_desc'] != '')
				$fields[$k]['form_desc_tab'] = explode('|', $v['form_desc']);

			if ($v['form_opts_values'] != '')
				$fields[$k]['form_opts_values_tab'] = explode('|', $v['form_opts_values']);
		}
		//d($fields);
		return $fields;
	}

	function ajaxCatBoxHTML($parent = 0, $idCategorySelected = 0) {
		$htmlRes = SmartyObj::getInstance();

		$htmlRes->assign('allegro_cats', self::getCategoriesByParent($parent));
		$htmlRes->assign('idCategorySelected', $idCategorySelected);
		return $htmlRes->fetch('allegro/catbox.tpl');
	}

	function getCategoriesByParent($parent = 0) {

		$sql = "SELECT *
				FROM " . MyConfig::getValue("__allegro_kategorie") . "
				WHERE parent_id = $parent
				ORDER BY pozycja ASC";

		$cats = ConnectDB::subQuery($sql);

		if ($parent > 0) {
			$sql = "SELECT *
					FROM " . MyConfig::getValue("__allegro_kategorie") . "
					WHERE id = $parent";

			$aParent = ConnectDB::subQuery($sql, "fetch");

			if (is_array($aParent)) {
				$new_parent['id'] = $aParent['parent_id'];
				$new_parent['nazwa'] = '.. ' . $aParent['nazwa'];
				array_unshift($cats, $new_parent);
			}
		}
		//print_r($cats);
		return $cats;
	}

	/**
	 * Zamienia id_allegro na id_aukcji
	 * @param type $auctionsId tablica id_aukcji
	 * @param type $parse tablica gdzie klucze do id_aukjci a wartosci to id w tabeli allegro
	 * @return array tablica idkows
	 */
	public function parseIdAllegroToId(array $auctionsId, array $parse) {
		$id = array();
		foreach ($auctionsId as $auctionId) {
			if (array_key_exists($auctionId, $parse)) {
				$id[] = $parse[$auctionId];
			}
		}

		return $id;
	}

	/**
	 * Dodaje opis do wielu aukcji
	 * @param array $auctionsId
	 * @param string $desc
	 * @return apiResult,error 
	 */
	public function addDescriptionMulti(array $auctionsId, $desc) {
		try {
			$aukcjeSerwisy = $this->getAuctionBySerwisy($auctionsId);

			# i zakanczamy dla poszczegolnych serwisow
			$result = array();
			foreach ($aukcjeSerwisy as $idSerwisu => $aukcje) {
				$allegroApi = new AllegroApi($idSerwisu);
				if ($allegroApi == false) {
					throw new InvalidArgumentException('Nie można połączyć się z serwisem');
				}

				$resultTmp = $allegroApi->doAddDescToItems($aukcje['id_aukcji'], $desc);
				foreach ($resultTmp as $key => $val) {
					$resultTmp[$key] = $this->parseIdAllegroToId($val, $aukcje['parse']);
				}
				$result = array_merge_recursive($resultTmp, $result);
			}
			return $result;
		} catch (Exception $e) {
			return array('error' => $e->getMessage());
		}
	}

	/**
	 * Dodaje opisy do wielu produktów 
	 * @param array $formData array(id=array(), desc=>'')
	 * @return xajaxResponse 
	 */
	public function ajaxAddDescriptionMulti($formData) {

		$data = $formData['formData'];
		$objResponse = new xajaxResponse();
		$filtr = array(
			'ids' => $data['id']
		);

		$apiResult = $this->addDescriptionMulti($data['id'], $data['description']);

		// result
		if (isset($apiResult['error'])) {
			$objResponse->alert('Wystąpił błąd: ' . $apiResult['error']);
			$objResponse->script("document.getElementById('btnWystaw').innerHTML = 'Wystaw';
								  document.getElementById('btnWystaw').disabled = false;");
		} else {
			if (count($filtr['ids']) > 0) {
				$listaAll = $this->getList($filtr, $sort, $limit);

				//laczenie api result z danymi z bazy
				$lista = array();
				foreach ($listaAll as $aukcja) {
					foreach ($apiResult as $key => $val) {
						if (in_array($aukcja['id'], $val)) {
							$lista[str_replace('-', '_', $key)][] = $aukcja;
						}
					}
				}

				$htmlRes = SmartyObj::getInstance();
				$htmlRes->assign('lista', $lista);
				$objResponse->assign('editTabs', "innerHTML", $htmlRes->fetch('_ajax/allegro.add_description_multi.result.tpl'));
			}
		}

		return $objResponse;
	}

	/**
	 * Sprawdzamy czy aukcje podane istnieja, zwraca istniejace z podanych aukcji
	 * @global type $_gTables
	 * @param array $auctionIds tablica aukcji do sprawdzenia
	 * @return array tablica aukcji istniejacych z podanych
	 */
	public function checkAuctionExist(array $auctionIds) {
		$result = array();
		if (count($auctionIds) == 0) {
			return $result;
		}

		$sql = "SELECT id, id_aukcji FROM " . MyConfig::getValue("__allegro") . " a WHERE id_aukcji = '" . implode("' OR id_aukcji='", $auctionIds) . "'";
		return ConnectDB::subQuery($sql, "assoc");
	}

	/**
	 * Sprawdza czy istnieją aukcje z wybranymi produktami i serwisem
	 * @global array $_gTables
	 * @param array $idProdukty
	 * @param int $idSerwis
	 * @return array Zwraca tablice gdzie klucz jest id produktu a wartosc id aukcji 
	 */
	public function checkAuctionsExistByProduct(array $idProdukty, $idSerwis) {
		
		$idSerwis = (int)$idSerwis;
		if(empty($idProdukty)) {
			throw new Exception('Podaj id produktów');
		}
		if($idSerwis <= 0) {
			throw new Exception('Podaj id serwisu');
		}
		
		$sql = "SELECT id_produktu, id FROM " . MyConfig::getValue("__allegro") . " WHERE id_serwisu=" . $idSerwis. " AND (id_produktu=" . implode( " OR id_produktu=", $idProdukty) . ")";
		$aukcje = ConnectDB::subQuery($sql, "assoc");
		
		$return = array();
		foreach($idProdukty as $idProdukt) {
			$return[$idProdukt] = '';
			
			if(key_exists($idProdukt, $aukcje)) {
				$return[$idProdukt] = $aukcje[$idProdukt];
			}
		}
		
		return $return;
	}
	
	/**
	 * Sprzedaje ponownie wiele aukcji
	 * @param array $formData
	 * @return xajaxResponse 
	 */
	public function ajaxSellAgainMulti($formData) {

		$data = $formData['formData'];
		$objResponse = new xajaxResponse();
		
		
		$sprzedaz = new Allegro_Sprzedaz();

		try {
			$sprzedaz->setAllegroApi($data['id_serwisu']);	
		} catch (Exception $e) {
			$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
			return $objResponse;
		}
		
		
		$aukcjeStare = $this->getList(array('ids'=>$data['id']));
		
		$blad = array();
		$bladAllegro = array();
		$apiResult = array();
		$result = array();
		$wystawione = array();
		$niewystawione = array();
		
		$lista = array();
		foreach($aukcjeStare as $aukcjaStara) {
			$aukcja = array(
				'nazwa' => $aukcjaStara['nazwa']
			);
			
			try {
				$apiResult = $sprzedaz->sprzedazPonowna($aukcjaStara['id'], $data);
				$aukcja['id_aukcji'] = $apiResult['item-id'];
				$aukcja['koszt'] = $apiResult['item-info'];
				$wystawione[] = $aukcja;
			} catch (Exception $e) {
				$aukcja['blad'] =  $e->getMessage();
				$niewystawione[] = $aukcja;
			} 	
		}
		
	
		$htmlRes = SmartyObj::getInstance();
		$htmlRes->assign('wystawione', $wystawione);
		$htmlRes->assign('niewystawione', $niewystawione);
		$objResponse->assign('editTabs', "innerHTML", $htmlRes->fetch('_ajax/allegro.sell_again_multi.result.tpl'));

		return $objResponse;
	}
	
	/**
	 * Sprzedaje wiele aukcji
	 * @param array $formData
	 * @return xajaxResponse 
	 */
	public function ajaxSellMulti($formData) {

		$data = $formData['formData'];
		$objResponse = new xajaxResponse();
		
		
		$sprzedaz = new Allegro_Sprzedaz();

		try {
			$sprzedaz->setAllegroApi($data['id_serwisu']);	
		} catch (Exception $e) {
			$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
			return $objResponse;
		}
		
		$blad = array();
		$bladAllegro = array();
		$apiResult = array();
		$result = array();
		$wystawione = array();
		$niewystawione = array();
		
		$lista = Produkt::getList(array('id_produkt'=>$data['id'], 'id_serwisu'=>1));	
		foreach($lista as $produkt) {
			$idProdukt = $produkt['id'];
			$aukcja = array(
				'nazwa' => $produkt['nazwa']
			);
			
			try {
				// tworzymy nowa aukcje na bazie produktu
				$aukcjaObj = new Allegro();
				$aukcjaObj->data['id_produktu'] = $idProdukt;
				$aukcjaObj->data['id_serwisu'] = (int)$data['id_serwisu'];

				// pobieramy aktualne dane produktu do aktualizacji
				$produkt = new Allegro_Produkt($aukcjaObj->data['id_produktu']);
				// pobiera wszystkie defaultowe dane aukcji - dla nowych aukcji
				$aukcjaObj = $produkt->updateAuction($aukcjaObj, $aukcjaObj->data['id_serwisu']);

				// dane z formularza
				foreach($data['fid'] as $fid=>$val) {
					$aukcjaObj->data['fid'][$fid] = $val;
				} 

				foreach($sprzedaz->getSzablonDostawyFids($aukcjaObj->data['id_szablon_dostawy']) as $fid=>$val) {
					$aukcjaObj->data['fid'][$fid] = $val;
				}


				$aukcja->data['data_cron'] = '';
				if (trim($data['data_cron']) != '' && trim($data['data_cron_time']) != '' && $data['czas_wyswietlenia'] == 1) {
					$aukcjaObj->data['data_cron'] = $data['data_cron'] . ' ' . $data['data_cron_time'] . ':00';
					$apiResult['item-id'] = 0;
					$apiResult['item-info'] = '';
				}else{
					// wystawiamy aukcje
					$apiResult = $sprzedaz->wystawNaAllegro($aukcjaObj);
				}

				
				// i zapisujemy do bazy
				$aukcjaObj->data['id_aukcji'] = $apiResult['item-id'];
				$sprzedaz->zapiszAukcje($aukcjaObj, $aukcjaObj->data['data_cron']);
			
				$aukcja['id_aukcji'] = $apiResult['item-id'];
				$aukcja['koszt'] = $apiResult['item-info'];
				$wystawione[] = $aukcja;
			} catch (Exception $e) {
				$aukcja['blad'] =  $e->getMessage();
				$niewystawione[] = $aukcja;
			} 	
		}
		
	
		$htmlRes = SmartyObj::getInstance();
		$htmlRes->assign('wystawione', $wystawione);
		$htmlRes->assign('niewystawione', $niewystawione);
		$objResponse->assign('editTabs', "innerHTML", $htmlRes->fetch('_ajax/allegro.sell_again_multi.result.tpl'));

		return $objResponse;
	}
	

	/**
	 * Pobiera pola obpwiazkowe i zapisuje do tablicy
	 * @global array $_gTables 
	 */
	public function getFidGeneralAll() {

		if (count($this->fidGeneral) == 0) {

			$sql = "SELECT * FROM " . MyConfig::getValue("__allegro_kategorie_parametry") . " WHERE id_kategorii = 0";
			$fids = ConnectDB::subQuery($sql);
			
			foreach ($fids as $val) {
				$this->fidGeneral[$val['form_id']] = $val;
				$wartosciKeys = explode('|', $val['form_opts_values']);
				$wartosciVals = explode('|', $val['form_desc']);
				$this->fidGeneral[$val['form_id']]['wartosci'] = array();
				foreach ($wartosciKeys as $wartosciKey => $wartosciKeyVal) {
					$this->fidGeneral[$val['form_id']]['wartosci'][$wartosciKeyVal] = $wartosciVals[$wartosciKey];
				}
			}
		}
		
		return $this->fidGeneral;
	}

	/**
	 * Pobiera podane pole glowne
	 * @param int $fid 
	 */
	public function getFidGeneral($fid) {
		//jezeli nie pobralismy z bazy to pobieramy
		if (count($this->fidGeneral) == 0) {
			$this->fidGeneral = $this->getFidGeneralAll();
		}

		// i zwracamy wlasciwe pole
		if ((int) $fid > 0 && array_key_exists($fid, $this->fidGeneral)) {
			return $this->fidGeneral[$fid];
		} else {
			return false;
		}
	}

	/**
	 * Pobiera wartości dla pola glownego
	 * @param int $fid
	 * @return type 
	 */
	public function getFidGeneralVal($fid) {
		$fidAll = $this->getFidGeneral($fid);
		if (isset($fidAll['wartosci'])) {
			return $fidAll['wartosci'];
		} else {
			return false;
		}
	}

	/**
	 * Lista pol zwiazannych z dostawa
	 */
	static public function getFidDostawa() {

		return array(
			array(
				'nazwa' => 'Paczka pocztowa ekonomiczna',
				'fid' => array(36, 136, 236)
			),
			array(
				'nazwa' => 'Paczka pocztowa priorytetowa',
				'fid' => array(38, 138, 238)
			),
			array(
				'nazwa' => 'List ekonomiczny',
				'fid' => array(37, 137, 237)
			),
			array(
				'nazwa' => 'List polecony ekonomiczny',
				'fid' => array(41, 141, 241)
			),
			array(
				'nazwa' => 'List polecony priorytetowy',
				'fid' => array(43, 143, 243)
			),
			array(
				'nazwa' => 'Przesyłka pobraniowa',
				'fid' => array(40, 140, 240)
			),
			array(
				'nazwa' => 'Przesyłka pobraniowa priorytetowa',
				'fid' => array(42, 142, 242)
			),
			array(
				'nazwa' => 'Przesyłka kurierska (PRZELEW)',
				'fid' => array(44, 144, 244)
			),
			array(
				'nazwa' => 'Przesyłka kurierska pobraniowa (POBRANIE, PŁACĘ Z ALLEGRO)',
				'fid' => array(45, 145, 245)
			),
			array(
				'nazwa' => 'Odbiór w punkcie - E-PRZESYŁKA',
				'fid' => array(52, 152, 252)
			),
			array(
				'nazwa' => 'Odbiór w punkcie po przedpłacie - E-PRZESYŁKA',
				'fid' => array(51, 151, 251)
			),
			array(
				'nazwa' => 'Odbiór w punkcie - PACZKA W RUCHU',
				'fid' => array(48, 148, 248)
			),
			array(
				'nazwa' => 'Odbiór w punkcie po przedpłacie - PACZKA W RUCHU',
				'fid' => array(46, 146, 246)
			),
			array(
				'nazwa' => 'Odbiór w punkcie - Paczkomaty 24/7',
				'fid' => array(49, 149, 249)
			),
			array(
				'nazwa' => 'Odbiór w punkcie po przedpłacie - Paczkomaty 24/7 (PACZKOMATY Z ALLEGRO)',
				'fid' => array(47, 147, 247)
			),
			array(
				'nazwa' => 'Odbiór w punkcie po przedpłacie - DHL SERVICE POINT',
				'fid' => array(50, 150, 250)
			)
		);
	}

	/**
	 * Sprawdza czy pole jest obowiązkowe
	 * @param int $fid
	 * @return type 
	 */
	public function checkFidOblig($fid) {
		$fidAll = $this->getFidGeneral($fid);
		if (isset($fidAll['form_opt']) && $fidAll['form_opt'] == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Pobiera formaty sprzedazy
	 * @return array 
	 */
	public function getFormatSprzedazySelect() {
		return array(
			1 => 'Kup teraz',
			2 => 'Aukcja',
			3 => 'Aukcja sklepowa'
		);
	}
	
	/**
	 * Pobiera id allegro ostatniej aukcji na bazie id produktu 
	 * @global array $_gTables
	 * @param array $idProdukty
	 * @return array tablica array(idProdukt=>idAllegro)
	 */
	static public function getAukcjeByIdProduktow(array $idProdukty = array()) {
		
		if(count($idProdukty) <= 0) {
			throw new InvalidArgumentException('Podaj id produktow');
		}
		
		$sql = "SELECT a.*, p.nazwa
				FROM " . MyConfig::getValue("__allegro") . " a
				LEFT JOIN " . MyConfig::getValue("__produkty") . " p ON p.id = a.id_produktu
				WHERE a.id IN (SELECT max(id) 
					FROM " . MyConfig::getValue("__allegro") . "
					WHERE id_produktu = " . implode(" OR id_produktu = ", array_map('intval', $idProdukty)) . " 
						GROUP BY id_produktu)";
		return ConnectDB::subQuery($sql);	
	}
	
	
	static public function getIdAukcjaByIdZamowienie($idZamowienie) {

		if($idZamowienie <= 0) {
			throw new InvalidArgumentException('Podaj id zamowienia');
		}
		
		$sql = "SELECT a.id FROM " . MyConfig::getValue("__allegro_sprzedane") . " s 
					    LEFT JOIN " . MyConfig::getValue("__allegro") . " a ON a.id_aukcji = s.id_aukcji
					    WHERE s.id_zamowienia = " . $idZamowienie;
		
		return ConnectDB::subQuery($sql, "one");
	}
	
	
	public function zakonczAukcjeInProdukt($idAllegro) {
		
		$idAllegro = intval($idAllegro);
		if($idAllegro <= 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$aData = array(
			'aukcja_zakonczona' => 1
		);

		ConnectDB::subAutoExec(MyConfig::getValue("__produkty"), $aData, 'UPDATE', 'id_allegro=' . $idAllegro);
	}
	
	public function setValue($record, $value) {
		
		try {
			$sql = "UPDATE " . MyConfig::getValue("__allegro") . " SET ".$record." = '".$value."' WHERE id = ".$this->id;
			ConnectDB::subExec($sql);
			
		} catch (Exception $e) {
			return array('error' => $e->getMessage());
		}
		return true;
		
	}
	
	public function getIdAllegroByIdAukcji(array $ids = array()) {

		if (!is_array($ids)) { $ids = (array) $ids; }
		if (count($ids) == 0) {
			throw new InvalidArgumentException('Podaj id allegro');
		}

		$sql = "SELECT id FROM " . MyConfig::getValue("__allegro") . " WHERE id_aukcji IN (" . implode(",", $ids) . ")";
		return ConnectDB::subQuery($sql, "col");
	}

}

?>