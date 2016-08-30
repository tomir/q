<?php

//$profiler = new Common_Profiler();
// -------------------- XAJAX --------------------------

$za = new ZamowienieAjax();
$pa = new PrzechowalniaAjax();
$produktAjax = new ProduktAjax();
$pra = new PorownanieAjax();

//$xajax->registerFunction(array("zapiszOpinie", $opiniaAjax, "ajaxZapiszOpinie"));

$xajax->registerFunction(array("doKoszyka", $za, "doKoszyka"));
$xajax->registerFunction(array("doKoszykaZestaw", $za, "doKoszykaZestaw"));
$xajax->registerFunction(array("zKoszyka", $za, "zKoszyka"));

$xajax->registerFunction(array("doPrzechowalni", $pa, "doPrzechowalni"));
$xajax->registerFunction(array("zPrzechowalni", $pa, "zPrzechowalni"));

$xajax->registerFunction(array("doPorownania", $pra, "doPorownania"));
$xajax->registerFunction(array("zPorownania", $pra, "zPorownania"));
$xajax->registerFunction(array("doPorownaniaNew", $pra, "doPorownaniaNew"));

$xajax->registerFunction(array("showClientProductPicInfo", $produktAjax, "showClientProductPicInfo"));
$xajax->registerFunction(array("addClientProductPicVote", $produktAjax, "addClientProductPicVote"));

$xajax->registerFunction(array("zapiszMonitoring", $produktAjax, "zapiszMonitoring"));

$xajax->registerFunction(array("obliczRate", $produktAjax, "obliczRate"));
$xajax->registerFunction(array("wstawRate", $produktAjax, "wstawRate"));

$newsletter_ajax = new Newsletter();
$xajax->registerFunction(array("newsletterPopup", $newsletter_ajax, "newsletterPopup"));

/*
 * NOWE OPINIE - XAJAX
 */
$xajax->registerFunction('dodajParametr');
$xajax->registerFunction('glosujOpinie');
$xajax->registerFunction('dodajOpinie');
$xajax->registerFunction('dodajKomentarz');
/*
 * NOWE OPINIE - XAJAX KONIEC
 */


$smarty = SmartyObj::getInstance();
$smarty->assign('xajax', $xajax->getJavascript(XAJAX_JS));

$xajax->processRequest();
// -----------------------------------------------------
// ------------- SPRAWDZENIE i USTAWIENIE -------------
$p = new Produkt((int) $_GET['id']);

// produkt poprzedni i nastepny na samej gorze
$poprzedniNastepnyProdukt = \Produkt\PoprzedniNastepny::getInstance();
$poprzedniNastepnyProdukt->setProdukt($p);
$poprzedniNastepnyProdukt->setIdSerwis(ID_SERWISU);
$poprzedniNastepnyProdukt->prepare();
$smarty->assign('produktPoprzedni', $poprzedniNastepnyProdukt->getPoprzedni());
$smarty->assign('produktNastepny', $poprzedniNastepnyProdukt->getNastepny());

// dopiski 
$dopiskiAppendInfo = new \Promocja\Dopisek\ProduktAppendInfo();
$p->data = $dopiskiAppendInfo->appendToOne($p->data);

// emblematy
$emblematyAppendInfo = new \Promocja\Emblemat\ProduktAppendInfo();
$p->data = $emblematyAppendInfo->appendToOne($p->data);


// redirect na poprawny link produktu
$linkProduktu = '/'.$p->data['link'];
if (getMainUrl() != $linkProduktu) {
	Common::redirect(getRedirectUrl($linkProduktu));
}

if ($p->data['blokada'] != 0)
    Common::redirect('index.html');

$p->data = array_merge($p->data, $p->getDataExt());

$_GET['kat_id'] = $p->data['id_kategoria'];


// pobieramy popup
$popupData = $c->getPopupData($_GET['kat_id']);
Popup::insertHtml($popupData['popup_id'], $popupData['popup_rodzaj']);


// ------------- KOSZT DOSTAWY -------------
$p->data['wartosc'] = $p->data['cena'];

# Pobranie kosztu transportu
$transport_tab = Transport::pobierzKosztProdukt($p->id, Transport_Typ::ID_KURIER);
$smarty->assign('transport_tab', $transport_tab);

// ------------- RATY LUKAS -------------
$p->data['raty_lukas'] = 0;
if ($p->data['lukas'] == 0) {
    if (Kategoria::getLukas($p->data['id_kategoria']) == 0)
        $p->data['raty_lukas'] = 1;
    else
        $p->data['raty_lukas'] = 0;
}

