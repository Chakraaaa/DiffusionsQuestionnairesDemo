<?php

namespace Appy\Src\Repository;

use Appy\Src\Manager;
use Appy\Src\Entity\QuizCriteresBarometre;

class QuizCriteresBarometreRepository extends Manager
{
    public $table = "quiz_criteres_barometre";

    public $champs = array(
        'QCB.id',
        'QCB.quiz_id',
        'QCB.critere1_titre', 'QCB.critere1_choix1', 'QCB.critere1_choix2', 'QCB.critere1_choix3', 'QCB.critere1_choix4', 'QCB.critere1_choix5', 'QCB.critere1_choix6', 'QCB.critere1_choix7', 'QCB.critere1_choix8', 'QCB.critere1_choix9', 'QCB.critere1_choix10',
        'QCB.critere2_titre', 'QCB.critere2_choix1', 'QCB.critere2_choix2', 'QCB.critere2_choix3', 'QCB.critere2_choix4', 'QCB.critere2_choix5', 'QCB.critere2_choix6', 'QCB.critere2_choix7', 'QCB.critere2_choix8', 'QCB.critere2_choix9', 'QCB.critere2_choix10',
        'QCB.critere3_titre', 'QCB.critere3_choix1', 'QCB.critere3_choix2', 'QCB.critere3_choix3', 'QCB.critere3_choix4', 'QCB.critere3_choix5', 'QCB.critere3_choix6', 'QCB.critere3_choix7', 'QCB.critere3_choix8', 'QCB.critere3_choix9', 'QCB.critere3_choix10',
        'QCB.critere4_titre', 'QCB.critere4_choix1', 'QCB.critere4_choix2', 'QCB.critere4_choix3', 'QCB.critere4_choix4', 'QCB.critere4_choix5', 'QCB.critere4_choix6', 'QCB.critere4_choix7', 'QCB.critere4_choix8', 'QCB.critere4_choix9', 'QCB.critere4_choix10',
    );

    public $champsInsert = array(
        'quiz_id',
        'critere1_titre', 'critere1_choix1', 'critere1_choix2', 'critere1_choix3', 'critere1_choix4', 'critere1_choix5', 'critere1_choix6', 'critere1_choix7', 'critere1_choix8', 'critere1_choix9', 'critere1_choix10',
        'critere2_titre', 'critere2_choix1', 'critere2_choix2', 'critere2_choix3', 'critere2_choix4', 'critere2_choix5', 'critere2_choix6', 'critere2_choix7', 'critere2_choix8', 'critere2_choix9', 'critere2_choix10',
        'critere3_titre', 'critere3_choix1', 'critere3_choix2', 'critere3_choix3', 'critere3_choix4', 'critere3_choix5', 'critere3_choix6', 'critere3_choix7', 'critere3_choix8', 'critere3_choix9', 'critere3_choix10',
        'critere4_titre', 'critere4_choix1', 'critere4_choix2', 'critere4_choix3', 'critere4_choix4', 'critere4_choix5', 'critere4_choix6', 'critere4_choix7', 'critere4_choix8', 'critere4_choix9', 'critere4_choix10'
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $criteres = array();
        foreach ($datas as $key => $value) {
            $criteres['id'] = $value['id'];
            if (!is_null($value['critere1_titre']) && !is_null($value['critere1_choix1'])) {
                $critere1 = new QuizCriteresBarometre();
                $critere1->titre = $value['critere1_titre'];
                $critere1->choix1 = $value['critere1_choix1'];
                $critere1->choix2 = $value['critere1_choix2'];
                $critere1->choix3 = $value['critere1_choix3'];
                $critere1->choix4 = $value['critere1_choix4'];
                $critere1->choix5 = $value['critere1_choix5'];
                $critere1->choix6 = $value['critere1_choix6'];
                $critere1->choix7 = $value['critere1_choix7'];
                $critere1->choix8 = $value['critere1_choix8'];
                $critere1->choix9 = $value['critere1_choix9'];
                $critere1->choix10 = $value['critere1_choix10'];
                $criteres[1] = $critere1;
            }

            if (!is_null($value['critere2_titre']) && !is_null($value['critere2_choix1'])) {
                $critere2 = new QuizCriteresBarometre();
                $critere2->titre = $value['critere2_titre'];
                $critere2->choix1 = $value['critere2_choix1'];
                $critere2->choix2 = $value['critere2_choix2'];
                $critere2->choix3 = $value['critere2_choix3'];
                $critere2->choix4 = $value['critere2_choix4'];
                $critere2->choix5 = $value['critere2_choix5'];
                $critere2->choix6 = $value['critere2_choix6'];
                $critere2->choix7 = $value['critere2_choix7'];
                $critere2->choix8 = $value['critere2_choix8'];
                $critere2->choix9 = $value['critere2_choix9'];
                $critere2->choix10 = $value['critere2_choix10'];
                $criteres[2] = $critere2;
            }

            if (!is_null($value['critere3_titre']) && !is_null($value['critere3_choix1'])) {
                $critere3 = new QuizCriteresBarometre();
                $critere3->titre = $value['critere3_titre'];
                $critere3->choix1 = $value['critere3_choix1'];
                $critere3->choix2 = $value['critere3_choix2'];
                $critere3->choix3 = $value['critere3_choix3'];
                $critere3->choix4 = $value['critere3_choix4'];
                $critere3->choix5 = $value['critere3_choix5'];
                $critere3->choix6 = $value['critere3_choix6'];
                $critere3->choix7 = $value['critere3_choix7'];
                $critere3->choix8 = $value['critere3_choix8'];
                $critere3->choix9 = $value['critere3_choix9'];
                $critere3->choix10 = $value['critere3_choix10'];
                $criteres[3] = $critere3;
            }

            if (!is_null($value['critere4_titre']) && !is_null($value['critere4_choix1'])) {
                $critere4 = new QuizCriteresBarometre();
                $critere4->titre = $value['critere4_titre'];
                $critere4->choix1 = $value['critere4_choix1'];
                $critere4->choix2 = $value['critere4_choix2'];
                $critere4->choix3 = $value['critere4_choix3'];
                $critere4->choix4 = $value['critere4_choix4'];
                $critere4->choix5 = $value['critere4_choix5'];
                $critere4->choix6 = $value['critere4_choix6'];
                $critere4->choix7 = $value['critere4_choix7'];
                $critere4->choix8 = $value['critere4_choix8'];
                $critere4->choix9 = $value['critere4_choix9'];
                $critere4->choix10 = $value['critere4_choix10'];
                $criteres[4] = $critere4;
            }
        }

        return $criteres;
    }

