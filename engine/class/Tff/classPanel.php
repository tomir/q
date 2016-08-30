<?php
class Panel {
	
	protected $wwwPatch;
	protected $serverPatch;

	public $columnLangList;
	public $columnList;
	public $contentList;
	public $contentLangList;
	
	public function __construct(){
		
		$this -> wwwPatch = MyConfig::getValue("wwwPatch");
		$this -> serverPatch = MyConfig::getValue("serverPatch");
	}
	
	public function getColumnList($action) {
		
		$sql = "SHOW COLUMNS FROM ".$action;
		$columnTable = array();
		$columnTable = ConnectDB::subQuery($sql);
		return $columnTable;
	}
	
	public function getTableCreator($action = '', $action2 = '') {
		
		$sql = "SHOW TABLES IN ".MyConfig::getValue("dbDatabase");
		$contentTable = array();
		$this -> contentList = ConnectDB::subQuery($sql);

		$sql = "SHOW TABLES IN ".MyConfig::getValue("dbDatabase")." LIKE '%_lang'";
		$this -> contentLangList = ConnectDB::subQuery($sql);

		if($action != '') {
			$this -> columnList = $this -> getColumnList($action);
		}

		if($action2 != '') {
			$this -> columnLangList = $this -> getColumnList($action2);
		}

		return true;
	}
	
