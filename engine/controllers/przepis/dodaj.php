<?php

if(isset($_POST['formData'])) {
	
	$obPrzepis = new \Przepis\Service\Save($_POST['formData']);
	$obPrzepis->preparePrzepis();
	$obPrzepis->addUser();
	$res = $obPrzepis->save();
	
	if($res) {
		$obPrzepis->saveImage();
		
		$obPrzepisMail = new \Przepis\Service\Mail();
		$obPrzepisMail->setStatus("potwierdzenie");
		$obPrzepisMail->setTitle("Potwierdzenie zgłoszenia przepisu do konkursu 'Zostań Mistrzem Kuchni'");
		$obPrzepisMail->sendMail(new \Przepis\Repository\Przepis($obPrzepis->getId()));
		
		\Enp\Tool::setFlashMsg("Twój przepis został zapisany.", \Enp\Tool::INFO);
		header("Location: /przepis-dodaj.html?ok=1");
	} else {
		header("Location: /przepis-dodaj.html?error=1");
	}
}
else {
	$objCzas = new \Przepis\Repository\Czas();
	$smarty->assign('czas',$objCzas->getAllSelect());
	
	$objRodzaj = new \Przepis\Repository\Rodzaj();
	$smarty->assign('rodzaj',$objRodzaj->getAllSelect());
	
	$objPoziom = new \Przepis\Repository\Poziom();
	$smarty->assign('poziom',$objPoziom->getAllSelect());
	
	$objProduct = new Product();
	$smarty->assign('produkty',$objProduct->getProductList(array(
		"active" => 1
	), 5));
}
