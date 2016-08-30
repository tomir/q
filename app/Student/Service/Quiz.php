<?php
/**
 * Created by PhpStorm.
 * User: Tomek
 * Date: 26.04.16
 * Time: 10:23
 */

namespace Student\Service;


class Quiz
{

    public function startSession($userId)
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['login'] = true;
    }

    public function startQuiz()
    {
        $_SESSION['quiz_start_time'] = time();

        $objStudent = new \Student\Model\Student();
        $objStudent->update(array(
            'quiz_start' => $_SESSION['quiz_start_time']
        ), $_SESSION['user_id']);
    }

    public function stopQuiz()
    {
        unset($_SESSION['quiz_start_time']);

        $objStudent = new \Student\Model\Student();
        $objStudent->update(array(
            'quiz_end'          => time(),
            'quiz_last_pytanie' => 0
        ), $_SESSION['user_id']);
    }

    public function getQuizAnswers()
    {

        $result = array();

        $objStudent = new \Student\Model\Student();
        $student = $objStudent->getOne(array(
            'uczen_id' => $_SESSION['user_id'],
        ));

        $result['czas'] = date("i:s", (int)($student['quiz_end']-$student['quiz_start']));

        $objQuestion = new \Question\Model\Question();
        $questions = $objQuestion->getAll();
        $result['ilosc'] = count($questions);
        $prawidlowe = 0;

        $objAnswer = new \Answer\Model\Answer();
        $objStudentAnswer = new \StudentAnswer\Model\StudentAnswer();

        foreach ($questions as $key => $question) {
            $answers = $objAnswer->getAll(array(
                'id_pytanie' => $question['pytanie_id']
            ));

            $studentAnswer = $objStudentAnswer->getOne(array(
                'uczen_id'   => $_SESSION['user_id'],
                'id_pytanie' => $question['pytanie_id']
            ));

            foreach ($answers as $key2 => $row) {
                if ($studentAnswer['id_odpowiedz'] == $row['odpowiedz_id']) {
                    $answers[$key2]['odpowiedz'] = true;
                    if ($row['prawidlowa'] == 1) {
                        $prawidlowe++;
                    }
                }
            }

            $questions[$key]['answers'] = $answers;
        }

        $result['wynik'] = $prawidlowe;
        $result['odpowiedzi'] = $questions;

        return $result;

    }

    public function getQuizAnswersAll()
    {
        $result = array();

        $objStudent = new \Student\Model\Student();
        $students = $objStudent->getAll(array(
            'quiz_end_od' => 1,
        ), array(
            'sort' => 'quiz_end',
            'order' => 'desc'
        ));
		
		$objSchool = new \School\Model\School();
		$schoolList = $objSchool->getAll();
		
		$schoolList2 = array();
		foreach($schoolList as $row) {
			$schoolList2[$row['szkoly_id']] = $row['nazwa'];
		}

        foreach($students as $key3 => $student) {
            $result[$key3]['czas'] = date("i:s", (int)($student['quiz_end']-$student['quiz_start']));
			$result[$key3]['student'] = $student;
			$result[$key3]['szkola'] = $schoolList2[$student['id_szkola']];

            $objQuestion = new \Question\Model\Question();
            $questions = $objQuestion->getAll();
            $result[$key3]['ilosc'] = count($questions);
            $prawidlowe = 0;

            $objAnswer = new \Answer\Model\Answer();
            $objStudentAnswer = new \StudentAnswer\Model\StudentAnswer();

            foreach ($questions as $key => $question) {
                $answers = $objAnswer->getAll(array(
                    'id_pytanie' => $question['pytanie_id']
                ));

                $studentAnswer = $objStudentAnswer->getOne(array(
                    'uczen_id'   => $student['uczen_id'],
                    'id_pytanie' => $question['pytanie_id']
                ));

                foreach ($answers as $key2 => $row) {
                    if ($studentAnswer['id_odpowiedz'] == $row['odpowiedz_id']) {
                        $answers[$key2]['odpowiedz'] = true;
                        if ($row['prawidlowa'] == 1) {
                            $prawidlowe++;
                        }
                    }
                }

                $questions[$key]['answers'] = $answers;
            }

            $result[$key3]['wynik'] = $prawidlowe;
            $result[$key3]['odpowiedzi'] = $questions;
        }

        return $result;
    }
} 