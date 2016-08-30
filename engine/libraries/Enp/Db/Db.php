<?php

namespace Enp\Db;

/**
 * @category Enp
 * @package  Enp_Db
 * @author   Piotr Flasza
 */
class Db extends \Zend_Db
{
	static private $instance = null;
	static private $instanceSelect = null;
	static private $instanceNewCopy = null;

	/**
	 * Wskaznik do obslugi smart tranz
	 * @var int
	 */
	static private $tranzFlaga = 0;

	/**
	 * Jak przypiszesz tutaj obiekt Adodb to polaczenie do Zendowskiego polaczenia zostanie przepisane z Adodb
	 * Dzieki temu tranzakcja uruchomiona w Adodb to ta sama tranzakcja co w ZendDb,
	 * poniewaz dziaĹ‚amy na jedym polaczeniu z mysql
	 * 
	 * @var \Adodb
	 */
	static protected $adodb = null;

	static public function setAdodb(\Adodb $adodb)
	{
		self::$adodb = $adodb;
	}

	static public function setProfiler($enable)
	{
		$enable = (bool) $enable;
		self::getInstance()->getProfiler()->setEnabled($enable);
		self::getInstanceSelect()->getProfiler()->setEnabled($enable);
	}

	static public function getProfilerInfo()
	{
		$info = array();
		foreach (self::getInstance()->getProfiler()->getQueryProfiles() as $one) {
			/* @var $one \Zend_Db_Profiler_Query */
			$info[] = $one->getQuery() . "\nparams: " . var_export($one->getQueryParams(), true) . "\ntime " . $one->getElapsedSecs();
		}

		$info2 = array();
		foreach (self::getInstanceSelect()->getProfiler()->getQueryProfiles() as $one) {
			/* @var $one \Zend_Db_Profiler_Query */
			$info2[] = $one->getQuery() . "\nparams: " . var_export($one->getQueryParams(), true) . "\ntime " . $one->getElapsedSecs();
		}

		return "MASTER\n****\n" . implode("\n---\n", $info) . "SLAVE\n****\n" . implode("\n---\n", $info2);
	}

	/**
	 * Polaczenie do mastera
	 * 
	 * @return \Zend_Db_Adapter_Abstract
	 */
	static public function getInstance()
	{
		if (self::$instance == null) {

			$config = array(
				'fetchMode' => \Zend_Db::FETCH_ASSOC
			);

			$db = new Adapter\Mysqli($config);
			if (self::$adodb == null) {
				self::setAdodb(\Db::getInstance());
			}
			$db->setConnection(self::$adodb->_connectionID);

			\Zend_Db_Table::setDefaultAdapter($db);

			// --------------------
			// wylaczam cache PF
			// malo czytelny i jak ktos o nim nie wiem to sie pogubi
			// przy pracy w adminie
			// np dodoalem pole i ono sie nie zapisywalo, poniewaz w cachu byly metadata
			// *** moze wlaczac go na froncie tylko ,a w adminie go wylaczac ?!? ****
			// -------------------
			//\Zend_Db_Table::setDefaultMetadataCache(\Enp\Cache::get());

			self::$instance = $db;
		}

		return self::$instance;
	}

	/**
	 * Laczy się na osobnym polączeniu z baza,
	 * tworzy osobne polaczenie do bazy 
	 * 
	 * @global type $dbhost
	 * @global type $dbname
	 * @global type $dblogin
	 * @global type $dbpass
	 * @return \Zend_Db_Adapter_Pdo_Mysql
	 */
	static public function getInstanceNewCopy($force = false)
	{
		if (self::$instanceNewCopy == null || $force == true) {
			global $dbhost;
			global $dbname;
			global $dblogin;
			global $dbpass;

			$db = new \Zend_Db_Adapter_Pdo_Mysql(array(
				'host' => $dbhost,
				'username' => $dblogin,
				'password' => $dbpass,
				'dbname' => $dbname,
				'charset' => 'utf8',
				'fetchMode' => \Zend_Db::FETCH_ASSOC
			));

			self::$instanceNewCopy = $db;
		}

		return self::$instanceNewCopy;
	}

