<?php
namespace Sphinx\Strategy;
use Sphinx\StrategyInterface;
use Sphinx\SearchLog;

class Simple2 implements StrategyInterface {

	public function query($query, \Sphinx\Search $search)
	{
		$queryOrgin = $query;

		$client = $search->getClient();
		$client->clearFromUnsafeChars(false);

		$indexName = $search->getIndexName();
		$aQuery = explode(" ", trim($query));
		if(count($aQuery)> 1 && $aQuery && is_array($aQuery)) {
			foreach($aQuery as $row_q) {
				if(strlen($row_q) > 3 && !is_numeric(mb_substr($row_q, -2, 1)) || (strlen($row_q) == 2 && is_numeric(mb_substr($row_q, -2, 1)) && !is_numeric(mb_substr($row_q, -1)) ) ) {
					$aQuery_new[] = '*='.StringHelper::cutLastLetter($row_q, -1);
				} elseif(strlen($row_q) > 2 && is_numeric(mb_substr($row_q, -1))) {
					$aQuery_new[] = $row_q."*";
				} elseif(strlen($row_q) > 2 && $row_q != 'dla') {
					$aQuery_new[] = $row_q;
				}
			}

			$query = '(('.implode($aQuery_new, ')&(').'))|('.StringHelper::cutLastLetter(StringHelper::breakQueryToAlfaAndDigits($query)).')';  
		} else {
			$query = StringHelper::cutLastLetter(StringHelper::breakQueryToAlfaAndDigits($query), -1);
			$query = "(((".$query.")|(".$queryOrgin."))&(".$queryOrgin."*))";
		}
		$resultSphinx = $client->Query($query, $indexName);

		$resId = array();
//		if(count($resultSphinx['matches']) < 25) {
//			$query = '('.$query.')|('.StringHelper::cutLastLetter($query).')';
//			$resultSphinx = $client->Query($query, $indexName);
//		}
		
		if (isset($resultSphinx['matches'])) {
			$resId = array_keys($resultSphinx['matches']);
		}

		$search->addSearchLog(new SearchLog(
				$queryOrgin,
				$query,
				get_class($this),
				$resultSphinx['total_found'],
				$resId
			));

		return $resultSphinx;
	}
}