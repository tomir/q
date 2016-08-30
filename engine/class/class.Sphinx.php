<?php

class Sphinx
{
	
	public $sphinx;
	public $_suggest = null;

	public function __construct(array $options = array()) {
		
		$this->sphinx = \Sphinx\SearchInstance::init(
			'autolicytacje_index', // index w sphinx ktory chcesz przeszukac
			'localhost', // host na ktorym nasluchuje sphinx
			3312  // port na ktorym nasluchuje sphinx
		);   

	}

	/**
	* @return array 
		* @throws Sphinx_Exception
	*/
	public function search($query) { 
		
		
		$searchSphinx = Sphinx\SearchInstance::getInstance(true);
		if ($searchSphinx != null) { // zabezpiecza przed przypadkiem kiedy ktos zapomni uruchomic komenty z pkt. 1.
			
//			$queryMap = array();
//			$queryMap = Wyszukiwarka::getMapowanie($query);
//		
//			if($queryMap) {
//				$aFraza = array();
//				foreach($queryMap as $map) {
//					$aFraza[] = $map;
//				}
//				
//				$res = $searchSphinx->query(implode(" ",$aFraza));
//			} else {
				$res = $searchSphinx->query($query);
			//}
	
		}
		
		if($_GET['debug_w'] == 1) {
			foreach($searchSphinx->getSearchLog() as $row_log) {
				var_dump($row_log);
			}
		}
		//$log = $searchSphinx->getLastSearchLog();
		$logSuggestFlag = false;
		/*if ($log instanceof \Sphinx\SearchLog) {
			if ($log->getStrategyClass() == 'Sphinx\Strategy\SeparateWords')
			{ $logSuggestFlag = true; }
		}*/
		
		if($logSuggestFlag) {
			$query = Sphinx\Producent::findProducent($query);
		}
		
		/*
		$logSuggestWiki = false;
		if(!is_array($res) || count($res) < 1 || $logSuggestFlag) {
			/*
			* Sugestie Wiki
			
			$wikiSuggest = new \Wikipedia\Suggest();
			$this->_suggest = $query['producent'][0]." ".$wikiSuggest ->getSuggestion(implode(' ', $query['query']));

			if(strlen($this->_suggest) > 2) {
				$res = $searchSphinx->query($this->_suggest);
				$logSuggestWiki = true;
			}
		}
		
		if(!is_array($res) || count($res) < 1 || (!$logSuggestWiki && $logSuggestFlag)) {
			/*
			* Sugestie nazwa producenta
			
			$this->_suggest = $query['producent'][0]." ".Sphinx\Producent::getLevenstain(implode(' ', $query['query']));
			if(strlen($this->_suggest) > 2) {
				$res = $searchSphinx->query($this->_suggest);
			}
		}
		*/
		/*
		foreach($searchSphinx->getSearchLog() as $row_log) {
			echo $row_log->getQueryForSphinx()."<br />";
			echo $row_log->getQueryForUser()."<br />";
			echo $row_log->getStrategyClass()."<br />";
			echo $row_log->getIloscWynikow()."<br />";
		}
		*/
		/*
		$queryMap = array();
		$queryMap = Wyszukiwarka::getMapowanie($query);
		foreach($queryMap as $map) {
		
			$map = str_replace("\'", "", $map);
			
			//dodanie *
			$aTemp = array();
			$aTemp = explode(" ", $map);
			
			if(count($aTemp) > 0 && is_array($aTemp)) {
				$aTemp2 = array();
				foreach($aTemp as $row_t) {
					if(strlen($row_t) > 2)
						$aTemp2[] = str_replace("'", "", "*".$row_t."*");
					else
						$aTemp2[] = str_replace("'", "", $row_t);
				}
		
				$map = implode(" ", $aTemp2);
			}
			
			$map = $this->_client->EscapeString($map);
			d($map);

			$this->_client->AddQuery(mb_strtoupper($this->zamienZnakiSpecjalne($map), 'UTF-8'), 'bdsklep_index_test');
			$this->_client->AddQuery(mb_strtolower($this->zamienZnakiSpecjalne($map), 'UTF-8'), 'bdsklep_index_test');
			
		}

		$resultSphinx = $this->_client->RunQueries(); 
		if($_GET['sphinxd'] == 1) {
			d($this->_preareResults($resultSphinx));
		}
		
		$return = $this->_preareResults($resultSphinx);
		if(!is_array($return) || count($return) < 2 || count($return) > 500) {

			
			$obSphinxLapse = new Sphinx_Lapse(str_replace("*", "", $map));
			$new_map = $obSphinxLapse->findLapse();
			
			if(mb_strlen($new_map) < 1) {
				
				$aTemp = explode(" ", str_replace("*", "", $map));
				if(count($aTemp) > 0 && is_array($aTemp)) {
					
					$aTemp2 = array();
					foreach($aTemp as $row_t) {

						if(mb_strlen($row_t) > 3 && !is_numeric(mb_substr($row_t, 0, 1)) ) {
							
							$obSphinxLapse->_parse = $row_t;
							$obSphinxLapse->getFirstLetter();
							$aTemp2[] = $obSphinxLapse->findLapse();
						} else {
							$aTemp2[] = $row_t;
						}
					}

					$new_map = implode(" ", $aTemp2);
				}
			}
			d($new_map);
			
			if(mb_strlen($new_map) > 1) {
				$this->_client->AddQuery(mb_strtoupper($this->zamienZnakiSpecjalne($new_map), 'UTF-8'), 'bdsklep_index_test');
				$this->_client->AddQuery(mb_strtolower($this->zamienZnakiSpecjalne($new_map), 'UTF-8'), 'bdsklep_index_test');

				$resultSphinx = $this->_client->RunQueries();
				$return = $this->_preareResults($resultSphinx);
			}
			$return['new_map'] = $new_map;
		}
		*/
		return $res;
	}

}

?>