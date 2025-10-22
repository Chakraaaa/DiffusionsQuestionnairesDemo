<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\QuizCriteresBarometre;
use Appy\Src\Manager;
use Appy\Src\Entity\ResponseQuizCriteresBarometre;

class ResponseQuizCriteresBarometreRepository extends Manager
{
    public $table = "response_quiz_criteres_barometre";

    public $champs = array(
        'RQCB.id',
        'RQCB.quiz_user_id',
        'RQCB.quiz_criteres_barometre_id',
        'RQCB.response_critere1',
        'RQCB.response_critere2',
        'RQCB.response_critere3',
        'RQCB.response_critere4'

    );

    public $champsInsert = array(
        'quiz_user_id',
        'quiz_criteres_barometre_id',
        'response_critere1',
        'response_critere2',
        'response_critere3',
        'response_critere4'


    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas){

        $responses = array();

        foreach ($datas as $key => $value) {
            $response = new ResponseQuizCriteresBarometre();
            $response->id = $value['id'];
            $response->quizUserId = $value['quiz_user_id'];
            $response->quizCriteresBarometreId = $value['quiz_criteres_barometre_id'];
            $response->responseCritere1 = $value['response_critere1'];
            $response->responseCritere2 = $value['response_critere2'];
            $response->responseCritere3 = $value['response_critere3'];
            $response->responseCritere4 = $value['response_critere4'];
            $responses[$value['id']] = $response;
        }

        return $responses;
    }

    public function getResponseQuizCritereBarometre($criteres = NULL, $order = NULL)
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table. " RQCB";
            $sql .= " INNER JOIN quiz_criteres_barometre QCB ON RQCB.quiz_criteres_barometre_id = QCB.id";
            $sql .= " WHERE 1=1 ";
            if ($criteres) {
                foreach ($criteres as $key => $value) {
                    if ($key == 'quizId') {
                        $sql .= " AND QCB.quiz_id = " . $value;
                    } elseif ($key == '') {
                        $sql .= "  " . $value;
                    }
                }
            }

            if($order) {
                if ($order == "user") {
                    $sql .= " ORDER BY RQCB.quiz_user_id ASC";
                } elseif ($order == "") {
                    $sql .= " ";

                }
            } else {
                $sql .= " ORDER BY RQCB.quiz_user_id ASC";
            }
//var_dump($sql);
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);

            $retour = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $retour;
    }


    public function InitiateCriteresResponses($QuizCriteresBarometreId, $QuizUserId){

            $sql = "INSERT INTO " . $this->table . " (quiz_user_id, quiz_criteres_barometre_id) 
                VALUES ('" . $QuizUserId . "','" . $QuizCriteresBarometreId . "')";

            \Appy\Src\Connexionbdd::query($sql);

        }


    public function updateCriteresResponses($numeroCritere, $quizUserId, $response, $quizCriteresBarometreId)
    {
        $escapedResponse = addslashes($response);
        $sql = "UPDATE " . $this->table .
            " SET response_critere" . $numeroCritere . " = '" . $escapedResponse .
            "' WHERE quiz_user_id = " . $quizUserId .
            " AND quiz_criteres_barometre_id = " . $quizCriteresBarometreId . ";";
//var_dump($sql);
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function getCriteresResponses($quizUserId, $quizCriteresBarometreId) : ?ResponseQuizCriteresBarometre
    {
        $sql = "SELECT " . implode(", ", $this->champs) . " ";
        $sql .= "FROM " . $this->table . " AS RQCB ";
        $sql .= "WHERE quiz_user_id = " . intval($quizUserId) . " ";
        $sql .= "AND quiz_criteres_barometre_id = " . intval($quizCriteresBarometreId);

        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $criteres = $this->arrayToEntity($datas);
        $critere = array_shift($criteres);

        return $critere;
    }

    public function getCriteresResponse($quizUserId) : ResponseQuizCriteresBarometre
    {
        $sql = "SELECT " . implode(", ", $this->champs) . " ";
        $sql .= "FROM " . $this->table . " AS RQCB ";
        $sql .= "WHERE quiz_user_id = " . intval($quizUserId);
//var_dump($sql);
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $criteres = $this->arrayToEntity($datas);
        $critere = array_shift($criteres);

        return $critere;
    }


}
