<?php
/**
 * Aktualizacja fraz wyszukiwania
 */

require('config.php');
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('memory_limit', '2000M');

$result = exec("/usr/local/sphinx/bin/indexer --all -c /usr/local/sphinx/etc/sphinx.conf --rotate/usr/local/sphinx/bin/indexer --all -c /usr/local/sphinx/etc/sphinx.conf --rotate");
echo $result;

?>