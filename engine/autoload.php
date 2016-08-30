<?php
/**
 * Automatycznie wczytywanie potrzebnych klas
 * - z katalogu ZEND_DIR,
 * - lub z katalogu MODELS_DIR
 *
 */
ini_set("include_path", LIBRARY_DIR);
require_once('Zend/Loader.php');

function __autoload($className)
{
	if (file_exists(CLASS_DIR.'class.'.$className.'.php')) {
		//echo "autoload $className<br />";
		require_once(CLASS_DIR.'class.'.$className.'.php');
	}
	else {
		
		try {
			Zend_Loader::loadClass($className,
								    array(
								        CLASS_DIR,
								        LIBRARY_DIR
								    )
								);
		}
		catch (Exception $e) {
			Common::log(__FUNCTION__,$e->getMessage());
			echo $e->getTraceAsString();
			return false;
		}
	}
}

?>