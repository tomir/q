<?php
namespace Sphinx;

class SearchLog {

	protected $queryForUser = '';
	protected $queryForSphinx = '';
	protected $strategyClass = '';
	protected $iloscWynikow = '';

	public function __construct($queryForUser = '', $queryForSphinx = '', $strategyClass = '', $iloscWynikow = '' ) {
		$this->queryForSphinx	= $queryForSphinx;
		$this->queryForUser		= $queryForUser;
		$this->strategyClass 	= $strategyClass;
		$this->iloscWynikow		= $iloscWynikow;
	}

	public function setIloscWynikow($iloscWynikow)
	{
		$this->iloscWynikow = $iloscWynikow;
	}

	public function getIloscWynikow()
	{
		return $this->iloscWynikow;
	}

	public function setQueryForSphinx($queryForSphinx)
	{
		$this->queryForSphinx = $queryForSphinx;
	}

	public function getQueryForSphinx()
	{
		return $this->queryForSphinx;
	}

	public function setQueryForUser($queryForUser)
	{
		$this->queryForUser = $queryForUser;
	}

	public function getQueryForUser()
	{
		return $this->queryForUser;
	}

	public function setStrategyClass($strategyClass)
	{
		$this->strategyClass = $strategyClass;
	}

	public function getStrategyClass()
	{
		return $this->strategyClass;
	}
}