<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\QuizReportBarometre;
use Appy\Src\Manager;
use Appy\Src\Entity\QuizCriteresBarometre;

class QuizReportBarometreRepository extends Manager
{
    public $table = "quiz_report_barometre";

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $report = null;

        foreach ($datas as $key => $value) {
            $report = new QuizReportBarometre();
            $report->id = $value['id'];
            $report->quizId = $value['quiz_id'];

            for ($chapterId = 1; $chapterId <= 10; $chapterId++) {
                $report->{'C' . $chapterId . 'Q1'} = $value['C' . $chapterId . 'Q1'];
                $report->{'C' . $chapterId . 'Q2'} = $value['C' . $chapterId . 'Q2'];
                $report->{'C' . $chapterId . 'Q3'} = $value['C' . $chapterId . 'Q3'];
                $report->{'C' . $chapterId . 'Q4'} = $value['C' . $chapterId . 'Q4'];
                $report->{'C' . $chapterId . 'Q5'} = $value['C' . $chapterId . 'Q5'];
                $report->{'C' . $chapterId . 'Q6'} = $value['C' . $chapterId . 'Q6'];
                $report->{'C' . $chapterId . 'Q7'} = $value['C' . $chapterId . 'Q7'];
                $report->{'C' . $chapterId . 'Q8'} = $value['C' . $chapterId . 'Q8'];
                $report->{'C' . $chapterId . 'Q9'} = $value['C' . $chapterId . 'Q9'];
                $report->{'C' . $chapterId . 'Q10'} = $value['C' . $chapterId . 'Q10'];
            }

            for ($chapterId = 1; $chapterId <= 10; $chapterId++) {
                $report->{'C' . $chapterId . 'C1Coef'} = $value['C' . $chapterId . 'C1_coef'];
                $report->{'C' . $chapterId . 'C2Coef'} = $value['C' . $chapterId . 'C2_coef'];
                $report->{'C' . $chapterId . 'C3Coef'} = $value['C' . $chapterId . 'C3_coef'];
                $report->{'C' . $chapterId . 'C4Coef'} = $value['C' . $chapterId . 'C4_coef'];
            }

            for ($chapterId = 1; $chapterId <= 10; $chapterId++) {
                $report->{'C' . $chapterId . 'C1Risque'} = $value['C' . $chapterId . 'C1_risque'];
                $report->{'C' . $chapterId . 'C2Risque'} = $value['C' . $chapterId . 'C2_risque'];
                $report->{'C' . $chapterId . 'C3Risque'} = $value['C' . $chapterId . 'C3_risque'];
                $report->{'C' . $chapterId . 'C4Risque'} = $value['C' . $chapterId . 'C4_risque'];
            }

            $report->C1Expo= $value['C1_expo'];
            $report->C2Expo= $value['C2_expo'];
            $report->C3Expo= $value['C3_expo'];
            $report->C4Expo= $value['C4_expo'];
        }

        return $report;
    }

    public function getReportBarometreByQuizId($quizId) : QuizReportBarometre {
        $sql = "SELECT QRB.* ";
        $sql .= " FROM " . $this->table . " QRB ";
        $sql .= " WHERE QRB.quiz_id = $quizId";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $report = $this->arrayToEntity($datas);
        return $report;
    }

    public function initReportBarometre($quizId) {
        $sql = "INSERT INTO quiz_report_barometre (quiz_id, C1Q1) ";
        $sql .= "VALUES (" . $quizId . ", 'OUI') ";
        \Appy\Src\Connexionbdd::query($sql);
    }
    public function updateQuizReport($quizId, $POST, $chapterNumber, $critereNumber) {
        $sql = "UPDATE quiz_report_barometre ";
        $sql .= "SET ";
        for ($chapterId = 1; $chapterId <= $chapterNumber; $chapterId++) {
            $sql .= " C".$chapterId."Q1 = '" . $POST['C'.$chapterId.'Q1'] . "',";
            $sql .= " C".$chapterId."Q2 = '" . $POST['C'.$chapterId.'Q2'] . "',";
            $sql .= " C".$chapterId."Q3 = '" . $POST['C'.$chapterId.'Q3'] . "',";
            $sql .= " C".$chapterId."Q4 = '" . $POST['C'.$chapterId.'Q4'] . "',";
            $sql .= " C".$chapterId."Q5 = '" . $POST['C'.$chapterId.'Q5'] . "',";
            $sql .= " C".$chapterId."Q6 = '" . $POST['C'.$chapterId.'Q6'] . "',";
            $sql .= " C".$chapterId."Q7 = '" . $POST['C'.$chapterId.'Q7'] . "',";
            $sql .= " C".$chapterId."Q8 = '" . $POST['C'.$chapterId.'Q8'] . "',";
            $sql .= " C".$chapterId."Q9 = '" . $POST['C'.$chapterId.'Q9'] . "',";
            $sql .= " C".$chapterId."Q10 = '" . $POST['C'.$chapterId.'Q10'] . "',";
        }
        for ($chapterId = 1; $chapterId <= $chapterNumber; $chapterId++) {
            for ($critereId = 1; $critereId <= $critereNumber; $critereId++) {
                $sql .= " C".$chapterId."C".$critereId."_coef = '" . $POST['C'.$chapterId.'C'.$critereId.'Coef'] . "',";
                $sql .= " C".$chapterId."C".$critereId."_risque = '" . $POST['C'.$chapterId.'C'.$critereId.'Risque'] . "',";
            }
        }
        for ($critereId = 1; $critereId <= $critereNumber; $critereId++) {
            $sql .= " C".$critereId."_expo = '" . $POST['C'.$critereId.'Expo'] . "',";
        }
        //On retir la dernière vigure
        $sql = substr($sql, 0, -1);

        $sql .= " WHERE quiz_id = $quizId";
        var_dump($sql);
        \Appy\Src\Connexionbdd::query($sql);
    }

}
