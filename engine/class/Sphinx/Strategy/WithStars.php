<?php
namespace Sphinx\Strategy;
use Sphinx\StrategyInterface;
use Sphinx\SearchLog;

class WithStars implements StrategyInterface {

	public function query($query, \Sphinx\Search $search)
	{
		$queryOrgin = $query;

		$client = $search->getClient();

		$indexName = $search->getIndexName();

		$query = '('.$query.')|('.StringHelper::breakQueryToAlfaAndDigits($query).')';
		$query = StringHelper::addStarsToWords($query, 1);
		$resultSphinx = $client->Query($query, $indexName);

		$resId = array();
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