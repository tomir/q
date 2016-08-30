<?php

define('DOMENA1', 'liderzyit.pl');
define('DOMENA2', 'auto-licytacje.pl');
define('DOMENA3', 'licytacje.auto.pl');

$x = $_SERVER['REQUEST_URI'];

if(strstr($_SERVER['HTTP_HOST'], DOMENA2) || strstr($_SERVER['HTTP_HOST'], DOMENA3)) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://".DOMENA1.$x); 
}

?>
