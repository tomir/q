<?php
namespace Sphinx;

class Producent {
	
	static public function getLevenstain($query) {
		
		$flagSearch = true;
		$aTemp		= array();
		
		$aQuery		= explode(" ", $query);
		foreach($aQuery as $row) {
		
			if($flagSearch) {
				
				$sql = "SELECT producent_name, (@t := levenshtein( producent_name, '".$row."' )) AS similar 
						FROM	(SELECT nazwa
									FROM shop_producents
									WHERE SUBSTRING( producent_name, 1, 1 ) = '".substr($row, 0, 1)."'
								) AS foo 
						HAVING similar < 3 ORDER BY similar ASC, LENGTH(producent_name) DESC";
				
				if(MEMCACHE == 1) {
					$obMem = new \MyMemcache();
					$rAll = $obMem->getData($sql, 'all', 36);
				} else {
					$rAll = $db->getAll($sql);
				}

				$res = $rAll[0]['nazwa'];
				if(strlen($res) > 1) {
					$flagSearch = false;
					$aTemp[] = $res;
				}
			} else {
				$aTemp[] = $row;
			}
		}
		
		return implode(" ", $aTemp);
	}
	
	static public function findProducent($query) {
		
		global $_gTables;
		try {
			
			$db = \Db::getInstance();
			$query = explode(" ", $query);
			
			$flagProducent = true;
			
			$aRow = array();
			$aRow['producent'][0] = '';
			foreach($query as $row_q) {
				
				if($flagProducent && strlen($row_q) > 2) {
				
					$sql = "SELECT producent_name
							FROM	(SELECT producent_name
										FROM shop_producents
										WHERE SUBSTRING( producent_name, 1, 1 ) = '".substr($row_q, 0, 1)."'
									) AS foo 
							WHERE levenshtein( producent_name, '".$row_q."' ) < 2 LIMIT 1";
					
					$nazwa_pop = $db->GetRow($sql);
					if(strlen($nazwa_pop['producent_name']) > 2) {
						$aRow['producent'][0] = $nazwa_pop['producent_name']; 
						$flagProducent = false;
					} else {
						$aRow['query'][] = $row_q;
					}
				} else {
					$aRow['query'][] = $row_q;
				}
			} 
			return $aRow;
		} catch (Exception $e) {
			Common::log(__CLASS__ . '::' . __METHOD__, $sql . "\n" . $e->getMessage());
			return null;
		}
		
	}
}

?>
