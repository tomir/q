<?php

namespace Enp;

class Csv {

	const FILTER_FOR_COLUMN_NAME = 'column.name.filter';

	/**
	 * Zmienna przechowuje roboczy wyglad zawratosci csv
	 * @var array
	 */
	protected $_csvArray = array();

	/**
	 * Kodowanie pliku/stringu csv
	 * @var string
	 */
	protected $charsetFrom 			= 'cp1250';

	/**
	 * Kodowanie danych wykorzystywanych w aplikacji
	 * @var string
	 */
	protected $charsetTo 			= 'utf-8';

	/**
	 * Znak oddzielajacy wiersze w pliku/stringu csv
	 * @var string
	 */
	protected $separator 			= ';';
	protected $lineSeparator 		= "\n";
	/**
	 * Funkcja odpowiedzialna za filtrowanie kolumn
	 * @var $methodName
	 */
	protected $assocColumnInflector = null;

	protected $filters = array();

	public function __construct(\Closure $inflectorFunction = null) {

		if ($inflectorFunction === null) {
			$inflectorFunction = function ($string) {
				$string = mb_strtolower(trim($string));
				$string = preg_replace('/(\.)+$/','',$string); // usuwa kropki na koncach
				$string = preg_replace('/^([0-9])+,([0-9]+)&/', '$1.$2', $string); // konwertuje liczby z przecinkiem na liczby z kropka
				$string = preg_replace('/[^a-zA-Z0-9_-]/','-',$string); // inne znaki niz litery i cyfry
				return \Doctrine\Common\Util\Inflector::tableize($string);
			};
		}

		$this->assocColumnInflector = $inflectorFunction;

		$this->init();
	}

	/**
	 * Metoda inicjujaca ustawienia tej klasy
	 * np : filtry , kodowanie plikow, columnInlector
	 */
	public function init() {

	}



