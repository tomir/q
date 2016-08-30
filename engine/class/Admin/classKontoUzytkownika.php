<?php

require_once 'Zend/Mail.php';
require_once 'Zend/Mail/Transport/Smtp.php';

/**
 * Klasa obsługuje wszy
 *
 */
class KontoUzytkownika
{
	protected $smarty;
	/**
	 * Zmienna przechowuje adres do katalogu szablonów
	 *
	 * @var unknown_type
	 */
	protected $szablonSmartyPath ;

	protected $rodzajAlgorytmu = 'sha1';

	public function __construct() {
		$this -> smarty = new SmartyLoad(MyConfig::getValue("templatePatch"),"admin/");
	}
	/**
	 * Funkcja wywoływana podczas procesu rejestracji użytkownika, korzysta ze zmiennych $_POST
	 * i jeżeli zgadzają się z założeniami zwraca true, w przeciwnym wypadku zwraca
	 * tablice błędów wykrytych w danych z formularza
	 *
	 */	
	public function sprawdzDaneRejestracyjne()
	{
		// najpierw pobieramy wszystkie dane z $_POSTa
		$imie     	= $_POST['imie'] ;
		$nazwisko 	= $_POST['nazwisko'] ;
		$firma    	= $_POST['firma'] ;
		$nip   		= $_POST['nip'] ;
		$ulica   	= $_POST['ulica'] ;
		$kod   		= $_POST['kod'] ;
		$miasto   	= $_POST['miasto'] ;
		$email    	= $_POST['email'] ;
		$telefon 	= $_POST['telefon'] ;
		$uwagi 		= $_POST['uwagi'] ;
		$zgoda 		= $_POST['zgoda'] ;


		// inicjujemy tablice blędów, jeżeli jakieś wystąpiął
		// funkcja zwórci tą tablice
		$tablicaBledow = array();

		// sprawdzamy imie
		if (strlen($imie) < 3|| strlen($imie) > 30)
		{
			$blad = "Pole imię nie zostało wypełnione poprawnie." ;
			array_push($tablicaBledow, $blad) ;
			// sprawdzamy czy login nie zawiera niedozwolonych znaków
		}

		if (!$zgoda)
		{
			$blad = "Wymagana jest zgoda na otrzymywanie informacji handlowej." ;
			array_push($tablicaBledow, $blad) ;
			// sprawdzamy czy login nie zawiera niedozwolonych znaków
		}

		// sprawdzamy nazwisko
		if (strlen($nazwisko) < 3|| strlen($nazwisko) > 30)
		{
			$blad = "Pole nazwisko nie zostało wypełnione poprawnie." ;
			array_push($tablicaBledow, $blad) ;
			// sprawdzamy czy login nie zawiera niedozwolonych znaków
		}

		// sprawdzamy login
		if (strlen($ulica) < 3 || strlen($ulica) > 50)
		{
			$bladLogin1 = "Pole ulica nie zostało wypełnione poprawnie." ;
			array_push($tablicaBledow, $bladLogin1) ;
			// sprawdzamy czy login nie zawiera niedozwolonych znaków
		}

		if (strlen($kod) < 3 || strlen($kod) > 6)
		{
			$bladLogin1 = "Pole kod pocztowy nie zostało wypełnione poprawnie." ;
			array_push($tablicaBledow, $bladLogin1) ;
			// sprawdzamy czy login nie zawiera niedozwolonych znaków
		}
		
		if (strlen($miasto) < 3 || strlen($miasto) > 60)
		{
			$bladLogin1 = "Pole miasto nie zostało wypełnione poprawnie." ;
			array_push($tablicaBledow, $bladLogin1) ;
			// sprawdzamy czy login nie zawiera niedozwolonych znaków
		}
		
		if (strlen($email) < 3 || strlen($email) > 60)
		{
			$bladLogin1 = "Pole email nie zostało wypełnione poprawnie." ;
			array_push($tablicaBledow, $bladLogin1) ;
			// sprawdzamy czy login nie zawiera niedozwolonych znaków
		}

		// teraz sprawdzamy czy wystąpiły jakieś błędy jeżeli tak zwracamy tablice z błędami
		// w przeciwnym przypadku zwracamy TRUE
		if( count($tablicaBledow) > 0)
		{
			return $tablicaBledow ;
		}
		else
		{
			$wynikDopisania = $this -> dopiszUzytkownika($imie,$nazwisko,$firma,$nip,$ulica,$kod,$miasto,$email,$telefon);
			if($wynikDopisania == true)
			{
				$czyWyslano = $this -> wyslijPotwierdzenieRejestracji($imie,$nazwisko,$firma,$nip,$ulica,$kod,$miasto,$email,$telefon,$uwagi);
				if($czyWyslano == true){
					return true;
				}else{
					$komunikat = "<br />Nie można wysłać wiadomości z potwierdzeniem rejestracji. Nie udało sie nawiązać połączenia z serwerem pocztowym. Prosimy o kontakt w celu potwierdzenia rejestracji ręcznie przez administratora.";
				}
				return $komunikat;
			}else{
				$komunikat = 'Nie można ukończyć procesu rejestracji. Wystąpił problem z połączeniem z bazą danych. Prosimy spróbować ponownie.' ;
				return $komunikat;
			}
		}

	}


