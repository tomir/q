<?php

if($_GET['id'] > 0 && $_SESSION['user_id'] > 0) {

	$obMain = new Main();

	$aWhere		= array(array('projekty.projekt_id', '=', $_GET['id']));
	$aTables	= array('projekty',array('projekty_kategorie','projekty','kategoria_id','kategoria_id', 'left'));
	$aProjekt = $obMain -> getRecords($aTables, 'fetch', $aWhere);
	
	$aWhere		= array(array('projekty_glosy2.user_id', '=', $_SESSION['user_id']), array('projekty_glosy2.kategoria_id', '=', $aProjekt['kategoria_id'], 'AND'));
	$aTables	= array('projekty_glosy2');
	$aGlosy = $obMain -> getRecords($aTables, 'fetchall', $aWhere);
	if(is_array($aGlosy) && count($aGlosy) > 0 && $_SESSION['user_id'] != 1103) {
		$_SESSION['error'] = true;
		Common::redirect('/konkurs/?kategoria='.$aProjekt['kategoria_nazwa'].'&kategoriafull='.$aProjekt['kategoria_nazwa_full'].'#'.str_replace(' ', '%20', $aProjekt['kategoria_nazwa']));
		exit();
	}
	
	ConnectDB::subAutoExec('autosalon_projekty_glosy2', array('user_id' => $_SESSION['user_id'], 'projekt_id' => $aProjekt['projekt_id'],'kategoria_id' => $aProjekt['kategoria_id']), 'INSERT');
	
	ConnectDB::subAutoExec('autosalon_projekty', array('glosow' =>  $aProjekt['glosow']+1), 'UPDATE', 'projekt_id = '.$aProjekt['projekt_id']);
	
	$_SESSION['ok'] = true;
}

Common::redirect('/konkurs/?kategoria='.$aProjekt['kategoria_nazwa'].'&kategoriafull='.$aProjekt['kategoria_nazwa_full'].'#'.str_replace(' ', '%20', $aProjekt['kategoria_nazwa']));
exit();
