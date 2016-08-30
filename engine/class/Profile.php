<?php

class Profile {

	public function getUser($id_user) {
		
	    if(intval($id_user)) {
		$sql = "SELECT * FROM shop_users WHERE user_id = ".$id_user;
		$aResult = ConnectDB::subQuery($sql);

		return $aResult;
	    }
	}

	public function getUserByEmail($email) {
		
		$sql = "SELECT user_id FROM shop_users WHERE user_email = '".$email."'";
		$aResult = ConnectDB::subQuery($sql, "one");

		return $aResult;
	}
	
	public function getUserByFacebookId($id) {
		
		$sql = "SELECT user_id FROM shop_users WHERE user_facebook_id = '".$id."'";
		$aResult = ConnectDB::subQuery($sql, "one");

		return $aResult;
	}

	public function zaloguj($email = '', $pass = '', $admin = '0') {

	    $error		= false;
	    $email		= trim($email);
	    $pass    	= trim($pass);
		
	    if($email	 == '') $error = true;
	    if($pass	 == '') $error = true;

	    if($error) {
			return false;
	    }
		
		if($admin > 0) {
			$where = " AND user_admin = 1";
		}
		
	    try {
			$sql = "SELECT * FROM shop_users WHERE user_email = '".$email."' AND user_pass = md5(CONCAT('".md5($pass)."', user_salt)) ".$where." LIMIT 1";
		
			$aResult = ConnectDB::subQuery($sql, "fetch");
			
			if(is_array($aResult)) {

				$_SESSION['user_id']		= $aResult['user_id'];
				$_SESSION['user_email'] 	= $aResult['user_email'];
				$_SESSION['imie']			= $aResult['user_first_name'];
				$_SESSION['nazwisko']		= $aResult['user_last_name'];
				$_SESSION['last_login']		= $aResult['user_last_login'];

				$sql = "UPDATE shop_users SET user_last_login = user_login_date, user_login_date = now() WHERE user_id = ".$aResult['user_id'];
				ConnectDB::subExec($sql);

				return true;
			}
			else {
				if($_GET['back_url'] && !$_GET['redirect'])
					header("Location: ".$_GET['back_url'].'?error=1');
				else {
					return false;
				}
			}
	    } catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			return false;
	    }
	}

	public function getCountryList() {

		$sql = "SELECT * FROM shop_country";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
	}
	
	public function getCountryListSelect() {

		$all = $this->getCountryList();
		$new = array();
			
		foreach($all as $row) {
			$new[$row['id_country']] = $row['country'];
		}
		return $new;
	}

	public function wyloguj() {
		
		$_SESSION['user_id']		= '';
		$_SESSION['user_login'] 	= '';
		$_SESSION['user_email'] 	= '';
		$_SESSION['imie']		= '';
		$_SESSION['nazwisko']		= '';
		$_SESSION['last_login']		= '';
		
		unset($_SESSION);
		return true;
	}
	
	public function rejestruj() {

		$border['username']    	= '#A5ACB2';
		$border['haslo'] 	  	= '#A5ACB2';
		$border['email'] 	  	= '#A5ACB2';
		$border['kod_obrazek'] 	= '#A5ACB2';
		$border['zgoda3'] 	  	= '';

		$error 		= false;
		$data		= $_POST['aData'];
		foreach($data as &$row) {
			$row = trim($row);
		}

		$haslo_md5  = md5($data['user_pass']);
		$haslo_org = $data['user_pass'];
		//sprawdzamy poprawność adresu email
		$pattern = '/^(([a-z0-9!#$%&*+-=?^_`{|}~]'.'[a-z0-9!#$%&*+-=?^_`{|}~.]*'.'[a-z0-9!#$%&*+-=?^_`{|}~])'.'|[a-z0-9!#$%&*+-?^_`{|}~]|'.'("[^"]+"))'.'[@]'.'([-a-z0-9]+\.)+'.'([a-z]{2}'.'|com|net|edu|org'.'|gov|mil|int|biz'.'|pro|info|arpa|aero'.'|coop|name|museum)$/ix';
	  	if(!preg_match($pattern, $data['user_email'])) {
	  		$error = true;
	  		$border['email'] 	  = '#ff0000';
			$border['email2'] 	  = '#ff0000';
			$komunikat_email = 'Błędny adres email';
	  	}

		//sprawdzamy czy już sie ktoś nie zarejestrował z takim emailem
		$sql = "SELECT COUNT(user_email) AS ilosc FROM shop_users WHERE user_email = '".$data['user_email']."'";
		$wynik = ConnectDB::subQuery($sql, 'one');

		if($wynik > 0) {
			$error = true;
			$border['email'] = '#ff0000';
			$komunikat_email = 'Podany adres email został już wcześniej użyty';
		}
		
		$sql = "SELECT COUNT(user_email) AS ilosc FROM shop_users WHERE user_zip NOT IN ('80.50.145.74', '193.41.230.142', '195.42.249.134', '84.10.13.126') AND user_zip = '".get_client_ip()."' AND user_add_date > DATE_SUB( CURDATE(), INTERVAL 1 HOUR)";
		$wynik = ConnectDB::subQuery($sql, 'one');

		if($wynik > 0) {
			$error = true;
			$border['email'] = '#ff0000';
			$komunikat_email = 'Blokada IP';
		}

		if($error) {
			
			$_SESSION['komunikat'] 	= '<img style="float: left;" src="'.MyConfig::getValue("gfxPatch").'v2/img/alert.png" alt="" /> <div style="width: 260px; margin-left: 8px;margin-top: 5px;color: #EC832B;float: left;">Blokada IP</div>';
			$_SESSION['komunikat_username'] = $komunikat_username;
			$_SESSION['komunikat_email'] = $komunikat_email;
			return false;
		}

		$salt = self::fetch_user_salt();
		$haslo_baza = md5($haslo_md5 . $salt);
		
		$data['user_salt'] = $salt;
		$data['user_pass'] = $haslo_baza;
		$data['user_add_date'] = date('Y-m-d H:i:s');

		$data['user_zip'] = get_client_ip();
		
		$id = ConnectDB::subAutoExec('shop_users', $data, 'INSERT');
		$zalogowano = $this -> zaloguj($data['user_email'], $haslo_org);
		
		$_SESSION['user_id']		= $id;
		$_SESSION['user_email'] 	= $data['user_email'];
	
		
		//wysyłamy maila aktywacyjnego i ewentualne dodawanie do bazy newslettera
		if($data['zgoda_newsletter'] == 1) {
			try {
				$data_newsletter['newsletter_email']	= $data['user_email'];
				$data_newsletter['newsletter_active']	= 1;
				$data_newsletter['newsletter_add_date'] = date('Y-m-d H:i:s');
				
		    	ConnectDB::subAutoExec('shop_users_newsletter', $data_newsletter, 'INSERT');
			}
			catch(PDOException $e) {
				echo 'blad zapisu do newslettera';
			}
		}
		
    	//$zalogowano = $this -> zaloguj($data['user_email'], $haslo_org);
		return $zalogowano;
		
	}
	
	static public function fetch_user_salt($length = 3)
	{
		$salt = '';
		for ($i = 0; $i < $length; $i++) {
			$salt .= chr(self::vbrand(33, 126));
		}
		return $salt;
	}
	
	static public function vbrand($min = 0, $max = 0, $seed = -1)
	{
		mt_srand(crc32(microtime()));
		if ($max AND $max <= mt_getrandmax()) {
			$number = mt_rand($min, $max);
		}
		else {
			$number = mt_rand();
		}
		// reseed so any calls outside this function don't get the second number
		mt_srand();
		return $number;
	}
	
	public function zmienHaslo($aData, $user_id) {
		
		$sql = "SELECT * FROM shop_users WHERE user_id = '".$user_id."' AND user_pass = MD5(CONCAT('".md5($aData['haslo'])."', user_salt)) LIMIT 1";
		$aResult = ConnectDB::subQuery($sql, "fetch");

		if(is_array($aResult) && count($aResult) > 0) {

			if($aData['hasloNowe'] == $aData['hasloNowe2']) {
				
				$new['user_pass'] = md5(md5($aData['hasloNowe']).$aResult['user_salt']);
				ConnectDB::subAutoExec('shop_users', $new, 'UPDATE', 'user_id = '.$user_id);
				
				return 1;
			} else {
				return 2;
			}
		} else {
			return 3;
		}
		
	}
	
	public function getAddressBookList($user_id) {

		$sql = "SELECT * FROM shop_users_address_book WHERE 1 AND user_id = ".$user_id;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
		
	}
	
	public function getAddressBook($user_id, $id = 0) {
		
		if($id > 0) {
			$where = ' AND address_id = '.$id;
		} else {
			$where = ' AND address_main = 1';
		}
		
		$sql = "SELECT * FROM shop_users_address_book WHERE 1 AND user_id = ".$user_id.$where;
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql, 'fetch');
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
		
	}
	
	public function saveAddressBook($data) {
		
		try {
			
			$data['address_m_date'] = date('Y-m-d H:i:s');
			
			if($data['address_id'] > 0) {
				ConnectDB::subAutoExec('shop_users_address_book', $data, 'UPDATE', 'address_id = '.$data['address_id']);
			} else {
				ConnectDB::subAutoExec('shop_users_address_book', $data, 'INSERT');
			}

		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
		return true;
		
	}
	
	public function setMainAddressBook($id, $id_user) {
		
		$list = $this->getAddressBookList($id_user);
		foreach($list as $row) {
			$data['address_main'] = 0;
			ConnectDB::subAutoExec('shop_users_address_book', $data, 'UPDATE', 'address_id = '.$row['address_id']);
		}
		
		$data['address_main'] = 1;
		ConnectDB::subAutoExec('shop_users_address_book', $data, 'UPDATE', 'address_id = '.$id);
		
		return true;
	}
	
	public function deleteAddressBook($user_id, $id) {
		
		$sql = "DELETE FROM shop_users_address_book WHERE user_id = {$user_id} AND address_id = ".$id;
		ConnectDB::subExec($sql);
		
		return true;
	}


	/*
	*	AJAX
	*/
	
	public function getAddressUserHtml($aUserData) {
		$aCountry = $this->getCountryListSelect();
		ob_start();
		
		include(MyConfig::getValue("templatePatch")."_ajax/a_daneUser.php");
		
		$includedphp = ob_get_contents();
		ob_end_clean();
		
		return $includedphp;
	}
	
	public function getAddressDevileryHtml($aUserData) {
		$aCountry = $this->getCountryListSelect();
		ob_start();
		
		include(MyConfig::getValue("templatePatch")."_ajax/a_daneDevilery.php");
		
		$includedphp = ob_get_contents();
		ob_end_clean();
		
		return $includedphp;
	}
	
	public function addSchowek($idProdukt, $idUser)
	{
		try {

			$data['user_id'] = $idUser;
			$data['p_id'] = $idProdukt;
			$data['clip_items'] = 1;
			ConnectDB::subAutoExec('shop_users_clipboard', $data, 'INSERT');

			return true;

		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return $e->getCode();
		}
	}

	public function deleteSchowek($p_id, $idUser)
	{
		try {
			
			$sql = "DELETE FROM shop_users_clipboard WHERE p_id = {$p_id} AND user_id = {$idUser} ";
			ConnectDB::subExec($sql);

			return true;

		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return $e->getCode();
		}
	}

	public function getSchowekList($idKlient, $ilosc = 0)
	{
		try {

			$sql = "SELECT *, p.p_id as p_id
					FROM shop_users_clipboard ks
					LEFT JOIN shop_product p ON p.p_id = ks.p_id
					LEFT JOIN shop_product_media pm ON (pm.m_i = (SELECT m_i FROM shop_product_media AS pm2 WHERE pm2.p_id = p.p_id AND pm2.m_jpg = 1 ORDER BY pm2.m_main DESC, pm2.m_order ASC LIMIT 1) )
					WHERE ks.user_id = {$idKlient} ";

			if (is_numeric($ilosc) && $ilosc > 0)
				$sql .= " LIMIT $ilosc ";

			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
			
			foreach($aResult as &$row) {
				$row['link'] = "p-".Misc::utworzSlug($row['p_name']).",".$row['p_id'];
			}

			return $aResult;

		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}
	
	public function getSchowekListSelect($idKlient, $ilosc = 0) {
		
		$all = $this->getSchowekList($idKlient, $ilosc);
		$new = array();
			
		foreach($all as $row) {
			$new[$row['p_id']] = $row['clip_id'];
		}
		return $new;
		
	}
	
	public function getSchowekHtml($user_id) {
		
		$getSchowekList = $this->getSchowekList($user_id);
		ob_start();
		
		include(MyConfig::getValue("templatePatch")."_ajax/konto/a_schowek.php");
		
		$includedphp = ob_get_contents();
		ob_end_clean();
		
		return $includedphp;
	}
		
	public function getInvoicesList($user_id) {

		$sql = "SELECT * FROM shop_users_invoice WHERE 1 AND user_id = ".$user_id." ORDER BY invoice_date DESC";
		try {
			$aResult = array();
			$aResult = ConnectDB::subQuery($sql);
		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		return $aResult;
		
	}
	
	public function saveUser($data) {
		
		try {
			if($data['user_id'] > 0) {
				ConnectDB::subAutoExec('shop_users', $data, 'UPDATE', 'address_id = '.$data['user_id']);
				$res = $data['user_id'];
			} else {
				$res = ConnectDB::subAutoExec('shop_users', $data, 'INSERT');
			}

		}
		catch(PDOException $e) {
			Log::SLog($e->getTraceAsString());
			header("Location: ".MyConfig::getValue("wwwPatch"));
		}
		
		return $res;
		
	}
	
}
