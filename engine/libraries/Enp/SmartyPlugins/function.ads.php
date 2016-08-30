<?php

// @TODO - pluginy do wyciagniecia 

//TEST DBG
//$_GET['prv'] = base64_encode("3;10;15"); 

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * 
 * @author Krzysztof Deneka 
 * @author Artur Świerc
 */
function smarty_function_ads($params, &$smarty) 
{    
	if (!isset($params['idMiejsce']) || empty($params['idMiejsce'])) {
		return; 
	}
	
	$count  = (isset($params['count']) && (int) $params['count'] > 0) ? (int) $params['count'] : 1;
	$page   = (isset($params['page']) && (int) $params['page'] > 0) ? (int) $params['page'] : 0;
	
	$constants = _adsGetEnumConstant(); 
	
	if (!is_numeric($params['idMiejsce']) && array_key_exists($params['idMiejsce'], $constants)) {
		$idMiejsce = $constants[$params['idMiejsce']];
	} else {
		$idMiejsce = (int) $params['idMiejsce'];
	}
	
	$categoryMenu = null;
	if (isset($params['categoryMenu']) && (int) $params['categoryMenu'] > 0) {
		$categoryMenu = (int) $params['categoryMenu'];
	}
	
	$ads			= new \AdRotator\SmartyHelper();
	$setupOptions	= \AdRotator\SmartyHelper::getOptions();
	$cachename		= 'ad_plugin' . md5(serialize($setupOptions) . '_' . serialize($params));
    $cache			= \Enp\Cache::get();
	
	$cacheNamespace = \Promocja\Kampania\Element\ElementFactory::getCacheNamespaceByType(\Promocja\Kampania\Element::AD_ROTATOR);
	
//	if (isset($_GET['ad_debug']) && $_GET['ad_debug'] == 1) {
//		\Zend_Debug::dump("namespace: " . \System\Cache\NamespacesEnum::PROMOCJA_ADROTATOR);
//		\Zend_Debug::dump($setupOptions, "setup options");
//		\Zend_Debug::dump($params, "params");
//	}
		
	$preview = _adsGetPreview();
	
	if (false !== $preview && $preview['place'] == $idMiejsce) {
		
		$lista = $ads->getAdsArray($idMiejsce, $count, $page, $preview, $categoryMenu);
		
	} else {
		
		if (false === ($lista = $cache->load($cachename))) {
			$lista = $ads->getAdsArray($idMiejsce, $count, $page, array(), $categoryMenu);
			
			$cache->setNamespace($cacheNamespace);
			$cache->save($lista, $cachename, array(), CACHE_TIME_BIG);
			
			if (isset($_GET['ad_debug']) && $_GET['ad_debug'] == 1) {
				\Zend_Debug::dump("namespace - " . $cacheNamespace);
				\Zend_Debug::dump("save - " . $cachename);
			}
		} else {
			if (isset($_GET['ad_debug']) && $_GET['ad_debug'] == 1) {
				\Zend_Debug::dump("load - " . $cachename);
			}
		}
	}

    $smarty->assign($params['assign'], $lista);
}

function _adsGetEnumConstant() 
{ 	
	static $constnats = null; 
	
	if (null === $constnats) { 
		$reflection = new \ReflectionClass('\AdRotator\Enum');
		$constnats  = $reflection->getConstants();
	}
	return $constnats;
}

function _adsGetPreview() 
{
	if (!isset($_GET['prv']) || empty($_GET['prv'])) { 
		return false;
	}
	
	$prv = base64_decode($_GET['prv']);
	$params = explode(";", $prv);
	
	if (count($params) != 3) { 
		return false;
	}
	
	return array(
		'type'	=> $params[0], 
		'place' => $params[1], 
		'id'	=> $params[2]
	);
}
