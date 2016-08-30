<?php

class Common
{ 
	static public function redirect($url)
	{
		header("Location: ".$url);
		exit();
	}

	static public function redirectBack($addToUrl = '')
	{
		$url = $_SERVER['HTTP_REFERER'];
		if ($addToUrl != '') {
			if (strpos($url,'?') !== false)
				$url .= '&'.$addToUrl;
			else
				$url .= '?'.$addToUrl;
		}
		header("Location: ".$url);
		exit();
	}

	static public function debug($mix)
	{
		//if ($_SERVER['REMOTE_ADDR'] == '109.95.237.208' || $_SERVER['REMOTE_ADDR'] == '83.15.149.52' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
			echo '<pre style="text-align:left;font-family:courier;font-size:10px;">';
			print_r($mix);
			echo '</pre>';
		//}
	}

	static public function debugForUser($mix)
	{
		echo '<pre style="color:#ff9600; display:block; width:400px; align:center; padding:10px; font-size:10px; border: 1px solid #ff0000">';
		print_r($mix);
		echo '</pre>';
	}

	static public function log($temat,$msg,$exit = false,$mailFlag = false)
	{
		if( !is_dir( 'logCommon' ) )
			mkdir( 'logCommon' );

		$filename = PATH.'logCommon/common_log'.date('Y_m_d-H').'.txt';	//d($filename);		
		$writerFile = new Zend_Log_Writer_Stream($filename);
		/*$mail = new Zend_Mail('utf-8');
		$writerMail = new MyZend_Log_Writer_Mail(
												$mail,
												array(MAIL_TO),
												array(
													'subject'=>$temat,
													'fromMail'=>'log@eurodom.pl',
													'fromName'=>'Eurodom Log'
													)
												);*/
		$logger = new Zend_Log();

		$logger->addWriter($writerFile);
		if ($mailFlag == true) {
			$logger->addWriter($writerMail);
		}

		@$logger->log($temat."\n".$msg,Zend_Log::ALERT );

		if ($exit) exit();
	}

	static public function log2($temat,$msg,$exit = false,$mailFlag = false)
	{
		self::log(print_r($temat,1),$msg,$exit,$mailFlag);
	}

	static function getNavBar( $startItem, $itemsQty, $perPage, $pagesQty )
	{
		if( $pagesQty%2 == 0 )
	    	$pagesQty++;

		$tabData = array();
		$tabData['itemsQty'] = $itemsQty;
		$tabData['pageCount'] = ceil( $itemsQty / $perPage );
		$tabData['currentPage'] = ( $startItem / $perPage )+1;
		$tabData['currentFrom'] = $startItem + 1;
		$tabData['currentTo'] = $startItem + $perPage <= $itemsQty ? $startItem + $perPage : $itemsQty;
		$tabData['nextPageStart'] = ( $startItem + $perPage <= $itemsQty ? $startItem + $perPage :(  $tabData['pageCount'] - 1 ) * $perPage );
		$tabData['prevPageStart'] = ( $startItem - $perPage >= 0 ? $startItem - $perPage : 0 );
		$tabData['firstPageStart'] = 0;
		$tabData['lastPageStart'] = (  $tabData['pageCount'] - 1 ) * $perPage;

	    // poniewaz numeracja jest od 0 dlatego floor
	    $iloscWszystkichStron = floor($itemsQty / $perPage);

	    $aktualna = ( $startItem / $perPage );
	    $plusminus = floor($pagesQty/2);

	    $pierwsza = $aktualna - $plusminus;
	    $ostatnia = $aktualna + $plusminus;

	    if ($pierwsza <0 ) {
	    	$ostatnia = $ostatnia + abs($pierwsza);
	    	$pierwsza = 0;
	    }

	    if ($ostatnia > $iloscWszystkichStron) {
	    	$pierwsza = $pierwsza - ($ostatnia - $iloscWszystkichStron);
	    	if ($pierwsza <0) $pierwsza = 0;
	    	$ostatnia = $iloscWszystkichStron;
	    }

	    if( $tabData['itemsQty'] / $tabData['pageCount'] == $perPage )
	    	$ostatnia -= 1;
	    
	    for( $i = $pierwsza; $i <= $ostatnia; $i++ )
	    {
	    	$tabData['pagesData'][$i]['startItem'] = $i * $perPage;
	        $tabData['pagesData'][$i]['pageNumber'] = $i+1;
	    }

	    return $tabData;
	}

