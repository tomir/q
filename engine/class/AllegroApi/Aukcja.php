<?php
/**
 * Description of 'Aukcja
 *
 * @author ArturLasota
 */
class AllegroApi_Aukcja {
	private $allegroApi = null;	
	private $id = null;
	
	public function __construct(AllegroApi $allegroApi, $id = null) {
		if(!$allegroApi instanceof AllegroApi) {
			throw new InvalidArgumentException('Podaj allegro api');
		}
		$this->allegroApi = $allegroApi;
		
		$id = (int)$id;
		if($id > 0) {
			$this->id = $id;
		}
	}
	
	public function setId($id) {
		$id = (int)$id;
		if($id <= 0) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		$this->id = $id;
	}
	
	private function getId() {
		if($this->id == null) {
			throw new InvalidArgumentException('Podaj id aukcji');
		}
		
		return $this->id;
	}
	
	public function updateIloscProduktow($ilosc) {
		try {
			return $this->allegroApi->client->doChangeQuantityItem(
					$this->allegroApi->session['session-handle-part'],
					$this->getId(),
					$ilosc
					);
		} catch(Exception $e) {
			Common::log(__METHOD__, $soapFault->faultstring . ' code ' . $soapFault->faultcode);
			throw new AllegroApi_Exception($soapFault->faultstring, $soapFault->faultcode);
		}
	}
	
	public function zakoncz() {
		$idsAukcji = array($this->getId());
		$this->allegroApi->finishItems($idsAukcji);
	}
	
	
	
	
}

?>
