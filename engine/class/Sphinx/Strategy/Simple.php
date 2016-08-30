<?php
namespace Sphinx\Strategy;
use Sphinx\StrategyInterface;
use Sphinx\SearchLog;

class Simple implements StrategyInterface {

	public function query($query, \Sphinx\Search $search)
	{
		$queryOrgin = $query;

		$client = $search->getClient();
		$client->clearFromUnsafeChars(false);

		$indexName = $search->getIndexName();
		$query = '('.$query.')|('.StringHelper::cutLastLetter(StringHelper::breakQueryToAlfaAndDigits($query)).')';  
		//$query = '"'.$query.'"~2';
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