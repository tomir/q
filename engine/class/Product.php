<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classProduct
 *
 * @author t.jurdzinski
 * @author Tomasz Cisowski
 */
class Product {
	
    public function getProduct($id, $filtr) {
		
		$sql = "SELECT * FROM shop_product p
			JOIN shop_categories_products pcp ON p.p_id = pcp.p_id
			JOIN shop_categories pc ON pc.cat_id = pcp.cat_id 
            LEFT JOIN shop_product_age pa ON pa.id = p.p_age_id
			LEFT JOIN shop_product_media pm ON (pm.m_i = (SELECT m_i FROM shop_product_media AS pm2 WHERE pm2.p_id = p.p_id AND pm2.m_jpg = 1 ORDER BY pm2.m_main DESC, pm2.m_order ASC LIMIT 1) )
			LEFT JOIN shop_product_flag pf ON pf.p_id = p.p_id
			LEFT JOIN shop_producents pp ON pp.producent_id = p.producent_id
			LEFT JOIN shop_hurtownie_import hi ON (p.p_id = hi.product_id)
			WHERE 1 AND p.p_id = ".$id." ";
		$sql .= $this->getFiltr($filtr);
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'fetch');
			
			$aResult['zdjecia'] = $this->getProductMedia($id, 1);
			$aResult['video'] = $this->getProductMedia($id, 3);
			
			$objOpinie = new \Opinie\Lista();
			$aResult['opinia_ocena'] = $objOpinie->pobierzOceneDlaProduktu(array(
				'p_id' => $id,
				'status_id' => \Opinie\Lista::OPINION_ACCEPT
			));
			
			if($aResult['opinia_ocena'] == ' ')
				$aResult['opinia_ocena'] = 0;
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	/**
	 * Pobieramy liste produktów
	 * @param int $order
	 * 1 - wedlug daty dodania (najnowsze jako pierwsze)
	 * 2 - wedlug liczby sprzedanych
	 * 3 - promocje
	 * 4 - wedlug nazwy autorow
	 * 5 - wedlug nazwy produktu
	 * 6 - wedlug ceny rosnąco
     * 7 - według ceny malejąco
	 * @param int $limit
	 * @param int $start
	 * @param int $kat_name
	 * @return array
	 */
	public function getProductList($filtr, $order = 1, $limit = 0, $start = 0) {
		
		switch($order) {
			case 1: $order = "ORDER by p.p_on_stock DESC, p.p_create_date DESC";
				break;
			case 2: $order = "ORDER by p.p_on_stock DESC, p.p_sells DESC";
				break;
			case 3: $order = "ORDER by p.p_on_stock DESC, p.p_create_date DESC";
				break;
			case 5: $order = "ORDER by p.p_on_stock DESC, p.p_name";
				break;
			case 6: $order = "ORDER by p.p_on_stock DESC, p.p_price_gross";
				break;
            case 7: $order = "ORDER by p.p_on_stock DESC, p.p_price_gross DESC";
				break;
			case 8: $order = "ORDER BY p.p_on_stock DESC, RAND()";
				break;
			case 9: 
				$findInSet = implode(',', $filtr['sphinx']);
				$order = "ORDER by FIND_IN_SET(p.p_id , '".$findInSet."')";
				break;
		}

		$sql = "SELECT *, p.p_id as p_id, hi.import_category, hi.import_code, hi.import_ean 
				FROM shop_product p
				LEFT JOIN shop_product_flag pf ON pf.p_id = p.p_id
				LEFT JOIN shop_producents pp ON pp.producent_id = p.producent_id
				LEFT JOIN shop_hurtownie_import hi ON (p.p_id = hi.product_id)
				LEFT JOIN shop_product_media pm ON ( pm.m_i = (SELECT m_i FROM shop_product_media AS pm2 WHERE pm2.p_id = p.p_id AND pm2.m_jpg = 1 ORDER BY pm2.m_main DESC, pm2.m_order ASC LIMIT 1))
				WHERE 1 ";
		$sql .= $this->getFiltr($filtr);
		
		$sql .= " GROUP by p.p_id ";
		$sql .= $order;
		if($limit > 0)
			$sql .= " LIMIT ".$start.", ".$limit;
		
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
			foreach($aResult as &$row) {
				$row['link'] = "p-".Misc::utworzSlug($row['p_name']).",".$row['p_id'];
			}
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}

