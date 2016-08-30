<?php

namespace App\Controller;

use App\Model\User;

class CrudController extends Controller
{
    public function index($request, $response)
    {
        $user = User::where('email', 't.cisowski@gmail.com')->first();
        return $this->view->render($response, 'index.twig');
    }
} 