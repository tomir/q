<?php

class Allegro_Ajax {
	
	public function ajaxTestApiKey($apiKey) {
		
		try {
			$obAllegroApi = new AllegroApi();
			$obAllegroApi->login(0, 0, $apiKey);
		
			$result = $obAllegroApi->doGetUserLicenceDate();
			return date("Y-m-d", $result);
		
		} catch (SoapFault $soapFault ){
			return array("error_code" => $soapFault->faultcode, "error_string" => $soapFault->faultstring);
		}
	}
	
	/**
	 * Zapisuje podane w tablicy dane (w razie potrzeby jesli id==0 tworzony jest nowy wpis)
	 *
	 * @param array $formData
	 * @return xajaxResponse
	 */
	public function ajaxAdminEditItem($formData) {
		$objResponse = new xajaxResponse();

		if (!is_array($formData) || count($formData) == 0)
			return $objResponse;

		$data = $formData['formData'];
		$sprzedaz = new Allegro_Sprzedaz();
		if( (int)$data['id_konto'] > 0) {
			$sprzedaz->idKonto = $data['id_konto'];
		} else {
			$sprzedaz->setIdKontoByIdSerwis($data['id_serwisu']);
		}
		
		if(mb_strlen($data["fid"][1], "utf8") > 50) {
			
			$objResponse->alert('Wystąpił błąd: Zbyt długa nazwa aukcji "'.$data["fid"][1].'" ('.mb_strlen($data["fid"][1], "utf8").')');
			$objResponse->script("document.getElementById('btnWystaw').innerHTML = 'Wystaw';
								 document.getElementById('btnWystaw').disabled = false;");
			return $objResponse;
		}
		
		
		if (trim($data['data_cron']) != '' && trim($data['data_cron_time']) != '') {
			$aukcja = new Allegro();
			$aukcja->data = $data;			

			foreach($sprzedaz->getSzablonDostawyFids($data['id_szablon_dostawy']) as $fid=>$val) {
				$aukcja->data['fid'][$fid] = $val;
			}

			$aukcja->data['data_cron'] = $data['data_cron'] . ' ' . $data['data_cron_time'] . ':00';
			
			try {
				$sprzedaz->zapiszAukcje($aukcja, $aukcja->data['data_cron']);
				$objResponse->assign("editContent", "innerHTML", self::ajaxGetEditResultHTML(0));
			} catch (Exception $e) {
				echo $e;
				$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
				$objResponse->script("document.getElementById('btnWystaw').innerHTML = 'Wystaw';
								  document.getElementById('btnWystaw').disabled = false;");
			} 
		} else {

			try {
				$sprzedaz->setAllegroApi($data['id_serwisu'], $data['id_konto']);	
			} catch (Exception $e) {
				$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
				return $objResponse;
			}

			try {
				$apiResult = $sprzedaz->sprzedaz($data);
				$objResponse->assign("editContent", "innerHTML", self::ajaxGetEditResultHTML((int) $apiResult["item-id"]));
			} catch (Exception $e) {
				$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
				$objResponse->script("document.getElementById('btnWystaw').innerHTML = 'Wystaw';
								  document.getElementById('btnWystaw').disabled = false;");
			} 				

		}	
		
		return $objResponse;
	}

	/**
	 * Zapisuje podane w tablicy dane (w razie potrzeby jesli id==0 tworzony jest nowy wpis)
	 *
	 * @param array $formData
	 * @return xajaxResponse
	 */
	public function ajaxAdmin_getCost($fd) {
		$objResponse = new xajaxResponse();

		$data = $fd['formData'];
		$allegroKoszt = new Allegro_Koszt();

		$objResponse->assign("koszt_aukcji", "innerHTML", sprintf("%.2f",  $allegroKoszt->getKoszt($data['fid'])) . ' zł');
		$objResponse->assign("prowizja", "innerHTML", sprintf("%.2f", $allegroKoszt->getProwizja($data['fid'][8])) . ' zł');
//		$objResponse->assign("cena_min", "innerHTML", sprintf("%.2f", $oplata_cena_min) . ' zł');
//		$objResponse->assign("al_price_final", "value", sprintf("%.2f", $this->data['auction_cost'] + $fd['formData']['price_buynow']) . ' zł');
		$objResponse->script("$('#opcje :checkbox').removeAttr('disabled')");

		return $objResponse;
	}
	
	/**
	 * Pobiera koszt aukcji przy multi ponownym wystawianiu
	 * @param array $fd
	 * @return xajaxResponse 
	 */
	public function ajaxAdminGetCostMultiAddAgain($fd) {
	
		$objResponse = new xajaxResponse();
		$data = $fd['formData'];
		
		$calkowityKoszt = 0;
		$calkowitaProwizja = 0;
		
		$allegroKoszt = new Allegro_Koszt();
		
		try {
			// pobieramy aukcje
			foreach($data['id'] as $idAukcji) {
				$aukcja = new Allegro($idAukcji);
				// pobieramy aktualne dane produktu
				$produkt = new Allegro_Produkt($aukcja->data['id_produktu']);
				$aukcja = $produkt->updateProdukt($aukcja, $data['id_serwisu']);
				// dane z formularza
				$aukcja->data['fid'][4] = $data['fid'][4]; // czas trwania

				// sprawdzamy cene
				$koszt = $allegroKoszt->getKoszt($aukcja->data['fid']);
				$prowizja = $allegroKoszt->getProwizja($aukcja->data['fid'][8]);

				$calkowityKoszt+= $koszt;
				$calkowitaProwizja+= $prowizja;

				$objResponse->assign("koszt" . $idAukcji, "innerHTML", sprintf("%.2f", $koszt . ' zł'));
				$objResponse->assign("prowizja" . $idAukcji, "innerHTML", sprintf("%.2f", $prowizja . ' zł'));
			}

			$objResponse->assign("koszt", "innerHTML", sprintf("%.2f",  $calkowityKoszt) . ' zł');
			$objResponse->assign("prowizja", "innerHTML", sprintf("%.2f",  $calkowitaProwizja) . ' zł');

		} catch (Exception $e) {
			$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
		}
		return $objResponse;
	}
	
	/**
	 * Pobiera koszt aukcji przy multi wystawieniu nowych aukcji na bazie produktow
	 * @param array $fd
	 * @return xajaxResponse 
	 */
	public function ajaxAdminGetCostMultiByProdukt($fd) {
		
		$objResponse = new xajaxResponse();
		$data = $fd['formData'];
		
		$calkowityKoszt = 0;
		$calkowitaProwizja = 0;
		
		$allegroKoszt = new Allegro_Koszt();
		
		try {
			// pobieramy aukcje
			foreach($data['id'] as $idProdukt) {
			
				// tworzymy nowa aukcje na bazie produktu
				$aukcja = new Allegro();
				// pobieramy aktualne dane produktu do aktualizacji
				$produkt = new Allegro_Produkt($idProdukt);
				// pobiera wszystkie defaultowe dane aukcji - dla nowych aukcji
				$aukcja = $produkt->updateAuction($aukcja, $data['id_serwisu']);
				
				// dane z formularza
				$aukcja->data['fid'][4] = $data['fid'][4]; // czas trwania
				
				// sprawdzamy cene
				$koszt = $allegroKoszt->getKoszt($aukcja->data['fid']);
				$prowizja = $allegroKoszt->getProwizja($aukcja->data['fid'][8]);

				$calkowityKoszt+= $koszt;
				$calkowitaProwizja+= $prowizja;
				
				$objResponse->assign("koszt" . $idProdukt, "innerHTML", sprintf("%.2f", $koszt . ' zł'));
				$objResponse->assign("prowizja" . $idProdukt, "innerHTML", sprintf("%.2f", $prowizja . ' zł'));
			}

			$objResponse->assign("koszt", "innerHTML", sprintf("%.2f",  $calkowityKoszt) . ' zł');
			$objResponse->assign("prowizja", "innerHTML", sprintf("%.2f",  $calkowitaProwizja) . ' zł');

		
		} catch (Exception $e) {
			$objResponse->alert('Wystąpił błąd: ' . $e->getMessage());
		}
		
		return $objResponse;
	}

	public function ajaxTab0() {

		$htmlRes = SmartyObj::getInstance();
		$htmlRes->assign('obj', $this);
	
		$htmlRes->assign('serwisy', Website::getListSelect());
		$oFCKeditor = new FCKeditor('FCKeditor');
		$oFCKeditor->BasePath = FCK_DIR;
		$oFCKeditor->Width = '900';
		$oFCKeditor->Height = '500';
		$oFCKeditor->Value = $this->data['fid']['24'];
		$output = $oFCKeditor->CreateHtml();
		$htmlRes->assign('FCKeditor', $output);
		$htmlRes->assign('ajaxCatBox', self::ajaxCatBoxHTML());
		$htmlRes->assign('kurier_przelew', 0.0 /* $this->data['objProduct']->getKosztDostawy(P_PRZELEW, T_KURIER) */);
		$htmlRes->assign('kurier_pobranie', 0.0 /* $this->data['objProduct']->getKosztDostawy(P_POBRANIE, T_KURIER) */);

		$htmlRes->assign('szablon_dostawy', Allegro_SzablonDostawy::getListSelect());
		$htmlRes->assign('szablon_graficzny', Allegro_SzablonGraficzny::getListSelect(array('id_serwisu' => $this->data['id_serwisu'])));

		return $htmlRes->fetch('_ajax/allegro.edit.tab0.tpl');
	}

	public function ajaxGetEditResultHTML($item_id) {
		$htmlRes = SmartyObj::getInstance();
		$htmlRes->assign('item_id', $item_id);
		$htmlRes->assign('auction_country_id', $this->auction_country_id);

		return $htmlRes->fetch('_ajax/allegro.edit.sold.tpl');
	}

	function ajaxAdmin_setCat($parent = 0) {
		$objResponse = new xajaxResponse();

		if (self::checkChildrens($parent)) {
			$objResponse->assign('catBox', "innerHTML", self::ajaxCatBoxHTML($parent));

			$objResponse->assign('fields', "innerHTML", '');
		} else {
			$cat = self::getCat($parent);
			$objResponse->assign('catMessage', "innerHTML", 'Kategoria allegro: ' . $cat['nazwa'] . ' [' . $cat['id'] . ']');
			$objResponse->script("$('#formData_category').val($parent)");

			$objResponse->assign('catBox', "innerHTML", self::ajaxCatBoxHTML($cat['parent_id'], $cat['id']));
			
			$fields = self::getCatFields($parent);
			$htmlRes = SmartyObj::getInstance();
			$htmlRes->assign('allegro_fields', $fields);

			$objResponse->assign('fields', "innerHTML", $htmlRes->fetch('allegro/catfields.tpl'));
		}

		return $objResponse;
	}
	
	/**
	 * Zakańcza aukcje w ajaxie
	 * @param type $formData
	 * @return xajaxResponse 
	 */
	public function ajaxSetFinishMulti($formData) {
		$objResponse = new xajaxResponse();
		$htmlRes = SmartyObj::getInstance();

		$filtr = array(
			'ids' => $formData['id']
		);

		// api
		$apiResult = $this->zakonczAukcjeMulti($formData['id']);

		// result
		if (isset($apiResult['error'])) {
			$objResponse->alert('Wystąpił błąd: ' . $apiResult['error']);
			$objResponse->script("document.getElementById('btnWystaw').innerHTML = 'Wystaw';
								  document.getElementById('btnWystaw').disabled = false;");
		} else {
			if (count($filtr['ids']) > 0 && !isset($apiResult['error'])) {

				$lista = $this->getList($filtr, $sort, $limit);
				$lista = $this->idToKey($lista);
				$apiResult = $this->replaceDashInKey($apiResult);

				$htmlRes->assign('apiResult', $apiResult);
				$htmlRes->assign('lista', $lista);
			}

			$objResponse->assign('editTabs', "innerHTML", $htmlRes->fetch('_ajax/allegro.finish_multi.result.tpl'));
		}

		return $objResponse;
	}
	
}

?>
