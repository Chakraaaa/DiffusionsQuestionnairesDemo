<?php

namespace Appy\Src\Controller;

use Appy\Src\Core\Appy;
use Appy\Src\Entity\Quiz;
use Appy\Src\Repository\GroupesRepository;
use Appy\Src\Repository\QuizUserResponseRepository;
use Appy\Src\Repository\UsersRepository;
use Appy\Src\Repository\QuizRepository;
use Appy\Src\Repository\QuizUserRepository;
use Appy\Src\Repository\TemplateQuizQuestionsRepository;
use Appy\Src\Repository\QuizQuestionRepository;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Language;

class Report360Controller extends \Appy\Src\Core\Controller
{
    public function data()
    {
        $quizId = $_GET['quizId'];

        $quizRepository = new QuizRepository();
        $quizUserResponseRepository = new QuizUserResponseRepository();

        //On recupere les infos du quiz
        $quiz = $quizRepository->getQuizById($quizId);

        //On recupere les reponse des user
        $critereRecherche = [];
        $critereRecherche['quizId'] = $quizId;
        $critereRecherche['responseRequired'] = 1;

        $usersResponses = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "userReport");

        $arrayUserReponse = [];
        $arrayReponses = [];
        $i = 0;

        if($usersResponses) {
            //Pour chaque user on construit un tableau avec les reponse aux questions
            $userId = $usersResponses[0]->quizUserId;
            foreach ($usersResponses as $userResponses) {
                //Si on change de user
                // on sauvegarde le tableau des reponses pour le user
                //on remet à 0 le compteur de recuperation des questions et le tableau de reponse
                if($userId != $userResponses->quizUserId) {
                    $arrayUserReponse[$userId] = $arrayReponses;
                    $i = 0;
                    $arrayReponses = [];
                }

                $arrayReponses[$i] = $userResponses->value;

                $userId = $userResponses->quizUserId;
                $i++;
            }

            $arrayUserReponse[$userResponses->quizUserId] = $arrayReponses;

        }



        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);
        //On parametre par defaut en mode paysage
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // format impression A4
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // Ajuster toutes les colonnes à une page
        //$sheet->getPageSetup()->setFitToWidth(1);
        //$sheet->getPageSetup()->setFitToHeight(0);

        //On sauvegarde le nombre de user
        $nbUser = $i;

        $sheet->getDefaultColumnDimension()->setWidth(4);
        $sheet->getStyle("A")->getAlignment()->setHorizontal('left');
        $sheet->getStyle("A")->getAlignment()->setVertical('center');
        $sheet->getStyle("A")->getFont()->setSize(11);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getStyle("A1:BX1")->getAlignment()->setHorizontal('left');
        $sheet->getStyle("A1:BX1")->getAlignment()->setVertical('center');
        $sheet->getStyle("A1:BX1")->getFont()->setSize(9);
        $sheet->getStyle("C2:BX500")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C2:BX500")->getAlignment()->setVertical('center');
        $sheet->getStyle("C2:BX500")->getFont()->setSize(11);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('L')->setWidth(6);
        $sheet->getColumnDimension('R')->setWidth(6);
        $sheet->getColumnDimension('X')->setWidth(6);
        $sheet->getColumnDimension('AP')->setWidth(6);
        $sheet->getStyle("A2:BX2")->getFont()->setBold(true);
        $sheet->getStyle('A4:BX4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E8EDF2');

        $i = 1;

        //On recupere le libellé des chapitres
        $quizQuestionRepository = new QuizQuestionRepository();
        $chapter = $quizQuestionRepository->getChapterByQuizIdAndOrder($quizId, 4);
        $sheet->setCellValueByColumnAndRow(5, $i, $this->formatChapterForExcel($chapter->label));
        $chapter = $quizQuestionRepository->getChapterByQuizIdAndOrder($quizId, 12);
        $sheet->setCellValueByColumnAndRow(13, $i, $this->formatChapterForExcel($chapter->label));
        $chapter = $quizQuestionRepository->getChapterByQuizIdAndOrder($quizId, 18);
        $sheet->setCellValueByColumnAndRow(19, $i, $this->formatChapterForExcel($chapter->label));
        $chapter = $quizQuestionRepository->getChapterByQuizIdAndOrder($quizId, 24);
        $sheet->setCellValueByColumnAndRow(25, $i, $this->formatChapterForExcel($chapter->label));
        $chapter = $quizQuestionRepository->getChapterByQuizIdAndOrder($quizId, 42);
        $sheet->setCellValueByColumnAndRow(43, $i, $this->formatChapterForExcel($chapter->label));
        $i++;

        $sheet->setCellValueByColumnAndRow(1, $i, 'Email / Identifiant');
        $sheet->setCellValueByColumnAndRow(3, $i, 'Position');

        $sheet->setCellValueByColumnAndRow(5, $i, "Q1");
        $sheet->setCellValueByColumnAndRow(6, $i, "Q2");
        $sheet->setCellValueByColumnAndRow(7, $i, "Q3");
        $sheet->setCellValueByColumnAndRow(8, $i, "Q4");
        $sheet->setCellValueByColumnAndRow(9, $i, "Q5");
        $sheet->setCellValueByColumnAndRow(10, $i, "Q6");
        $sheet->setCellValueByColumnAndRow(11, $i, "Q7");

        $sheet->setCellValueByColumnAndRow(13, $i, "Q1");
        $sheet->setCellValueByColumnAndRow(14, $i, "Q2");
        $sheet->setCellValueByColumnAndRow(15, $i, "Q3");
        $sheet->setCellValueByColumnAndRow(16, $i, "Q4");
        $sheet->setCellValueByColumnAndRow(17, $i, "Q5");

        $sheet->setCellValueByColumnAndRow(19, $i, "Q1");
        $sheet->setCellValueByColumnAndRow(20, $i, "Q2");
        $sheet->setCellValueByColumnAndRow(21, $i, "Q3");
        $sheet->setCellValueByColumnAndRow(22, $i, "Q4");
        $sheet->setCellValueByColumnAndRow(23, $i, "Q5");

        $sheet->setCellValueByColumnAndRow(25, $i, "Q1");
        $sheet->setCellValueByColumnAndRow(26, $i, "Q2");
        $sheet->setCellValueByColumnAndRow(27, $i, "Q3");
        $sheet->setCellValueByColumnAndRow(28, $i, "Q4");
        $sheet->setCellValueByColumnAndRow(29, $i, "Q5");
        $sheet->setCellValueByColumnAndRow(30, $i, "Q6");
        $sheet->setCellValueByColumnAndRow(31, $i, "Q7");
        $sheet->setCellValueByColumnAndRow(32, $i, "Q8");
        $sheet->setCellValueByColumnAndRow(33, $i, "Q9");
        $sheet->setCellValueByColumnAndRow(34, $i, "Q10");
        $sheet->setCellValueByColumnAndRow(35, $i, "Q11");
        $sheet->setCellValueByColumnAndRow(36, $i, "Q12");
        $sheet->setCellValueByColumnAndRow(37, $i, "Q13");
        $sheet->setCellValueByColumnAndRow(38, $i, "Q14");
        $sheet->setCellValueByColumnAndRow(39, $i, "Q15");
        $sheet->setCellValueByColumnAndRow(40, $i, "Q16");
        $sheet->setCellValueByColumnAndRow(41, $i, "Q17");

        $sheet->setCellValueByColumnAndRow(43, $i, "Q1");
        $sheet->setCellValueByColumnAndRow(44, $i, "Q2");
        $sheet->setCellValueByColumnAndRow(45, $i, "Q3");
        $sheet->setCellValueByColumnAndRow(46, $i, "Q4");
        $sheet->setCellValueByColumnAndRow(47, $i, "Q5");
        $sheet->setCellValueByColumnAndRow(48, $i, "Q6");
        $sheet->setCellValueByColumnAndRow(49, $i, "Q7");
        $sheet->setCellValueByColumnAndRow(50, $i, "Q8");
        $sheet->setCellValueByColumnAndRow(51, $i, "Q9");
        $sheet->setCellValueByColumnAndRow(52, $i, "Q10");
        $sheet->setCellValueByColumnAndRow(53, $i, "Q11");

        $i = 6;

        foreach($arrayUserReponse as $key => $userReponse){
            //var_dump($userReponse[0]);
            //Recuperation des infos du user
            $quizUserRepository = new QuizUserRepository();
            $quizUser = $quizUserRepository->getQuizUserById($key);

            $numLign = $i;
            //Si c'est l'autoévalué on ecrit en ligne 4
            if($quizUser->auto == 1) {
                $numLign = 4;
            }

            //Pour l'autoevalué on  indique dans la position Autoévalé
            //On met une couleur de fond sur la ligne
            $position = $userReponse[0];
            if($quizUser->auto == 1) {
                $position = "Autoévalué";
            }

            //Si c'est un quiiz anonyme on affiche l'identifiant du user sinon son email
            $identiferOrEmil = "";
            if($quiz->anonymous == 1) {
                $identiferOrEmil = $quizUser->userIdentifier;
            } else {
                $identiferOrEmil = $quizUser->userEmail;
            }

            $sheet->setCellValueByColumnAndRow(1, $numLign, $identiferOrEmil);
            $sheet->setCellValueByColumnAndRow(3, $numLign, $position);

            $sheet->setCellValueByColumnAndRow(5, $numLign, $userReponse[1]);
            $sheet->setCellValueByColumnAndRow(6, $numLign, $userReponse[2]);
            $sheet->setCellValueByColumnAndRow(7, $numLign, $userReponse[3]);
            $sheet->setCellValueByColumnAndRow(8, $numLign, $userReponse[4]);
            $sheet->setCellValueByColumnAndRow(9, $numLign, $userReponse[5]);
            $sheet->setCellValueByColumnAndRow(10, $numLign, $userReponse[6]);
            $sheet->setCellValueByColumnAndRow(11, $numLign, $userReponse[7]);

            $sheet->setCellValueByColumnAndRow(13, $numLign, $userReponse[8]);
            $sheet->setCellValueByColumnAndRow(14, $numLign, $userReponse[9]);
            $sheet->setCellValueByColumnAndRow(15, $numLign, $userReponse[10]);
            $sheet->setCellValueByColumnAndRow(16, $numLign, $userReponse[11]);
            $sheet->setCellValueByColumnAndRow(17, $numLign, $userReponse[12]);

            $sheet->setCellValueByColumnAndRow(19, $numLign, $userReponse[13]);
            $sheet->setCellValueByColumnAndRow(20, $numLign, $userReponse[14]);
            $sheet->setCellValueByColumnAndRow(21, $numLign, $userReponse[15]);
            $sheet->setCellValueByColumnAndRow(22, $numLign, $userReponse[16]);
            $sheet->setCellValueByColumnAndRow(23, $numLign, $userReponse[17]);

            $sheet->setCellValueByColumnAndRow(25, $numLign, $userReponse[18]);
            $sheet->setCellValueByColumnAndRow(26, $numLign, $userReponse[19]);
            $sheet->setCellValueByColumnAndRow(27, $numLign, $userReponse[20]);
            $sheet->setCellValueByColumnAndRow(28, $numLign, $userReponse[21]);
            $sheet->setCellValueByColumnAndRow(29, $numLign, $userReponse[22]);
            $sheet->setCellValueByColumnAndRow(30, $numLign, $userReponse[23]);
            $sheet->setCellValueByColumnAndRow(31, $numLign, $userReponse[24]);
            $sheet->setCellValueByColumnAndRow(32, $numLign, $userReponse[25]);
            $sheet->setCellValueByColumnAndRow(33, $numLign, $userReponse[26]);
            $sheet->setCellValueByColumnAndRow(34, $numLign, $userReponse[27]);
            $sheet->setCellValueByColumnAndRow(35, $numLign, $userReponse[28]);
            $sheet->setCellValueByColumnAndRow(36, $numLign, $userReponse[29]);
            $sheet->setCellValueByColumnAndRow(37, $numLign, $userReponse[30]);
            $sheet->setCellValueByColumnAndRow(38, $numLign, $userReponse[31]);
            $sheet->setCellValueByColumnAndRow(39, $numLign, $userReponse[32]);
            $sheet->setCellValueByColumnAndRow(40, $numLign, $userReponse[33]);
            $sheet->setCellValueByColumnAndRow(41, $numLign, $userReponse[34]);

            $sheet->setCellValueByColumnAndRow(43, $numLign, $userReponse[35]);
            $sheet->setCellValueByColumnAndRow(44, $numLign, $userReponse[36]);
            $sheet->setCellValueByColumnAndRow(45, $numLign, $userReponse[37]);
            $sheet->setCellValueByColumnAndRow(46, $numLign, $userReponse[38]);
            $sheet->setCellValueByColumnAndRow(47, $numLign, $userReponse[39]);
            $sheet->setCellValueByColumnAndRow(48, $numLign, $userReponse[40]);
            $sheet->setCellValueByColumnAndRow(49, $numLign, $userReponse[41]);
            $sheet->setCellValueByColumnAndRow(50, $numLign, $userReponse[42]);
            $sheet->setCellValueByColumnAndRow(51, $numLign, $userReponse[43]);
            $sheet->setCellValueByColumnAndRow(52, $numLign, $userReponse[44]);
            $sheet->setCellValueByColumnAndRow(53, $numLign, $userReponse[45]);

            //On incrémenté la ligne que si ce n'est psa l'autoévalué
            if($quizUser->auto == 0) {
                $i++;
            }
        }

        $lastResponseLign = $i - 1;

        $i++;

        //On ajoute les calculs de moyenne
        $sheet->getStyle("E".$i.":BX".strval($i+5))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_0);
        $stringPosition = "";
        for ($k = 1; $k <= 3; $k++) {
            if($k == 1) $stringPosition = "Equipe";
            elseif($k == 2) $stringPosition = "Hierarchie";
            elseif($k == 3) $stringPosition = "Transverse";
            $sheet->setCellValueByColumnAndRow(3, $i, "Moy " . $stringPosition);
            $sheet->setCellValue("E" . $i, '=AVERAGEIFS(E6:E' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("F" . $i, '=AVERAGEIFS(F6:F' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("G" . $i, '=AVERAGEIFS(G6:G' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("H" . $i, '=AVERAGEIFS(H6:H' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("I" . $i, '=AVERAGEIFS(I6:I' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("J" . $i, '=AVERAGEIFS(J6:J' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("K" . $i, '=AVERAGEIFS(K6:K' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');

            $sheet->setCellValue("M" . $i, '=AVERAGEIFS(M6:M' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("N" . $i, '=AVERAGEIFS(N6:N' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("O" . $i, '=AVERAGEIFS(O6:O' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("P" . $i, '=AVERAGEIFS(P6:P' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("Q" . $i, '=AVERAGEIFS(Q6:Q' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');

            $sheet->setCellValue("S" . $i, '=AVERAGEIFS(S6:S' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("T" . $i, '=AVERAGEIFS(T6:T' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("U" . $i, '=AVERAGEIFS(U6:U' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("V" . $i, '=AVERAGEIFS(V6:V' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("W" . $i, '=AVERAGEIFS(W6:W' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');

            $sheet->setCellValue("Y" . $i, '=AVERAGEIFS(Y6:Y' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("Z" . $i, '=AVERAGEIFS(Z6:Z' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AA" . $i, '=AVERAGEIFS(AA6:AA' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AB" . $i, '=AVERAGEIFS(AB6:AB' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AC" . $i, '=AVERAGEIFS(AC6:AC' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AD" . $i, '=AVERAGEIFS(AD6:AD' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AE" . $i, '=AVERAGEIFS(AE6:AE' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AF" . $i, '=AVERAGEIFS(AF6:AF' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AG" . $i, '=AVERAGEIFS(AG6:AG' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AH" . $i, '=AVERAGEIFS(AH6:AH' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AI" . $i, '=AVERAGEIFS(AI6:AI' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AJ" . $i, '=AVERAGEIFS(AJ6:AJ' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AK" . $i, '=AVERAGEIFS(AK6:AK' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AL" . $i, '=AVERAGEIFS(AL6:AL' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AM" . $i, '=AVERAGEIFS(AM6:AM' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AN" . $i, '=AVERAGEIFS(AN6:AN' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AO" . $i, '=AVERAGEIFS(AO6:AO' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');

            $sheet->setCellValue("AQ" . $i, '=AVERAGEIFS(AQ6:AQ' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AR" . $i, '=AVERAGEIFS(AR6:AR' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AS" . $i, '=AVERAGEIFS(AS6:AS' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AT" . $i, '=AVERAGEIFS(AT6:AT' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AU" . $i, '=AVERAGEIFS(AU6:AU' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AV" . $i, '=AVERAGEIFS(AV6:AV' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AW" . $i, '=AVERAGEIFS(AW6:AW' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AX" . $i, '=AVERAGEIFS(AX6:AX' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AY" . $i, '=AVERAGEIFS(AY6:AY' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("AZ" . $i, '=AVERAGEIFS(AZ6:AZ' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $sheet->setCellValue("BA" . $i, '=AVERAGEIFS(BA6:BA' . $lastResponseLign . ',C6:C' . $lastResponseLign . ',"' . $stringPosition . '")');
            $i++;
        }

        $sheet->setCellValueByColumnAndRow(3, $i, "Moy Sans auto");
        $sheet->setCellValue("E" . $i, '=AVERAGE(E6:E' . $lastResponseLign . ')');
        $sheet->setCellValue("F" . $i, '=AVERAGE(F6:F' . $lastResponseLign . ')');
        $sheet->setCellValue("G" . $i, '=AVERAGE(G6:G' . $lastResponseLign . ')');
        $sheet->setCellValue("H" . $i, '=AVERAGE(H6:H' . $lastResponseLign . ')');
        $sheet->setCellValue("I" . $i, '=AVERAGE(I6:I' . $lastResponseLign . ')');
        $sheet->setCellValue("J" . $i, '=AVERAGE(J6:J' . $lastResponseLign . ')');
        $sheet->setCellValue("K" . $i, '=AVERAGE(K6:K' . $lastResponseLign . ')');

        $sheet->setCellValue("M" . $i, '=AVERAGE(M6:M' . $lastResponseLign . ')');
        $sheet->setCellValue("N" . $i, '=AVERAGE(N6:N' . $lastResponseLign . ')');
        $sheet->setCellValue("O" . $i, '=AVERAGE(O6:O' . $lastResponseLign . ')');
        $sheet->setCellValue("P" . $i, '=AVERAGE(P6:P' . $lastResponseLign . ')');
        $sheet->setCellValue("Q" . $i, '=AVERAGE(Q6:Q' . $lastResponseLign . ')');

        $sheet->setCellValue("S" . $i, '=AVERAGE(S6:S' . $lastResponseLign . ')');
        $sheet->setCellValue("T" . $i, '=AVERAGE(T6:T' . $lastResponseLign . ')');
        $sheet->setCellValue("U" . $i, '=AVERAGE(U6:U' . $lastResponseLign . ')');
        $sheet->setCellValue("V" . $i, '=AVERAGE(V6:V' . $lastResponseLign . ')');
        $sheet->setCellValue("W" . $i, '=AVERAGE(W6:W' . $lastResponseLign . ')');

        $sheet->setCellValue("Y" . $i, '=AVERAGE(Y6:Y' . $lastResponseLign . ')');
        $sheet->setCellValue("Z" . $i, '=AVERAGE(Z6:Z' . $lastResponseLign . ')');
        $sheet->setCellValue("AA" . $i, '=AVERAGE(AA6:AA' . $lastResponseLign . ')');
        $sheet->setCellValue("AB" . $i, '=AVERAGE(AB6:AB' . $lastResponseLign . ')');
        $sheet->setCellValue("AC" . $i, '=AVERAGE(AC6:AC' . $lastResponseLign . ')');
        $sheet->setCellValue("AD" . $i, '=AVERAGE(AD6:AD' . $lastResponseLign . ')');
        $sheet->setCellValue("AE" . $i, '=AVERAGE(AE6:AE' . $lastResponseLign . ')');
        $sheet->setCellValue("AF" . $i, '=AVERAGE(AF6:AF' . $lastResponseLign . ')');
        $sheet->setCellValue("AG" . $i, '=AVERAGE(AG6:AG' . $lastResponseLign . ')');
        $sheet->setCellValue("AH" . $i, '=AVERAGE(AH6:AH' . $lastResponseLign . ')');
        $sheet->setCellValue("AI" . $i, '=AVERAGE(AI6:AI' . $lastResponseLign . ')');
        $sheet->setCellValue("AJ" . $i, '=AVERAGE(AJ6:AJ' . $lastResponseLign . ')');
        $sheet->setCellValue("AK" . $i, '=AVERAGE(AK6:AK' . $lastResponseLign . ')');
        $sheet->setCellValue("AL" . $i, '=AVERAGE(AL6:AL' . $lastResponseLign . ')');
        $sheet->setCellValue("AM" . $i, '=AVERAGE(AM6:AM' . $lastResponseLign . ')');
        $sheet->setCellValue("AN" . $i, '=AVERAGE(AN6:AN' . $lastResponseLign . ')');
        $sheet->setCellValue("AO" . $i, '=AVERAGE(AO6:AO' . $lastResponseLign . ')');

        $sheet->setCellValue("AQ" . $i, '=AVERAGE(AQ6:AQ' . $lastResponseLign . ')');
        $sheet->setCellValue("AR" . $i, '=AVERAGE(AR6:AR' . $lastResponseLign . ')');
        $sheet->setCellValue("AS" . $i, '=AVERAGE(AS6:AS' . $lastResponseLign . ')');
        $sheet->setCellValue("AT" . $i, '=AVERAGE(AT6:AT' . $lastResponseLign . ')');
        $sheet->setCellValue("AU" . $i, '=AVERAGE(AU6:AU' . $lastResponseLign . ')');
        $sheet->setCellValue("AV" . $i, '=AVERAGE(AV6:AV' . $lastResponseLign . ')');
        $sheet->setCellValue("AW" . $i, '=AVERAGE(AW6:AW' . $lastResponseLign . ')');
        $sheet->setCellValue("AX" . $i, '=AVERAGE(AX6:AX' . $lastResponseLign . ')');
        $sheet->setCellValue("AY" . $i, '=AVERAGE(AY6:AY' . $lastResponseLign . ')');
        $sheet->setCellValue("AZ" . $i, '=AVERAGE(AZ6:AZ' . $lastResponseLign . ')');
        $sheet->setCellValue("BA" . $i, '=AVERAGE(BA6:BA' . $lastResponseLign . ')');

        $docName = "360-" . $quizId;
        $docName = str_replace(' ', '_', $docName);
        $docName = str_replace(',', '-', $docName);
        $docName .= '-' . date("d-m-Y-H-i-s") . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($docName);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($docName);

        $this->downloadFileExcel($docName, $docName);
    }

    public function formatChapterForExcel($Label) {
        $Label = str_replace('<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">', '', $Label);
        $Label = str_replace("<div style='font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold'>", '', $Label);
        $Label = str_replace('</div>', '', $Label);
        return $Label;
    }

    public function downloadFileExcel($fileName, $filePath)
    {
        header('Content-Description: File Transfer');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment; filename='.basename($fileName));
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Pragma: public");
        //header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Cache-Control: no cache');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);
        exit();
    }

    public function generate()
    {
        $quizId = $_GET['quizId'];
        $pageGarde = 0;
        if (isset($_GET['pagegarde'])) {
            $pageGarde = $_GET['pagegarde'];
        }

        $quizRepository = new QuizRepository();
        $userRepository = new UsersRepository();
        $quizQuestionRepository = new QuizQuestionRepository();
        $quizUserResponseRepository = new QuizUserResponseRepository();

        //On recupere les infos du quiz
        $quiz = $quizRepository->getQuizById($quizId);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::FR_FR));

        $phpWord->addParagraphStyle('StyleParagrapheSautDeLigne', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheText1FirstPage', ['align' => 'left', 'spaceBefore' => 50, 'spaceAfter' => 5000, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheText1SecondPage', ['align' => 'center', 'spaceBefore' => 600, 'spaceAfter' => 1000, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheText2SecondPage', ['align' => 'left', 'spaceBefore' => 1000, 'spaceAfter' => 1000, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheTitre1', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheTitre2', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheTitre4', ['name' => 'Trebuchet MS', 'align' => "left", 'spaceBefore' => 100, 'spaceAfter' => 10, 'indentation' => array('left' => 600, 'right' => 0)]);
        $phpWord->addParagraphStyle('StyleParagrapheText', ['name' => 'Trebuchet MS', 'align' => "left", 'spaceBefore' => 100, 'spaceAfter' => 10, 'indentation' => array('left' => 360, 'right' => 0)]);
        $phpWord->addParagraphStyle('StyleParagrapheTextDecale', ['name' => 'Trebuchet MS', 'align' => "left", 'spaceBefore' => 100, 'spaceAfter' => 10, 'indentation' => array('left' => 800, 'right' => 0)]);
        $phpWord->addParagraphStyle('StyleParagrapheFooterHaut', ['name' => 'Trebuchet MS', 'size' => 8, 'align' => "center", 'space' => array('before' => 470), 'indentation' => array('left' => 40, 'right' => 40)]);
        $phpWord->addParagraphStyle('StyleParagrapheFooterBas', ['name' => 'Trebuchet MS', 'size' => 8, 'align' => "center", 'space' => array('before' => 50), 'indentation' => array('left' => 120, 'right' => 120)]);
        $phpWord->addParagraphStyle('StyleParagrapheFooterTexte', ['name' => 'Trebuchet MS', 'align' => "center", 'spaceBefore' => 50, 'spaceAfter' => 50, 'spacing' => 50]);
        $phpWord->addParagraphStyle('StyleParagraphePageNumberFooter', ['align' => "right",'name' => 'Trebuchet MS', 'size' => 9, 'color' => '696252']);
        $phpWord->addParagraphStyle('StyleParagrapheCoche', ['align' => "center", 'spaceBefore' => 50, 'spaceAfter' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheLeftColle', ['align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheRightColle', ['align' => 'right']);
        $phpWord->addParagraphStyle('StyleParagrapheCenterAfterColle', ['align' => 'center', 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheList', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceBefore' => 100, 'spaceAfter' => 10]);
        $phpWord->addParagraphStyle('StyleParagrapheTabRecapNumber', ['name' => 'Trebuchet MS', 'align' => 'right', 'spaceBefore' => 10, 'spaceAfter' => 10]);
        $phpWord->addParagraphStyle('StyleParagrapheTabAnalyseNumber', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceBefore' => 10, 'spaceAfter' => 10]);
        $phpWord->addFontStyle('StyleTexte5White', ['name' => 'Trebuchet MS', 'size' => 5, 'color' => "FFFFFF", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte8', ['name' => 'Trebuchet MS', 'size' => 8, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte9', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte9Underline', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => "696252", 'underline' => 'single']);
        $phpWord->addFontStyle('StyleTexte9Orange', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => "ff8000"]);
        $phpWord->addFontStyle('StyleTexteChapter', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => 'E9660B', 'bold' => true, 'space' => array('before' => 10)]);
        $phpWord->addFontStyle('StyleTexte10', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte10Coche', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte11', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte11Colle', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'space' => array('before' => 5, 'after' => 5)]);
        $phpWord->addFontStyle('StyleTexte11Rouge', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B']);
        $phpWord->addFontStyle('StyleTexte11RougeMarginLeft', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B', 'indentation' => array('left' => 400, 'right' => 0)]);
        $phpWord->addFontStyle('StyleTexte11Colle10Before', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "000000", 'space' => array('before' => 10)]);
        $phpWord->addFontStyle('StyleTexte11Bold', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte11BoldUnderline', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'bold' => true, 'underline' => 'single']);
        $phpWord->addFontStyle('StyleTexte12', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte12RougeBold', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte13Bold', ['name' => 'Trebuchet MS', 'size' => 13, 'color' => "696252", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte14Rouge', ['name' => 'Trebuchet MS', 'size' => 14, 'color' => 'E9660B']);
        $phpWord->addFontStyle('StyleTexte14RougeBold', ['name' => 'Trebuchet MS', 'size' => 14, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte16Vert', ['name' => 'Trebuchet MS', 'size' => 16, 'color' => "92D050"]);
        $phpWord->addFontStyle('StyleTexte16Orange', ['name' => 'Trebuchet MS', 'size' => 16, 'color' => "F97407"]);
        $phpWord->addFontStyle('StyleTexte16Rouge', ['name' => 'Trebuchet MS', 'size' => 16, 'color' => "FF0000"]);
        $phpWord->addFontStyle('StyleTexte18RougeBold', ['name' => 'Trebuchet MS', 'size' => 18, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte20', ['name' => 'Trebuchet MS', 'size' => 20, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte20Rouge', ['name' => 'Trebuchet MS', 'size' => 20, 'color' => "E9660B"]);
        $phpWord->addFontStyle('StyleTexte28RougeBold', ['name' => 'Trebuchet MS', 'size' => 28, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('list1', array('name' => 'Trebuchet MS', 'size' => 11, 'color' => '696252'));
        $phpWord->addTableStyle('StyleTableFirstPage', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableFooter', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableFirstPage', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableRecap', ['borderSize' => 0, 'borderColor' => '000000', 'cellMarginLeft' => 150, 'cellMarginRight' => 150, 'cellMarginTop' => 100, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableAnalyse', ['borderSize' => 0, 'borderColor' => 'ffffff', 'cellMarginLeft' => 50, 'cellMarginRight' => 50, 'cellMarginTop' => 50, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $listStyle = array('listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_SQUARE_FILLED);
        $listStyleLetter = array('listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_ALPHANUM);
        $phpWord->addNumberingStyle(
            'list1',
            [
                'type' => 'singleLevel',
                'levels' => [
                    [
                        'start' => '1',
                        'format' => 'bullet',
                        'text' => '',
                        'alignment' => 'left',
                        'tabPos' => 360,
                        'left' => 360,
                        'hanging' => 360,
                        'font' => 'Symbol',
                        'hint' => 'default',
                        'color' => 'FF372F', // this doesnt seem to work
                    ],
                ],
            ]
        );

        $columnStyle = array('valign' => 'center');
        $columnStyleOrange = array('valign' => 'center', 'borderSize' => 20, 'borderColor' => 'ff8000');

        //PAGE DE GARDE
        if($pageGarde == 1) {
            //si on met des  marges sur la section se sont les marges de la page
            $sectionPageGarde = $phpWord->addSection(array('marginLeft' => 500, 'marginRight' => 500, 'marginTop' => 400, 'marginBottom' => 400));

            //HEADER
            //Tableau du header avec le logo uniquement sur la 1ere page
            $header = $sectionPageGarde->addHeader();
            $header->firstPage();
            $table = $header->addTable('StyleTableHeader');
            $table->addRow();
            $cell = $table->addCell(4000);
            $cell->addImage(BASE_PATH . "/assets/images/logo-rm-simple.png", array('height' => 50,'width' => 125, 'align' => 'left'));
            $cell = $table->addCell(6000);

            $this->addSautLigne($sectionPageGarde, 20);

            $textrun = $sectionPageGarde->addText("EVALUATION EN 360°", 'StyleTexte20', 'StyleParagrapheCenterAfterColle');

            $this->addSautLigne($sectionPageGarde, 5);

            $textrun = $sectionPageGarde->addText($quiz->autoUserFirstName . " " . $quiz->autoUserLastName, 'StyleTexte20Rouge', 'StyleParagrapheCenterAfterColle');

            $this->addSautLigne($sectionPageGarde, 5);

            $table = $sectionPageGarde->addTable('StyleTableHeader');
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addImage(BASE_PATH . "/assets/images/logo-rm-opacity.png", array('height' => 200, 'align' => 'center'));

            $textrun = $sectionPageGarde->addTextRun('StyleParagrapheRightColle');
            $now = new \DateTime();
            $textrun->addText(ucfirst(strftime('%B %Y',$now->getTimestamp())), 'StyleTexte12');

            //FOOTER
            $footerPageGarde = $sectionPageGarde->addFooter();
            $footerPageGarde->firstPage();
            $table = $footerPageGarde->addTable();
            $table->addRow();
            $table->addCell(2100)->addText('RM Conseil et Interventions', 'StyleTexte8', 'StyleParagrapheFooterHaut');
            $cell = $table->addCell(300);
            $cell->addText(' ');
            $cell->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width' => 10, 'height' => 10, 'align' => 'center',));
            $table->addCell(1800)->addText('1, place Jules Ferry', 'StyleTexte8', 'StyleParagrapheFooterHaut');
            $cell = $table->addCell(300);
            $cell->addText(' ');
            $cell->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width' => 10, 'height' => 10, 'align' => 'center',));
            $table->addCell(1400)->addText('69006 LYON', 'StyleTexte8', 'StyleParagrapheFooterHaut');
            $cell = $table->addCell(300);
            $cell->addText(' ');
            $cell->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width' => 10, 'height' => 10, 'align' => 'center',));
            $table->addCell(1400)->addText('04.78.60.42.73', 'StyleTexte8', 'StyleParagrapheFooterHaut');
            $cell = $table->addCell(300);
            $cell->addText(' ');
            $cell->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width' => 10, 'height' => 10, 'align' => 'center',));
            $table->addCell(1400)->addText('www.relaismanagers.fr', 'StyleTexte8', 'StyleParagrapheFooterHaut');

            $table = $footerPageGarde->addTable();
            $table->addRow();
            $cell = $table->addCell(9300);
            $cell->addText('SIRET 822 178 216 00021 - TVA Intracommunautaire FR36 822 178 207 - RCS : LYON - APE 8589A', 'StyleTexte8', 'StyleParagrapheFooterBas');
        }

        //si on met des  marges sur la section se sont les marges de la page
        $section = $phpWord->addSection(array('marginLeft' => 1200, 'marginRight' => 1000, 'marginTop' => 800, 'marginBottom' => 800));

        //Tableau du header avec le logouniquement sur la 1ere page
        $header = $section->addHeader();
        $header->firstPage();
        $table = $header->addTable('StyleTableHeader');
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addImage(BASE_PATH . "/assets/images/logo-rm-simple.png", array('height' => 50,'width' => 125, 'align' => 'left'));

        $this->addSautLigne($section, 5);

        //1ere page
        $table = $section->addTable('StyleTableFirstPage');
        $table->addRow();
        $cell = $table->addCell(4000);
        $cell = $table->addCell(6000);
        $textrun = $cell->addTextRun();
        $textrun->addText("Evaluation en 360°", 'StyleTexte28RougeBold', 'StyleParagrapheText1FirstPage');
        $textrun = $cell->addTextRun();
        $textrun->addText("", 'StyleTexte14Rouge', 'StyleParagrapheLeftColle');
        $textrun = $cell->addTextRun();
        $textrun->addText("Management de la mobilisation, de l’intelligence collective, de l’agilité, de la performance et de la Qualité de Vie au Travail", 'StyleTexte14Rouge', 'StyleParagrapheLeftColle');

        $this->addSautLigne($section, 12);

        //On affiche le logo du client si existant
        if ($quiz->logo) {
            $table = $section->addTable('StyleTableFirstPage');
            $table->addRow();
            $cell = $table->addCell(4000);
            $cell->addImage(BASE_PATH . "assets/images/logosClients/" . $quiz->logo, array('height' => 140, 'align' => 'left'));
            $cell = $table->addCell(6000);
        } else {
            $this->addSautLigne($section, 10);
        }

        // On affiche le nom de l'autoévalué et sa fonction
        // En fonction du sexe on adaptera les libellés $quiz->sexeAutoUser;
        $nameAutoUser = "";
        if ($quiz->autoUserFirstName) $nameAutoUser = $quiz->autoUserFirstName;
        if ($quiz->autoUserLastName) $nameAutoUser = $nameAutoUser . " " . strtoupper($quiz->autoUserLastName);

        $this->addSautLigne($section, 2);
        $textrun = $section->addTextRun();
        $textrun->addText("$nameAutoUser", 'StyleTexte18RougeBold', 'StyleParagrapheLeftColle');
        $textrun = $section->addTextRun();
        $textrun->addText($quiz->fonctionAutoUser, 'StyleTexte14RougeBold', 'StyleParagrapheLeftColle');
        $now = new \DateTime();
        $section->addText(ucfirst(strftime('%B %Y',$now->getTimestamp())), 'StyleTexte12', array('align' => 'right'));

        //FOOTER POUR PAGE 1
        $footer = $section->addFooter();
        $footer->firstPage();
        $table = $footer->addTable();
        $table->addRow();
        $table->addCell(2100)->addText('RM Conseil et Interventions', 'StyleTexte8', 'StyleParagrapheFooterHaut');
        $cell = $table->addCell(300);
        $cell->addText(' ');
        $cell->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width' => 10, 'height' => 10, 'align' => 'center',));
        $table->addCell(1800)->addText('1, place Jules Ferry', 'StyleTexte8', 'StyleParagrapheFooterHaut');
        $cell = $table->addCell(300);
        $cell->addText(' ');
        $cell->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width' => 10, 'height' => 10, 'align' => 'center',));
        $table->addCell(1400)->addText('69006 LYON', 'StyleTexte8', 'StyleParagrapheFooterHaut');
        $cell = $table->addCell(300);
        $cell->addText(' ');
        $cell->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width' => 10, 'height' => 10, 'align' => 'center',));
        $table->addCell(1400)->addText('04.78.60.42.73', 'StyleTexte8', 'StyleParagrapheFooterHaut');
        $cell = $table->addCell(300);
        $cell->addText(' ');
        $cell->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width' => 10, 'height' => 10, 'align' => 'center',));
        $table->addCell(1400)->addText('www.relaismanagers.fr', 'StyleTexte8', 'StyleParagrapheFooterHaut');
        $table = $footer->addTable();
        $table->addRow();
        $cell = $table->addCell(9300);
        $cell->addText('SIRET 822 178 216 00021 - TVA Intracommunautaire FR36 822 178 207 - RCS : LYON - APE 8589A', 'StyleTexte8', 'StyleParagrapheFooterBas');

        //FOOTER POUR TOUTES LES AUTRES PAGES
        $footer_sub = $section->addFooter();
        $footer_sub->addPreserveText('{PAGE} / {NUMPAGES}','StyleTexte9','StyleParagraphePageNumberFooter');


        $section->addPageBreak();
        $this->addSautLigne($section, 1);
        $section->addText("Evaluation en 360°", 'StyleTexte28RougeBold', 'StyleParagrapheText1SecondPage');
        $section->addText("Sommaire", 'StyleTexte28RougeBold', 'StyleParagrapheText2SecondPage');
        $textrun = $section->addTextRun();
        $textrun->addText("1.	Pourquoi cette évaluation en 360° ?", 'StyleTexte14RougeBold', 'StyleParagrapheLeftColle');
        $textrun = $section->addTextRun();
        $textrun->addText("2.	Qu’est-ce qui est évalué dans le 360° et sur quels fondements ?", 'StyleTexte14RougeBold', 'StyleParagrapheLeftColle');
        $textrun = $section->addTextRun();
        $textrun->addText("3.	Comment tirer le meilleur parti possible de cette évaluation ?", 'StyleTexte14RougeBold', 'StyleParagrapheLeftColle');
        $textrun = $section->addTextRun();
        $textrun->addText("4.	Quels sont les résultats de votre évaluation ?", 'StyleTexte14RougeBold', 'StyleParagrapheLeftColle');

        $section->addPageBreak();
        $this->addSautLigne($section, 1);
        $section->addText("1. Pourquoi cette évaluation en 360° ?", 'StyleTexte14RougeBold', 'StyleParagrapheTitre1');
        $section->addText("Les entreprises sont confrontées à des contextes de plus en plus complexes, à des défis de plus en plus rudes, à des changements constants.", 'StyleTexte11', 'StyleParagrapheText');
        $section->addText("Elles n’ont d’autres choix, pour faire face, que de développer en permanence leur agilité, leur ingéniosité, leur efficience, leurs performances.", 'StyleTexte11', 'StyleParagrapheText');
        $section->addText("Les équipes sont la clef de voute. Tout repose sur leur mobilisation, leur talent, leur inventivité, leur adaptabilité, leur cohésion et leur capacité à générer de l’intelligence collective, leur satisfaction au travail.", 'StyleTexte11', 'StyleParagrapheText');
        $section->addText("Les recherches et observations les plus récentes nous montrent que :", 'StyleTexte11', 'StyleParagrapheText');
        $section->addListItem('les façons d’être et de vivre son travail ne se décrètent pas. Elles résultent,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('l’Humain est naturellement « programmé pour donner son meilleur »(chacun avec ses talents et ses limites), pour relever les défis, développer ses capacités, se sentir au mieux dans son travail,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('il ne libèrera ce potentiel que sous certaines conditions (8 sont nécessaires),', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('certaines façons de manager créent ces conditions et libèrent ce potentiel, alors que d’autres l’éteignent.', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addText("Trop peu de managers ont, encore à ce jour, eu accès à ces connaissances scientifiques et ont pu faire évoluer leurs pratiques au regard de ce qui vient d’être exposé. Beaucoup s’appuient toujours sur des modèles aujourd’hui obsolètes ou leur seule expérience.", 'StyleTexte11', 'StyleParagrapheText');
        $section->addText("Il est donc fondamental d’aider les managers :", 'StyleTexte11', 'StyleParagrapheText');
        $section->addListItem('à observer s’ils pratiquent un management qui libère la mobilisation, l’ingéniosité, l’adaptabilité, la cohésion, la performance et la Qualité de Vie au Travail (dont la leur),', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('à circonscrire leurs axes éventuels de progrès et les moyens (savoir, savoir-faire, et être, dispositifs RH, soutiens, conditions de travail…) qui seraient nécessaires à une progression.', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addText("C’est l’objet de ce 360°.", 'StyleTexte11', 'StyleParagrapheText');

        $section->addPageBreak();
        $this->addSautLigne($section, 1);
        $section->addText("2. Qu’est-ce qui est évalué dans ce 360°et sur quels fondements ?", 'StyleTexte14RougeBold', 'StyleParagrapheTitre1');
        $section->addText("Comme évoqué précédemment, les fondements sur lesquels s’appuyaient les modèles de management ont évolué.", 'StyleTexte11', 'StyleParagrapheText');
        $section->addText("Ils sont aujourd’hui le fruit d'études, d'expérimentations, d'analyses plus « scientifiques ».", 'StyleTexte11', 'StyleParagrapheText');
        $section->addText("Ils révèlent entre autres que :", 'StyleTexte11', 'StyleParagrapheText');
        $section->addListItem('plutôt que de devoir développer leur charisme et leurs savoir-faire pour fédérer leurs équipes, les impliquer dans les changements, les motiver, développer une Qualité de Vie au Travail (QVT)… les managers ont à connaître et mettre en place les 8 conditions évoquées ci-dessus. En résultera l’engagement, l’adaptabilité, la cohésion, le bien-vivre son travail,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('plutôt que de devoir chercher à faire grandir leurs équipes, à transformer les femmes et les hommes qu’ils managent, les managers ont à savoir faire en sorte que les projets se réalisent avec l’homme et la femme Réels*', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('là où tout reposait souvent sur leurs épaules, la pratique du co-management s’avère plus efficiente pour eux comme pour leurs équipes et l’entreprise.', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addText("Mettre en place les 8 conditions, « le co-management » du travail avec la femme et l’homme Réels dont nous parlons, se traduit par :", 'StyleTexte11', 'StyleParagrapheText');

        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('a. ', 'StyleTexte11Rouge');
        $textRun->addText('mettre en œuvre un certain nombre d’actes à ce jour connus', 'StyleTexte11');
        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('b. ', 'StyleTexte11Rouge');
        $textRun->addText('développer un savoir-faire relationnel spécifique', 'StyleTexte11');
        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('c. ', 'StyleTexte11Rouge');
        $textRun->addText('gérer son temps et son stress.', 'StyleTexte11');

        $this->addSautLigne($section, 1);
        $section->addText("Ce sont :", 'StyleTexte11', 'StyleParagrapheText');
        $section->addListItem('la mise en œuvre de ces 3 axes (a, b, c),', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('les éventuelles améliorations nécessaires,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('les besoins pour que ces améliorations soient possibles...,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addText("...qui sont évalués ici.", 'StyleTexte11', 'StyleParagrapheText');

        $this->addSautLigne($section, 2);

        $section->addText("*L’homme et la femme réels : les personnes telles qu’elles sont, singulières, ayant des limites, comme tout un chacun mais propres à chacune, par opposition à la « femme et l’homme idéalisés » qu’on obtiendrait grâce au management d’équipe, stéréotypés, complets, heureux et dotés d’un potentiel illimité. ", 'StyleTexte9Orange', 'StyleParagrapheText');

        $this->addSautLigne($section, 2);

        $section->addText("3. Comment tirer le meilleur profit de cette évaluation ?", 'StyleTexte14RougeBold', 'StyleParagrapheTitre1');
        $section->addText("Cette évaluation n’a ni pour objet de vous noter, ni de vous juger, ni d’évaluer vos capacités ou votre potentiel.", 'StyleTexte11', 'StyleParagrapheText');
        $section->addText("Elle met en évidence :", 'StyleTexte11', 'StyleParagrapheText');
        $section->addListItem('quels actes managériaux vous ne posez peut-être pas à ce jour (susceptibles de nuire à l’efficience de votre management),', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('quels postures et savoir-faire relationnels spécifiques vous ne mettez peut-être pas en œuvre,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('comment est perçue votre gestion du temps, des priorités, du stress.', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $this->addSautLigne($section, 2);
        $section->addText("Nous vous suggérons :", 'StyleTexte11', 'StyleParagrapheText');
        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('1. ', 'StyleTexte11Rouge');
        $textRun->addText('de distinguer :', 'StyleTexte11Bold');
        $section->addListItem('les points où vous êtes totalement en accord avec le résultat,', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $section->addListItem('ceux où vous ne comprenez pas pourquoi vous obtenez ces résultats, où vous vous sentez en désaccord,', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('2. ', 'StyleTexte11Rouge');
        $textRun->addText('de questionner votre entourage ', 'StyleTexte11Bold');
        $textRun->addText('et notamment les fonctions qui ont évalué votre management sur les points que vous ne comprenez pas, avec lesquels vous vous sentez en désaccord,', 'StyleTexte11');
        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('3. ', 'StyleTexte11Rouge');
        $textRun->addText('de vous observer ', 'StyleTexte11Bold');
        $textRun->addText('sur ces points qui vous questionnent pour vérifier si finalement vous validez les résultats obtenus ou si vous maintenez votre incompréhension,', 'StyleTexte11');
        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('4. ', 'StyleTexte11Rouge');
        $textRun->addText('de réfléchir aux causes ', 'StyleTexte11Bold');
        $textRun->addText('des points évalués comme n’étant pas ad hoc et que vous avez validés. Sont-ils dus :', 'StyleTexte11');
        $section->addListItem('au fait que vous n’aviez pas conscience de leur importance ?', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $section->addListItem('à un manque de savoir-faire ?', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $section->addListItem('à un manque de temps ?', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $section->addListItem('à un manque de moyens, de soutien… ?', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $section->addListItem('etc.', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('5. ', 'StyleTexte11Rouge');
        $textRun->addText('de réfléchir aux solutions ', 'StyleTexte11Bold');
        $textRun->addText('à mettre en œuvre pour faire évoluer votre management et son impact.', 'StyleTexte11');

        $this->addSautLigne($section, 3);
        $section->addText("4. Quels sont les résultats de votre évaluation en 360° ?", 'StyleTexte14RougeBold', 'StyleParagrapheTitre1');
        $section->addText("Rappel de ce qui est évalué :", 'StyleTexte11Bold', 'StyleParagrapheText');
        $section->addListItem('Mettez-vous en œuvre les actes managériaux fondamentaux ?', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $section->addListItem('Vos façons de faire et d’être, vos postures, facilitent-elles votre collaboration avec votre entourage et génèrent-t-elles un leadership, de l’agilité, de l’intelligence collective, de la performance, de la QVT ?', 1, 'list1', $listStyle, 'StyleParagrapheList1');
        $section->addListItem('Gérez-vous votre temps, vos priorités, votre stress avec efficience ?', 1, 'list1', $listStyle, 'StyleParagrapheList1');


        /////////////////////////
        // AJOUT DU TABLEAU RECAP
        /////////////////////////
        ///
        $section->addPageBreak();

        //Titre du tableau
        $textrun = $section->addTextRun('StyleParagrapheTitre2');
        $textrun->addText('A – Tableau récapitulatif', 'StyleTexte14RougeBold');

        $this->addSautLigne($section, 1);

        $table = $section->addTable('StyleTableRecap');

        //Ajout de la ligne de legend
        $row = $table->addRow();
        $cell = $table->addCell(7000);
        $cell->addText(' ', 'StyleTexte8');
        $cell->getStyle()->setBorderBottomColor('ffffff');
        $cell->getStyle()->setBorderTopColor('ffffff');
        $cell->getStyle()->setBorderLeftColor('ffffff');
        $cell->getStyle()->setBorderBottomSize(0);
        $cell->getStyle()->setBorderTopSize(0);
        $cell->getStyle()->setBorderLeftSize(0);
        $cell->getStyle()->setGridSpan(2);
        $cell = $table->addCell(990);
        $cell->addText('0', 'StyleTexte16Vert');
        $cell->getStyle()->setBgColor('92D050');
        $cell->getStyle()->setGridSpan(3);
        $cell = $table->addCell(990);
        $cell->addText('0', 'StyleTexte16Orange');
        $cell->getStyle()->setBgColor('F97407');
        $cell->getStyle()->setGridSpan(3);
        $cell = $table->addCell(990);
        $cell->addText('0', 'StyleTexte16Rouge');
        $cell->getStyle()->setBgColor('FF0000');
        $cell->getStyle()->setGridSpan(3);

        //Tableau qui contient les libellé des questions du rapport
        $questionLabels = array();

        //Ajout de la ligne chapitre AVEC SON ÉQUIPE
        $label = $quizQuestionRepository->getQuestionsByQuizIdAndOrder($quizId, 4);
        $LabelChapterEquipe = $label->label;
        $this->addLignChapter($table, $LabelChapterEquipe);

        //Question de 1 à 7
        $questionNumber = 1;
        for ($i = 1; $i <= 7; $i++) {
            $questionLabel = $quizQuestionRepository->getQuestionByQuizIdAndReportOrder($quizId, $i);
            $questionLabels[$i] = $questionLabel->label;
            //On recupere les note de la question et on calcul la moyenne
            $critereRecherche = [];
            $critereRecherche['quizId'] = $quizId;
            $critereRecherche['questionTypeReportOrder'] = $i;
            $critereRecherche['fonction'] = "Equipe";
            $quizUserResponses = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            //Si pas de reponse on met une note de -1 pour que rien ne soit cocher
            $note = -1;
            if ($quizUserResponses) {
                $note = self::calculateMoyenne($quizUserResponses);
            }
            $this->addLignQuestion($table, $questionNumber, $questionLabel, $note);
            $questionNumber = $questionNumber + 1;
        }

        //Ajout de la ligne chapitre AVEC SA HIREARCHIE
        $label = $quizQuestionRepository->getQuestionsByQuizIdAndOrder($quizId, 12);
        $LabelChapterHierachie = $label->label;
        $this->addLignChapter($table, $LabelChapterHierachie);

        //Question de 8 à 12
        $questionNumber = 1;
        for ($i = 8; $i <= 12; $i++) {
            $questionLabel = $quizQuestionRepository->getQuestionByQuizIdAndReportOrder($quizId, $i);
            $questionLabels[$i] = $questionLabel->label;
            $critereRecherche = [];
            $critereRecherche['quizId'] = $quizId;
            $critereRecherche['questionTypeReportOrder'] = $i;
            $critereRecherche['fonction'] = "Hierarchie";
            $quizUserResponses = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $note = -1;
            if ($quizUserResponses) {
                $note = self::calculateMoyenne($quizUserResponses);
            }
            $this->addLignQuestion($table, $questionNumber, $questionLabel, $note);
            $questionNumber = $questionNumber + 1;
        }

        //Ajout de la ligne chapitre AVEC LES ACTEURS TRANSVERSES
        $label = $quizQuestionRepository->getQuestionsByQuizIdAndOrder($quizId, 18);
        $LabelChapterTransverse = $label->label;
        $this->addLignChapter($table, $LabelChapterTransverse);

        //Question de 13 à 17
        $questionNumber = 1;
        for ($i = 13; $i <= 17; $i++) {
            $questionLabel = $quizQuestionRepository->getQuestionByQuizIdAndReportOrder($quizId, $i);
            $questionLabels[$i] = $questionLabel->label;
            $critereRecherche = [];
            $critereRecherche['quizId'] = $quizId;
            $critereRecherche['questionTypeReportOrder'] = $i;
            $critereRecherche['fonction'] = "Transverse";
            $quizUserResponses = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $note = -1;
            if ($quizUserResponses) {
                $note = self::calculateMoyenne($quizUserResponses);
            }
            $this->addLignQuestion($table, $questionNumber, $questionLabel, $note);
            $questionNumber = $questionNumber + 1;
        }

        //Ajout de la ligne chapitre GESTION DE SOI, DE SON LEADERSHIP
        $label = $quizQuestionRepository->getQuestionsByQuizIdAndOrder($quizId, 24);
        $LabelChapterGestionDeSoi = $label->label;
        $this->addLignChapter($table, $LabelChapterGestionDeSoi);

        //Question de 18 à 34
        $questionNumber = 1;
        for ($i = 18; $i <= 34; $i++) {
            $questionLabel = $quizQuestionRepository->getQuestionByQuizIdAndReportOrder($quizId, $i);
            $questionLabels[$i] = $questionLabel->label;
            $critereRecherche = [];
            $critereRecherche['quizId'] = $quizId;
            $critereRecherche['questionTypeReportOrder'] = $i;
            $critereRecherche['excludeAuto'] = 1;
            $quizUserResponses = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $note = -1;
            if ($quizUserResponses) {
                $note = self::calculateMoyenne($quizUserResponses);
            }
            $this->addLignQuestion($table, $questionNumber, $questionLabel, $note);
            $questionNumber = $questionNumber + 1;
        }

        //Ajout de la ligne chapitre GESTION DE SON TEMPS, DE SES PRIORITÉS
        $label = $quizQuestionRepository->getQuestionsByQuizIdAndOrder($quizId, 42);
        $LabelChapterGestionDeSonTemps = $label->label;
        $this->addLignChapter($table, $LabelChapterGestionDeSonTemps);

        //Question de 35 à 45
        $questionNumber = 1;
        for ($i = 35; $i <= 45; $i++) {
            $questionLabel = $quizQuestionRepository->getQuestionByQuizIdAndReportOrder($quizId, $i);
            $questionLabels[$i] = $questionLabel->label;
            $critereRecherche = [];
            $critereRecherche['quizId'] = $quizId;
            $critereRecherche['questionTypeReportOrder'] = $i;
            $critereRecherche['excludeAuto'] = 1;
            $quizUserResponses = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $note= -1;
            if ($quizUserResponses) {
                $note = self::calculateMoyenne($quizUserResponses);
            }
            $this->addLignQuestion($table, $questionNumber, $questionLabel, $note);
            $questionNumber = $questionNumber + 1;
        }

        /////////////////////////
        // RECUPERATION DES REPONSES
        /////////////////////////
        //Pour chaque question on fait 5 requetes en base
        //reponse de l'autoevalué
        //reponse de tous les autre user
        //reponse de tous les autre user de la fonction Hierarchie
        //reponse de tous les autre user de la fonction Transverse
        //reponse de tous les autre user de la fonction Equipe
        $questionsResponseUserMoyenne = array();
        for ($i = 1; $i <= 45; $i++) {
            //On recupere les reponses de l'autoevalué
            $critereRecherche = [];
            $critereRecherche['quizId'] = $quiz->id;
            $critereRecherche['questionTypeReportOrder'] = $i;
            $critereRecherche['auto'] = 1;
            $resultAutoUser = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $noteAutoUser = -1;
            if ($resultAutoUser) {
                $noteAutoUser = (int)$resultAutoUser[0];
            }

            //reponse de tous les autre user (on exclus l'auto evalué)
            unset($critereRecherche['auto']);
            $critereRecherche['excludeAuto'] = 1;
            $quizUserResponsesUsers = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $noteMoyenneAllUsers = -1;
            if ($quizUserResponsesUsers) {
                $noteMoyenneAllUsers = self::calculateMoyenne($quizUserResponsesUsers);
            }

            //reponse de tous les autre user de la fonction Hierarchie
            $critereRecherche['fonction'] = "Hierarchie";
            $quizUserResponsesUsersHierarchie = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $noteMoyenneHierarchie = -1;
            if ($quizUserResponsesUsersHierarchie) {
                $noteMoyenneHierarchie = self::calculateMoyenne($quizUserResponsesUsersHierarchie);
            }

            //reponse de tous les autre user de la fonction Transverse
            $critereRecherche['fonction'] = "Transverse";
            $quizUserResponsesUsersTransverses = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $noteMoyenneTransverses = -1;
            if ($quizUserResponsesUsersTransverses) {
                $noteMoyenneTransverses = self::calculateMoyenne($quizUserResponsesUsersTransverses);
            }

            //reponse de tous les autre user de la fonction Equipe
            $critereRecherche['fonction'] = "Equipe";
            $quizUserResponsesUsersEquipe = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, 'value');
            $noteMoyenneEquipe = -1;
            if ($quizUserResponsesUsersEquipe) {
                $noteMoyenneEquipe = self::calculateMoyenne($quizUserResponsesUsersEquipe);
            }

            $questionsResponseUserMoyenne[$i] = [$noteMoyenneEquipe, $noteMoyenneTransverses, $noteMoyenneHierarchie, $noteMoyenneAllUsers, $noteAutoUser];
        }


        /////////////////////////
        // AJOUT DES ANALYSES
        /////////////////////////

        $section->addPageBreak();

        $textrun = $section->addTextRun('StyleParagrapheTitre2');
        $textrun->addText('B - Analyse', 'StyleTexte14RougeBold');
        $this->addSautLigne($section, 1);
        $section->addText('1. La vision et l’utilisation du Requis', 'StyleTexte13Bold');
        $section->addText('Le « Requis », c’est :', 'StyleTexte11', 'StyleParagrapheText');
        $section->addListItem('le projet d’entreprise auquel chacun est censé contribuer,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('les missions, tâches à accomplir par chacun ; les périmètres de responsabilités ; les objectifs à atteindre,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('les règles à respecter,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('les règles qui régissent la collaboration du salarié avec l’entreprise (rémunération, gratifications, sanctions, possibilités d’évolutions…),', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('les contraintes à prendre en compte,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('les changements à mettre en œuvre,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('…/…', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addText('Chacun est à priori rémunéré pour que le Requis puisse aboutir. Plus il est clair pour chacun, plus il est pris en compte par chacun et plus chacun cherche à y répondre, plus on constate que la collaboration et la cohésion sont facilitées, que l’intelligence collective peut se développer, que l’agilité et l’efficience sont au rendez-vous.', 'StyleTexte11', 'StyleParagrapheText');
        $section->addText('Plus ce Requis est flou, mis de côté ou oublié, plus s’installe le risque de voir chacun défendre ses intérêts et sa vision des choses plutôt que de porter le Requis.', 'StyleTexte11', 'StyleParagrapheText');

        $this->addSautLigne($section, 2);
        $text = $nameAutoUser . ' donne le sentiment d’avoir une très bonne vision du « Requis » (le projet d’entreprise, les objectifs à atteindre, le périmètre et les responsabilités de chacun, les règles qui régissent les rapports entre les personnes, au travail, à l’entreprise…), de chercher à le prendre systématiquement en compte';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        //Pour rappel $questionsResponseUserMoyenne[$i] = [$noteMoyenneEquipe, $noteMoyenneTransverses, $noteMoyenneHierarchie, $noteMoyenneAllUsers, $noteAutoUser];
        if ($questionsResponseUserMoyenne[1][0] > 3) $cocheEquipe = 1;
        if ($questionsResponseUserMoyenne[8][2] > 3) $cocheHierarchie = 1;
        if ($questionsResponseUserMoyenne[13][1] > 3) $cocheTransverse = 1;
        $this->addAnalyse($section, 300, $text, 'a)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment d’avoir une assez bonne vision du Requis et de plutôt chercher à le prendre en compte et à y répondre';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($questionsResponseUserMoyenne[1][0] > 2 && $questionsResponseUserMoyenne[1][0] <= 3) $cocheEquipe = 1;
        if ($questionsResponseUserMoyenne[8][2] > 2 && $questionsResponseUserMoyenne[8][2] <= 3) $cocheHierarchie = 1;
        if ($questionsResponseUserMoyenne[13][1] > 2 && $questionsResponseUserMoyenne[13][1] <= 3) $cocheTransverse = 1;
        $this->addAnalyse($section, 300, $text, 'b)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment de ne pas toujours avoir une vision claire du Requis ou de ne pas toujours chercher à le prendre en compte ou à y répondre';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($questionsResponseUserMoyenne[1][0] > 1 && $questionsResponseUserMoyenne[1][0] <= 2) $cocheEquipe = 1;
        if ($questionsResponseUserMoyenne[8][2] > 1 && $questionsResponseUserMoyenne[8][2] <= 2) $cocheHierarchie = 1;
        if ($questionsResponseUserMoyenne[13][1] > 1 && $questionsResponseUserMoyenne[13][1] <= 2) $cocheTransverse = 1;
        $this->addAnalyse($section, 300, $text, 'c)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment de ne pas avoir une vision claire du Requis ou de ne pas chercher à le prendre en compte ou à y répondre';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($questionsResponseUserMoyenne[1][0] <= 1 && $questionsResponseUserMoyenne[1][0] >= 0) $cocheEquipe = 1;
        if ($questionsResponseUserMoyenne[8][2] <= 1 && $questionsResponseUserMoyenne[8][2] >= 0) $cocheHierarchie = 1;
        if ($questionsResponseUserMoyenne[13][1] <= 1 && $questionsResponseUserMoyenne[13][1] >= 0) $cocheTransverse = 1;
        $this->addAnalyse($section, 300, $text, 'd)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);

        $this->addSautLigne($section, 1);
        $section->addText('2. La prise en compte du Réel et son utilisation', 'StyleTexte13Bold');
        $section->addText('Le Réel, c’est tout ce qui peut rendre difficile ou impossible la réalisation du Requis :', 'StyleTexte11', 'StyleParagrapheText');
        $section->addListItem('un Requis confus,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('une organisation et/ou des méthodes de travail inadaptées,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('des moyens inadéquats,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('un milieu inadapté ou pénalisant,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('un management inefficient,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('des collègues ou autres acteurs néfastes à l’agilité, la performance, la QVT,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('soi-même (une vision erronée de sa mission, de son environnement de travail, des valeurs en conflit avec son travail, des savoir-faire inadaptés, une motivation qui ne pourra être satisfaite, une limite de capacité…),', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('un équilibre de vie professionnelle/vie privée difficile', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('…/…', 0, 'list1', $listStyle, 'StyleParagrapheList');

        $this->addSautLigne($section, 1);
        $section->addText('2.1 L’accès au Réel', 'StyleTexte11', 'StyleParagrapheTitre4');
        $section->addText('Plus le Réel est considéré par chaque acteur comme une source d’amélioration continue (et non comme quelque chose de négatif) et plus il peut être exprimé, plus il permettra à chacun et au collectif de se libérer de ce qui le préoccupe et de trouver des façons d’améliorer la situation, de faire face.', 'StyleTexte11', 'StyleParagrapheTextDecale');

        $this->addSautLigne($section, 1);
        $text = $nameAutoUser . ' donne le sentiment de s’intéresser pleinement au travail réalisé, aux difficultés rencontrées (les siennes, celles de son entourage), à la QVT';
        $notesEquipe = array($questionsResponseUserMoyenne[2][0], $questionsResponseUserMoyenne[3][0], $questionsResponseUserMoyenne[4][0]);
        $notesHierarchie = array($questionsResponseUserMoyenne[9][2], $questionsResponseUserMoyenne[10][2]);
        $notesTransverse = array($questionsResponseUserMoyenne[16][1], $questionsResponseUserMoyenne[17][1]);
        $averageEquipe = self::calculateMoyenne($notesEquipe);
        $averageHierarchie = self::calculateMoyenne($notesHierarchie);
        $averageTransverse = self::calculateMoyenne($notesTransverse);
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 3) $cocheEquipe = 1;
        if ($averageHierarchie > 3) $cocheHierarchie = 1;
        if ($averageTransverse > 3) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'a)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment de plutôt s’intéresser au travail réalisé, aux difficultés rencontrées, à la QVT';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 2 && $averageEquipe <= 3) $cocheEquipe = 1;
        if ($averageHierarchie > 2 && $averageHierarchie <= 3) $cocheHierarchie = 1;
        if ($averageTransverse > 2 && $averageTransverse <= 3) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'b)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment de trop peu se soucier du travail réalisé, des difficultés rencontrées, de la QVT';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 1 && $averageEquipe <= 2) $cocheEquipe = 1;
        if ($averageHierarchie > 1 && $averageHierarchie <= 2) $cocheHierarchie = 1;
        if ($averageTransverse > 1 && $averageTransverse <= 2) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'c)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment de ne pas s’intéresser au travail réalisé, aux difficultés rencontrées, à la QVT';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe <= 1 && $averageEquipe  >= 0) $cocheEquipe = 1;
        if ($averageHierarchie <= 1 && $averageHierarchie  >= 0) $cocheHierarchie = 1;
        if ($averageTransverse <= 1 && $averageTransverse  >= 0) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'd)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);

        $this->addSautLigne($section, 1);
        $section->addText('2.2 L’utilisation du Réel', 'StyleTexte11', 'StyleParagrapheTitre4');
        $section->addText('Plus le Réel (ce qui fait obstacle) est l’objet d’une co-réflexion avec les personnes concernées sur les causes, les solutions, « qui doit résoudre »… plus se développent l’agilité, l’intelligence collective, l’autonomie, la performance et la QVT.', 'StyleTexte11', 'StyleParagrapheTextDecale');

        $this->addSautLigne($section, 1);
        $text = $nameAutoUser . ' donne le sentiment de systématiquement provoquer une co-réflexion et co-résolution avec les personnes concernées lorsqu’une difficulté se répète';
        $notesEquipe = array($questionsResponseUserMoyenne[5][0], $questionsResponseUserMoyenne[6][0], $questionsResponseUserMoyenne[7][0]);
        $notesHierarchie = array($questionsResponseUserMoyenne[11][2], $questionsResponseUserMoyenne[12][2]);
        $notesTransverse = array($questionsResponseUserMoyenne[16][1], $questionsResponseUserMoyenne[17][1]);
        $averageEquipe = self::calculateMoyenne($notesEquipe);
        $averageHierarchie = self::calculateMoyenne($notesHierarchie);
        $averageTransverse = self::calculateMoyenne($notesTransverse);
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 3) $cocheEquipe = 1;
        if ($averageHierarchie > 3) $cocheHierarchie = 1;
        if ($averageTransverse > 3) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'a)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment de plutôt provoquer une co-réflexion et co-résolution avec les personnes concernées lorsqu’une difficulté se répète';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 2 && $averageEquipe <= 3) $cocheEquipe = 1;
        if ($averageHierarchie > 2 && $averageHierarchie <= 3) $cocheHierarchie = 1;
        if ($averageTransverse > 2 && $averageTransverse <= 3) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'b)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment de peu solliciter et impliquer les personnes concernées et les mieux placées pour résoudre des difficultés qui se répètent';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 1 && $averageEquipe <= 2) $cocheEquipe = 1;
        if ($averageHierarchie > 1 && $averageHierarchie <= 2) $cocheHierarchie = 1;
        if ($averageTransverse > 1 && $averageTransverse <= 2) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'c)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = $nameAutoUser . ' donne le sentiment de ne pas solliciter ni impliquer les personnes concernées et les mieux placées pour résoudre des difficultés qui se répètent ou réfléchir à des améliorations';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe <= 1 && $averageEquipe  >= 0) $cocheEquipe = 1;
        if ($averageHierarchie <= 1 && $averageHierarchie  >= 0) $cocheHierarchie = 1;
        if ($averageTransverse <= 1 && $averageTransverse  >= 0) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'd)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);

        $this->addSautLigne($section, 1);
        $section->addText('3. Les postures et le savoir-faire relationnel (Gestion de soi, de son leadership)', 'StyleTexte13Bold');
        $section->addText('Plus un manager semble :', 'StyleTexte11', 'StyleParagrapheText');
        $section->addListItem('clair, cohérent, réaliste, centré sur le Requis de l’entreprise (et non sur ses visions personnelles),', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('exposer clairement et avec pertinence ses attendus, ses besoins, les problèmes à résoudre,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('écouter le « Réel » des autres, leurs propres difficultés,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('chercher à co-réfléchir aux meilleures façons d’avancer et de résoudre en prenant en compte la réalité (le Requis et le Réel de chacun),', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('savoir conjuguer avec justesse exigence et bienveillance,', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addText('plus il contribuera à développer la mobilisation, l’agilité, l’intelligence collective, les performances, la QVT.', 'StyleTexte11', 'StyleParagrapheText');

        $this->addSautLigne($section, 1);
        $section->addText($nameAutoUser . ' donne le sentiment :', 'StyleTexte11', 'StyleParagrapheText');
        $text = "d'avoir pleinement des postures et un savoir-faire relationnel qui contribuent fortement au déploiement de la mobilisation, de l’agilité, de la cohésion, de l’intelligence collective, de la performance et de la QVT";
        $notesEquipe = array();
        $notesHierarchie = array();
        $notesTransverse = array();
        for ($i = 18; $i <= 34; $i++) {
            $notesEquipe[] = $questionsResponseUserMoyenne[$i][0];
            $notesHierarchie[] = $questionsResponseUserMoyenne[$i][2];
            $notesTransverse[] = $questionsResponseUserMoyenne[$i][1];
        }
        $averageEquipe = self::calculateMoyenne($notesEquipe);
        $averageHierarchie = self::calculateMoyenne($notesHierarchie);
        $averageTransverse = self::calculateMoyenne($notesTransverse);
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 3) $cocheEquipe = 1;
        if ($averageHierarchie > 3) $cocheHierarchie = 1;
        if ($averageTransverse > 3) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'a)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = 'de plutôt avoir des postures et un savoir-faire relationnel qui contribuent fortement au déploiement de la mobilisation, de l’agilité, de la cohésion, de l’intelligence collective, de la performance et de la QVT';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 2 && $averageEquipe <= 3) $cocheEquipe = 1;
        if ($averageHierarchie > 2 && $averageHierarchie <= 3) $cocheHierarchie = 1;
        if ($averageTransverse > 2 && $averageTransverse <= 3) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'b)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = 'de ne pas vraiment avoir les postures et le savoir-faire relationnel qui contribuent fortement au déploiement de la mobilisation, de l’agilité, de la cohésion, de l’intelligence collective, de la performance et de la QVT';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 1 && $averageEquipe <= 2) $cocheEquipe = 1;
        if ($averageHierarchie > 1 && $averageHierarchie <= 2) $cocheHierarchie = 1;
        if ($averageTransverse > 1 && $averageTransverse <= 2) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'c)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = 'de ne pas du tout avoir les postures et le savoir-faire relationnel qui contribuent fortement au déploiement de la mobilisation, de l’agilité, de la cohésion, de l’intelligence collective, de la performance et de la QVT';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe <= 1 && $averageEquipe  >= 0) $cocheEquipe = 1;
        if ($averageHierarchie <= 1 && $averageHierarchie  >= 0) $cocheHierarchie = 1;
        if ($averageTransverse <= 1 && $averageTransverse  >= 0) $cocheTransverse = 1;
        $this->addAnalyse($section, 900, $text, 'd)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);

        $this->addSautLigne($section, 1);

        $section->addText('4. La gestion de son temps, de ses priorités, de son stress', 'StyleTexte13Bold');
        $section->addText('Plus un manager gère bien son temps, ses priorités, son stress, plus sa Qualité de Vie au Travail, sa mobilisation, son efficience seront préservées et plus il pourra développer un management qui accroît l’agilité, l’intelligence collective, la performance et un bien-vivre son travail.', 'StyleTexte11', 'StyleParagrapheText');
        $section->addText($nameAutoUser . ' donne le sentiment ', 'StyleTexte11', 'StyleParagrapheText');
        $text = 'de très bien gérer ses priorités, son temps, son stress';
        $notesEquipe = array();
        $notesHierarchie = array();
        $notesTransverse = array();
        for ($i = 35; $i <= 45; $i++) {
            $notesEquipe[] = $questionsResponseUserMoyenne[$i][0];
            $notesHierarchie[] = $questionsResponseUserMoyenne[$i][2];
            $notesTransverse[] = $questionsResponseUserMoyenne[$i][1];
        }
        $averageEquipe = self::calculateMoyenne($notesEquipe);
        $averageHierarchie = self::calculateMoyenne($notesHierarchie);
        $averageTransverse = self::calculateMoyenne($notesTransverse);
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 3) $cocheEquipe = 1;
        if ($averageHierarchie > 3) $cocheHierarchie = 1;
        if ($averageTransverse > 3) $cocheTransverse = 1;
        $this->addAnalyseV2($section, 900, $text, 'a)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = 'de plutôt bien gérer ses priorités, son temps, son stress';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 2 && $averageEquipe <= 3) $cocheEquipe = 1;
        if ($averageHierarchie > 2 && $averageHierarchie <= 3) $cocheHierarchie = 1;
        if ($averageTransverse > 2 && $averageTransverse <= 3) $cocheTransverse = 1;
        $this->addAnalyseV2($section, 900, $text, 'b)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = 'de ne pas très bien gérer ses priorités, son temps, son stress';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe > 1 && $averageEquipe <= 2) $cocheEquipe = 1;
        if ($averageHierarchie > 1 && $averageHierarchie <= 2) $cocheHierarchie = 1;
        if ($averageTransverse > 1 && $averageTransverse <= 2) $cocheTransverse = 1;
        $this->addAnalyseV2($section, 900, $text, 'c)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);
        $text = 'd’être en difficulté en termes de gestion de ses priorités, de son temps, de son stress';
        $cocheEquipe = 0;
        $cocheHierarchie = 0;
        $cocheTransverse = 0;
        if ($averageEquipe <= 1 && $averageEquipe  >= 0) $cocheEquipe = 1;
        if ($averageHierarchie <= 1 && $averageHierarchie  >= 0) $cocheHierarchie = 1;
        if ($averageTransverse <= 1 && $averageTransverse  >= 0) $cocheTransverse = 1;
        $this->addAnalyseV2($section, 900, $text, 'd)', $cocheEquipe, $cocheHierarchie, $cocheTransverse);


        $this->addSautLigne($section, 1);

        $section->addText('Le regard de l’intéressé(e) :', 'StyleTexte11Bold');
        $section->addListItem('quant à l’analyse ci-dessus', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('sur ce qui pourrait « empêcher » ou favoriser une amélioration des axes ci-dessus', 0, 'list1', $listStyle, 'StyleParagrapheList');
        $section->addListItem('sur ses besoins pour les optimiser', 0, 'list1', $listStyle, 'StyleParagrapheList');

        //ajout d'une page blanche
        $section->addPageBreak();

        /////////////////////////
        // AJOUT DES GRAPHIQUES
        /////////////////////////
        $section->addPageBreak();

        $textrun = $section->addTextRun('StyleParagrapheTitre2');
        $textrun->addText('C - Résultats détaillés', 'StyleTexte14RougeBold');
        $this->addSautLigne($section, 1);

        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('Nota : ', 'StyleTexte9Orange');
        $textRun->addText('les notations encadrées en ', 'StyleTexte9');
        $textRun->addText('orange', 'StyleTexte9Orange');
        $textRun->addText(', ont pour objectif de permettre au bénéficiaire de repérer plus rapidement ses axes de vigilance.', 'StyleTexte9');


        $this->addSautLigne($section, 1);

        $arrayColor = ['E96609', 'E96609', 'E96609', 'E96609', '696252'];
        $categories = ['Equipe', 'Transverse', 'Hierarchie', 'Eval 360', 'AutoEvaluation'];

        $LabelChapterEquipe = $this->formatForWordChapter($LabelChapterEquipe);
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $LabelChapterEquipe);

        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('Les critères retenus pour encadrer en ', 'StyleTexte9');
        $textRun->addText('orange ', 'StyleTexte9Orange');
        $textRun->addText('les axes de vigilance, ont été : ', 'StyleTexte9');
        $textRun->addText('toute notation attribuée par votre équipe ', 'StyleTexte9Underline');
        $textRun->addText('inférieure ou égale à 2,5', 'StyleTexte9');
        $this->addSautLigne($section, 1);

        //Question de 1 à 7 - AVEC SES EQUIPES
        //Regle pour encadrer en orange
        //note inferieur à 3  sur equipe pour chapitre equipe
        //note inferieur à 3  sur transverse pour chapitre transverse
        //note inferieur à 3  sur hierarchie pour chapitre hierarchie
        //sur le 2 autre paragraphe. Si une des 5 notes inferieur à 3
        $questionNumber = 1;
        for ($i = 1; $i <= 7; $i++) {
            $styleGraph = $columnStyle;
            if ($questionsResponseUserMoyenne[$i][0] < 3) {
                $styleGraph = $columnStyleOrange;
            }
            $this->addResult($section, $i, $arrayColor, $categories, $questionLabels, $questionsResponseUserMoyenne, $questionNumber, $styleGraph);
            $questionNumber = $questionNumber + 1;
            $this->addSautLigne($section, 2);
        }

        $section->addPageBreak();
        $this->addSautLigne($section, 1);
        $LabelChapterHierachie = $this->formatForWordChapter($LabelChapterHierachie);
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $LabelChapterHierachie);

        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('Les critères retenus pour encadrer en ', 'StyleTexte9');
        $textRun->addText('orange ', 'StyleTexte9Orange');
        $textRun->addText('les axes de vigilance, ont été : ', 'StyleTexte9');
        $textRun->addText('toute notation attribuée par votre hiérarchie ', 'StyleTexte9Underline');
        $textRun->addText('inférieure ou égale à 2,5', 'StyleTexte9');
        $this->addSautLigne($section, 1);

        //Question de 8 à 12 - AVEC SA HIERARCHIE
        $questionNumber = 1;
        for ($i = 8; $i <= 12; $i++) {
            $styleGraph = $columnStyle;
            if ($questionsResponseUserMoyenne[$i][2] < 3) {
                $styleGraph = $columnStyleOrange;
            }
            $this->addResult($section, $i, $arrayColor, $categories, $questionLabels, $questionsResponseUserMoyenne, $questionNumber, $styleGraph);
            $questionNumber = $questionNumber + 1;
            $this->addSautLigne($section, 2);
        }

        $section->addPageBreak();
        $this->addSautLigne($section, 1);
        $LabelChapterTransverse = $this->formatForWordChapter($LabelChapterTransverse);
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $LabelChapterTransverse);

        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('Les critères retenus pour encadrer en ', 'StyleTexte9');
        $textRun->addText('orange ', 'StyleTexte9Orange');
        $textRun->addText('les axes de vigilance, ont été : ', 'StyleTexte9');
        $textRun->addText('toute notation attribuée par les acteurs transverses ', 'StyleTexte9Underline');
        $textRun->addText('inférieure ou égale à 2,5', 'StyleTexte9');
        $this->addSautLigne($section, 1);

        //Question de 13 à 17 - AVEC LES ACTEURS TRASNVERSESS
        $questionNumber = 1;
        for ($i = 13; $i <= 17; $i++) {
            $styleGraph = $columnStyle;
            if ($questionsResponseUserMoyenne[$i][1] < 3) {
                $styleGraph = $columnStyleOrange;
            }
            $this->addResult($section, $i, $arrayColor, $categories, $questionLabels, $questionsResponseUserMoyenne, $questionNumber, $styleGraph);
            $questionNumber = $questionNumber + 1;
            $this->addSautLigne($section, 2);


        }

        $section->addPageBreak();
        $this->addSautLigne($section, 1);
        $LabelChapterGestionDeSoi = $this->formatForWordChapter($LabelChapterGestionDeSoi);
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $LabelChapterGestionDeSoi);

        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('Les critères retenus pour encadrer en ', 'StyleTexte9');
        $textRun->addText('orange ', 'StyleTexte9Orange');
        $textRun->addText('les axes de vigilance, ont été : ', 'StyleTexte9');
        $textRun->addText('toute notation attribuée par une des fonctions qui vous évalue ', 'StyleTexte9Underline');
        $textRun->addText('inférieure ou égale à 2,5', 'StyleTexte9');
        $this->addSautLigne($section, 1);

        //Question de 18 à 34
        $questionNumber = 1;
        for ($i = 18; $i <= 34; $i++) {
            $styleGraph = $columnStyle;
            if (($questionsResponseUserMoyenne[$i][0] < 3) ||
                ($questionsResponseUserMoyenne[$i][1] < 3) ||
                ($questionsResponseUserMoyenne[$i][2] < 3) ||
                ($questionsResponseUserMoyenne[$i][3] < 3) ||
                ($questionsResponseUserMoyenne[$i][4] < 3)) {
                $styleGraph = $columnStyleOrange;
            }
            $this->addResult($section, $i, $arrayColor, $categories, $questionLabels, $questionsResponseUserMoyenne, $questionNumber, $styleGraph);
            $questionNumber = $questionNumber + 1;
            $this->addSautLigne($section, 2);
        }

        $section->addPageBreak();
        $this->addSautLigne($section, 1);
        $LabelChapterGestionDeSonTemps = $this->formatForWordChapter($LabelChapterGestionDeSonTemps);
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $LabelChapterGestionDeSonTemps);

        $textRun = $section->addTextRun('StyleParagrapheText');
        $textRun->addText('Les critères retenus pour encadrer en ', 'StyleTexte9');
        $textRun->addText('orange ', 'StyleTexte9Orange');
        $textRun->addText('les axes de vigilance, ont été : ', 'StyleTexte9');
        $textRun->addText('toute notation attribuée par une des fonctions qui vous évalue ', 'StyleTexte9Underline');
        $textRun->addText('inférieure ou égale à 2,5', 'StyleTexte9');
        $this->addSautLigne($section, 1);

        //Question de 35 à 45
        $questionNumber = 1;
        for ($i = 35; $i <= 45; $i++) {
            $styleGraph = $columnStyle;
            if (($questionsResponseUserMoyenne[$i][0] < 3) ||
                ($questionsResponseUserMoyenne[$i][1] < 3) ||
                ($questionsResponseUserMoyenne[$i][2] < 3) ||
                ($questionsResponseUserMoyenne[$i][3] < 3) ||
                ($questionsResponseUserMoyenne[$i][4] < 3)) {
                $styleGraph = $columnStyleOrange;
            }
            $this->addResult($section, $i, $arrayColor, $categories, $questionLabels, $questionsResponseUserMoyenne, $questionNumber, $styleGraph);
            $questionNumber = $questionNumber + 1;
            $this->addSautLigne($section, 2);
        }

        // Retrouner le fichier à l'utilisateur
        $DocxResultName = "RAPPORT_360_" . date("d-m-Y-H-i-s") . ".docx";
        $phpWord->save($DocxResultName);
        header("Content-Description: File Transfer");
        header("content-type: application/application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header("Content-Disposition: attachment; filename=" . $DocxResultName);
        header("Content-Transfer-Encoding: binary");
        header('Expires: 0');
        header('Cache-Control: no cache');
        header('Pragma: public');
        header('Content-Length: ' . filesize($DocxResultName));
        readfile($DocxResultName);

        exit;
    }

    private function addSautLigne($section, $number)
    {
        for ($i = 1; $i <= $number; $i++) {
            $textrun = $section->addTextRun('StyleParagrapheSautDeLigne');
        }
    }


    private function addAnalyse($section, $marginLeft, $text, $labelPuce, $cocheEquipe, $cocheHierarchie, $cocheTransverse)
    {
        $table = $section->addTable('StyleTableAnalyse');
        $row = $table->addRow();
        $cell = $table->addCell($marginLeft);
        $cell = $table->addCell(300);
        $cell->addText($labelPuce . ' ', 'StyleTexte11');
        $cell = $table->addCell(10400);
        $cell->getStyle()->setGridSpan(2);
        $cell->addText($text, 'StyleTexte11');
        $row = $table->addRow();
        $cell = $table->addCell($marginLeft);
        $cell = $table->addCell(300);
        $cell = $table->addCell(800);
        $cell->addText('avec : ', 'StyleTexte11');
        $cell = $table->addCell(9600);
        $textrun = $cell->addTextRun();
        if ($cocheEquipe == 1) $textrun->addFormField('checkbox')->setValue(true)->setDefault(true); else $textrun->addFormField('checkbox');
        $textrun->addText(' son équipe', 'StyleTexte11');
        $row = $table->addRow();
        $cell = $table->addCell($marginLeft);
        $cell = $table->addCell(300);
        $cell = $table->addCell(800);
        $cell->addText(' ', 'StyleTexte11');
        $cell = $table->addCell(9600);
        $textrun = $cell->addTextRun();
        if ($cocheHierarchie == 1) $textrun->addFormField('checkbox')->setValue(true)->setDefault(true); else $textrun->addFormField('checkbox');
        $textrun->addText(' sa hiérarchie', 'StyleTexte11');
        $row = $table->addRow();
        $cell = $table->addCell($marginLeft);
        $cell = $table->addCell(300);
        $cell = $table->addCell(800);
        $cell->addText(' ', 'StyleTexte11');
        $cell = $table->addCell(9600);
        $textrun = $cell->addTextRun();
        if ($cocheTransverse == 1) $textrun->addFormField('checkbox')->setValue(true)->setDefault(true); else $textrun->addFormField('checkbox');
        $textrun->addText(' les acteurs transverses', 'StyleTexte11');
    }

    private function addAnalyseV2($section, $marginLeft, $text, $labelPuce, $cocheEquipe, $cocheHierarchie, $cocheTransverse)
    {
        $table = $section->addTable('StyleTableAnalyse');
        $row = $table->addRow();
        $cell = $table->addCell($marginLeft);
        $cell = $table->addCell(300);
        $cell->addText($labelPuce. ' ', 'StyleTexte11');
        $cell = $table->addCell(10400);
        $cell->getStyle()->setGridSpan(2);
        $cell->addText($text, 'StyleTexte11');
        $row = $table->addRow();
        $cell = $table->addCell($marginLeft);
        $cell = $table->addCell(300);
        $cell = $table->addCell(2200);
        $cell->addText('du point de vue : ', 'StyleTexte11');
        $cell = $table->addCell(8200);
        $textrun = $cell->addTextRun();
        if ($cocheEquipe == 1) $textrun->addFormField('checkbox')->setValue(true)->setDefault(true); else $textrun->addFormField('checkbox');
        $textrun->addText(' de son équipe', 'StyleTexte11');
        $row = $table->addRow();
        $cell = $table->addCell($marginLeft);
        $cell = $table->addCell(300);
        $cell = $table->addCell(2200);
        $cell->addText(' ', 'StyleTexte11');
        $cell = $table->addCell(8200);
        $textrun = $cell->addTextRun();
        if ($cocheHierarchie == 1) $textrun->addFormField('checkbox')->setValue(true)->setDefault(true); else $textrun->addFormField('checkbox');
        $textrun->addText(' de sa hiérarchie', 'StyleTexte11');
        $row = $table->addRow();
        $cell = $table->addCell($marginLeft);
        $cell = $table->addCell(300);
        $cell = $table->addCell(2200);
        $cell->addText(' ', 'StyleTexte11');
        $cell = $table->addCell(8200);
        $textrun = $cell->addTextRun();
        if ($cocheTransverse == 1) $textrun->addFormField('checkbox')->setValue(true)->setDefault(true); else $textrun->addFormField('checkbox');
        $textrun->addText(' des acteurs transverses', 'StyleTexte11');
    }

    private function addResult($section, $questionNumberRapport, $arrayColor, $categories, $questionLabels, $questionsResponseUser, $questionNumber, $styleGraph)
    {


        $table = $section->addTable('StyleTableVille');
        $table->addRow();
        $cell = $table->addCell(500);
        $cell->addText($questionNumber, 'StyleTexte9', 'StyleParagrapheTabAnalyseNumber');
        $cell = $table->addCell(6000);
        $labelQuestion = $this->formatForWordQuestion($questionLabels[$questionNumberRapport]);
        $labelQuestion = str_replace('16px', '14px', $labelQuestion);
        \PhpOffice\PhpWord\Shared\Html::addHtml($cell, $labelQuestion);
        $table->addCell(500)->addText("", 'StyleTexte11');

        //Quand c'est un float onb ne met qu'un chiffre apres la virgule
        //si c'est un int on le garde ainsi
        $noteMoyenneEquipe = $questionsResponseUser[$questionNumberRapport][0];
        if (is_float($noteMoyenneEquipe)) $noteMoyenneEquipe = number_format($noteMoyenneEquipe, 1,'.',' ');
        $noteMoyenneTransverses = $questionsResponseUser[$questionNumberRapport][1];
        if (is_float($noteMoyenneTransverses)) $noteMoyenneTransverses = number_format($noteMoyenneTransverses, 1,'.',' ');
        $noteMoyenneHierarchie = $questionsResponseUser[$questionNumberRapport][2];
        if (is_float($noteMoyenneHierarchie)) $noteMoyenneHierarchie = number_format($noteMoyenneHierarchie, 1,'.',' ');
        $noteMoyenneAllUsers = $questionsResponseUser[$questionNumberRapport][3];
        if (is_float($noteMoyenneAllUsers)) $noteMoyenneAllUsers = number_format($noteMoyenneAllUsers, 1,'.',' ');
        $noteAutoUser = $questionsResponseUser[$questionNumberRapport][4];
        if (is_float($noteAutoUser)) $noteAutoUser = number_format($noteAutoUser, 1,'.',' ');

        //Option pour ne pas afficher la valeur sur le graph
        //J'ai aussi ajouté un hack dans le code de phpoffice
        // dans vendor/phpoffice/phpword/src/PhpWord/Writer/Word2007/Part/Chart.php
        // private function writeChart
        if($noteMoyenneEquipe == -1) $noteMoyenneEquipe = "#N/A";
        if($noteMoyenneTransverses == -1) $noteMoyenneTransverses = "#N/A";
        if($noteMoyenneHierarchie == -1) $noteMoyenneHierarchie = "#N/A";
        if($noteMoyenneAllUsers == -1) $noteMoyenneAllUsers = "#N/A";
        if($noteAutoUser == -1) $noteAutoUser = "#N/A";

        $chart = $table->addCell(4500, $styleGraph)->addChart('bar', $categories, [$noteMoyenneEquipe,$noteMoyenneTransverses,$noteMoyenneHierarchie,$noteMoyenneAllUsers,$noteAutoUser]);

        $chart->getStyle()->setTitle("360-GRAPH");
        $chart->getStyle()->setWidth(Converter::inchToEmu(3));
        $chart->getStyle()->setHeight(Converter::inchToEmu(2));
        $chart->getStyle()->setShowGridX(false);
        $chart->getStyle()->setShowGridY(true);
        $chart->getStyle()->setShowAxisLabels(true);
        $chart->getStyle()->setShowLegend(false);
        $chart->getStyle()->setColors($arrayColor);
        $chart->getStyle()->setDataLabelOptions(['showVal' => true,
            'showLegendKey' => false, //show the cart legend
            'showSerName' => false, // series name
            'showPercent' => false,
            'showLeaderLines' => false,
            'showBubbleSize' => false,
            'showCatName' => false,]);
    }

    private function addLignChapter($table,$labelChapter)
    {
        $labelChapter = $this->formatForWordChapter($labelChapter);
        //var_dump($labelChapter);
        $row = $table->addRow();
        $cell = $table->addCell(7000);
        \PhpOffice\PhpWord\Shared\Html::addHtml($cell, $labelChapter);
        $cell->getStyle()->setBorderTopStyle('single');
        $cell->getStyle()->setBorderTopSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setGridSpan(2);
        $cell = $table->addCell(990);
        $cell->addText('', 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderTopStyle('single');
        $cell->getStyle()->setBorderTopSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setGridSpan(3);
        $cell = $table->addCell(990);
        $cell->addText('', 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderTopStyle('single');
        $cell->getStyle()->setBorderTopSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setGridSpan(3);
        $cell = $table->addCell(990);
        $cell->addText('', 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderTopStyle('single');
        $cell->getStyle()->setBorderTopSize(10);
        $cell->getStyle()->setBorderRightColor('ffffff');
        $cell->getStyle()->setBorderRightStyle(0);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setGridSpan(3);
    }

    private function addLignQuestion($table, $questionNumber, $questionLabel, $notes)
    {
        //Si note = null on coche rien
        //Si note inferieure à 0,5 on coche 0
        //Si note inferieure à 1 on coche 0,5
        //...
        //Si note inferieure à 4 on coche 3,5
        $row = $table->addRow(null, array('cantSplit' => true));
        $cell = $table->addCell(100);
        $cell->addText($questionNumber.'.', 'StyleTexte9','StyleParagrapheTabRecapNumber');
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setBorderRightColor('ffffff');
        $cell->getStyle()->setBorderRightSize(1);

        $cell = $table->addCell(6900);
        $labelQuestion = $this->formatForWordQuestion($questionLabel->label);
        \PhpOffice\PhpWord\Shared\Html::addHtml($cell, $labelQuestion);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes == 4) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderRightColor('ffffff');
        $cell->getStyle()->setBorderRightSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes < 4 && $notes >= 3.5) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderRightColor('ffffff');
        $cell->getStyle()->setBorderRightSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes < 3.5 && $notes >= 3) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes < 3 && $notes >= 2.5) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderRightColor('ffffff');
        $cell->getStyle()->setBorderRightSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes < 2.5 && $notes >= 2) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderRightColor('ffffff');
        $cell->getStyle()->setBorderRightSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes < 2 && $notes >= 1.5) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes < 1.5 && $notes >= 1) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderRightColor('ffffff');
        $cell->getStyle()->setBorderRightSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes < 1 && $notes >= 0.5) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderRightColor('ffffff');
        $cell->getStyle()->setBorderRightSize(10);
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');

        $cell = $table->addCell(330);
        $textCoche = "";
        if($notes < 0.5 && $notes >= 0) $textCoche = "X";
        $cell->addText($textCoche, 'StyleTexte10Coche','StyleParagrapheCoche');
        $cell->getStyle()->setBorderBottomStyle('dashed');
        $cell->getStyle()->setBorderBottomSize(10);
        $cell->getStyle()->setVAlign('center');
    }
    private function formatForWordChapter($label)
    {
        $labelFormated = str_replace('19px', '16px', $label);

        return $labelFormated;
    }

    private function formatForWordQuestion($label)
    {
        $labelFormated = str_replace('9px', '12px', $label);
        $labelFormated = str_replace('14px', '12px', $label);
        $labelFormated = str_replace('<br>', '<br/>', $labelFormated);

        return $labelFormated;
    }
    function calculateMedian($arr) {
        $count = count($arr); //total numbers in array
        $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
        if($count % 2) { // odd number, middle is the median
            $median = $arr[$middleval];
        } else { // even number, calculate avg of 2 medians
            $low = $arr[$middleval];
            $high = $arr[$middleval+1];
            $median = (($low+$high)/2);
        }
        return (int)$median;
    }
    function calculateMoyenne($arr) {
        $sum = array_sum($arr);
        $average = $sum / count($arr);

        return $average;
    }
}
