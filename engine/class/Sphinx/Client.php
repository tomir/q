<?php

namespace Sphinx;

require_once(CLASS_DIR. 'Sphinx/sphinxapi.php');

class Client extends \SphinxClient {

	protected $clearQuery = true;
	protected $enableInfixFields = true;

    /**
     * connect to searchd server, run given search query through given indexes,
     * and return the search results
     * @param type $query
     * @param type $index
     * @param type $comment
     * @return array
     * @throws \Enp\Sphinx\Exception
     */
    public function Query($query, $index = "*", $comment = "") {

		if ($this->clearQuery) {
			$query = $this->clearFromUnsafeChars($query);
		}
		
		//\Zend_Debug::dump('client : '.$query);

		$result = parent::Query($query, $index, $comment);

        if ($result === false) {
            throw new Exception($this->GetLastError());
        } else {
            if ($this->GetLastWarning() != '') {
                throw new Exception($this->GetLastWarning());
            }
        }

        return $result;
    }

    public function clearFromUnsafeChars($string) {
        $string = trim(mb_strtolower($string, 'utf8'));
        $new_string = '';
        $len = mb_strlen($string, 'utf-8');
		
		$string = str_replace('.', ",", $string);
		
        for ($i = 0; $i < $len; ++$i) {

            $sign = mb_substr($string, $i, 1, 'utf-8');

            switch ($sign) {
                case '(':
				case ')':
					/**
					 * nie usuwa dwoch powyzszych znakow ( ) jezeli w zapytaniu
					 * jest logiczny operator OR zapisany jako |
					 */
					if (strpos($string,'|') !== false ) {
						$new_string.=$sign;
						break;
					}
                case '-':
                case '!':
                case '?':
                case '.':
                case '>':
                case '<':
                case '=':
                case '@':
                case '\'':
                case '"':
                case '~':
                    $new_string .=' ';
                    break;
                default:
                    $new_string.=$sign;
                    break;
            }
        }

		/**
		 * jeszcze zbedne dublowane spacje
		 */
		$new_string = preg_replace('/( ){2,}/',' ',$new_string);

		return $new_string;
    }

	/**
	 * Czy frazy wyszukiwane maja byc czyszczone przed wyslaniem do uslugi Sphinx
	 *
	 * @param $clearQuery bool
	 */
	public function setClearQuery($clearQuery)
	{
		$this->clearQuery = $clearQuery;
	}

	public function getClearQuery()
	{
		return $this->clearQuery;
	}

}

?>
