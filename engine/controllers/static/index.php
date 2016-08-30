<?php

if(file_exists('templates/_static/'.$atr2.'.php')) {
	$sub_template = $atr2.".php";
} else {
	header("Location: $wwwPatch");
}