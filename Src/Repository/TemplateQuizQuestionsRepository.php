<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\TemplateQuizQuestions;

class TemplateQuizQuestionsRepository extends \Appy\Src\Manager
{
    public $table = "template_quiz_question";

    public $champs = array(
        'TQQ.id',
        'TQQ.quiz_type',
        'TQQ.question_type',
        'TQQ.label',
        'TQQ.label_auto',
        'TQQ.ordre',
        'TQQ.response_required',
        'TQQ.report_ordre'
    );

    public $champsInsert = array(
        'quiz_type',
        'question_type',
        'label',
        'label_auto',
        'ordre',
        'response_required',
        'report_ordre'

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
            $question = new TemplateQuizQuestions();
            $question->id = $value['id'];
            $question->quizType = $value['quiz_type'];
            $question->questionType = $value['question_type'];
            $question->label = $value['label'];
            $question->labelAuto = $value['label_auto'];
            $question->ordre = $value['ordre'];
            $question->responseRequired = $value['response_required'];
            $question->reportOrdre = $value['report_ordre'];
            // prcc_category_id supprimé car le champ n'existe pas dans la table

            $questions[] = $question;
        }

        return $questions;
    }

    public function getQuestionsByType($quizType)
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table. " TQQ";
            $sql .= " WHERE quiz_type = '" . $quizType . "'";
            $sql .= " ORDER BY ordre ASC";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $questions = $this->arrayToEntity($datas);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $questions;
    }

    public function getQuestionsByPrccCategory($prccCategoryId)
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table. " TQQ";
            $sql .= " WHERE quiz_type = 'PRCC'";
            // Note: prcc_category_id n'existe pas dans la table, retour de toutes les questions PRCC
            $sql .= " ORDER BY ordre ASC";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $questions = $this->arrayToEntity($datas);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $questions;
    }

    public function getQuestionById($questionId){
        $sql = "SELECT " . implode(",", $this->champs);
        $sql .= " FROM " . $this->table. " TQQ";
        $sql .= " WHERE id = '" . $questionId . "'";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $questions = $this->arrayToEntity($datas);
        $question = array_shift($questions);
        return $question;

    }

    public function UpdateLabelsByQuestionId($questionId, $label, $label_auto)
    {
        $newLabel = addslashes($label);
        if ($label_auto !== null) {
            $newLabelAuto = addslashes($label_auto);
            $sql = "UPDATE $this->table 
                SET label = '$newLabel', 
                    label_auto = '$newLabelAuto' 
                WHERE id = '$questionId'";
        } else {
            $sql = "UPDATE $this->table 
                SET label = '$newLabel'
                WHERE id = '$questionId'";
        }

        \Appy\Src\Connexionbdd::query($sql);
    }



    public function createNewChapter360($label, $label_auto)
    {
        $newLabel = addslashes($label);
        $newLabelAuto = addslashes($label_auto);
        $sqlMaxOrdre = "SELECT MAX(ordre) AS max_ordre FROM $this->table WHERE quiz_type = '360'";
        $maxOrdreResult = \Appy\Src\Connexionbdd::query($sqlMaxOrdre)->fetchAll(\PDO::FETCH_ASSOC);
        $maxOrdre = (isset($maxOrdreResult[0]['max_ordre']) && $maxOrdreResult[0]['max_ordre'] !== null) ? $maxOrdreResult[0]['max_ordre'] : 0;
        $newOrdre = $maxOrdre + 1;
        $sql = "INSERT INTO $this->table (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre, editable, deletable) 
            VALUES ('360', 'CHAPTER', '$newLabel', '$newLabelAuto', '$newOrdre', 0, NULL, 1, 1)";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function createNewRadio360Text($label, $label_auto)
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
        $sql = "INSERT INTO $this->table (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre, editable, deletable) 
        VALUES ('360', 'INPUT-RADIO', '$newLabel', '$newLabelAuto', '$newOrdre', 1, '$newReportOrdre', 1, 1)";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function createNewRadio360List($labelNormal, $labelAutoEvaluated)
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
        $sql = "INSERT INTO $this->table (quiz_type, question_type, label, label_auto, ordre, response_required, report_ordre, editable, deletable) 
            VALUES ('360', 'INPUT-RADIO', '$newLabelNormal', '$newLabelAutoEvaluated', '$newOrdre', 1, '$newReportOrdre', 1, 1)";

        \Appy\Src\Connexionbdd::query($sql);
    }


    public function deleteQuestion360($QuestionId)
    {
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE id = '" . $QuestionId . "';";
            \Appy\Src\Connexionbdd::query($sql);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return true;
    }




}
