<?php
namespace Sphinx;

interface StrategyInterface {
	public function query($query, \Sphinx\Search $search);
}