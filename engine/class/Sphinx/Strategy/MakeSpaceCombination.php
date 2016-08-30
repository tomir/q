<?php
namespace Sphinx\Strategy;
use Sphinx\StrategyInterface;
use Sphinx\SearchLog;

class MakeSpaceCombination implements StrategyInterface {

	protected $minLenWithStars = 3;

	public function __construct($minLenWithStars = 3) {
		$this->minLenWithStars = $minLenWithStars;
	}

	public function query($query, \Sphinx\Search $search)
	{
		$queryOrgin = $query;

		$client = $search->getClient();

		$indexName = $search->getIndexName();
		
		$aQuery = StringHelper::getSpaceCombination($query);

		$query = '('.StringHelper::addStarsToWords($query, $this->minLenWithStars).')|('.StringHelper::addStarsToWords(StringHelper::breakQueryToAlfaAndDigits($query), $this->minLenWithStars).')|('.implode(")|(", $aQuery).')';
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