	static public function filtr($filtr)
	{
		$filtr_out = '';

		if( !is_array($filtr) )
			return $filtr_out;

		while( list($key, $val)=each($filtr) )
		{
			if( $key!='mod' && $key!='sort' && $key!='order' )
				$filtr_out.= '&filtr['.$key.']='.$val;
		}

		return $filtr_out;
	}

	/**
	 * Zwraca tablice gotowa do wygenerowania selecta z okresami
	 *
	 * @return unknown
	 */
	static public function okresyGetSelect()
	{
		$timestampTemp = mktime(0,0,0,date('n')-1,date('j'),date('Y'));
		$dayInPastMon = date('t',$timestampTemp);

		$result = array( 	'0' => 'Dzisiaj',
							'1' => 'Wczoraj',
							'0-'.(date('w')-1) => 'Ten tydzien', // 0 dla niedzieli , 6 dla soboty
							date('w').'-'.(date('w')+7-1) => 'Poprzedni tydzien',
							'0-'.(date('j')-1) => 'Ten miesiac',
							date('j').'-'.(date('j')+$dayInPastMon-1) => 'Poprzedni miesiac',
							);
		return $result;
	}

	/**
	 * Przeksztalca jedna z wartosci selecta Common::okresyGetSelect
	 * Na wartosci data_od i data_do jakie nalezy dolaczyc do zapytania sql
	 *
	 * @param okres(Common::okresyGetSelect) $strOkres
	 * @return array
	 */
	static public function okresConvert($strOkres)
	{
		$tab = split("-",$strOkres);
		if (count($tab) < 2)
			$tab[] = $tab[0];

		$result = array();

		foreach ($tab as $dni) {
			$result[] = self::makeData(mktime(0,0,0,date('n'),date('j')-$dni,date('Y')));
		}

		return $result;
	}

	/**
	 * Zamienia znacznik czasowy timestamp na reprezentacje daty w postaci stringa
	 *
	 * @param timestamp $timestamp
	 * @return string
	 */
	static public function makeData($timestamp)
	{
		$d = getdate($timestamp);

		if ($d['mon'] < 10) $d['mon'] = '0'.$d['mon'];
		if ($d['mday'] < 10) $d['mday'] = '0'.$d['mday'];

		return $d['year'].'-'.$d['mon'].'-'.$d['mday'];
	}

	/**
	 * Czysci ciag lub tablice z potencjalnie niebezpiecznych znakow
	 *
	 * @param string|array $dirty
	 * @return string|array
	 */
	static function clean_input($dirty)
	{
		//$input = get_magic_quotes_gpc() ? $dirty : addslashes($dirty);

		if( !is_array($dirty) )
		{
			$dirty = strtr( $dirty, array('<script>'=>'', '</script>'=>'') );
			$dirty = trim( strip_tags( htmlspecialchars($dirty, ENT_QUOTES), '' ) );
		}else if( is_array($dirty) )
		{
			foreach($dirty as $k=>$v)
			{
				if( is_array($v) )
				{
					foreach($v as $k2=>$v2)
					{
						if( is_array($v2) )
						{
							foreach($v2 as $k3=>$v3)
							{
								$v3 = strtr( $v3, array('<script>'=>'', '</script>'=>'') );
								$dirty[ $k ][ $k2 ][ $k3 ] = trim( strip_tags( htmlspecialchars($v3, ENT_QUOTES), '' ) );
							}
						}else{
							$v2 = strtr( $v2, array('<script>'=>'', '</script>'=>'') );
							$dirty[ $k ][ $k2 ] = trim( strip_tags( htmlspecialchars($v2, ENT_QUOTES), '' ) );
						}
					}
				}else{
					$v = strtr( $v, array('<script>'=>'', '</script>'=>'') );
					$dirty[ $k ] = trim( strip_tags( htmlspecialchars($v, ENT_QUOTES), '' ) );
				}
			}

		}
		return $dirty;
	}
	
	/**
	 * Usuwa encje
	 */
	static function cleanOutput($data) {
		if(is_array($data)) {
			foreach($data as $k=>$v) {
				$data[$k] = htmlspecialchars_decode(stripslashes($v), ENT_QUOTES);
			}
		}else {
			$data = htmlspecialchars_decode(stripslashes($data), ENT_QUOTES);
		}
		
		return $data;
	}

	static function takNieSelect()
	{
		return array('1'=>'Tak','0'=>'Nie');
	}

	static function takNieSelect2()
	{
		return array('0'=>'Nie','1'=>'Tak');
	}

