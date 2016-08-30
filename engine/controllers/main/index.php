<?php

$obMain = new Main();
//$aOrder = array(array('projekty_kategorie.kategoria_nazwa', 'ASC'), array('projekty.laureat', 'DESC'), array('projekty.glosow', 'DESC'));
$aOrder = array(array('projekty_kategorie.kategoria_nazwa', 'ASC'), array('projekty.glosow', 'DESC'));
if($_SESSION['user_id'] > 0) {
	//$aWhere	= array(array('projekty.na_glownej', '=', '1'),array('projekty.active', '=', '1','AND'),array('projekty.nominacja', '=', '1','AND'));
	$aWhere	= array(array('projekty.na_glownej', '=', '1'),array('projekty.active', '=', '1','AND'));
} else {
	$aWhere	= array(array('projekty.na_glownej', '=', '1'),array('projekty.active', '=', '1','AND'));
}
$aTables	= array('projekty',array('projekty_img','projekty','projekt_id','projekt_id', 'left'),
								array('projekty_kategorie', 'projekty', 'kategoria_id', 'kategoria_id', 'left'));
$aGroup		= array('projekty.projekt_id');
//$aList = $obMain -> getRecords($aTables, 'fetchall', $aWhere, $aOrder, $aGroup, 3, 0);
$aList = $obMain -> getRecords($aTables, 'fetchall', $aWhere, $aOrder, $aGroup);
	
$smarty->assign('temp', $aList);


$aOrder = array(array('projekty_kategorie.kategoria_nazwa', 'ASC'), array('projekty.laureat', 'DESC'), array('projekty.glosow', 'DESC'));
if($_SESSION['user_id'] > 0) {
	//$aWhere	= array(array('projekty.active', '=', '1'),array('projekty.nominacja', '=', '1','AND'));
	$aWhere	= array(array('projekty.active', '=', '1'));
} else {
	$aWhere	= array(array('projekty.active', '=', '1'));
}
	
		$aTables	= array('projekty',array('projekty_img','projekty','projekt_id','projekt_id', 'left'),
										array('projekty_kategorie', 'projekty', 'kategoria_id', 'kategoria_id', 'left'));
		$aGroup		= array('projekty.projekt_id');
		$aList = $obMain -> getRecords($aTables, 'fetchall', $aWhere, $aOrder, $aGroup);
	
foreach($aList as $row) {
	$aProductList2[$row['kategoria_id']][] = $row;
}

$smarty->assign('products', $aProductList2);
