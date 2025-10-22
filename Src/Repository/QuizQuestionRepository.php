<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\Groupe;
use Appy\Src\Entity\QuizQuestion;
use Appy\Src\Entity\QuizUserResponse;
use Composer\Package\Loader\ValidatingArrayLoader;

class QuizQuestionRepository extends \Appy\Src\Manager
{
    public $table = "quiz_question";

    public $champs = array(
        'QQ.id',
        'QQ.quiz_type',
        'QQ.question_type',
        'QQ.label',
        'QQ.label_auto',
        'QQ.ordre',
        'QQ.response_required',
        'QQ.report_ordre',
        'QQ.created_at',
        'QQ.quiz_id'
    );

    public $champsInsert = array(
        'quiz_type',
        'question_type',
        'label',
        'label_auto',
        'ordre',
        'response_required',
        'report_ordre',
        'quiz_id'
    );

    public function arrayToEntity($datas)
    {
        $questions = array();

        foreach ($datas as $value) {
            $question = new QuizQuestion();
            $question->id = $value['id'];
            $question->quizType = $value['quiz_type'];
            $question->questionType = $value['question_type'];
            $question->label = $value['label'];
            $question->labelAuto = $value['label_auto'];
            $question->ordre = $value['ordre'];
            $question->responseRequired = $value['response_required'];
            $question->reportOrdre = $value['report_ordre'];
            $question->createdAt = $value['created_at'];
            $question->quizId = $value['quiz_id'];

            if(isset($value['value'])) {
                $quizUserResponse = new QuizUserResponse();
                $quizUserResponse->questionId = $value['id'];
                $quizUserResponse->value = $value['value'];
                $question->addResponse($quizUserResponse);
            }

            $questions[$question->id] = $question;
        }

        return $questions;
    }


    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function createQuizQuestions($quizId, $questions)
    {
        foreach ($questions as $question) {

            $reportOrder = 'NULL';
            if($question->reportOrdre) {
                $reportOrder = $question->reportOrdre;
            }
            
            $sql = "INSERT INTO " . $this->table . " (" . implode(",", $this->champsInsert) . ") ";
            $sql .= "VALUES ('" . $question->quizType . "', '" . $question->questionType . "', '" . addslashes($question->label) . "', '" . addslashes($question->labelAuto) . "', '" . $question->ordre . "', '" . $question->responseRequired . "', " . $reportOrder . ", " . $quizId . ")";
            
            // Debug SQL si nécessaire
            if (\Appy\Src\Config::DEBUG) {
                error_log("DEBUG SQL - createQuizQuestions: " . $sql);
            }
            
            \Appy\Src\Connexionbdd::query($sql);
        }
    }