	public function saveForm($action, $action2 = '') {
		
		if($action != '') {
			
			$field_name 	= '';
			$field_type 	= '';
			$field_required = 0;
			$field_relation = '';
			$relation_value = '';
			$relation_name 	= '';
			$s_action 		= '';
			$field_limit 	= 0;
			$field_style 	= '';
						
			if($_POST['id_formularza'] != 0 || $_POST['id_formularza'] != '') {
				$sql = "UPDATE ".MyConfig::getValue("dbPrefix")."cms_formularz SET nazwa_formularza = :nazwa_formularza, nazwa_tabeli = :nazwa_tabeli, lang_table = :lang_table, data_modyfikacji = now() WHERE id_formularza=".$_POST['id_formularza'] ;
			}
			else {
				$sql = "INSERT INTO ".MyConfig::getValue("dbPrefix")."cms_formularz (nazwa_formularza, nazwa_tabeli, lang_table, active, data_stworzenia, data_modyfikacji) VALUES (:nazwa_formularza, :nazwa_tabeli, :lang_table, 1, now(), now())" ;
			}
			
			try {
				$pdo = new ConnectDB();
				$pdo -> exec("SET names utf8");
				$wynik = $pdo -> prepare($sql) ;
				$wynik -> bindValue (':nazwa_formularza'  		  , $_POST['nazwa_formularza'] 	, PDO::PARAM_STR) ;
				$wynik -> bindValue (':nazwa_tabeli' 	          , $action	    				, PDO::PARAM_STR) ;
				$wynik -> bindValue (':lang_table'	 	          , $action2	    			, PDO::PARAM_STR) ;
	
				$liczbaZmian = $wynik -> execute();
			}
			catch (PDOException $e) {
				Log::SLog($e->getMessage().'\n'.$e->getTraceAsString());
			}
			
			if($liczbaZmian > 0) {
				if($_POST['id_formularza'] == 0)
					$id_formularza = $pdo -> lastInsertId();
				else
					$id_formularza = $_POST['id_formularza'];
			
				$columnTable = $this -> getColumnList($action);
				
				foreach($columnTable as $row) {

					if($row['Extra'] != 'auto_increment') {
						
						$column_name 	= $_POST["column_".$row['Field']];
						$field_name 	= $_POST[$row['Field']];
						$field_type 	= $_POST['pole_'.$row['Field']];
						$field_required = $_POST['wymagane_'.$row['Field']];
						if(!$field_required) {
							$field_required = '0';
						}
						$field_wybor = $_POST['wybor_'.$row['Field']];
						if(!$field_wybor) {
							$field_wybor = '0';
						}
						$field_relation = $_POST['powiazania_'.$row['Field']];
						if($field_relation != '') {
							$relation_value = $_POST['pow_value_'.$row['Field']];
							for($i=0;$i<$_POST['pow_ilosc_kolumn_'.$row['Field']];$i++) {
								if($_POST['pow_display_'.$row['Field'].'_'.$i] != '') {
									$relation_name .= $_POST['pow_display_'.$row['Field'].'_'.$i].', ';
								}
							}
							$relation_name = substr($relation_name,0,-2);
						}
						$aField_actions = $_POST['akcje_'.$row['Field']];
						if(count($aField_actions > 0) && $aField_actions != '') {
							foreach($aField_actions as $action) {
								$s_action .= $action.",";
							}
							$s_action = substr($s_action,0,-1);
						}
					
					
						if(isset($_POST[$row['Field'].'_id']) || $_POST[$row['Field'].'_id'] != 0 || $_POST[$row['Field'].'_id'] != '') {
							$sql2 = "UPDATE ".MyConfig::getValue("dbPrefix")."cms_formularz_pola SET active = :field_wybor, id_formularza = :id_formularza, column_s = :column, nazwa_pola = :nazwa_pola, typ_pola = :typ_pola, wymagane = :wymagane, powiazana_tabela = :powiazana_tabela, pow_value = :pow_value, pow_name = :pow_name, akcje = :akcje WHERE id_pola =".$_POST[$row['Field'].'_id'];
						}
						else {
							$sql2 = "INSERT INTO ".MyConfig::getValue("dbPrefix")."cms_formularz_pola (id_formularza, active, column_s, nazwa_pola, typ_pola, wymagane, powiazana_tabela, pow_value, pow_name, akcje)
									VALUES (:id_formularza, :field_wybor, :column, :nazwa_pola, :typ_pola, :wymagane, :powiazana_tabela, :pow_value, :pow_name, :akcje)";
						}
						
						try {
							$wynik2 = $pdo -> prepare($sql2) ;
							$wynik2 -> bindValue (':id_formularza'  	, $id_formularza 		, PDO::PARAM_INT) ;
							$wynik2 -> bindValue (':field_wybor' 	   	, $field_wybor    		, PDO::PARAM_INT) ;
							$wynik2 -> bindValue (':column' 	   		, $column_name    		, PDO::PARAM_STR) ;
							$wynik2 -> bindValue (':nazwa_pola' 	    , $field_name	    	, PDO::PARAM_STR) ;
							$wynik2 -> bindValue (':typ_pola' 	        , $field_type	    	, PDO::PARAM_STR) ;
							$wynik2 -> bindValue (':wymagane' 	        , $field_required	    , PDO::PARAM_INT) ;
							$wynik2 -> bindValue (':powiazana_tabela' 	, $field_relation	    , PDO::PARAM_STR) ;
							$wynik2 -> bindValue (':pow_value' 	        , $relation_value	    , PDO::PARAM_STR) ;
							$wynik2 -> bindValue (':pow_name' 	        , $relation_name	    , PDO::PARAM_STR) ;
							$wynik2 -> bindValue (':akcje' 	          	, $s_action	    		, PDO::PARAM_STR) ;
		
							$liczbaZmian2 = $wynik2 -> execute();
						}
						catch (PDOException $e) {
							Log::SLog($e->getMessage().' '.$e->getTraceAsString());
						}

						$wynik2 		= null;
						$field_name 	= '';
						$field_type 	= '';
						$field_required = 0;
						$field_relation = '';
						$relation_value = '';
						$relation_name 	= '';
						$s_action 		= '';
						$field_limit 	= 0;
						$field_style 	= '';
						
					}
				}
				//teraz dodajemy teksty z langu
				if($action2 != '') {
				    $columnLangTable = $this -> getColumnList($action2);
				    if(is_array($columnLangTable)) {
					foreach($columnLangTable as $row) {

						if($row['Extra'] != 'auto_increment' && (stristr($row['Type'], 'varchar') || stristr($row['Type'], 'text'))) {

							$column_name 	= $_POST["lang_column_".$row['Field']];
							$field_name 	= $_POST["lang_".$row['Field']];
							$field_type 	= $_POST['lang_pole_'.$row['Field']];
							$field_required = $_POST['lang_wymagane_'.$row['Field']];
							if(!$field_required) {
								$field_required = '0';
							}
							$field_wybor = $_POST['lang_wybor_'.$row['Field']];
							if(!$field_wybor) {
								$field_wybor = '0';
							}

							$aField_actions = $_POST['lang_akcje_'.$row['Field']];
							if(count($aField_actions > 0) && $aField_actions != '') {
								foreach($aField_actions as $action) {
									$s_action .= $action.",";
								}
								$s_action = substr($s_action,0,-1);
							}

							if(isset($_POST[$row['Field'].'_id']) || $_POST[$row['Field'].'_id'] != 0 || $_POST[$row['Field'].'_id'] != '') {
								$sql2 = "UPDATE ".MyConfig::getValue("dbPrefix")."cms_formularz_pola SET active = :field_wybor, id_formularza = :id_formularza, column_s = :column, nazwa_pola = :nazwa_pola, typ_pola = :typ_pola, wymagane = :wymagane, powiazana_tabela = :powiazana_tabela, pow_value = :pow_value, pow_name = :pow_name, akcje = :akcje, lang = :lang WHERE id_pola =".$_POST[$row['Field'].'_id'];
							}
							else {
								$sql2 = "INSERT INTO ".MyConfig::getValue("dbPrefix")."cms_formularz_pola (id_formularza, active, column_s, nazwa_pola, typ_pola, wymagane, powiazana_tabela, pow_value, pow_name, akcje, lang)
										VALUES (:id_formularza, :field_wybor, :column, :nazwa_pola, :typ_pola, :wymagane, :powiazana_tabela, :pow_value, :pow_name, :akcje, :lang)";
							}

							try {
								$wynik2 = $pdo -> prepare($sql2) ;
								$wynik2 -> bindValue (':id_formularza'  	, $id_formularza 		, PDO::PARAM_INT) ;
								$wynik2 -> bindValue (':field_wybor' 	   	, $field_wybor    		, PDO::PARAM_INT) ;
								$wynik2 -> bindValue (':column' 	   		, $column_name    		, PDO::PARAM_STR) ;
								$wynik2 -> bindValue (':nazwa_pola' 	    , $field_name	    	, PDO::PARAM_STR) ;
								$wynik2 -> bindValue (':typ_pola' 	        , $field_type	    	, PDO::PARAM_STR) ;
								$wynik2 -> bindValue (':wymagane' 	        , $field_required	    , PDO::PARAM_INT) ;
								$wynik2 -> bindValue (':powiazana_tabela' 	, $field_relation	    , PDO::PARAM_STR) ;
								$wynik2 -> bindValue (':pow_value' 	        , $relation_value	    , PDO::PARAM_STR) ;
								$wynik2 -> bindValue (':pow_name' 	        , $relation_name	    , PDO::PARAM_STR) ;
								$wynik2 -> bindValue (':akcje' 	          	, $s_action	    		, PDO::PARAM_STR) ;
								$wynik2 -> bindValue (':lang' 	          	, 1						, PDO::PARAM_INT) ;

								$liczbaZmian2 = $wynik2 -> execute();
							}
							catch (PDOException $e) {
								Log::SLog($e->getMessage().' '.$e->getTraceAsString());
							}
						}
					}
				    }
				}
				//dodajemy opcje dodatkowe
				$select_filtr 	= $_POST["select_filtr"];
				if($select_filtr != '') {
					$filtr_value = $_POST['pow_value_filtr'];
					for($i=0;$i<$_POST['pow_ilosc_kolumn_filtr'];$i++) {
						if($_POST['pow_display_filtr_'.$i] != '') {
							$filtr_name .= $_POST['pow_display_filtr_'.$i].', ';
						}
					}
					$relation_name = substr($relation_name,0,-2);
				}
				
				$sort 				= $_POST['sortuj'];
				$main_form			= $_POST['main_form'];
				$tresc_przycisku 	= $_POST['tresc_przycisku'];
				$sort_column	 	= $_POST['sort_column'];
				$sort_desc		 	= $_POST['sort_desc'];
				
				if(!$sort_column) 		$sort_column = '';
				if(!$sort_desc) 		$sort_desc = '0';
				if(!$tresc_przycisku) 	$tresc_przycisku = '';
				if(!$sort) 				$sort = '0';
				if(!$main_form) 		$main_form = '0';
				
				$sql1 = "UPDATE ".MyConfig::getValue("dbPrefix")."cms_formularz SET sort_by = '".$sort_column."', desc_p = ".$sort_desc.", tresc_przycisku = '".$tresc_przycisku."', sort = ".$sort.", pow_table = '".$select_filtr."', pow_value = '".$filtr_value."', pow_name = '".$filtr_name."', main_form = ".$main_form." WHERE id_formularza = ".$id_formularza;
				
				ConnectDB::subExec($sql1);
						
				if($_POST[0]['image'] != '') {
					for($i=0; $i<=$_POST['ilosc_akcji']; $i++) {
						$image 		= $_POST[$i]['image'];
						$form_id 	= $_POST[$i]['pow_form'];
						$link 		= $_POST[$i]['link'];
						$sql = "INSERT INTO ".MyConfig::getValue("dbPrefix")."cms_formularz_dod_akcje (id_form, image, pow_form, pow_link)
									VALUES (".$id_formularza.", '".$image."', ".$form_id.", '".$link."')";
						ConnectDB::subExec($sql);
						
						$image = '';
						$form_id = '';
						$link = '';
					}
				}
			}
		}
		if($liczbaZmian2 > 0)
			header("Location: ".MyConfig::getValue("tffPatch")."lista.html,2,add_success");
		else
			header("Location: ".MyConfig::getValue("tffPatch")."lista.html,1,add_error");
	}

