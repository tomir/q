<?php

namespace Enp;

/**
 * @package Enp
 */
class Tool
{
	/**
	 * definicja kluczy dla flash messangera
	 */
	const INFO = 'info';
	const ERROR = 'error';

	static public function setFlashMsg($message, $key = 'info')
	{
		$_SESSION['flashMessenger'][$key][] = $message;
	}

	static public function getFlashMsg($key = 'default')
	{
		$messages = array();
		if (isset($_SESSION['flashMessenger'][$key])) {
			$messages = $_SESSION['flashMessenger'][$key];
			$_SESSION['flashMessenger'][$key] = array();

			foreach ($messages as $k => $tekst) {
				$messages[$k] = \Flashmsg\Tool::getForTekst($tekst, $key);
			}

			return implode('<br />', $messages);
		}

		return '';
	}

	/**
	 * Czysci dane wejscowe ze znaków specjalnych i zastepuje je encjami htmlowymi
	 * 
	 * @author Piotr Flasza
	 * @param mixed $dirty
	 * @return mixed
	 */
	static public function clean($dirty)
	{
		if (!is_array($dirty)) {
			$dirty = htmlentities($dirty, ENT_QUOTES, 'UTF-8', false);
		} elseif (is_array($dirty)) {
			foreach ($dirty as $k => $v) {
				$dirty[$k] = self::clean($v); // rekurencja
			}
		}
		return $dirty;
	}

	/**
	 * @author Krzysztof Górecki
	 * @param array $inputArray
	 * @param string $key
	 * @return type 
	 */
	static public function arrayPluck(array $inputArray, $key)
	{
		$array = array();
		foreach ($inputArray as $v) {
			if (array_key_exists($key, $v))
				$array[] = $v[$key];
		}
		return $array;
	}

	/**
	 * Zaklada ze tablica jest assocjacyjna jezeli index pierwszego elementu to 0
	 * 
	 * @param array $array 
	 */
	static public function arrayIsAssoc(array $array)
	{
		return (array_keys($array) != array_keys(array_keys($array)));
	}

	static public function sendToBrowser($dataString, $fileName)
	{
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Transfer-Encoding: binary");
		header('Content-Type: application/force-download');
		header('Content-Length: ' . strlen($dataString));
		header('Content-disposition: attachment; filename=' . $fileName);

		echo $dataString;
		exit;
	}

	static public function deepCopy($object)
	{
		return unserialize(serialize($object));
	}

	static public function redirect($getParams = array(), $hash = '', $url = 'index.php')
	{

		if (count($getParams) > 0) {
			$url = $url . '?' . http_build_query($getParams);
		}

		header("Location: " . $url . $hash);
		exit();
	}

	static public function redirectBack($getParams = array(), $hash = '')
	{

		$url = $_SERVER['HTTP_REFERER'];

		$addToUrl = '';
		if (count($getParams) > 0) {
			$addToUrl = http_build_query($getParams);
		}

		if ($addToUrl != '') {
			if (strpos($url, '?') !== false)
				$url .= '&' . $addToUrl;
			else
				$url .= '?' . $addToUrl;
		}
		header("Location: " . $url . $hash);
		exit();
	}

	static public function isHttps()
	{
		if (isset($_SERVER['HTTPS'])) {
			if ($_SERVER['HTTPS'] == 'on') {
				return true;
			}
		}

		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
			if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
				return true;
			}
		}

		return false;
	}

	/**
	 * bierze pod uwage:
	 * stala SSL - config.serwisy.php
	 * stala LOCAL_SERVER - config/enviroment.php
	 * 
	 */
	static public function checkAndRedirectToHttps()
	{
		if (!self::isHttps() && SSL == 1 && LOCAL_SERVER == 0) {
			$x = $_SERVER['REQUEST_URI'];
			\Common::Redirect('https://' . $_SERVER['HTTP_HOST'] . $x);
		}
	}

	/**
	 * Działa jak array_merge z tą różnicą, że nie przepisuje nullów z nadpisującej na nadpisywaną tablice
	 * @param array $a tablica nadpisywana
	 * @param array $b tablica nadpisujaca
	 * @return array
	 */
	static public function mergeArraysNotNulls($a, $b)
	{
		$clearB = array_filter($b); //wyczysc tablice z nullow
		$clearA = array_filter($a); // j.w
		$result = array_merge($clearA, $clearB); //połącz tablice bez nullow

		$result = array_merge($b, $result); //jeśli nulle były tylko w jednej z tabel (nie było czym ich nadpisać), dodaj je spowrotem do wyników
		$result = array_merge($a, $result); // jw dla drugiej tablicy		
		return $result;
	}

}
