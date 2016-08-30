<?php

include("Asido/class.asido.php");

class Formularz {
	
	protected $wwwPatch;
	protected $serverPatch;
	public $aPola;
	
	public function __construct(){
		
		$this -> wwwPatch = MyConfig::getValue("wwwPatch");
		$this -> serverPatch = MyConfig::getValue("serverPatch");
	}
	public function getFormTable($formId) {
		$sql = "SELECT nazwa_tabeli FROM ".MyConfig::getValue("dbPrefix")."cms_formularz WHERE id_formularza = ".$formId;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);

		} catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatchPanel"));
		}

		return $aResult[0]['nazwa_tabeli'];
	}

	public function getAvalibleLanguages() {

		$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_langs";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);

		} catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatchPanel"));
		}

		return $aResult;
	}
	
	public function start($akcja, $formId=0, $akcja2='', $akcja4='') {

		$plik = false;
		$oMain 	= new AdminMain($akcja);
		if($formId) {
			
			if(isset($_POST['zapisz']) && $_POST['zapisz'] != '') {
				$error = false;
				$pola = "";
				$pola_bind = "";
				
				$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola fp LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz szcz ON szcz.id_pola = fp.id_pola WHERE fp.id_formularza = ".$formId." AND fp.lang IS NULL AND fp.akcje != 'visible' AND fp.active = 1";
				try {

					$tablicaWynikow = array();
					$tablicaWynikow = ConnectDB::subQuery($sql);
					$nazwaPliku = $_POST['nazwaPliku'];
					
					if(count($tablicaWynikow) > 0) {
						foreach($tablicaWynikow as $row) {
							if($row['wymagane']) { 
								if($row['typ_pola'] == 'file' && $row['ftp_host'] == '') {

 									$nazwaPliku = $_POST['nazwaPliku'];
									$plik = true;
									
									$infoPlik = array();
									$infoPlik['sciezka'] 	= $row['file_miejsce'];
									$infoPlik['imgx'] 		= $row['image_scalex'];
									$infoPlik['imgy'] 		= $row['image_scaley'];
									$infoPlik['thumbx'] 	= $row['thumb_scalex'];
									$infoPlik['thumby'] 	= $row['thumb_scaley'];
									$infoPlik['img_main'] 			= $row['img_main'];
									$infoPlik['img_main_thumb'] 	= $row['img_main_thumb'];
									$infoPlik['rozszerzenie'] = $row['image_main_roz'];
									$infoPlik['column'] = $row['column_s'];

								}
								else {
									if($_POST[$row['column_s']."_field"] == '') $error = true;
									$dane[$row['column_s']."_field"] = $_POST[$row['column_s']."_field"];
									if($akcja2 == 'edit') {
										$pola .= $row['column_s']." = :".$row['column_s'].", ";
									}
									else {
										$pola .= $row['column_s'].", ";
										$pola_bind .= ":".$row['column_s'].", ";
									}
								}
							}
							else {
								if($row['typ_pola'] == 'file' && $row['ftp_host'] == '') {
									$nazwaPliku = $_POST['nazwaPliku'];
									$plik = true;

									$infoPlik = array();
									$infoPlik['sciezka'] 	= $row['file_miejsce'];
									$infoPlik['imgx'] 		= $row['image_scalex'];
									$infoPlik['imgy'] 		= $row['image_scaley'];
									$infoPlik['thumbx'] 	= $row['thumb_scalex'];
									$infoPlik['thumby'] 	= $row['thumb_scaley'];
									$infoPlik['img_main'] 			= $row['img_main'];
									$infoPlik['img_main_thumb'] 	= $row['img_main_thumb'];
									$infoPlik['rozszerzenie'] = $row['image_main_roz'];
									$infoPlik['column'] = $row['column_s'];
								}
								else {
									$dane[$row['column_s']."_field"] = $_POST[$row['column_s']."_field"];
									if($akcja2 == 'edit') {
										$pola .= $row['column_s']." = :".$row['column_s'].", ";
									}
									else {
										$pola .= $row['column_s'].", ";
										$pola_bind .= ":".$row['column_s'].", ";
									}
								}
							}
						}
					}
				} catch(PDOException $e) {
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
					header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,get_error");
				} 
				if(!$error) {
					$pola 		= substr($pola,0,-2);
					$pola_bind 	= substr($pola_bind,0,-2);
					
					if($akcja2 == 'edit') {
						$sql = "UPDATE ".$oMain -> table." SET ".$pola." 
								WHERE ".$oMain -> idColumn." = ".$akcja4;
					}
					else {
						$sql = "INSERT INTO ".$oMain -> table." (".$pola.") 
								VALUES (".$pola_bind.")";
					}
				
					try {
						$pdo = new ConnectDB();
						$pdo -> exec("SET names utf8");
						$wynik = $pdo -> prepare($sql) ;
						foreach($tablicaWynikow as $row) {
							if($row['typ_pola'] != 'file' || ($row['typ_pola'] == 'file' && $row['ftp_host'] != '')) {
								if($row['typ_pola'] == 'password')
									$wynik -> bindValue (':'.$row['column_s'] 	    , sha1($dane[$row['column_s']."_field"])) ;
								else
									$wynik -> bindValue (':'.$row['column_s'] 	    , $dane[$row['column_s']."_field"]) ;
							}
						}
						$liczbaZmian = $wynik -> execute();
						$lastId = $pdo -> lastInsertId();

						//sprawdzamy jeszcze czy został do dodania pliczek
						if($plik && ($lastId || $liczbaZmian)) {
							
							if($lastId == 0)
								$lastId = $akcja4;
							$nazwaPliku = $_POST['nazwaPliku'];
							$roz = $this -> uploadPhoto($lastId, $nazwaPliku, $infoPlik['sciezka'], $infoPlik['rozszerzenie'], $infoPlik['imgx'], $infoPlik['imgy'], $infoPlik['img_main']);
							if($infoPlik['imgx'])
								$roz = $this -> uploadPhoto($lastId."_thumb", $nazwaPliku, $infoPlik['sciezka'], $infoPlik['rozszerzenie'], $infoPlik['thumbx'], $infoPlik['thumby'], $infoPlik['img_main_thumb']);
							$sql = "UPDATE ".$oMain -> table." SET ".$infoPlik['column']." = '".$roz."' WHERE ".$oMain -> idColumn." = ".$lastId;
							ConnectDB::subExec($sql);
						}
						
					}catch(PDOException $e) {
						Log::SLog($e->getMessage().' '.$e->getTraceAsString());
						header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,get_error");
					} //echo $akcja;

				} else {
					echo 'bykol';
				}
				$this -> clearPhoto($nazwaPliku, $infoPlik['sciezka']);

				//dodajemy wersje jezykowe
				if($lastId == 0)
					$lastId = $akcja4;

				$error = false;
				$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola fp LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz szcz ON szcz.id_pola = fp.id_pola WHERE fp.id_formularza = ".$formId." AND fp.lang = 1 AND fp.akcje != 'visible' AND fp.active = 1";

				try {

					$tablicaWynikow = array();
					$tablicaWynikow = ConnectDB::subQuery($sql);

					$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_langs";
					$aLang = array();
					$aLang = ConnectDB::subQuery($sql);

					if(count($tablicaWynikow) > 0) {
						if($oMain -> lang_table != '') {
							foreach($aLang as $row_l) {

								$aTemp = null;
								$sql = '';
								$sql = "SELECT * FROM ".$oMain -> lang_table." WHERE ".$oMain -> idColumn." = ".$lastId." AND lang_prefix = '".$row_l['prefix']."'";
								$aTemp = ConnectDB::subQuery($sql);

								$pola = '';
								$pola_bind = '';
								foreach($tablicaWynikow as $row) {

									$dane[$row['column_s']."_field"][$row_l['prefix']] = $_POST[$row['column_s']."_field"][$row_l['prefix']];
									if($akcja2 == 'edit' && is_array($aTemp) && count($aTemp) > 0) {
										$pola .= $row['column_s']." = :".$row['column_s'].", ";
									}
									else {
										$pola .= $row['column_s'].", ";
										$pola_bind .= ":".$row['column_s'].", ";
									}
								}

								if(!$error) {
									$pola 		= substr($pola,0,-2);
									$pola_bind 	= substr($pola_bind,0,-2);



									if($akcja2 == 'edit' && is_array($aTemp) && count($aTemp) > 0) {
										$sql = "UPDATE ".$oMain -> lang_table." SET ".$pola."
												WHERE ".$oMain -> idColumn." = ".$akcja4." AND lang_prefix = '".$row_l['prefix']."'";

									}
									else {
										$sql = "INSERT INTO ".$oMain -> lang_table." (".$pola.", lang_prefix, ".$oMain -> idColumn.")
												VALUES (".$pola_bind.", '".$row_l['prefix']."', $lastId)";

									}

									try {
										$pdo = new ConnectDB();
										$pdo -> exec("SET names utf8");
										$wynik = $pdo -> prepare($sql) ;
										foreach($tablicaWynikow as $row) {
											if($row['typ_pola'] != 'file' || ($row['typ_pola'] == 'file' && $row['ftp_host'] != '')) {
												if($row['typ_pola'] == 'password')
													$wynik -> bindValue (':'.$row['column_s'] 	    , sha1($dane[$row['column_s']."_field"][$row_l['prefix']])) ;
												else
													$wynik -> bindValue (':'.$row['column_s'] 	    , $dane[$row['column_s']."_field"][$row_l['prefix']]) ;
											}
										}
										$liczbaZmian = $wynik -> execute();

									}catch(PDOException $e) {
										echo $e->getMessage();
										//header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,get_error");
									}  //echo $akcja;

								} else {
									echo 'bykol2';
								}
							}
						}
						header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,2,add_success");
					}
				} catch(PDOException $e) {
					
					echo $e->getMessage();
					//header("Location: ".MyConfig::getValue("wwwPatchPanel"));
				}
				
			}
			else {
				$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola fp LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz fps ON fps.id_pola = fp.id_pola WHERE fp.id_formularza = ".$formId." AND fp.akcje != 'visible' AND active = 1 ORDER BY pole_order";
				try {
					$tablicaWynikow = array();
					$tablicaWynikow = ConnectDB::subQuery($sql);
					if(count($tablicaWynikow) > 0) {
						$j = 0;
						foreach($tablicaWynikow as $row) {
							if($row['powiazana_tabela']) {

								if(strpos($row['powiazana_tabela'], "_lang")) {
									$tabela = str_replace ("_lang", "", $row['powiazana_tabela']);
									$join = " LEFT JOIN ".$tabela." ON ".$tabela.".".$row['pow_value']." = ".$row['powiazana_tabela'].".".$row['pow_value'];
								}
								else
									$tabela = $row['powiazana_tabela'];

								$sql = "SELECT id_formularza FROM ".MyConfig::getValue("dbPrefix")."cms_formularz WHERE nazwa_tabeli = '".$tabela."'";
								$aWyniki = array();
								$aWyniki = ConnectDB::subQuery($sql);

								if(is_array($aWyniki)) {
									$sql = "SELECT column_s FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$aWyniki[0]['id_formularza']." AND akcje = 'visible' LIMIT 1";
									$aTab = array();
									$aTab = ConnectDB::subQuery($sql);
									if(is_array($aTab) && count($aTab) > 0)
										$visible_column = $aTab[0]['column_s'];

									if($visible_column != "")
										$where = " WHERE ".$visible_column." = 1";
								}
								$sql = "SELECT *, ".$row['powiazana_tabela'].".".$row['pow_value']." as pow_value, CONCAT_WS(' ',".$row['powiazana_tabela'].".".$row['pow_name'].") as pow_name FROM ".$row['powiazana_tabela'].$join.$where." GROUP BY ".$row['powiazana_tabela'].".".$row['pow_value']." ORDER BY ".$row['pow_name'];
							
								$tablicaWynikow2 = array();
								$tablicaWynikow2 = ConnectDB::subQuery($sql);
								$tablicaWynikow[$j]['powiazania'] = $tablicaWynikow2;
							}
							$j++;
						}
					}
					else
						header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,get_error");
				} catch(PDOException $e) {
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
					header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,get_error");
				}
				if($akcja2 == 'edit') {
					$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_langs";
					$aLang = array();
					$aLang = ConnectDB::subQuery($sql);
					
					if($oMain -> lang_table) {
						$sql 	= "SELECT * FROM ".$oMain -> table." LEFT JOIN ".$oMain -> lang_table." ON (".$oMain -> table.".".$oMain -> idColumn." = ".$oMain -> lang_table.".".$oMain -> idColumn.") WHERE ".$oMain -> table.".".$oMain -> idColumn." = ".$akcja4;
					} else {
						$sql 	= "SELECT * FROM ".$oMain -> table."  WHERE ".$oMain -> table.".".$oMain -> idColumn." = ".$akcja4;
					}
					$aDane = array();
					$aDane = ConnectDB::subQuery($sql);
					
					$aRecord = array();
					foreach($tablicaWynikow as $row) {
						if($row['lang']) {
							foreach($aLang as $lang) {
								foreach($aDane as $row_d) {
									if($row_d['lang_prefix'] == $lang['prefix']) {
										$value = $row_d[$row['column_s']];
										break;
									}
								}
								$aRecord[0][$row['column_s']][$lang['prefix']] = $value;
								$value = "";
							}
						} else {
							$aRecord[0][$row['column_s']] = $aDane[0][$row['column_s']];
						}
					}

				}

				$this -> aPola = $tablicaWynikow;
				return $aRecord;
			}
		}
		else {
			header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,get_error");
			exit();
		}
	}
	
	public function uploadPhoto($id, $nazwaPliku, $sciezka, $rozszerzenie = '', $imgx = 0, $imgy = 0, $crop = 0) {
		if($rozszerzenie == '') {
			$roz = explode(".",$nazwaPliku);
			$cnt = count($roz);
			$rozszerzenie = strtolower($roz[$cnt-1]);
		}
		copy($sciezka.$nazwaPliku, $sciezka.$id.".".$rozszerzenie);

		//jeśli obrazek to resizujemy
		if($rozszerzenie == 'jpg') {
			//sprawdzamy czy crpujemy
			asido::driver('gd');
			$img = imagecreatefromjpeg($sciezka.$id.".".$rozszerzenie);
			$x = imagesx($img);
			$y = imagesy($img);

			if($crop) {
				
				if($imgx == 0) {
					$imgx = $x * $imgy/$y;
				}
				else {
					if($crop == 'x') {
						$crop_val = $imgy;
						$ny = $y * $imgx/$x;
						$prop = ($ny - $imgy)/2;
						if($prop < 0)
							$prop = 0;
						if($crop_val > $ny)
							$crop_val = $ny;
					}
				}

				if($imgy == 0) { 
					$imgy = $y * $imgx/$x;
				}
				else {
					if($crop == 'y') {
						$crop_val = $imgx;
						$nx = $x * $imgy/$y;
						$prop = ($nx - $imgx)/2;
						if($prop < 0)
							$prop = 0;
						if($crop_val > $nx)
							$crop_val = $nx;
					}	
				}

				$i1 = asido::image($sciezka.$id.".".$rozszerzenie, $sciezka.$id.".".$rozszerzenie);
				Asido::resize($i1, $nx, $ny);

				if($crop == 'x')
					Asido::crop($i1, 0, floor($prop), $imgx, $crop_val);
				else
					Asido::crop($i1, floor($prop), 0, $crop_val, $imgy);
				$i1->save(ASIDO_OVERWRITE_ENABLED);
				$i1 = null;

			}
			//bez cropa
			else {
				$i1 = asido::image($sciezka.$id.".".$rozszerzenie, $sciezka.$id.".".$rozszerzenie);
				if($imgx == 0) {
					$imgx = $x * $imgy/$y;
				}
				if($imgy == 0) { 
					$imgy = $y * $imgx/$x;
				}
				Asido::resize($i1, $imgx, $imgy);

				$i1->save(ASIDO_OVERWRITE_ENABLED);
				$i1 = null;

			}
		}
		return $rozszerzenie;
	}

	public function saveRelated($form_id) {

	    $error = false;
	    $sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola fp LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz szcz ON szcz.id_pola = fp.id_pola WHERE fp.id_formularza = ".$form_id." AND fp.lang IS NULL AND fp.akcje != 'visible' AND fp.active = 1";
	    try {

		$tablicaWynikow = array();
		$tablicaWynikow = ConnectDB::subQuery($sql);

		if(count($tablicaWynikow) > 0) {
		    foreach($tablicaWynikow as $row) {
			if($_POST[$row['column_s']."_field"] == '') $error = true;

			$dane[$row['column_s']."_field"] = $_POST[$row['column_s']."_field"];
			$pola .= $row['column_s'].", ";
			$pola_bind .= ":".$row['column_s'].", ";

		    }
		}
	    } catch(PDOException $e) {
		Log::SLog($e->getMessage().' '.$e->getTraceAsString());
		header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,add_error");
	    }

	    if(!$error) {
		$pola 		= substr($pola,0,-2);
		$pola_bind 	= substr($pola_bind,0,-2);

		$sql = "INSERT INTO ".$_POST['table']." (".$pola.") VALUES (".$pola_bind.")";

		try {
		    $pdo = new ConnectDB();
		    $pdo -> exec("SET names utf8");
		    $wynik = $pdo -> prepare($sql) ;
		    foreach($tablicaWynikow as $row) {
			$wynik -> bindValue (':'.$row['column_s'] 	    , $dane[$row['column_s']."_field"]) ;
		    }
		    $liczbaZmian = $wynik -> execute();


		 } catch(PDOException $e) {
		    Log::SLog($e->getMessage().' '.$e->getTraceAsString());
		    header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,add_error");
		} return true;
	    } else return false;
	}

	public function saveRelatedArray($form_id) {

	    $error = false;
	    $sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola fp LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz szcz ON szcz.id_pola = fp.id_pola WHERE fp.id_formularza = ".$form_id." AND fp.lang IS NULL AND fp.akcje != 'visible' AND fp.active = 1";
	    try {

		$tablicaWynikow = array();
		$tablicaWynikow = ConnectDB::subQuery($sql);

		if(count($tablicaWynikow) > 0) {
		    if(is_array($_POST[$_POST['array_column']."_field"])) {

			$sql = "DELETE FROM ".$_POST['table']." WHERE ".$_POST['main_field']." = ".$_POST[$_POST['main_field'].'_field'];
			ConnectDB::subExec($sql);
			foreach($_POST[$_POST['array_column']."_field"] as $row_v) {
			    foreach($tablicaWynikow as $row) {

				if(is_array($_POST[$row['column_s']."_field"]))
				    $dane[$row['column_s']."_field"] = $row_v;
				else
				    $dane[$row['column_s']."_field"] = $_POST[$row['column_s']."_field"];
				
				$pola .= $row['column_s'].", ";
				$pola_bind .= ":".$row['column_s'].", ";
				
			    }

			    $pola 		= substr($pola,0,-2);
			    $pola_bind		= substr($pola_bind,0,-2);

			    $sql = "INSERT INTO ".$_POST['table']." (".$pola.") VALUES (".$pola_bind.")";
			    try {
				$pdo = new ConnectDB();
				$pdo -> exec("SET names utf8");
				$wynik = $pdo -> prepare($sql) ;
				foreach($tablicaWynikow as $row) {
				    $wynik -> bindValue (':'.$row['column_s'] 	    , $dane[$row['column_s']."_field"]) ;
				}
				$liczbaZmian = $wynik -> execute();
				$pola	    = false;
				$pola_bind  = false;
				$dane	    = false;

			    } catch(PDOException $e) {
				Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,add_error");
			    }
			}
		    }
		}
	    } catch(PDOException $e) {
		Log::SLog($e->getMessage().' '.$e->getTraceAsString());
		header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,add_error");
	    }
	}

	public function saveRelatedPhotos($form_id) {

	    $error = false;
	    $sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola fp LEFT JOIN ".MyConfig::getValue("dbPrefix")."cms_formularz_pola_szcz szcz ON szcz.id_pola = fp.id_pola WHERE fp.id_formularza = ".$form_id." AND fp.lang IS NULL AND fp.akcje != 'visible' AND fp.active = 1";
	    try {

		$tablicaWynikow = array();
		$tablicaWynikow = ConnectDB::subQuery($sql);

	    } catch(PDOException $e) {
		Log::SLog($e->getMessage().' '.$e->getTraceAsString());
		header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,add_error");
	    }

	    if ($dir = @opendir($_POST['place'].'/new')) {
		while($file = readdir($dir)) {
		    if($file != '' && $file != '.'&& $file != '..') {

			if(count($tablicaWynikow) > 0) {
			    foreach($tablicaWynikow as $row) {
				if($_POST[$row['column_s']."_field"] == '') $_POST[$row['column_s']."_field"] == 0;

				$dane[$row['column_s']."_field"] = $_POST[$row['column_s']."_field"];
				$pola .= $row['column_s'].", ";
				$pola_bind .= ":".$row['column_s'].", ";
				if($row['typ_pola'] == 'file') {
				    $imgx		= $row['image_scalex'];
				    $imgy 		= $row['image_scaley'];
				    $img_main		= $row['img_main'];
				    $img_roz		= $row['image_main_roz'];
				    $thumb_x		= $row['thumb_scalex'];
				    $thumb_y		= $row['thumb_scaley'];
				    $thumb_main		= $row['img_main_thumb'];

				}
			    }
			}

			if(!$error) {
			    $pola 	= substr($pola,0,-2);
			    $pola_bind 	= substr($pola_bind,0,-2);

			    $sql = "INSERT INTO ".$_POST['table']." (".$pola.") VALUES (".$pola_bind.")";

			    try {
				$pdo = new ConnectDB();
				$pdo -> exec("SET names utf8");
				$wynik = $pdo -> prepare($sql) ;
				foreach($tablicaWynikow as $row) {
				    $wynik -> bindValue (':'.$row['column_s'] 	    , $dane[$row['column_s']."_field"]) ;
				}
				$liczbaZmian = $wynik -> execute();
				$lastId = $pdo -> lastInsertId();$this -> clearPhoto($nazwaPliku, $infoPlik['sciezka']);

				$this -> uploadPhoto($lastId, 'new/'.$file, $_POST['place'].'/', $img_roz, $imgx, $imgy, $img_main);
				$this -> uploadPhoto($lastId.'_thumb', 'new/'.$file, $_POST['place'].'/', $img_roz, $thumb_x, $thumb_y, $thumb_main);
				$this -> clearPhoto($file, $_POST['place'].'/new/');

			     } catch(PDOException $e) {
				Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				header("Location: ".MyConfig::getValue("wwwPatchPanel").$akcja.".html,1,add_error");
			    }
			}
			$pola = '';
			$pola_bind = '';
		    }
		}
	    }
	    return true;
	    
	}
	
	public function clearPhoto($nazwaPliku, $sciezka) {
		@unlink($sciezka.$nazwaPliku);
		return true;
	}


}
?>