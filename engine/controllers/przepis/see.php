<?php

if((int)$_GET['id'] > 0) {
	$objPrzepis = new \Przepis\Repository\Przepis((int)$_GET['id']);

	if(in_array($objPrzepis->data['user_id'], \Przepis\Enum\Cooker::$cookerId)) {
		$smarty->assign('enum_cooker', \Przepis\Enum\Cooker::$cookerAlias[$objPrzepis->data['user_id']]);
		$filtr['x.user_id'] = $objPrzepis->data['user_id'];
	}
	
	$smarty->assign('przepis', $objPrzepis->data);
	
	$filtr['x.active'] = 1;
	$filtr['x.id_not'] = $objPrzepis->id;
	
	$list = $objPrzepis->getAll($filtr, array(
		'sort' => 'rand'
	), array(
		'limit' => 6,
		'start' => 0
	));
	$smarty->assign('przepisy', $list);

}

