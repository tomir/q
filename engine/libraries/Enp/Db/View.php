<?php

namespace Enp\Db;

abstract class View extends \Enp\Db\Model
{

	public function delete($id = null)
	{
		throw new \Exception("Niedozwolone dla widoku");
	}

	public function deleteWhere($where)
	{
		throw new \Exception("Niedozwolone dla widoku");
	}

	public function insert($data)
	{
		throw new \Exception("Niedozwolone dla widoku");
	}

	public function save()
	{
		throw new \Exception("Niedozwolone dla widoku");
	}

	public function set($pole, $wartosc, $id = null)
	{
		throw new \Exception("Niedozwolone dla widoku");
	}

	public function setOnOff($pole, $id = null)
	{
		throw new \Exception("Niedozwolone dla widoku");
	}

	public function update($data, $id = null)
	{
		throw new \Exception("Niedozwolone dla widoku");
	}

}