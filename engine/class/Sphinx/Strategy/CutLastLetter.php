<?php
namespace Sphinx\Strategy;
use Sphinx\StrategyInterface;
use Sphinx\SearchLog;

class CutLastLetter implements StrategyInterface {

	public function query($query, \Sphinx\Search $search)
	{
		$queryOrgin = $query;

		$client = $search->getClient();
		$client->clearFromUnsafeChars(false);

		$indexName = $search->getIndexName();
		echo StringHelper::cutLastLetter($query);
		$resultSphinx = $client->Query(StringHelper::cutLastLetter($query), $indexName);

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