<?php

$objOrder = new Order();

$aResult = $objOrder -> getTempOrder($_SESSION['order_id']);
$aPozycje = $objBasket->getBasketElements();

$obProfile = new Profile();
$aCountry = $obProfile->getCountryListSelect();

$head_title = 'Koszyk -';