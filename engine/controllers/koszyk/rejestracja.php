<?php

if($_SESSION['user_id']) {
	Common::redirect("/koszyk-2.html");
}

$obProfile = new Profile();
$aCountry = $obProfile -> getCountryList();