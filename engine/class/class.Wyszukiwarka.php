<?php

class Wyszukiwarka
{
	
	
	public function getData()
	{

		$sql = "SELECT 	w.*, (SELECT GROUP_CONCAT(DISTINCT slowo SEPARATOR ',') FROM wyszukiwanie_mapowanie_pow wmp WHERE wm.id = wmp.id_mapowanie) AS nowe						
				FROM wyszukiwanie_slowa w
				LEFT JOIN wyszukiwanie_mapowanie wm ON w.slowo = wm.stare_slowo
				WHERE w.id = ".$this->id;
		
		$res = ConnectDB::subQuery($sql, "fetch");
		$res['a_nowe'] = array();
		$res['a_nowe'] = explode(",",$res['nowe']);
		
		return $res;
	}
	
	public function saveData( $data )
	{
		
		$data['m_kto'] = $_SESSION['adminID'];
		
		ConnectDB::subAutoExec('wyszukiwanie_slowa', $data, 'INSERT');
				
		return true;
	}
	
	public static function updateData( $data )
	{
		
		$data['m_kto'] = $_SESSION['adminID'];
		$id = $data['id'];
		
		return ConnectDB::subAutoExec('wyszukiwanie_slowa', $data, 'UPDATE', "id = '$id' ");
	}
	
	public static function deleteData( $id )
	{
		
		$x = new Wyszukiwarka($id);
		
		// usuniecie z mapowania
		$sql = "DELETE FROM wyszukiwanie_mapowanie WHERE stare = '".$x->data['slowo']."' ";
		ConnectDB::subExec($sql);
		
		// usuniecie ze slow
		$sql = "DELETE FROM wyszukiwanie_slowa WHERE id = ".$id;
		ConnectDB::subExec($sql);
		
		return true;
	}
	
	static function getList( $filtr=NULL, $sort = NULL, $limit = NULL )
	{
		
		$sql = "SELECT 	w.*, (SELECT GROUP_CONCAT(DISTINCT slowo SEPARATOR ', ') FROM wyszukiwanie_mapowanie_pow wmp WHERE wm.id = wmp.id_mapowanie) AS nowe
				FROM wyszukiwanie_slowa w
				LEFT JOIN wyszukiwanie_mapowanie wm ON w.slowo = wm.stare_slowo
				WHERE 1 ";
		
		$sql.= self::filterGetList($filtr);

		$sql.= self::getSort($sort);
		
		$sql.= self::getLimit($limit);
	
		//Debug($sql, false);
			
		$res = ConnectDB::subQuery($sql, "fetchAll");		
		
		return $res;
	}
	
	static function getListQty( $filtr = NULL)
	{
		
		$sql = "SELECT count(*) AS ilosc
				FROM wyszukiwanie_slowa w
				LEFT JOIN wyszukiwanie_mapowanie wm ON w.slowo = wm.stare_slowo
				WHERE 1 ";

		$sql.= self::filterGetList($filtr);

		$res = ConnectDB::subQuery($sql, "one");	

		return $res;
	}
	
	static function filterGetList($filtr=NULL)
	{
		$sql = '';
		
		if( isset($filtr) && is_array($filtr) )
		{
			if (isset($filtr['slowo']) && $filtr['slowo'] != '') {
				$sql .= " AND (
								w.slowo LIKE '%".$filtr['slowo']."%'
							) ";
			}
		}
		
