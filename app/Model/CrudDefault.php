<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CrudDefault {

    public $atr;
    public $table;
    public $lang_table;
    public $pow_table = array();
    public $id_form;
    public $sort;
    public $sort_by;
    public $desc;
    public $button;
    public $idColumn;
    public $visibleColumn;
    public $keyColumn;
    public $atr_main;

    public function listCount($id_pow = 0) {

        if($id_pow)
            $where = " WHERE ".$this -> keyColumn." = ".$id_pow;
        else
            $where = "";

        $sql = "SELECT * FROM ".$this -> table.$where;

        try {
            $aResult = array();
            $aResult = ConnectDB::subQuery($sql);

            return count($aResult);

        } catch(PDOException $e) {
            Log::SLog($e->getTraceAsString());
            header("Location: ".MyConfig::getValue("wwwPatchPanel"));
        }

    }

    public function showList($id_pow = 0, $number = 15, $start = 0) {

        //jeśli sortujemy to nie wyświetlamy stronicowania
        if($this -> sort )
            $number = 9999;
        //dopisanie pobierania kolum ustawionych jako wyświetlane w nagłówkach listy
        if($id_pow)
            $where = ' AND '.$this -> keyColumn." = ".$id_pow;
        else
            $where = "";

        if(!$start)
            $start = 0;

        //sprawdzamy czy sortujemy liste wynikow
        if($this -> sort_by != '') {
            if($this -> desc)
                $sortuj = " ORDER BY ".$this -> sort_by." DESC";
            else
                $sortuj = " ORDER BY ".$this -> sort_by;
        }
        else
            $sortuj = "";
        if($this -> lang_table)
            $sql = "SELECT * FROM ".$this -> table." LEFT JOIN ".$this -> lang_table." ON (".$this -> table.".".$this -> idColumn." = ".$this -> lang_table.".".$this -> idColumn.") WHERE lang_prefix = 'pl' ".$where." GROUP BY ".$this -> table.".".$this -> idColumn." ".$sortuj." LIMIT ".$start.", ".$number;
        else {
            if($id_pow)
                $where = 'WHERE '.$this -> keyColumn." = ".$id_pow;
            else
                $where = "";
            $sql = "SELECT * FROM ".$this -> table." ".$where."".$sortuj." LIMIT ".$start.", ".$number;
        }

        try {
            $aResult = array();
            $aResult = ConnectDB::subQuery($sql);

            $this -> aWyniki = $aResult;
            return true;

        } catch(PDOException $e) {
            Log::SLog($e->getTraceAsString());
            header("Location: ".MyConfig::getValue("wwwPatchPanel"));
        }

    }

    public function searchModel($model) {

        //jeśli sortujemy to nie wyświetlamy stronicowania
        if($this -> sort )
            $number = 9999;
        //dopisanie pobierania kolum ustawionych jako wyświetlane w nagłówkach listy
        if($id_pow)
            $where = ' AND '.$this -> keyColumn." = ".$id_pow;
        else
            $where = "";

        if(!$start)
            $start = 0;

        //sprawdzamy czy sortujemy liste wynikow
        if($this -> sort_by != '') {
            if($this -> desc)
                $sortuj = " ORDER BY ".$this -> sort_by." DESC";
            else
                $sortuj = " ORDER BY ".$this -> sort_by;
        }
        else
            $sortuj = "";
        if($this -> lang_table) {

            $sql = "SELECT * FROM ".$this -> table." LEFT JOIN ".$this -> lang_table." ON (".$this -> table.".".$this -> idColumn." = ".$this -> lang_table.".".$this -> idColumn.") LEFT JOIN mgc_car_producer ON ( mgc_car.producer_id = mgc_car_producer.producer_id )
WHERE producer_name LIKE '%".$model."%' AND lang_prefix = 'pl' ".$where." GROUP BY ".$this -> table.".".$this -> idColumn." ".$sortuj." LIMIT 0, 999";

            //$sql = "SELECT * FROM mgc_car LEFT JOIN mgc_car_producer ON (mgc_car.car_id = mgc_car_producer.producer_id) WHERE producer_name like '%".$model."%' GROUP BY mgc_car.car_id ORDER BY mgc_car.car_id DESC LIMIT 0, 999";
            //echo $sql; exit;
        }
        else {
            if($id_pow)
                $where = 'WHERE '.$this -> keyColumn." = ".$id_pow;
            else
                $where = "";
            $sql = "SELECT * FROM ".$this -> table." ".$where."".$sortuj." LIMIT ".$start.", ".$number;
        }

        try {
            $aResult = array();
            $aResult = ConnectDB::subQuery($sql);

            $this -> aWyniki = $aResult;
            return true;

        } catch(PDOException $e) {
            Log::SLog($e->getTraceAsString());
            header("Location: ".MyConfig::getValue("wwwPatchPanel"));
        }

    }

    public function getIdColumn($table = '') {
        if($table == '') $table = $this -> table;

        $sql = "SHOW COLUMNS FROM ".$table;

        try {
            $aWynik = array();
            $aWynik = ConnectDB::subQuery($sql);
            foreach($aWynik as $row) {
                if($row['Key'] == "PRI" && $row['Extra'] = "auto_increment")
                    return $row['Field'];
            }

        } catch(PDOException $e) {
            Log::SLog($e->getTraceAsString());
            header("Location: ".MyConfig::getValue("wwwPatchPanel"));
        }

    }
} 