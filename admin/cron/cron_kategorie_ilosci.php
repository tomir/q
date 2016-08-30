<?php

require('/home/administrator/www/zambi/www/config/config.php');
error_reporting(E_ERROR);

echo '<pre>';

$sql = "SELECT pc.cat_id, COUNT(p.p_id) AS qty
				FROM shop_product p
				JOIN shop_categories_products pcp ON p.p_id = pcp.p_id
				JOIN shop_categories pc ON pc.cat_id = pcp.cat_id 
				WHERE 1 AND p.p_active = 1 AND (p.p_magazine > 0 OR (p.p_magazine = 0 AND DATE_SUB(CURDATE(), INTERVAL 14 DAY) <= p.p_on_stock_date)) GROUP BY pc.cat_id ORDER by p.p_create_date DESC";

$ogladalnosc = ConnectDB::subQuery($sql, 'assoc');

$sql = "UPDATE shop_categories SET cat_ilosc = 0";
ConnectDB::subExec($sql);

$c = new Category();

$tree = $c->getTree();
$ilosci = array();

foreach($tree as $v)
{
	$ilosci[ $v['cat_id'] ]+= (int)$ogladalnosc[ $v['cat_id'] ];
	if( count($v['children']) > 0 )
	{
		foreach($v['children'] as $v2)
		{
			$ilosci[ $v2['cat_id'] ]+= (int)$ogladalnosc[ $v2['cat_id'] ];
			$ilosci[ $v['cat_id'] ]+= (int)$ogladalnosc[ $v2['cat_id'] ];
			if( count($v2['children']) > 0 )
			{
				foreach($v2['children'] as $v3)
				{
					$ilosci[ $v3['cat_id'] ]+= (int)$ogladalnosc[ $v3['cat_id'] ];
					$ilosci[ $v2['cat_id'] ]+= (int)$ogladalnosc[ $v3['cat_id'] ];
					$ilosci[ $v['cat_id'] ]+= (int)$ogladalnosc[ $v3['cat_id'] ];

					if( count($v3['children']) > 0 )
					{
						foreach($v3['children'] as $v4)
						{
							$ilosci[ $v4['cat_id'] ]+= (int)$ogladalnosc[ $v4['cat_id'] ];
							$ilosci[ $v3['cat_id'] ]+= (int)$ogladalnosc[ $v4['cat_id'] ];
							$ilosci[ $v2['cat_id'] ]+= (int)$ogladalnosc[ $v4['cat_id'] ];
							$ilosci[ $v['cat_id'] ]+= (int)$ogladalnosc[ $v4['cat_id'] ];

							if( count($v4['children']) > 0 )
							{
								foreach($v4['children'] as $v5)
								{
									$ilosci[ $v5['cat_id'] ]+= (int)$ogladalnosc[ $v5['cat_id'] ];
									$ilosci[ $v4['cat_id'] ]+= (int)$ogladalnosc[ $v5['cat_id'] ];
									$ilosci[ $v3['cat_id'] ]+= (int)$ogladalnosc[ $v5['cat_id'] ];
									$ilosci[ $v2['cat_id'] ]+= (int)$ogladalnosc[ $v5['cat_id'] ];
									$ilosci[ $v['cat_id'] ]+= (int)$ogladalnosc[ $v5['cat_id'] ];
								}
							}
						}
					}
				}
			}
		}
	}
}

$sqlMulti = "UPDATE shop_categories SET cat_ilosc = %s";

$tmp = " CASE \n";
foreach( $ilosci as $k=>$v )
{
	$tmp.= " WHEN cat_id = '".(int)$k."' THEN '".(int)$v."' \n";
}
$tmp.= " ELSE cat_ilosc \n";
$tmp.= " END \n";

$sql = sprintf($sqlMulti, $tmp); 
var_dump($sql);

//file_put_contents( _TEMP_DIR.'multiupdate-'.date("Y-m-d_H-i-s").'.sql', $sql );
//$log->log('Plik: '._TEMP_DIR.'multiupdate-'.date("Y-m-d_H-i-s").'.sql', $sql);

$startX = microtime(true);
ConnectDB::subExec($sql);
$endX = microtime(true);
$diff = $endX - $startX;

echo 'Czas: '.($diff/1000);

?>