	/**
	 * Funkcja wywoływana podczas procesu rejestracji użytkownika, korzysta ze zmiennych $_POST
	 * i jeżeli zgadzają się z założeniami zwraca true, w przeciwnym wypadku zwraca
	 * tablice błędów wykrytych w danych z formularza
	 *
	 */
	public function dopiszUzytkownika($imie, $nazwisko, $firma, $nip, $ulica, $kod, $miasto, $email, $telefon)
	{
		// dopisujemy nowego użytkownika do bazy
		try {
			$pdo = new ConnectDB();
			
			$sql = "INSERT INTO users (imie, nazwisko, login, haslo, miasto, email, telefon) 
											VALUES (:imie, :nazwisko, :firma, :nip, :ulica, :kod, :miasto, :email, :telefon)";
			
			$wynik = $pdo -> prepare($sql) ;
			$wynik -> bindValue (':imie'        , $imie         , PDO::PARAM_STR) ;
			$wynik -> bindValue (':nazwisko'    , $nazwisko 	, PDO::PARAM_STR) ;
			$wynik -> bindValue (':firma' 		, $firma  		, PDO::PARAM_STR) ;
			$wynik -> bindValue (':nip'			, $nip	 		, PDO::PARAM_STR) ;
			$wynik -> bindValue (':ulica' 		, $ulica		, PDO::PARAM_STR) ;
			$wynik -> bindValue (':kod' 		, $kod			, PDO::PARAM_STR) ;
			$wynik -> bindValue (':miasto' 		, $miasto		, PDO::PARAM_STR) ;
			$wynik -> bindValue (':email' 		, $email		, PDO::PARAM_STR) ;
			$wynik -> bindValue (':telefon'		, $telefon		, PDO::PARAM_STR) ;
			$liczbaDopisanych = $wynik -> execute();

			if($liczbaDopisanych  == 1)
				return true ;
			else
				return false ;
	
		}catch (PDOException $e){
			opdErrorHandler($e) ;
		}
	}
		
