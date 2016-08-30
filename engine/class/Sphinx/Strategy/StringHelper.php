<?php
namespace Sphinx\Strategy;

class StringHelper {

	/**
	 * Oddziela w slowach litery od cyfr
	 * DX435 => DX 435
	 *
	 * @param string $query
	 * @return string
	 */
	static public function breakQueryToAlfaAndDigits($query) {

		$query = preg_replace('/([^0-9,\.])([0-9])/i','$1$2',$query);
		$query = preg_replace('/([0-9])([^0-9,\.])/i','$1$2',$query);
		$query = str_replace("-", "", $query);
		$query = str_replace("\'", " ", $query);
		
		return $query;
	}
	
	static public function cutLastLetter($query, $ile = -2) {

		$explode = explode(" ", $query);
		
		if(is_array($explode) && count($explode) > 0) {
			foreach($explode as $word) {
				if(!is_numeric(mb_substr($word, -1)) && !is_numeric(mb_substr($word, -2, 1)) && mb_substr($word, -1) != '%' && strlen($word) > 3) {
					
					if(strlen($word) < 5) $ile2 = -1;
					else $ile2 = $ile;
					
					$aWords[] = mb_substr($word, 0, $ile2, 'utf8')."*"; 
				} elseif(strlen($word) == 2 && is_numeric(mb_substr($word, -2, 1)) && !is_numeric(mb_substr($word, -1)) ) {
					$aWords[] = $word."*";
				} else {
					$aWords[] = $word;
				}
			}
	
			return implode(" ", $aWords);
		} else {
			return mb_substr($query, 0, $ile, 'utf8')."*"; 
		}
		
		return $query;
	}

	/**
	 * Dodoaje gwiazdki do slow dluzszych niz $minLenWithStars
	 * "czesc i czolem" => "*czesc* i *czolem*"
	 *
	 * @param string $query
	 * @param int $minLenWithStars
	 * @return string
	 */
	static public function addStarsToWords($query, $minLenWithStars) {

		$explode = explode(" ",$query);

		$res = array();
		foreach($explode as $word) {
			$word = trim($word);
			if ($word != '') {
				if (strlen($word) >= $minLenWithStars && preg_match('/^[a-zA-Z]+$/',$word)) {
					$word = '*'.$word.'*';
				}
				$res[] = $word;
			}
		}

		$query = implode(" ", $res);

		return $query;
	}

	/**
	 * Rozbija query na slowa
	 *
	 * @param $query
	 * @return array
	 */
	static public function breakToWords($query) {

		$words = explode(' ', $query);

		$res = array();
		foreach($words as $key => $word) {
			$word = trim($word);
			if (strlen($word) > 2) {
				$res[] = $word;
			}
		}

		return $res;
	}

	/**
	 * usuwa spacje pomiedzy niektorymi kombinacjami liter/znakow/cyfr,...
	 *
	 * @param string $query
	 * @return string
	 */
	static public function removeSpacesBetween($query) {

		$query = preg_replace('/( )+%/','%',$query);

		return $query;
	}
	
	static public function getSpaceCombination($query) {
		
		$words = explode(' ', trim($query));
		
		$res = array();
		foreach($words as $key => $word) {
			$word = trim($word);
			$temp = '';
			
			foreach($words as $key2 => $word2) {
				
				if($key2 != $key || strlen($word2) < 2) {
					$temp .= $word2;
				} else {
					$temp .= " ".$word2." ";
				}
			}
			
			$res[] = trim($temp);
			$res[] = trim(self::cutLastLetter($temp));

		}
		
		$res[] = '*'.implode("", $words).'*';

		return $res;
		
	}
	
	static public function getWordCombination($query) {
		
		$words = explode(' ', trim($query));
	
		$res = array();
		if($words && is_array($words) && count($words) > 1) {
			foreach($words as $key => $word) {
				$word = trim($word);
				$temp = '';

				foreach($words as $key2 => $word2) {

					if($key2 != $key) {
						$temp .= " ".$word2;
					} 
				}

				$res[] = trim($temp);

			}

			return $res;
		} else {
			return $query;
		}
		
	}

}