// ------------- RATY SYGMA -------------
$p->data['raty_sygma'] = 0;
if ($p->data['sygma'] == 0) {
    if (Kategoria::getSygma($p->data['id_kategoria']) == 0)
        $p->data['raty_sygma'] = 1;
    else
        $p->data['raty_sygma'] = 0;
}


// ------------- PROMOCJE -------------
$p = Promocja::przeliczProdukt($p);


// ------------- CENEO -------------
//$p = Ceneo::przeliczProdukt( $p );
// ------------- ODBIORY OSOBISTE ----------------------
//$odbiory = Odbior::getList( array('blokada'=>0), array('sort'=>'miasto', 'order'=>'asc') );
//$smarty->assign('odbiory', $odbiory);
// ----------------------------------------------------
$tmp = Produkt::pobierzIkony(array(0 => $p->data)); //d($tmp);
$p->data = $tmp[0];

// Nazwy uzupelniajace
$nazwyUzupelniajace = $p->appendNazwyUzupelniajaceToLista(array($p->data), ID_SERWISU);
$p->data['nazwy_uzupelniajace'] = $nazwyUzupelniajace[0]['nazwy_uzupelniajace'];
$p->data['nazwy_uzupelniajace_recznie_bool'] = $nazwyUzupelniajace[0]['nazwy_uzupelniajace_recznie_bool'];

$smarty = SmartyObj::getInstance();

$pr = new Producent($p->data['id_producent']);
$smarty->assign('pr', $pr);

$dost = new Dostepnosc($p->data['id_dostepnosc']);
$smarty->assign('dost', $dost);

// ------------- INFORMACJE ROZSZERZONE -------------
$ext = $p->getDataExt();
$smarty->assign('ext', $ext);

// ------------- DODATKOWE ZDJECIA -------------
$zdjecia_ext = Produkt::pobierzDodatkoweZdjecia($p->id); //d($zdjecia_ext);
$smarty->assign('zdjecia_ext', $zdjecia_ext);

// ------------- DODATKOWE FILMY -------------
$film = new Produkt_Film();
$smarty->assign('filmy_ext', $film->getLista($p->id));

// ------------- DODATKOWE MEDIA STRONY -------------
$mediaStrona = new Produkt_MediaStrona();
$smarty->assign('strony_ext', $mediaStrona->getLista($p->id));

// ------------- PARAMETRY PRODUKTU -------------
$smarty->assign('pKupic', $p->getParametryTechniczne(ID_SERWISU));
//\Zend_Debug::dump($p->data);
// ------------- AKCESORIA -------------
$cache = \Enp\Cache::get();
$akcesoriaCacheId = 'akcesoria_' . md5(serialize($p->data));

if (false === ($lista_akcesoria = $cache->load($akcesoriaCacheId))) {
    $filtrAkcesoria = array();
    $lista_akcesoria = Akcesoria::getAkcesoriaForProdukt($p->data['id'], $filtrAkcesoria);
	
	
   $cache->save($lista_akcesoria, $akcesoriaCacheId, array(), CACHE_TIME_NORMAL);//
}
$smarty->assign('akcesoria', $lista_akcesoria);
//\Zend_Debug::dump($lista_akcesoria);

// ------------ USŁUGI --------------

$uslugiRepo  = new \Transport\Usluga();
$uslugi = $uslugiRepo->getAllByProduct($p, array(
	'id_serwis' => ID_SERWISU, 
	'aktywny'	=> 1, 
	'cena_min' => true
));
$smarty->assign('uslugi', $uslugi);


// ------------- ZESTAWY -------------
$zestawy = Zestawy::getProducts((int) $p->id, (int) $p->data['id_kategoria'], (float) $p->data['cena'], $p->data['id_producent']);
$smarty->assign('zestawy', $zestawy);

$pz = new ProduktZdjecia();
$zdjecia = $pz->getPicsList((int) $_GET['id']);
$smarty->assign('zdjecia', $zdjecia);
$zdjecia_ilosc = count($zdjecia);
$smarty->assign('zdjecia_ilosc', $zdjecia_ilosc);

// ------------- LISTA ZDJEC PRODUKTU DODANA PRZEZ KLIENTA ---------------
if ($_GET['tab'] == 'zdjecia') {
    $zdjecie_info = $pz->getPic((int) $_GET['id']);
    $smarty->assign('pic_info', $zdjecie_info);
}

