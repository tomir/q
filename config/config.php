<?php

error_reporting(E_ERROR);
ini_set('display_errors', 0);


define('APPLICATION_DIR',$_SERVER['DOCUMENT_ROOT'].'/konkurs/');

//define('APPLICATION_DIR','/var/www/clients/client1/web144/');
define('PATH',APPLICATION_DIR.'/');

define('CLASS_DIR', APPLICATION_DIR.'engine/class/');
define('CONTROLLERS_DIR', APPLICATION_DIR.'engine/controllers/');
define('LIBRARY_DIR', APPLICATION_DIR.'engine/libraries/');
define('TEMPLATES_DIR', APPLICATION_DIR.'templates/');
define('LOG_DIR', APPLICATION_DIR.'log/');
define('IMAGE_DIR', APPLICATION_DIR.'temp/product_images/');
define('ADODB_CACHE_DIR', APPLICATION_DIR.'temp/ADODB_CACHE_DIR/');

define('_TEMP_DIR', APPLICATION_DIR.'temp/');

define('_SMARTY_DIR', LIBRARY_DIR.'Smarty/');
define('_SMARTY_TEMPLATES_DIR', APPLICATION_DIR.'templates/');
define('_SMARTY_COMPILE_DIR', _TEMP_DIR.'templates_pl_c/');

define('ZEND_DIR',LIBRARY_DIR.'Zend/');
define('PEAR_DIR',LIBRARY_DIR.'pear/' );

define('SSL',0 );

ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.LIBRARY_DIR );
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.CLASS_DIR );
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.LIBRARY_DIR.'Facebook/' );

require_once( LIBRARY_DIR.'adodb/adodb-exceptions.inc.php');
require_once( LIBRARY_DIR.'adodb/adodb.inc.php' );
require_once( LIBRARY_DIR.'facebookv2/facebook.php' );

require_once('autoload.php');
require_once('config.serwis.php');

date_default_timezone_set("Europe/Warsaw");