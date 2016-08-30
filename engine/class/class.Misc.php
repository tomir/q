<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classMisc
 *
 * @author t.jurdzinski
 */
class Misc {
    static function secToMin($sec) {
		$min = $sec/60;
		$seconds = $sec-($min*60);
		$aTab = array();
		$aTab['min'] = $min;
		$aTab['sec'] = $seconds;
		return $aTab;
	}

	static function secTo24HourFormat($secodns_in) {
		$hours = floor($secodns_in/3600);
		$minutes = floor(($secodns_in - $hours*3600)/60);
		$seconds = (($secodns_in - $hours*3600) - $minutes*60);
		
		if($hours < 10) $hours = '0'.$hours;
		if($minutes < 10) $minutes = '0'.$minutes;
		if($seconds < 10) $seconds = '0'.$seconds;

		return $hours.':'.$minutes.':'.$seconds;
	}

	static function minToSec($minutes) {
		return $minutes*60;
	}

	static function dayNumerToShortName($number) {
		switch($number) {
			case 0:
				return 'N';
			case 1:
				return 'Pn';
			case 2:
				return 'Wt';
			case 3:
				return 'Śr';
			case 4:
				return 'Cz';
			case 5:
				return 'Pt';
			case 6:
				return 'So';
		}
	}

	static public function makeSlug($tekst, $text2 = '')
	{
		if($tekst == '' && $text2 != '')
			$tekst = $text2;
		$tekst = trim($tekst);
		

	   $tabela = Array(
	   //WIN
	    "\xb9" => "a", "\xa5" => "A", "\xe6" => "c", "\xc6" => "C",
	    "\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
	    "\xf3" => "o", "\xd3" => "O", "\x9c" => "s", "\x8c" => "S",
	    "\x9f" => "z", "\xaf" => "Z", "\xbf" => "z", "\xac" => "Z",
	    "\xf1" => "n", "\xd1" => "N",
	   //UTF
	    "\xc4\x85" => "a", "\xc4\x84" => "A", "\xc4\x87" => "c", "\xc4\x86" => "C",
	    "\xc4\x99" => "e", "\xc4\x98" => "E", "\xc5\x82" => "l", "\xc5\x81" => "L",
	    "\xc3\xb3" => "o", "\xc3\x93" => "O", "\xc5\x9b" => "s", "\xc5\x9a" => "S",
	    "\xc5\xbc" => "z", "\xc5\xbb" => "Z", "\xc5\xba" => "z", "\xc5\xb9" => "Z",
	    "\xc5\x84" => "n", "\xc5\x83" => "N",
	   //ISO
	    "\xb1" => "a", "\xa1" => "A", "\xe6" => "c", "\xc6" => "C",
	    "\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
	    "\xf3" => "o", "\xd3" => "O", "\xb6" => "s", "\xa6" => "S",
	    "\xbc" => "z", "\xac" => "Z", "\xbf" => "z", "\xaf" => "Z",
	    "\xf1" => "n", "\xd1" => "N");

	   	$tekst = strtr($tekst,$tabela);
	   	$tekst = str_replace(" (", "_", $tekst);
	   	$tekst = str_replace(" - ", "_", $tekst);
		$tekst = str_replace(" ", "_", $tekst);
		$tekst = str_replace(")", "", $tekst);
		$tekst = str_replace("\"", "", $tekst);
		$tekst = str_replace("%", "", $tekst);
		$tekst = str_replace("+", "", $tekst);
		$tekst = str_replace("–", "-", $tekst);
                
                $tekst = str_replace("&", "", $tekst);
		$tekst = str_replace("  ", " ", $tekst);
		$tekst = str_replace(",", "", $tekst);
		$tekst = str_replace("/", "", $tekst);
		$tekst = str_replace("'", "", $tekst);
		$tekst = str_replace(".", "", $tekst);
		$tekst = str_replace("\"", "", $tekst);
		$tekst = str_replace("?", "", $tekst);
		$tekst = str_replace("!", "", $tekst);
		$tekst = str_replace("„", "", $tekst);
		$tekst = str_replace("”", "", $tekst);
		$tekst = str_replace(":", "", $tekst);
		$tekst = str_replace("-", "", $tekst);
		$tekst = str_replace('"', "", $tekst);
		$tekst = str_replace('%', "", $tekst);
		$tekst = str_replace('ë', "e", $tekst);
	 	return strtolower(urlencode($tekst));
	}

	public static function cropName($string, $maxDlugosc, $surfix="...") {

		if($dl_string = strlen($string) > $maxDlugosc)
		{
			$dl_surfix = strlen($surfix);
			$string = mb_substr($string,0,($maxDlugosc-$dl_surfix+1), 'UTF-8');
			return $string.$surfix;
		}
		return stripslashes($string);
	}

	public static function showPrice($price) {
		return number_format(floatval($price), 2, ',', ' ');
	}

	public static function creditCalc($mc, $kwota) {
		//return $kwota * pow((1 + 0.16/12),-$mc);
		$Z = 1 / (1 + (0.16/12));
$x = ((1 - $Z) * $kwota*1.03) / ($Z * (1 - pow($Z,$mc)));
return round($x,2);
	}

}
?>
