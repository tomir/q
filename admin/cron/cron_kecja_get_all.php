<?php
require('/home/administrator/www/zambi/www/config/config.php');

ini_set('max_execution_time', 9000);

/**
 *	Decryption Class for Fideli software
 *	Real-time decryption using specified variables
 *	v.1.0, 30.07.2012 
 */
class FideliEncrypt {
	
	protected $key = 'J(jsiduj90s*&isudysYUU*&2';
	protected $uid = '8A4758D9347598A943J';
	protected $cipher = 'RIJNDAEL_256';
	protected $shop_http = '';
	
	protected $ver = '1_0';	
	
	public function __construct($key, $uid, $cipher = '', $shop_http = '') {
		//$this->key = $key;
		//$this->uid = $uid;
		
		if (!empty($cipher))
			$this->cipher = $cipher;
		
		if (!empty($shop_http))
			$this->shop_http = $shop_http;
	}

	/*
	 * Gets and return decrypted file using selected method
	 * Methods are described in fideli documentation
	 * get_all_products
	 * get_last_states
	 * get_all_states
	 */
	function getFile($method){
		
		$iv = substr(md5('iv'.$this->key, true), 0, 8); 
		$key = substr(md5('pass1'.$this->key, true) .
						md5('pass2'.$this->key, true), 0, 24);

		$opts = array('iv'=>$iv, 'key'=>$key);
		
		try {		
			
			// reading stream
			$fp = fopen($this->shop_http.'/files/'.$method.'_'.$this->ver.'/'.$this->uid, 'rb');
			stream_filter_append($fp, 'mdecrypt.'.strtolower($this->cipher), STREAM_FILTER_READ, $opts);
			$data = rtrim(stream_get_contents($fp)); 
			fclose($fp);

			// validate result of encryption
			$valid_begin = '<?xml version="1.0" encoding="utf-8"';
			if (substr($data, 0, strlen($valid_begin))==$valid_begin)
				return $data;		
			else
				return false;
		
		// exit with error
		} catch (excepion $e){
			return $e->getMessage();
		}	
	}		
	
}

$ke = new FideliEncrypt('', '', 'RIJNDAEL_256', 'http://kecja.pl');
$aProducts = simplexml_load_string($ke->getFile('get_all_products'));

foreach ($aProducts->product as $product) {
    switch($product->vat) {
        case 23: $vat_id = 7; break;
        case 5: $vat_id = 8; break;
        case 8: $vat_id = 9; break;
        default : $vat_id = 0;
    }
    $array = array(
        "import_name" => $product->name,
        "hurtownia_id" => 2,
        "import_desc" => $product->desc,
        "import_category" => $product->cat,
        "import_code" => $product->code,
        "import_others_id" => $product->id,
        "import_producer" => $product->made,
        "vat_id" => $vat_id
        );
    
    $sql = "SELECT import_id FROM shop_hurtownie_import WHERE import_others_id = ".$product->id.' AND hurtownia_id = 2';
    
    $import_id = ConnectDB::subQuery($sql,'one');
    
    if($import_id > 0) {
        $result = ConnectDB::subAutoExec('shop_hurtownie_import',$array,'UPDATE', "import_code = '".$product->code."'");
    } else {
        $import_id = ConnectDB::subAutoExec('shop_hurtownie_import',$array,'INSERT');
    }
    //dodajemy zdjecia
    foreach($product->imgs[0] as $img) {
        ConnectDB::subExec("INSERT IGNORE INTO shop_hurtownie_import_img (import_id, img_url) 
            VALUES(".$import_id.",'http://kecja.pl".$product->path.$img."')");
    }
}


?>