$ratyCetelem = RatyKalkulator::getCetelem($p->data['cena']); //Common::debug($ratyCetelem);
$smarty->assign('ratyCetelem', $ratyCetelem);

/*
 * NOWE OPINIE - brać a sie nie bać
 */
$objOpinia = new \Opinie\Lista();

$ocena_dla_produktu = $objOpinia->pobierzOceneDlaProduktu((int) $_GET['id']);
$polecenie_dla_produktu = $objOpinia->pobierzPolecenieDlaProduktu((int) $_GET['id']);
$smarty->assign('ocena_dla_produktu', round($ocena_dla_produktu, 0));
$smarty->assign('polecenie_dla_produktu', $polecenie_dla_produktu);

$opinie_ilosc = $objOpinia->getAllIlosc(array('id_produkt' => $_GET['id'], 'id_status' => 5, 'id_serwisu' => ID_SERWISU));
$smarty->assign('opinie_ilosc', $opinie_ilosc);
$opinie_lista = $objOpinia->getAll(array('id_produkt' => $_GET['id'], 'id_status' => 5, 'id_serwisu' => ID_SERWISU));
$smarty->assign('opinie_lista', $opinie_lista); 

$opinia_parametry = new \Opinie\Parametry();
$opinie_parametry = $opinia_parametry->getGroupByDzial(array('id_kategoria' => $p->data['id_kategoria'], 'blokada_tmp' => 0)); //Zend_Debug::dump($opinie_parametry);
$smarty->assign('opinie_parametry', $opinie_parametry);

//sprawdzamy czy uzytkownik nie przeszedł z maila z informacją o pytaniu do jego opinii
//jeśli tak to oznacza to, że mamy mu wyświetlić formularz dodawania odpowiedzi
if(isset($_GET['hash']) && $_GET['hash'] != '') {
	
	$odp_id_opinia = $objOpinia->sprawdzHash($_GET['hash']);
	if(is_numeric($odp_id_opinia) && $odp_id_opinia > 0) {
		$smarty->assign('odp_id_opinia', $odp_id_opinia);
		$smarty->assign('odp_id_pytanie', $_GET['id_pytanie']);
	}
}


/*
 * OPINIE KONIEC
 */


// ------------- INNE PRODUKTY FIRMY ------------------
$inneProduktyKat = ProduktInne::kategorie($p->data['id_producent']);
$smarty->assign('inneProduktyKat', $inneProduktyKat);

// ------------- INNE ----------------------------------
$smarty->assign('topPrzechowalnia', PrzechowalniaAjax::htmlPrzechowalnia());
$smarty->assign('topPorownanie', PorownanieAjax::htmlPorownanie());

// ---------- filtry dla szukarki produktow podobnych
$cacheid = 'filtry_produkt_' . $p->id . '_' . $p->data['id_kategoria'];
$cache = \Enp\Cache::get();

if (false === ($filtryKategorii = $cache->load($cacheid))) {
    $filtry = new Bdk\Filtry();
    $filtryKategorii = $filtry->getFiltryKupicIDProduktow(array($p->id), $p->data['id_kategoria']);
    $filtryKategorii = array_slice($filtryKategorii, 0, 10); // 10 filtrow
    $cache->save($filtryKategorii, $cacheid, array(), CACHE_TIME_NORMAL);
}
$smarty->assign('filtryKategorii', $filtryKategorii);

// -----------------------------------------------------
$k = new Kategoria($p->data['id_kategoria']);
$smarty->assign('kateg', $k);
$breadcrumbs = $k->getPath((int) $p->data['id_kategoria']);
$breadcrumbsReversed = array_reverse($breadcrumbs);
$smarty->assign('current_url_cat', $breadcrumbsReversed[0]['url']);

$smarty->assign('parentCategory', $breadcrumbsReversed[1]['id']);
$smarty->assign('rootCategory', $breadcrumbs[0]['id']);
$breadcrumbs[] = array('nazwa' => $p->data['producent'] . ' ' . $p->data['nazwa'], 'link' => 'produkt-' . $p->id . '.html');

$seo_title = $p->data['nazwa'] . ' - ' . DOMENA;
$seo_keywords = (($p->data['nazwa_typ'] == '' && $p->data['model'] == '') ? $p->data['nazwa'] . ', ' : '') . ($p->data['nazwa_typ'] != '' ? $p->data['nazwa_typ'] . ' ' : '') . ($p->data['model'] != '' ? trim($p->data['model']) . ', ' : '') . $p->data['producent'] . ', ' . $p->data['kategoria'] . ', ' . ($p->data['model'] != '' ? trim($p->data['model']) . ', ' : '');

