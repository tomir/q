<?php

/**
 * get login
 */
$this->get('/login', function ($request, $response, $args) {
    $objSchool = new \School\Model\School();
    $schoolList = $objSchool->getAll();
    return $this->view->render($response, 'profile.phtml', [
        'school' => $schoolList
    ]);
});

$this->post('/login', function ($request, $response, $args) {
    $data = $request->getParsedBody();

    $serviceStudent = new \Student\Service\Quiz();
    $objStudent = new \Student\Model\Student();
    $row = $objStudent->getOne(array($data));

    if (array_key_exists('id', $row) && $row['id'] > 0) {

        $serviceStudent->startSession($row['id']);
        return $response->withStatus(302)->withHeader('Location', '/konkurs/quiz/wynik');
    } else {
        $id = $objStudent->insert($data);
        $serviceStudent->startSession($id);
        header('Location: /konkurs/quiz/start?login=ok');
        exit();
    }


});

