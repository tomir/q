<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logger
 *
 * @author tomaszcisowski
 */
class Log_Logger implements SplObserver {
	
	public $aData;
	
	/**
	* Log the fact that a user has been updated
	*
	* @param SplSubject $subject
	*/
	public function update(SplSubject $subject)
	{
		$this->aData['method'] = $subject->method;
		$loginData = $subject->getLoginData();
		
		$this->aData['login']		= $loginData['dealer-login'];
		$this->aData['exec_date']	= date("Y-m-d H:i:s");
		$this->addLog();
	}
	
	public function addLog() {
		
		ConnectDB::subAutoExec( "autosalon_otomoto_log", $this->aData, "INSERT" );
		return true;
	}
	
	public function getList($filtr) {
		
		global $_gTables;

		try {
			$db = Db::getInstance();

			$sql = "SELECT
						al.*
						FROM allegro_log al
						WHERE 1 ";

			$sql.= self::filterGetList($filtr);
			$all = $db->getAll($sql);
		
			$new_array = array();
			foreach($all as $row) {
				$new_array[$row['login']][substr($row['exec_date'], 0,10)] = 0;
				$new_array["labels"][substr($row['exec_date'],0,10)] = 1;
			}
			
			foreach($all as $row) {
				$new_array[$row['login']][substr($row['exec_date'],0,10)]++;
			}
			
			$this->generateTsvFile($new_array, $sql);
			
			return $new_array;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
		
	}
	
	static function filterGetList($filtr=NULL)
	{

		$sql = '';

		if( isset($filtr) && is_array($filtr) )
		{
			if ($filtr['id_konto'] != '' && is_numeric($filtr['id_konto'])) {
				$sql .= " AND al.id_konto = '".$filtr['id_konto']."' ";
			}
			elseif (isset($filtr['id_konto']) && is_array($filtr['id_konto']) && count($filtr['id_konto']) > 0) {
				$sql .= " AND al.id_konto IN (".implode(',',$filtr['id_konto']).") ";
			}
			
			if (isset($filtr['data_od']) && $filtr['data_od'] != '' ) {
				$sql .= " AND al.exec_date >= '".$filtr['data_od']."' ";
			}

			if (isset($filtr['data_do']) && $filtr['data_do'] != '' ) {
				$sql .= " AND al.exec_date <= '".$filtr['data_do']."' ";
			}

			if ($filtr['method'] != '') {
				$sql .= " AND al.method = '".$filtr['method']."' ";
			}

		}
		return $sql;
	}
	
	public function ajaxGetChart($filter) {
		
		$objResponse = new xajaxResponse();
		
		$list = $this->getList($filter['filtr']);
		$objResponse->assign("editContent", "innerHTML", $this->getChartHtml($list));
		$objResponse->script('generuj_wykres("chart");');
		
		return $objResponse;
		
		
	}
	
	public function getChartHtml($filtr) {
		
		$htmlRes = SmartyObj::getInstance();
		$list = $this->getList($filtr);
		$htmlRes->assign('colors', array(1=>"red", 2=>"blue", 3=>"green"));
		$htmlRes->assign('lista', $list );

		$html = $htmlRes->fetch('_ajax/allegro/log.wykres.tpl');
		return $html;
	}
	
	public function generateTsvFile($list, $sql) {
		
		$tsv .= "# ----------------------------------------\n";
		$tsv .= "# Graph\n";
		$tsv .= "# ----------------------------------------\n";
		$tsv .= "Label";
		
		foreach($list as $key => $row) {
			if($key != 'labels') {
				$tsv .= "\t".$key;
				$column[] = $key;
			}
		}
		
		$tsv .= "\n";
		
		foreach($list["labels"] as $key => $row) {
			$tsv .= $key;
			
			foreach($column as $row2) {
				if(!isset($list[$row2][$key]) || $list[$row2][$key] == '' || $list[$row2][$key] == 0) {
					$list[$row2][$key] = 0;
				}
				$tsv .= "\t".$list[$row2][$key];
			}
			
			$tsv .= "\n";
		}
		
		$logfile = APPLICATION_DIR.'admin/allegro/log_view_1_'.md5($sql).'.tsv';
		$fplog = fopen($logfile, 'wa+');
		fputs($fplog, $tsv);

		rewind($fplog);
		$output = stream_get_contents($fplog);
		fclose($fplog);
		return $output;
	}
	
	static public function setAllegroLog($id_aukcji, $error) {
		
		try {
			$db = Db::getInstance();
			
			$aData = array();
			$aData['id_allegro_bledy'] = self::mapError($error);
			$aData['id_aukcja'] = $id_aukcji;
			$aData['data'] = date("Y-m-d H:i:s");

			$db->AutoExecute( "allegro_aukcja_log", $aData );
			return true;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
		
	}
	
	static function getAllegroLog($id_allegro) {
		
		try {
			$db = Db::getInstance();
			
			$sql = "SELECT b.*, l.data
				   FROM allegro_aukcja_log l
				   LEFT JOIN allegro_bledy b ON b.id = l.id_allegro_bledy 
				   WHERE l.id_aukcja = ".$id_allegro;
			
			$all = $db->GetAll($sql);
			
			return $all;
	
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
		
	}
	
	static function mapError($string) {
		
		try {
			$db = Db::getInstance();
			
			$sql = "SELECT b.id FROM allegro_bledy b WHERE code = '".$string."'";
			$id = $db->GetOne($sql);
			
			if($id > 0) {
				return $id;
			} else {
				return false;
			}
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
	}
}

?>
