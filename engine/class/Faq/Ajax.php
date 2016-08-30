<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ajax
 *
 * @author tomi
 */
class Faq_Ajax {
	
	public function displayFaq($aResult) {
		
		foreach($aResult as $row) {
			
			$html .= "test";
		}
		
		return $html;
	}
	
}

?>
