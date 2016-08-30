<?php

namespace Enp;


class Mail extends \Zend_Mail {
	/**
	 * Jak znajdziesz jeszcze jakies opcje ktora beda dla nas mialy lepsze ustawienie niz domyslne w klasie \zend_mail
	 * to mozesz tez to napisac w ten sposob jak ponizej $_charset
	 */

	/**
	 * Zmiana domyslnego sposobu kodowania maila na utf-8
	 */
	protected $_charset = 'utf-8';

	/**
	 * Ta zmienna powinna decydowac czy obrazki w tresci maja byc osadzone czy maja pozostac linkami 
	 * ktore beda dociagane na prosbe klienta pocztowego od odbiorcy
	 * 
	 * @var bool
	 */
	protected $_embeddedImages = false;

	/**
	 * Definiuje ile może być jawnych adresatów. 
	 * 
	 * $var int
	 */
	protected $_visibleRecipientCount = 1;

	/**
	 * @return bool
	 */
	public function getEmbeddedImagesFlag() {
		return $this->_embeddedImages;
	}

	/**
	 * @param bool $embeddedImageFlags
	 */
	public function setEmbeddedImagesFlag($embeddedImageFlags) {
		$this->_embeddedImages = $embeddedImageFlags;
	}
	
	/**
	 * Ustawia maksymalną ilość jawnych adresatów.
	 * 
	 * @param int $count
	 * @return \Enp\Mail 
	 */
	public function setVisibleRecipientCount($count) {
		$this->_visibleRecipientCount = (int)$count;
		return $this;
	}
	
	/**
	 * Pobiera maksymalną ilość jawnych adresatów.
	 * @return int 
	 */
	public function getVisibleRecipientCount() {
		return $this->_visibleRecipientCount;
	}

	/**
	 * @todo Opis
	 * -to bedzie metoda do szybkiego wyslania maila
	 * bedzie ona automatycznie konfigurowac obiekt zend_mail, wypelniac go podanymi danymi, a wyslanie bedzie wymagalo uruchomienia metody $this->send();
	 * - jak ktos bedzie chcial zrobic wiecej to zrobi to sobie po wywolaniu tej metody
	 * - jak ktos bedzie chcial zrobic cos nie standardowego to moze nie korzystac z tej metody
	 * 
	 * @todo Parametry
	 * $to  - jezeli string to prosty adres email
	 * 		- jezeli array[] to tablica adresow email
	 * 		- jezeli arrayAssoc to powinna miec dwa parametry 'emial' i 'name', trzeba rozbic i podac do metody $this->addTo($email, $name='')
	 * 		- jezeli array[arrayAssoc] - to kazdy arrayAssoc powinien byc dodany i sprawdzony tak jak powyzszy podpunkt
	 * 
	 * $from	- jezeli string to prosty adres email
	 * 			- jezeli arrayAssoc to powinna miec dwa parametry 'emial' i 'name', trzeba rozbic i podac do metody $this->setFrom($email, $name = null)
	 * 
	 * $subject - po prostu temat dla maila
	 * 
	 * $body	- jezeli string to tresc HTML plus tresc wyczyszczona z tagow html wstawic jako tresc TXT (metodty : setBodyHtml i setBodytext )
	 * 			- jezeli arrayAssoc to powinna miec dwa parametry 'html' i 'txt', wtedy wartosc z 'html' wstawiamy jako tresc HTML, a wartosc z 'txt' jako tresc TXT
	 * 
	 * $attachmentPath	- jezeli string to nalezy przerobic plik z podanej lokalizacji na Zend_Mime_Part i dodoac go do maila
	 * 					metoda $this->addAttachment(Zend_Mime_Part $attachment) lub moze lepiej metoda 
	 * 							$this->createAttachment($body,
	 *                               $mimeType    = Zend_Mime::TYPE_OCTETSTREAM,
	 *                               $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
	 *                               $encoding    = Zend_Mime::ENCODING_BASE64,
	 *                               $filename    = null)
	 * 					- jezeli array[] to kazda wartosc to string, ktory nalezy obsluzyc tak jak ten z podpunktu wyzej
	 *
	 * $visibleRecipientCount - maxymalna ilość jawnych odbiorców ustawiona na 1 zabezpiecza przed ujawnieniem każdemu z adresatów adresów email innych odbiorców
	 * 
	 * @param array|string $to
	 * @param array|string $from
	 * @param string $subject
	 * @param string|array $body
	 * @param  string|array $attachmentPath
	 * @param int $visibleRecipientCount
	 * 
	 * @return \Enp\Mail 
	 */
	public function configure($to, $from, $subject, $body, $attachmentPath = null, $visibleRecipientCount = 1) {
		$this->setVisibleRecipientCount($visibleRecipientCount);
		$this->_prepareTo($to);
		$this->_prepareFrom($from);
		$this->setSubject($subject);
		$this->_prepareBody($body);
		if($attachmentPath) {
			$this->_prepareAttachment($attachmentPath);
		}
		if($this->_embeddedImages) {
			$this->_prepareEmbededImages();
		}
		
		return $this;
	}
	
