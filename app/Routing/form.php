<?php

/**
 * get selector by image id
 */
$this->get('/id/{id}', function ($request, $response, $args) {

    if(!$_SESSION['login']) {
        return $response->withStatus(302)->withHeader('Location', '/konkurs/student/login');
    }

    $objQuestion = new \Question\Model\Question();
    $pytanie = $objQuestion->getOne(array(
        'pytanie_id' => $args['id']
    ));

    $objAnswer = new \Answer\Model\Answer();
    $pytanie['odpowiedzi'] = $objAnswer->getAll(array(
        'id_pytanie' => $pytanie['pytanie_id']
    ), array(
        'sort'  => 'kolejnosc',
        'order' => 'asc'
    ));

    $pytanieNext = $objQuestion->getOne(array(
        'kolejnosc_od' => $pytanie['kolejnosc'],
        'kolejnosc_not' => $pytanie['kolejnosc']
    ));

    $next = false;
    if(is_array($pytanieNext) && count($pytanieNext) > 0 && $pytanieNext['kolejnosc'] > $pytanie['kolejnosc']) {
        $next = true;
    }

    return $this->view->render($response, 'pytanie.phtml', [
        'pytanie' => $pytanie,
        'next'  => $next
    ]);
});

$this->get('/start', function ($request, $response, $args) {
    return $this->view->render($response, 'start.phtml', [
        'info' => $_GET['login']
    ]);
});

$this->post('/start', function ($request, $response, $args) {

    if(!$_SESSION['login']) {
        return $response->withStatus(302)->withHeader('Location', '/konkurs/student/login');
    }

    $serviceStudent = new \Student\Service\Quiz();
    $serviceStudent->startQuiz();

    $objQuestion = new \Question\Model\Question();
    $row = $objQuestion->getOne(array(
        'kolejnosc' => 1
    ));

    header('Location: /konkurs/quiz/id/' . $row['pytanie_id']);
    exit();
});

$this->get('/wynik', function ($request, $response, $args) {

    if(!$_SESSION['login']) {
        return $response->withStatus(302)->withHeader('Location', '/konkurs/student/login');
    }

    $serviceStudent = new \Student\Service\Quiz();
    $answers = $serviceStudent->getQuizAnswers();

    return $this->view->render($response, 'wynik.phtml', [
        'odpowiedzi' => $answers['odpowiedzi'],
        'czas'       => $answers['czas'],
        'wynik'      => $answers['wynik'],
        'ilosc'      => $answers['ilosc'],
    ]);
});

/**
 * make new selector on image
 */
$this->post('/id/{id}', function ($request, $response, $args) {

    $objStudentAnswer = new \StudentAnswer\Model\StudentAnswer();
    $data = $request->getParsedBody();

    $objStudentAnswer->insert(array(
        'uczen_id'     => $_SESSION['user_id'],
        'id_pytanie'   => $data['id_pytanie'],
        'id_odpowiedz' => $data['id_odpowiedz'],
        'czas'         => time(),
    ));

    $objQuestion = new \Question\Model\Question();
    $row = $objQuestion->getOne(array(
        'pytanie_id_od'  => $data['id_pytanie'],
        'pytanie_id_not' => $data['id_pytanie'],
    ));

    if (is_array($row) && !empty($row) && $row['pytanie_id'] > 0) {
        $objStudent = new \Student\Model\Student();
        $objStudent->update(array(
            'quiz_last_pytanie' => $data['id_pytanie']
        ), $_SESSION['user_id']);

        return $response->withStatus(302)->withHeader('Location', '/konkurs/quiz/id/' . $row['pytanie_id']);
    } else {
        //koniec
        $serviceStudent = new \Student\Service\Quiz();
        $serviceStudent->stopQuiz();

        return $response->withStatus(302)->withHeader('Location', '/konkurs/quiz/wynik');
    }


});
