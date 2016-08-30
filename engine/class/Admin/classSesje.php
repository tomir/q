<?php

/**
 * Klasa do obsługi Sesji
 *
 * Klasa obsługuje sesje w php
 * 
 * 
 */

	class Sesje
	{
		private $phpSessionID;
		private $nativeSessionID;
		private $dbHandle;
		private $sessionTimeout		= 3600;		# 60 minutowy maksymalny czas nieaktywności sesji
		private $sessionLifespan	= 14400;	# 4 godzinny maksymalny czas trwania sesji.
		private	$botList			= array('bot', 'google', 'yahoo', 'crawler', 'szukaj', 'onet', 'spider', 'slurp');
		private	$strUserAgent;

		protected function __construct()
		{
			# Łączy z bazą danych
			try {
				$this -> dbHandle = new ConnectDB();

				# Inicjalizuje mechanizm obsługi sesji
				session_set_save_handler(
					array(&$this, '_session_open_method'),
					array(&$this, '_session_close_method'),
					array(&$this, '_session_read_method'),
					array(&$this, '_session_write_method'),
					array(&$this, '_session_destroy_method'),
					array(&$this, '_session_gc_method')
				);

				# Sprawdza przesłane cookie o ile je przesłano; jeżeli wygląda podejrzanie zostaja z miejsca anulowane
				if ($this -> botSearch($_SERVER['HTTP_USER_AGENT']))
					$this -> strUserAgent = "BOT";
				else
				{
					$this -> strUserAgent = $_SERVER['HTTP_USER_AGENT'];
				
					if ($_COOKIE['PHPSESSID'])
					{
						# Kontrola bezpieczeństwa i ważności
						$this -> phpSessionID = $_COOKIE['PHPSESSID'];
						$sql	= "SELECT id FROM sesja_uzytkownika WHERE identyfikator_sesji_ascii = '".$this -> phpSessionID."' AND ((NOW() - utworzono) <= '".$this -> sessionLifespan."seconds') AND ((NOW() - ostatnia_reakcja) <= '".$this -> sessionTimeout."seconds' OR ostatnia_reakcja IS NULL);";

						$wynik	= $this -> dbHandle -> query($sql);
						$row	= $wynik -> fetch();
						$wynik -> closeCursor();
						unset($wynik);

						if ($row === false)
						{
							# Usuwa z bazy danych - w tym samym czasie usuwane są przeterminowane sesje
							$sql = "DELETE FROM sesja_uzytkownika WHERE (identyfikator_sesji_ascii = '".$this -> phpSessionID."') OR ((NOW() - utworzono) > '".$this -> sessionLifespan."seconds');";
							$wynik	= $this -> dbHandle -> query($sql);
							$wynik -> closeCursor();
							unset($wynik);

							# Usuwa nieprzydatne zmienne sesji
							$sql = "DELETE FROM zmienna_sesji WHERE identyfikator_sesji NOT IN (SELECT id FROM sesja_uzytkownika)";
							$wynik	= $this -> dbHandle -> query($sql);
							$wynik -> closeCursor();
							unset($wynik);

							# Pozbywa się identyfikatora, wymuszając na php nadanie nowego
							unset($_COOKIE['PHPSESSID']);
						} else
						{
							if (intval($row['id']) > 0)
							{
								$sql = "UPDATE sesja_uzytkownika SET ostatnia_reakcja = NOW() WHERE id = ".$row['id'];
								$wynik	= $this -> dbHandle -> query($sql);
								$wynik -> closeCursor();
								unset($wynik);
							}
						}
					}
				}
				# Ustawia czas życia cookie
				session_set_cookie_params($this -> sessionLifespan);
				# Wywołuje metodę session_start by zainicjować sesję
				session_start();
			} catch (PDOException $e)
			{
				Log::SLog($e->getMessage().' '.$e->getTraceAsString());			
			}
		}

		public function singleton()
		{
			static $instance;
			if(!isset($instance))
			{
				$instance = new Sesje();
			}
			return $instance;
		}

		// metoda podaje identyfikator sesji ASCII
		public function GetSessionIdentifier()
		{
			return $this -> phpSessionID;
		}

		public function __get($nm)
		{
			$variable = false;

			if ($this -> strUserAgent != "BOT")
			{
				try {
					$sql	= "SELECT wartosc_zmiennej FROM zmienna_sesji WHERE identyfikator_sesji = '".$this -> nativeSessionID."' AND nazwa_zmiennej = '".$nm."'";
					$wynik	= $this -> dbHandle -> query($sql);
					$row	= $wynik -> fetch();
					$wynik -> closeCursor();
					unset($wynik);

					if (isset($row['wartosc_zmiennej']))
					{
						$variable = unserialize($row["wartosc_zmiennej"]);
					}
				} catch (PDOException $e)
				{
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());		
				}
			}

			return $variable;
		}

		public function __set($nm, $val)
		{
			$success = false;

			if ($this -> strUserAgent != "BOT")
			{
				$strSer	= serialize($val);
				try {
					$sql = "SELECT id FROM zmienna_sesji WHERE identyfikator_sesji = '".$this -> nativeSessionID."' AND nazwa_zmiennej = '".$nm."'";
					$wynik	= $this -> dbHandle -> query($sql);
					$row	= $wynik -> fetchAll();
					$wynik -> closeCursor();
					unset($wynik);

					if (count($row) == 1 && $row !== false)
					{
						$sql = "UPDATE zmienna_sesji SET wartosc_zmiennej = '".$strSer."' WHERE id = '".$row[0]['id']."'";
						$this -> dbHandle -> exec($sql);
					} else
					{
						
						$sql = "INSERT INTO zmienna_sesji (identyfikator_sesji, nazwa_zmiennej, wartosc_zmiennej) VALUES(".$this -> nativeSessionID.", '".$nm."', '".$strSer."')";
						$this -> dbHandle -> exec($sql);
					}

					$success = true;
				} catch (PDOException $e)
				{
					
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				}
			}

			return $success;
		}

		public function isVar($nm)
		{
			$isVar = false;

			if ($this -> strUserAgent != "BOT")
			{
				try {
					$sql	= "SELECT wartosc_zmiennej FROM zmienna_sesji WHERE identyfikator_sesji = '".$this -> nativeSessionID."' AND nazwa_zmiennej = '".$nm."'";
					$wynik	= $this -> dbHandle -> query($sql);
					$row = $wynik -> fetchAll();
					$wynik -> closeCursor();
					unset($wynik);

					if ($row !== false && count($row) === 1)
					{
						$isVar = true;
					}
				} catch (PDOException $e)
				{
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				}
			}

			return $isVar;
		}

		public function delVar($nm)
		{
			$delVar = false;

			if ($this -> strUserAgent != "BOT")
			{
				try {
					$sql	= "DELETE FROM zmienna_sesji WHERE identyfikator_sesji = '".$this -> nativeSessionID."' AND nazwa_zmiennej = '".$nm."'";
					$wynik	= $this -> dbHandle -> exec($sql);

					if ($wynik === 1)
						$delVar = true;
					
					unset($wynik);
				} catch (PDOException $e)
				{
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				}
			}

			return $delVar;
		}

		public function getVars()
		{
			$variables = false;

			if ($this -> strUserAgent != "BOT")
			{
				try {
					$sql	= "SELECT nazwa_zmiennej, wartosc_zmiennej FROM zmienna_sesji WHERE identyfikator_sesji = '".$this -> nativeSessionID."'";
					$wynik	= $this -> dbHandle -> query($sql);
					$rows = $wynik -> fetchAll();

					foreach ($rows AS $row)
						$variables[$row['nazwa_zmiennej']] = unserialize($row["wartosc_zmiennej"]);

					$wynik -> closeCursor();
					unset($wynik);
				} catch (PDOException $e)
				{
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				}
			}

			return $variables;
		}

		public function destroySession()
		{
			session_destroy();
			unset($_COOKIE['PHPSESSID']);
		}

		# okresla czy wchodzacy jest botem - w $this -> botList jest zapisana lista slow po ktorych wykrywane sa boty
		private function botSearch($agent = false)
		{
			$searchResult = false;

			foreach ($this -> botList AS $pattern)
			{
				if (preg_match('/'.$pattern.'/i', $agent))
					$searchResult = true;
			}

			return $searchResult;
		}

		public function showUserAgent()
		{
			return $this -> strUserAgent;
		}

		public function _session_open_method($save_path, $session_name)
		{
			# Do nothing
			return true;
		}

		public function _session_close_method()
		{
			//unset($this -> dbHandle);
			return true;
		}

		public function _session_read_method($id)
		{
			if ($this -> strUserAgent != "BOT")
			{
				try {		
					$this -> phpSessionID = $id;
					# Sprawdza czy sesja istnieje w bazie danych
					$sql	= "SELECT id FROM sesja_uzytkownika WHERE identyfikator_sesji_ascii = '$id'";
					$wynik	= $this -> dbHandle -> query($sql);
					$tablicaWynikow = $wynik -> fetchAll();
			//		$row	= $wynik -> fetch();
					$wynik -> closeCursor();
					unset($wynik);

				//	if ($row !== false)
					
					if(count($tablicaWynikow) > 0 )
					{
						foreach($tablicaWynikow as $row) {
							$this -> nativeSessionID = $row["id"];
						}
					} else
					{
						# Konieczne jest stworzenie wpisu w bazie danych
						$sql	= "INSERT INTO sesja_uzytkownika (identyfikator_sesji_ascii, utworzono, user_agent) VALUES ('$id', NOW(), '".$this -> strUserAgent."')";
						$wynik	= $this -> dbHandle -> exec($sql);

						# Teraz pobiera prawdziwy identyfikator
						$this -> nativeSessionID = $this -> dbHandle -> lastInsertId('sesja_uzytkownika_id_seq');
						
						unset($wynik);
					}
				} catch (PDOException $e)
				{
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				}
			}

			# Zwraca jedynie ciąg pusty
			return "";
		}

		public function _session_write_method($id, $sess_data)
		{
			return true;
		}

		public function _session_destroy_method($id)
		{
			if ($this -> strUserAgent != "BOT")
			{
				try {
					$sql	= "DELETE FROM sesja_uzytkownika WHERE identyfikator_sesji_ascii = '$id'";
					$wynik	= $this -> dbHandle -> exec($sql);

					$sql	= "DELETE FROM zmienna_sesji WHERE identyfikator_sesji NOT IN (SELECT id FROM sesja_uzytkownika)";
					$wynik	= $this -> dbHandle -> exec($sql);

					unset($wynik);
				} catch (PDOException $e)
				{
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				}
			}

			return true;
		}

		public function _session_gc_method($maxlifetime)
		{
			if ($this -> strUserAgent != "BOT")
			{
				try {
					$sql	= "DELETE FROM sesja_uzytkownika WHERE ((NOW() - utworzono) > '".$this -> sessionLifespan."seconds') OR ((NOW() - ostatnia_reakcja) > '".$this -> sessionTimeout."seconds');";
					$wynik	= $this -> dbHandle -> exec($sql);
					unset($wynik);
				} catch (PDOException $e)
				{
					Log::SLog($e->getMessage().' '.$e->getTraceAsString());
				}
			}

			return true;
		}
	}
?>