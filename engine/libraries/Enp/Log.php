<?php

namespace Enp;

class Log {
	
	const INFO = 1;
	const ERROR = 2;
	
	static public function exception(\Exception $e, $level = self::ERROR) {
		
		$msg =	'Data : '.date('Y-m-d H:i:s')."\n\n";
		$msg .=	'Message : '.$e->getMessage()."\n\n";
		$msg .=	'Trace : '."\n".$e->getTraceAsString()."\n\n";
		$msg .=	'Code : '.$e->getCode()."\n\n";
		
		switch($level) {
			case self::ERROR :
				mail('piotr.flasza@enp.pl', 'Enp\Log::exception() host: '.$_SERVER['HTTP_HOST'], $msg);
				break;
			case self::INFO : 
				//mail('piotr.flasza@enp.pl', 'Enp\Log::exception() host: '.$_SERVER['HTTP_HOST'], $msg);
				break;
		}
		
	} 
	
}
?>
