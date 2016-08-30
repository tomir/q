<?php
//require('../../config/config.php');
require('/home/administrator/www/zambi/www/config/config.php');

$sql = "INSERT shop_import_log (log_product) VALUES ('Kecja states RUN')";
ConnectDB::subExec($sql); 

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
			stream_filter_append($fp, 'mdecrypt.'.strtolower('RIJNDAEL_256'), STREAM_FILTER_READ, $opts);
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
$aProducts = simplexml_load_string($ke->getFile('get_all_prices'));

foreach ($aProducts->product as $product) {
    $vat = ConnectDB::subQuery("SELECT CONCAT('1.',vat_level) as vat FROM `shop_vat_levels` v JOIN 
shop_hurtownie_import i on (i.hurtownia_id = 2 AND v.vat_id = i.vat_id) limit 1",'fetch');
    
    $array = array(
        "import_price_buy" => $product->buy,
        "import_code" => $product->code,
        "import_price_suggest" => $product->netto,
        "import_inventory" => $product->stock,
        "import_price_suggest_gross" => $product->netto * $vat['vat']
        );
    
    $sql = "UPDATE shop_hurtownie_import SET import_price_buy = ".$array['import_price_buy'].",
        import_price_suggest = ".$array['import_price_suggest'].",
        import_inventory = ".$array['import_inventory'].",
        import_price_suggest_gross = ".$array['import_price_suggest_gross']."
        WHERE import_code = '".(string)$array['import_code']."' AND hurtownia_id = 2";
    
    ConnectDB::subExec($sql);
}

?>
