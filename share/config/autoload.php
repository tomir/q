<?php

function autoload($className) {

	$classPath = explode('\\', $className);

	if (count($classPath) > 3) {
		// Maximum class file path depth in this project is 3.
		$classPath = array_slice($classPath, 0, 3);
	}
	$filePath = $_SERVER['DOCUMENT_ROOT'] . '/konkurs/app/' . implode('/', $classPath) . '.php';

	if (file_exists($filePath)) {
		require_once($filePath);
	} else {
	
		$filePath = $_SERVER['DOCUMENT_ROOT'] . '/konkurs/vendor/' . implode('/', $classPath) . '.php';

		if (file_exists($filePath)) {
			require_once($filePath);
		} else {
            $classPath = explode('_', $className);
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/konkurs/vendor/' . implode('/', $classPath) . '.php';

            if (file_exists($filePath)) {
                require_once($filePath);
            }
        }
	}
}

spl_autoload_register('autoload');
