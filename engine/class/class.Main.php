<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Main {
	private $records_count;
	public static $lang_patch = false;
/**
 *
 * @param array $table array(table_name, [array(join_table_name,table_name,join column name, table column name, [relation left, right])])
 * @param string $fetch fetchll - fetch
 * @param array $where_array array(array(table_name.column_name,table_name.column_name,'=/</>'),[array(table_name.column_name,table_name.column_name,'=/</>','AND/OR')]))
 * @param array $order_array array(array(table_name.column_name,ASC-DESC),[array(table_name.column_name,ASC-DESC)])
 * @param array $group_array array(table_name.column_name, [table_name.column_name])
 * @param int $limit
 * @param int $start
 * @param string $extra_select
 * @return array
 */
	public function getRecords($table = false, $fetch = 'fetchall' ,$where_array = false, $order_array = false, $group_array = false, $limit = 9999, $start = 0,$extra_select = false) {
		if(is_array($table)) {
			foreach($table as $row) {
				if(is_array($row)) {
					if($row[4] == 'left') {
						$from .= ' LEFT ';
					} else if($row[4] == 'right') {
						$from .= ' RIGHT';
					}
					$from .= ' JOIN '.MyConfig::getValue("dbPrefix").$row[0].' on '.MyConfig::getValue("dbPrefix").$row[1].'.'.$row[2].' = '.MyConfig::getValue("dbPrefix").$row[0].'.'.$row[3];
				} else {
					if($from) $from .= ', '.MyConfig::getValue("dbPrefix").$row;
						else $from = MyConfig::getValue("dbPrefix").$row;
				}
			}
		} else {
			$from = MyConfig::getValue("dbPrefix").$table;
		}
		
		if(is_array($where_array)) {
			foreach($where_array as $row) {
				if($where) {
					$where .= ' '.$row[3].' ';
				} else $where = ' WHERE ';
				$where .= MyConfig::getValue("dbPrefix").$row[0].' '.$row[1]." '".$row[2]."'";
			}
		}
		
		if(is_array($order_array)) {
			foreach($order_array as $row) {
				if($order) {
					$order .= ', ';
				} else $order = ' ORDER BY ';
				if($row[0] != 'random') {
					$order .= ' '.MyConfig::getValue("dbPrefix").$row[0].' '.$row[1];
				} else {
					$order .= ' rand() ';
				}
				
			}
		}

		if(is_array($group_array)) {
			foreach($group_array as $row) {
				if($group) {
					$group .= ', ';
				} else $group = ' GROUP BY ';
				$group .= ' '.MyConfig::getValue("dbPrefix").$row;
			}
		}

		$sql = "SELECT * ".$extra_select." FROM ".$from.' '.$where.' '.$group.' '.$order.' LIMIT '.$start.', '.$limit;
		
		//if($table[0] == 'categories') { echo $sql; exit; }
		try {
			$aResult = array();
			if($fetch == 'fetchall') {
				$aResult = ConnectDB::subQuery($sql,'fetchAll');
			} else {
				$aResult = ConnectDB::subQuery($sql,'fetch');
			}
		} catch(PDOException $e) {
			//mail('jurdziol@gmail.com','aa',$sql);
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}

		$this -> records_count = count($aResult);
		return $aResult;
	}

	public function getRecordsCount() {
		return $this -> records_count;
	}
	
	public function makePatch($elements) {
		if(is_array($elements)) {
			foreach($elements as $row) {
				if($patch) {
						$patch .= ' ';
					}
				if($row[1]) {
					$patch .= '<a href="'.$row[0].'">'.$row[1].'</a>';
				} else {
					$patch .= $row[0];
				}
			}
		} else {
			$patch = '/';
		}
		return $patch;
	}

	public function setLangPatch($lang) {
		Main::$lang_patch = '?lang='.$lang;
	}

	public function get2Records($sql) {
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);

		} catch(PDOException $e) {
			//mail('jurdziol@gmail.com','aa',$sql);
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}

		return $aResult;
	}

	public function makeUrl($array, $ext) {
		foreach($array as $row) {
			if($url) $url .= '/';
			if(is_array($row)) {
				foreach($row as $row2) {
					if($temp_url) $temp_url .= ',';
					$temp_url .= Misc::makeSlug($row2);
				}
				$url .= $temp_url;
			} else {
				$url .= $row;
			}
			$temp_url = false;
		}

		$url .= '.'.$ext;
		if(Main::$lang_patch) $url .= Main::$lang_patch;
		return $url;
	}

	public function getCatName($url_name) {
		$sql = "SELECT cat_name FROM ".MyConfig::getValue("dbPrefix")."categories JOIN categories_lang USING(cat_id) WHERE cat_url_name = '".$url_name."'";
		$aResult = ConnectDB::subQuery($sql,'','','fetch');
		return $aResult['cat_name'];
	}

	public function getTXT($lang = 'pl') {
		$aFrom = array('site_info',array('site_info_lang','site_info','id_info','id_info'));
		$aWhere = array(array('site_info_lang.lang_prefix','=',$lang));
		$result = $this->getRecords($aFrom,'fetchall',$aWhere);
		$aResult = array();
		if(is_array($result)) {
			foreach($result as $row) {
				$aResult[$row['i_title']] = $row['i_text'];
			}
			return $aResult;
		} else return false;
	}

	public function getParentName($id_parent) {
		$sql = "SELECT cat_url_name FROM ".MyConfig::getValue("dbPrefix")."categories JOIN ".MyConfig::getValue("dbPrefix")."categories_lang USING(cat_id) WHERE cat_id = ".$id_parent;
		$aResult = ConnectDB::subQuery($sql,'','','fetch');
		return $aResult['cat_url_name'];
	}

	public function replaceUrl($url, $uKey, $uVal){
	    
	    $Qurl = explode("?",$url);
	    $arr = @explode("&",$Qurl[1]); 
	    if(is_array($arr)) {
		
		$r = ''; $set = 0;
		for($i=0;$i<sizeof($arr);$i++){
		    $arr2 = explode("=",$arr[$i]);
		    if($arr2[0] == $uKey && $arr2[1] != $uVal){
			$arr2[1] = $uVal;
			$set = 1;
		    }
		    $r .= $arr2[0]."=".$arr2[1]."&";
		}
	    }
	    if($set == 0){
		$r.= $uKey."=".$uVal;
	    } else {
		$r = rtrim($r, "&");
	    }
	    $aRow = array();
	    $aRow[0] = $set;
	    $aRow[1] = $Qurl[0].'?'.$r;
	    
	    return $aRow;
	}

}
?>
