<?php

namespace Przepis\Enum;

/**
 * Description of Kucharz
 *
 * @author tomi
 */
class Cooker {
	
	const COOKER_KURON_ID = 2;
	const COOKER_JEDRASZCZAK_ID = 3;
	
	const COOKER_KURON_ALIAS = 'kuron';
	const COOKER_JEDRASZCZAK_ALIAS = 'jedraszczak';
	
	public static $cookerAlias = array(
		self::COOKER_KURON_ID			=> self::COOKER_KURON_ALIAS, 
		self::COOKER_JEDRASZCZAK_ID		=> self::COOKER_JEDRASZCZAK_ALIAS
	);
	
	public static $cookerId = array(
		self::COOKER_KURON_ALIAS		=> self::COOKER_KURON_ID, 
		self::COOKER_JEDRASZCZAK_ALIAS	=> self::COOKER_JEDRASZCZAK_ID
	);
}
