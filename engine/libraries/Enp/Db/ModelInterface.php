<?php

namespace Enp\Db;

interface ModelInterface {
	public function __construct($id);
	public function getAll($filtr, $sort, $limit);
	public function getAllIlosc($filtr);
	public function load($id);
	public function insert($data);
	public function update($data, $id = null);
	public function delete($id = null);
}

?>