		return $sql;
	}
	
	
	static function getListFrazy( $filtr=NULL, $sort = NULL, $limit = NULL )
	{
		
		$sql = "SELECT 	*
				FROM wyszukiwanie_fraza 
				WHERE 1 ";
		
		$sql.= self::getSort($sort);
			
		$res = ConnectDB::subQuery($sql, "fetchAll");		
		
		return $res;
	}
	
	
	public function saveDataMapowanie( $slowo, $nowe )
	{
		
		$sql = "SELECT id FROM wyszukiwanie_mapowanie WHERE stare_slowo = '$slowo' ";
		$id = ConnectDB::subQuery($sql, "one");
		
		
		if($id > 0) {
			$sql = "DELETE FROM wyszukiwanie_mapowanie_pow WHERE id_mapowanie = '".$id."' ";
			ConnectDB::subExec($sql);
			
			if(is_array($nowe) && count($nowe) > 0) {
				foreach($nowe as $row) {
					if($row != "") {
						ConnectDB::subAutoExec('wyszukiwanie_mapowanie_pow', array('id_mapowanie'=> $id, 'slowo' => $row), 'INSERT');
					}
				}
			}
		} else {
			$id = ConnectDB::subAutoExec('wyszukiwanie_mapowanie', array( 'stare_slowo' => $slowo), 'INSERT');
			ConnectDB::subAutoExec('wyszukiwanie_slowa', array('slowo' => $slowo), 'INSERT');
			
			if(is_array($nowe) && count($nowe) > 0) {
				foreach($nowe as $row) {
					if($row != "") {
						ConnectDB::subAutoExec('wyszukiwanie_mapowanie_pow', array('id_mapowanie'=> $id, 'slowo' => $row), 'INSERT');
					}
				}
			}
		}
				
		return true;
	}
	
	
	
	
	/**
	 * Czysci fraze 
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	static public function clearFraza($string)
	{
		$temp = explode(" ", $string);
		if(strstr($temp[0], 'Citro')) $temp[0] = "Citroen";
		$string = implode(" ", $temp);
		$string = str_replace(array("<br>","<br />","<br/>"),' ',nl2br($string));
		$string = strip_tags($string);
		$string = preg_replace("/[^0-9a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\/\. ]/",' ',$string);
		
		$string = preg_replace("/( {2,})/",' ',$string);
			
		// rozbicie fraz zawierajacych i litery i cyfry
		//$string = preg_replace("/([0-9])([a-zA-Z])/",'$1 $2',$string);
		//$string = preg_replace("/([a-zA-Z])([0-9])/",'$1 $2',$string);
		
		return $string;
	}
	
	/**
	 * Przerabia szukany string na fraze z opcjoami +, - , ~, * < , >, .....
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	static public function getFtSearch($string, $inc = false, $no_clear = false)
	{
		if ($inc == true && strlen($string) > 1 )
			self::incFraza($string);
		
		if(!$no_clear)
			$string = self::clearFraza($string);
		
		// rozbicie na pojedyncze zwroty
		$tab = split(' ',$string);
		
		//$mapowanie = self::getMapowanie($string);
		
		$string = '';
		foreach ($tab as $one) {
			$one = trim($one);
			if(strlen($one) < 4) {
				$one = "x".$one;
				if(strlen($one) < 4) {
					$one = "x".$one;
				}
			}
			
			if ($one != '' && strlen($one) > 1) {
				if ($inc == true && $one != ''){
					self::incSlowo($one);
				}
				
//				if (key_exists($one,$mapowanie))
//					$one = $mapowanie[$one];
				
				$prefix = '+';
				//if (strlen($one) > 3) $prefix = '>';
					
				$string .= $prefix.$one.' ';
				/*
				if (strlen($one) >= 3)
					$string .= '+X'.$one.' ';
				 * 
				 */
			}
		}
		
		//if ($string != '')
		//	$string = substr($string,0,-1);
		
		return $string;
	}
	
	/**
	 * zwraca ilosc ile musi byc score aby pokazac wynik
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	static public function getFtSearchIlosc($string)
	{

		$string = self::getFtSearch($string);
		
		// rozbicie na pojedyncze zwroty
		$tab = split(' ',$string);
		
		
		$nadmiar = 0;
		foreach ($tab as $one) {
			$one = trim($one);
			if ($one != '' && strlen($one) > 1) {
				$res++;
				
				if (strpos($one,'X') === 0) {
				$nadmiar++;
					$res--;
				}
			}
		}
		
		
		if ($res >= 4) {
			$res--;
		}
		
		return ($res + $nadmiar);
	}

	/**
	 * Przerabia szukany string na fraze z opcjoami +, - , ~, * < , >, .....
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	static public function getSearch($string)
	{
		$mapowanie = self::getMapowanie();

		if( $mapowanie[$string] != '' )
			return $mapowanie[$string];
		else
			return $string;

		return $string;
	}
	
	
	static public function incSlowo($slowo)
	{
		try {
			$sql = "UPDATE wyszukiwanie_slowa SET ilosc = ilosc + 1 WHERE slowo = '$slowo' ";
			$res = ConnectDB::subExec($sql);	

			if ($res == false) {
				$sql = "INSERT INTO wyszukiwanie_slowa (slowo, ilosc) VALUES ('$slowo', 1) ";
				ConnectDB::subExec($sql);
			}
		}
		catch (Exception $e) {
			//echo $e->getMessage();
			//exit;
		}
	}
	
	
	static public function incFraza($string)
	{
		try {
			
			$string = Common::clean_input($string);
			
			$sql = "UPDATE wyszukiwanie_fraza SET ilosc = ilosc + 1 WHERE fraza = '$string' ";
			$res = ConnectDB::subExec($sql);
			
			if ($res == false) {
				$sql = "INSERT INTO wyszukiwanie_fraza (fraza, ilosc) VALUES ('$string', 1) ";
				ConnectDB::subExec($sql);
			}
		}
		catch (Exception $e) {
			//echo $e->getMessage();
			//exit;
		}
	}
	
	static public function getMapowanie($slowo)
	{

		$sql = "SELECT id FROM wyszukiwanie_mapowanie WHERE stare_slowo = '".$slowo."' ";
		$res = ConnectDB::subQuery($sql, "fetchAll");
		
		$result = array();
		if(is_array($res) && count($res) > 0) {
			foreach ($res as $one) {

				$sql = "SELECT slowo FROM wyszukiwanie_mapowanie_pow WHERE id_mapowanie = ".$one['id'];
				$res2 = ConnectDB::subQuery($sql, "fetchAll");

				foreach($res2 as $one2) {
					$result[$one2['slowo']] = $one2['slowo'];
				}
			}
			return $result;
		} else {
			return array($slowo => $slowo);
		}
	}
	
	static public function getLikeSearch($string)
	{
		$string = self::clearFraza($string);
		
		//$string = preg_replace('/ /','',$string);
		
		return $string;
	}
	
	static public function getLikeNazwaSearch($string, $column = "")
	{
		if(strlen($string) > 0) {
			// rozbicie na pojedyncze zwroty
			$tab = explode(' ',$string);

			$string = '';
			foreach ($tab as $one) {
				$one = trim($one);
				if ($one != '' && strlen($one) > 1) {
					if ($inc == true && $one != ''){
						self::incSlowo($one);
					}
					if($column != '') {
						$string .= "%".$one."%' OR ".$column." LIKE '";
					} else 
						$string .= '%'.$one.'%';
				}
			}

		}
		if($column != '') {
			$cnt_columnn = strlen($column);
			$string = substr($string, 0, -12-$cnt_columnn);
		}
		return $string;
	}
	
}

?>