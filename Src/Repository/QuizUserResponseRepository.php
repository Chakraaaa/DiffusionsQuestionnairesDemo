<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\QuizUserResponse;

class QuizUserResponseRepository extends \Appy\Src\Manager
{
    public $table = "quiz_user_response";

    public $champs = array(
        'QUR.id',
        'QUR.quiz_user_id',
        'QUR.question_id',
        'QUR.value',
        'QUR.created_at',
        'QQ.report_ordre'
    );

    public $champsInsert = array(
        '`quiz_user_id`',
        '`question_id`',
        '`value`',
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }


    public function arrayToEntity($datas)
    {
        $questions = array();

        foreach ($datas as $value) {
            $question = new QuizUserResponse();
            $question->id = $value['id'];
            $question->quizUserId = $value['quiz_user_id'];
            $question->questionId = $value['question_id'];
            $question->value = $value['value'];
            $question->createdAt = $value['created_at'];
            $question->reportOrder = $value['report_ordre'];

            $questions[] = $question;
        }

        return $questions;
    }

    public function getQuizUserResponse($criteres = NULL, $order = NULL)
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            if ($order == "value") {
                $sql = "SELECT CAST(QUR.value AS UNSIGNED) AS 'value'";
                //$sql = "SELECT CAST(NULLIF(QUR.value,0) AS UNSIGNED) AS 'value'";
            }
            $sql .= " FROM " . $this->table. " QUR";
            $sql .= " INNER JOIN quiz_user QU ON QUR.quiz_user_id = QU.id";
            $sql .= " INNER JOIN quiz_question QQ ON QUR.question_id = QQ.id";
            $sql .= " WHERE QU.status = 'FINISH'";
            if ($criteres) {
                foreach ($criteres as $key => $value) {
                    if ($key == 'quizId') {
                        $sql .= " AND QU.quiz_id = " . $value;
                    } elseif ($key == 'quizUserId') {
                        $sql .= " AND QU.id = " . $value;
                    } elseif ($key == 'userId') {
                        $sql .= " AND QUR.quiz_user_id IN ( SELECT id FROM quiz_user WHERE user_id = " . $value . " AND quiz_id = " . $criteres['quizId'] . " )";
                    } elseif ($key == 'auto') {
                        $sql .= " AND QU.auto = 1";
                    } elseif ($key == 'excludeAuto') {
                        $sql .= " AND QU.auto = 0";
                    } elseif ($key == 'responseRequired') {
                        $sql .= " AND QQ.response_required = 1";
                    } elseif ($key == 'questionTypeReportOrder') {
                        $sql .= " AND QQ.report_ordre = " . $value;
                    } elseif ($key == 'prccCategory') {
                        $sql .= " AND QQ.prcc_category_id = " . $value;
                    } elseif ($key == 'questionReportOrderPlage') {
                        $sql .= " AND QQ.report_ordre IN " . $value;
                    } elseif ($key == 'fonction') {
                        $sql .= " AND QUR.quiz_user_id in (SELECT QUR2.quiz_user_id FROM quiz_user_response QUR2 WHERE QUR2.value = '" . $value . "') ";
                    } elseif ($key == 'column-critere-name') {
                        $sql .= " AND QUR.quiz_user_id IN (SELECT RQCB.quiz_user_id FROM response_quiz_criteres_barometre RQCB INNER JOIN quiz_criteres_barometre QCB ON RQCB.quiz_criteres_barometre_id = QCB.id WHERE QU.quiz_id = QU.quiz_id AND RQCB." . $value . " = '" . $criteres['choix'] . "')";
                    }

                }
            }

            //On elimine les valeurs null
            //Car il ne faut pas les remplacer par 0 qui peuvent etre des vraies notes
            //Sinon cela fausse les moyennes
            /*if ($order == "value") {
                $sql .= " AND CAST(NULLIF(QUR.value,0) AS UNSIGNED) IS NOT NULL";
            }*/

            if($order) {
                if ($order == "value") {
                    $sql .= " ORDER BY QUR.value ASC";
                } elseif ($order == "user") {
                    $sql .= " ORDER BY QUR.quiz_user_id ASC, QUR.value ASC";
                } elseif ($order == "user-ordre") {
                    $sql .= " ORDER BY QUR.quiz_user_id ASC, QQ.ordre ASC, QUR.value ASC";
                } elseif ($order == "userReport") {
                    $sql .= " ORDER BY QU.id ASC, QQ.report_ordre ASC";
                } elseif ($order == "ordre") {
                    $sql .= " ORDER BY QU.id ASC, QQ.ordre ASC";
                }
            } else {
                $sql .= " ORDER BY  QQ.ordre ASC";
            }
//var_dump($sql);
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);

            $retour = null;
            if ($order == "value") {
                $retour = array();
                foreach ($datas as $value) {
                    $retour[] = $value['value'];
                }
            } else {
                $retour = $this->arrayToEntity($datas);
            }


        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $retour;
    }

    public function insertResponses($questionId, $userId, $value)
    {
        $sql = "INSERT INTO " . $this->table . " (question_id, quiz_user_id, value) VALUES ($questionId, $userId, '$value')";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function getNbResponsesByUser($quiz, $quizUserId)
    {
        $sql = "SELECT COUNT(QUR.id) AS nb_responses";
        $sql .= " FROM " . $this->table . " AS QUR";
        $sql .= " INNER JOIN quiz_user QU ON QUR.quiz_user_id = QU.id";
        $sql .= " WHERE QUR.value <> '' AND QUR.value IS NOT NULL";
        $sql .= " AND QU.quiz_id = " . $quiz->id;
        $sql .= " AND QUR.quiz_user_id = " . $quizUserId;

        $datas = \Appy\Src\Connexionbdd::query($sql)->fetch(\PDO::FETCH_ASSOC);

        return $datas['nb_responses'];
    }






}
