<?php

namespace Enp\Admin\Controller;

/**
 * @category Enp
 * @package  Enp_Admin_Controller
 * @author   Piotr Flasza
 */
class Controller
{
	/**
	 * @var \Enp\Admin\Controller\Config
	 */
	protected $config = null;
	protected $templateDir = '';

	public function getTemplateDir()
	{
		return $this->templateDir;
	}

	public function setTemplateDir($templateDir)
	{
		$this->templateDir = $templateDir;
		$smarty = \SmartyObj::getInstance();
		$smarty->assign('controller_dir', $this->getTemplateDir());
	}

	/**
	 * @var \Enp\Db\Model
	 */
	protected $model = null;

	/**
	 * @return \Enp\Db\Model
	 */
	public function getModel($id = null)
	{
		$model = $this->config->getModel();

		if ((int) $id > 0) {
			$model->load($id);
		}

		return $model;
	}

	public function __construct(\Enp\Admin\Controller\Config $config = null)
	{
		$this->config = $config;
		$this->init();
	}

	/**
	 * uruchamiane w konstruktorze
	 */
	public function init()
	{
	}

	/**
	 * @return \Enp\Admin\Controller\Config
	 */
	public function getConfig()
	{
		return $this->config;
	}

	public function setConfig(\Enp\Admin\Controller\Config $config)
	{
		$this->config = $config;
	}

	/**
	 * Pobranie listy
	 */
	public function indexAction()
	{
		$smarty = \SmartyObj::getInstance();

		$filtr = $this->getFiltrCleared();

		$sort = $this->getSort();

		$limit = array(
			'start' => (int) $_GET['start'],
			'limit' => $this->config->getListLimit()
		);


		$lista = $this->getModel()->getAll($filtr, $sort, $limit);
		$ilosc = $this->getModel()->getAllIlosc($filtr);

		$smarty->assign('lista', $lista);
		$smarty->assign('ilosc', $ilosc);

		$smarty->assign('nav', \Common::getNavBar(
						$limit['start'], $ilosc, $limit['limit'], 10
		));
	}

	/**
	 * @return \Enp\Db\Model
	 */
	public function editAction()
	{
		$smarty = \SmartyObj::getInstance();
		$model = $this->getModel();

		$id = (isset($_GET['id']) && (int) $_GET['id'] > 0) ? (int) $_GET['id'] : 0;
		if ($id > 0) {
			$model->load($id);
		}
		$smarty->assign('obj', $model);
		$smarty->assign('linkPowrot', '?mod=' . $_GET['mod'] .
				'&sort=' . $_GET['sort'] .
				'&order=' . $_GET['order'] .
				\Common::filtr($_GET['filtr']
		));
		return $model;
	}

	public function saveAction()
	{
		$dane = $_POST['formData'];

		if (count($dane) > 0) {
			$dane = \Common::clean_input($dane);

			$id = (int) $dane['id'];
			unset($dane['id']);

			$id = $this->save($id, $dane);

			\Enp\Tool::setFlashMsg("Dane zostały zapisane", \Enp\Tool::INFO);
		} else {
			\Enp\Tool::setFlashMsg("Nie zdefiniowano danych do zapisu !", \Enp\Tool::ERROR);
		}

		\Enp\Tool::redirect(array(
			'mod' => $_GET['mod'],
			'act' => 'edit',
			'id' => $id
		));
	}

	protected function save($id, $dane)
	{
		$model = $this->getModel();

		if ($id > 0) {
			// update
			$model->update($dane, $id);
		} else {
			// insert
			$id = $model->insert($dane);
		}

		return $id;
	}

	public function deleteAction()
	{
		$id = (int) $_GET['id'];

		if ($id > 0) {
			/**
			 * usuwamy
			 */
			$this->getModel()->delete($id);
			\Enp\Tool::setFlashMsg("Dane zostały usuniete", \Enp\Tool::INFO);
		} else {
			\Enp\Tool::setFlashMsg("Nie zdefiniowano danych do usunięcia !", \Enp\Tool::ERROR);
		}

		\Enp\Tool::redirect(array(
			'mod' => $_GET['mod'],
			'act' => 'index'
		));
	}

	protected function getSort()
	{
		$sort = \Common::clean_input($_GET['sort']);
		$order = \Common::clean_input($_GET['order']);

		$res = array();
		if ($sort != '' && $order != '') {
			$res['sort'] = $sort;
			$res['order'] = $order;
		}

		return $res;
	}

	protected function getFiltr()
	{
		return \Common::clean_input($_GET['filtr']);
	}

	/**
	 * Czysci filtry z pustych wartosci
	 * @return array
	 */
	protected function getFiltrCleared()
	{
		$filtry = $this->getFiltr();

		foreach ($filtry as $key => $val) {
			if (empty($val)) {
				unset($filtry[$key]);
			}
		}

		return $filtry;
	}

	public function runAction($action)
	{
		/**
		 * Konwersja nazwy na actionName
		 */
		$action = str_replace(array('_', '-'), ' ', $action);
		$action = ucwords(strtolower($action));
		$action = str_replace(' ', '', $action);
		$action = lcfirst($action);

		$methodName = $action . 'Action';

		if (!method_exists($this, $methodName)) {
			throw new \Enp\Exception("Nie ma akcji $action w controllerze " . __CLASS__);
		}

		/**
		 * Uruchamiamy akcje
		 */
		$this->$methodName();

		return true;
	}

	public function setTemplateView($action)
	{
		/**
		 * Wyswietlamy widok
		 */
		$_template = $this->getTemplateDir() . get_class($this) . '/' . $action . '.tpl';

		$smarty = \SmartyObj::getInstance();
		$smarty->assign('_template', $_template);

		return true;
	}
}