	public function getProductListCount($filtr) {
		
		$sql = "SELECT COUNT(p.p_id) as ile FROM shop_product p
				JOIN shop_categories_products pcp ON p.p_id = pcp.p_id
				JOIN shop_categories pc ON pc.cat_id = pcp.cat_id 
				WHERE 1 ";
		
		$sql .= $this->getFiltr($filtr);
		
		try {
			$ile = ConnectDB::subQuery($sql, 'one');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $ile;
	}
    
    public function getProductListPrices($filtr) {
        $temp_array = $this->getProductList($filtr);
        $aResult = array();
        foreach($temp_array as $row) {
            array_push($aResult, ceil($row['p_price_gross']));
        }
        return $aResult;
    }
	
	public function getFiltr($filtr) {
		
		$sql = "";
		$this->filtr = $filtr;
		
		if(isset($this->filtr['active']) && is_numeric($this->filtr['active'])) {
			$sql .= " AND p.p_active = 1 ";
		}
		
		if(isset($this->filtr['magazine']) && is_numeric($this->filtr['magazine'])) {
			$sql .= " AND p.p_magazine > 0 ";
		}
		
		if(is_array($this->filtr['cat_id']) && count($this->filtr['cat_id']) > 0) {
			$sql .= " AND pc.cat_id IN (".implode(",",$this->filtr['cat_id']).") ";
		}
		
		if (isset($filtr['sphinx']) && is_array($filtr['sphinx']) && count($filtr['sphinx']) > 0) {
			$prodSQL = implode(',', $filtr['sphinx']);
			$sql .= " AND p.p_id IN ($prodSQL) ";
		}
        
        if ($this->filtr['age'] > 0) {
			$sql .= " AND p.p_age_id = ".$this->filtr['age'];
		}
		
		if(isset($this->filtr['cat_id']) && is_numeric($this->filtr['cat_id'])) {
			
			$objCategory = new Category();
				
			if(MEMCACHE == 1) {
				$memObject = new Memcache;
				$memObject->connect('127.0.0.1', 11211);

				if($result = $memObject->get("getAllChildren".$filtr['cat_id'])) {
					$drzewko = unserialize($result);
				} else {
					$drzewko = $objCategory->getAllChildren( $filtr['cat_id'] );
					$memObject->set("getAllChildren".$filtr['cat_id'], serialize($drzewko), 0, 3600);
				}

			} else {
				$drzewko = $objCategory->getAllChildren($filtr['cat_id']);
			}
			
			if(is_array($drzewko) && count($drzewko) > 0) {
				$drzewko[] = $filtr['cat_id'];
				$drzewko = implode(',', $drzewko);
				if( substr($drzewko, strlen($drzewko)-1, 1)==',' )
					$drzewko = substr($drzewko, 0, strlen($drzewko)-1);

				$sql.= " AND pc.cat_id IN (".$drzewko.") ";
			} else {
				$sql.= " AND pc.cat_id = ".$this->filtr['cat_id'];
			}
		}
		
		if(isset($this->filtr['promocja']) && is_numeric($this->filtr['promocja'])) {
			$sql .= " AND pf.flag_id = ".$this->filtr['promocja']." ";
		}
		
		if(isset($this->filtr['bestseller']) && is_numeric($this->filtr['bestseller'])) {
			$sql .= " AND pf.flag_id = ".$this->filtr['bestseller']." ";
		}
		
		if(isset($this->filtr['nowosc']) && is_numeric($this->filtr['nowosc'])) {
			$sql .= " AND pf.flag_id = ".$this->filtr['nowosc']." ";
		}
		
		if(is_array($this->filtr['producent_id']) && count($this->filtr['producent_id']) > 0) {
			$sql .= " AND p.producent_id IN (".implode(",",$this->filtr['producent_id']).") ";
		}
		
		if(isset($this->filtr['producent_id']) && is_numeric($this->filtr['producent_id']) && $this->filtr['producent_id'] > 0) {
			$sql .= " AND p.producent_id = ".$this->filtr['producent_id']." ";
		}
		
		if(isset($this->filtr['product_not']) && is_numeric($this->filtr['product_not'])) {
			$sql .= " AND p.p_id != ".$this->filtr['product_not']." ";
		}
		
		if(isset($this->filtr['price_from']) && is_numeric($this->filtr['price_from']) && isset($this->filtr['price_to']) && is_numeric($this->filtr['price_to']) && $this->filtr['price_to'] > 0) {
            $sql .= " AND p.p_price_gross > ".$this->filtr['price_from']." AND p.p_price_gross < ".$this->filtr['price_to'];
        }
        
		return $sql;
	}

	public function getProductMedia($product_id, $media_type) {
		switch($media_type) {
			case 1: $where = " AND 	m_jpg = 1 ";
				break;
			case 2: $where = " AND 	m_audio = 1 ";
				break;
			case 3: $where = " AND  m_video = 1 ";
				break;
		}
		$sql = "SELECT * FROM shop_product_media WHERE p_id = ".$product_id." ".$where." ORDER by m_order";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}
	
	static public function incrementSale($id) {
		
		$sql = "UPDATE shop_product SET p_sells = p_sells+1 WHERE p_id = ".$id;
		ConnectDB::subExec($sql);
		
		return true;
	}
    
    public function getPriceRanges($filtr) {
        
        if (MEMCACHE == 1) {
            $memObject = new Memcache;
            $memObject->connect('127.0.0.1', 11211);
            if($cache = $memObject->get('getPriceRanges'.md5($filtr))) {
                $aResult = unserialize($cache);
                return $aResult;
            }
        } else {
            $ceny_wszystkie = $this->getProductListPrices( $filtr );
            asort($ceny_wszystkie);
            $czyParzyste = ( count($ceny_wszystkie)%2 ? false : true );
            //var_dump($ceny_wszystkie);
            //var_dump($czyParzyste);

            if( $czyParzyste )
            {
                    $poIle = round( count($ceny_wszystkie) / 10 );
            }else{
                    $poIle = round( ( count($ceny_wszystkie)+1 ) / 10 );
            }

            $przedzialyCenowe = array();
            for($x = 0; $x < 10; $x++)
            {
                    $przedzialyCenowe[ $x ] = array_slice( $ceny_wszystkie, $x * $poIle, $poIle );
            }

            //var_dump($przedzialyCenowe);

            $zakresyCen = array(
                            0=>array('od'=>0, 'do'=>end($przedzialyCenowe[1])),
                            1=>array('od'=>end($przedzialyCenowe[1]), 'do'=>end($przedzialyCenowe[3])),
                            2=>array('od'=>end($przedzialyCenowe[3]), 'do'=>end($przedzialyCenowe[5])),
                            3=>array('od'=>end($przedzialyCenowe[5]), 'do'=>end($przedzialyCenowe[7])),
                            4=>array('od'=>end($przedzialyCenowe[7]), 'do'=>0)
                                                    );


            if( count($zakresyCen) == 1 )
                    $zakresyCen = array();
            
            if($zakresyCen[0]['od'] == 0 && intval($zakresyCen[1]['od']) == 0) {
                $zakresyCen = array();
            }
            
            if(MEMCACHE == 1) {
                $memObject->set('getPriceRanges'.md5($filtr), serialize($zakresyCen), 0, 3600);
            }
            return $zakresyCen;
        }
    }
    
    public function getCategoryAge($category_id) {
        
        if($category_id != 0) {
            if(MEMCACHE == 1) {
                $memObject = new Memcache;
                $memObject->connect('127.0.0.1', 11211);
                if($cache = $memObject->get('getCategoryAge'.$category_id)) {
                    $aResult = unserialize($cache);
                    return $aResult;
                }
            }

            $objCategory = new Category();
            $drzewko = $objCategory->getAllChildren($category_id);

            $drzewko[] = $category_id;
            $drzewko = implode(',', $drzewko);
            if (substr($drzewko, strlen($drzewko)-1, 1)==',' )
                $drzewko = substr($drzewko, 0, strlen($drzewko)-1);

            $sql = "SELECT p_age_id, pa.name FROM shop_product p
                JOIN shop_categories_products pcp ON p.p_id = pcp.p_id
                JOIN shop_categories pc ON pc.cat_id = pcp.cat_id
                JOIN shop_product_age pa ON p.p_age_id = pa.id
                WHERE pc.cat_id IN (".$drzewko.")
                GROUP by p_age_id ORDER by p_age_id";
        } else {
            if(MEMCACHE == 1) {
                $memObject = new Memcache;
                $memObject->connect('127.0.0.1', 11211);
                if($cache = $memObject->get('getCategoryAge'.$category_id)) {
                    $aResult = unserialize($cache);
                    return $aResult;
                }
            }
            $sql = "SELECT pa.id as p_age_id, pa.name FROM shop_product_age pa ORDER by p_age_id";
        }
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
        if(MEMCACHE == 1) {
            $memObject->set('getCategoryAge'.$category_id, serialize($aResult), 0, 3600);
        }
		return $aResult;
    }
}
?>
