<?php
namespace Sphinx\Strategy;
use Sphinx\StrategyInterface;
use Sphinx\SearchLog;

class SeparateWords implements StrategyInterface {

	public function query($query, \Sphinx\Search $search)
	{
		$queryOrgin = $query;

		$client = $search->getClient();

		$indexName = $search->getIndexName();

		$queryArray = StringHelper::breakToWords($query);

		$result = array(
			'matches' 		=> array(),
			'total_found' 	=> 0
		);

		foreach($queryArray as $query) {
			$query = '('.$query.')|('.StringHelper::addStarsToWords($query, 1).')|('.StringHelper::addStarsToWords(StringHelper::breakQueryToAlfaAndDigits($query), 1).')';

			$resultSphinx = $client->Query($query, $indexName);

			$resId = array();
			if (isset($resultSphinx['matches'])) {
				$resId = array_keys($resultSphinx['matches']);
			}

			$search->addSearchLog(new SearchLog(
					$queryOrgin,
					$query,
					get_class($this).'::'.$query,
					$resultSphinx['total_found'],
					$resId
				));

			if (isset($resultSphinx['matches'])) {
				foreach($resultSphinx['matches'] as $key => $row) {
					if (isset($result['matches'][$key])) {
						$result['matches'][$key]['weight'] += $row['weight'];
					}
					else {
						$result['matches'][$key] = $row;
					}
				}

				$result['total_found'] += $resultSphinx['total_found'];
			}

		}

		uasort($result['matches'], function ($a, $b)	{
			if ($a['weight'] == $b['weight']) {
				return 0;
			}
			return ($a['weight'] < $b['weight']) ? 1 : -1;
		});

		$resId = array();
		if (isset($result['matches'])) {
			$resId = array_keys($result['matches']);
		}

		$search->addSearchLog(new SearchLog(
				$queryOrgin,
				$query,
				get_class($this),
				$resultSphinx['total_found'],
				$resId
		));

		return $result;
	}

}