    public function getQuestionById($questionId) {
        $sql = "SELECT " . implode(",", $this->champs);
        $sql .= " FROM " . $this->table. " QQ";
        $sql .= " WHERE id = '" . $questionId . "'";
        //var_dump($sql);
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);
        $question = array_shift($questions);
        return $question;

    }

    public function getQuestionsByQuizId($quizId) {
        $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS QQ WHERE quiz_id = $quizId ORDER BY ordre ASC";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);
        return $questions;
    }

    public function getQuestionsByQuizIdAndType($quizId, $questionType) {
        $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS QQ WHERE quiz_id = $quizId AND question_type = '". $questionType . "' ORDER BY ordre ASC";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);
        return $questions;
    }

    public function getQuestionsRequiredResponseByQuizId($quizId) {
        $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS QQ WHERE response_required = 1 AND quiz_id = $quizId";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);
        return $questions;
    }

    public function getQuestionsByQuizIdAndOrder($quizId, $order) : QuizQuestion {
        $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS QQ WHERE quiz_id = " . $quizId . " AND ordre = " . $order;
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);
        $question = array_shift($questions);

        return $question;
    }

    public function getQuestionsAndResponseByQuizId($quizId, $quizUserId) {
        $sql = "SELECT " . implode(", ", $this->champs) . ", QUR.value ";
        $sql .= " FROM " . $this->table . " AS QQ LEFT JOIN quiz_user_response QUR ON QUR.question_id = QQ.ID AND QUR.quiz_user_id = " . $quizUserId;
        $sql .= " WHERE QQ.quiz_id = " . $quizId;
        $sql .= " ORDER BY QQ.ordre";
//var_dump($sql);
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);

        return $questions;
    }

    public function getQuestionByQuizIdAndReportOrder($quizId, $order): QuizQuestion {
        $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS QQ WHERE quiz_id = " . $quizId . " AND report_ordre = " . $order;
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);
        $question = array_shift($questions);
        return $question;
    }

    public function getChapterByQuizIdAndOrder($quizId, $order): QuizQuestion {
        $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS QQ WHERE quiz_id = " . $quizId . " AND ordre = " . $order;

        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $chapters = $this->arrayToEntity($datas);
        $chapter = array_shift($chapters);

        return $chapter;
    }


    public function getNbQuestionsByQuizId($quizId)
    {
        $sql = "SELECT COUNT(id) AS nb_questions";
        $sql .= " FROM " . $this->table . " AS QQ";
        $sql .= " WHERE quiz_id = " . intval($quizId);
        $sql .= " AND response_required = 1";
        $sql .= " AND (label IS NOT NULL AND TRIM(REPLACE(REPLACE(REPLACE(label, '<div>', ''), '</div>', ''), '<div style=\"font-size: 16px; color: #696252;font-family:Trebuchet MS\">', '')) <> '')";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetch(\PDO::FETCH_ASSOC);
        return $datas['nb_questions'];
    }

    public function getChapterBarometre($quizId) {
        $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS QQ WHERE quiz_id = " . $quizId . " AND question_type = 'CHAPTER' ORDER BY ordre";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);
        return $questions;
    }

    public function UpdateLabelsByQuestionIdAndQuizId($questionId, $quizId, $label, $label_auto)
    {
        $newLabel = addslashes($label);
        $newLabelAuto = addslashes($label_auto);
        if ($label_auto !== null){
            $sql = "UPDATE $this->table 
            SET label = '$newLabel', label_auto = '$newLabelAuto'
            WHERE id = '$questionId' AND quiz_id = '$quizId'";
            \Appy\Src\Connexionbdd::query($sql);
        } else {
            $sql = "UPDATE $this->table 
            SET label = '$newLabel'
            WHERE id = '$questionId' AND quiz_id = '$quizId'";
            \Appy\Src\Connexionbdd::query($sql);
        }
    }

    public function createNewSingleChapter360($label, $label_auto, $quizId)
    {
        $newLabel = addslashes($label);
        $newLabelAuto = addslashes($label_auto);
        $sqlMaxOrdre = "SELECT MAX(ordre) AS max_ordre FROM $this->table WHERE quiz_type = '360'";
        $maxOrdreResult = \Appy\Src\Connexionbdd::query($sqlMaxOrdre)->fetchAll(\PDO::FETCH_ASSOC);
        $maxOrdre = (isset($maxOrdreResult[0]['max_ordre']) && $maxOrdreResult[0]['max_ordre'] !== null) ? $maxOrdreResult[0]['max_ordre'] : 0;
        $newOrdre = $maxOrdre + 1;
        $sql = "INSERT INTO $this->table (quiz_id, quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre) 
            VALUES ('$quizId', '360', 'CHAPTER', '$newLabel', '$newLabelAuto', '$newOrdre', 0, NULL)";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function createNewSingleRadio360Text($label, $label_auto, $quizId)
    {
        $newLabel = addslashes($label);
        $newLabelAuto = addslashes($label_auto);
        $sqlMaxOrdre = "SELECT MAX(ordre) AS max_ordre FROM $this->table WHERE quiz_type = '360'";
        $maxOrdreResult = \Appy\Src\Connexionbdd::query($sqlMaxOrdre)->fetchAll(\PDO::FETCH_ASSOC);
        $maxOrdre = (isset($maxOrdreResult[0]['max_ordre']) && $maxOrdreResult[0]['max_ordre'] !== null) ? $maxOrdreResult[0]['max_ordre'] : 0;
        $newOrdre = $maxOrdre + 1;
        $sqlMaxReportOrdre = "SELECT MAX(report_ordre) AS max_report_ordre FROM $this->table WHERE quiz_type = '360'";
        $maxReportOrdreResult = \Appy\Src\Connexionbdd::query($sqlMaxReportOrdre)->fetchAll(\PDO::FETCH_ASSOC);
        $maxReportOrdre = (isset($maxReportOrdreResult[0]['max_report_ordre']) && $maxReportOrdreResult[0]['max_report_ordre'] !== null) ? $maxReportOrdreResult[0]['max_report_ordre'] : 0;
        $newReportOrdre = $maxReportOrdre + 1;
        $sql = "INSERT INTO $this->table (quiz_id, quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre) 
        VALUES ('$quizId', '360', 'INPUT-RADIO', '$newLabel', '$newLabelAuto', '$newOrdre', 1, '$newReportOrdre')";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function createNewSingleRadio360List($labelNormal, $labelAutoEvaluated, $quizId)
    {
        $newLabelNormal = addslashes($labelNormal);
        $newLabelAutoEvaluated = addslashes($labelAutoEvaluated);
        $sqlMaxOrdre = "SELECT MAX(ordre) AS max_ordre FROM $this->table WHERE quiz_type = '360'";
        $maxOrdreResult = \Appy\Src\Connexionbdd::query($sqlMaxOrdre)->fetchAll(\PDO::FETCH_ASSOC);
        $maxOrdre = (isset($maxOrdreResult[0]['max_ordre']) && $maxOrdreResult[0]['max_ordre'] !== null) ? $maxOrdreResult[0]['max_ordre'] : 0;
        $newOrdre = $maxOrdre + 1;
        $sqlMaxReportOrdre = "SELECT MAX(report_ordre) AS max_report_ordre FROM $this->table WHERE quiz_type = '360'";
        $maxReportOrdreResult = \Appy\Src\Connexionbdd::query($sqlMaxReportOrdre)->fetchAll(\PDO::FETCH_ASSOC);
        $maxReportOrdre = (isset($maxReportOrdreResult[0]['max_report_ordre']) && $maxReportOrdreResult[0]['max_report_ordre'] !== null) ? $maxReportOrdreResult[0]['max_report_ordre'] : 0;
        $newReportOrdre = $maxReportOrdre + 1;
        $sql = "INSERT INTO $this->table (quiz_id, quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre) 
            VALUES ('$quizId', '360', 'INPUT-RADIO', '$newLabelNormal', '$newLabelAutoEvaluated', '$newOrdre', 1, '$newReportOrdre')";

        \Appy\Src\Connexionbdd::query($sql);
    }

    public function deleteSingleQuestion360($questionId)
    {
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE id = '" . $questionId . "';";
            \Appy\Src\Connexionbdd::query($sql);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return true;
    }
}
