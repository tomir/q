<?php

$facebook = new Facebook(array('appId' => '754003071362138', 'secret' => '2e25e40e469a843320b43c86da200f1c'));

$user = $facebook->getUser();

if (!$user) {
    $loginUrl = $facebook->getLoginUrl($params = array('redirect_uri' => MyConfig::getValue("wwwPatchSsl").'index.php?action=login&action2=fb&r='.$_GET['r'], 'scope'=>'email'));
    header("Location: $loginUrl");
    exit;
} else {
    $user_profile = $facebook->api('/me');
	


	$obProfile = new Profile();
	$id = $obProfile->getUserByFacebookId($user_profile['id']);
	if ((int) $id == 0) {

		$dane = array();

		$salt = Profile::fetch_user_salt();
		$data['user_salt'] = $salt;
		$data['user_add_date'] = date('Y-m-d H:i:s');
		$dane['user_pass'] = md5(md5(time()) . $salt);
		
		$dane['user_email'] = $user_profile['email'];
		$dane['user_facebook_id'] = $user_profile['id'];
		$dane['user_first_name'] = $user_profile['first_name'];
		$dane['user_last_name'] = $user_profile['last_name'];
		$id = $obProfile->saveUser($dane);
		/*
		 * Dodajemy domyślny adres użytkownika z danymi które mamy z FB
		 */
		$adresDane['user_id'] = $id;
		$adresDane['address_first_name'] = $user_profile['first_name'];
		$adresDane['address_last_name'] = $user_profile['last_name'];
		$adresDane['address_name'] = $user_profile['first_name']." ".$user_profile['last_name'];
		$adresDane['address_main'] = 1;
		$obProfile->saveAddressBook($adresDane);
	} 
	
	$aResult = $obProfile->getUser($id);
		
	$_SESSION['user_id']		= $id;
	$_SESSION['user_email'] 	= $aResult[0]['user_email'];
	$_SESSION['imie']			= $aResult[0]['user_first_name'];
	$_SESSION['nazwisko']		= $aResult[0]['user_last_name'];
	$_SESSION['last_login']		= $aResult[0]['user_last_login'];
	
	if(isset($_GET['r'])){
		Common::redirect('/konkurs/index.php?action=glosuj&id='.$_GET['r']);
	}else{
		Common::redirect('/konkurs/');
	}
	exit;
	
}
