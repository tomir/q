<?php

/**
 * Klasa do obsługi Autoryzacji użytkownika
 *
 * Klasa zwraca informacje:
 * - czy użytkownik jest już zalogowany
 * - login użytkownika
 * - id_uzytkownika
 * - sprawdza dane przy logowaniu i albo zalogowuje użytkownika albo zwraca false
 * 
 */
class Autoryzacja
{
	protected $idUzytkownika ;
	
	protected $loginUzytkownika ;
	
	protected $imieUzytkownika ;
	
	protected $nazwiskoUzytkownika ;
	
	protected $emailUzytkownika ;
	
	protected $grupaUzytkownika ;
	
	protected $dataLogowania ;

	
	protected function __construct()
	{
		//$objectSession = Sesje::singleton();
		if($objectSession -> idUzytkownika > 0 && strlen($objectSession->loginUzytkownika) > 0){
			$this -> idUzytkownika 			= $objectSession -> idUzytkownika ;
			$this -> loginUzytkownika 		= $objectSession -> loginUzytkownika ;
			$this -> imieUzytkownika 		= $objectSession -> imieUzytkownika ;
			$this -> nazwiskoUzytkownika 	= $objectSession -> nazwiskoUzytkownika ;
			$this -> emailUzytkownika 		= $objectSession -> emailUzytkownika ;
			$this -> grupaUzytkownika 		= $objectSession -> grupaUzytkownika ;
			$this -> dataLogowania 			= $objectSession -> dataLogowania ;
		}else{
			 $this -> idUzytkownika 		= 0 ;
			 $this -> loginUzytkownika 		= '' ;
			 $this -> imieUzytkownika 		= '' ;
			 $this -> nazwiskoUzytkownika 	= '';
			 $this -> emailUzytkownika 		= '';
			 $this -> grupaUzytkownika 		= 0;
			 $this -> dataLogowania			= '';
		}
	}
	
	public function singleton()
	{
		static $instance;
		if(!isset($instance))
		{
			$instance = new Autoryzacja();
		}
		return $instance;
	}
	
	public function utworzSesjePanel($idUzytkownika, $loginUzytkownika, $imieUzytkownika, $nazwiskoUzytkownika, $emailUzytkownika, $grupaUzytkownika, $dataLogowania)
	{	
		if($idUzytkownika > 0 && strlen($loginUzytkownika) > 0)
		{	
			$objectSession = Sesje::singleton();
			$objectSession -> idUzytkownika 		= $idUzytkownika ;
			$objectSession -> loginUzytkownika 		= $loginUzytkownika ;
			$objectSession -> imieUzytkownika 		= $imieUzytkownika ;
			$objectSession -> nazwiskoUzytkownika 	= $nazwiskoUzytkownika ;
			$objectSession -> emailUzytkownika 		= $emailUzytkownika ;
			$objectSession -> grupaUzytkownika 		= $grupaUzytkownika ;
			$objectSession -> dataLogowania 		= $dataLogowania ;
			$objectSession -> idSesji 				= $objectSession -> GetSessionIdentifier();
			$this -> idUzytkownika 			= $idUzytkownika ;
			$this -> loginUzytkownika 		= $loginUzytkownika ;
			$this -> imieUzytkownika 		= $imieUzytkownika ;
			$this -> nazwiskoUzytkownika 	= $nazwiskoUzytkownika ;
			$this -> emailUzytkownika 		= $emailUzytkownika ;
			$this -> grupaUzytkownika 		= $grupaUzytkownika ;
			$this -> dataLogowania 			= $dataLogowania ;
		}
	}	
	
	/**
	 * Składowa metoda do rozbudowy
	 *
	 * @return int
	 */
	public function czyIstniejeSesja()
	{
		
		$objectSession    = Sesje::singleton();
		$idUzytkownika    = $objectSession -> idUzytkownika ;
		$loginUzytkownika = $objectSession -> loginUzytkownika;
		$idSesji = $objectSession -> idSesji;
		
		if($idUzytkownika > 0 && strlen($loginUzytkownika) > 0 && $idSesji == $objectSession->GetSessionIdentifier())
		{
			return true ;
		}
		else {
			return false ;
		}
			
	}
	
	public function usunSesje()
	{
		$objectSession    = Sesje::singleton();
		$objectSession -> delVar('idUzytkownika');
		$objectSession -> delVar('loginUzytkownika');
		$objectSession -> delVar('imieUzytkownika');
		$objectSession -> delVar('nazwiskoUzytkownika');
		$objectSession -> delVar('emailUzytkownika');
		$objectSession -> delVar('grupaUzytkownika');
		$objectSession -> delVar('dataLogowania');
		$objectSession -> destroySession();
		return true;
	}
	
	/**
	 * Do rozbudowy
	 *
	 * @return string
	 */
	public function getLogin()
	{
		$objectSession    = Sesje::singleton();
		return $objectSession -> loginUzytkownika;
	}
	
	public function getImie()
	{
		$objectSession    = Sesje::singleton();
		return $objectSession -> imieUzytkownika;
	}
	
	public function getNazwisko()
	{
		$objectSession    		= Sesje::singleton();
		return $objectSession -> nazwiskoUzytkownika;
	}
	
	public function getGrupa()
	{
		$objectSession    		= Sesje::singleton();
		return $objectSession -> grupaUzytkownika;
	}
	
	public function getDataLog()
	{
		$objectSession    		= Sesje::singleton();
		return $objectSession -> dataLogowania;
	}
	
	public function getEmail()
	{
		$objectSession    		= Sesje::singleton();
		return $objectSession -> emailUzytkownika;
	}

	public function getIdUzytkownika()
	{
		$objectSession    = Sesje::singleton();
		return $objectSession -> idUzytkownika ;
	}
	
}


?>