<?php

namespace App\Controller;


class AuthController extends Controller
{

    public function changePassword($request, $response)
    {
//        if(!$_POST['zapisz']) {
//            $template = 'a_changePass.php';
//        }
//        else {
//            $wynik = $obProfile->zmienHaslo($_POST, $aUser['username']);
//            if($wynik) header("Location: ".MyConfig::getValue("wwwPatchPanel").",2,edit_success");
//            else header("Location: ".MyConfig::getValue("wwwPatchPanel")."zmienhaslo.html,1,edit_error");
//        }
    }

    public function logout($request, $response) {
//        $obProfile -> wyloguj();
    }
} 