	/**
	 * Polaczenie do slajva
	 * 
	 * @return \Zend_Db_Adapter_Abstract
	 */
	static public function getInstanceSelect()
	{
		if (self::$instanceSelect == null) {

			$config = array(
				'fetchMode' => \Zend_Db::FETCH_ASSOC
			);

			$db = new Adapter\Mysqli($config);

			if (self::$adodb == null) {
				self::setAdodb(\Db::getInstance());
			}
			$db->setConnection(self::$adodb->getConnectionSelect()->_connectionID);


			self::$instanceSelect = $db;
		}

		return self::$instanceSelect;
	}

	static protected function beginTrans()
	{
		$db = self::getInstance();
		$db->beginTransaction();
	}

	static protected function commitTrans()
	{
		$db = self::getInstance();
		$db->commit();
	}

	static protected function rollbackTrans()
	{
		$db = self::getInstance();
		$db->rollBack();
	}

	/**
	 * Probuje uruchomic tranzakcje, ale jezeli jest ona juz uruchomiona to nic nie robi
	 */
	static public function startTrans()
	{

		try {

			if (self::$tranzFlaga > 0)
				throw new \Exception('Tranzakcja jest juz uruchomiona', 0);

			self::beginTrans();
			self::$tranzFlaga = 1;
		} catch (\Exception $e) {
			if ($e->getCode() === 0) {
				// tranzakcja juz jest uruchomiona
				self::$tranzFlaga++;
			} else {
				$e = new \Enp\Exception(__METHOD__ . ' >> ' . $e->getMessage(), $e->getCode(), $e->getPrevious());
				throw $e;
			}
		}
	}

	/**
	 * Probuje zatwierdzic tranzakcje, ale jezeli nie jest ona ostatnia trazackja na stosie to nic nie robi
	 */
	static public function completeTrans()
	{
		try {
			if (self::$tranzFlaga > 1)
				throw new \Exception('Tranzakcja nie jest glowna tranzakcja w kodzie', 0);

			self::commitTrans();
			self::$tranzFlaga = 0;
		} catch (\Exception $e) {
			if ($e->getCode() === 0) {
				// tranzakcja nie jest glowna tranzakcja
				self::$tranzFlaga--;
			} else {
				$e = new \Enp\Exception(__METHOD__ . ' >> ' . $e->getMessage(), $e->getCode(), $e->getPrevious());
				throw $e;
			}
		}
	}

	/**
	 * Czy tranzakcja jest wlasnie uruchomiona 
	 * 
	 * @return boolean
	 */
	static public function isTransOn()
	{
		return (self::$tranzFlaga >= 1);
	}

	/**
	 * wycofuje transakcje 
	 */
	static public function cancelTrans()
	{
		self::$tranzFlaga = 0;
		self::rollbackTrans();
	}

	/**
	 * @return \Zend_Db_Adapter_Mysqli
	 */
	static public function getRightInstance()
	{
		if (\Enp\Db\Db::isTransOn()) {
			$adapter = \Enp\Db\Db::getInstance();
		} else {
			$adapter = \Enp\Db\Db::getInstanceSelect();
		}
		return $adapter;
	}

	/**
	 * Executes a function inside a transaction.
	 *
	 * If an exception occurs during execution of the function or the completeTrans() call,
	 * the transaction is rolled back, and the exception re-thrown.
	 *
	 * @param Closure $func The function to execute transactionally.
	 * @return mixed Returns the non-empty value returned from the closure or true instead
	 * @throws \Exception if the passed function throws an exception, it's re-thrown
	 * 
	 * @example
	 * 
	 * \Enp\Db\Db::executeInTransaction(function() use ($model1, $model2) {
	 *   $model1->update(array('name' => 'Test 123'));
	 *   $model2->update(array('code' => 14));
	 * });
	 * 
	 * $result = array();
	 * 
	 * \Enp\Db\Db::executeInTransaction(function() use (&$result) {
	 *   $result[] = $model1->update(array('name' => 'Test 123'));
	 * });
	 * 
	 * var_dump($result);
	 * 
	 * $result = \Enp\Db\Db::executeInTransaction(function() use ($model1) {
	 *   $model1->update(array('name' => 'Test 123'));
	 * 
	 *   return $model1->getResult();
	 *
	 * });
	 */
	static public function executeInTransaction(Closure $func)
	{
		self::startTrans();

		try {
			$return = $func();

			self::completeTrans();

			return $return ? : true;
		} catch (Exception $e) {

			self::rollbackTrans();

			throw $e;
		}
	}

}
