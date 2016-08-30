<?php
class AdminMain {
	
	public $atr;
	public $table;
	public $lang_table;
	public $pow_table = array();
	public $id_form;
	public $sort;
	public $sort_by;
	public $desc;
	public $button;
	public $idColumn;
	public $visibleColumn;
	public $keyColumn;
	public $atr_main;
	
	public $aWyniki = array();
	
	public function __construct($atr = '') {
		$this -> atr = $atr;
		
		if($this -> atr == '')
			$this -> atr = $this -> getMainForm();
			
		//dopisać metode pobierania id formularza z cms_menu a potem pobierania tabeli wg id_formularza :)
		$sql = "SELECT cf.nazwa_tabeli, cf.lang_table, cf.id_formularza, cf.sort, cf.tresc_przycisku, cf.sort_by, cf.desc_p, pow_table, pow_name, pow_value FROM ".MyConfig::getValue("dbPrefix")."cms_menu cm LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz cf ON cf.id_formularza = cm.id_formularz WHERE nazwa_url = '".$this -> atr."' LIMIT 1";

		try {
			$aWyniki = array();
			$aWyniki = ConnectDB::subQuery($sql);

			$this -> table 	 	= $aWyniki[0]['nazwa_tabeli'];
			$this -> lang_table	= $aWyniki[0]['lang_table'];
			$this -> id_form 	= $aWyniki[0]['id_formularza'];
			$this -> sort 		= $aWyniki[0]['sort'];
			$this -> button 	= $aWyniki[0]['tresc_przycisku'];
			$this -> sort_by 	= $aWyniki[0]['sort_by'];
			$this -> desc	 	= $aWyniki[0]['desc_p'];
			$this -> pow_table['table'] = $aWyniki[0]['pow_table'];
			$this -> pow_table['name'] = $aWyniki[0]['pow_name'];
			$this -> pow_table['value'] = $aWyniki[0]['pow_value'];

			//sprawdzamy kolumne z primary key
			$this -> idColumn = $this -> getIdColumn();
			//sprawdzamy kolumne odpowiadającą za publikacje (widoczny/niewidoczny) - kolumna taka ma ustawione w kacjach 'visible'
			$this -> visibleColumn = $this -> getVisibleColumn();
			//sprawdzamy kolumne odpowiadającą za klucz obcy, przyda sie do sprawdzenia powiązań np. między newsem a zdjęciemi
			$this -> keyColumn = $this -> getKeyColumn();
			//sprawdzamy czy istnieje menu parrent
			$this -> showParrent();

		} catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatchPanel"));
		}
	}
	
	public function getMainForm() {
		$sql = "SELECT nazwa_url FROM ".MyConfig::getValue("dbPrefix")."cms_formularz LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_menu ON id_formularza = id_formularz WHERE main_form = 1 LIMIT 1";

		try {
			$aWynik = array();
			$aWynik = ConnectDB::subQuery($sql);

		} catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatchPanel"));
		}
		return $aWynik[0]['nazwa_url'];
	}
	
	public function getIdColumn($table = '') {
	    if($table == '') $table = $this -> table;

		$sql = "SHOW COLUMNS FROM ".$table;
		
		try {
			$aWynik = array();
			$aWynik = ConnectDB::subQuery($sql);
			foreach($aWynik as $row) {
				if($row['Key'] == "PRI" && $row['Extra'] = "auto_increment")
					return $row['Field'];
				}

		} catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatchPanel"));
		}
		
	}

	public function listCount($id_pow = 0) {

		if($id_pow)
			$where = " WHERE ".$this -> keyColumn." = ".$id_pow;
		else
			$where = "";

		$sql = "SELECT * FROM ".$this -> table.$where;

		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);

			return count($aResult);

		} catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatchPanel"));
		}

	}

	public function showList($id_pow = 0, $number = 15, $start = 0) {

		//jeśli sortujemy to nie wyświetlamy stronicowania
		if($this -> sort )
			$number = 9999;
		//dopisanie pobierania kolum ustawionych jako wyświetlane w nagłówkach listy
		if($id_pow)
			$where = ' AND '.$this -> keyColumn." = ".$id_pow;
		else
			$where = "";

		if(!$start)
			$start = 0;
			
		//sprawdzamy czy sortujemy liste wynikow
		if($this -> sort_by != '') {
			if($this -> desc)
				$sortuj = " ORDER BY ".$this -> sort_by." DESC";
			else
				$sortuj = " ORDER BY ".$this -> sort_by;
		}
		else
			$sortuj = "";
		if($this -> lang_table)
			$sql = "SELECT * FROM ".$this -> table." LEFT JOIN ".$this -> lang_table." ON (".$this -> table.".".$this -> idColumn." = ".$this -> lang_table.".".$this -> idColumn.") WHERE lang_prefix = 'pl' ".$where." GROUP BY ".$this -> table.".".$this -> idColumn." ".$sortuj." LIMIT ".$start.", ".$number;
		else {
			if($id_pow)
				$where = 'WHERE '.$this -> keyColumn." = ".$id_pow;
			else
				$where = "";
			$sql = "SELECT * FROM ".$this -> table." ".$where."".$sortuj." LIMIT ".$start.", ".$number;
		}
	
		try {
			$aResult = array(); 
			$aResult = ConnectDB::subQuery($sql);

			$this -> aWyniki = $aResult;
			return true;

		} catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatchPanel"));
		}

	}

	public function searchModel($model) {

		//jeśli sortujemy to nie wyświetlamy stronicowania
		if($this -> sort )
			$number = 9999;
		//dopisanie pobierania kolum ustawionych jako wyświetlane w nagłówkach listy
		if($id_pow)
			$where = ' AND '.$this -> keyColumn." = ".$id_pow;
		else
			$where = "";

		if(!$start)
			$start = 0;

		//sprawdzamy czy sortujemy liste wynikow
		if($this -> sort_by != '') {
			if($this -> desc)
				$sortuj = " ORDER BY ".$this -> sort_by." DESC";
			else
				$sortuj = " ORDER BY ".$this -> sort_by;
		}
		else
			$sortuj = "";
		if($this -> lang_table) {
                    
			$sql = "SELECT * FROM ".$this -> table." LEFT JOIN ".$this -> lang_table." ON (".$this -> table.".".$this -> idColumn." = ".$this -> lang_table.".".$this -> idColumn.") LEFT JOIN mgc_car_producer ON ( mgc_car.producer_id = mgc_car_producer.producer_id ) 
WHERE producer_name LIKE '%".$model."%' AND lang_prefix = 'pl' ".$where." GROUP BY ".$this -> table.".".$this -> idColumn." ".$sortuj." LIMIT 0, 999";
                     
                    //$sql = "SELECT * FROM mgc_car LEFT JOIN mgc_car_producer ON (mgc_car.car_id = mgc_car_producer.producer_id) WHERE producer_name like '%".$model."%' GROUP BY mgc_car.car_id ORDER BY mgc_car.car_id DESC LIMIT 0, 999";
                    //echo $sql; exit;
                }
		else {
			if($id_pow)
				$where = 'WHERE '.$this -> keyColumn." = ".$id_pow;
			else
				$where = "";
			$sql = "SELECT * FROM ".$this -> table." ".$where."".$sortuj." LIMIT ".$start.", ".$number;
		}

		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);

			$this -> aWyniki = $aResult;
			return true;

		} catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatchPanel"));
		}

	}
	
	public function showParrent() {
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_menu WHERE nazwa_url = '".$this -> atr."'";
	
		$aResult = array();
		$aResult = ConnectDB::subQuery($sql);

		if($aResult[0]['id_rodzina'] != 0) {
			$sql = "SELECT nazwa_url FROM ".MyConfig::getValue("dbPrefix")."cms_menu WHERE id_menu = ".$aResult[0]['id_rodzina'];
			$aResult2 = array();
			$aResult2 = ConnectDB::subQuery($sql);
			$this -> atr_main = $aResult2[0]['nazwa_url'];
		}
		else 
			$this -> atr_main = $this -> atr;	
	}
	
	public function showPow($aWyniki) {

		//$this-> smarty-> assign('idColumn', 		$this -> idColumn) ;
		//$this-> smarty-> assign('aColumns', 		$this -> getColumnList(0)) ;
		//$this-> smarty-> assign('menu_url', 		$this -> atr) ;
		//$this-> smarty-> assign('id_form', 			$this -> id_form) ;
		//$this-> smarty-> assign('aWyniki', 			$aWyniki) ;
		//$this-> smarty-> assign('wwwPatch', 		MyConfig::getValue("wwwPatch")) ;
		//$this-> smarty-> assign('wwwPatchPanel', 	MyConfig::getValue("wwwPatchPanel")) ;
		//$this-> smarty-> assign('serverPatch', 		MyConfig::getValue("serverPatch") );
		//$finalContent = $this->smarty -> fetch("powSite.tpl");
		//return $finalContent;
	}
	
	public function getDodActions() {
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_dod_akcje LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz ON id_formularza = pow_form LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_menu ON id_formularz = pow_form WHERE id_form = ".$this -> id_form." GROUP BY nazwa_tabeli";
		$aResult = array();
		$aResult = ConnectDB::subQuery($sql);
		return $aResult;
	}
	
	public function getPowTable() {

		$sql = "SELECT nazwa_tabeli FROM ".MyConfig::getValue("dbPrefix")."cms_formularz WHERE id_form = '".$this -> pow_table['table']."'";
		$aWyniki = array();
		$aWyniki = ConnectDB::subQuery($sql);

		$visible_column = $this->getVisibleColumn($aWyniki[0]['nazwa_tabeli']);
		if($visible_column != "")
			$where = " WHERE ".$visible_column." = 1";

		$sql = "SELECT ".$this -> pow_table['value'].", ".$this->pow_table['name']." FROM ".$this -> pow_table['table'].$where." ORDER BY ".$this -> pow_table['value']." ASC";
		
		$aResult = array();
		$aResult = ConnectDB::subQuery($sql);
		return $aResult;
	}
	
	public function getColumnList($jako_kolumna = 1, $table = 0) {

		if(!$table) {
		    $where_form = 'fp.id_formularza = '.$this -> id_form.' ';
		    $active_where= "AND fp.active = 1";
		}
		else {
		    $left_join  = 'LEFT JOIN '.MyConfig::getValue("dbPrefix").'cms_formularz f USING(id_formularza)';
		    $where_form = 'f.nazwa_tabeli = "'.$table.'" ';
		}

		if($jako_kolumna)
			$where_kol = " AND ps.column_show = 1";
		else
			$where_kol = "";
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola fp ".$left_join." LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz ps ON ps.id_pola = fp.id_pola WHERE ".$where_form.$where_kol." ".$active_where." ORDER BY fp.id_pola";
		
		$result = array();
		$result = ConnectDB::subQuery($sql);

		if(is_array($this -> aWyniki) && count($this -> aWyniki) > 0) {
			for($j=0;$j<count($this -> aWyniki);$j++) {
				$k = 0;
				for($i=0;$i<count($result);$i++) {
					if(is_array($result) && count($result) > 0) {
						if($result[$i]['powiazana_tabela'] != '') {
							if(strstr($result[$i]['powiazana_tabela'], "_lang")) {
								$sql = "SELECT ".$result[$i]['pow_name']." FROM ".$result[$i]['powiazana_tabela']." WHERE ".$result[$i]['pow_value']." = ".$this -> aWyniki[$j][$result[$i]['column_s']]." AND lang_prefix = 'pl' LIMIT 1";
							} else {
								if($result[$i]['powiazana_tabela'] == 'autosalon_car_producer') {
									$where = " AND type = 'CAR'";
								}
								$sql = "SELECT ".$result[$i]['pow_name']." FROM ".$result[$i]['powiazana_tabela']." WHERE ".$result[$i]['pow_value']." = ".$this -> aWyniki[$j][$result[$i]['column_s']]." ".$where." LIMIT 1";
								$where = '';
								
							}
							
							$result2 = array();
							$result2 = ConnectDB::subQuery($sql);
							if(is_array($result2) && count($result2) > 0) {
								$this -> aWyniki[$j]['powColumn_rec'][$k] = $result2[0][$result[$i]['pow_name']];
							} else {
								$this -> aWyniki[$j]['powColumn_rec'][$k] = '';
							}
							$k++;
						}
					}
				}
			}
		}
		return $result;
	}
	
	public function getVisibleColumn($id_form = 0) {

		if($id_form == 0)
			$id_form = $this->id_form;
		$sql = "SELECT column_s FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$id_form." AND akcje = 'visible' LIMIT 1";
		$aTab = array();
		$aTab = ConnectDB::subQuery($sql);
		if(is_array($aTab) && count($aTab) > 0)
			return $aTab[0]['column_s'];
		else
			return 0;
	}
	
	public function getKeyColumn() {
		$sql = "SELECT column_s FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$this -> id_form." AND klucz_zew = 1 LIMIT 1";
		$aTab = array();
		$aTab = ConnectDB::subQuery($sql);
		if(is_array($aTab) && count($aTab) > 0)
			return $aTab[0]['column_s'];
		else
			return 0;
	}
	
	public function getSortColumn() {
		$sql = "SELECT column_s FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$this -> id_form." AND akcje = 'sort_colum' LIMIT 1";
		$aTab = array();
		$aTab = ConnectDB::subQuery($sql);
		if(is_array($aTab) && count($aTab) > 0)
			return $aTab[0]['column_s'];
		else
			return 0;
	}
	
	public function changeAjax($id) {
		if($id) {
			$pdo = new ConnectDB() ;
			$sql = "SELECT ".$this->visibleColumn." FROM ".$this->table." WHERE ".$this->idColumn." = ".$id;
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			if($aResult[0][$this->visibleColumn] == 1) {
				$sql = "UPDATE ".$this->table." SET ".$this->visibleColumn." = 0 WHERE ".$this->idColumn." = ".$id;
				ConnectDB::subExec($sql);
				echo MyConfig::getValue("wwwPatch").'images/admin/icons/main_off.png';
			}
			else {
				$sql = "UPDATE ".$this->table." SET ".$this->visibleColumn." = 1 WHERE ".$this->idColumn." = ".$id;
				ConnectDB::subExec($sql);
				echo MyConfig::getValue("wwwPatch").'images/admin/icons/main_on.png';
			}
		}
	}
	
	public function sortAjax($order) {
		if($order) {
			$aOrder = explode(",",$order);
			$i = 1;
			
			$pdo = new ConnectDB() ;
			foreach($aOrder as $row) {
				$sql = "UPDATE ".$this -> table." SET ".$this -> getSortColumn()." = ".$i." WHERE ".$this -> idColumn." = ".$row;
				ConnectDB::subExec($sql);
				$i++;
			}
		}
	}
	
	public function deleteAjax($id) {
		if($id) {
			$sql = "SELECT * FROM ".$this -> table." WHERE ".$this -> idColumn." = ".$id;
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
			$sql = "DELETE FROM ".$this -> table." WHERE ".$this -> idColumn." = ".$id;
			$wynik = ConnectDB::subExec($sql);

			if($this -> lang_table) {
				$sql = "DELETE FROM ".$this -> lang_table." WHERE ".$this -> idColumn." = ".$id;
				ConnectDB::subExec($sql);
			}
			
			$aColumns = $this -> getColumnList(0);
			foreach($aColumns as $row) {
				if($row['typ_pola'] == 'field') {
					@unlink($row['file_miejsce'].$id.'.'.$aResult[0][$row['column_s']]);
					@unlink($row['file_miejsce'].$id.'_thumb'.'.'.$aResult[0][$row['column_s']]);
					@unlink($row['file_miejsce'].'thumb/'.$id.'_main'.'.'.$aResult[0][$row['column_s']]);
					@unlink($row['file_miejsce'].'thumb/'.$id.'_title'.'.'.$aResult[0][$row['column_s']]);
					@unlink($row['file_miejsce'].'thumb/'.$id.'_article'.'.'.$aResult[0][$row['column_s']]);
				}
			}

			if($wynik)
				return true;
			else
				return false;	
		}
	}

	public function getMultiRelatedData($id) {

	    $sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz LEFT OUTER JOIN ".MyConfig::getValue("dbPrefix")."cms_menu ON(id_formularza = id_formularz) WHERE parent_form = ".$this -> id_form;
	    
	    try {
		$aResult = array();
		$aResult = ConnectDB::subQuery($sql);
		if(is_array($aResult) && count($aResult) > 0) {
		    $i = 0;
		    foreach($aResult as $row) {
			if(!strstr($row['pow_table'], '_lang')) {

			    if($row['pow_table'])
				$aColumns = $this -> getColumnList(0, $row['pow_table']);
			    else
				$aColumns = $this -> getColumnList(0, $row['nazwa_tabeli']);
			    if(is_array($aColumns) && count($aColumns) > 0) {
					foreach($aColumns as $col) {
						$aResult[$i]['info']['lang_table']	= $col['lang_table'];
						$aResult[$i]['info']['title']	= $col['nazwa_formularza'];
						$aResult[$i]['info']['main_column']	= $col['main_column'];
						$aResult[$i]['info']['main_column_id']	= $col['main_column_id'];
						$aResult[$i]['info']['main_pow_type']	= $col['main_pow_type'];
						$aResult[$i]['info'][$col['column_s']] = $col;
						$aResult[$i]['info']['id_column']	= $this -> getIdColumn($row['nazwa_tabeli']);
					}
					
					$sort = '';
					if($aResult[$i]['info'][$aColumns[0]['column_s']]['sort_by']) {
					$sort = " ORDER BY ".$aResult[$i]['info'][$aColumns[0]['column_s']]['sort_by'];
					if($aResult[$i]['info'][$aColumns[0]['column_s']]['desc_p']) 
						$sort .= " DESC";
					}
			    }
			    if($row['pow_table']) {
				$idColumnRelated = $this -> getIdColumn($row['pow_table']);

				if($aResult[$i]['info']['lang_table'] != '')
				    $sql = "SELECT * FROM ".$row['nazwa_tabeli']." LEFT JOIN ".$row['pow_table']." USING(".$idColumnRelated.") LEFT JOIN ".$aResult[$i]['info']['lang_table']." USING(".$idColumnRelated.") WHERE ".$this -> idColumn." = ".$id.$sort;
				else
				    $sql = "SELECT * FROM ".$row['nazwa_tabeli']." LEFT JOIN ".$row['pow_table']." USING(".$idColumnRelated.") WHERE ".$this -> idColumn." = ".$id.$sort;
				
			    } else {
				$idColumnRelated = $this -> getIdColumn($row['nazwa_tabeli']);

				if($aResult[$i]['info']['lang_table'] != '')
				    $sql = "SELECT * FROM ".$row['nazwa_tabeli']." LEFT JOIN ".$aResult[$i]['info']['lang_table']." USING(".$idColumnRelated.") WHERE ".$this -> idColumn." = ".$id.$sort;
				else
				    $sql = "SELECT * FROM ".$row['nazwa_tabeli']." WHERE ".$this -> idColumn." = ".$id.$sort;
				
			    } 
			    $aResult2 = array();
				
			    $aResult2 = ConnectDB::subQuery($sql);
			    $l = -1;
			    $temp = 0;
			    if(is_array($aResult2) && count($aResult2) > 0) {
				foreach($aResult2 as $row2) {
				    if($row2[$idColumnRelated] != $temp) $l++;
				    if($aResult[$i]['info']['lang_table'] != '')
					$aResult[$i]['data'][$l][$row2['lang_prefix']] = $row2;
				    else
					$aResult[$i]['data'][$l] = $row2;
				    $temp = $row2[$idColumnRelated];
				    
				}
			    }
			    

			    if($row['pow_table']) {
				if($aResult[$i]['info']['lang_table'] != '')
				    $sql = "SELECT * FROM ".$row['pow_table']." LEFT JOIN ".$aResult[$i]['info']['lang_table']." USING(".$idColumnRelated.")".$sort;
				else
				    $sql = "SELECT * FROM ".$row['pow_table'].$sort;
			    } else {
				if($aResult[$i]['info']['lang_table'] != '')
				    $sql = "SELECT * FROM ".$row['nazwa_tabeli']." LEFT JOIN ".$aResult[$i]['info']['lang_table']." USING(".$idColumnRelated.")".$sort;
				else
				    $sql = "SELECT * FROM ".$row['nazwa_tabeli'].$sort;
			    }
			    $sort = false;
			    $aResult3 = array();
			    $aResult3 = ConnectDB::subQuery($sql);
			    $h = 0;
			    if(is_array($aResult3) && count($aResult3) > 0) {
				foreach($aResult3 as $row3) {
				    if($row3[$idColumnRelated] != $temp) $h++;
				    if($aResult[$i]['info']['lang_table'] != '')
					$aResult[$i]['values'][$h][$row3['lang_prefix']] = $row3;
				    else
					$aResult[$i]['values'][$h] = $row3;
				    $temp = $row3[$idColumnRelated];

				}
			    }

			} else unset($aResult[$i]);
			$i++;
			$strTab = '';
			$aTname = null;
		    }
		}

	    } catch(PDOException $e) {
		//Log::SLog($e->getTraceAsString());
		echo $e->getTraceAsString();
	    }

	    return $aResult;
	}
}
?>