	public function sprawdzDaneLogowania()
	{
		// najpierw pobieramy wszystkie dane z $_POSTa
		$login = $_POST['login'] ;
		$haslo = $_POST['haslo'] ;
		$haslo_crypt = sha1($_POST['haslo']);

		$tablicaBledow = array();
		try{
			$pdo = new ConnectDB() ;

			$sql = " SELECT *
					FROM users
					WHERE login like '".$login."' 
						AND haslo like '".$haslo_crypt."'";			
			
			$wynik = $pdo -> query($sql);
			$tablicaWynikow = $wynik -> fetchAll();
			if(count($tablicaWynikow) != 1 )
			{
				header("Location: ".MyConfig::getValue("wwwPatchPanel")."logowanie.html,1,login_error");
				
			}else{
				foreach ($tablicaWynikow as $wiersz)
				{
					if($wiersz['active'] == 0) {
						header("Location: ".MyConfig::getValue("wwwPatchPanel")."logowanie.html,1,active_error");
						exit();
					}
					$idUzytkownikaBaza 	= $wiersz['id_user'];
					$loginBaza 			= $wiersz['login'];
					$hasloBazaCrypt 	= $wiersz['haslo'];
					$imie				= $wiersz['imie'];
					$nazwisko			= $wiersz['nazwisko'];
					$email				= $wiersz['email'];
					$id_grupy			= $wiersz['id_grupy'];
					$data_log			= $wiersz['login_last_date'];
				}
			}
			$wynik -> closeCursor();
		}catch (PDOException $e){
			$blad = "Niestety chwilowo nie możemy sprawdzić danych użytkownika w bazie danych. Prosimy spróbować ponownie później." ;
			array_push($tablicaBledow, $blad) ;
		}

		// teraz sprawdzamy czy wystąpiły jakieś błędy jeżeli tak zwracamy tablice z błędami
		// w przeciwnym przypadku zwracamy TRUE
		if( count($tablicaBledow) > 0)
		{
			return false ;
		}else{
			$sql = "UPDATE users SET login_last_date = login_date, login_date = now() WHERE id_user = ".$idUzytkownikaBaza;			
			$pdo -> exec($sql);
			$obiektAutoryzacji = Autoryzacja::singleton();
			$obiektAutoryzacji -> utworzSesjePanel($idUzytkownikaBaza, $loginBaza, $imie, $nazwisko, $email, $id_grupy, $data_log);
			$czyUtworzonoSesje = $obiektAutoryzacji -> czyIstniejeSesja();
			return $czyUtworzonoSesje;
		}
		
	}
	
	public function pobierzStroneLogowania() {
		$this-> smarty-> assign('wwwPatch', MyConfig::getValue("wwwPatch"));
		$this-> smarty-> assign('serverPatch', MyConfig::getValue("serverPatch"));
		$finalContent = $this->smarty -> fetch("loginSite.tpl");
		return $finalContent;
	}
	
	public function pobierzStroneZmianyHasla() {
		$this-> smarty-> assign('wwwPatch', MyConfig::getValue("wwwPatch"));
		$this-> smarty-> assign('serverPatch', MyConfig::getValue("serverPatch"));
		$finalContent = $this->smarty -> fetch("changePassSite.tpl");
		return $finalContent;
	}
	
	public function zmienHaslo($aPost, $login) {
		
		if($aPost['haslo'] != '') {
			if($aPost['haslo_new'] != '' && $aPost['haslo_new_2'] != '' && $aPost['haslo_new'] == $aPost['haslo_new_2']) {
				$haslo_crypt = sha1($aPost['haslo']);
				$pdo = new ConnectDB() ;

				$sql = " SELECT id_user, login, haslo, imie, nazwisko, email, id_grupy
						 FROM users
					 	 WHERE login like '".$login."' 
						 AND haslo like '".$haslo_crypt."' AND active = 1";
				
				$wynik = $pdo -> query($sql);
				$tablicaWynikow = $wynik -> fetchAll();
				if(count($tablicaWynikow) == 1) {
					$haslo_crypt_new = sha1($aPost['haslo_new']);
					$sql = "UPDATE users SET haslo = '".$haslo_crypt_new."' WHERE id_user = ".$tablicaWynikow[0]['id_user'];
					$wynik2 = $pdo -> exec($sql);
					if($wynik2 > 0)
						return true;
					else
						return false;
				}
				else {
					return false;
				}
			}
			else
				return false;
		}
		else
			return false;
	}
	
