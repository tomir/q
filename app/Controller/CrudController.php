<?php

namespace App\Controller;


class CrudController extends Controller
{
    public function index($request, $response)
    {
        return $this->view->render($response, 'index.phtml');
    }
} 