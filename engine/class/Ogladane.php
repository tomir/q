<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author tomi
 */
class Ogladane
{
	
	static function dodaj( $id )
	{
		if( !in_array($id, $_SESSION['ostatnio_ogladane']) )
		{
			$tmp = array_reverse($_SESSION['ostatnio_ogladane']);
			$tmp[] = $id;
			$_SESSION['ostatnio_ogladane'] = array_reverse($tmp);
			if( count($_SESSION['ostatnio_ogladane']) > 3 )
				$_SESSION['ostatnio_ogladane'] = array_slice( $_SESSION['ostatnio_ogladane'], 0, 3 );
		}
		setcookie("ostatnio_ogladane_cookie", implode(',', $_SESSION['ostatnio_ogladane']), time()+(30*24*3600));		
	}

}