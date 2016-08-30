<?php

$obProfile = new Profile();
if(isset($_POST['aData'])) {
	$res = $obProfile->zmienHaslo($_POST['aData'], $_SESSION['user_id']);
	if($res > 0) {
		Common::redirect('/konto-haslo.html?kom='.$res);
	}
}