foreach ($pKupic as $p1) {
    foreach ($p1 as $p2) {
        foreach ($p2 as $p3) {
            $seo_keywords .= $p3['atrybut'] . ',' . $p3['wartosc'] . ',';
        }
    }
}


$punktOdbioruFiltr = array(
    'blokada' => 0
);
$punktOdbioruSort = array(
    'sort' => 'miasto',
    'order' => 'asc'
);

/* SEO */
$criteria = new Common\Orm\Criteria();
$criteria->orderBy($punktOdbioruSort);
$mapper = new PunktOdbioru\PunktOdbioru\Mapper();

$punktyMiasta = array();
foreach ($mapper->findAll($punktOdbioruFiltr, $criteria) as $punkt) {
    $punktyMiasta[] = $punkt->miasto;
}

//$seo_description = 'Sprawdź niską cenę '.$p->data['nazwa'].' w sklepie '.ucfirst(DOMENA).'! ' . ControlPanel::getValue('seo_text');
/* @AL dla zlecenie P.Konopka */
$seo_description = 'Sprawdź cenę ' . $p->data['nazwa'] . ' w ' . ucfirst(DOMENA) . '! Szybka dostawa, odbiór osobisty: ' . implode(', ', array_unique($punktyMiasta));
$seo = SeoObj::getInstance();
$seo->setTitle($seo_title);
$seo->setKeywords($seo_keywords);
$seo->setDescription($seo_description);
/* end seo */


// Popularne produkty
$listaPopularne = Produkt::getList(array('id_kategoria' => $p->data['id_kategoria'], 'blokada' => 0, 'posiada_nr_kat' => 1), array('string' => ' rand() ASC'), array('start' => 0, 'limit' => '5'));
$smarty->assign('listaPopularne', $listaPopularne);

// Drzewko kategorii
Kategoria::drzewo($p->data['id_kategoria'], $k);
$smarty->assign('drzewo_typ', 1);


$bestsellery_producenci90 = Bestseller::pobierz_marka2(90, (int) $p->data['id_kategoria']);
$smarty->assign('bestsellery_producenci90', $bestsellery_producenci90);


// ostatnio ogladane
$ostatnioProdukty = \Core\Produkt\Ostatnio::getInstance();
$ostatnioProdukty->add($p->data['id']);

$cacheSerialize = new CacheSerialize();
$cacheSerialize->setIlDniDlaWaznosciCache(1);

$barylkaData = false;
$useCache = false;
$stringID = '';
$identifier = 0;
if ((int) $p->data['barylka_id'] > 0) {
    $stringID = 'Produkt::pobierzDaneBarylka(' . $p->data['barylka_id'] . ')';
    $useCache = true;
    $identifier = (int) $p->data['barylka_id'];
    $barylkaData = $cacheSerialize->getData($stringID);
}

if ($barylkaData === false && $identifier > 0) {

    $barylkaData = array();

    // sprawdz czy opis produktu istnieje w barylce
    if (!isset($_GET['barylkaOff'])) {

        $barylkaObj = new Barylka();

        //$barylka = Produkt::pobierzOpisBarylka($identifier, $useBarylkaId);
        $barylka = $barylkaObj->pobierzOpisBarylka($identifier);

        // multimedia
        //$barylkaData['barylka_multimedia'] = Produkt::pobierzMultimediaBarylka($identifier, $useBarylkaId);
        // dokumenty
        //$barylkaData['barylka_dokumenty'] = Produkt::pobierzDokumentyBarylka($identifier, $useBarylkaId);
        $barylkaData['barylka_dokumenty'] = $barylkaObj->pobierzDokumentyBarylka($identifier);

        // zdjecia
        //$barylkaZdjecia = Produkt::pobierzZdjeciaBarylka($identifier, $useBarylkaId);
        $barylkaZdjecia = $barylkaObj->pobierzZdjeciaBarylka($identifier);
        if (!empty($barylkaZdjecia)) {
            $barylkaZdjecia = array_pad($barylkaZdjecia, BARYLKA_ZDJECIA_ILOSC_NA_STRONIE, null);
        }
        $barylkaData['barylka_zdjecia'] = $barylkaZdjecia;

        //$barylkaData['barylka_linki'] = Produkt::pobierzLinkiBarylka($identifier, $useBarylkaId);
        //$barylkaData['barylka_iframe'] = Produkt::pobierzIframeBarylka($identifier, $useBarylkaId);
    }

    if ($barylka['opis'] != null) {

        $barylkaData['opis_barylka'] = $barylka['opis'];

        $barylkaData['atrybuty_grupy_barylka'] = $barylka['atrybuty'];

        if ($barylka['opis']['OpisK'] && strlen($barylka['opis']['OpisK']) > 0) {
            $barylkaData['kupic_opis'] = $barylka['opis']['OpisK'];
        }

        $barylkaData['barylka_opis_full'] = $barylka['opis']['OpisD'];

        if ($barylka['opis']['ProduktRodzajNazwa'] && strlen($barylka['opis']['ProduktRodzajNazwa']) > 0) {
            $barylkaData['producent'] = $barylka['opis']['ProduktRodzajNazwa'];
        }
    }

    // cachuj dane
    if ($useCache == true) {
        $cacheSerialize->setData($barylkaData, $stringID);
    }
}