    public function createQuizCriteresBarometre($quizId)
    {
        $insertFields = implode(", ", $this->champsInsert);
        $selectFields = implode(", ", array_map(function($field) {
            return "template_" . $field;
        }, array_slice($this->champsInsert, 1))); // On ignore quiz_id pour le template

        $sql = "INSERT INTO " . $this->table . " ($insertFields) ";
        $sql .= "SELECT " . $quizId . " AS quiz_id, " . $selectFields . " ";
        $sql .= "FROM template_quiz_criteres_barometre";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function getCriteresByQuizId($quizId){
        $sql = "SELECT " . implode(",", $this->champs);
        $sql .= " FROM " . $this->table . " AS QCB";
        $sql .= " WHERE quiz_id = $quizId";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $criteres = $this->arrayToEntity($datas);
        return $criteres;

    }
    public function getNbCriteresByQuizId($quizId)
    {
        $sql = "SELECT (";
        $sql .= "   (critere1_titre IS NOT NULL AND critere1_titre <> '') + ";
        $sql .= "   (critere2_titre IS NOT NULL AND critere2_titre <> '') + ";
        $sql .= "   (critere3_titre IS NOT NULL AND critere3_titre <> '') + ";
        $sql .= "   (critere4_titre IS NOT NULL AND critere4_titre <> '') ";
        $sql .= ") AS nb_criteres";
        $sql .= " FROM " . $this->table . " AS QCB";
        $sql .= " WHERE quiz_id = $quizId";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetch(\PDO::FETCH_ASSOC);
        
        // Vérifier si la requête a retourné des résultats
        if ($datas === false || !isset($datas['nb_criteres'])) {
            return 0;
        }
        
        return $datas['nb_criteres'];
    }


    public function getIdByQuizId($quizId){
        $sql = "SELECT " . "QCB.id";
        $sql .= " FROM " . $this->table . " AS QCB";
        $sql .= " WHERE quiz_id = $quizId";
        $id = \Appy\Src\Connexionbdd::query($sql)->fetchColumn();
        return $id;
    }

    public function updateQuizCriteresBarometre($quizId, $criteres)
    {
        $updates = [];
        foreach ($criteres as $index => $critere) {
            $critereIndex = $index + 1;

            $updates[] = "critere{$critereIndex}_titre = " . $this->quote($critere->titre);
            $updates[] = "critere{$critereIndex}_choix1 = " . $this->quote($critere->choix1);
            $updates[] = "critere{$critereIndex}_choix2 = " . $this->quote($critere->choix2);
            $updates[] = "critere{$critereIndex}_choix3 = " . $this->quote($critere->choix3);
            $updates[] = "critere{$critereIndex}_choix4 = " . $this->quote($critere->choix4);
            $updates[] = "critere{$critereIndex}_choix5 = " . $this->quote($critere->choix5);
            $updates[] = "critere{$critereIndex}_choix6 = " . $this->quote($critere->choix6);
            $updates[] = "critere{$critereIndex}_choix7 = " . $this->quote($critere->choix7);
            $updates[] = "critere{$critereIndex}_choix8 = " . $this->quote($critere->choix8);
            $updates[] = "critere{$critereIndex}_choix9 = " . $this->quote($critere->choix9);
            $updates[] = "critere{$critereIndex}_choix10 = " . $this->quote($critere->choix10);
        }

        $sql = "UPDATE " . $this->table . " SET " . implode(", ", $updates) . " WHERE quiz_id = " . $this->quote($quizId);
        \Appy\Src\Connexionbdd::query($sql);
    }


    // Méthode pour échapper et ajouter des quotes à une valeur
    private function quote($value)
    {
        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
    }

}