	public function loadCsvFromFile($csvFilePath)
	{
		if (!file_exists($csvFilePath)) {
			throw new \Exception("nie znaleziono pliku : $csvFilePath");
		}
		
		$result = array();
		//$row = 0;
		if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, $this->separator)) !== FALSE) {

				// kodowanie
				$data = $this->convertCharsets($data, $this->charsetFrom, $this->charsetTo);

				$result[/*$row*/] = $data;
				//$row++;
			}
			fclose($handle);
		}
		$this->_csvArray = $result;
	}

	public function loadCsvFromString($csvText)
	{
		$result = array();
		$wiersze = explode($this->lineSeparator, $csvText);
		//$row = 0;
		foreach ($wiersze as $wiersz) {
			$data = explode($this->separator, $wiersz);

			// kodowanie
			$data = $this->convertCharsets($data, $this->charsetFrom, $this->charsetTo);

			$result[/*$row*/] = $data;
			//$row++;
		}

		$this->_csvArray = $result;
	}


	public function getAssocArrayFromCsv($filtred = true) {
		$result = array();

		$kolumny = $this->_csvArray[0];
		foreach ($kolumny as $key => $one) {
			$kolumny[$key] = $this->getColumnName($one);
		}

		for ($i = 1; $i < count($this->_csvArray); $i++) {
			$result[$i - 1] = array_combine($kolumny, $this->_csvArray[$i]);
		}

		if ($filtred == true) {
			$result = $this->getAssocArrayFiltred($result);
		}

		return $result;
	}

	protected function getAssocArrayFiltred($resultArrayAssoc) {

		foreach($resultArrayAssoc as &$row) {
			$row = $this->getAssocArrayRowFiltred($row);
		}

		return $resultArrayAssoc;
	}
	protected function getAssocArrayRowFiltred($rowAssoc) {
		foreach($rowAssoc as $column => $value) {
			if (isset($this->filters[$column]) && is_array($this->filters[$column])) {
				foreach($this->filters[$column] as $filter) {
					$rowAssoc[$column] = $filter->filter($value);
				}
			}
		}

		return $rowAssoc;
	}

	public function getArrayFromCsv() {
		return $this->_csvArray;
	}


	public function getCsvStringFromArray($array)
	{
		if (!isset($array[0])) {
			throw new \Exception("Dane wejsciowe powinny byc w postaci tablicy indeksowanej od zera. Element array[0] nie istnieje !");
		}

		$csv = '';
		$columns = array_keys($array[0]);
		$isAssoc = \Enp\Tool::arrayIsAssoc($array[0]);

		if ($isAssoc) {
			// pobranie z pierwsego wiersza nazw kolumn
			$columns = $this->convertCharsets($columns, $this->charsetTo, $this->charsetFrom);
			foreach ($columns as $key => $column) {
				$columns[$key] = $this->getColumnName($column);
				$csv .= $columns[$key].$this->separator;
			}
			$csv .= $this->lineSeparator;
		}

		// zawartosc
		foreach ($array as $key=>$row) {
			
			$row = $this->convertCharsets($row, $this->charsetTo, $this->charsetFrom);
			if ($isAssoc) {
				$row = array_combine($columns, array_values($row));
				$row = $this->getAssocArrayRowFiltred($row);
			}
			foreach ($row as $col=>$val) {
				$val = str_replace(array("\r\n","\n"),"<br>",$val);
				$val = html_entity_decode($val, ENT_QUOTES);
				$csv .= $val.$this->separator;
			}
			$csv .= $this->lineSeparator;
		}
		
		return $csv;
	}


	protected function getColumnName($name) {

		if (isset($this->filters[self::FILTER_FOR_COLUMN_NAME]) && is_array($this->filters[self::FILTER_FOR_COLUMN_NAME])) {
			foreach($this->filters[self::FILTER_FOR_COLUMN_NAME] as $filter) {
				$name = $filter->filter($name);
			}
		}

		if ($this->assocColumnInflector != null) {
			$name = call_user_func($this->assocColumnInflector, $name);
		}

		return $name;
	}

	protected function convertCharsets($data, $from, $to)
	{
		foreach ($data as $k => $v) {
			$v = trim($v);
			$vAfter = iconv($from, $to, $v);
			
			/**
			 * Na wypadek gdyby nie udalo sie przekonwertowac.
			 */
			if ($v != '' && $vAfter == '') {
				$vAfter = $v;
			}
			
			$data[$k] = $vAfter;
		}

		return $data;
	}



	/**
	 * Dodanie filtru dla danej kolumny lub kolumn
	 *
	 * @param Zend_Filter_Interface $zendFilter
	 * @param mixed                $columnName
	 */
	public function addFilter(\Zend_Filter_Interface $zendFilter, $columnName = array()) {
		if (is_string($columnName)) {
			$columnName = (array) $columnName;
		}

		foreach($columnName as $column) {
			$this->filters[$column][spl_object_hash($zendFilter)] = $zendFilter;
		}
	}

	/**
	 * Usuwa filtry dla danej kolumny lub kolumn
	 *
	 * @param mixed $columnName
	 */
	public function removeFilter($columnName) {
		if (is_string($columnName)) {
			$columnName = (array) $columnName;
		}
		foreach($columnName as $column) {
			unset($this->filters[$column]);
		}
	}

	public function getFilters() {
		return $this->filters;
	}



	/**
	 * @param  $assocColumnInflector
	 */
	public function setAssocColumnInflector($assocColumnInflector)
	{
		$this->assocColumnInflector = $assocColumnInflector;
	}

	/**
	 * @return
	 */
	public function getAssocColumnInflector()
	{
		return $this->assocColumnInflector;
	}



	public function setCharsetForCsv($charsetCsv)
	{
		$this->charsetFrom = $charsetCsv;
	}

	public function getCharsetForCsv()
	{
		return $this->charsetFrom;
	}

	public function setCharsetToData($charsetToData)
	{
		$this->charsetTo = $charsetToData;
	}

	public function getCharsetToData()
	{
		return $this->charsetToData;
	}

	public function setSeparator($separator)
	{
		$this->separator = $separator;
	}

	public function getSeparator()
	{
		return $this->separator;
	}

	public function setLineSeparator($lineSeparator)
	{
		$this->lineSeparator = $lineSeparator;
	}

	public function getLineSeparator()
	{
		return $this->lineSeparator;
	}


}