// przepisz dane z $barylkaData do produktu
if ($useCache === true) {
    $barylkaFields = array(
        'barylka_multimedia', 'barylka_dokumenty', 'barylka_zdjecia',
        'barylka_linki', 'barylka_iframe', 'opis_barylka', 'atrybuty_grupy_barylka', 'kupic_opis',
        'barylka_opis_full',
            //'producent'
    );

    foreach ($barylkaFields as $field) {
        if (isset($barylkaData[$field]))
            $p->data[$field] = $barylkaData[$field];
    }

    $smarty->assign('zdjecia_puste_miejsca', BARYLKA_ZDJECIA_ILOSC_NA_STRONIE - count($p->data['barylka_zdjecia']));
}

if ($_GET['show_barylka'] == 1) {
    \Zend_Debug::dump($p);
}

$czasRealizacjiRepo = new \Core\Dostepnosc\CzasRealizacji();
$czasRealizacji = $czasRealizacjiRepo->get($p->data['id_dostepnosc']);
$smarty->assign('czas_realizacji', $czasRealizacji);

$smarty->assign('p', $p);



/**
 * sprawdzenie czy do kategorii produktu dopisane jest mapowanie z cumulusem
 * i ewentualnie pokazujemy/ukrywamy zakladke Uslugi/gwarancje
 */
if (Ubezpieczenie::getKategoriaZKodami($p->data['id_kategoria']) != null) {
	$smarty->assign('isCumulus', true);
};


/*
 * NOWE OPINIE - funkcje xajax
 * @todo: po dodaniu opini wraz z nowymi parametrami nakładamy blokade_tmp aby kolejni nie widzieli dodanych parametrów tylko ten co właśnie dodawał
 */

$opiniaAjax = new \Opinie\Ajax\Opinie();

function dodajParametr($nazwa, $id_dzial, $id_kategoria, $post_data) {
	
	$objResponse = new xajaxResponse();
	$smarty = SmartyObj::getInstance();
	
	$dataAdd['id_kategoria']			= $id_kategoria;
	$dataAdd['id_parametry_dzialy']		= $id_dzial;
	$dataAdd['nazwa']					= $nazwa;
	$dataAdd['blokada']					= 1;
	$dataAdd['blokada_tmp']				= 0;
	$dataAdd['id_klinet']				= $_SESSION['klientID']; 
	
	$parametr	= new \Opinie\Parametry();
	$res		= $parametr->insert($dataAdd);
	$parametr	= new \Opinie\Parametry();
	
	$opinie_parametry = $parametr->getGroupByDzial(array('id_kategoria' => $id_kategoria, 'blokada_tmp' => 0)); 
	$smarty->assign('opinie_parametry', $opinie_parametry);
	$smarty->assign('id_kategoria', $id_kategoria);
	$smarty->assign('post_parametr', $post_data['parametry']);
	

	if($res > 0) {
		$objResponse->assign('parametry_show', 'innerHTML', $smarty->fetch('_ajax/produkty.opinie.parametry.tpl') );
		$objResponse->script( "$('#parametry_show').find('input:checkbox').uniform()" );
		$objResponse->script( "$('#parCheckbox_".$id_dzial."_".$res."').attr('checked', 'checked');" );
		$objResponse->script( "$('#uniform-parCheckbox_".$id_dzial."_".$res."').find('span').addClass('checked');" );
	}
	
	return $objResponse;
	
}

