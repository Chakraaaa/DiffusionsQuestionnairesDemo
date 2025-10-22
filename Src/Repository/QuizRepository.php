<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\Quiz;

class QuizRepository extends \Appy\Src\Manager
{
    public $table = "quiz";

    public $champs = array(
        'Q.id',
        'Q.identifier',
        'Q.type',
        'Q.name',
        'Q.auto_user_id',
        'Q.auto_user_firstname',
        'Q.auto_user_lastname',
        'Q.auto_user_identifier',
        'Q.auto_user_email',
        'Q.created_at',
        'Q.logo',
        'Q.start_date',
        'Q.end_date',
        'Q.reminder_date',
        'Q.color_form',
        'Q.coef_tafv',
        'Q.coef_pv',
        'Q.coef_ppv',
        'Q.coef_pdtv',
        'Q.risque_de_sr',
        'Q.risque_de_pdr',
        'Q.risque_de_r',
        'Q.risque_de_fr',
        'Q.risque_a_sr',
        'Q.risque_a_pdr',
        'Q.risque_a_r',
        'Q.risque_a_fr',
        'Q.taux_de_sr',
        'Q.taux_de_pdr',
        'Q.taux_de_r',
        'Q.taux_de_fr',
        'Q.taux_a_sr',
        'Q.taux_a_pdr',
        'Q.taux_a_r',
        'Q.taux_a_fr',
        'Q.sexe_auto_user',
        'Q.header',
        'Q.intro',
        'Q.conclusion',
        'Q.footer',
        'Q.cc_p1_l1',
        'Q.cc_p1_l2',
        'Q.cc_p1_l3',
        'Q.cc_p1_l4',
        'Q.cc_p1_l5',
        'Q.cc_p2_l1',
        'Q.cc_p2_l2',
        'Q.cc_p2_l3',
        'Q.cc_p2_l4',
        'Q.cc_p2_l5',
        'Q.cc_p3_l1',
        'Q.cc_p3_l2',
        'Q.cc_p3_l3',
        'Q.cc_p3_l4',
        'Q.cc_p3_l5',
        'Q.groupe_id',
        'Q.fonction_auto_user',
        'Q.deleted',
        'Q.anonymous'
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $quizzes = array();

        foreach ($datas as $value) {
            $quiz = new Quiz();
            $quiz->id = $value['id'];
            $quiz->identifier = $value['identifier'];
            $quiz->name = $value['name'];
            $quiz->type = $value['type'];
            $quiz->autoUserId = $value['auto_user_id'];
            $quiz->autoUserFirstName = $value['auto_user_firstname'];
            $quiz->autoUserLastName = $value['auto_user_lastname'];
            $quiz->autoUserIdentifier = $value['auto_user_identifier'];
            $quiz->autoUserEmail = $value['auto_user_email'];
            $quiz->createdAt = $value['created_at'];
            $quiz->logo = $value['logo'];
            $quiz->startDate = $value['start_date'];
            $quiz->endDate = $value['end_date'];
            $quiz->reminderDate = $value['reminder_date'];
            $quiz->colorForm = $value['color_form'];
            $quiz->coefTafv = $value['coef_tafv'];
            $quiz->coefPv = $value['coef_pv'];
            $quiz->coefPpv = $value['coef_ppv'];
            $quiz->coefPdtv = $value['coef_pdtv'];
            $quiz->risqueDeSr = $value['risque_de_sr'];
            $quiz->risqueDePdr = $value['risque_de_pdr'];
            $quiz->risqueDeR = $value['risque_de_r'];
            $quiz->risqueDeFr = $value['risque_de_fr'];
            $quiz->risqueASr = $value['risque_a_sr'];
            $quiz->risqueAPdr = $value['risque_a_pdr'];
            $quiz->risqueAR = $value['risque_a_r'];
            $quiz->risqueAFr = $value['risque_a_fr'];
            $quiz->tauxDeSr = $value['taux_de_sr'];
            $quiz->tauxDePdr = $value['taux_de_pdr'];
            $quiz->tauxDeR = $value['taux_de_r'];
            $quiz->tauxDeFr = $value['taux_de_fr'];
            $quiz->tauxASr = $value['taux_a_sr'];
            $quiz->tauxAPdr = $value['taux_a_pdr'];
            $quiz->tauxAR = $value['taux_a_r'];
            $quiz->tauxAFr = $value['taux_a_fr'];
            $quiz->sexeAutoUser = $value['sexe_auto_user'];
            $quiz->header = $value['header'];
            $quiz->intro = $value['intro'];
            $quiz->conclusion = $value['conclusion'];
            $quiz->footer = $value['footer'];
            $quiz->ccP1L1 = $value['cc_p1_l1'];
            $quiz->ccP1L2 = $value['cc_p1_l2'];
            $quiz->ccP1L3 = $value['cc_p1_l3'];
            $quiz->ccP1L4 = $value['cc_p1_l4'];
            $quiz->ccP1L5 = $value['cc_p1_l5'];
            $quiz->ccP2L1 = $value['cc_p2_l1'];
            $quiz->ccP2L2 = $value['cc_p2_l2'];
            $quiz->ccP2L3 = $value['cc_p2_l3'];
            $quiz->ccP2L4 = $value['cc_p2_l4'];
            $quiz->ccP2L5 = $value['cc_p2_l5'];
            $quiz->ccP3L1 = $value['cc_p3_l1'];
            $quiz->ccP3L2 = $value['cc_p3_l2'];
            $quiz->ccP3L3 = $value['cc_p3_l3'];
            $quiz->ccP3L4 = $value['cc_p3_l4'];
            $quiz->ccP3L5 = $value['cc_p3_l5'];
            $quiz->groupeId = $value['groupe_id'];
            $quiz->fonctionAutoUser = $value['fonction_auto_user'];
            $quiz->deleted = $value['deleted'];
            $quiz->anonymous = $value['anonymous'];
            $quiz->nbUsers = $value['NB_REPONDANTS'];
            $quiz->nbReponses = $value['NB_RESPONSES_FINISH'];

            $quizzes[] = $quiz;
        }
        return $quizzes;
    }
    public function getQuizzes($criteres = NULL, $order = NULL)
    {
        try {
            $currentDate = (new \DateTime())->format('Y-m-d');

            $sql = "SELECT " . implode(",", $this->champs) . ", ";
            $sql .= "(SELECT COUNT(QU.id) FROM quiz_user QU WHERE QU.quiz_id = Q.id) AS 'NB_REPONDANTS', ";
            $sql .= "(SELECT COUNT(QU.id) FROM quiz_user QU WHERE QU.quiz_id = Q.id AND QU.status = 'FINISH') AS 'NB_RESPONSES_FINISH' ";
            $sql .= "FROM " . $this->table . " Q ";
            $sql .= "WHERE 1=1 ";

            if ($criteres) {
                foreach ($criteres as $key => $value) {
                    if ($key === 'id' && !empty($value)) {
                        $sql .= " AND Q.id = " . intval($value);
                    } elseif ($key === 'quizIdentifier' && !empty($value)) {
                        $sql .= " AND Q.identifier = '" . $value . "'";
                    } elseif ($key === 'date_end_not_passed' && !empty($value)) {
                        $sql .= " AND Q.end_date >= '" . $value . "'";
                    } elseif ($key === 'reminder_date' && !empty($value)) {
                        $sql .= " AND Q.reminder_date = '" . $value . "'";
                    } elseif ($key === 'deleted' && isset($value)) {
                        $sql .= " AND Q.deleted = " . intval($value);
                    } elseif ($key === 'anonymous') {
                        $sql .= " AND Q.anonymous = " . intval($value);
                    } elseif ($key === 'quiz-type' && $value != '') {
                        $sql .= " AND Q.type = '" . $value . "'";
                    }
                }
            }

            if ($order) {
                $sql .= " ORDER BY " . $order;
            } else {
                $sql .= " ORDER BY Q.created_at DESC";
            }
            //var_dump($sql);
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $quizzes = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $quizzes;
    }



    public function deleteQuiz($quizId)
    {
        $escapedQuizId = intval($quizId);
        $sql = "UPDATE " . $this->table . " SET deleted = 1 WHERE id = " . $escapedQuizId . ";";

        \Appy\Src\Connexionbdd::query($sql);
    }



    public function createQuiz($quiz)
    {
        $identifier = $quiz->identifier;

        $sql = "INSERT INTO " . $this->table . " (identifier, type, name, deleted, anonymous) ";
        $sql .= "VALUES ('" . $identifier . "','" . $quiz->type . "','" . $quiz->name . "',0,0)";

        \Appy\Src\Connexionbdd::query($sql);
        $id = \Appy\Src\Connexionbdd::lastInsertId();
        return $id;
    }

    public function updateResponse($questionId, $response, $quizUserId) {
        $escapedResponse = addslashes($response);
        $sql = "UPDATE quiz_user_response SET value = '" . $escapedResponse . "' WHERE question_id = " . $questionId . " AND quiz_user_id = " . $quizUserId . ";";

        \Appy\Src\Connexionbdd::query($sql);
    }


    public function updateUserStatus($quizUserId) {
        $sql = "UPDATE quiz_user SET status = 'PROGRESS' WHERE id = " .$quizUserId;
        \Appy\Src\Connexionbdd::query($sql);

    }
    public function UpdateOptionsQuiz(Quiz $quiz)
    {
        $fields = [];

        if (isset($quiz->name)) $fields[] = "name = '" . addslashes($quiz->name) . "'";
        if (isset($quiz->autoUserId)) $fields[] = "auto_user_id = '" . addslashes($quiz->autoUserId) . "'";
        if (isset($quiz->autoUserFirstName)) $fields[] = "auto_user_firstname = '" . addslashes($quiz->autoUserFirstName) . "'";
        if (isset($quiz->autoUserLastName)) $fields[] = "auto_user_lastname = '" . addslashes($quiz->autoUserLastName) . "'";
        if (isset($quiz->autoUserIdentifier)) $fields[] = "auto_user_identifier = '" . addslashes($quiz->autoUserIdentifier) . "'";
        if (isset($quiz->autoUserEmail)) $fields[] = "auto_user_email = '" . addslashes($quiz->autoUserEmail) . "'";
        if (isset($quiz->startDate)) $fields[] = "start_date = '" . addslashes($quiz->startDate) . "'";
        if (isset($quiz->endDate)) $fields[] = "end_date = '" . addslashes($quiz->endDate) . "'";
        if (isset($quiz->reminderDate)) $fields[] = "reminder_date = '" . addslashes($quiz->reminderDate) . "'";
        if (isset($quiz->colorForm)) $fields[] = "color_form = '" . addslashes($quiz->colorForm) . "'";
        if (isset($quiz->sexeAutoUser)) $fields[] = "sexe_auto_user = '" . addslashes($quiz->sexeAutoUser) . "'";
        if (isset($quiz->header)) $fields[] = "header = '" . addslashes($quiz->header) . "'";
        if (isset($quiz->intro)) $fields[] = "intro = '" . addslashes($quiz->intro) . "'";
        if (isset($quiz->conclusion)) $fields[] = "conclusion = '" . addslashes($quiz->conclusion) . "'";
        if (isset($quiz->footer)) $fields[] = "footer = '" . addslashes($quiz->footer) . "'";
        if (isset($quiz->ccP1L1)) $fields[] = "cc_p1_l1 = '" . addslashes($quiz->ccP1L1) . "'";
        if (isset($quiz->ccP1L2)) $fields[] = "cc_p1_l2 = '" . addslashes($quiz->ccP1L2) . "'";
        if (isset($quiz->ccP1L3)) $fields[] = "cc_p1_l3 = '" . addslashes($quiz->ccP1L3) . "'";
        if (isset($quiz->ccP1L4)) $fields[] = "cc_p1_l4 = '" . addslashes($quiz->ccP1L4) . "'";
        if (isset($quiz->ccP1L5)) $fields[] = "cc_p1_l5 = '" . addslashes($quiz->ccP1L5) . "'";
        if (isset($quiz->ccP2L1)) $fields[] = "cc_p2_l1 = '" . addslashes($quiz->ccP2L1) . "'";
        if (isset($quiz->ccP2L2)) $fields[] = "cc_p2_l2 = '" . addslashes($quiz->ccP2L2) . "'";
        if (isset($quiz->ccP2L3)) $fields[] = "cc_p2_l3 = '" . addslashes($quiz->ccP2L3) . "'";
        if (isset($quiz->ccP2L4)) $fields[] = "cc_p2_l4 = '" . addslashes($quiz->ccP2L4) . "'";
        if (isset($quiz->ccP2L5)) $fields[] = "cc_p2_l5 = '" . addslashes($quiz->ccP2L5) . "'";
        if (isset($quiz->ccP3L1)) $fields[] = "cc_p3_l1 = '" . addslashes($quiz->ccP3L1) . "'";
        if (isset($quiz->ccP3L2)) $fields[] = "cc_p3_l2 = '" . addslashes($quiz->ccP3L2) . "'";
        if (isset($quiz->ccP3L3)) $fields[] = "cc_p3_l3 = '" . addslashes($quiz->ccP3L3) . "'";
        if (isset($quiz->ccP3L4)) $fields[] = "cc_p3_l4 = '" . addslashes($quiz->ccP3L4) . "'";
        if (isset($quiz->ccP3L5)) $fields[] = "cc_p3_l5 = '" . addslashes($quiz->ccP3L5) . "'";
        if (isset($quiz->groupeId) && $quiz->groupeId != "") $fields[] = "groupe_id = " . addslashes($quiz->groupeId) ;
        if (isset($quiz->fonctionAutoUser)) $fields[] = "fonction_auto_user = '" . addslashes($quiz->fonctionAutoUser) . "'";
        if (isset($quiz->logo)) $fields[] = "logo = '" . addslashes($quiz->logo) . "'";
        if (isset($quiz->anonymous)) $fields[] = "anonymous = " . (int)$quiz->anonymous;
        if (isset($quiz->coef_tafv)) $fields[] = "coef_tafv = '" . addslashes($quiz->coef_tafv) . "'";
        if (isset($quiz->coef_pv)) $fields[] = "coef_pv = '" . addslashes($quiz->coef_pv) . "'";
        if (isset($quiz->coef_ppv)) $fields[] = "coef_ppv = '" . addslashes($quiz->coef_ppv) . "'";
        if (isset($quiz->coef_pdtv)) $fields[] = "coef_pdtv = '" . addslashes($quiz->coef_pdtv) . "'";
        if (isset($quiz->risque_de_sr)) $fields[] = "risque_de_sr = '" . addslashes($quiz->risque_de_sr) . "'";
        if (isset($quiz->risque_a_sr)) $fields[] = "risque_a_sr = '" . addslashes($quiz->risque_a_sr) . "'";
        if (isset($quiz->risque_de_pdr)) $fields[] = "risque_de_pdr = '" . addslashes($quiz->risque_de_pdr) . "'";
        if (isset($quiz->risque_a_pdr)) $fields[] = "risque_a_pdr = '" . addslashes($quiz->risque_a_pdr) . "'";
        if (isset($quiz->risque_de_r)) $fields[] = "risque_de_r = '" . addslashes($quiz->risque_de_r) . "'";
        if (isset($quiz->risque_a_r)) $fields[] = "risque_a_r = '" . addslashes($quiz->risque_a_r) . "'";
        if (isset($quiz->risque_de_fr)) $fields[] = "risque_de_fr = '" . addslashes($quiz->risque_de_fr) . "'";
        if (isset($quiz->risque_a_fr)) $fields[] = "risque_a_fr = '" . addslashes($quiz->risque_a_fr) . "'";
        if (isset($quiz->taux_de_sr)) $fields[] = "taux_de_sr = '" . addslashes($quiz->taux_de_sr) . "'";
        if (isset($quiz->taux_a_sr)) $fields[] = "taux_a_sr = '" . addslashes($quiz->taux_a_sr) . "'";
        if (isset($quiz->taux_de_pdr)) $fields[] = "taux_de_pdr = '" . addslashes($quiz->taux_de_pdr) . "'";
        if (isset($quiz->taux_a_pdr)) $fields[] = "taux_a_pdr = '" . addslashes($quiz->taux_a_pdr) . "'";
        if (isset($quiz->taux_de_r)) $fields[] = "taux_de_r = '" . addslashes($quiz->taux_de_r) . "'";
        if (isset($quiz->taux_a_r)) $fields[] = "taux_a_r = '" . addslashes($quiz->taux_a_r) . "'";
        if (isset($quiz->taux_de_fr)) $fields[] = "taux_de_fr = '" . addslashes($quiz->taux_de_fr) . "'";
        if (isset($quiz->taux_a_fr)) $fields[] = "taux_a_fr = '" . addslashes($quiz->taux_a_fr) . "'";

        $sql = "UPDATE $this->table SET " . implode(", ", $fields) . " WHERE id = '" . addslashes($quiz->id) . "'";

        \Appy\Src\Connexionbdd::query($sql);
    }

    public function initOptionsBarom() {

        $sql = "UPDATE $this->table SET coef_tafv = '0',";
        $sql .=                        "coef_pv = '1', ";
        $sql .=                        "coef_ppv = '2', ";
        $sql .=                        "coef_pdtv = '3', ";
        $sql .=                        "risque_de_sr = '0', ";
        $sql .=                        "risque_a_sr = '5', ";
        $sql .=                        "risque_de_pdr = '6', ";
        $sql .=                        "risque_a_pdr = '10', ";
        $sql .=                        "risque_de_r = '11', ";
        $sql .=                        "risque_a_r = '20', ";
        $sql .=                        "risque_de_fr = '21', ";
        $sql .=                        "risque_a_fr = '30', ";
        $sql .=                        "taux_de_sr = '0', ";
        $sql .=                        "taux_a_sr = '15', ";
        $sql .=                        "taux_de_pdr = '16', ";
        $sql .=                        "taux_a_pdr = '25', ";
        $sql .=                        "taux_de_r = '26', ";
        $sql .=                        "taux_a_r = '50', ";
        $sql .=                        "taux_de_fr = '51', ";
        $sql .=                        "taux_a_fr = '70' ";
        \Appy\Src\Connexionbdd::query($sql);
    }


    public function getQuizById($quizId) : Quiz
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs) . ', ';
            $sql.= " '0' AS 'NB_REPONDANTS', ";
            $sql.= " '0' AS 'NB_RESPONSES_FINISH' ";
            $sql.= " FROM " . $this->table . " AS Q WHERE id = " . $quizId . ";";

            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $quizzes = $this->arrayToEntity($datas);
            $quiz = array_shift($quizzes);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $quiz;
    }

    public function insertQuizOptionsByQuizId($quizId, $quizOptions)
    {
        $sql = "UPDATE " . $this->table . " SET ";
        $sql .= "color_form = '" . $quizOptions->colorForm . "', ";
        $sql .= "header = '" . addslashes($quizOptions->header) . "', ";
        $sql .= "intro = '" . addslashes($quizOptions->intro) . "', ";
        $sql .= "conclusion = '" . addslashes($quizOptions->conclusion) . "', ";
        $sql .= "footer = '" . addslashes($quizOptions->footer) . "', ";
        $sql .= "cc_p1_l1 = '" . addslashes($quizOptions->ccP1L1) . "', ";
        $sql .= "cc_p1_l2 = '" . addslashes($quizOptions->ccP1L2) . "', ";
        $sql .= "cc_p1_l3 = '" . addslashes($quizOptions->ccP1L3) . "', ";
        $sql .= "cc_p1_l4 = '" . addslashes($quizOptions->ccP1L4) . "', ";
        $sql .= "cc_p1_l5 = '" . addslashes($quizOptions->ccP1L5) . "', ";
        $sql .= "cc_p2_l1 = '" . addslashes($quizOptions->ccP2L1) . "', ";
        $sql .= "cc_p2_l2 = '" . addslashes($quizOptions->ccP2L2) . "', ";
        $sql .= "cc_p2_l3 = '" . addslashes($quizOptions->ccP2L3) . "', ";
        $sql .= "cc_p2_l4 = '" . addslashes($quizOptions->ccP2L4) . "', ";
        $sql .= "cc_p2_l5 = '" . addslashes($quizOptions->ccP2L5) . "', ";
        $sql .= "cc_p3_l1 = '" . addslashes($quizOptions->ccP3L1) . "', ";
        $sql .= "cc_p3_l2 = '" . addslashes($quizOptions->ccP3L2) . "', ";
        $sql .= "cc_p3_l3 = '" . addslashes($quizOptions->ccP3L3) . "', ";
        $sql .= "cc_p3_l4 = '" . addslashes($quizOptions->ccP3L4) . "', ";
        $sql .= "cc_p3_l5 = '" . addslashes($quizOptions->ccP3L5) . "' ";
        $sql .= "WHERE id = " . $quizId . ";";
        //var_dump($sql);
        \Appy\Src\Connexionbdd::query($sql);
    }
}
