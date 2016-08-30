<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author tomaszcisowski
 */
abstract class AbstractObject {
	
	abstract public function save( $aData ) ;
	abstract static public function delete( $id ) ;
}
?>
