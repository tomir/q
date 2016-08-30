<?php

/**
 * get login
 */
$this->get('/login', function ($request, $response, $args) {

    return $this->view->render($response, 'login.phtml', [
        'error' => $_GET['error']
    ]);
});

$this->post('/login', function ($request, $response, $args) {

    $data = $request->getParsedBody();

    if($data['pass'] == MyConfig::getValue('adminPass') && $data['login'] == MyConfig::getValue('adminLogin')) {
        $_SESSION['admin'] = true;
        header('Location: /konkurs/admin/');
        exit();
    } else {
        header('Location: /konkurs/admin/login?error=1');
        exit();
    }
});


$this->get('/', function ($request, $response, $args) {

    if(!isset($_SESSION['admin']) && !$_SESSION['admin']) {
        header('Location: /konkurs/admin/login');
        exit();
    }

    $serviceStudent = new \Student\Service\Quiz();
    $answers = $serviceStudent->getQuizAnswersAll();

    return $this->view->render($response, 'admin.phtml', [
        'odpowiedzi' => $answers
    ]);
});
