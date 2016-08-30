<?php

namespace Enp\Admin\Controller;

class Config {

	protected $model = null;
	protected $listLimit = 50;
	
	/**
	 * Metoda uruchamiana w konstruktorze
	 * Powinna zawierac wszystkie wpisy konfiguracyjne
	 */
	protected function init() {
		
	}

	public function __construct() {
		$this->init();
	}

	/**
	 * @return \Enp\Db\Model
	 */
	public function getModel() {
		return $this->model;
	}

	public function setModel(\Enp\Db\ModelInterface $model) {
		$this->model = $model;
	}

	public function getListLimit() {
		return $this->listLimit;
	}

	public function setListLimit($listLimit) {
		$this->listLimit = $listLimit;
	}
	
}

?>
