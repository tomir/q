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
class Admin_Import {
    
    public $hurtownia_id = false;
    
    public function importList($start = 0, $limit = 50) {
        ini_set('max_execution_time', 9000);
        if($this->hurtownia_id) {
            $where = "WHERE hurtownia_id = ".$this->hurtownia_id." AND product_id = 0";
        }
        $sql = "SELECT * FROM shop_hurtownie_import ".$where." LIMIT ".$start.", ".$limit;
        try {
                $aResult = array();
                $aResult = ConnectDB::subQuery($sql);
        }
        catch(PDOException $e) {
                Log::SLog($e->getTraceAsString());
                header("Location: ".MyConfig::getValue("wwwPatch"));
        }
        return $aResult;
    }
    
    public function importListCount($filtr) {
        if($this->hurtownia_id) {
            $where = "WHERE hurtownia_id = ".$this->hurtownia_id." AND product_id = 0";
        }
        $sql = "SELECT count(*) as ile FROM shop_hurtownie_import ".$where."";
        try {
                $aResult = array();
                $aResult = ConnectDB::subQuery($sql,'fetch');
        }
        catch(PDOException $e) {
                Log::SLog($e->getTraceAsString());
                header("Location: ".MyConfig::getValue("wwwPatch"));
        }
        return $aResult['ile'];
    }
    
    public function importCopy($import_id) {
        try {
            $aImport = $this->importGet($import_id);
            $producer = new Admin_Producents();
            $producer->setProducentName(addslashes($aImport[0]['import_producer']));

            $aProduct = array(
                "p_name"=>$aImport[0]['import_name'],
                "p_description"=>$aImport[0]['import_desc'],
                "p_price"=>$aImport[0]['import_price_suggest'],
                "vat_id"=>$aImport[0]['vat_id'],
                "p_price_gross"=>$aImport[0]['import_price_suggest_gross'],
                "p_magazine"=>$aImport[0]['import_inventory'],
                "p_age_id"=>$aImport[0]['import_age_id'],
                "p_price_buy"=>$aImport[0]['import_price_buy']
            );
            if(!$aProduct['producent_id'] = $producer->search()) {
                $aProduct['producent_id'] = $producer->save(array("producent_name" => $aImport[0]['import_producer']));
                echo $aProduct['producent_id'];
            }
            
            if(is_null($aProduct['p_description'])) {
                $aProduct['p_description'] = '';
            }
            
            if(is_null($aProduct['p_age_id'])) {
                $aProduct['p_age_id'] = 0;
            }

            $p = new Admin_Product();

            $product_id = $p->save($aProduct);
            
            foreach($aImport['img'] as $img) {
                if(strrpos($img['img_url'], 'jpg') > 0) {
                    $fData = array(
                        'm_jpg' => 1,
                        'p_id' => $product_id,
                        'url' => $img['img_url']
                    );
                    $p->saveFileUrl($fData);
                }
            }
            
            ConnectDB::subExec('UPDATE shop_hurtownie_import SET product_id = '.$product_id.' WHERE import_id = '.$import_id); 
            //$this->importCopyImg($product_id);
        }
        catch(PDOException $e) {
                Log::SLog($e->getTraceAsString());
                header("Location: ".MyConfig::getValue("wwwPatch"));
        }
        return $aResult;
    }
    
    public function importCopyImg($product_id = false) {
        $p = new Admin_Product();

        //$product_id = $p->save($aProduct);
        
        if ($product_id) {
            $where = 'WHERE i.product_id = '.$product_id;
        }
        
        $sql = "SELECT * FROM shop_product p JOIN shop_hurtownie_import i on p.p_id = i.product_id ".$where;
        $aList = ConnectDB::subQuery($sql);
        
        foreach($aList as $row) {
            $aImport = $this->importGet($row['import_id']);
            $i = 0;
            foreach($aImport['img'] as $img) {
                if(strrpos($img['img_url'], 'jpg') > 0) {
                    $fData = array(
                        'm_jpg' => 1,
                        'p_id' => $row['p_id'],
                        'url' => $img['img_url']
                    );
                    
                    if($i == 0) {
                        $fData['m_main'] = 1;
                    }
                    $i++;
                    $p->saveFileUrl($fData);
                }
            }
        }
    }
    
    public function importGet($import_id) {
        $sql = "SELECT * FROM shop_hurtownie_import WHERE import_id = ".$import_id;
        try {
                $aResult = array();
                $aResult = ConnectDB::subQuery($sql);
                
                $img_sql = "SELECT * FROM shop_hurtownie_import_img WHERE import_id = ".$import_id;
                $aResult['img'] = ConnectDB::subQuery($img_sql);
        }
        catch(PDOException $e) {
                Log::SLog($e->getTraceAsString());
                header("Location: ".MyConfig::getValue("wwwPatch"));
        }
        return $aResult;
    }
}
?>