	public function getFormDisplay() {
		
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz ORDER BY data_stworzenia DESC";
		$aForms = array();
		$aForms = ConnectDB::subQuery($sql);
		return $aForms;
	}
	
	public function getFieldDisplay($field_id) {

		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola fp LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz fps ON fp.id_pola = fps.id_pola WHERE fp.id_pola = ".$field_id;
		$field = array();
		$field = ConnectDB::subQuery($sql);
		
		$aAkcje = array();
		$akcje = explode(",",$field[0]['akcje']);
		foreach($akcje as $row) {
			$aAkcje[$row] = 1;
		}
                $field[0]['akcje'] = array();
                $field[0]['akcje'] = $aAkcje;
		
		$aRoz = array();
		$roz = explode("; ",$field[0]['file_rozszerzenia']);
		foreach($roz as $row2) {
			if($row2 != '') {
				$row2 = str_replace("*.","",$row2);
				$aRoz[$row2] = 1;
			}
		}

                $field[0]['file_rozszerzenia'] = array();
                $field[0]['file_rozszerzenia'] = $aRoz;

		return $field;
	}
	
	public function getFileRozsz() {
		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_file_rozsz ORDER BY nazwa";
		$tablicaRozszerzen = array();
		$tablicaRozszerzen = ConnectDB::subQuery($sql);
		
		return $tablicaRozszerzen;
	}
	
