<?php

define('CUSTOMER_LOGIN_OK', 1);
define('CUSTOMER_LOGIN_USER_NOT_FOUND', 2);
define('CUSTOMER_LOGIN_INCORRECT_PASS', 3);
define('CUSTOMER_LOGIN_NOT_ACTIVATED', 4);
define('CUSTOMER_LOGIN_ERROR', 5);
define('CUSTOMER_EXISTS', 6);

/**
 * Klasa obslugujaca klienta
 *
 * @package Customer
 */
class Klient
{
	public $id;
	public $data;

	function __construct( $id=0 )
	{
		$this->id = (int)$id;
		
		if( $this->id > 0 )
		{
			$this->data = $this->getData();
		}
	}
	
	/**
	 * Pobiera dane obiektu i zwraca w postaci tablicy
	 *
	 * @return array
	 *
	 */
	function getData() 
	{
		try {

			$sql = "SELECT k.*
					FROM ".MyConfig::getValue("dbPrefix")."klient k
					WHERE k.id = '".(int)$this->id."'";

			$row = (array)ConnectDB::subQuery($sql,'fetch');
			
			$waluty = $this->getWaluty();
			
			foreach($waluty as $row2) {
				$row['saldo_'.$row2['nazwa']] = $row['saldo']/$row2['kurs'];
			}

			$row['saldo_PLN'] = $row['saldo'];
			
			return $row;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
	}
	
	public function getWaluty() {
		
		$sql = "SELECT w.kurs, t.nazwa FROM autosalon_waluty w LEFT JOIN autosalon_car_price_type t ON w.price_type = t.price_type_id WHERE 1";
		
		$all = ConnectDB::subQuery($sql);
		foreach( $all as $row) {
			$new[$row['nazwa']] = $row;
		}
		return $new;
	}
	
	function getDataAdmin() 
	{
		try {

			$sql = "SELECT k.*
					FROM ".MyConfig::getValue("dbPrefix")."klient k
					WHERE k.id = '".(int)$this->id."'";

			$row = (array)ConnectDB::subQuery($sql,'', '', 'fetch');

			
			return $row;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
	}

	/**
	 * Zapisuje nowego klienta w bazie
	 *
	 * @param array $dane
	 * @return true lub ExceptionCode
	 */
	function saveData( $data ) {
		
		try {

			if( (int)$data['id'] > 0 )
				return $this->updateData( $data );

			$data['data_rejestracji'] = date("Y-m-d H:i:s");

			$id = ConnectDB::subAutoExec(MyConfig::getValue("dbPrefix")."klient" ,$data,'INSERT');

			if (is_object($this)){
				$this->id = $id;

				$this->sendMailRejestracja(array('__KONTO_HASLO__' =>$data['haslo_plain'], '__IMIE__'=>$data['imie'], '__NAZWISKO__'=>$data['nazwisko'], '__EMAIL__'=>$data['email']));

			}

			return true;

		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return $e->getCode();
		}
	}

	/**
	 * Aktualizuje dane obiektu w bazie. Tablica wejsciowa musi zawierac klucz 'id'
	 *
	 * @param array $dane
	 * @return true lub ExceptionCode
	 */
	static function updateData( $data ) {

		try {

			$id = $data['id'];

			ConnectDB::subAutoExec(MyConfig::getValue("dbPrefix")."klient",$data,'UPDATE'," id = $id ");
			return true;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return $e->getCode();
		}
	}

	/**
	 * Sprawdza czy dla danego serwisu już istnieje klient o podanym adresie e-mail
	 *
	 * @param int $id_serwisu
	 * @param string $email
	 * @return true lub ExceptionCode
	 */
	static function checkUser( $email )
	{
		
		try {

			$sql = "SELECT count(id) FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email = '$email' "; 
			$one = ConnectDB::subQuery($sql,'one');
			if($one == ' ')
				$one = 0;
			return $one;
						
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return $e->getCode();
		}
	}

	/**
	 * uwa rekord z bazy
	 *
	 * @param unknown_type $id
	 * @return true lub ExceptionCode
	 */
	static function deleteData( $id )
	{
		try {

			$sql = "DELETE FROM ".MyConfig::getValue("dbPrefix")."klient WHERE id = ".$id;
			ConnectDB::subExec($sql);

			return true;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return $e->getCode();
		}
	}

	/**
	 * Zwraca liste obiektow odfiltrowana wg tablicy $filtr, posortowana wg tablicy $sort oraz w przedziale podanym w tablicy $limit
	 *
	 * @param array $filtr	Tablica asocjacyjna filtrujaca w postaci $filtr['pole'] = 'wartosc'
	 * @param array $sort	Tablica asocjacyjna okreslajaca sortowanie, musi zawierac klucz 'sort' okreslajacy po ktorym polu sortujemy oraz klucz 'order' okreslajacy kolejnosc sortowania (ASC/DESC)
	 * @param array $limit	Tablica asocjacyjna okreslajaca przedzial zwracanych wynikow, musi zawierac klucz 'start' okreslajacy od ktorego rekordu pobieramy wyniki oraz klucz 'limit' okreslajacy ile rekordow ma zostac zwroconych
	 * @return array
	 */
	static function getList( $filtr=NULL, $sort = NULL, $limit = NULL )
	{
		try {

			$sql = "SELECT
						k.*
						FROM ".MyConfig::getValue("dbPrefix")."klient k
						WHERE 1 ";

			//$sql.= self::filterGetList($filtr);

			$sql.= self::getSort($sort);

			$sql.= self::getLimit($limit);
			
			$all = ConnectDB::subQuery($sql);	//d($sql);

			return $all;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
	}
	
	function getSort( $sort = NULL )
	{
		$sql = '';

		if( isset($sort) && is_array($sort) )
		{
			if( isset($sort['sort']) && eregi('^[a-zA-Z0-9_\._]*$', $sort['sort']) &&
				isset($sort['order']) && eregi('^[a-zA-Z0-9\._]*$', $sort['order']) )
			{
				if(is_numeric($sort['order']))
					$sql.= " ORDER BY ".$sort['sort']."=".$sort['order']." DESC, ".$sort['sort']." ASC";
				else
					$sql.= " ORDER BY ".$sort['sort']." ".$sort['order'];
			}
		}
		return $sql;
	}

	/**
	 * Generuje kod SQL ograniczajacy ilosc zwracanych wynikow wg tablicy asocjacyjnej $limit
	 *
	 * @param array $limit Tablica asocjacyjna okreslajaca przedzial zwracanych wynikow, musi zawierac klucz 'start' okreslajacy od ktorego rekordu pobieramy wyniki oraz klucz 'limit' okreslajacy ile rekordow ma zostac zwroconych
	 * @return string
	 */
	function getLimit( $limit = NULL )
	{
		$sql = '';

		if( isset($limit) && is_array($limit) )
		{
			if( isset($limit['start']) && is_numeric($limit['start']) &&
				isset($limit['limit']) && is_numeric($limit['limit']) )
			{
				$sql.= " LIMIT ".$limit['start'].", ".$limit['limit'];
			}
		}
		return $sql;
	}

	/**
	 * zwraca ilosc pozycji wg zadanych kryteriow z pominieciem LIMIT
	 *
	 * @param array $filtr
	 * @return int
	 */
	static function getListQty( $filtr = NULL )
	{
		try {

			$sql = "SELECT COUNT(k.id) AS ilosc FROM ".MyConfig::getValue("dbPrefix")."klient k WHERE 1 ";

			//$sql.= self::filterGetList($filtr);
			
			$val = ConnectDB::subQuery($sql,'one');
			if(!is_int($val))
				$val = 0;
			return (int)$val;

		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return null;
		}
	}

	/**
 	 * Przeprowadza autoryzacje klienta
 	 *
 	 * @param string $email
 	 * @param string $password
 	 * @return int
 	 */
	static function login( $email, $password, $cookie_ok = 0)
	{

		try {

			if( Klient::checkUser($email) > 0 )
			{ 
				$sql = "SELECT COUNT(id) AS ilosc FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email='".$email."' AND haslo='".md5($password)."' AND blokada=0 ";
				$val = ConnectDB::subQuery( $sql, "one" );

				if( $val==0 )
				{
					$val = ConnectDB::subQuery("SELECT COUNT(id) AS ilosc FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email='".$email."' ", "one");

					if( $val>0 )
						return CUSTOMER_LOGIN_INCORRECT_PASS;
					else
						return CUSTOMER_LOGIN_USER_NOT_FOUND;
				}else{
				
					$sql = "SELECT * FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email='".$email."' AND haslo='".md5($password)."' ";
					$row = ConnectDB::subQuery( $sql, "fetch" );

		 			$_SESSION['klientID'] = (int)$row['id'];
					$_SESSION['klient_email'] = $row['email'];
					
					if($cookie_ok > 0) {
						
						$hash = md5(time()."WE%#GsdssDTSI394");
						setcookie("au_login_cookie", $hash, time()+(3600*24*365));
						
						$coockie_sql = ", hash = '".$hash."' ";
					}

					$sql = "UPDATE ".MyConfig::getValue("dbPrefix")."klient SET ostatnia_wizyta = NOW()".$coockie_sql." WHERE id = '".(int)$_SESSION['klientID']."'";
					$res = ConnectDB::subExec( $sql );


					return CUSTOMER_LOGIN_OK;
				}
			}
			else 
				return CUSTOMER_LOGIN_USER_NOT_FOUND;

			return CUSTOMER_LOGIN_ERROR;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return CUSTOMER_LOGIN_ERROR;
		}
	}
	
	static public function checkCookie($hash = '') {
		
		if($hash == '') {
			return false;
		}
		global $_gTables;
		try {
			$db = Db::getInstance();

			$sql = "SELECT id, email, imie FROM ".MyConfig::getValue("dbPrefix")."klient WHERE hash='".$hash."' ";
			$row = $db->GetRow( $sql );
			
			if(is_array($row) && count($row) > 0 && $row['id'] > 0) {
			
				$_SESSION['klientID'] = (int)$row['id'];
				$_SESSION['klient_email'] = $row['email'];
				
				$sql = "UPDATE ".MyConfig::getValue("dbPrefix")."klient SET ostatnia_wizyta = NOW() WHERE id = '".(int)$_SESSION['klientID']."'";
				$res = $db->Execute( $sql );

				setcookie("ebox_imie", $row['imie'], time()+(30*24*3600));
				setcookie("ebox_email", $row['email'], time()+(30*24*3600));
					
				return true;
			}

		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
		
	}

	static function checkLogin( $email, $password )
	{

		try {

			$sql = "SELECT COUNT(id) FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email='".$email."' AND haslo='".md5($password)."' ";
			$val = ConnectDB::subQuery( $sql, "one" );

			return ( (int)$val > 0 ? true : false );
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}


	/**
	 * Usuwa biezaca sesje i wylogowuje klienta
	 *
	 * @param string $url
	 */
	static function logout($url=NULL)
	{
		
		setcookie("au_login_cookie", '', 0);
		unset($_COOKIE['au_login_cookie']);
		
		unset($_SESSION['klientID']);
		session_destroy();

		if( isset($url) )
			Common::redirect( $url );
		else
			Common::redirect( 'index.html' );
	}

	/**
	 * Zwraca ID klienta na podstawie podanego maila lub 0 jesli nie znaleziono klienta
	 *
	 * @param string $email
	 * @return int
	 */
	static public function getIDByEmail( $email )
	{

		try {

			$sql = "SELECT id FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email='".$email."' ";
			$id = ConnectDB::subQuery( $sql, "one" );

			return $id;
		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}


	/**
	 * Sprawdza czy podany adres email nalezy do klienta o podanym ID. Uzywane w edycji konta.
	 *
	 * @param string $email
	 * @param int $klient_id
	 * @return bool
	 */
	function checkEmailExists( $email, $klient_id )
	{

		try {

			$sql = "SELECT COUNT(id) AS ilosc FROM ".MyConfig::getValue("dbPrefix")."klient WHERE email = '".$email."' AND id <> '".(int)$klient_id."' ";

			$val = ConnectDB::subQuery( $sql, "one" );

			if( $val == 0 )
			{
				return false;
			}else{
				return true;
			}

		}
		catch (Exception $e) {
			Common::log(__CLASS__.'::'.__METHOD__,$sql."\n".$e->getMessage());
			return false;
		}
	}


	public function sendMailRejestracja($parametryMaila = array())
	{
		$obMail = new Mail();
		$obMail->setReceiver($parametryMaila['__EMAIL__']);
		$obMail->setFrom('rejestracja@autolicytacje.pl');
		$obMail->setSubject("Rejestracja w serwisie autolicytacje.pl");
		$obMail->generateMailTemplate('rejestracja', $parametryMaila);
		$obMail->send();
	}

	public function sendMailNoweHaslo($parametryMaila = array())
	{
		$obMail = new Mail();
		$obMail->setReceiver($parametryMaila['__EMAIL__']);
		$obMail->setFrom('rejestracja@autolicytacje.pl');
		$obMail->setSubject("Rejestracja w serwisie autolicytacje.pl");
		$obMail->generateMailTemplate('nowehaslo', $parametryMaila);
		$obMail->send();
	}

	public function generujHaslo($old = 0)
	{
		$dane['id'] = $this->id;
		$pass = Klient::createPassword();
		$dane['haslo'] = md5( $pass );
		
		$res = $this->updateData($dane);
		if ($res !== true) {
			Common::log(__CLASS__.'::'.__METHOD__,"Problem podczas generowania nowego hasla");
			return $res;
		}

		$this->sendMailNoweHaslo(array('__KONTO_HASLO__' =>$pass));
		

		return true;
	}

	static function createPassword()
	{
		$salt = "abchefghjkmnpqrstuvwxyz0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($salt, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}

	
}




?>