	public function pobierzListeUserow() {
		
		$pdo = new ConnectDB() ;
		$sql = "SELECT * FROM users u LEFT JOIN users_grupy ug ON u.id_grupy = ug.id_grupy ORDER BY login";			
		$wynik = $pdo -> query($sql);
		$tablicaWynikow = $wynik -> fetchAll();
		$this-> smarty-> assign('aUser', $tablicaWynikow);
		$this-> smarty-> assign('wwwPatch', MyConfig::getValue("wwwPatch"));
		$this-> smarty-> assign('wwwPatchPanel', MyConfig::getValue("wwwPatchPanel"));
		$this-> smarty-> assign('serverPatch', MyConfig::getValue("serverPatch"));
		$finalContent = $this->smarty -> fetch("usersSite.tpl");
		return $finalContent;
	}
	
	public function usunUsera($id_user=0) {
		if($id_user) {
			$pdo = new ConnectDB() ;
			$sql = "DELETE FROM users WHERE id_user = ".$id_user;
			$wynik = $pdo -> exec($sql);
			if($wynik>0)
				return true;
			else
				return false;
		}
	}
	
	public function getUserAjax($id_user=0) {
		if($id_user) {
			$pdo = new ConnectDB() ;
			$sql = "SELECT * FROM users WHERE id_user = ".$id_user." ORDER BY login";		
			$wynik = $pdo -> query($sql);
			$tablicaWynikow = $wynik -> fetchAll();
			
			$sql = "SELECT * FROM users_grupy ORDER by nazwa";
			$wynik2 = $pdo -> query($sql);
			$tablicaWynikow2 = $wynik2 -> fetchAll();
			
			foreach($tablicaWynikow as $row) {
				echo '<td>'.$row['id_user'].'</td>';
				echo '<td><input type="text" value="'.$row['login'].'" name="login" id="login" /></td>';
				echo '<td><input type="text" value="'.$row['email'].'" name="email" id="email" /></td>';
				echo '<td><input type="text" value="'.$row['imie'].'" name="imie" id="imie" /></td>';
				echo '<td><input type="text" value="'.$row['nazwisko'].'" name="nazwisko" id="nazwisko" /></td>';
				echo '<td></td>';
				echo '<td><select name="grupa" id="grupa">';
				foreach($tablicaWynikow2 as $row2) {
					if($row2['id_grupy'] == $row['id_grupy']) $selected = 'selected="selected"';
					else $selected = "";
					echo '<option value="'.$row2['id_grupy'].'" '.$selected.'>'.$row2['nazwa'].'</option>';
				}
				echo '</select></td>';
				echo '<td><input type="hidden" name="edited_userid" id="edited_userid" value="'.$row['id_user'].'" /><button value="Zapisz" name="zapisz_form" id="zapisz_form">Zapisz</button></td>';
			}
		}	
	}
	
