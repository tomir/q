<?php

namespace Enp\DataStructures; 

use Doctrine\Common\Collections\ArrayCollection;

class PrevNextLink {
	
	/**
	 * @var ArrayCollection
	 */
	protected $_elementsIds;

	/**
	 * @var int
	 */
	protected $_currentId = null;
	
	/**
	 * @param array $listId
	 */
	public function __construct(array $listId) {
		$this->_elementsIds = new ArrayCollection();

		foreach ($listId as $id) { 
			$this->_elementsIds->add((int) $id);
		}
	}
	
	/**
	 * @param int $id
	 */
	public function setCurrentId($id) {
		if (!is_numeric($id)) {
			throw new Exception(sprintf("Podano bledny parametr ID, wymagany INT, podano %s", gettype($id)));
		}
		$this->_currentId = (int) $id;
	}
	
	/**
	 * @return mixed|null
	 */
	public function getNextId() {
		$index = $this->_getCurrentKey();
		if (null === $index) {
			return null;
		}
		if (!$this->_elementsIds->containsKey($index + 1)) {
			return null;
		}
		return $this->_elementsIds->get($index + 1);
	}
	
	/**
	 * @return mixed|null
	 */
	public function getPrevId() {
		$index = $this->_getCurrentKey();
		if (null === $index) {
			return null;
		}
		if (!$this->_elementsIds->containsKey($index - 1)) {
			return null;
		}
		return $this->_elementsIds->get($index - 1);
	}

	/**
	 * @return mixed|false
	 */
	protected function _getCurrentKey() {
		if (null === $this->_currentId) {
			return false;
		}
		$index = $this->_elementsIds->indexOf($this->_currentId);
		if (false === $index) {
			return false;
		}
		return $index;
	}
}