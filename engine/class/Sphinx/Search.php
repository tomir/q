<?php
namespace Sphinx;
use Sphinx\Strategy;
use Sphinx\StrategyInterface;

class Search {

	protected $indexName;
	protected $host;
	protected $port;

	protected $client = null;

	protected $sugestie = array();

	protected $strategyHeap = array();

	protected $searchLogHeap = array();

	public function __construct($indexName, $host, $port = 3312) {
		$this->indexName = $indexName;
		$this->host = $host;
		$this->port = $port;

		
		//$this->addStrategy(new Strategy\Simple()); // domyslna strategia
		//$this->addStrategy(new Strategy\Simple2());
		$this->addStrategy(new Strategy\WithoutStars());
		//$this->addStrategy(new Strategy\WithStars());
		//$this->addStrategy(new Strategy\MakeSpaceCombination());
		$this->addStrategy(new Strategy\MakeWordCombination());
		//$this->addStrategy(new Strategy\WithAndWithoutStars());
		//$this->addStrategy(new Strategy\SeparateWords());

	}

	/**
	 * @return \Enp\Sphinx\Client
	 */
	public function getClient() {
		if ($this->client == null) {
			$this->client = $this->getConfiguredClient();
		}

		return $this->client;
	}

	protected function getConfiguredClient() {
		$sphinxClient = new \Sphinx\Client();
		$sphinxClient->SetServer($this->host, (int)$this->port);
		$sphinxClient->SetLimits(0, 1000);
		$sphinxClient->SetMatchMode(SPH_MATCH_EXTENDED2);
		//$sphinxClient->SetFieldWeights(array('wyszukiwarka_new' => 4000, 'wyszukiwarka_own' => 8000, 'nazwa' => 200));

		return $sphinxClient;
	}

	public function query($query) {

		$client = $this->getClient();

		$query = Strategy\StringHelper::removeSpacesBetween($query);

		foreach($this->strategyHeap as $strategyObject) {

			if (!$strategyObject instanceof StrategyInterface) {
				throw new Exception("Strategia nie implementuje interfejsu StrategyInterface");
			}

			$res = $strategyObject->query($query, $this);

			if (isset($res['matches']) && count($res['matches']) > 0) {
				$resId = array_keys($res['matches']);
				
				/*
				 * JEśli znajdzie podstawową metodą to uzupełniamy wyniki opcjonalnie o wyrażenie w liczbie pojedyńczej
				
				//if($strategyObject instanceof 'Sphinx\Strategy\Simple') {
					$strategyObject = new Strategy\CutLastLetter();
					$res = $strategyObject->query($query, $this);
					if (isset($res['matches']) && count($res['matches']) > 2) {
						array_push($resId, array_keys($res['matches']));
					}
					
				//}
				 */
				return $resId;
			}

		}

		return null;
	}

	public function addSearchLog(SearchLog $log) {
		/** @var $one \Enp\Sphinx\SearchLog **/
		foreach($this->searchLogHeap as $key => $one) {
			// nie dodawaj jak juz jest ta trategia dodana
			if ($one->getStrategyClass() == $log->getStrategyClass()) {
				return;
			}
		}

		$this->searchLogHeap[] = $log;
	}
	
	public function getLastSearchLog() {
		if (count($this->searchLogHeap) <= 0) {
			return array();
		}

		$log = array_reverse($this->searchLogHeap);

		/** @var $last \Enp\Sphinx\SearchLog **/
		$last = array_shift($log);
		return $last;
	}

	public function getSearchLog() {
		return $this->searchLogHeap;
	}

	public function addStrategy(StrategyInterface $strategy) {
		$this->strategyHeap[] = $strategy;
	}

	public function removeStrategy($heapIndex) {
		unset($this->strategyHeap[$heapIndex]);
	}

	public function getStrategyAll() {
		return $this->strategyHeap;
	}

	public function setHost($host)
	{
		$this->host = $host;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function setIndexName($indexName)
	{
		$this->indexName = $indexName;
	}

	public function getIndexName()
	{
		return $this->indexName;
	}

	public function setPort($port)
	{
		$this->port = $port;
	}

	public function getPort()
	{
		return $this->port;
	}


}