	public function saveUserAjax($id_user) {
		if($id_user) {
			$pdo = new ConnectDB() ;
			$sql = "UPDATE users SET login = :login, imie = :imie, nazwisko = :nazwisko, email = :email, id_grupy = :id_grupy WHERE id_user=".$id_user;
			
			$wynik = $pdo -> prepare($sql) ;
			$wynik -> bindValue (':login'  		  , $_POST['login'] 			, PDO::PARAM_STR) ;
			$wynik -> bindValue (':imie' 	      , $_POST['imie']			    , PDO::PARAM_STR) ;
			$wynik -> bindValue (':nazwisko'  	  , $_POST['nazwisko'] 			, PDO::PARAM_STR) ;
			$wynik -> bindValue (':email' 	      , $_POST['email']			    , PDO::PARAM_STR) ;
			$wynik -> bindValue (':id_grupy'  	  , $_POST['grupa'] 			, PDO::PARAM_INT) ;
			
			$liczbaZmian = $wynik -> execute();
			
			$sql = "SELECT * FROM users u LEFT JOIN users_grupy ug ON u.id_grupy = ug.id_grupy WHERE id_user = ".$id_user." ORDER BY login";			
			$wynik = $pdo -> query($sql);
			$tablicaWynikow = $wynik -> fetchAll();
			if(count($tablicaWynikow) > 0) {
				foreach($tablicaWynikow as $row) {
					
					echo '<td>'.$row['id_user'].'</td>';
					echo '<td>'.$row['login'].'</td>';
					echo '<td>'.$row['email'].'</td>';
					echo '<td>'.$row['imie'].'</td>';
					echo '<td>'.$row['nazwisko'].'</td>';
					if($row['active'] == 1)
						echo '<td style="text-align: center;"><a rel="show_user" href="javascript:void(0);" title="Zmień status użytkownika" id="'.$row['id_user'].'" ><img style="padding: 3px;border: 0;" src="'.MyConfig::getValue("wwwPatch").'img/admin/main_on.jpg" alt="Zmień status użytkownika" /></a></td>';
					else
						echo '<td style="text-align: center;"><a rel="show_user" href="javascript:void(0);" title="Zmień status użytkownika" id="'.$row['id_user'].'" ><img style="padding: 3px;border: 0;" src="'.MyConfig::getValue("wwwPatch").'img/admin/main_off.jpg" alt="Zmień status użytkownika" /></a></td>';
					echo '<td style="text-align: center;">'.$row['nazwa'].'</td>';
					echo '<td style="text-align: center;">
							  <a href="javascript:void(0);" rel="edit_form" title="Edytuj"><img src="../img/admin/edit.jpg" id="'.$row['id_user'].'" alt="Edytuj" style="border: 0;" /></a>
							  <a href="javascript:void(0);" rel="delete_form" title="Usuń"><img src="../img/admin/delete.gif" id="'.$row['id_user'].'" alt="Usuń" style="padding-bottom: 2px; border: 0;" /></a>
						  </td>';
				}
			}
		}
	}
	
	public function changeUserAjax($id_user) {
		if($id_user) {
			$pdo = new ConnectDB() ;
			$sql = "SELECT active FROM users WHERE id_user=".$id_user;
			$wynik = $pdo -> query($sql);
			$tablicaWynikow = $wynik -> fetchAll();
			if($tablicaWynikow[0]['active'] == 1) {
				$sql = "UPDATE users SET active = 0 WHERE id_user=".$id_user;
				$pdo -> query($sql);
				echo MyConfig::getValue("wwwPatch").'img/admin/main_off.jpg';
			}
			else {
				$sql = "UPDATE users SET active = 1 WHERE id_user=".$id_user;
				$pdo -> query($sql);
				echo MyConfig::getValue("wwwPatch").'img/admin/main_on.jpg';
			}
		}
	}
	
	public function showContactForm() {
		

		$this-> smarty-> assign('wwwPatch', MyConfig::getValue("wwwPatch"));
		$this-> smarty-> assign('wwwPatchPanel', MyConfig::getValue("wwwPatchPanel"));
		$this-> smarty-> assign('serverPatch', MyConfig::getValue("serverPatch"));
		$finalContent = $this->smarty -> fetch("contactSite.tpl");
		return $finalContent;
	}
	