	/**
	 * Przygotowuje i dodaje adresatów wiadomości
	 * 
	 * @param string|array $to
	 * @throws \Enp\Exception\MailError 
	 */
	protected function _prepareTo($to) {
		if(!$to) {
			throw new \Enp\Exception('Brak adresata wiadomości!');
		}
		if(!is_array($to)) {
			$this->addTo($to);
			return;
		}
		if(isset($to['mail']) && isset($to['name'])) {
			$this->addTo($to['mail'], $to['name']);
			return;
		}
		foreach($to as $t) {
			if(!is_array($t)) {
				$this->addTo($t);
			} elseif(isset($t['mail']) && isset($t['name'])) {
				$this->addTo($t['mail'], $t['name']);
			}
		}
	}
	
	/**
	 * Ustawia nadawcę
	 * 
	 * @param string|array $from
	 * @return true
	 * @throws \Enp\Exception\MailError 
	 */
	protected function _prepareFrom($from) {
		if(!$from) {
			throw new \Enp\Exception('Brak nadawcy!');
		}
		if(!is_array($from)) {
			$this->setFrom($from);
			return true;
		}
		if(isset($from['mail']) && isset($from['name'])) {
			$this->setFrom($from['mail'], $from['name']);
			return true;
		}
	}
	
	/**
	 * Ustawia treść wiadomości.
	 * 
	 * @param string|array $body
	 * @return boolean 
	 */
	protected function _prepareBody($body) {
		if(!is_array($body)) {
			$this->setBodyHtml($body)->setBodyText(strip_tags($body));
			return true;
		}
		if(isset($body['html'])){
			$this->setBodyHtml($body['html']);
		}
		if(isset($body['txt'])){
			$this->setBodyText($body['txt']);
		}
		return true;
	}
	
	/**
	 * Przygotowóje załaczniki
	 * @param string|array $attachmentPath
	 * @return type 
	 */
	protected function _prepareAttachment($attachmentPath) {
		if(!is_array($attachmentPath)) {
			$this->_addAttachment($attachmentPath);
			return;
		}
		foreach($attachmentPath as $path) {
			if(!empty($path)) {
				$this->_addAttachment($path);
			}
		}
	}
	
	/**
	 * Dodaje pojedynczy załącznik.
	 * 
	 * @param string $attachmentPath
	 * @throws Exception 
	 */
	private function _addAttachment($attachmentPath) 
	{
		if (!file_exists($attachmentPath)) {
			throw new \Enp\Exception("Plik $attachmentPath nie istnieje!"); //XXX: czy taki typ wyjątku?
		}		
		$fileName		= basename($attachmentPath);
		$file			= file_get_contents($attachmentPath);
		$att			= $this->createAttachment($file);
		$att->filename	= $fileName;
	}
	
	protected function _prepareEmbededImages() {
		$match = array();
		preg_match_all('/<img[^>]+src[\\s=\'"]+([^"\'>\\s]+)/is', $this->_bodyHtml, $match);
		if (empty($match[1])) {
			return $this->setBodyHtml($this->_bodyHtml);
		}

		foreach ($match[1] as $img) {
			$fileContent = file_get_contents($img);
			$fileName = basename($img);
			$extension = pathinfo($img, PATHINFO_EXTENSION);
			$att = $this->createAttachment($fileContent, 'image/' . strtolower($extension), Zend_Mime::DISPOSITION_INLINE, Zend_Mime::ENCODING_BASE64);
			$att->id = $fileName;
			$att->filename = $fileName;
			$this->_bodyHtml = str_replace($img, 'cid:' . $fileName, $this->_bodyHtml);
		}
		return $this->setBodyHtml($this->_bodyHtml);
	}

	/**
	 * Sprawdza czy ilość adresatów podanych jawnie.
	 * Jak jest większa niż zdefinowane przez $this->_toCount rzuca wyjątkiem
	 * 
	 * @todo: zastanowić się czy nie lepiej żeby zwracał $this !
	 * @return boolean
	 * @throws \Enp\Exception\MailError 
	 */
	public function sprawdzIloscJawnychAdresatow() {
		$pomHeaders = $this->getHeaders();
		$countTo = 0;
		$countCc = 0;
		unset($pomHeaders['To']['append']);
		unset($pomHeaders['Cc']['append']);
		if(isset($pomHeaders['To'])) {
			$countTo = count($pomHeaders['To']);
		}
		if(isset($pomHeaders['Cc'])) {
			$countCc = count($pomHeaders['Cc']);
		}
		if (($countTo + $countCc) > $this->_visibleRecipientCount) {
			throw new \Enp\Exception('Przekroczona ilość jawnych adresatów pojedynczej wiadomości email.');
		}
		return true;
	}

	/**
	 * Po dodaniu adresata sprawdza czy nie przekroczona maxymalna ilość jawnych adresatów.
	 * 
	 * @see Zend_Mail::addTo
	 * @return \Enp\Mail 
	 */
	public function addTo($email, $name = '') {
		parent::addTo($email, $name);
		$this->sprawdzIloscJawnychAdresatow();
		return $this;
	}

	/**
	 * Po dodaniu adresata sprawdza czy nie przekroczona maxymalna ilość jawnych adresatów.
	 * 
	 * @see Zend_Mail::addCc
	 * @return \Enp\Mail 
	 */
	public function addCc($email, $name = '') {
		parent::addCc($email, $name);
		$this->sprawdzIloscJawnychAdresatow();
		return $this;
	}

}

?>
