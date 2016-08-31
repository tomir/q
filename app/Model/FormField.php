<?php
/**
 * Created by PhpStorm.
 * User: Tomek
 * Date: 31.08.16
 * Time: 10:49
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model {

    protected $table = 'cms_form_field';

    public function getVisibleColumn($id_form = 0) {

        if($id_form == 0)
            $id_form = $this->id_form;
        $sql = "SELECT column_s FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$id_form." AND akcje = 'visible' LIMIT 1";
        $aTab = array();
        $aTab = ConnectDB::subQuery($sql);
        if(is_array($aTab) && count($aTab) > 0)
            return $aTab[0]['column_s'];
        else
            return 0;
    }

    public function getKeyColumn() {
        $sql = "SELECT column_s FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$this -> id_form." AND klucz_zew = 1 LIMIT 1";
        $aTab = array();
        $aTab = ConnectDB::subQuery($sql);
        if(is_array($aTab) && count($aTab) > 0)
            return $aTab[0]['column_s'];
        else
            return 0;
    }

    public function getSortColumn() {
        $sql = "SELECT column_s FROM ".MyConfig::getValue("dbPrefix")."cms_formularz_pola WHERE id_formularza = ".$this -> id_form." AND akcje = 'sort_colum' LIMIT 1";
        $aTab = array();
        $aTab = ConnectDB::subQuery($sql);
        if(is_array($aTab) && count($aTab) > 0)
            return $aTab[0]['column_s'];
        else
            return 0;
    }
} 