	public function sendForm($aPost, $email, $login, $imie, $nazwisko) {
		
		//wysyłanie do klienta
		if($aPost['rodzaj'] == "blad") {
			$tytulWiadomosci = "Zgłoszenie błędu w systemie tForm zostało dostarczone";
			$trescWiadomosciHtml = 	'Witaj <b>'.$login.'</b><br />
									 Administrator systemu tForm otrzymał własnie wiadomość o błędzie.<br />
									 W najbliższym czasie zespół postara się zdiagnozować i w razie potrzeby naprawić występujący błąd.
									 Nie zwłocznie po zapoznaniu się z problemem zostaną Państwo o tym poinformowani.<br /><br />
									 W razie jakichkolwiek wątpliwości zachęcamy do ponownego skorzystania z formularza kontaktowego.<br /><br /><br />
									 ---------------------------------<br />
									 Zespół tform.net<br />
									 Systemy zarządzania treścią<br />
									';
		}
		else {
			$tytulWiadomosci = "Wiadomość w systemie tForm została dostarczona";
			$trescWiadomosciHtml = 	'Witaj <b>'.$login.'</b><br />
									 Dziękujemy za wiadomość.<br />
									 Nie zwłocznie po jej przeczytaniu skontaktujemy się z Państwem.<br /><br /><br />
									 ---------------------------------<br />
									 Zespół tform.net<br />
									 Systemy zarządzania treścią<br />
									';
		}

		
		try {
			$mail = new Zend_Mail('utf-8');
			//$mail->setBodyText($trescWiadomosciText);
			$trescWiadomosciHtml = str_replace(array("\n", "\r"), " " ,$trescWiadomosciHtml);
			$mail->setBodyHtml($trescWiadomosciHtml);
			$mail->setFrom(MyConfig::getValue("adminEmail"),  "Administrator tForm");
			$mail->addTo($email, $imie." ".$nazwisko);
			$mail->setSubject($tytulWiadomosci);
			/*$config = array('auth' => 'login',
			                'username' => ,
			                'password' => ;
			require_once 'Zend/Mail/Transport/Smtp.php';
			$transport = new Zend_Mail_Transport_Smtp($EmailSerwer, $config);*/
			$mail->send();
			return true;
		}catch(Zend_Mail_Transport_Exception $e )
		{
			Log::SLog($e->getMessage().' '.$e->getTraceAsString());
			return false;
		}catch (Exception $e){
			Log::SLog($e->getMessage().' '.$e->getTraceAsString());
			return false;
		}
		
		//wysyłanie do admina
		if($aPost['rodzaj'] == "blad") {
			$tytulWiadomosci = "Zgłoszenie błędu w działaniu systemu tForm (od: ".$login.")";
			$trescWiadomosciHtml = 	$imie.' '.$nazwisko.' ('.$email.') wysyła informację o błędzie<br />
									 Treść wiadomości:<br />
									 '.$aPost['tresc'].'<br /><br /><br />
									 ---------------------------------<br />
									 System zgłaszania błędów tform.net<br />
									';
		}
		else {
			$tytulWiadomosci = "Została wysłana wiadomość w systemie tForm (od: ".$login.")";
			$trescWiadomosciHtml = 	 $imie.' '.$nazwisko.' ('.$email.') wysyła wiadomość<br />
									  Treść wiadomości:<br />
									 '.$aPost['tresc'].'<br /><br /><br />
									 ---------------------------------<br />
									 System wysyłania wiadomości tform.net<br />
									';
		}

		
		try {
			$mail = new Zend_Mail('utf-8');
			//$mail->setBodyText($trescWiadomosciText);
			$trescWiadomosciHtml = str_replace(array("\n", "\r"), " " ,$trescWiadomosciHtml);
			$mail->setBodyHtml($trescWiadomosciHtml);
			$mail->setFrom("system@tform.net",  "System tForm");
			$mail->addTo(MyConfig::getValue("adminEmail"), MyConfig::getValue("adminImie")." ".MyConfig::getValue("adminNazwisko"));
			$mail->setSubject($tytulWiadomosci);
			/*$config = array('auth' => 'login',
			                'username' => ,
			                'password' => ;
			require_once 'Zend/Mail/Transport/Smtp.php';
			$transport = new Zend_Mail_Transport_Smtp($EmailSerwer, $config);*/
			$mail->send();
			return true;
		}catch(Zend_Mail_Transport_Exception $e )
		{
			Log::SLog($e->getMessage().' '.$e->getTraceAsString());
		}catch (Exception $e){
			Log::SLog($e->getMessage().' '.$e->getTraceAsString());
		}
	}

}




?>