	public function deleteForm($id) {
		
		if($id > 0) {
			$sql = "DELETE FROM ".MyConfig::getValue("dbPrefix")."cms_formularz WHERE id_formularza = ".$id;
			ConnectDB::subExec($sql);
			
			$sql = "DELETE FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$id;
			ConnectDB::subExec($sql);
			
			if($wynik >0)
				header("Location: ".MyConfig::getValue("tffPatch")."lista.html,2,del_success");
			else
				header("Location: ".MyConfig::getValue("tffPatch")."lista.html,1,del_error");
		}
	}
	
	public function deleteField($id) {
		
		if($id > 0) {
			
			$sql = "DELETE FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_pola = ".$id;
			ConnectDB::subExec($sql);
			
			$sql = "DELETE FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz WHERE id_pola = ".$id;
			ConnectDB::subExec($sql);
			
			if($wynik >0)
				header("Location: ".MyConfig::getValue("tffPatch")."lista.html,2,del_success");
			else
				header("Location: ".MyConfig::getValue("tffPatch")."lista.html,1,del_error");
		}
	}
	
	public function saveField($id_field, $aPost) {

		$pole 			= $_POST['field_name'];
		$field_name 	= $_POST[$pole];
		$field_type 	= $_POST['pole_'.$pole];
		$field_required = $_POST['wymagane_'.$pole];
		if(!$field_required) {
			$field_required = '0';
		}
		//$field_relation = $_POST['powiazania_'.$pole];
		/*if($field_relation != '') {
			$relation_value = $_POST['pow_value_'.$row['Field']];
			for($i=0;$i<$_POST['pow_ilosc_kolumn_'.$row['Field']];$i++) {
				if($_POST['pow_display_'.$row['Field'].'_'.$i] != '') {
					$relation_name .= $_POST['pow_display_'.$row['Field'].'_'.$i].', ';
				}
			}
			$relation_name = substr($relation_name,0,-2);
		}*/
		
		$aField_actions = $_POST['akcje_'.$pole];
		if(count($aField_actions > 0) && $aField_actions != '') {
			foreach($aField_actions as $action) {
				$s_action .= $action.",";
			}
			$s_action = substr($s_action,0,-1);
		}
		$field_style = $_POST['styl_'.$pole];
		$field_iloscZnakow_min = $_POST['text_ilosc_min_'.$pole];
		$field_iloscZnakow_max = $_POST['text_ilosc_max_'.$pole];
		$field_dlugosc = $_POST['text_dlugosc_'.$pole];
		$aField_rozszerzenia = $_POST['rozszerzenia_'.$pole];
		if(count($aField_rozszerzenia > 0) && $aField_rozszerzenia != '') {
			foreach($aField_rozszerzenia as $rozsz) {
				$s_rozsz .= "*.".$rozsz."; ";
			}
			$s_rozsz = substr($s_rozsz,0,-2);
		}
		$field_sciezka = $_POST['sciezka_'.$pole];
		$field_image_main_roz = $_POST['image_main_roz_'.$pole];
		$field_image_scalex = $_POST['image_scalex_'.$pole];
		if(!$field_image_scalex)
			$field_image_scalex = 0;
		$field_image_scaley = $_POST['image_scaley_'.$pole];
		if(!$field_image_scaley)
			$field_image_scaley = 0;
		$field_thumb_scalex = $_POST['thumb_scalex_'.$pole];
		if(!$field_thumb_scalex)
			$field_thumb_scalex = 0;
		$field_thumb_scaley = $_POST['thumb_scaley_'.$pole];
		if(!$field_thumb_scaley)
			$field_thumb_scaley = 0;
		$field_textarea_x = $_POST['textarea_x_'.$pole];
		$field_textarea_y = $_POST['textarea_y_'.$pole];
		$field_tinymc = $_POST['textarea_tinymc_'.$pole];
		
		$field_kolumna = $_POST['kolumna_'.$pole];
		if(!$field_kolumna)
			$field_kolumna = 0;
		$field_wysrodkowana = $_POST['wysrodkowana_'.$pole];
		if(!$field_wysrodkowana)
			$field_wysrodkowana = 0;
		$field_active = $_POST['active_'.$pole];
		if(!$field_active)
			$field_active = 0;
		$field_klucz_zew = $_POST['klucz_zew_'.$pole];
		if(!$field_klucz_zew)
			$field_klucz_zew = 0;
		
		$sql = "UPDATE ".MyConfig::getValue("dbPrefix")."cms_formularz_pola SET klucz_zew = :klucz_zew, active = :active, nazwa_pola = :nazwa_pola, typ_pola = :typ_pola, wymagane = :wymagane, akcje = :akcje, styl = :styl WHERE id_pola = ".$id_field;
		try {
			$pdo	= new ConnectDB();
			$pdo -> exec("SET names utf8");
			$wynik	= $pdo -> prepare($sql) ;
			$wynik -> bindValue (':nazwa_pola' 	    	, $field_name	    	, PDO::PARAM_STR) ;
			$wynik -> bindValue (':typ_pola' 	        , $field_type	    	, PDO::PARAM_STR) ;
			$wynik -> bindValue (':wymagane' 	        , $field_required	    , PDO::PARAM_INT) ;
			$wynik -> bindValue (':akcje' 	          	, $s_action	    		, PDO::PARAM_STR) ;
			$wynik -> bindValue (':styl' 	          	, $field_style	    	, PDO::PARAM_STR) ;
			$wynik -> bindValue (':klucz_zew' 	        , $field_klucz_zew	    , PDO::PARAM_INT) ;
			$wynik -> bindValue (':active' 	          	, $field_active		    , PDO::PARAM_INT) ;

			$liczbaZmian = $wynik -> execute();
		}
		catch (PDOException $e) {
			Log::SLog($e->getMessage().' '.$e->getTraceAsString());
		}
		
		$sql = "SELECT id_pola FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz WHERE id_pola = ".$id_field;
		
		$aWyniki = array();
		$aWyniki = ConnectDB::subQuery($sql);

		if(count($aWyniki) > 0 && is_array($aWyniki))
			$sql = "UPDATE ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz SET file_rozszerzenia = :file_rozszerzenia , file_miejsce = :file_miejsce , text_ilosc_min = :text_ilosc_min , text_ilosc_max = :text_ilosc_max , image_scalex = :image_scalex , image_scaley = :image_scaley , thumb_scalex = :thumb_scalex , thumb_scaley = :thumb_scaley , textarea_x = :textarea_x , textarea_y = :textarea_y , textarea_tinymc = :textarea_tinymc , text_dlugosc = :text_dlugosc , image_main_roz = :image_main_roz, column_show = :kolumna, wysrodkowana = :wysrodkowana  WHERE id_pola = ".$id_field;
		else 
			$sql = "INSERT INTO ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz (file_rozszerzenia, file_miejsce, text_ilosc_min, text_ilosc_max, image_scalex, image_scaley, thumb_scalex, thumb_scaley, textarea_x, textarea_y, textarea_tinymc, text_dlugosc, image_main_roz, id_pola, column_show, wysrodkowana) VALUES (:file_rozszerzenia, :file_miejsce, :text_ilosc_min, :text_ilosc_max, :image_scalex, :image_scaley, :thumb_scalex, :thumb_scaley, :textarea_x, :textarea_y, :textarea_tinymc, :text_dlugosc, :image_main_roz, :id_pola, :kolumna, :wysrodkowana)";
		
		try {
			$pdo	= new ConnectDB();
			$pdo -> exec("SET names utf8");
			$wynik2	= $pdo -> prepare($sql) ;
			$wynik2 -> bindValue (':file_rozszerzenia' 	    , $s_rozsz	    			, PDO::PARAM_STR) ;
			$wynik2 -> bindValue (':file_miejsce' 	        , $field_sciezka	    	, PDO::PARAM_STR) ;
			$wynik2 -> bindValue (':text_ilosc_min' 	        , $field_iloscZnakow_min	, PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':text_ilosc_max' 	        , $field_iloscZnakow_max	, PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':image_scalex' 	        , $field_image_scalex	    , PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':image_scaley' 	        , $field_image_scaley	    , PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':thumb_scalex' 	        , $field_thumb_scalex	    , PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':thumb_scaley' 	        , $field_thumb_scaley	    , PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':textarea_x' 	          	, $field_textarea_x	    	, PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':textarea_y' 	          	, $field_textarea_y	    	, PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':textarea_tinymc' 	    , $field_tinymc	    		, PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':text_dlugosc' 	        , $field_dlugosc	    	, PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':image_main_roz' 	        , $field_image_main_roz	    , PDO::PARAM_STR) ;
			if(!is_array($aWyniki)) {
			    $wynik2 -> bindValue (':id_pola' 	        , $id_field	    			, PDO::PARAM_INT) ;
			}
			$wynik2 -> bindValue (':kolumna' 	   	 		, $field_kolumna    		, PDO::PARAM_INT) ;
			$wynik2 -> bindValue (':wysrodkowana' 	        , $field_wysrodkowana   	, PDO::PARAM_INT) ;

			$liczbaZmian2 = $wynik2 -> execute();
		}
		catch (PDOException $e) {
			Log::SLog($e->getMessage().' '.$e->getTraceAsString());
			print_r($e->getTrace());
		}
		
		if($liczbaZmian2 > 0)
			header("Location: ".MyConfig::getValue("tffPatch")."lista.html,2,add_success");
		else
			header("Location: ".MyConfig::getValue("tffPatch")."lista.html,1,add_error");
	}
}
?>