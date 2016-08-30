<?php

namespace Enp\Exception;

class HttpException extends \Enp\Exception
{
	private $messages = array(
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found'
	);

	/**
	 * Popularne:
	 *
	 * 400 Bad Request — błędne zapytanie HTTP,
     * 401 Unauthorized — strona wymaga autoryzacji,
     * 403 Forbidden — dostęp zabroniony,
	 * 404 Not Found — plik nie został odnaleziony.
	 * etc.
	 *
	 * @param int $httpCode
	 */
	public function __construct($httpCode = 404)
	{
		$msg = 'Http Error';
		if (array_key_exists($httpCode, $this->messages)) {
			$msg = $this->messages[$httpCode];
		}
		parent::__construct($msg, $httpCode);
	}
}