	static function arrayToString($obj)
	{
		ob_start();
		echo '<pre>';
		print_r($obj);
		echo '</pre>';
		$msg = ob_get_contents();
		ob_end_clean();

		return $msg;
	}
	
	/**
	 * Funccja czyszcząca dla url, powinna być taka sama jak smarty_modifier_url
	 * @param string $string
	 * @return string type 
	 */
	static public function url($string)
	{
		$addr = strtr( html_entity_decode($string), array("ą"=>"a","ć"=>"c","ę"=>"e","ł"=>"l","ń"=>"n","ó"=>"o","ś"=>"s","ż"=>"z","ź"=>"z","Ą"=>"a","Ć"=>"c","Ę"=>"e","Ł"=>"l","Ń"=>"n","Ó"=>"o","Ś"=>"s","Ż"=>"z","Ź"=>"z"," "=>"-","/"=>"-","("=>"-",")"=>"-","."=>"-",","=>"-",":"=>"-","ö"=>"o","ä"=>"a","ü"=>"u","ß"=>"ss","Ö"=>"o","Ä"=>"a","Ü"=>"u", "+"=>'-', '"'=>'', "'"=>'', "?"=>'', "%"=>'', "!"=>'', "<"=>'', ">"=>'', "'"=>'', ";"=>'', "@"=>'', "#"=>'', "$"=>'', "^"=>'', "&"=>'', "*"=>'', "="=>'', "{"=>'', "}"=>'', "["=>'', "]"=>'', '\\'=>'', "|"=>'', "™"=>'', "®"=>'', "€"=>'', "©"=>'', "*"=>'', "_"=>""));

	    return strtolower( ereg_replace( '-+', '-', $addr ) );
	}

	function shuffle_assoc(&$array) {
        $keys = array_keys($array);

        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return true;
    }

    static public function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }

    function mb_ucfirst($string) {
	$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
	return $string;
    }

	/**
	 *
	 * @param <type> $date1
	 * @param <type> $date2
	 * @param <type> $withStartDay
	 * @param int $limitDay - jeżeli osiągneliśmy limit dni to wyrzucamy liczbe dni(czyli limit) , dla wydajnosci
	 * @return <type>
	 */
	function workDays($date1, $date2, $withStartDay = false, $limitDay = false) {
		$date1=strtotime($date1);
		$date2=strtotime($date2);
		if ($date2===$date1 && !$withStartDay) return 0;
		$char=1;
		if ($date1>$date2)
			{$datePom=$date1;$date1=$date2;$date2=$datePom;$char=-1;}
		$count=0;
		if (!$withStartDay)
			$date1=strtotime('+1 day',$date1);
		$date2=strtotime('+1 day',$date2);
		$lastYear = null;
		$hol=array('01-01','05-01','05-03','08-15','11-01','11-11','12-25','12-26');
		while ($date1<$date2) {
			$year = date('Y', $date1);
			if ($year !== $lastYear){
				$lastYear = $year;
				$easter = date('m-d', easter_date($year));
				$date = strtotime($year . '-' . $easter);
				$easterSec = date('m-d', strtotime('+1 day', $date));
				$cc = date('m-d', strtotime('+60 days', $date));
				$hol[8] = $easter;
				$hol[9] = $easterSec;
				$hol[10] = $cc;
			}
			$weekDay=date('w',$date1);
			if (!($weekDay==0 || $weekDay==6 || in_array(date('m-d',$date1),$hol)))
				$count++;

			if($count == $limitDay) {
				break;
			}

			$date1=strtotime('+1 day',$date1);
		}
		$count*=$char;
		return $count;
	}
	
	function workDate( $data, $days )
	{
		$data_odbioru = $data;
		$time_max = strtotime($data_odbioru) + ($days * 86400);
		$data_max = date("Y-m-d", $time_max);

		$dni = self::WorkDays($data_odbioru, $data_max);
		while( $dni < $days )
		{
			$time_max += 86400;
			$data_max = date("Y-m-d", $time_max);
			$dni = self::WorkDays($data_odbioru, $data_max);
		}

		return $data_max;
	}

	static public function emailValidate($email) {
		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
			return false;
		} else {
			return true;
		}
	}
	
	static function getUrl() {
		return 'http://' . $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	public function getPolishDate($date) {
		
		switch($date) {
			
			case 0: return "Niedziela"; break;
			case 1: return "Poniedziałek"; break;
			case 2: return "Wtorek"; break;
			case 3: return "Środa"; break;
			case 4: return "Czwartek"; break;
			case 5: return "Piątek"; break;
			case 6: return "Sobota"; break;
				
		}
	}
	
}

?>