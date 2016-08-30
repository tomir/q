<?php

namespace Flashmsg;

class Tool {
	
	public static function getForTekst($tekst, $enum) {
		return self::checkInDb($tekst, $enum);
	}
	
	public static function getHashFormTekst($tekst) {
		$tekst = trim($tekst);
		$tekst = \Common::unPolishString($tekst);
		$tekst = preg_replace('/[^a-zA-Z0-9]/', ' ', $tekst);
		$tekst = preg_replace('/( )+/', ' ', $tekst);
		
		return md5($tekst);
	}
	
	public static function checkInDb($tekst, $enum) {
		
		$hash = self::getHashFormTekst($tekst);
		$enum = \Common::clean_input($enum);
		
		$model = \Enp\Instance::getInstanceOfClass('\Flashmsg\Model\Flashmsg');
		/* @var $model \Flashmsg\Model\Flashmsg */
		$one = $model->getFirst(array(
			'hash' => $hash,
			'enum' => $enum
		));
		if (trim($one['hash']) != $hash) {
			// insert
			$model->insert(array(
				'tekst' => $tekst,
				'tekst_org' => $tekst,
				'hash'	=> $hash,
				'enum'	=> $enum
			));
			
			return $tekst;
		} else {
			return $one['tekst'];
		}
	}
	
}