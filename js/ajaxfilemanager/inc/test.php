<?php
	include_once(dirname(__FILE__) . "/function.base.php");
	$path = 'C:\Inetpub\wwwroot\tinymce\jscripts\tiny_mce\plugins\ajaxfilemanager\inc';
	echo "current folder: ". dirname(__FILE__) . "<br>";
	echo "realpath: " .  realpath($path) . "<br>";
	echo "real relative path: " . realpath("") . "<br>";
?>