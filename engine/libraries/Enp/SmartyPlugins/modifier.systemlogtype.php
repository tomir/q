<?php

function smarty_modifier_systemlogtype($typeEnum)
{
	$color = '#000000';
	switch($typeEnum) {
		
		case \System\Log\TypeEnum::ERROR :
			$color = '#ff0000';
			break;
		case \System\Log\TypeEnum::WARNING :
			$color = '#ff9900';
			break;
		case \System\Log\TypeEnum::SUCCESS :
			$color = '#006600';
			break;
		case \System\Log\TypeEnum::INFO :
			$color = '#000066';
			break;
		
	}
	
	$enum = \Enp\Instance::getInstanceOfClass('\System\Log\TypeEnum');
	
	return '<span style="color: '.$color.'; font-weight:bold">'.$enum->getLabel($typeEnum).'</span>';
	
}
