<?php

namespace App\Controller;

use App\Model\User;

class CrudController extends Controller
{
    public function index($request, $response)
    {
//        $obMain = new AdminMain($atr);
//        $show_number = 100;
//        $list_count = $obMain -> listCount($atr3);
//        $show_pages = ceil($list_count/$show_number);
//
//        if(intval($atr2)) {
//            $show_page = $atr2;
//        } else $show_page = 1;
//
//        $show_start = ($show_page * $show_number) - $show_number;
//
//        if($_POST["model"])
//            $obMain -> searchModel($_POST["model"]);
//        else
//            $obMain -> showList($atr3, $show_number, $show_start);
//        if($obMain -> pow_table['table'] != '')
//            $aPow = $obMain -> getPowTable();
//
//        $aColumns 		= $obMain -> getColumnList();
//        $aResult 		= $obMain -> aWyniki;
//        $dodActions 	= $obMain -> getDodActions();
//        $idForm 		= $obMain -> id_form;
//        $button 		= $obMain -> button;
//        $filtr 			= $obMain -> pow_table;
//        $menu_url 		= $obMain -> atr;
//        $idColumn 		= $obMain -> idColumn;
//        $visibleColumn 	= $obMain -> visibleColumn;
//
//        if($menu_url == 'pojazdy') {
//            foreach($aResult as $key => $row) {
//                $obCar = new Admin_Car($row['car_id']);
//                $aResult[$key]['photos'] = $obCar->carGetPhotos($row['car_id']);
//            }
//        }
//
//        if($obMain -> sort)
//            $template = 'a_listSort.php';
//        else
//            $template = 'a_list.php';

        $user = User::where('email', 't.cisowski@gmail.com')->first();
        return $this->view->render($response, 'index.twig');
    }

    public function view($request, $response)
    {
//        $obMain = new AdminMain($atr);
//        $obFormularz    = new Formularz();
//        //$aLangs	    = $obFormularz  -> getAvalibleLanguages();
//        $aData	    = $obFormularz  -> start($atr, $obMain->id_form, 'edit', $atr4);
//        $aColumns	    = $obFormularz  -> aPola;
//        if(isset($_POST['dodaj_pow'])) {
//            $obFormularz  -> saveRelated($_POST['form_id'], $_POST);
//        }
//        if(isset($_POST['dodaj_pow_array'])) {
//            $obFormularz  -> saveRelatedArray($_POST['form_id'], $_POST);
//        }
//        if(isset($_POST['zapisz_foto'])) {
//            $obFormularz  -> saveRelatedPhotos($_POST['form_id'], $_POST);
//        }
//        $aRelatedData   = $obMain -> getMultiRelatedData($atr4);
//        //echo '<pre>';
//        //print_r($aRelatedData);
//        //echo '</pre>';
//        $lang_table = $obMain -> lang_table;
//
//
//        if($lang_table)
//            $template = 'a_showLang.php';
//        else
//            $template = 'a_show.php';
    }

    public function edit($request, $response)
    {
//        $obMain = new AdminMain($atr);
//        $obFormularz = new Formularz();
//        $aDane = $obFormularz -> start($atr, $obMain->id_form, $atr2, $atr4);
//        $aPola = $obFormularz -> aPola;
//        $akcja = $atr;
//        $akcja2 = $atr2;
//        $akcja4 = $atr4;
//        $formId = $obMain->id_form;
//        $lang_table = $obMain -> lang_table;
//
//        if($lang_table) {
//            $aLangs = $obFormularz -> getAvalibleLanguages();
//            $template = 'a_formLang.php';
//        }
//        else
//            $template = 'a_form.php';
    }

    public function save($request, $response)
    {

    }

    public function delete($request, $response)
    {
//        $obMain = new AdminMain($atr);
//        $wynik = $obMain -> deleteAjax($atr3);
    }
} 