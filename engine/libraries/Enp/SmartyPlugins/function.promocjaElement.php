<?php

/**
 * @param array $params
 * @param \Smarty $smarty
 */
function smarty_function_promocjaElement($options, &$smarty) 
{
	$defaultOptions = array(
		'formId'			 => null, 
		'submitCallback'	 => null, 
		'submitButtonClass'	 => null,
		'elementId'			 => null,
		'elementType'		 => null,
		'parts'				 => 'ksldb' // parts : k - kampania, s - serwis, l - listay , d - daty, b - blokada
	);
	
	$options = array_merge($defaultOptions, $options);
	
	$controller = 'promocja-element';
	$action		= 'elements-config';
		
	$_POST['promo_element_params'] = $options;
	
	ob_start();
	\Enp\Admin\Controller::run($controller, $action, CONTROLLERS_DIR . 'Enp/', 'Enp/');
	
	$content = ob_get_clean();
	
	unset($_POST['promo_element_params']);
	return $content;
}