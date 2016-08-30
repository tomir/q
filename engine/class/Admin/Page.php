<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Class Admin_Page {
    protected $page_id;
    protected $aPage = false;
    
    public function __construct($pageId = 0) {
        if($pageId > 0) {
            $this -> page_id = $pageId;
            try {
                $sql = "SELECT *
                        FROM shop_page
                        WHERE page_id= ".$pageId."
                        ";
                $aResult = ConnectDB::subQuery($sql);
                
                if (!is_array($aResult)){
                    return false;
                } 
                
                $this->aPage = $aResult[0];
                
            } catch (PDOException $e){
                //echo "Błąd nie można utworzyć obiektu material.";
                return false;
            }
        } else {
            $this->page_id = 0;
        }
    }
    
    public function getPage($id = 0) {
        if($id > 0) {
            
        } else {
            return $this->aPage;
        }
    }
    
    public function getPageList() {
        $sql = "SELECT * FROM shop_page";
        try {
            $aResult = ConnectDB::subQuery($sql);
            if(!is_array($aResult)){
                    return false;
            } else {
                return $aResult;
            }
            
        } catch (PDOException $e){
            //echo "Błąd nie można utworzyć obiektu material.";
            return false;
        }
    }
    
    public function save($data) {
        if ($this->page_id) {
                $update = ' page_id = '.$this->page_id;
                $action = 'UPDATE';
            } else {
                $update = '';
                $action = 'INSERT';
        }

        try {
            if(ConnectDB::subAutoExec('shop_page', $data, $action, $update))
            return true;
            else return false;

        } catch (PDOException $e){

            return false;
        }
	
    }
    
}
