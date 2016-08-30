<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Tomasz
 */
class Newsletter {
    
    public $email = false;
    
    public function add() {
        try {
            $soap = new SoapClient("http://api.freshmail.pl/soap?wsdl");
            $soap->loginAccount(array("login" => 'biuro@subvision.pl', "password" => 'Sub677.1'));

            //dodawanie subskrybenta do listy subskrybcyjnej
            $subscriber = array();
            $subscriber['subscriberListHash'] = 'en1e4pxns7';
            $subscriber['email'] = $this->email;
            $subscriber['sendActivationMail'] = true;
            //$subscriber['name'] = 'Test';

            //metoda zwraca unikalny hash subskrybenta
            $subscriberHash = $soap->createSubscriber($subscriber);
            
            echo 'Twój adres został zapisany.';
            
            $soap->logoutAccount();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
?>
