<?php

function smarty_modifier_systemloglabel($labelEnum)
{
	$color = '#000000';
	/*
	switch($labelEnum) {
		
		case \System\Log\LabelEnum::CRON :
			$color = '#ff0000';
			break;
		
	}
	*/
	$enum = \Enp\Instance::getInstanceOfClass('\System\Log\LabelEnum');
	
	return '<span style="color: '.$color.'; font-weight:bold">'.$enum->getLabel($labelEnum).'</span>';
	
}
