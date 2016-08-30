<?php

namespace Enp\Db;

abstract class ModelTree extends \Enp\Db\Model {

	/**
	 * pole ktore jest id nadrzednej galezi drzewa
	 *
	 * @var string
	 */
	protected $_id_nadrzedne = 'id_nadrzedne';
	protected $_cacheLifetime = 300;

	/**
	 * @param int $time
	 */
	public function setCacheLifetime($time) {
		$this->_cacheLifetime = $time;
	}
	
	/**
	 * @param int $idNadrzedne
	 */
	public function setIdNadrzedne($idNadrzedne) {
		$this->_id_nadrzedne = $idNadrzedne;
	}

	public function update($data, $id = null) {

		if ($id === null) {
			$id = $this->id;
		}

		// czy id nadrzednej to to samo id co aktualnie aktualizowane
		if ($id == $data[$this->_id_nadrzedne]) {
			throw new \Enp\Db\ModelTree\DependencyException("Nie mozna zapisac zmian, wiersz nie moze byc przypisany do siebie samego");
		}
		return parent::update($data, $id);
	}

	public function delete($id = null) {

		if ($id === null) {
			$id = $this->id;
		}

		// czy posiada on kategorie podrzedne
		$lista = $this->getAll(array($this->_id_nadrzedne => $id));
		if (count($lista) > 0) {
			throw new \Enp\Db\ModelTree\DependencyException("Nie mozna usunac wiersza poniewaz sa do niego przypisane inne wiersze");
		}
		parent::delete($id);
	}

	/**
	 * Zwraca wszystkie aktywne kategorie w formie
	 * listy select
	 *
	 */
	public function getListSelect($idNadrzednej = 0, $filtr = null, $sort = null, $maxLevel = 1000) {
		$kat = $this->getTreeForIdNadrzedne($idNadrzednej, $filtr, $sort, 0, $maxLevel);

		$result = array();

		$result = $this->rekurencja($kat, $result);

		return $result;
	}

	/**
	 * @param int $idNadrzedne
	 * @param array $filtr
	 * @param array $sort
	 * @param int $level
	 * @param int $maxLevel
	 * @return array
	 */
	public function getTreeForIdNadrzedne($idNadrzedne = 0, $filtr = null, $sort = null, $level = 0, $maxLevel = 1000) {
		
		$cacheid = 'tree_get_tree_for_id_nadrzedne_' . $this->getCacheHash(func_get_args());
		$cache = \Enp\Cache::get();
		
		$result = array();
		if (($result = $cache->load($cacheid)) === false) {
			
			$result = $this->getTreeForIdNadrzedneRecur($idNadrzedne, $filtr, $sort, $level, $maxLevel);
			
			$cache->save($result, $cacheid, array(), $this->_cacheLifetime);
		}

		return $result;
	}

	protected function getTreeForIdNadrzedneRecur($idNadrzedne = 0, $filtr = null, $sort = null, $level = 0, $maxLevel = 1000) {
		$result = array();
		if ($level < $maxLevel) {
			$filtr[$this->_id_nadrzedne] = $idNadrzedne;
			$lista = $this->getAll($filtr, $sort);
			
			$separator = '';

			if ($level > 0) {
				$separator = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
				$separator .= '|-';
			}
			foreach ($lista as $one) {
				$result[$one['id']] = $one;
				$result[$one['id']]['separator'] = $separator;
				$result[$one['id']]['podrzedne'] = $this->getTreeForIdNadrzedneRecur($one['id'], $filtr, $sort, $level + 1, $maxLevel);
			}
		}
		return $result;
	}


	private function rekurencja($tree, $result = array()) {

		foreach ($tree as $key => $one) {

			$result[$key] = $one['separator'] . $this->getAllSelectString($one);
			if (count($one['podrzedne']) > 0) {
				$result = $this->rekurencja($one['podrzedne'], $result);
			}
		}

		return $result;
	}

	/**
	 * Pobiera wszystkie kategorie podrzedne dla kategorii
	 * 
	 * dla "RTV" beda :
	 * - telewizory
	 *		- lcd
	 *		- led
	 * - wieze
	 * - stoliki
	 * 
	 * @param int $idKat
	 * @return array
	 */
	public function getArrayOfIdPodrzednych($idKat) {

		$cacheid = 'tree_get_array_of_id_podrzednych_' . $this->getCacheHash(func_get_args());
		$cache = \Enp\Cache::get();
		$result = $cache->load($cacheid);

		if (false !== $result) {
			return $result;
		}

		$result = $this->getArrayOfIdPodrzednychRecur($idKat);
		$result = array_unique($result);
		
		$cache->save($result, $cacheid, array(), $this->_cacheLifetime);

		return $result;
	}

	protected function getArrayOfIdPodrzednychRecur($id) {
		$ids = $this->getCol('x.' . $this->_firstPrimaryKey, array(
			$this->_id_nadrzedne => $id
		));

		foreach ($ids as $id) {
			$ids2 = $this->getArrayOfIdPodrzednychRecur($id);
			$ids = array_merge($ids, $ids2);
		}

		return $ids;
	}

	
	/**
	 * Pobiera wszystkie kategorie w gore dla podanej kategorii
	 * 
	 * dla "tv lcd" beda:
	 *			- telewizory
	 *		- rtv
	 * - rtv i agd
	 * 
	 * @param int $idKat
	 * @return array
	 */
	public function getArrayOfIdSciezka($idKat) {

		$cacheid = "get_array_of_id_sciezka_" . $idKat;
		$cache = \Enp\Cache::get();
		$result = $cache->load($cacheid);
		
		if (false !== $result) {
			return $result;
		}

		$result = $this->getArrayOfIdSciezkaRecur($idKat);

		$cache->save($result, $cacheid, array(), $this->_cacheLifetime);

		return $result;
	}

	protected function getArrayOfIdSciezkaRecur($id) {

		$ids = array();

		if ($id > 0) {

			$ids[] = $id;

			$idNad = $this->getCol('x.' . $this->_id_nadrzedne, array(
				'x.' . $this->_firstPrimaryKey => $id
					));
			
			$idNadrzedne = (int) $idNad[0];

			$ids2 = $this->getArrayOfIdSciezkaRecur($idNadrzedne);

			$ids = array_merge($ids, $ids2);
		}

		return $ids;
	}
}