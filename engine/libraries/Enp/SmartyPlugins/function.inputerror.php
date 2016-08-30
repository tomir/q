<?php

/**
 * @author Artur Świerc
 * 
 * @param array $params
 * @param \Smarty $smarty
 */
function smarty_function_inputerror($params, &$smarty) 	
{
	$sessionFieldErrors = (isset($smarty->_tpl_vars['fieldErrors'])) ? $smarty->_tpl_vars['fieldErrors'] : array();
	$fieldErrors = (isset($params['errors'])) ? $params['errors'] : $sessionFieldErrors;
	$class		 = (isset($params['class'])) ? $params['class'] : 'input-error';
	
	if (!isset($params['input']) || empty($params['input'])) { 
		throw new \Enp\Exception("Nie podano nazwy pola");
	}
	$input = $params['input'];

	if (!isset($fieldErrors[$input]) || empty($fieldErrors[$input])) { 
		return '';
	}
	
	$errorText = '<ul id="errors-' . $input . '" class="' . $class . '" >';
	
	foreach ($fieldErrors[$input] as $errType => $errMsg) {
		$errorText .= '<li>' . $errMsg . '</li>';
	}
	$errorText .= '</ul>';
	
	return $errorText;
}
