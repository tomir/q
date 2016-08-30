<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxHtml
 *
 * @author tomi
 */
class Admin_AjaxHtml {
	
	static public function searchUser($result) {
		
		$html = include TEMPLATES_DIR.'admin/_ajax/request/searchUser.php';
		return $html;
		
	}
}

?>
