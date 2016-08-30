<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * 
 * @author Krzysztof Deneka 
 */
function smarty_function_enpurl($params, &$smarty) {
    if (!isset($params['data']) || empty($params['data'])) {
        return;
    }
    if (!isset($params['type']) || empty($params['type'])) {
        return;
    }

    $data = $params['data'];
    $type = $params['type'];
    
    $link = '';

    switch($type) {
        case 'product':
        $link = '/'.Common::url($data['kategoria']) .'/'. Common::url($data['nazwa']) . ',id-' . $data['id'];

        break;
    }

    return $link;
}
