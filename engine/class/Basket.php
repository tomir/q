<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classBasket
 *
 * @author tomi_weber
 */
class Basket {
	
	private $basket_id = 0;
	
	public function setBasketId($id) {
		$this->basket_id = $id;
	}
	
    public function getBasketElements() {
		$sql = "SELECT *, shop_basket_items.p_id FROM shop_basket JOIN shop_basket_items USING(basket_id) JOIN shop_product USING(p_id) LEFT JOIN shop_product_media on (shop_product_media.p_id = shop_product.p_id AND m_jpg = 1) WHERE basket_id = ".$this->basket_id." GROUP by shop_product.p_id";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
			$objProduct = new Product();
			
			foreach($aResult as &$row) {
				$row['link'] = "p-".Misc::utworzSlug($row['p_name']).",".$row['p_id'];
				$row['object'] = $objProduct->getProduct($row['p_id']);
				$row['zysk'] = $row['p_price_gross']-$row['p_price_buy'];
			}
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}
	
	 public function getBasketElement($id) {
		 
		$sql = "SELECT * FROM shop_basket_items WHERE item_id = ".$id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'fetch');
			
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function getBasketId($session) {
		$sql = "SELECT basket_id FROM shop_basket
					WHERE session_id = '".$session."'
					ORDER by create_date DESC";
		try {
			$one = ConnectDB::subQuery($sql, 'one');
			$this->basket_id = $one;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		if($one > 0)
			return $one;
		else
			return 0;
	}

	public function addBasket($session, $ip = '') {

		$sql = "INSERT INTO shop_basket (session_id,adress_ip,create_date)
			VALUES('".$session."','".$ip."',now())"; 
		$this->basket_id = ConnectDB::subExec($sql);
		
		return $this->basket_id;
	}

	public function addBasketItem($p_id, $items) {
		
		$sql = "SELECT SUM(items) FROM shop_basket_items WHERE p_id = {$p_id} AND basket_id = '{$this->basket_id}' ";
		$ile = ConnectDB::subQuery($sql, 'one');
			
		if($ile > 0) {
			$sql = "UPDATE shop_basket_items SET items = items + 1 WHERE basket_id = ".$this->basket_id." AND p_id = ".$p_id." LIMIT 1";
		} else {
		$sql = "INSERT IGNORE INTO shop_basket_items (basket_id, p_id, items, elements)
			VALUES(".$this->basket_id.",".$p_id.",".$items.", ".$items.")";
		}
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
			$this->calcBasket();
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return true;
	}

	public function saveBasketItem($id, $count) {
		
		$item = $this->getBasketElement($id);
		
		$objProduct = new Product();
		$product = $objProduct->getProduct($item['p_id']);
		if($count == 'items + 1') {
			if($product['p_magazine'] < $item['items']+1) {
				return false;
			}
		}
		
		$sql = "UPDATE shop_basket_items SET items = ".$count." WHERE item_id = ".$id." LIMIT 1";
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
			
			$item = $this->getBasketElement($id);
			if($item['items'] < 1) {
				$this->deteleBasketItem($id);
			}
			$this->calcBasket();
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return true;
	}

	public function deteleBasketItem($id) {
		$sql = "DELETE FROM shop_basket_items WHERE item_id = ".$id." LIMIT 1";
		try {
			
			ConnectDB::subExec($sql);
			$array = $this->getBasketElements();
			if(is_array($array) && count($array) > 0 && !empty($array)) {
				$this->calcBasket();
			} else {
				$this->deteleAllBasketItems();
				
				$objOrder = new Order();
				$objOrder->deleteTempOrder($_SESSION['order_id']);
				$_SESSION['order_id'] = 0;
			}
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return true;
	}

	public function deteleAllBasketItems() {
		
		try {
			$sql = "DELETE FROM shop_basket_items WHERE basket_id = ".$this->basket_id;
			ConnectDB::subExec($sql);
			
			$sql = "DELETE FROM shop_basket WHERE basket_id = ".$this->basket_id." LIMIT 1";
			ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return true;
	}

	public function deteleBasket() {
		$sql = "DELETE FROM shop_basket WHERE basket_id = ".$this->basket_id." LIMIT 1";
		try {
			$aResult = array();
			$aResult = ConnectDB::subExec($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return true;
	}

	public function calcBasket() {
			$calc = "UPDATE `shop_basket`
				 SET price_sum = (SELECT sum( IF( p_promo_price >0, p_promo_price, p_price_gross ) * items ) as cena FROM shop_basket_items JOIN shop_product USING(p_id)
				 WHERE basket_id = ".$this->basket_id." GROUP by basket_id),
					count_elements  = (SELECT sum(items) as ile FROM shop_basket_items JOIN shop_product USING(p_id)
				 WHERE basket_id = ".$this->basket_id." GROUP by basket_id),
					 product_elements = (SELECT sum(elements*items) as ile_elementow FROM shop_basket_items JOIN shop_product USING(p_id)
				 WHERE basket_id = ".$this->basket_id." GROUP by basket_id)
				WHERE basket_id = ".$this->basket_id;
			ConnectDB::subExec($calc);
			
			$objOrder = new Order();
			if($_SESSION['order_id'] > 0 || $_SESSION['order_id'] != '')
				$objOrder->saveTempStep1($_SESSION['order_id'], $this->basket_id);
	}
	
	public function getBasketTopHtml() {
		
		$result = $this->getBasketElements();
		ob_start();
		
		include(MyConfig::getValue("templatePatch")."_ajax/a_topBasket.php");
		
		$includedphp = ob_get_contents();
		ob_end_clean();
		
		return $includedphp;
	}
	
	
	public function getBasketListHtml($res = true, $item_id = 0) {
		
		$aPozycje = $this->getBasketElements();
		ob_start();
		
		include(MyConfig::getValue("templatePatch")."_ajax/a_basketList.php");
		
		$includedphp = ob_get_contents();
		ob_end_clean();
		
		return $includedphp;
	}
	
	public function sumBasketElements() {
		$sql = "SELECT price_sum FROM shop_basket WHERE basket_id = ".$this->basket_id;
		try {
			$one = ConnectDB::subQuery($sql, 'one');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $one;
	}
	
	public function countBasketElements() {
		$sql = "SELECT count_elements FROM shop_basket WHERE basket_id = ".$this->basket_id;
		try {
			$one = ConnectDB::subQuery($sql, 'one');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $one;
	}
	
	public function getTransportHtml() {
		
		$obOrder = new Order();
		$aResult = $obOrder -> getTempOrder($_SESSION['order_id']);
		$aDevilery = $obOrder -> getDevilery($_SESSION['id_country'], $aResult['payment_id']);
		ob_start();
		
		include(MyConfig::getValue("templatePatch")."_ajax/a_transportContent.php");
		
		$includedphp = ob_get_contents();
		ob_end_clean();
		
		return $includedphp;
	}
}
?>
