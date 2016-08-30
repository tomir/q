<?php
/**
 * Description of Komentarz
 *
 * @author ArturLasota
 */
class AllegroApi_Komentarz {
	private $allegroApi = null;	
	public $daneDoWystawienia = array();
	
	
	public function __construct(AllegroApi $allegroApi) {
		$this->setAllegroApi($allegroApi);
	}
	
	private function setAllegroApi($allegroApi) {
		if(!$allegroApi instanceof AllegroApi) {
			throw new InvalidArgumentException('Podaj allegro api');
		}
		
		$this->allegroApi = $allegroApi;
	}
	
	/**
	 * Pobiera komentarze dla zalogowanego konta od podanego offsetu
	 * @param int $od
	 * @return array 
	 */
	public function getMojeKomentarzeOstatnioOtrzymane($limit = 25) {
			return $this->getMojeKomentarzeOtrzymaneWszystkie($limit);
	}
	
	public function getMojeKomentarzeOstatnioWystawione($limit = 25) {
			return $this->getMojeKomentarzeWystawioneWszystkie($limit);
	}
	
	public function getMojeKomentarzeOtrzymaneWszystkie($limit = 0) {
		try {
			return $this->allegroApi->client->doMyFeedback2Limit(
					$this->allegroApi->session['session-handle-part'],
					'fb_recvd',
					$od,
					0,
					array(),
					$limit
					);
		} catch(Exception $e) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
	}
	
	public function getMojeKomentarzeWystawioneWszystkie($limit = 0) {
		try {
			return $this->allegroApi->client->doMyFeedback2Limit(
					$this->allegroApi->session['session-handle-part'],
					'fb_gave',
					$od,
					0,
					array(),
					$limit
					);
		} catch(Exception $e) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
	}
	
	
	/**
	 * Wystawia komentarze
	 * @return type 
	 */
	public function addKomentarze() {
		try {
			return $this->allegroApi->client->doFeedbackMany(
					$this->allegroApi->session['session-handle-part'],
					$this->getDataToAdd()
					);
		} catch(Exception $soapFault) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
	}
	
	
	
	
	public function addDane(AllegroApi_Komentarz_Dane $dane) {
		if(!$dane instanceof AllegroApi_Komentarz_Dane) {
			throw new InvalidArgumentException('Podaj dane za pomocą klasy AllegroApi_Komentarz_Dane');
		}
		
		$this->daneDoWystawienia[] = array(
			'fe-item-id' => $dane->idAukcji,
			'fe-to-user-id' => $dane->idOdbiorcy,
			'fe-comment' => $dane->komentarz,
			'fe-comment-type' => $dane->ocena,
			'fe-use-comment-template' => 0,
			'fe-op' => $dane->typOdbiorcy,
			'fe-rating' => array()
		); 
	}
	
	private function getDataToAdd() {
		if(count($this->daneDoWystawienia) <= 0) {
			throw new InvalidArgumentException('Podaj dane do wystawienia');
		}
		return $this->daneDoWystawienia;
	}
	
}

?>
