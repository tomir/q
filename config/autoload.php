<?php

/**
 * Automatycznie wczytywanie potrzebnych klas
 * - z katalogu ZEND_DIR,
 * - lub z katalogu MODELS_DIR
 *
 */
require_once('Zend/Loader.php');

function autoloadZend2()
{
    try {
        require LIBRARY_DIR . 'Zend2/Zend/Loader/StandardAutoloader.php';
        require LIBRARY_DIR . 'Enp/Loader/ENPLoader.php';
        $loader = new Enp\Loader\ENPLoader(array(
			'autoregister_zf' => false,
			'fallback_autoloader' => true,
			'namespaces' => array(
				'Zend' => LIBRARY_DIR . 'Zend2/Zend/',
			),
		));
        $loader->register();
    } catch (Exception $e) {
        Common::log(__FUNCTION__, $e->getMessage());
        return false;
    }
}

autoloadZend2();

function d($mix)
{
    Common::debug($mix);
}
function de($mix)
{
    Common::debuge($mix);
}
