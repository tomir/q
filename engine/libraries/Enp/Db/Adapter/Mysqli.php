<?php

namespace Enp\Db\Adapter;

class Mysqli extends \Zend_Db_Adapter_Mysqli
{

	/**
	 * celowe napisanie walidacji wymaganyc marametrow w configu,
	 * poniewaz polaczenie nawiazywane jest w obiekcie Adodb
	 * i nie ma potrzeby przekazywac tutaj loginow hasel i adresow serwera
	 * 
	 * @param array $config
	 */
	protected function _checkRequiredOptions(array $config)
	{
		
	}

	/**
	 * Akcesor dla chronionej zmiennej _connection ktora zaiwra obiekt mysqli
	 * @param \mysqli $connection
	 * @throws \Exception
	 */
	public function setConnection(\mysqli $connection)
	{
		if (!$connection instanceof \mysqli) {
			throw new \Exception("Proba przypisania polaczenia które nie istnieje !!!");
		}
		$this->_connection = $connection;
	}

	/**
	 * Prosta implementacji metody _connect, ktora uniemozliwia ponowne polaczenie
	 * 
	 * @return void
	 * @throws \Exception
	 */
	protected function _connect()
	{
		if ($this->_connection) {
			return;
		}

		throw new \Exception("Nigdy nie powinno do tego dojsc. Polaczenie jest nawiazywane w obiekcie Adodb i przekazywane w postaci obiektu mysqli to Zen_Db");
	}

	/**
	 * Nadpisanie domyslnej metody sleep ktora czycic polaczenie
	 * co przy wspolpracy z tranzakcjami i adodb powoduje niemale problemy
	 */
	public function __sleep()
	{
		//return parent::__sleep();
	}

}
