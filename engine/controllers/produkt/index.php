<?php

$obMain = new Main();
$aOrder = array(array('projekty.projekt_sort', 'ASC'));
		$aWhere	= array(array('projekty.active', '=', '1'));
		$aTables	= array('projekty',array('projekty_img','projekty','projekt_id','projekt_id', 'left'),
										array('projekty_kategorie', 'projekty', 'kategoria_id', 'kategoria_id', 'left'));
		$aGroup		= array('projekty.projekt_id');
		$aList = $obMain -> getRecords($aTables, 'fetchall', $aWhere, $aOrder, $aGroup);
	
foreach($aList as $row) {
	$aProductList2[$row['kategoria_id']][] = $row;
}

$smarty->assign('products', $aProductList2);