function glosujOpinie($column = 'glos_tak', $id = 0) {

	$objResponse = new xajaxResponse();
	$opiniaAjax = new \Opinie\Ajax\Opinie();

	if( $_COOKIE['opinie_glosowane_'.$id] > 0 ) {
		$objResponse->assign('opKomunikat_'.$id, 'innerHTML', '<span style="color: red; font-weight: bold;">Już wcześniej oddałeś głos na tą opinię.</span>');
		return $objResponse;
	}
	if( (int)$id == 0 )
		return $objResponse;

	$opiniaAjax->glosujNaOpinie($column, $id);

	$o = new \Opinie\Lista($id);
	$objResponse->assign('opTak_'.$id, 'innerHTML', $o->data['glos_tak'] );
	$objResponse->assign('opNie_'.$id, 'innerHTML', $o->data['glos_nie'] );
	$objResponse->assign('opProcent_'.$id, 'innerHTML', $o->data['ocena_opinii'] );

	$objResponse->assign('opKomunikat_'.$id, 'innerHTML', '<span style="color: green; font-weight: bold;">Dziękujemy za oddanie głosu.</span>');

	setcookie("opinie_glosowane_".$id, $id, time()+3600*24*7);

	return $objResponse;
	
}

function dodajOpinie($data) {
	
	$objResponse	= new xajaxResponse();
	$objOpinie		= new \Opinie\Lista();
	$obParametr		= new \Opinie\Parametry();
		
	$objResponse->assign('formOpiniaBlad', 'style.display', 'none');
	$objResponse->assign('formOpiniaDodana', 'style.display', 'none');
	$objResponse->assign('formOpiniaDodaj', 'style.display', 'block');

	if( !isset($data['formData']) || !is_array($data['formData']) || count($data['formData'])==0 )
	{
		$objResponse->assign('formOpiniaBlad', 'style.display', 'block');
		return $objResponse;
	}

	$id = $objOpinie->insert($data['formData']);
	$parametry = $data['parametry'];
	
	$parametry['id_produkt']	= $data['formData']['id_produkt'];
	$parametry['id_opinia']		= $id;
	$obParametr->addToProduct($parametry);

	$objResponse->assign('formOpiniaDodaj', 'style.display', 'none');
	$objResponse->assign('formOpiniaDodana', 'style.display', 'block');
		
	return $objResponse;
}

function dodajKomentarz(array $kData) {
	
	$objResponse	= new xajaxResponse();
	$objKomentarz	= new \Opinie\Komentarz();
		
	$objResponse->assign('formOpiniaBlad', 'style.display', 'none');
	$objResponse->assign('formOpiniaDodana', 'style.display', 'none');
	$objResponse->assign('formOpiniaDodaj', 'style.display', 'block');

	if( !isset($kData['formData']) || !is_array($kData['formData']) || count($kData['formData'])==0 )
	{
		$objResponse->assign('formOpiniaBlad', 'style.display', 'block');
		return $objResponse;
	}

	$id = $objKomentarz->insert($kData['formData']);

	if($kData['formData']['rodzaj'] == 1) {
		$objResponse->assign('add_return_box_'.$kData['formData']['id_opinia'], 'style.display', 'none');
		$objResponse->assign('formPytanieDodane_'.$kData['formData']['id_opinia'], 'style.display', 'block');
	} elseif($kData['formData']['rodzaj'] == 2) {
		$objResponse->assign('add_odpowiedz_box_'.$kData['formData']['id_opinia'], 'style.display', 'none');
		$objResponse->assign('formOdpowiedzDodana_'.$kData['formData']['id_opinia'], 'style.display', 'block');
	} else {
		$objResponse->assign('add_comment_box_'.$kData['formData']['id_opinia'], 'style.display', 'none');
		$objResponse->assign('formKomentarzDodany_'.$kData['formData']['id_opinia'], 'style.display', 'block');
	}
		
	return $objResponse;
}

function getMainUrl() {
	$url = $_SERVER['REQUEST_URI'];
	$queryPos = strpos($url, '?');
	if ($queryPos > 0) {
		$url = substr($url, 0 , $queryPos);
	}
	return $url;
}

function getRedirectUrl($url) {
	
	$tab = $_GET;
	unset($tab['mod']);
	unset($tab['act']);
	unset($tab['id']);
	$queryString = http_build_query($tab);
	if ($queryString != '') {
		$url .= '?'.$queryString;
	}
	return $url;
}
