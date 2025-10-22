<?php

namespace Appy\Src\Controller;

require_once __DIR__ . '/../Library/phplot.php';

use Appy\Src\Core\Appy;
use Appy\Src\Entity\Quiz;
use Appy\Src\Entity\QuizReportBarometre;
use Appy\Src\Library\PHPlot;
use Appy\Src\Repository\GroupesRepository;
use Appy\Src\Repository\QuizCriteresBarometreRepository;
use Appy\Src\Repository\QuizReportBarometreRepository;
use Appy\Src\Repository\QuizUserResponseRepository;
use Appy\Src\Repository\ResponseQuizCriteresBarometreRepository;
use Appy\Src\Repository\UsersRepository;
use Appy\Src\Repository\QuizRepository;
use Appy\Src\Repository\QuizUserRepository;
use Appy\Src\Repository\TemplateQuizQuestionsRepository;
use Appy\Src\Repository\QuizQuestionRepository;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Area;
use PhpOffice\PhpPresentation\Shape\Drawing\File;
use PhpOffice\PhpPresentation\Shape\Line;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Shadow;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Language;

class ReportBarometreController extends \Appy\Src\Core\Controller
{
    private function getData($quizId, &$quiz, &$string, &$quizQuestions, &$ChaptersInfo, &$quizUsers, &$quizUserResponsesByChapter,
                             &$resultByChapter, &$resultByChapterQuestion, &$tabChapterCritereChoix, &$tabCritereChoixNegative,
                             &$critere1Values, &$critere2Values, &$critere3Values, &$critere4Values) {

        $quizRepository = new QuizRepository();
        $quizUserRepository = new QuizUserRepository();
        $quizQuestionRepository = new QuizQuestionRepository();
        $quizUserResponseRepository = new QuizUserResponseRepository();
        $quizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
        $responseQuizCriteresBarometreRepository = new ResponseQuizCriteresBarometreRepository();

        //On recupere les infos du quiz
        $quiz = $quizRepository->getQuizById($quizId);

        //Récuperaton des répondants
        $criteres = ['status' => 'FINISH'];
        $quizUsers = $quizUserRepository->getQuizUsersByQuizId($quiz->id, $criteres, "id");

        //Recueration des chapitres
        $quizChapters = $quizQuestionRepository->getChapterBarometre($quiz->id);
        $chapterNumber = 1;
        foreach ($quizChapters as $quizChapter) {
            //On ne prend par les chapitres vides
            if (trim($quizChapter->label) != '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold"></div>') {
                $ChaptersInfo[$chapterNumber]['label'] = $quizChapter->label;
                $ChaptersInfo[$chapterNumber]['number'] = $chapterNumber;
                $chapterNumber = $chapterNumber + 1;
            }
        }

        //Recuperation du libellé des questions
        $quizQuestions = $quizQuestionRepository->getQuestionsByQuizIdAndType($quizId, 'INPUT-RADIO');

        //Recuperation des réponses
        $critereRecherche = [];
        $critereRecherche['quizId'] = $quiz->id;
        $critereRecherche['questionReportOrderPlage'] = "(1,2,3,4,5,6,7,8,9,10)";
        $quizUserResponsesByChapter[1] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(11,12,13,14,15,16,17,18,19,20)";
        $quizUserResponsesByChapter[2] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(21,22,23,24,25,26,27,28,29,30)";
        $quizUserResponsesByChapter[3] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(31,32,33,34,35,36,37,38,39,40)";
        $quizUserResponsesByChapter[4] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(41,42,43,44,45,46,47,48,49,50)";
        $quizUserResponsesByChapter[5] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(51,52,53,54,55,56,57,58,59,60)";
        $quizUserResponsesByChapter[6] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(61,62,63,64,65,66,67,68,69,70)";
        $quizUserResponsesByChapter[7] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(71,72,73,74,75,76,77,78,79,80)";
        $quizUserResponsesByChapter[8] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        if (array_key_exists('9', $ChaptersInfo)) {
            $critereRecherche['questionReportOrderPlage'] = "(81,82,83,84,85,86,87,88,89,90)";
            $quizUserResponsesByChapter[9] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        }
        if (array_key_exists('10', $ChaptersInfo)) {
            $critereRecherche['questionReportOrderPlage'] = "(91,92,93,94,95,96,97,98,99,100)";
            $quizUserResponsesByChapter[10] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        }

        //Recuperation des réponses aux critère
        $critereRecherche = [];
        $critereRecherche['quizId'] = $quiz->id;
        $responsesQuizCritereBarometre = $responseQuizCriteresBarometreRepository->getResponseQuizCritereBarometre($critereRecherche, "user");

        //Transformation des valeurs des critère par rapport à la table quizcriterebarometre
        $quizCriteresBarometre = $quizCriteresBarometreRepository->getCriteresByQuizId($quiz->id);
        foreach ($responsesQuizCritereBarometre as $responseQuizCritereBarometre) {
            if(isset($quizCriteresBarometre[1])) {
                $value = $responseQuizCritereBarometre->responseCritere1;
                $valueTransform = "";
                if($value == "A") $valueTransform = $quizCriteresBarometre[1]->choix1;
                elseif($value == "B") $valueTransform = $quizCriteresBarometre[1]->choix2;
                elseif($value == "C") $valueTransform = $quizCriteresBarometre[1]->choix3;
                elseif($value == "D") $valueTransform = $quizCriteresBarometre[1]->choix4;
                elseif($value == "E") $valueTransform = $quizCriteresBarometre[1]->choix5;
                elseif($value == "F") $valueTransform = $quizCriteresBarometre[1]->choix6;
                elseif($value == "G") $valueTransform = $quizCriteresBarometre[1]->choix7;
                elseif($value == "H") $valueTransform = $quizCriteresBarometre[1]->choix8;
                elseif($value == "I") $valueTransform = $quizCriteresBarometre[1]->choix9;
                elseif($value == "J") $valueTransform = $quizCriteresBarometre[1]->choix10;
                $critere1Values[] = $valueTransform;
            }
            if(isset($quizCriteresBarometre[2])) {
                $value = $responseQuizCritereBarometre->responseCritere2;
                $valueTransform = "";
                if($value == "A") $valueTransform = $quizCriteresBarometre[2]->choix1;
                elseif($value == "B") $valueTransform = $quizCriteresBarometre[2]->choix2;
                elseif($value == "C") $valueTransform = $quizCriteresBarometre[2]->choix3;
                elseif($value == "D") $valueTransform = $quizCriteresBarometre[2]->choix4;
                elseif($value == "E") $valueTransform = $quizCriteresBarometre[2]->choix5;
                elseif($value == "F") $valueTransform = $quizCriteresBarometre[2]->choix6;
                elseif($value == "G") $valueTransform = $quizCriteresBarometre[2]->choix7;
                elseif($value == "H") $valueTransform = $quizCriteresBarometre[2]->choix8;
                elseif($value == "I") $valueTransform = $quizCriteresBarometre[2]->choix9;
                elseif($value == "J") $valueTransform = $quizCriteresBarometre[2]->choix10;
                $critere2Values[] = $valueTransform;
            }
            if(isset($quizCriteresBarometre[3])) {
                $value = $responseQuizCritereBarometre->responseCritere3;
                $valueTransform = "";
                if($value == "A") $valueTransform = $quizCriteresBarometre[3]->choix1;
                elseif($value == "B") $valueTransform = $quizCriteresBarometre[3]->choix2;
                elseif($value == "C") $valueTransform = $quizCriteresBarometre[3]->choix3;
                elseif($value == "D") $valueTransform = $quizCriteresBarometre[3]->choix4;
                elseif($value == "E") $valueTransform = $quizCriteresBarometre[3]->choix5;
                elseif($value == "F") $valueTransform = $quizCriteresBarometre[3]->choix6;
                elseif($value == "G") $valueTransform = $quizCriteresBarometre[3]->choix7;
                elseif($value == "H") $valueTransform = $quizCriteresBarometre[3]->choix8;
                elseif($value == "I") $valueTransform = $quizCriteresBarometre[3]->choix9;
                elseif($value == "J") $valueTransform = $quizCriteresBarometre[3]->choix10;
                $critere3Values[] = $valueTransform;
            }
            if(isset($quizCriteresBarometre[4])) {
                $value = $responseQuizCritereBarometre->responseCritere4;
                $valueTransform = "";
                if($value == "A") $valueTransform = $quizCriteresBarometre[4]->choix1;
                elseif($value == "B") $valueTransform = $quizCriteresBarometre[4]->choix2;
                elseif($value == "C") $valueTransform = $quizCriteresBarometre[4]->choix3;
                elseif($value == "D") $valueTransform = $quizCriteresBarometre[4]->choix4;
                elseif($value == "E") $valueTransform = $quizCriteresBarometre[4]->choix5;
                elseif($value == "F") $valueTransform = $quizCriteresBarometre[4]->choix6;
                elseif($value == "G") $valueTransform = $quizCriteresBarometre[4]->choix7;
                elseif($value == "H") $valueTransform = $quizCriteresBarometre[4]->choix8;
                elseif($value == "I") $valueTransform = $quizCriteresBarometre[4]->choix9;
                elseif($value == "J") $valueTransform = $quizCriteresBarometre[4]->choix10;
                $critere4Values[] = $valueTransform;
            }
        }

        //Init des index du tableau
        $j = 1;
        $questionId = 0;
        foreach ($ChaptersInfo as $Chapter) {
            $resultByChapter[$j]['nbEfficient'] =  0;
            $resultByChapter[$j]['nbPeuDegrade'] =  0;
            $resultByChapter[$j]['nbDegrade'] =  0;
            $resultByChapter[$j]['nbFortDegrade'] =  0;
            $resultByChapter[$j]['nbTotal'] =  0;
            $resultByChapter[$j]['percentEfficient'] =  0;
            $resultByChapter[$j]['percentPeuDegrade'] =  0;
            $resultByChapter[$j]['percentDegrade'] =  0;
            $resultByChapter[$j]['percentFortDegrade'] =  0;

            foreach ($quizUserResponsesByChapter[$j] as $quizUserResponse) {
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbEfficient'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbPeuDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbFortDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbTotal'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['percentEfficient'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['percentPeuDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['percentDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['percentFortDegrade'] =  0;
            }
            $j = $j + 1;
        }

        //On boucle sur chaque chapitre puis sur chaque réponses des user
        $chapterId = 1;
        foreach ($ChaptersInfo as $Chapter) {

            foreach ($quizUserResponsesByChapter[$chapterId] as $quizUserResponse) {

                //On construit le tableau des resultats par chapitre et par question
                if ($chapterId == 2) {
                    if ($quizUserResponse->value == 'TAFV') {
                        $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbFortDegrade'] = $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbFortDegrade'] + 1;
                        $resultByChapter[$chapterId]['nbFortDegrade'] = $resultByChapter[$chapterId]['nbFortDegrade'] + 1;
                    } elseif ($quizUserResponse->value == 'PV') {
                        $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbDegrade'] = $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbDegrade'] + 1;
                        $resultByChapter[$chapterId]['nbDegrade'] = $resultByChapter[$chapterId]['nbDegrade'] + 1;
                    } elseif ($quizUserResponse->value == 'PPV') {
                        $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbPeuDegrade'] = $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbPeuDegrade'] + 1;
                        $resultByChapter[$chapterId]['nbPeuDegrade'] = $resultByChapter[$chapterId]['nbPeuDegrade'] + 1;
                    } elseif ($quizUserResponse->value == 'PDTV') {
                        $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbEfficient'] =  $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbEfficient'] + 1;
                        $resultByChapter[$chapterId]['nbEfficient'] =  $resultByChapter[$chapterId]['nbEfficient'] + 1;
                    }
                } else {
                    if ($quizUserResponse->value == 'TAFV') {
                        $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbEfficient'] =  $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbEfficient'] + 1;
                        $resultByChapter[$chapterId]['nbEfficient'] =  $resultByChapter[$chapterId]['nbEfficient'] + 1;
                    } elseif ($quizUserResponse->value == 'PV') {
                        $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbPeuDegrade'] = $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbPeuDegrade'] + 1;
                        $resultByChapter[$chapterId]['nbPeuDegrade'] = $resultByChapter[$chapterId]['nbPeuDegrade'] + 1;
                    } elseif ($quizUserResponse->value == 'PPV') {
                        $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbDegrade'] = $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbDegrade'] + 1;
                        $resultByChapter[$chapterId]['nbDegrade'] = $resultByChapter[$chapterId]['nbDegrade'] + 1;
                    } elseif ($quizUserResponse->value == 'PDTV') {
                        $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbFortDegrade'] = $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbFortDegrade'] + 1;
                        $resultByChapter[$chapterId]['nbFortDegrade'] = $resultByChapter[$chapterId]['nbFortDegrade'] + 1;
                    }
                }
                $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbTotal'] =  $resultByChapterQuestion[$chapterId][$quizUserResponse->questionId]['nbTotal'] + 1;
                $resultByChapter[$chapterId]['nbTotal'] =  $resultByChapter[$chapterId]['nbTotal'] + 1;
            }
            $chapterId++;
        }

        //Calcul des statistique par chapitre
        foreach($resultByChapter as $keyChapitre => $value) {
            /*var_dump("chapitre " . $keyChapitre . "<br>");
            var_dump($value['nbEfficient'] . "<br>");
            var_dump($value['nbPeuDegrade'] . "<br>");
            var_dump($value['nbDegrade'] . "<br>");
            var_dump($value['nbFortDegrade'] . "<br>");
            var_dump($value['nbTotal'] . "<br>");*/
            $resultByChapter[$keyChapitre]['percentEfficient'] = number_format($value['nbEfficient'] * 100 / $value['nbTotal'], 0, ',', '');
            $resultByChapter[$keyChapitre]['percentPeuDegrade'] = number_format($value['nbPeuDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
            $resultByChapter[$keyChapitre]['percentDegrade'] = number_format($value['nbDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
            $resultByChapter[$keyChapitre]['percentFortDegrade'] = number_format($value['nbFortDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
            /*var_dump($resultByChapter[$keyChapitre]['percentEfficient'] . "<br>");
            var_dump($resultByChapter[$keyChapitre]['percentPeuDegrade'] . "<br>");
            var_dump($resultByChapter[$keyChapitre]['percentDegrade'] . "<br>");
            var_dump($resultByChapter[$keyChapitre]['percentFortDegrade'] . "<br>");*/
        }

        //Calcul des statistique par chapitre/question
        foreach($resultByChapterQuestion as $keyChapitre => $value) {
            //var_dump("chapitre " . $keyChapitre . "<br>");
            if (is_array($value)) {
                foreach ($value as $keyQuestion => $value) {
                    /*var_dump("question " . $keyQuestion . "<br>");
                    var_dump($value['nbEfficient'] . "<br>");
                    var_dump($value['nbPeuDegrade'] . "<br>");
                    var_dump($value['nbDegrade'] . "<br>");
                    var_dump($value['nbFortDegrade'] . "<br>");
                    var_dump($value['nbTotal'] . "<br>");*/
                    $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient'] = number_format($value['nbEfficient'] * 100 / $value['nbTotal'], 0, ',', '');
                    $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade'] = number_format($value['nbPeuDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
                    $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade'] = number_format($value['nbDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
                    $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade'] = number_format($value['nbFortDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
                    /*var_dump($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient'] . "<br>");
                    var_dump($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade'] . "<br>");
                    var_dump($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade'] . "<br>");
                    var_dump($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade'] . "<br>");*/
                }
            }
        }


        //Recuperation des critères et valeur
        // construction d'un tableau de critere puis valeurs
        $QuizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
        $quizCriteresBarometre = $QuizCriteresBarometreRepository->getCriteresByQuizId($quiz->id);

        $tabCritereChoix = [];
        foreach($quizCriteresBarometre as $keyCritere => $value) {
            if(isset($value->titre)) {
                if($value->choix1) $tabCritereChoix[$value->titre]['A'] = $value->choix1;
                if($value->choix2) $tabCritereChoix[$value->titre]['B'] = $value->choix2;
                if($value->choix3) $tabCritereChoix[$value->titre]['C'] = $value->choix3;
                if($value->choix4) $tabCritereChoix[$value->titre]['D'] = $value->choix4;
                if($value->choix5) $tabCritereChoix[$value->titre]['E'] = $value->choix5;
                if($value->choix6) $tabCritereChoix[$value->titre]['F'] = $value->choix6;
                if($value->choix7) $tabCritereChoix[$value->titre]['G'] = $value->choix7;
                if($value->choix8) $tabCritereChoix[$value->titre]['H'] = $value->choix8;
                if($value->choix9) $tabCritereChoix[$value->titre]['I'] = $value->choix9;
                if($value->choix10) $tabCritereChoix[$value->titre]['J'] = $value->choix10;
            }
        }

        //On boucle sur les chapitre puis les criteres puis les choix
        //Pour chacun on parcours les questions
        for ($ChapterId = 1; $ChapterId <= 10; $ChapterId++) {
            $indexCritere = 1;
            foreach($tabCritereChoix as $keyCritere => $tabChoix) {
                foreach($tabChoix as $keyChoix => $value) {

                    $plageQuestion = "";
                    if($ChapterId == 1) $plageQuestion = "(1,2,3,4,5,6,7,8,9,10)";
                    elseif($ChapterId == 2) $plageQuestion = "(11,12,13,14,15,16,17,18,19,20)";
                    elseif($ChapterId == 3) $plageQuestion = "(21,22,23,24,25,26,27,28,29,30)";
                    elseif($ChapterId == 4) $plageQuestion = "(31,32,33,34,35,36,37,38,39,40)";
                    elseif($ChapterId == 5) $plageQuestion = "(41,42,43,44,45,46,47,48,49,50)";
                    elseif($ChapterId == 6) $plageQuestion = "(51,52,53,54,55,56,57,58,59,60)";
                    elseif($ChapterId == 7) $plageQuestion = "(61,62,63,64,65,66,67,68,69,70)";
                    elseif($ChapterId == 8) $plageQuestion = "(71,72,73,74,75,76,77,78,79,80)";
                    elseif($ChapterId == 9) $plageQuestion = "(81,82,83,84,85,86,87,88,89,90)";
                    elseif($ChapterId == 10) $plageQuestion = "(91,92,93,94,95,96,97,98,99,100)";

                    $critereRecherche = [];
                    $critereRecherche['quizId'] = $quiz->id;
                    $critereRecherche['questionReportOrderPlage'] = $plageQuestion;
                    $critereRecherche['column-critere-name'] = "response_critere" . $indexCritere;
                    $critereRecherche['choix'] = $keyChoix;

                    if ($ChapterId == 9) {
                        if(array_key_exists('9', $ChaptersInfo)) {
                            $result = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value] = $result;
                        }
                    }
                    elseif ($ChapterId == 10) {
                        if( array_key_exists('10', $ChaptersInfo)) {
                            $result = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value] = $result;
                        }
                    }
                    else {
                        $result = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value] = $result;
                    }




                    if(isset($tabChapterCritereChoix[$ChapterId][$keyCritere][$value])) {
                        /*
                                                if ($ChapterId == 1 && $keyCritere == "ANCIENNETÉ" && $keyChoix == "D") {
                                                    var_dump($ChapterId . '<br>');
                                                    var_dump($indexCritere . '<br>');
                                                    var_dump($keyChoix . '<br>');
                                                    var_dump($result);
                                                    var_dump('<br>');
                                                }
                        */
                        $nbEfficient = 0;
                        $nbPeuDegrade = 0;
                        $nbDegrade = 0;
                        $nbFortDegrade = 0;
                        $totalReponses = 0;
                        $valueTotalByUser = 0;
                        $quizUserId = 0;
                        $nombreFortsRisques = 0;
                        $nombreRisques = 0;
                        $nombrePeuDeRisque = 0;
                        $nombreSansRisque = 0;
                        $valueCoef = 0;
                        $lign = 1;

                        foreach ($result as $quizUserResponse) {

                            //Nbre de fois où un item à reçu des réponses :
                            //  "pas du tout vrai" = fort dégradé ; "Plutôt pas vrai" = dégradé ; "plutôt vrai" = peu dégradé; "tout à fait vrai" = efficient.
                            // (Nota -  Pour l'item 2, le principe est inversé : "pas du tout vrai" = efficient ; "plutôt pas vrai" = peu dégradé;
                            if ($ChapterId == 2) {
                                if ($quizUserResponse->value == 'TAFV') $nbFortDegrade = $nbFortDegrade + 1;
                                elseif ($quizUserResponse->value == 'PV') $nbDegrade = $nbDegrade + 1;
                                elseif ($quizUserResponse->value == 'PPV') $nbPeuDegrade = $nbPeuDegrade + 1;
                                elseif ($quizUserResponse->value == 'PDTV') $nbEfficient = $nbEfficient + 1;
                            } else {
                                if ($quizUserResponse->value == 'TAFV') $nbEfficient = $nbEfficient + 1;
                                elseif ($quizUserResponse->value == 'PV') $nbPeuDegrade = $nbPeuDegrade + 1;
                                elseif ($quizUserResponse->value == 'PPV') $nbDegrade = $nbDegrade + 1;
                                elseif ($quizUserResponse->value == 'PDTV') $nbFortDegrade = $nbFortDegrade + 1;
                            }

                            $totalReponses = $totalReponses + 1;

                            //Si on traite un nouveau user
                            //On remet les compteur à zéro
                            if ($lign != 1 && $quizUserResponse->quizUserId != $quizUserId) {

                                //On stocke le resultat
                                //En fonction de la valeur on somme dans la bonne variable de risque
                                if ($valueTotalByUser >= $quiz->risqueDeFr) $nombreFortsRisques = $nombreFortsRisques + 1;
                                elseif ($valueTotalByUser >= $quiz->risqueDeR && $valueTotalByUser <= $quiz->risqueAR) $nombreRisques = $nombreRisques + 1;
                                elseif ($valueTotalByUser >= $quiz->risqueDePdr && $valueTotalByUser <= $quiz->risqueAPdr) $nombrePeuDeRisque = $nombrePeuDeRisque + 1;
                                elseif ($valueTotalByUser <= $quiz->risqueASr) $nombreSansRisque = $nombreSansRisque + 1;

                                //On remet à 0 le total par user
                                $valueTotalByUser = 0;

                                if ($ChapterId == 2) {
                                    if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefPdtv;
                                    elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPpv;
                                    elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPv;
                                    elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefTafv;

                                } else {
                                    if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefTafv;
                                    elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPv;
                                    elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPpv;
                                    elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefPdtv;
                                }

                                $valueTotalByUser = $valueTotalByUser + $valueCoef;

                            } else {
                                //On convertir la string en valeur selon le paramétrage
                                //on inverse la logique pour le chapitre
                                if ($ChapterId == 2) {
                                    if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefPdtv;
                                    elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPpv;
                                    elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPv;
                                    elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefTafv;
                                } else {
                                    if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefTafv;
                                    elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPv;
                                    elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPpv;
                                    elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefPdtv;
                                }

                                $valueTotalByUser = $valueTotalByUser + $valueCoef;
                            }

                            $quizUserId = $quizUserResponse->quizUserId;
                            $lign = $lign + 1;

                        }

                        if($result) {
                            //On stocke le resultat du dernier user
                            if ($valueTotalByUser >= $quiz->risqueDeFr) $nombreFortsRisques = $nombreFortsRisques + 1;
                            elseif ($valueTotalByUser >= $quiz->risqueDeR && $valueTotalByUser <= $quiz->risqueAR) $nombreRisques = $nombreRisques + 1;
                            elseif ($valueTotalByUser >= $quiz->risqueDePdr && $valueTotalByUser <= $quiz->risqueAPdr) $nombrePeuDeRisque = $nombrePeuDeRisque + 1;
                            elseif ($valueTotalByUser <= $quiz->risqueASr) $nombreSansRisque = $nombreSansRisque + 1;
                        }

                        //Caulcul de l'etat des item
                        $totalReponses = $nbEfficient + $nbPeuDegrade + $nbDegrade + $nbFortDegrade;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbTotal'] = $totalReponses;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbEfficient'] = $nbEfficient;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbPeuDegrade'] = $nbPeuDegrade;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbDegrade'] = $nbDegrade;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbFortDegrade'] = $nbFortDegrade;
                        if ($totalReponses == 0) {
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentEfficient'] = 0;
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentPeuDegrade'] = 0;
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentDegrade'] = 0;
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentFortDegrade'] = 0;
                        } else {
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentEfficient'] = $nbEfficient / $totalReponses;
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentPeuDegrade'] = $nbPeuDegrade / $totalReponses;
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentDegrade'] = $nbDegrade / $totalReponses;
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentFortDegrade'] = $nbFortDegrade / $totalReponses;

                        }

                        //Caulcul de l'impact
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbFortsRisques'] = $nombreFortsRisques;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbRisques'] = $nombreRisques;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbPeuDeRisque'] = $nombrePeuDeRisque;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbSansRisque'] = $nombreSansRisque;

                        $nombreDetousLesRisquesCalculated = $nombreFortsRisques + $nombreRisques + $nombrePeuDeRisque + $nombreSansRisque;
                        if($nombreDetousLesRisquesCalculated == 0) $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentFortsRisques'] = 0;
                        else $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentFortsRisques'] = $nombreFortsRisques / $nombreDetousLesRisquesCalculated;
                        if($nombreDetousLesRisquesCalculated == 0) $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentRisques'] = 0;
                        else $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentRisques'] = $nombreRisques / $nombreDetousLesRisquesCalculated;
                        if($nombreDetousLesRisquesCalculated == 0) $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentPeuDeRisque'] = 0;
                        else $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentPeuDeRisque'] = $nombrePeuDeRisque / $nombreDetousLesRisquesCalculated;
                        if($nombreDetousLesRisquesCalculated == 0) $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentSansRisque'] = 0;
                        else $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentSansRisque'] = $nombreSansRisque / $nombreDetousLesRisquesCalculated;

                        /*
                                                if ($ChapterId == 1) {
                                                    var_dump($keyCritere . '<br>');
                                                    var_dump($keyChoix . '<br>');
                                                    var_dump($totalReponses . '<br>');
                                                    var_dump($nbEfficient . '<br>');
                                                    var_dump($nbPeuDegrade . '<br>');
                                                    var_dump($nbDegrade . '<br>');
                                                    var_dump($nbFortDegrade . '<br>');
                                                    var_dump($nombreFortsRisques . '<br>');
                                                    var_dump($nombreRisques . '<br>');
                                                    var_dump($nombrePeuDeRisque . '<br>');
                                                    var_dump($nombreSansRisque . '<br>');
                                                    var_dump($tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentEfficient'] . '<br>');
                                                    var_dump($tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentPeuDegrade'] . '<br>');
                                                    var_dump($tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentDegrade'] . '<br>');
                                                    var_dump($tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentFortDegrade'] . '<br>');
                                                    var_dump($tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentFortsRisques'] . '<br>');
                                                    var_dump($tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentRisques'] . '<br>');
                                                    var_dump($tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentPeuDeRisque'] . '<br>');
                                                    var_dump($tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentSansRisque'] . '<br>');
                                                    var_dump('<br>');
                                                }
                        */

                    }
                }
                $indexCritere++;
            }
        }

        //comptage des valeur negative par critere, choix et user
        //On boucle sur les critre puis les choix
        $indexCritere = 1;
        foreach($tabCritereChoix as $keyCritere => $tabChoix) {

            //On remet à 0 le nombre de reponse negative par user
            $valueNegativeQuestionsByUser = 0;

            foreach($tabChoix as $keyChoix => $value) {

                $critereRecherche = [];
                $critereRecherche['quizId'] = $quiz->id;
                $critereRecherche['column-critere-name'] = "response_critere" . $indexCritere;
                $critereRecherche['choix'] = $keyChoix;

                $result = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
                //$tabCritereChoixNegative[$keyCritere][$value] = $result;

                if(isset($result)) {

                    //var_dump($keyCritere . '<br>');
                    //var_dump($keyChoix . '<br>');

                    $quizUserId = 0;
                    $valueNegativeQuestionsByUser = 0;
                    $lign = 1;
                    foreach ($result as $quizUserResponse) {

                        //Si on traite un nouveau user
                        //On remet les compteur à zéro
                        if ($lign != 1 && $quizUserResponse->quizUserId != $quizUserId) {
                            //On stock la valeur pour chaque user
                            $tabCritereChoixNegative[$keyCritere][$value][$quizUserId] = $valueNegativeQuestionsByUser;
                            //var_dump('user id ' . $quizUserId . '<br>');
                            //var_dump('nb reponse negative : ' . $valueNegativeQuestionsByUser . '<br>');

                            $valueNegativeQuestionsByUser = 0;
                        }

                        //var_dump($quizUserResponse->value . '<br>');
                        //var_dump($quizUserResponse->reportOrder . '<br>');

                        $valueCoef = "";
                        if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefTafv;
                        elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPv;
                        elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPpv;
                        elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefPdtv;


                        //Pour le taux d'expostion on compte le nombre de reponse négative par user
                        //Negative = reponse 0 (tafv) ou 1 (pv) pour le chapitre 2
                        //Negative = reponse 2 (ppv) ou 3 (pdtv) pour les chapitre autre que 2
                        if ($quizUserResponse->reportOrder == 11 || $quizUserResponse->reportOrder == 12 ||
                            $quizUserResponse->reportOrder == 13 || $quizUserResponse->reportOrder == 14 ||
                            $quizUserResponse->reportOrder == 15 || $quizUserResponse->reportOrder == 16 ||
                            $quizUserResponse->reportOrder == 17 || $quizUserResponse->reportOrder == 18 ||
                            $quizUserResponse->reportOrder == 19 || $quizUserResponse->reportOrder == 20) {
                            if (($valueCoef == $quiz->coefTafv) || ($valueCoef == $quiz->coefPv)) {
                                $valueNegativeQuestionsByUser = $valueNegativeQuestionsByUser + 1;
                                //var_dump('negative chapitre 2' . '<br>');
                            }

                        } else {
                            if (($valueCoef== $quiz->coefPpv) || ($valueCoef == $quiz->coefPdtv)) {
                                $valueNegativeQuestionsByUser = $valueNegativeQuestionsByUser + 1;
                                //var_dump('negative' . '<br>');
                            }
                        }

                        $quizUserId = $quizUserResponse->quizUserId;
                        $lign++;

                    }

                    //On stock la valeur du dernier user
                    $tabCritereChoixNegative[$keyCritere][$value][$quizUserId] = $valueNegativeQuestionsByUser;
                    //var_dump('user id ' . $quizUserId . '<br>');
                    //var_dump('nb reponse negative : ' . $valueNegativeQuestionsByUser . '<br>');
                }
            }
            $indexCritere++;
        }


        /* On boucle pour compter calculer les 4 range de valeurs negative pour chaque couple critere / valeur */
        foreach ($tabCritereChoixNegative as $keyCritere => $tabChoix) {
            //var_dump($keyCritere . '<br>');
            foreach ($tabChoix as $keyChoix => $tabUser) {
                //var_dump($keyChoix . '<br>');
                $nombreTauxExpositionDeFortsRisques = 0;
                $nombreTauxExpositionDesRisques = 0;
                $nombreTauxExpositionPeuDeRisque = 0;
                $nombreTauxExpositionSansRisque = 0;
                //Pour chaque user on regarde la valeur du nb de réponse negative par rapport aux tranches de coef et on somme dans la bonne variable de risque
                foreach ($tabUser as $keyUser => $value) {
                    //var_dump($tabUser);
                    //var_dump($keyUser . '<br>');
                    //var_dump($value . '<br>');

                    if ($value >= $quiz->tauxDeFr) $nombreTauxExpositionDeFortsRisques = $nombreTauxExpositionDeFortsRisques + 1;
                    elseif ($value >= $quiz->tauxDeR && $value <= $quiz->tauxAR) $nombreTauxExpositionDesRisques = $nombreTauxExpositionDesRisques + 1;
                    elseif ($value >= $quiz->tauxDePdr && $value <= $quiz->tauxAPdr) $nombreTauxExpositionPeuDeRisque = $nombreTauxExpositionPeuDeRisque + 1;
                    elseif ($value <= $quiz->tauxASr) $nombreTauxExpositionSansRisque = $nombreTauxExpositionSansRisque + 1;
                }

                $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionDeFortsRisques'] = $nombreTauxExpositionDeFortsRisques;
                $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionDesRisques'] = $nombreTauxExpositionDesRisques;
                $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionPeuDeRisque'] = $nombreTauxExpositionPeuDeRisque;
                $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionSansRisque'] = $nombreTauxExpositionSansRisque;
                $nombreTauxExpositionDetousLesRisquesCalculated = $nombreTauxExpositionDeFortsRisques + $nombreTauxExpositionDesRisques + $nombreTauxExpositionPeuDeRisque + $nombreTauxExpositionSansRisque;

                $tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionDeFortsRisques'] = $nombreTauxExpositionDeFortsRisques / $nombreTauxExpositionDetousLesRisquesCalculated;
                $tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionDesRisques'] = $nombreTauxExpositionDesRisques / $nombreTauxExpositionDetousLesRisquesCalculated;
                $tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionPeuDeRisque'] = $nombreTauxExpositionPeuDeRisque / $nombreTauxExpositionDetousLesRisquesCalculated;
                $tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionSansRisque'] = $nombreTauxExpositionSansRisque / $nombreTauxExpositionDetousLesRisquesCalculated;

            }
        }

        /*
        foreach ($tabCritereChoixNegative as $keyCritere => $tabChoix) {
            var_dump($keyCritere . '<br>');
            foreach ($tabChoix as $keyChoix => $value) {
                var_dump($keyChoix . '<br>');
                //var_dump($value);
                var_dump($tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionDeFortsRisques'] . '<br>');
                var_dump($tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionDesRisques'] . '<br>');
                var_dump($tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionPeuDeRisque'] . '<br>');
                var_dump($tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionSansRisque'] . '<br>');
                var_dump($tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionDeFortsRisques'] . '<br>');
                var_dump($tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionDesRisques'] . '<br>');
                var_dump($tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionPeuDeRisque'] . '<br>');
                var_dump($tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionSansRisque'] . '<br>');
            }
        }
*/
    }

    public function generateExel()
    {
        $quizId = $_GET['quizId'];

        $ChaptersInfo = [];
        $quizUserResponsesByChapter = [];
        $resultByChapterQuestion = [];
        $resultByChapter = [];
        $tabChapterCritereChoix = [];
        $tabCritereChoixNegative = [];
        $critere1Values = [];
        $critere2Values = [];
        $critere3Values = [];
        $critere4Values = [];
        $quizUsers = null;
        $quiz = null;
        $this->getData($quizId, $quiz, $string, $quizQuestions, $ChaptersInfo, $quizUsers, $quizUserResponsesByChapter,
            $resultByChapter, $resultByChapterQuestion, $tabChapterCritereChoix, $tabCritereChoixNegative,
                        $critere1Values, $critere2Values, $critere3Values, $critere4Values);

        setlocale(LC_NUMERIC, 'C');
        $spreadsheet = new Spreadsheet();

        //-----------------------------------------------------------------------------
        //Ajout de l'onglet Résultat
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bases analyse');

        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(20);

        $sheet->setCellValue("B1", "Mode d'analyse :");
        $sheet->getStyle("B1")->getFont()->setBold(true);


        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $text = $richText->createTextRun('1. Etat des facteurs : ');
        $text->getFont()->setBold(true);
        $text = $richText->createTextRun('Dans cet onglet, nous regardons le nombre de fois où un item (tel que "le travail demandé" ou ma "perception de mon travail" ou ...)  a reçu : pas du tout vrai - plutôt pas vrai - plutôt vrai - tout à fait vrai');
        $sheet->setCellValue("B3", $richText);

        $sheet->setCellValue("B4", "a reçu : pas du tout vrai - plutôt pas vrai - plutôt vrai - tout à fait vrai");

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $text = $richText->createTextRun('Partant du principe que : "pas du tout vrai" = ');
        $text = $richText->createTextRun('fort dégradé ');
        $text->getFont()->setColor(new Color('FF0000'));
        $text = $richText->createTextRun('; "plutôt pas vrai" = ');
        $text = $richText->createTextRun('dégradé ');
        $text->getFont()->setColor(new Color('ED7D31'));
        $text = $richText->createTextRun('; "plutôt vrai" = ');
        $text = $richText->createTextRun('peu dégradé ');
        $text->getFont()->setColor(new Color('FFD966'));
        $text = $richText->createTextRun('; "tout à fait vrai" = ');
        $text = $richText->createTextRun('efficient');
        $text->getFont()->setColor(new Color('92D050'));
        $text = $richText->createTextRun(". (Nota - Pour l'item 2, le principe est inversé : ");
        $text = $richText->createTextRun('"pas du tout vrai" = ');
        $text = $richText->createTextRun('efficient');
        $text->getFont()->setColor(new Color('92D050'));
        $text = $richText->createTextRun('; "plutôt pas vrai" = ');
        $text = $richText->createTextRun('peu dégradé ');
        $text->getFont()->setColor(new Color('FFD966'));
        $text = $richText->createTextRun('; Etc) ');
        $sheet->setCellValue("B5", $richText);

        $sheet->setCellValue("B6", "Nous pouvons aussi observer pour chaque item, le nombre et le pourcentage de réponses qui le pointent comme fort dégradé, dégradé, peu dégradé, efficient, et donc voir l'état de chaque item.");
        $sheet->setCellValue("B7", 'Cet état est croisé avec les "CSP" en onglet 5');

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $text = $richText->createTextRun('2. Impact des facteurs : ');
        $text->getFont()->setBold(true);
        $text = $richText->createTextRun('Dans cet onglet, nous regardons lenombre de personnes, par item, confrontées via les coef, à :');
        $sheet->setCellValue("B9", $richText);

        $sheet->getStyle('B10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('C10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('D10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('E10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->setCellValue("B10", "0 risque");
        $sheet->setCellValue("C10", "Peu de risques");
        $sheet->setCellValue("D10", "Des risques");
        $sheet->setCellValue("E10", "De forts risques");

        $sheet->setCellValue("B11", 'Nous appliquons un coefficient selon la réponse : tout à fait vrai = 0 ; Plutôt vrai = 1 ; plutôt pas = 2 ; pas du tout = 3 (Nota - pour l\'item 2, le principe est inversé : "tout à fait vrai" = 3 ; "Plutôt vrai" = 2 ; etc.)');
        $sheet->setCellValue("B12", "Nous additionnons le nombre de points obtenu par chacun, par thème et nous considérons que  :");

        $sheet->mergeCells('B13:C13');
        $sheet->mergeCells('B14:C14');
        $sheet->mergeCells('B15:C15');
        $sheet->mergeCells('B16:C16');
        $sheet->getStyle('B13')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('B14')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('B15')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('B16')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->setCellValue("B13", $quiz->risqueDeSr . " à " . $quiz->risqueASr . " = sans risque");
        $sheet->setCellValue("B14", $quiz->risqueDePdr . " à " . $quiz->risqueAPdr . " = peu de risques");
        $sheet->setCellValue("B15", $quiz->risqueDeR . " à " . $quiz->risqueAR . " = risques");
        $sheet->setCellValue("B16", $quiz->risqueDeFr . " à " . $quiz->risqueAFr . " = forts risques");

        $sheet->setCellValue("B17", 'Cet impact est croisé avec les "CSP" en onglet 6');

        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $text = $richText->createTextRun("3. Taux d'exposition : ");
        $text->getFont()->setBold(true);
        $text = $richText->createTextRun('Dans cet onglet nous étudions le nombre de réponses négatives ("plutôt vrai" et "pas du tout vrai", tout thèmes confondus, sauf resentis, par personne)');
        $sheet->setCellValue("B18", $richText);

        $sheet->setCellValue("B19", "Nous partons du principe ci-dessous :");

        $sheet->getStyle('B21:F21')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('B22:F22')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('B23:F23')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('B24:F24')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');

        $sheet->mergeCells('B21:D21');
        $sheet->mergeCells('B22:D22');
        $sheet->mergeCells('B23:D23');
        $sheet->mergeCells('B24:D24');

        $sheet->setCellValue("B21", "Entre " . $quiz->tauxDeFr . " et " . $quiz->tauxAFr . " réponses  pour une personne");
        $sheet->setCellValue("B22", "Entre " . $quiz->tauxDeR . " et " . $quiz->tauxAR . " réponses négatives  pour une personne");
        $sheet->setCellValue("B23", "Entre " . $quiz->tauxDePdr . " et " . $quiz->tauxAPdr . " réponses négatives  pour une personne");
        $sheet->setCellValue("B24", "Entre " . $quiz->tauxDeSr . " et " . $quiz->tauxASr . " réponses négatives  pour une personne");

        $sheet->mergeCells('E21:F21');
        $sheet->mergeCells('E22:F22');
        $sheet->mergeCells('E23:F23');
        $sheet->mergeCells('E24:F24');

        $sheet->setCellValue("E21", "Fort risque d'impact");
        $sheet->setCellValue("E22", "Risque d'impact");
        $sheet->setCellValue("E23", "Peu de risque d'impact");
        $sheet->setCellValue("E24", "Pas de risque d'impact");


        $sheet->setCellValue("G20", "Nombre de personne");
        $sheet->getStyle("G20")->getFont()->setBold(true);
        $sheet->getStyle('G20:G24')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        $sheet->setCellValue("B26", 'Ce taux est croisé avec les critères "CSP" en onglet 7');


        //-----------------------------------------------------------------------------
        //Ajout de l'onglet Résultat
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Résultat');

        $sheet->getDefaultColumnDimension()->setWidth(25);

        //On parametre par defaut en mode paysage
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // format impression A4
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        //Taille des colonnes
        $sheet->getDefaultColumnDimension()->setWidth(15);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(8);

        //Colonnes des questions
        $columnRange = function($startColumn, $endColumn) {
            ++$endColumn;
            for($column = $startColumn; $column !== $endColumn; ++$column) {
                yield $column;
            }
        };
        foreach($columnRange('C', 'L') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('V', 'AE') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('AO', 'AX') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('BH', 'BQ') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('CA', 'CJ') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('CT', 'DC') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('DM', 'DV') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('EF', 'EO') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('EY', 'FH') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);
        foreach($columnRange('FR', 'GA') as $letter) $sheet->getColumnDimension($letter)->setWidth(5);

        //colonnes de separation
        $sheet->getColumnDimension("T")->setWidth(3); $sheet->getColumnDimension("U")->setWidth(3);
        $sheet->getColumnDimension("AM")->setWidth(3); $sheet->getColumnDimension("AN")->setWidth(3);
        $sheet->getColumnDimension("BF")->setWidth(3); $sheet->getColumnDimension("BG")->setWidth(3);
        $sheet->getColumnDimension("BY")->setWidth(3); $sheet->getColumnDimension("BZ")->setWidth(3);
        $sheet->getColumnDimension("CR")->setWidth(3); $sheet->getColumnDimension("CS")->setWidth(3);
        $sheet->getColumnDimension("DK")->setWidth(3); $sheet->getColumnDimension("DL")->setWidth(3);
        $sheet->getColumnDimension("ED")->setWidth(3); $sheet->getColumnDimension("EE")->setWidth(3);
        $sheet->getColumnDimension("EW")->setWidth(3); $sheet->getColumnDimension("EX")->setWidth(3);
        $sheet->getColumnDimension("FP")->setWidth(3); $sheet->getColumnDimension("FQ")->setWidth(3);
        $sheet->getColumnDimension("GH")->setWidth(3); $sheet->getColumnDimension("GI")->setWidth(3);
        $sheet->getColumnDimension("GO")->setWidth(3); $sheet->getColumnDimension("GP")->setWidth(3);
        $sheet->getStyle('T1:U500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('AM1:AN500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('BF1:BG500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('BY1:BZ500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('CR1:CS500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('DK1:DL500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('ED1:EE500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('EW1:EX500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('FP1:FQ500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('GH1:GI500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle('GO1:GP500')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');


        //On affiche les paramètres en haut du Excel
        //Coefficients
        $sheet->getStyle('C2:J2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('C3:J3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('C4:J4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('C5:J5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->getStyle("C1:J5")->getAlignment()->setHorizontal('left');
        $sheet->getStyle("K2:L5")->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(3, 1, "Coefficients");
        $sheet->setCellValueByColumnAndRow(3, 2, "Efficient");
        $sheet->setCellValueByColumnAndRow(3, 3, "Peu dégradé");
        $sheet->setCellValueByColumnAndRow(3, 4, "Dégradé");
        $sheet->setCellValueByColumnAndRow(3, 5, "Fort Dégradé");
        $sheet->setCellValueByColumnAndRow(7, 2, "Tout à fait vrai");
        $sheet->setCellValueByColumnAndRow(7, 3, "Plutôt vrai");
        $sheet->setCellValueByColumnAndRow(7, 4, "Plutôt pas vrai");
        $sheet->setCellValueByColumnAndRow(7, 5, "Pas du tout vrai");
        $sheet->setCellValueByColumnAndRow(11, 2, "=");
        $sheet->setCellValueByColumnAndRow(11, 3, "=");
        $sheet->setCellValueByColumnAndRow(11, 4, "=");
        $sheet->setCellValueByColumnAndRow(11, 5, "=");
        $sheet->setCellValueByColumnAndRow(12, 2, $quiz->coefTafv);
        $sheet->setCellValueByColumnAndRow(12, 3, $quiz->coefPv);
        $sheet->setCellValueByColumnAndRow(12, 4, $quiz->coefPpv);
        $sheet->setCellValueByColumnAndRow(12, 5, $quiz->coefPdtv);
        $sheet->setCellValueByColumnAndRow(3, 6, "*pour le chapitre 2 l’inversion de la notation est déjà prise en compte sur la note affichée");
        //Risque avec Coefficients
        $sheet->getStyle('N2:O2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('N3:O3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('N4:O4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('N5:O5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->setCellValueByColumnAndRow(14, 1, "Risques avec Coefficients");
        $sheet->setCellValueByColumnAndRow(14, 2, $quiz->risqueDeSr . " à " . $quiz->risqueASr . " = sans risque");
        $sheet->setCellValueByColumnAndRow(14, 3, $quiz->risqueDePdr . " à " . $quiz->risqueAPdr . " = peu de risques");
        $sheet->setCellValueByColumnAndRow(14, 4, $quiz->risqueDeR . " à " . $quiz->risqueAR . " = risques");
        $sheet->setCellValueByColumnAndRow(14, 5, $quiz->risqueDeFr . " à " . $quiz->risqueAFr . " = forts risques");

        $sheet->getStyle('A7:GU7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('A6A6A6');


        //Style des data
        $sheet->getStyle("B10:GK500")->getAlignment()->setHorizontal('center');


        //On affiche le libellé des chapitres
        $sheet->setCellValueByColumnAndRow(3, 9, $this->formatChapterForExcel($ChaptersInfo[1]['label']));
        $sheet->setCellValueByColumnAndRow(22, 9, $this->formatChapterForExcel($ChaptersInfo[2]['label']));
        $sheet->setCellValueByColumnAndRow(41, 9, $this->formatChapterForExcel($ChaptersInfo[3]['label']));
        $sheet->setCellValueByColumnAndRow(60, 9, $this->formatChapterForExcel($ChaptersInfo[4]['label']));
        $sheet->setCellValueByColumnAndRow(79, 9, $this->formatChapterForExcel($ChaptersInfo[5]['label']));
        $sheet->setCellValueByColumnAndRow(98, 9, $this->formatChapterForExcel($ChaptersInfo[6]['label']));
        $sheet->setCellValueByColumnAndRow(117, 9, $this->formatChapterForExcel($ChaptersInfo[7]['label']));
        $sheet->setCellValueByColumnAndRow(136, 9, $this->formatChapterForExcel($ChaptersInfo[8]['label']));
        if (array_key_exists('9', $ChaptersInfo)) $sheet->setCellValueByColumnAndRow(155, 9, $this->formatChapterForExcel($ChaptersInfo[9]['label']));
        if (array_key_exists('10', $ChaptersInfo)) $sheet->setCellValueByColumnAndRow(174, 9, $this->formatChapterForExcel($ChaptersInfo[10]['label']));
        $sheet->getStyle("C9")->getFont()->setItalic(true);
        $sheet->getStyle("V9")->getFont()->setItalic(true);

        //On affiche les numéros des questions
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 2, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 21, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 40, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 59, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 78, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 97, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 116, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 135, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 154, 10, "Q" . $i); }
        for($i = 1; $i<=10; $i++) { $sheet->setCellValueByColumnAndRow($i + 173, 10, "Q" . $i); }

        //On affiche en gras tous les libellés de colonne (sauf les label de chapitre
        $sheet->getStyle("C8:GT10")->getFont()->setBold(true);
        $sheet->getStyle("C9")->getFont()->setBold(false); $sheet->getStyle("V9")->getFont()->setBold(false);
        $sheet->getStyle("AO9")->getFont()->setBold(false); $sheet->getStyle("BH9")->getFont()->setBold(false);
        $sheet->getStyle("CA9")->getFont()->setBold(false); $sheet->getStyle("CT9")->getFont()->setBold(false);
        $sheet->getStyle("DM9")->getFont()->setBold(false); $sheet->getStyle("EH9")->getFont()->setBold(false);
        $sheet->getStyle("EY9")->getFont()->setBold(false);$sheet->getStyle("FR9")->getFont()->setBold(false);


        //On ecrit les répondants
        $lign = 11;
        foreach ($quizUsers as $quizUser) {

            if($quiz->anonymous) {
                $sheet->setCellValueByColumnAndRow(1, $lign, $quizUser->userIdentifier);
            } else {
                $sheet->setCellValueByColumnAndRow(1, $lign, $quizUser->userEmail);
            }

            $lign++;
        }
        $lignMax = $lign-1;

        //On boucle sur chaque chapitre puis sur chaque réponses des user
        $chapterId = 1;
        foreach ($ChaptersInfo as $Chapter) {

            $quizUserId = 0;
            $lign = 10;

            $col = 0;
            if ($chapterId == 1) $col = 3;
            elseif ($chapterId == 2) $col = 22;
            elseif ($chapterId == 3) $col = 41;
            elseif ($chapterId == 4) $col = 60;
            elseif ($chapterId == 5) $col = 79;
            elseif ($chapterId == 6) $col = 98;
            elseif ($chapterId == 7) $col = 117;
            elseif ($chapterId == 8) $col = 136;
            elseif ($chapterId == 9) $col = 155;
            elseif ($chapterId == 10) $col = 174;

            foreach ($quizUserResponsesByChapter[$chapterId] as $quizUserResponse) {
                //Si le répondant est différent on change de ligne et on remet le numero de colonne à la valeur initiale par rapport à son chapitre
                if ($quizUserId != $quizUserResponse->quizUserId) {
                    $lign++;
                    if ($chapterId == 1) $col = 3;
                    elseif ($chapterId == 2) $col = 22;
                    elseif ($chapterId == 3) $col = 41;
                    elseif ($chapterId == 4) $col = 60;
                    elseif ($chapterId == 5) $col = 79;
                    elseif ($chapterId == 6) $col = 98;
                    elseif ($chapterId == 7) $col = 117;
                    elseif ($chapterId == 8) $col = 136;
                    elseif ($chapterId == 9) $col = 155;
                    elseif ($chapterId == 10) $col = 174;
                }

                $value = null;
                if ($chapterId != 2) {
                    if ($quizUserResponse->value == 'TAFV') $value = $quiz->coefTafv;
                    elseif ($quizUserResponse->value == 'PV') $value = $quiz->coefPv;
                    elseif ($quizUserResponse->value == 'PPV') $value = $quiz->coefPpv;
                    elseif ($quizUserResponse->value == 'PDTV') $value = $quiz->coefPdtv;
                } else {
                    if ($quizUserResponse->value == 'TAFV') $value = $quiz->coefPdtv;
                    elseif ($quizUserResponse->value == 'PV') $value = $quiz->coefPpv;
                    elseif ($quizUserResponse->value == 'PPV') $value = $quiz->coefPv;
                    elseif ($quizUserResponse->value == 'PDTV') $value = $quiz->coefTafv;
                }

                $sheet->setCellValueByColumnAndRow($col, $lign, $value);

                //Si on arrive à la fin de la ligne du répondant
                //On ajoute les 2 fonctions de la somme et du NB de réponse négative
                $numCol = 0;
                $colBegin = "";
                $colEnd = "";
                $colSum = "";
                $colCountIf = "";
                $colCountIfTAFV = "";
                $colCountIfPV = "";
                $colCountIfPPV = "";
                $colCountIfPDTV = "";
                $colIf = "";
                if ($chapterId == 1) { $numCol = 12; $colBegin = "C"; $colEnd = "L"; $colSum = "M"; $colCountIf = "N"; $colIf = "O"; $colCountIfTAFV = "P"; $colCountIfPV = "Q";  $colCountIfPPV = "R"; $colCountIfPDTV = "S"; }
                elseif ($chapterId == 2) { $numCol = 31; $colBegin = "V"; $colEnd = "AE"; $colSum = "AF"; $colCountIf = "AG"; $colIf = "AH"; $colCountIfTAFV = "AI"; $colCountIfPV = "AJ";  $colCountIfPPV = "AK"; $colCountIfPDTV = "AL"; }
                elseif ($chapterId == 3) { $numCol = 50; $colBegin = "AO"; $colEnd = "AX"; $colSum = "AY"; $colCountIf = "AZ"; $colIf = "BA"; $colCountIfTAFV = "BB"; $colCountIfPV = "BC";  $colCountIfPPV = "BD"; $colCountIfPDTV = "BE"; }
                elseif ($chapterId == 4) { $numCol = 69; $colBegin = "BH"; $colEnd = "BQ"; $colSum = "BR"; $colCountIf = "BS"; $colIf = "BT"; $colCountIfTAFV = "BU"; $colCountIfPV = "BV";  $colCountIfPPV = "BW"; $colCountIfPDTV = "BX"; }
                elseif ($chapterId == 5) { $numCol = 88; $colBegin = "CA"; $colEnd = "CJ"; $colSum = "CK"; $colCountIf = "CL"; $colIf = "CM"; $colCountIfTAFV = "CN"; $colCountIfPV = "CO";  $colCountIfPPV = "CP"; $colCountIfPDTV = "CQ"; }
                elseif ($chapterId == 6) { $numCol = 107; $colBegin = "CT"; $colEnd = "DC"; $colSum = "DD"; $colCountIf = "DE"; $colIf = "DF"; $colCountIfTAFV = "DG"; $colCountIfPV = "DH";  $colCountIfPPV = "DI"; $colCountIfPDTV = "DJ"; }
                elseif ($chapterId == 7) { $numCol = 126; $colBegin = "DM"; $colEnd = "DV"; $colSum = "DW"; $colCountIf = "DX"; $colIf = "DY"; $colCountIfTAFV = "DZ"; $colCountIfPV = "EA";  $colCountIfPPV = "EB"; $colCountIfPDTV = "EC"; }
                elseif ($chapterId == 8) { $numCol = 145; $colBegin = "EF"; $colEnd = "EO"; $colSum = "EP"; $colCountIf = "EQ"; $colIf = "ER"; $colCountIfTAFV = "ES"; $colCountIfPV = "ET";  $colCountIfPPV = "EU"; $colCountIfPDTV = "EV"; }
                elseif ($chapterId == 9) { $numCol = 164; $colBegin = "EY"; $colEnd = "FH"; $colSum = "FI"; $colCountIf = "FJ"; $colIf = "FK"; $colCountIfTAFV = "FL"; $colCountIfPV = "FM";  $colCountIfPPV = "FN"; $colCountIfPDTV = "FO"; }
                elseif ($chapterId == 10) { $numCol = 183; $colBegin = "FR"; $colEnd = "GA"; $colSum = "GB"; $colCountIf = "GC";  $colIf = "GD"; $colCountIfTAFV = "GE"; $colCountIfPV = "GF";  $colCountIfPPV = "GG"; $colCountIfPDTV = "GH"; }
                if ($col == $numCol) {

                    //On affiche le titre des colonnes une seul fois
                    if($lign == 11) {
                        $sheet->setCellValueByColumnAndRow($numCol+1, 10, "Total");
                        $sheet->setCellValueByColumnAndRow($numCol+2, 10, "Réponses négatives");
                        $sheet->setCellValueByColumnAndRow($numCol+3, 10, "Risque avec coéf");
                        $sheet->setCellValueByColumnAndRow($numCol+4, 8, "Evaluation de l'item");
                        $sheet->setCellValueByColumnAndRow($numCol+4, 9, "Tout à fait vrai");
                        $sheet->setCellValueByColumnAndRow($numCol+4, 10, "Efficient");
                        $sheet->setCellValueByColumnAndRow($numCol+5, 9, "Plutôt vrai ");
                        $sheet->setCellValueByColumnAndRow($numCol+5, 10, "Peu dégradé");
                        $sheet->setCellValueByColumnAndRow($numCol+6, 9, "Plutôt pas vrai");
                        $sheet->setCellValueByColumnAndRow($numCol+6, 10, "Dégradé");
                        $sheet->setCellValueByColumnAndRow($numCol+7, 9, "Pas du tout vrai");
                        $sheet->setCellValueByColumnAndRow($numCol+7, 10, "Fort Dégradé");
                        $sheet->getStyle($colCountIfTAFV . '9:' . $colCountIfTAFV .  '10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
                        $sheet->getStyle($colCountIfPV . '9:' . $colCountIfPV .  '10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                        $sheet->getStyle($colCountIfPPV . '9:' . $colCountIfPPV .  '10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
                        $sheet->getStyle($colCountIfPDTV . '9:' . $colCountIfPDTV .  '10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
                        $sheet->getStyle($colCountIfTAFV."9:" . $colCountIfPDTV . "10")->getAlignment()->setHorizontal('center');
                        $sheet->getColumnDimension($colSum)->setWidth(8);
                        $sheet->getColumnDimension($colCountIf)->setWidth(18);
                    }


                    $sheet->setCellValue($colSum . $lign, '=SUM(' . $colBegin . $lign . ':' . $colEnd . $lign . ')');
                    $sheet->setCellValue($colCountIf . $lign, '=COUNTIF(' . $colBegin . $lign . ':' . $colEnd . $lign . ',">1")');
                    $sheet->setCellValue($colIf . $lign, '=IF(' . $colSum . $lign . '<=' . $quiz->risqueASr . ',"' . $quiz->risqueDeSr . ' à ' . $quiz->risqueASr . ' points",'.
                        'IF(' . $colSum . $lign . '<= ' . $quiz->risqueAPdr . ',"' . $quiz->risqueDePdr . ' à ' . $quiz->risqueAPdr .  ' points",'.
                        'IF(' . $colSum . $lign . '<= ' . $quiz->risqueAR . ',"' . $quiz->risqueDeR . ' à ' . $quiz->risqueAR .  ' points",'.
                        'IF(' . $colSum . $lign . '>= ' . $quiz->risqueDeFr . ',"' . $quiz->risqueDeFr . ' à ' . $quiz->risqueAFr .  ' points"))))'
                    );
                    $sheet->setCellValue($colCountIfTAFV . $lign, '=COUNTIF(' . $colBegin . $lign . ':' . $colEnd . $lign . ',' . $quiz->coefTafv . ')');
                    $sheet->setCellValue($colCountIfPV . $lign, '=COUNTIF(' . $colBegin . $lign . ':' . $colEnd . $lign . ',' . $quiz->coefPv . ')');
                    $sheet->setCellValue($colCountIfPPV . $lign, '=COUNTIF(' . $colBegin . $lign . ':' . $colEnd . $lign . ',' . $quiz->coefPpv . ')');
                    $sheet->setCellValue($colCountIfPDTV  . $lign, '=COUNTIF(' . $colBegin . $lign . ':' . $colEnd . $lign . ',' . $quiz->coefPdtv . ')');
                }

                $quizUserId = $quizUserResponse->quizUserId;
                $col++;
            }

            $chapterId++;
        }


        //Taux d'exposition
        $sheet->getColumnDimension("GJ")->setWidth(18);
        $sheet->getColumnDimension("GK")->setWidth(25);
        $sheet->getStyle('GJ2:GK2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('GJ3:GK3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('GJ4:GK4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('GJ5:GK5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->setCellValueByColumnAndRow(192, 1, "Taux d'exposition");
        $sheet->setCellValueByColumnAndRow(192, 2, $quiz->tauxDeSr . " à " . $quiz->tauxASr . " = sans risque");
        $sheet->setCellValueByColumnAndRow(192, 3, $quiz->tauxDePdr . " à " . $quiz->tauxAPdr . " = peu de risques");
        $sheet->setCellValueByColumnAndRow(192, 4, $quiz->tauxDeR . " à " . $quiz->tauxAR . " = risques");
        $sheet->setCellValueByColumnAndRow(192, 5, $quiz->tauxDeFr . " à " . $quiz->tauxAFr . " = forts risques");
        $sheet->setCellValueByColumnAndRow(192, 6, "*Taux d'exposition = nbre total de répnses négatives, tous thèmes inclus (sauf ressentis), par personne");

        $sheet->setCellValueByColumnAndRow(192, 10, "Réponses négatives");
        $sheet->setCellValueByColumnAndRow(193, 10, "Taux d'exposition");
        $lign = 11;
        foreach ($quizUsers as $quizUser) {

            $sheet->setCellValue("GJ" . $lign, '=SUM(' . "N" . $lign . ',' .
                "AG" . $lign . ',' .
                "AZ" . $lign . ',' .
                "BS" . $lign . ',' .
                "CL" . $lign . ',' .
                "DE" . $lign . ',' .
                "DX" . $lign . ',' .
                "EQ" . $lign . ',' .
                "FJ" . $lign . ',' .
                "GC" . $lign . ',' .
                ')');

            $sheet->setCellValue("GK" . $lign, '=IF(' . "GJ" . $lign . '<=' . $quiz->tauxASr . ',"' . $quiz->tauxDeSr . ' à ' . $quiz->tauxASr . ' réponses négatives",'.
                'IF(' . "GJ" . $lign . '<= ' . $quiz->tauxAPdr . ',"' . $quiz->tauxDePdr . ' à ' . $quiz->tauxAPdr .  ' réponses négatives",'.
                'IF(' . "GJ" . $lign . '<= ' . $quiz->tauxAR . ',"' . $quiz->tauxDeR . ' à ' . $quiz->tauxAR .  ' réponses négatives",'.
                'IF(' . "GJ" . $lign . '>= ' . $quiz->tauxDeFr . ',"' . $quiz->tauxDeFr . ' à ' . $quiz->tauxAFr .  ' réponses négatives"))))'
            );

            $lign++;
        }


        //Caracteristiques individuelles

        $sheet->getStyle("GQ9:GS9")->getFont()->setItalic(true);
        $sheet->getStyle('GQ9:GS9')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('3A3838');
        $sheet->getStyle('GQ9:GS9')->getFont()->getColor()->setARGB('FFFFFF');
        $sheet->setCellValueByColumnAndRow(200, 9, "Caractéristiques individuelles");
        $sheet->getColumnDimension("GQ")->setWidth(30);
        $sheet->getColumnDimension("GR")->setWidth(60);
        $sheet->getColumnDimension("GS")->setWidth(30);
        $sheet->getColumnDimension("GT")->setWidth(30);
        $sheet->getColumnDimension("GU")->setWidth(30);
        if(isset($quizCriteresBarometre[1])) $sheet->setCellValueByColumnAndRow(199, 10, $quizCriteresBarometre[1]->titre);
        if(isset($quizCriteresBarometre[2])) $sheet->setCellValueByColumnAndRow(200, 10, $quizCriteresBarometre[2]->titre);
        if(isset($quizCriteresBarometre[3])) $sheet->setCellValueByColumnAndRow(201, 10, $quizCriteresBarometre[3]->titre);
        if(isset($quizCriteresBarometre[4])) $sheet->setCellValueByColumnAndRow(202, 10, $quizCriteresBarometre[4]->titre);
        $lign = 11;
        foreach($critere1Values as $key => $value) {
            $sheet->setCellValueByColumnAndRow(199, $lign, $value);
            $lign++;
        }
        $lign = 11;
        foreach($critere2Values as $key => $value) {
            $sheet->setCellValueByColumnAndRow(200, $lign, $value);
            $lign++;
        }
        $lign = 11;
        foreach($critere3Values as $key => $value) {
            $sheet->setCellValueByColumnAndRow(201, $lign, $value);
            $lign++;
        }
        $lign = 11;
        foreach($critere4Values as $key => $value) {
            $sheet->setCellValueByColumnAndRow(202, $lign, $value);
            $lign++;
        }

        //-----------------------------------------------------------------------------
        //Ajout de l'onglet 1. Etat des items
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('1. Etat des facteurs');

        //Style des 2 Tableau etat des items en nombre et en percent
        $sheet->getStyle("D4:D11")->getAlignment()->setHorizontal('right');
        $sheet->getStyle("E3:N". strval(count($ChaptersInfo)+4))->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(2);
        foreach($columnRange('B', 'N') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        $sheet->getStyle('E3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('K3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('F3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('L3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('M3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->getStyle('N3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');

        //format percent
        $sheet->getStyle('K4:N'.strval(count($ChaptersInfo)+3))->getNumberFormat()->setFormatCode('0%');

        //Bordures du tableau en nombre
        $sheet->getStyle('B3:I'.strval(count($ChaptersInfo)+4))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('B3:D3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('I3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('E4:H'.strval(count($ChaptersInfo)+3))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('B'.strval(count($ChaptersInfo)+4).':D'.strval(count($ChaptersInfo)+4))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('I'.strval(count($ChaptersInfo)+4))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        //Bordures du tableau en percent
        $sheet->getStyle('K3:N'.strval(count($ChaptersInfo)+3))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('K3:N3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));


        $sheet->setCellValueByColumnAndRow(5, 3, "Efficient");
        $sheet->setCellValueByColumnAndRow(6, 3, "Peu dégradé");
        $sheet->setCellValueByColumnAndRow(7, 3, "Dégradé");
        $sheet->setCellValueByColumnAndRow(8, 3, "Fort Dégradé");
        $sheet->setCellValueByColumnAndRow(9, 3, "TOTAL");

        $sheet->setCellValueByColumnAndRow(11, 3, "Efficient");
        $sheet->setCellValueByColumnAndRow(12, 3, "Peu dégradé");
        $sheet->setCellValueByColumnAndRow(13, 3, "Dégradé");
        $sheet->setCellValueByColumnAndRow(14, 3, "Fort Dégradé");

        $lign = 4;
        for ($chapterId = 1; $chapterId <= 10; $chapterId++) {
            if (array_key_exists($chapterId, $ChaptersInfo)) {
                $sheet->getStyle('A'.$lign)->getFont()->getColor()->setRGB('FFFFFF');
                $sheet->setCellValueByColumnAndRow(1, $lign, $chapterId);
                $sheet->setCellValueByColumnAndRow(4, $lign, $this->formatChapterForExcel($ChaptersInfo[$chapterId]['label']));

                $colCountIfTAFV = "";
                $colCountIfPV = "";
                $colCountIfPPV = "";
                $colCountIfPDTV = "";
                if ($chapterId == 1) { $colCountIfTAFV = "P"; $colCountIfPV = "Q";  $colCountIfPPV = "R"; $colCountIfPDTV = "S"; }
                elseif ($chapterId == 2) { $colCountIfTAFV = "AI"; $colCountIfPV = "AJ";  $colCountIfPPV = "AK"; $colCountIfPDTV = "AL"; }
                elseif ($chapterId == 3) { $colCountIfTAFV = "BB"; $colCountIfPV = "BC";  $colCountIfPPV = "BD"; $colCountIfPDTV = "BE"; }
                elseif ($chapterId == 4) { $colCountIfTAFV = "BU"; $colCountIfPV = "BV";  $colCountIfPPV = "BW"; $colCountIfPDTV = "BX"; }
                elseif ($chapterId == 5) { $colCountIfTAFV = "CN"; $colCountIfPV = "CO";  $colCountIfPPV = "CP"; $colCountIfPDTV = "CQ"; }
                elseif ($chapterId == 6) { $colCountIfTAFV = "DG"; $colCountIfPV = "DH";  $colCountIfPPV = "DI"; $colCountIfPDTV = "DJ"; }
                elseif ($chapterId == 7) { $colCountIfTAFV = "DZ"; $colCountIfPV = "EA";  $colCountIfPPV = "EB"; $colCountIfPDTV = "EC"; }
                elseif ($chapterId == 8) { $colCountIfTAFV = "ES"; $colCountIfPV = "ET";  $colCountIfPPV = "EU"; $colCountIfPDTV = "EV"; }
                elseif ($chapterId == 9) { $colCountIfTAFV = "FL"; $colCountIfPV = "FM";  $colCountIfPPV = "FN"; $colCountIfPDTV = "FO"; }
                elseif ($chapterId == 10) { $colCountIfTAFV = "GE"; $colCountIfPV = "GF";  $colCountIfPPV = "GG"; $colCountIfPDTV = "GH"; }

                $sheet->setCellValue('E'.$lign,'=SUM(Résultat!'.$colCountIfTAFV.'11:'.$colCountIfTAFV. $lignMax . ')');
                $sheet->setCellValue('F'.$lign,'=SUM(Résultat!'.$colCountIfPV.'11:'.$colCountIfPV. $lignMax . ')');
                $sheet->setCellValue('G'.$lign,'=SUM(Résultat!'.$colCountIfPPV.'11:'.$colCountIfPPV. $lignMax . ')');
                $sheet->setCellValue('H'.$lign,'=SUM(Résultat!'.$colCountIfPDTV.'11:'.$colCountIfPDTV. $lignMax . ')');

                $sheet->setCellValue('I'.$lign,'=SUM(E'.$lign.':H' . $lign . ')');

                $sheet->setCellValue('K'.$lign,'=IFERROR(E'.$lign.'/I'.$lign.',"")');
                $sheet->setCellValue('L'.$lign,'=IFERROR(F'.$lign.'/I'.$lign.',"")');
                $sheet->setCellValue('M'.$lign,'=IFERROR(G'.$lign.'/I'.$lign.',"")');
                $sheet->setCellValue('N'.$lign,'=IFERROR(H'.$lign.'/I'.$lign.',"")');

                $lign++;
            }
        }

        $sheet->setCellValue('E'.$lign,'=SUM(E4:E'.strval(count($ChaptersInfo)+3).')');
        $sheet->setCellValue('F'.$lign,'=SUM(F4:F'.strval(count($ChaptersInfo)+3).')');
        $sheet->setCellValue('G'.$lign,'=SUM(G4:G'.strval(count($ChaptersInfo)+3).')');
        $sheet->setCellValue('H'.$lign,'=SUM(H4:H'.strval(count($ChaptersInfo)+3).')');


        //GRAPHIQUE EN NOMBRE
        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$A$4:$A$'.($lign-1), null, 8), // Catégories
        ];

        $series = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'1. Etat des facteurs'!" . '$E$4:$E$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'1. Etat des facteurs'!" . '$F$4:$F$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'1. Etat des facteurs'!" . '$G$4:$G$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'1. Etat des facteurs'!" . '$H$4:$H$'.($lign-1), null, 3, []),
        ];

        $legnds = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$E$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$F$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$G$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$H$3', null, 2),
        ];

        // Créez de la série
        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
            DataSeries::GROUPING_STACKED,    // Type de regroupement : Empilé
            range(0, count($series) - 1),    // Index des séries
            $legnds,                              // Légendes (laissées vides ici)
            $categories,                     // Catégories (axes X)
            $series                          // Séries de données (valeurs)
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_COLUMN); // Orientation en colonnes (vertical)

        // Création du graphique
        $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
        $legend = new Legend(Legend::POSITION_RIGHT, null, false); // Légende
        $title = new Title('BAROM-ETAT-DES-FACTEURS'); // Titre du graphique
        $chart = new Chart('Stacked Bar Chart',$title, $legend, $plotArea);

        // ajout du graphique
        $chart->setTopLeftPosition('B16');
        $chart->setBottomRightPosition('J34');
        $sheet->addChart($chart);

        //GRAPHIQUE EN PERCENT
        $series = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'1. Etat des facteurs'!" . '$K$4:$K$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'1. Etat des facteurs'!" . '$L$4:$L$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'1. Etat des facteurs'!" . '$M$4:$M$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'1. Etat des facteurs'!" . '$N$4:$N$'.($lign-1), null, 3, []),
        ];

        $legnds = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$K$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$L$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$M$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'1. Etat des facteurs'!" . '$N$3', null, 2),
        ];

        // Créez de la série
        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
            DataSeries::GROUPING_PERCENT_STACKED,    // Type de regroupement : percent
            range(0, count($series) - 1),    // Index des séries
            $legnds,                              // Légendes (laissées vides ici)
            $categories,                     // Catégories (axes X)
            $series                          // Séries de données (valeurs)
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_COLUMN); // Orientation en colonnes (vertical)

        // Création du graphique
        $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
        $title = new Title('BAROM-ETAT-DES-FACTEURS'); // Titre du graphique
        $chart = new Chart('Stacked Bar Chart',$title, null, $plotArea);

        // ajout du graphique
        $chart->setTopLeftPosition('K16');
        $chart->setBottomRightPosition('T34');
        $sheet->addChart($chart);



        //-----------------------------------------------------------------------------
        //Ajout de l'onglet 2. Impact des facteur
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('2. Impact des facteurs');

        //Style des 2 Tableau etat des items en nombre et en percent
        $sheet->getStyle("D4:D11")->getAlignment()->setHorizontal('right');
        $sheet->getStyle("E2:N". strval(count($ChaptersInfo)+4))->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(2);
        foreach($columnRange('B', 'N') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        $sheet->getStyle('E2:E3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->getStyle('K3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->getStyle('F2:F3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('L3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('G2:G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('M3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('H2:H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('N3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');

        //format percent
        $sheet->getStyle('K4:N'.strval(count($ChaptersInfo)+3))->getNumberFormat()->setFormatCode('0%');

        //Bordures du tableau en nombre
        $sheet->getStyle('B3:I'.strval(count($ChaptersInfo)+4))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('B3:D3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('I3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('E4:H'.strval(count($ChaptersInfo)+3))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('B'.strval(count($ChaptersInfo)+4).':D'.strval(count($ChaptersInfo)+4))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('I'.strval(count($ChaptersInfo)+4))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        //Bordures du tableau en percent
        $sheet->getStyle('K3:N'.strval(count($ChaptersInfo)+3))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('K3:N3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));



        $sheet->setCellValueByColumnAndRow(5, 2, "De forts risques");
        $sheet->setCellValueByColumnAndRow(6, 2, "Des riques");
        $sheet->setCellValueByColumnAndRow(7, 2, "Peu de risque");
        $sheet->setCellValueByColumnAndRow(8, 2, "0 risque");

        $sheet->setCellValueByColumnAndRow(5, 3, $quiz->risqueDeFr . " à " . $quiz->risqueAFr . " points");
        $sheet->setCellValueByColumnAndRow(6, 3, $quiz->risqueDeR . " à " . $quiz->risqueAR . " points");
        $sheet->setCellValueByColumnAndRow(7, 3, $quiz->risqueDePdr . " à " . $quiz->risqueAPdr . " points");
        $sheet->setCellValueByColumnAndRow(8, 3, $quiz->risqueDeSr . " à " . $quiz->risqueASr . " points");
        $sheet->setCellValueByColumnAndRow(9, 3, "TOTAL");

        $sheet->setCellValueByColumnAndRow(11, 3, $quiz->risqueDeFr . " à " . $quiz->risqueAFr . " points");
        $sheet->setCellValueByColumnAndRow(12, 3, $quiz->risqueDeR . " à " . $quiz->risqueAR . " points");
        $sheet->setCellValueByColumnAndRow(13, 3, $quiz->risqueDePdr . " à " . $quiz->risqueAPdr . " points");
        $sheet->setCellValueByColumnAndRow(14, 3, $quiz->risqueDeSr . " à " . $quiz->risqueASr . " points");

        $lign = 4;
        for ($chapterId = 1; $chapterId <= 10; $chapterId++) {
            if (array_key_exists($chapterId, $ChaptersInfo)) {
                $sheet->getStyle('A'.$lign)->getFont()->getColor()->setRGB('FFFFFF');
                $sheet->setCellValueByColumnAndRow(1, $lign, $chapterId);
                $sheet->setCellValueByColumnAndRow(4, $lign, $this->formatChapterForExcel($ChaptersInfo[$chapterId]['label']));

                $coltotal = "";
                if ($chapterId == 1) $coltotal = "M";
                elseif ($chapterId == 2) $coltotal = "AF";
                elseif ($chapterId == 3) $coltotal = "AY";
                elseif ($chapterId == 4) $coltotal = "BR";
                elseif ($chapterId == 5) $coltotal = "CK";
                elseif ($chapterId == 6) $coltotal = "DD";
                elseif ($chapterId == 7) $coltotal = "DW";
                elseif ($chapterId == 8) $coltotal = "EP";
                elseif ($chapterId == 9) $coltotal = "FI";
                elseif ($chapterId == 10) $coltotal = "GB";

                //=NB.SI(Résultat!M11:M103;">=21")

                $sheet->setCellValue('E'.$lign,'=COUNTIF(Résultat!'.$coltotal.'11:'.$coltotal. $lignMax . ',">='.$quiz->risqueDeFr.'")');
                $sheet->setCellValue('F'.$lign,'=COUNTIFS(Résultat!'.$coltotal.'11:'.$coltotal. $lignMax . ',"<='.$quiz->risqueAR.'",Résultat!'.$coltotal.'11:'.$coltotal. $lignMax . ',">='.$quiz->risqueDeR.'")');
                $sheet->setCellValue('G'.$lign,'=COUNTIFS(Résultat!'.$coltotal.'11:'.$coltotal. $lignMax . ',"<='.$quiz->risqueAPdr.'",Résultat!'.$coltotal.'11:'.$coltotal. $lignMax . ',">='.$quiz->risqueDePdr.'")');
                $sheet->setCellValue('H'.$lign,'=COUNTIFS(Résultat!'.$coltotal.'11:'.$coltotal. $lignMax . ',"<='.$quiz->risqueASr.'",Résultat!'.$coltotal.'11:'.$coltotal. $lignMax . ',">='.$quiz->risqueDeSr.'")');

                $sheet->setCellValue('I'.$lign,'=SUM(E'.$lign.':H' . $lign . ')');

                $sheet->setCellValue('K'.$lign,'=IFERROR(E'.$lign.'/I'.$lign.',"")');
                $sheet->setCellValue('L'.$lign,'=IFERROR(F'.$lign.'/I'.$lign.',"")');
                $sheet->setCellValue('M'.$lign,'=IFERROR(G'.$lign.'/I'.$lign.',"")');
                $sheet->setCellValue('N'.$lign,'=IFERROR(H'.$lign.'/I'.$lign.',"")');

                $lign++;
            }
        }

        $sheet->setCellValue('E'.$lign,'=SUM(E4:E'.strval(count($ChaptersInfo)+3).')');
        $sheet->setCellValue('F'.$lign,'=SUM(F4:F'.strval(count($ChaptersInfo)+3).')');
        $sheet->setCellValue('G'.$lign,'=SUM(G4:G'.strval(count($ChaptersInfo)+3).')');
        $sheet->setCellValue('H'.$lign,'=SUM(H4:H'.strval(count($ChaptersInfo)+3).')');

        //GRAPHIQUE EN NOMBRE
        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$A$4:$A$'.($lign-1), null, 8), // Catégories
        ];

        $series = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'2. Impact des facteurs'!" . '$H$4:$H$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'2. Impact des facteurs'!" . '$G$4:$G$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'2. Impact des facteurs'!" . '$F$4:$F$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'2. Impact des facteurs'!" . '$E$4:$E$'.($lign-1), null, 3, []),
        ];

        $legnds = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$H$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$G$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$F$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$E$3', null, 2),
        ];

        // Créez de la série
        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
            DataSeries::GROUPING_STACKED,    // Type de regroupement : Empilé
            range(0, count($series) - 1),    // Index des séries
            $legnds,                              // Légendes (laissées vides ici)
            $categories,                     // Catégories (axes X)
            $series                          // Séries de données (valeurs)
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_COLUMN); // Orientation en colonnes (vertical)

        // Création du graphique
        $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
        $legend = new Legend(Legend::POSITION_RIGHT, null, false); // Légende
        $title = new Title('BAROM-IMPACT-DES-FACTEURS'); // Titre du graphique
        $chart = new Chart('Stacked Bar Chart',$title, $legend, $plotArea);

        // ajout du graphique
        $chart->setTopLeftPosition('B16');
        $chart->setBottomRightPosition('J34');
        $sheet->addChart($chart);

        //GRAPHIQUE EN PERCENT
        $series = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'2. Impact des facteurs'!" . '$N$4:$N$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'2. Impact des facteurs'!" . '$M$4:$M$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'2. Impact des facteurs'!" . '$L$4:$L$'.($lign-1), null, 3, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'2. Impact des facteurs'!" . '$K$4:$K$'.($lign-1), null, 3, []),
        ];

        $legnds = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$N$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$M$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$L$3', null, 2),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'2. Impact des facteurs'!" . '$K$3', null, 2),
        ];

        // Créez de la série
        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
            DataSeries::GROUPING_PERCENT_STACKED,    // Type de regroupement : percent
            range(0, count($series) - 1),    // Index des séries
            $legnds,                              // Légendes (laissées vides ici)
            $categories,                     // Catégories (axes X)
            $series                          // Séries de données (valeurs)
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_COLUMN); // Orientation en colonnes (vertical)

        // Création du graphique
        $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
        $title = new Title('BAROM-IMPACT-DES-FACTEURS'); // Titre du graphique
        $chart = new Chart('Stacked Bar Chart',$title, null, $plotArea);

        // ajout du graphique
        $chart->setTopLeftPosition('K16');
        $chart->setBottomRightPosition('T34');
        $sheet->addChart($chart);



        //-----------------------------------------------------------------------------
        //Ajout de l'onglet 3. Taux d'exposition
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('3. Taux d exposition');

        //Style des 2 Tableau etat des items en nombre et en percent
        $sheet->getStyle("B3:B6")->getAlignment()->setHorizontal('left');
        $sheet->getStyle("H3:H6")->getAlignment()->setHorizontal('left');
        $sheet->getStyle("C2:D7")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("I2:J7")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C7")->getAlignment()->setHorizontal('right');
        $sheet->getStyle("I7")->getAlignment()->setHorizontal('right');
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('H')->setWidth(40);
        $sheet->getColumnDimension('I')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(5);
        $sheet->getColumnDimension('F')->setWidth(5);
        $sheet->getColumnDimension('G')->setWidth(5);
        $sheet->getStyle('B3:C3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->getStyle('H3:I3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
        $sheet->getStyle('B4:C4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('H4:I4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
        $sheet->getStyle('B5:C5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('H5:I5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $sheet->getStyle('B6:C6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        $sheet->getStyle('H6:I6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');

        //format percent
        $sheet->getStyle('J3:J7')->getNumberFormat()->setFormatCode('0%');
        $sheet->getStyle('K3:K7')->getFont()->getColor()->setRGB('FFFFFF');

        //Bordures du tableau en nombre
        $sheet->getStyle('B3:B6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('C3:C6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('D2')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('D3:D6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        //Bordures du tableau en percent
        $sheet->getStyle('H3:H6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('I3:I6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('J2')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        $sheet->getStyle('J3:J6')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        $sheet->getStyle("B1")->getFont()->setBold(true);
        $sheet->getStyle("D2")->getFont()->setBold(true);
        $sheet->getStyle("J2")->getFont()->setBold(true);

        $sheet->setCellValueByColumnAndRow(2, 1, "Nbre de réponses négatives, tous thèmes confondus, par personne");

        $sheet->setCellValueByColumnAndRow(4, 2, "Nombre de personne");
        $sheet->setCellValueByColumnAndRow(10, 2, "Pourcentage du total");

        $sheet->setCellValueByColumnAndRow(3, 3, "Fort risque d'impact");
        $sheet->setCellValueByColumnAndRow(3, 4, "Risque d'impact");
        $sheet->setCellValueByColumnAndRow(3, 5, "Peu de risque d'impact");
        $sheet->setCellValueByColumnAndRow(3, 6, "Pas de risque d'impact");
        $sheet->setCellValueByColumnAndRow(3, 7, "Total");

        $sheet->setCellValueByColumnAndRow(9, 3, "Fort risque d'impact");
        $sheet->setCellValueByColumnAndRow(9, 4, "Risque d'impact");
        $sheet->setCellValueByColumnAndRow(9, 5, "Peu de risque d'impact");
        $sheet->setCellValueByColumnAndRow(9, 6, "Pas de risque d'impact");
        $sheet->setCellValueByColumnAndRow(9, 7, "Total");

        $sheet->setCellValueByColumnAndRow(2, 3, "entre " . $quiz->tauxDeFr . " et " . $quiz->tauxAFr . " réponses négatives");
        $sheet->setCellValueByColumnAndRow(2, 4, "entre " . $quiz->tauxDeR . " et " . $quiz->tauxAR . " réponses négatives");
        $sheet->setCellValueByColumnAndRow(2, 5, "entre " . $quiz->tauxDePdr . " et " . $quiz->tauxAPdr . " réponses négatives");
        $sheet->setCellValueByColumnAndRow(2, 6, "entre " . $quiz->tauxDeSr . " et " . $quiz->tauxASr . " réponses négatives");

        $sheet->setCellValueByColumnAndRow(8, 3, "entre " . $quiz->tauxDeFr . " et " . $quiz->tauxAFr . " réponses négatives");
        $sheet->setCellValueByColumnAndRow(8, 4, "entre " . $quiz->tauxDeR . " et " . $quiz->tauxAR . " réponses négatives");
        $sheet->setCellValueByColumnAndRow(8, 5, "entre " . $quiz->tauxDePdr . " et " . $quiz->tauxAPdr . " réponses négatives");
        $sheet->setCellValueByColumnAndRow(8, 6, "entre " . $quiz->tauxDeSr . " et " . $quiz->tauxASr . " réponses négatives");


        $sheet->setCellValue('D3','=COUNTIF(Résultat!GJ11:GJ'.$lignMax . ',">='.$quiz->tauxDeFr.'")');
        $sheet->setCellValue('D4','=COUNTIFS(Résultat!GJ11:GJ'.$lignMax . ',"<='.$quiz->tauxAR.'",Résultat!GJ11:GJ'.$lignMax . ',">='.$quiz->tauxDeR.'")');
        $sheet->setCellValue('D5','=COUNTIFS(Résultat!GJ11:GJ'.$lignMax . ',"<='.$quiz->tauxAPdr.'",Résultat!GJ11:GJ'.$lignMax . ',">='.$quiz->tauxDePdr.'")');
        $sheet->setCellValue('D6','=COUNTIFS(Résultat!GJ11:GJ'.$lignMax . ',"<='.$quiz->tauxASr.'",Résultat!GJ11:GJ'.$lignMax . ',">='.$quiz->tauxDeSr.'")');
        $sheet->setCellValue('D7','=SUM(D3:D6)');

        $sheet->setCellValue('J3','=IFERROR(D3/D7,"")');
        $sheet->setCellValue('J4','=IFERROR(D4/D7,"")');
        $sheet->setCellValue('J5','=IFERROR(D5/D7,"")');
        $sheet->setCellValue('J6','=IFERROR(D6/D7,"")');
        /*$sheet->setCellValue('K3',number_format( $sheet->getCell('D3')->getCalculatedValue() / $sheet->getCell('D7')->getCalculatedValue() , 2, '.', ''));
        $sheet->setCellValue('K4',number_format( $sheet->getCell('D4')->getCalculatedValue() / $sheet->getCell('D7')->getCalculatedValue() , 2, '.', ''));
        $sheet->setCellValue('K5',number_format( $sheet->getCell('D5')->getCalculatedValue() / $sheet->getCell('D7')->getCalculatedValue() , 2, '.', ''));
        $sheet->setCellValue('K6',number_format( $sheet->getCell('D6')->getCalculatedValue() / $sheet->getCell('D7')->getCalculatedValue() , 2, '.', ''));

        $sheet->getStyle('K3:K7')->getNumberFormat()->setFormatCode('0%');*/
        /*$sheet->setCellValue('K3','0.22');
        $sheet->setCellValue('K4','0.44');
        $sheet->setCellValue('K5','0.15');
        $sheet->setCellValue('K6','0.19');*/

        $sheet->setCellValue('J7','=SUM(J3:J6)');


        //GRAPHIQUE EN NOMBRE
        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$D$2', null, 1), // Catégories
        ];

        $series = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'3. Taux d exposition'!" . '$D$3', null, 1, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'3. Taux d exposition'!" . '$D$4', null, 1, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'3. Taux d exposition'!" . '$D$5', null, 1, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'3. Taux d exposition'!" . '$D$6', null, 1, []),

        ];

        $legnds = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$B$3', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$B$4', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$B$5', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$B$6', null, 1),
        ];

        // Créez de la série
        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
            DataSeries::GROUPING_STACKED,    // Type de regroupement : Empilé
            range(0, count($series) - 1),    // Index des séries
            $legnds,                         // Légendes (laissées vides ici)
            $categories,                     // Catégories (axes X)
            $series                          // Séries de données (valeurs)
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_COLUMN); // Orientation en colonnes (vertical)

        // Création du graphique
        $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
        $legend = new Legend(Legend::POSITION_RIGHT, null, false); // Légende
        $title = new Title("Taux d'exposition"); // Titre du graphique
        $chart = new Chart('Stacked Bar Chart',$title, $legend, $plotArea);

        // ajout du graphique
        $chart->setTopLeftPosition('B10');
        $chart->setBottomRightPosition('E26');
        $sheet->addChart($chart);

        //GRAPHIQUE EN PERCENT
        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$J$2', null, 1), // Catégories
        ];

        $series = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'3. Taux d exposition'!" . '$J$3', null, 1, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'3. Taux d exposition'!" . '$J$4', null, 1, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'3. Taux d exposition'!" . '$J$5', null, 1, []),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'3. Taux d exposition'!" . '$J$6', null, 1, []),
        ];

        $legnds = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$H$3', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$H$4', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$H$5', null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'3. Taux d exposition'!" . '$H$6', null, 1),
        ];

        // Créez de la série
        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
            DataSeries::GROUPING_PERCENT_STACKED,    // Type de regroupement : percent
            range(0, count($series) - 1),    // Index des séries
            $legnds,                              // Légendes (laissées vides ici)
            $categories,                     // Catégories (axes X)
            $series                          // Séries de données (valeurs)
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_COLUMN); // Orientation en colonnes (vertical)

        // Création du graphique
        $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
        $title = new Title("Taux d'exposition"); // Titre du graphique
        $chart = new Chart('Stacked Bar Chart',$title, null, $plotArea);

        // ajout du graphique
        $chart->setTopLeftPosition('H10');
        $chart->setBottomRightPosition('K26');
        $sheet->addChart($chart);


        //-----------------------------------------------------------------------------
        //Ajout de l'onglet 4. Barres
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('4. Barres');

        $sheet->getRowDimension(1)->setRowHeight(8);
        $sheet->getRowDimension(2)->setRowHeight(8);
        $sheet->getRowDimension(3)->setRowHeight(8);
        $sheet->getRowDimension(4)->setRowHeight(8);

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(5);

        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('U')->setWidth(12);
        $sheet->getColumnDimension('AD')->setWidth(12);
        $sheet->getColumnDimension('AM')->setWidth(12);
        $sheet->getColumnDimension('AV')->setWidth(12);
        $sheet->getColumnDimension('BE')->setWidth(12);
        $sheet->getColumnDimension('BN')->setWidth(12);
        $sheet->getColumnDimension('BW')->setWidth(12);
        $sheet->getColumnDimension('CF')->setWidth(12);

        foreach($columnRange('D', 'G') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('M', 'P') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('V', 'Y') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('AE', 'AH') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('AN', 'AQ') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('AW', 'AZ') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('BF', 'BI') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('BO', 'BR') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('BX', 'CA') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);
        foreach($columnRange('CG', 'CJ') as $letter) $sheet->getColumnDimension($letter)->setWidth(15);

        foreach($columnRange('H', 'K') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('Q', 'T') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('Z', 'AC') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('AI', 'AL') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('AR', 'AU') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('BA', 'BD') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('BJ', 'BM') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('BS', 'BV') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('CB', 'CE') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);
        foreach($columnRange('CK', 'CN') as $letter) $sheet->getColumnDimension($letter)->setWidth(3);

        $chapterId = 1;
        foreach($resultByChapterQuestion as $keyChapitre => $value) {

            //if ($chapterId < 3) {

            $lignLabelQuestion = 7;

            $colQuestionNumber = "";
            $colEfficient = "";
            $colPeuDegrade = "";
            $colDegrade = "";
            $colFortDegrade = "";
            $colNumEfficient = "";
            $colNumPeuDegrade = "";
            $colNumDegrade = "";
            $colNumFortDegrade = "";
            $colGraphicEnd = "";
            if ($chapterId == 1) { $colQuestionNumber = "C"; $colEfficient = "D"; $colPeuDegrade = "E";  $colDegrade = "F"; $colFortDegrade = "G"; $colGraphicEnd = "H"; $colNumEfficient = "4"; $colNumPeuDegrade = "5";  $colNumDegrade = "6"; $colNumFortDegrade = "7"; }
            elseif ($chapterId == 2) { $colQuestionNumber = "L"; $colEfficient = "M"; $colPeuDegrade = "N";  $colDegrade = "O"; $colFortDegrade = "P"; $colGraphicEnd = "Q"; $colNumEfficient = "13"; $colNumPeuDegrade = "14";  $colNumDegrade = "15"; $colNumFortDegrade = "16"; }
            elseif ($chapterId == 3) { $colQuestionNumber = "U"; $colEfficient = "V"; $colPeuDegrade = "W";  $colDegrade = "X"; $colFortDegrade = "Y"; $colGraphicEnd = "Z"; $colNumEfficient = "22"; $colNumPeuDegrade = "23";  $colNumDegrade = "24"; $colNumFortDegrade = "25"; }
            elseif ($chapterId == 4) { $colQuestionNumber = "AD"; $colEfficient = "AE"; $colPeuDegrade = "AF";  $colDegrade = "AG"; $colFortDegrade = "AH"; $colGraphicEnd = "AI"; $colNumEfficient = "31"; $colNumPeuDegrade = "32";  $colNumDegrade = "33"; $colNumFortDegrade = "34"; }
            elseif ($chapterId == 5) { $colQuestionNumber = "AM"; $colEfficient = "AN"; $colPeuDegrade = "AO";  $colDegrade = "AP"; $colFortDegrade = "AQ"; $colGraphicEnd = "AR"; $colNumEfficient = "40"; $colNumPeuDegrade = "41";  $colNumDegrade = "42"; $colNumFortDegrade = "43"; }
            elseif ($chapterId == 6) { $colQuestionNumber = "AV"; $colEfficient = "AW"; $colPeuDegrade = "AX";  $colDegrade = "AY"; $colFortDegrade = "AZ"; $colGraphicEnd = "BA"; $colNumEfficient = "49"; $colNumPeuDegrade = "50";  $colNumDegrade = "51"; $colNumFortDegrade = "52"; }
            elseif ($chapterId == 7) { $colQuestionNumber = "BE"; $colEfficient = "BF"; $colPeuDegrade = "BG";  $colDegrade = "BH"; $colFortDegrade = "BI"; $colGraphicEnd = "BJ"; $colNumEfficient = "58"; $colNumPeuDegrade = "59";  $colNumDegrade = "60"; $colNumFortDegrade = "61"; }
            elseif ($chapterId == 8) { $colQuestionNumber = "BN"; $colEfficient = "BO"; $colPeuDegrade = "BP";  $colDegrade = "BQ"; $colFortDegrade = "BR"; $colGraphicEnd = "BS"; $colNumEfficient = "67"; $colNumPeuDegrade = "68";  $colNumDegrade = "69"; $colNumFortDegrade = "70"; }
            elseif ($chapterId == 9) { $colQuestionNumber = "BW"; $colEfficient = "BX"; $colPeuDegrade = "BY";  $colDegrade = "BZ"; $colFortDegrade = "CA"; $colGraphicEnd = "CB"; $colNumEfficient = "76"; $colNumPeuDegrade = "77";  $colNumDegrade = "78"; $colNumFortDegrade = "79"; }
            elseif ($chapterId == 10) { $colQuestionNumber = "CF"; $colEfficient = "CG"; $colPeuDegrade = "CH";  $colDegrade = "CI"; $colFortDegrade = "CJ"; $colGraphicEnd = "CK"; $colNumEfficient = "85"; $colNumPeuDegrade = "86";  $colNumDegrade = "87"; $colNumFortDegrade = "88"; }

            $sheet->mergeCells($colEfficient .'5:'.$colFortDegrade .'5');
            $sheet->getStyle($colEfficient."5:".$colFortDegrade."5")->getAlignment()->setHorizontal('center');
            $sheet->getStyle($colEfficient."5:".$colFortDegrade."5")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D6DCE4');
            $sheet->getStyle($colEfficient."5:".$colFortDegrade."5")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->setCellValue($colEfficient."5", $this->formatChapterForExcel($ChaptersInfo[$keyChapitre]['label']));

            if (is_array($value)) {

                $questionIndex = 1;
                foreach ($value as $keyQuestion => $value) {

                    $sheet->getRowDimension($lignLabelQuestion)->setRowHeight(75);

                    //LA couleur de fond depend de
                    //c’est l’addition des items "dégradé" et "fort dégradé" dès qu’elle donne un % supérieur ou égal à 50

                    if(($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade'] + $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade']) > 50) {
                        $sheet->getStyle($colQuestionNumber.$lignLabelQuestion.":".$colQuestionNumber.($lignLabelQuestion+3))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C00000');
                    } else {
                        $sheet->getStyle($colQuestionNumber.$lignLabelQuestion.":".$colQuestionNumber.($lignLabelQuestion+3))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00B050');
                    }

                    $sheet->getStyle($colQuestionNumber.$lignLabelQuestion.":".$colQuestionNumber.($lignLabelQuestion+3))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                    $sheet->getStyle($colQuestionNumber.$lignLabelQuestion)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($colQuestionNumber.$lignLabelQuestion)->getAlignment()->setVertical('center');
                    $sheet->getStyle($colQuestionNumber.$lignLabelQuestion)->getFont()->getColor()->setRGB('FFFFFF');
                    $sheet->getStyle($colQuestionNumber.$lignLabelQuestion)->getFont()->setBold(true);
                    $sheet->setCellValue($colQuestionNumber.$lignLabelQuestion, "Q".$questionIndex);

                    $sheet->mergeCells($colEfficient .$lignLabelQuestion.':'.$colFortDegrade .$lignLabelQuestion);
                    $sheet->getStyle($colEfficient .$lignLabelQuestion.':'.$colFortDegrade .$lignLabelQuestion)->getAlignment()->setHorizontal('center');

                    //Recuperation du label de la question
                    $question = $quizQuestions[$keyQuestion];
                    $sheet->getStyle($colEfficient .$lignLabelQuestion)->getAlignment()->setWrapText(true);
                    $sheet->getStyle($colEfficient .$lignLabelQuestion)->getAlignment()->setVertical('center');
                    $sheet->setCellValue($colEfficient .$lignLabelQuestion, $this->formatQuestionForExcel($question->label));

                    $sheet->setCellValueByColumnAndRow($colNumEfficient, $lignLabelQuestion+1, "Efficient");
                    $sheet->setCellValueByColumnAndRow($colNumPeuDegrade, $lignLabelQuestion+1, "Peu dégradé");
                    $sheet->setCellValueByColumnAndRow($colNumDegrade, $lignLabelQuestion+1, "Dégradé");
                    $sheet->setCellValueByColumnAndRow($colNumFortDegrade, $lignLabelQuestion+1, "Fort Dégradé");
                    $sheet->getStyle($colEfficient.($lignLabelQuestion+1).":".$colFortDegrade.($lignLabelQuestion+3))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                    $sheet->getStyle($colEfficient.($lignLabelQuestion+1).":".$colFortDegrade.($lignLabelQuestion+3))->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($colEfficient.($lignLabelQuestion+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
                    $sheet->getStyle($colPeuDegrade.($lignLabelQuestion+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                    $sheet->getStyle($colDegrade.($lignLabelQuestion+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
                    $sheet->getStyle($colFortDegrade.($lignLabelQuestion+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
                    $sheet->getStyle($colEfficient.($lignLabelQuestion+3).':'.$colFortDegrade.($lignLabelQuestion+3))->getNumberFormat()->setFormatCode('0%');

                    $sheet->setCellValueByColumnAndRow($colNumEfficient, $lignLabelQuestion+2, $resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbEfficient']);
                    $sheet->setCellValueByColumnAndRow($colNumPeuDegrade, $lignLabelQuestion+2, $resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbPeuDegrade']);
                    $sheet->setCellValueByColumnAndRow($colNumDegrade, $lignLabelQuestion+2, $resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbDegrade']);
                    $sheet->setCellValueByColumnAndRow($colNumFortDegrade, $lignLabelQuestion+2, $resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbFortDegrade']);
                    $sheet->setCellValueByColumnAndRow($colNumEfficient, $lignLabelQuestion+3, $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient']/100);
                    $sheet->setCellValueByColumnAndRow($colNumPeuDegrade, $lignLabelQuestion+3, $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade']/100);
                    $sheet->setCellValueByColumnAndRow($colNumDegrade, $lignLabelQuestion+3, $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade']/100);
                    $sheet->setCellValueByColumnAndRow($colNumFortDegrade, $lignLabelQuestion+3, $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade']/100);

                    //GRAPHIQUE EN NOMBRE
                    $categories = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'4. Barres'!" . $colQuestionNumber.$lignLabelQuestion, null, 4), // Catégories
                    ];

                    $series = [
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'4. Barres'!" . $colEfficient.($lignLabelQuestion+2), null, 3, []),
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'4. Barres'!" . $colPeuDegrade.($lignLabelQuestion+2), null, 3, []),
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'4. Barres'!" . $colDegrade.($lignLabelQuestion+2), null, 3, []),
                        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'4. Barres'!" . $colFortDegrade.($lignLabelQuestion+2), null, 3, []),

                    ];

                    // Créez de la série
                    $dataSeries = new DataSeries(
                        DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
                        DataSeries::GROUPING_PERCENT_STACKED,    // Type de regroupement : Empilé
                        range(0, count($series) - 1),    // Index des séries
                        [],                         // Légendes (laissées vides ici)
                        $categories,                     // Catégories (axes X)
                        $series                          // Séries de données (valeurs)
                    );
                    $dataSeries->setPlotDirection(DataSeries::DIRECTION_HORIZONTAL); // Orientation en colonnes (vertical)

                    // Création du graphique
                    $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
                    //$legend = new Legend(Legend::POSITION_N, null, false); // Légende
                    $title = new Title("BAROM-BARRE"); // Titre du graphique
                    $chart = new Chart('Stacked Bar Chart',$title, null, $plotArea);

                    // ajout du graphique
                    $sheet->getRowDimension($lignLabelQuestion+4)->setRowHeight(5);
                    $chart->setTopLeftPosition($colQuestionNumber.($lignLabelQuestion+5));
                    $chart->setBottomRightPosition($colGraphicEnd.($lignLabelQuestion+9));
                    $sheet->addChart($chart);

                    $lignLabelQuestion = $lignLabelQuestion + 11;
                    $questionIndex++;

                }
            }
            //}

            $chapterId++;
        }

        //-----------------------------------------------------------------------------
        //Ajout de l'onglet 5. Croisement état facteurs
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('5. Croisement état facteurs');

        $sheet->freezePane('B1');

        $chapterId = 1;
        foreach($tabChapterCritereChoix as $keyChapter => $tabCritere) {

            $lignLabelChapter = 5;

            $colCritere = "";
            $colEtiquette = "";
            $colEfficient = "";
            $colPeuDegrade = "";
            $colDegrade = "";
            $colFortDegrade = "";
            $colTotalGeneral = "";
            $colTotalNombre = "";

            if ($chapterId == 1) { $colCritere="A"; $colEtiquette="B"; $colEfficient="C"; $colPeuDegrade="D"; $colDegrade="E"; $colFortDegrade="F"; $colTotalGeneral="G"; $colTotalNombre="H"; }
            elseif ($chapterId == 2) { $colEtiquette="J"; $colEfficient="K"; $colPeuDegrade="L"; $colDegrade="M"; $colFortDegrade="N"; $colTotalGeneral="O"; $colTotalNombre="P"; }
            elseif ($chapterId == 3) { $colEtiquette="R"; $colEfficient="S"; $colPeuDegrade="T"; $colDegrade="U"; $colFortDegrade="V"; $colTotalGeneral="W"; $colTotalNombre="X"; }
            elseif ($chapterId == 4) { $colEtiquette="Z"; $colEfficient="AA"; $colPeuDegrade="AB"; $colDegrade="AC"; $colFortDegrade="AD"; $colTotalGeneral="AE"; $colTotalNombre="AF"; }
            elseif ($chapterId == 5) { $colEtiquette="AH"; $colEfficient="AI"; $colPeuDegrade="AJ"; $colDegrade="AK"; $colFortDegrade="AL"; $colTotalGeneral="AM"; $colTotalNombre="AN"; }
            elseif ($chapterId == 6) { $colEtiquette="AP"; $colEfficient="AQ"; $colPeuDegrade="AR"; $colDegrade="AS"; $colFortDegrade="AT"; $colTotalGeneral="AU"; $colTotalNombre="AV"; }
            elseif ($chapterId == 7) { $colEtiquette="AX"; $colEfficient="AY"; $colPeuDegrade="AZ"; $colDegrade="BA"; $colFortDegrade="BB"; $colTotalGeneral="BC"; $colTotalNombre="BD"; }
            elseif ($chapterId == 8) { $colEtiquette="BF"; $colEfficient="BG"; $colPeuDegrade="BH"; $colDegrade="BI"; $colFortDegrade="BJ"; $colTotalGeneral="BK"; $colTotalNombre="BL"; }
            elseif ($chapterId == 9) { $colEtiquette="BN"; $colEfficient="BO"; $colPeuDegrade="BP"; $colDegrade="BQ"; $colFortDegrade="BR"; $colTotalGeneral="BS"; $colTotalNombre="BT"; }
            elseif ($chapterId == 10) { $colEtiquette="BV"; $colEfficient="BW"; $colPeuDegrade="BX"; $colDegrade="BY"; $colFortDegrade="BZ"; $colTotalGeneral="CA"; $colTotalNombre="CB"; }

            if ($chapterId == 1) { $sheet->getColumnDimension($colCritere)->setWidth(20); }
            $sheet->getColumnDimension($colEtiquette)->setWidth(20);
            $sheet->getColumnDimension($colEfficient)->setWidth(15);
            $sheet->getColumnDimension($colPeuDegrade)->setWidth(15);
            $sheet->getColumnDimension($colDegrade)->setWidth(15);
            $sheet->getColumnDimension($colFortDegrade)->setWidth(15);
            $sheet->getColumnDimension($colTotalGeneral)->setWidth(15);
            $sheet->getColumnDimension($colTotalNombre)->setWidth(15);

            $sheet->mergeCells($colEtiquette . $lignLabelChapter . ':' . $colTotalNombre . $lignLabelChapter);
            $sheet->getStyle($colEtiquette . $lignLabelChapter . ":" . $colTotalNombre . $lignLabelChapter)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D6DCE4');
            $sheet->getStyle($colEtiquette . $lignLabelChapter . ":" . $colTotalNombre . $lignLabelChapter)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle($colEtiquette . $lignLabelChapter)->getAlignment()->setHorizontal('center');
            $sheet->setCellValue($colEtiquette . $lignLabelChapter, $this->formatChapterForExcel($ChaptersInfo[$chapterId]['label']));

            $sheet->getStyle($colEtiquette . "6:" . $colEtiquette . "50")->getAlignment()->setHorizontal('left');
            $sheet->getStyle($colEfficient . "6:" . $colTotalNombre . "50")->getAlignment()->setHorizontal('center');
            $sheet->getStyle($colEfficient . "6:" . $colTotalGeneral . "50")->getAlignment()->setHorizontal('center');

            $indexCritere = 1;
            $bgColorCritere = "";
            foreach ($tabCritere as $keyCritere => $tabChoix) {

                if($indexCritere == 1) $bgColorCritere = "F8CBAD";
                elseif($indexCritere == 2) $bgColorCritere = "FFE699";
                elseif($indexCritere == 3) $bgColorCritere = "A9D08E";
                elseif($indexCritere == 4) $bgColorCritere = "548235";

                $sheet->setCellValue($colEtiquette.($lignLabelChapter+1), "Étiquettes de lignes");
                $sheet->setCellValue($colEfficient.($lignLabelChapter+1), "Efficient");
                $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1), "Peu dégradé");
                $sheet->setCellValue($colDegrade.($lignLabelChapter+1), "Dégradé");
                $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1), "Fort Dégradé");
                $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1), "Total général");
                $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1), "Total en nombre");
                $sheet->getStyle($colEtiquette.($lignLabelChapter+1).":".$colTotalNombre.($lignLabelChapter+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $sheet->getStyle($colEtiquette.($lignLabelChapter+1).":".$colTotalNombre.($lignLabelChapter+1))->getAlignment()->setHorizontal('center');
                $sheet->getStyle($colEfficient.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
                $sheet->getStyle($colPeuDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                $sheet->getStyle($colDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
                $sheet->getStyle($colFortDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');

                $nombrechoix = 1;
                $totalGeneral = 0;
                $totalEfficient = 0;
                $totalPeuDegrade = 0;
                $totalDegrade = 0;
                $totalFortDegrade = 0;
                foreach ($tabChoix as $keyChoix => $valueChoix) {

                    $sheet->setCellValue($colEtiquette.($lignLabelChapter+1+$nombrechoix), $keyChoix);

                    $sheet->setCellValue($colEfficient.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['percentEfficient']);
                    $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['percentPeuDegrade']);
                    $sheet->setCellValue($colDegrade.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['percentDegrade']);
                    $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['percentFortDegrade']);
                    $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1+$nombrechoix),'=('.$colTotalNombre.($lignLabelChapter+1+$nombrechoix).'/'.$colTotalNombre.($lignLabelChapter+2+count($tabChoix)).')');
                    $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbTotal']);

                    $totalEfficient = $totalEfficient + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbEfficient'];
                    $totalPeuDegrade = $totalPeuDegrade + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbPeuDegrade'];
                    $totalDegrade = $totalDegrade + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbDegrade'];
                    $totalFortDegrade = $totalFortDegrade + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbFortDegrade'];
                    $totalGeneral = $totalGeneral + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbTotal'];
                    $nombrechoix++;
                }

                $sheet->getStyle($colEtiquette . ($lignLabelChapter+2) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $sheet->getStyle($colEtiquette . ($lignLabelChapter+1+$nombrechoix) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $sheet->getStyle($colTotalGeneral . ($lignLabelChapter+2) . ':' . $colTotalGeneral . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $sheet->getStyle($colTotalNombre . ($lignLabelChapter+2) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

                $sheet->getStyle($colEtiquette . ($lignLabelChapter+2) . ':' . $colTotalGeneral . ($lignLabelChapter+1+$nombrechoix))->getNumberFormat()->setFormatCode('0%');

                if ($chapterId == 1) {
                    $sheet->mergeCells($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix + 1));
                    $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getAlignment()->setVertical('center');
                    $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($bgColorCritere);
                    $sheet->setCellValue($colCritere . ($lignLabelChapter + 1), $keyCritere);
                }

                $sheet->setCellValue($colEtiquette.($lignLabelChapter+1+$nombrechoix), "Total général");
                $sheet->setCellValue($colEfficient.($lignLabelChapter+1+$nombrechoix), $totalEfficient/$totalGeneral);
                $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1+$nombrechoix), $totalPeuDegrade/$totalGeneral);
                $sheet->setCellValue($colDegrade.($lignLabelChapter+1+$nombrechoix), $totalDegrade/$totalGeneral);
                $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1+$nombrechoix), $totalFortDegrade/$totalGeneral);
                $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1+$nombrechoix), $totalGeneral/$totalGeneral);
                $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1+$nombrechoix), $totalGeneral);

                $indexCritere++;
                $lignLabelChapter = $lignLabelChapter+$nombrechoix+3;
            }

            $chapterId++;
        }


        //-----------------------------------------------------------------------------
        //Ajout de l'onglet 6 Croisement impact facteurs
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('6. Croisement impact facteurs');

        $sheet->freezePane('B1');

        $chapterId = 1;
        foreach($tabChapterCritereChoix as $keyChapter => $tabCritere) {

            $lignLabelChapter = 5;

            $colCritere = "";
            $colEtiquette = "";
            $colEfficient = "";
            $colPeuDegrade = "";
            $colDegrade = "";
            $colFortDegrade = "";
            $colTotalGeneral = "";
            $colTotalNombre = "";
            $colEtiquetteTruncate = "";

            if ($chapterId == 1) { $colCritere="A"; $colEtiquette="B"; $colEfficient="C"; $colPeuDegrade="D"; $colDegrade="E"; $colFortDegrade="F"; $colTotalGeneral="G"; $colTotalNombre="H"; $colEtiquetteTruncate="I"; }
            elseif ($chapterId == 2) { $colEtiquette="J"; $colEfficient="K"; $colPeuDegrade="L"; $colDegrade="M"; $colFortDegrade="N"; $colTotalGeneral="O"; $colTotalNombre="P"; $colEtiquetteTruncate="Q"; }
            elseif ($chapterId == 3) { $colEtiquette="R"; $colEfficient="S"; $colPeuDegrade="T"; $colDegrade="U"; $colFortDegrade="V"; $colTotalGeneral="W"; $colTotalNombre="X"; $colEtiquetteTruncate="Y"; }
            elseif ($chapterId == 4) { $colEtiquette="Z"; $colEfficient="AA"; $colPeuDegrade="AB"; $colDegrade="AC"; $colFortDegrade="AD"; $colTotalGeneral="AE"; $colTotalNombre="AF"; $colEtiquetteTruncate="AG"; }
            elseif ($chapterId == 5) { $colEtiquette="AH"; $colEfficient="AI"; $colPeuDegrade="AJ"; $colDegrade="AK"; $colFortDegrade="AL"; $colTotalGeneral="AM"; $colTotalNombre="AN"; $colEtiquetteTruncate="AO"; }
            elseif ($chapterId == 6) { $colEtiquette="AP"; $colEfficient="AQ"; $colPeuDegrade="AR"; $colDegrade="AS"; $colFortDegrade="AT"; $colTotalGeneral="AU"; $colTotalNombre="AV"; $colEtiquetteTruncate="AW"; }
            elseif ($chapterId == 7) { $colEtiquette="AX"; $colEfficient="AY"; $colPeuDegrade="AZ"; $colDegrade="BA"; $colFortDegrade="BB"; $colTotalGeneral="BC"; $colTotalNombre="BD"; $colEtiquetteTruncate="BE"; }
            elseif ($chapterId == 8) { $colEtiquette="BF"; $colEfficient="BG"; $colPeuDegrade="BH"; $colDegrade="BI"; $colFortDegrade="BJ"; $colTotalGeneral="BK"; $colTotalNombre="BL"; $colEtiquetteTruncate="BM"; }
            elseif ($chapterId == 9) { $colEtiquette="BN"; $colEfficient="BO"; $colPeuDegrade="BP"; $colDegrade="BQ"; $colFortDegrade="BR"; $colTotalGeneral="BS"; $colTotalNombre="BT"; $colEtiquetteTruncate="BU"; }
            elseif ($chapterId == 10) { $colEtiquette="BV"; $colEfficient="BW"; $colPeuDegrade="BX"; $colDegrade="BY"; $colFortDegrade="BZ"; $colTotalGeneral="CA"; $colTotalNombre="CB"; $colEtiquetteTruncate="CC"; }

            if ($chapterId == 1) { $sheet->getColumnDimension($colCritere)->setWidth(20); }
            $sheet->getColumnDimension($colEtiquette)->setWidth(20);
            $sheet->getColumnDimension($colEfficient)->setWidth(15);
            $sheet->getColumnDimension($colPeuDegrade)->setWidth(15);
            $sheet->getColumnDimension($colDegrade)->setWidth(15);
            $sheet->getColumnDimension($colFortDegrade)->setWidth(15);
            $sheet->getColumnDimension($colTotalGeneral)->setWidth(15);
            $sheet->getColumnDimension($colTotalNombre)->setWidth(15);

            $sheet->mergeCells($colEtiquette . $lignLabelChapter . ':' . $colTotalNombre . $lignLabelChapter);
            $sheet->getStyle($colEtiquette . $lignLabelChapter . ":" . $colTotalNombre . $lignLabelChapter)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D6DCE4');
            $sheet->getStyle($colEtiquette . $lignLabelChapter . ":" . $colTotalNombre . $lignLabelChapter)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle($colEtiquette . $lignLabelChapter)->getAlignment()->setHorizontal('center');
            $sheet->setCellValue($colEtiquette . $lignLabelChapter, $this->formatChapterForExcel($ChaptersInfo[$chapterId]['label']));

            $sheet->getStyle($colEtiquette . "6:" . $colEtiquette . "50")->getAlignment()->setHorizontal('left');
            $sheet->getStyle($colEfficient . "6:" . $colTotalNombre . "50")->getAlignment()->setHorizontal('center');
            $sheet->getStyle($colEfficient . "6:" . $colTotalGeneral . "50")->getAlignment()->setHorizontal('center');

            $indexCritere = 1;
            $bgColorCritere = "";
            foreach ($tabCritere as $keyCritere => $tabChoix) {

                if($indexCritere == 1) $bgColorCritere = "F8CBAD";
                elseif($indexCritere == 2) $bgColorCritere = "FFE699";
                elseif($indexCritere == 3) $bgColorCritere = "A9D08E";
                elseif($indexCritere == 4) $bgColorCritere = "548235";

                $sheet->setCellValue($colEtiquette.($lignLabelChapter+1), "Étiquettes de lignes");
                $sheet->setCellValue($colEfficient.($lignLabelChapter+1), $quiz->risqueDeSr . ' à ' . $quiz->risqueASr . ' points');
                $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1), $quiz->risqueDePdr . ' à ' . $quiz->risqueAPdr .  ' points');
                $sheet->setCellValue($colDegrade.($lignLabelChapter+1), $quiz->risqueDeR . ' à ' . $quiz->risqueAR .  ' points');
                $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1), $quiz->risqueDeFr . ' à ' . $quiz->risqueAFr .  ' points');
                $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1), "Total général");
                $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1), "Total en nombre");


                $sheet->getStyle($colEtiquette.($lignLabelChapter+1).":".$colTotalNombre.($lignLabelChapter+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $sheet->getStyle($colEtiquette.($lignLabelChapter+1).":".$colTotalNombre.($lignLabelChapter+1))->getAlignment()->setHorizontal('center');
                $sheet->getStyle($colEfficient.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
                $sheet->getStyle($colPeuDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                $sheet->getStyle($colDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
                $sheet->getStyle($colFortDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');

                $nombrechoix = 1;
                $totalGeneral = 0;
                $totalSansRisque = 0;
                $totalPeuDeRisque = 0;
                $totalRisques = 0;
                $totalFortsRisques = 0;

                foreach ($tabChoix as $keyChoix => $valueChoix) {

                    $sheet->setCellValue($colEtiquette.($lignLabelChapter+1+$nombrechoix), $keyChoix);
                    //Ecritrure des valeure en blanc masqué pour le graphquique (tronqué à 15 car)
                    $sheet->setCellValue($colEtiquetteTruncate.($lignLabelChapter+1+$nombrechoix), substr($keyChoix, 0, 15));
                    $sheet->getStyle($colEtiquetteTruncate.($lignLabelChapter+1+$nombrechoix))->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

                    $sheet->setCellValue($colEfficient.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['percentSansRisque']);
                    $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['percentPeuDeRisque']);
                    $sheet->setCellValue($colDegrade.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['percentRisques']);
                    $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['percentFortsRisques']);
                    $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1+$nombrechoix),'=('.$colTotalNombre.($lignLabelChapter+1+$nombrechoix).'/'.$colTotalNombre.($lignLabelChapter+2+count($tabChoix)).')');

                    $totalSansRisque = $totalSansRisque + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbSansRisque'];
                    $totalPeuDeRisque = $totalPeuDeRisque + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbPeuDeRisque'];
                    $totalRisques = $totalRisques + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbRisques'];
                    $totalFortsRisques = $totalFortsRisques + $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbFortsRisques'];
                    $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1+$nombrechoix), $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbSansRisque']+
                                                                                             $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbPeuDeRisque']+
                                                                                             $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbRisques']+
                                                                                             $tabChapterCritereChoix[$chapterId][$keyCritere][$keyChoix]['nbFortsRisques']);

                    $nombrechoix++;

                }

                $sheet->getStyle($colEtiquette . ($lignLabelChapter+2) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $sheet->getStyle($colEtiquette . ($lignLabelChapter+1+$nombrechoix) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $sheet->getStyle($colTotalGeneral . ($lignLabelChapter+2) . ':' . $colTotalGeneral . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
                $sheet->getStyle($colTotalNombre . ($lignLabelChapter+2) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

                $sheet->getStyle($colEtiquette . ($lignLabelChapter+2) . ':' . $colTotalGeneral . ($lignLabelChapter+1+$nombrechoix))->getNumberFormat()->setFormatCode('0%');

                if ($chapterId == 1) {
                    $sheet->mergeCells($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix + 1));
                    $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getAlignment()->setVertical('center');
                    $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($bgColorCritere);
                    $sheet->setCellValue($colCritere . ($lignLabelChapter + 1), $keyCritere);
                }

                $totalGeneral = $totalSansRisque + $totalPeuDeRisque + $totalRisques + $totalFortsRisques;

                $sheet->setCellValue($colEtiquette.($lignLabelChapter+1+$nombrechoix), "Total général");
                $sheet->setCellValue($colEtiquetteTruncate.($lignLabelChapter+1+$nombrechoix), "Total général");
                $sheet->getStyle($colEtiquetteTruncate.($lignLabelChapter+1+$nombrechoix))->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $sheet->setCellValue($colEfficient.($lignLabelChapter+1+$nombrechoix), $totalSansRisque/$totalGeneral);
                $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1+$nombrechoix), $totalPeuDeRisque/$totalGeneral);
                $sheet->setCellValue($colDegrade.($lignLabelChapter+1+$nombrechoix), $totalRisques/$totalGeneral);
                $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1+$nombrechoix), $totalFortsRisques/$totalGeneral);
                $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1+$nombrechoix), $totalGeneral/$totalGeneral);
                $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1+$nombrechoix), $totalGeneral);

                $categories = [];
                $categories[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'6. Croisement impact facteurs'!" . '$'.$colEtiquetteTruncate.'$'.($lignLabelChapter+2). ':$'.$colEtiquetteTruncate.'$'.($lignLabelChapter+1+$nombrechoix), null, 3);

                $series = [
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'6. Croisement impact facteurs'!" . '$'.$colEfficient.'$'.($lignLabelChapter+2).':$'.$colEfficient.'$'.($lignLabelChapter+1+$nombrechoix), null, 3, []),
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'6. Croisement impact facteurs'!" . '$'.$colPeuDegrade.'$'.($lignLabelChapter+2).':$'.$colPeuDegrade.'$'.($lignLabelChapter+1+$nombrechoix), null, 3, []),
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'6. Croisement impact facteurs'!" . '$'.$colDegrade.'$'.($lignLabelChapter+2).':$'.$colDegrade.'$'.($lignLabelChapter+1+$nombrechoix), null, 3, []),
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'6. Croisement impact facteurs'!" . '$'.$colFortDegrade.'$'.($lignLabelChapter+2).':$'.$colFortDegrade.'$'.($lignLabelChapter+1+$nombrechoix), null, 3, []),
                    ];

                $legnds = [
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'6. Croisement impact facteurs'!" . '$'.$colEfficient.'$'.($lignLabelChapter+1), null, 3),
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'6. Croisement impact facteurs'!" . '$'.$colPeuDegrade.'$'.($lignLabelChapter+1), null, 3),
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'6. Croisement impact facteurs'!" . '$'.$colDegrade.'$'.($lignLabelChapter+1), null, 3),
                    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'6. Croisement impact facteurs'!" . '$'.$colFortDegrade.'$'.($lignLabelChapter+1), null, 3),
                ];

                // Créez de la série
                $dataSeries = new DataSeries(
                    DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
                    DataSeries::GROUPING_STACKED,    // Type de regroupement : Empilé
                    range(0, count($series) - 1),    // Index des séries
                    $legnds,                         // Légendes (laissées vides ici)
                    $categories,                     // Catégories (axes X)
                    $series                          // Séries de données (valeurs)
                );
                $dataSeries->setPlotDirection(DataSeries::DIRECTION_COLUMN); // Orientation en colonnes (vertical)

                // Création du graphique
                $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
                $legend = new Legend(Legend::POSITION_RIGHT, null, false); // Légende
                $title = new Title("BAROM-CROISEMENT-IMPACT-FACTEURS"); // Titre du graphique
                $chart = new Chart('Stacked Bar Chart',$title, $legend, $plotArea);

                // ajout du graphique
                $chart->setTopLeftPosition($colEtiquette.($lignLabelChapter+3+$nombrechoix));
                $chart->setBottomRightPosition($colEtiquetteTruncate.($lignLabelChapter+10+$nombrechoix));
                $sheet->addChart($chart);


                $indexCritere++;
                $lignLabelChapter = $lignLabelChapter+$nombrechoix+10;
            }

            $chapterId++;
        }


        //-----------------------------------------------------------------------------
        //Ajout de l'onglet 7 Croisement exposition
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('7. Croisement exposition');

        $sheet->freezePane('B1');

        $lignLabelChapter = 5;

        $colCritere = "";
        $colEtiquette = "";
        $colEfficient = "";
        $colPeuDegrade = "";
        $colDegrade = "";
        $colFortDegrade = "";
        $colTotalGeneral = "";
        $colTotalNombre = "";
        $colEtiquetteTruncate = "";

        $colCritere="A"; $colEtiquette="B"; $colEfficient="C"; $colPeuDegrade="D"; $colDegrade="E"; $colFortDegrade="F"; $colTotalGeneral="G"; $colTotalNombre="H"; $colEtiquetteTruncate="I";

        $sheet->getColumnDimension($colCritere)->setWidth(20);
        $sheet->getColumnDimension($colEtiquette)->setWidth(20);
        $sheet->getColumnDimension($colEfficient)->setWidth(22);
        $sheet->getColumnDimension($colPeuDegrade)->setWidth(22);
        $sheet->getColumnDimension($colDegrade)->setWidth(22);
        $sheet->getColumnDimension($colFortDegrade)->setWidth(22);
        $sheet->getColumnDimension($colTotalGeneral)->setWidth(15);
        $sheet->getColumnDimension($colTotalNombre)->setWidth(15);

        $sheet->getStyle($colEtiquette . "6:" . $colEtiquette . "50")->getAlignment()->setHorizontal('left');
        $sheet->getStyle($colEfficient . "6:" . $colTotalNombre . "50")->getAlignment()->setHorizontal('center');
        $sheet->getStyle($colEfficient . "6:" . $colTotalGeneral . "50")->getAlignment()->setHorizontal('center');

        $indexCritere = 1;
        $bgColorCritere = "";
        foreach ($tabCritereChoixNegative as $keyCritere => $tabChoix) {

            if($indexCritere == 1) $bgColorCritere = "F8CBAD";
            elseif($indexCritere == 2) $bgColorCritere = "FFE699";
            elseif($indexCritere == 3) $bgColorCritere = "A9D08E";
            elseif($indexCritere == 4) $bgColorCritere = "548235";

            $sheet->setCellValue($colEtiquette.($lignLabelChapter+1), "Étiquettes de lignes");
            $sheet->setCellValue($colEfficient.($lignLabelChapter+1), $quiz->tauxDeSr . ' à ' . $quiz->tauxASr . ' réponses négatives');
            $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1), $quiz->tauxDePdr . ' à ' . $quiz->tauxAPdr .  ' réponses négatives');
            $sheet->setCellValue($colDegrade.($lignLabelChapter+1), $quiz->tauxDeR . ' à ' . $quiz->tauxAR .  ' réponses négatives');
            $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1), $quiz->tauxDeFr . ' à ' . $quiz->tauxAFr .  ' réponses négatives');
            $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1), "Total général");
            $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1), "Total en nombre");


            $sheet->getStyle($colEtiquette.($lignLabelChapter+1).":".$colTotalNombre.($lignLabelChapter+1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle($colEtiquette.($lignLabelChapter+1).":".$colTotalNombre.($lignLabelChapter+1))->getAlignment()->setHorizontal('center');
            $sheet->getStyle($colEfficient.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
            $sheet->getStyle($colPeuDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
            $sheet->getStyle($colDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ED7D31');
            $sheet->getStyle($colFortDegrade.($lignLabelChapter+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');

            $nombrechoix = 1;
            $totalGeneral = 0;
            $totalSansRisque = 0;
            $totalPeuDeRisque = 0;
            $totalRisques = 0;
            $totalFortsRisques = 0;

            foreach ($tabChoix as $keyChoix => $valueChoix) {

                $sheet->setCellValue($colEtiquette.($lignLabelChapter+1+$nombrechoix), $keyChoix);
                //Ecritrure des valeure en blanc masqué pour le graphquique (tronqué à 15 car)
                $sheet->setCellValue($colEtiquetteTruncate.($lignLabelChapter+1+$nombrechoix), substr($keyChoix, 0, 15));
                $sheet->getStyle($colEtiquetteTruncate.($lignLabelChapter+1+$nombrechoix))->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

                $sheet->setCellValue($colEfficient.($lignLabelChapter+1+$nombrechoix), $tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionSansRisque']);
                $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1+$nombrechoix), $tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionPeuDeRisque']);
                $sheet->setCellValue($colDegrade.($lignLabelChapter+1+$nombrechoix), $tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionDesRisques']);
                $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1+$nombrechoix), $tabCritereChoixNegative[$keyCritere][$keyChoix]['percentTauxExpositionDeFortsRisques']);
                $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1+$nombrechoix),'=('.$colTotalNombre.($lignLabelChapter+1+$nombrechoix).'/'.$colTotalNombre.($lignLabelChapter+2+count($tabChoix)).')');

                $totalSansRisque = $totalSansRisque + $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionSansRisque'];
                $totalPeuDeRisque = $totalPeuDeRisque + $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionPeuDeRisque'];
                $totalRisques = $totalRisques + $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionDesRisques'];
                $totalFortsRisques = $totalFortsRisques + $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionDeFortsRisques'];
                $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1+$nombrechoix), $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionSansRisque']+
                    $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionPeuDeRisque']+
                    $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionDesRisques']+
                    $tabCritereChoixNegative[$keyCritere][$keyChoix]['nbTauxExpositionDeFortsRisques']);

                $nombrechoix++;

            }

            $sheet->getStyle($colEtiquette . ($lignLabelChapter+2) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle($colEtiquette . ($lignLabelChapter+1+$nombrechoix) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle($colTotalGeneral . ($lignLabelChapter+2) . ':' . $colTotalGeneral . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
            $sheet->getStyle($colTotalNombre . ($lignLabelChapter+2) . ':' . $colTotalNombre . ($lignLabelChapter+1+$nombrechoix))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

            $sheet->getStyle($colEtiquette . ($lignLabelChapter+2) . ':' . $colTotalGeneral . ($lignLabelChapter+1+$nombrechoix))->getNumberFormat()->setFormatCode('0%');

            $sheet->mergeCells($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix + 1));
            $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getAlignment()->setHorizontal('center');
            $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getAlignment()->setVertical('center');
            $sheet->getStyle($colCritere . ($lignLabelChapter + 1) . ':' . $colCritere . ($lignLabelChapter + $nombrechoix))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($bgColorCritere);
            $sheet->setCellValue($colCritere . ($lignLabelChapter + 1), $keyCritere);

            $totalGeneral = $totalSansRisque + $totalPeuDeRisque + $totalRisques + $totalFortsRisques;

            $sheet->setCellValue($colEtiquette.($lignLabelChapter+1+$nombrechoix), "Total général");
            $sheet->setCellValue($colEtiquetteTruncate.($lignLabelChapter+1+$nombrechoix), "Total général");
            $sheet->getStyle($colEtiquetteTruncate.($lignLabelChapter+1+$nombrechoix))->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            $sheet->setCellValue($colEfficient.($lignLabelChapter+1+$nombrechoix), $totalSansRisque/$totalGeneral);
            $sheet->setCellValue($colPeuDegrade.($lignLabelChapter+1+$nombrechoix), $totalPeuDeRisque/$totalGeneral);
            $sheet->setCellValue($colDegrade.($lignLabelChapter+1+$nombrechoix), $totalRisques/$totalGeneral);
            $sheet->setCellValue($colFortDegrade.($lignLabelChapter+1+$nombrechoix), $totalFortsRisques/$totalGeneral);
            $sheet->setCellValue($colTotalGeneral.($lignLabelChapter+1+$nombrechoix), $totalGeneral/$totalGeneral);
            $sheet->setCellValue($colTotalNombre.($lignLabelChapter+1+$nombrechoix), $totalGeneral);

            $categories = [];
            $categories[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'7. Croisement exposition'!" . '$'.$colEtiquetteTruncate.'$'.($lignLabelChapter+2). ':$'.$colEtiquetteTruncate.'$'.($lignLabelChapter+1+$nombrechoix), null, 3);

            $series = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'7. Croisement exposition'!" . '$'.$colEfficient.'$'.($lignLabelChapter+2).':$'.$colEfficient.'$'.($lignLabelChapter+1+$nombrechoix), null, 3, []),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'7. Croisement exposition'!" . '$'.$colPeuDegrade.'$'.($lignLabelChapter+2).':$'.$colPeuDegrade.'$'.($lignLabelChapter+1+$nombrechoix), null, 3, []),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'7. Croisement exposition'!" . '$'.$colDegrade.'$'.($lignLabelChapter+2).':$'.$colDegrade.'$'.($lignLabelChapter+1+$nombrechoix), null, 3, []),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'7. Croisement exposition'!" . '$'.$colFortDegrade.'$'.($lignLabelChapter+2).':$'.$colFortDegrade.'$'.($lignLabelChapter+1+$nombrechoix), null, 3, []),
            ];

            $legnds = [
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'7. Croisement exposition'!" . '$'.$colEfficient.'$'.($lignLabelChapter+1), null, 3),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'7. Croisement exposition'!" . '$'.$colPeuDegrade.'$'.($lignLabelChapter+1), null, 3),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'7. Croisement exposition'!" . '$'.$colDegrade.'$'.($lignLabelChapter+1), null, 3),
                new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'7. Croisement exposition'!" . '$'.$colFortDegrade.'$'.($lignLabelChapter+1), null, 3),
            ];

            // Créez de la série
            $dataSeries = new DataSeries(
                DataSeries::TYPE_BARCHART,       // Type de graphique : Bar chart
                DataSeries::GROUPING_STACKED,    // Type de regroupement : Empilé
                range(0, count($series) - 1),    // Index des séries
                $legnds,                         // Légendes (laissées vides ici)
                $categories,                     // Catégories (axes X)
                $series                          // Séries de données (valeurs)
            );
            $dataSeries->setPlotDirection(DataSeries::DIRECTION_COLUMN); // Orientation en colonnes (vertical)

            // Création du graphique
            $plotArea = new PlotArea(null, [$dataSeries]); // Zone de tracé
            $legend = new Legend(Legend::POSITION_RIGHT, null, false); // Légende
            $title = new Title("BAROM-CROISEMENT-EXPOSITION"); // Titre du graphique
            $chart = new Chart('Stacked Bar Chart',$title, $legend, $plotArea);

            // ajout du graphique
            $chart->setTopLeftPosition($colEtiquette.($lignLabelChapter+3+$nombrechoix));
            $chart->setBottomRightPosition($colEtiquetteTruncate.($lignLabelChapter+10+$nombrechoix));
            $sheet->addChart($chart);

            $indexCritere++;
            $lignLabelChapter = $lignLabelChapter+$nombrechoix+10;
        }


        $spreadsheet->setActiveSheetIndex(0);


        $docName = "BAROMETRE-" . $quizId;
        $docName = str_replace(' ', '_', $docName);
        $docName = str_replace(',', '-', $docName);
        $docName .= '-' . date("d-m-Y-H-i-s") . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setIncludeCharts(true);
        $callStartTime = microtime(true);
        $writer->save($docName);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($docName);
        $this->downloadFileExcel($docName, $docName);
    }

    public function generateReport() {

        $quizId = $_GET['quizId'];

        $ChaptersInfo = [];
        $quizUserResponsesByChapter = [];
        $resultByChapterQuestion = [];
        $resultByChapter = [];
        $tabChapterCritereChoix = [];
        $tabCritereChoixNegative = [];
        $critere1Values = [];
        $critere2Values = [];
        $critere3Values = [];
        $critere4Values = [];
        $quizUsers = null;
        $quiz = null;
        /*
        $this->getData($quizId, $quiz, $string, $quizQuestions, $ChaptersInfo, $quizUsers, $quizUserResponsesByChapter,
            $resultByChapter, $resultByChapterQuestion, $tabChapterCritereChoix, $tabCritereChoixNegative,
            $critere1Values, $critere2Values, $critere3Values, $critere4Values);
        */
        $presentation = new PhpPresentation();

        $presentation->getLayout()->setDocumentLayout(
            DocumentLayout::LAYOUT_SCREEN_4X3
        );

        $presentation->getDocumentProperties()->setCreator('PHPOffice')
        ->setLastModifiedBy('Relais Managers')
        ->setTitle('Rapport Baromètre')
        ->setSubject('Rapport Baromètre')
        ->setDescription('Rapport Baromètre');

        // Remove first slide
        $presentation->removeSlideByIndex(0);

        //dimension du slide
        $slideWidth = 960;
        $slideHeight = 720;

        // ------------------------------------------------------------------------------------------------------------------------
        // SLIDE 1
        /*$slide = $presentation->createSlide();
        $this->masqueDiapositive($slide, $slideWidth, $slideHeight, 1);

        // Logo RM
        $shape = new File();
        $shape->setPath(BASE_PATH . '/assets/images/logo-rm-simple.png')->setHeight(100)->setOffsetX(50)->setOffsetY(40);
        $slide->addShape($shape);

        //Logo client
        //On recupere les infos du quiz
        $quizRepository = new QuizRepository();
        $quiz = $quizRepository->getQuizById($quizId);
        if ($quiz->logo) {
            $shape = new File();
            $shape->setPath(BASE_PATH . '/assets/images/logosClients/' . $quiz->logo)->setHeight(80)->setOffsetX(650)->setOffsetY(40);
            //$shape->setPath(BASE_PATH . '/assets/images/logosClients/182c3af48851035cebc970cbf5b0e829.png')->setHeight(80)->setOffsetX(650)->setOffsetY(40);
            $slide->addShape($shape);
        }

        // Les Cadre de couleur sur la gauche
        $shape = new RichText();
        $shape->setOffsetX(0)->setOffsetY($slideHeight-500)->setWidth(70)->setHeight(70);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $slide->addShape($shape);
        $shape = new RichText();
        $shape->setOffsetX(140)->setOffsetY($slideHeight-430)->setWidth(70)->setHeight(70);
        //transparance - 20% CC - 10%  E6
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('CC696252'));
        $slide->addShape($shape);
        $shape = new RichText();
        $shape->setOffsetX(70)->setOffsetY($slideHeight-360)->setWidth(70)->setHeight(70);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('CC696252'));
        $slide->addShape($shape);
        $shape = new RichText();
        $shape->setOffsetX(0)->setOffsetY($slideHeight-290)->setWidth(70)->setHeight(70);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('CC696252'));
        $slide->addShape($shape);
        $shape = new RichText();
        $shape->setOffsetX(140)->setOffsetY($slideHeight-290)->setWidth(70)->setHeight(70);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('E6696252'));
        $slide->addShape($shape);

        $shape = new RichText();
        $shape->setOffsetX(210)->setOffsetY($slideHeight-500)->setWidth(750)->setHeight(280);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $paragraph1 = $shape->createParagraph();
        $paragraph1->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $paragraph1->getAlignment()->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_TOP);
        $text = $paragraph1->createTextRun('Baromètre');
        $text->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $text->getFont()->setSize(32);
        $paragraph1 = $shape->createParagraph();
        $paragraph1->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $paragraph1->getAlignment()->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_TOP);
        $text = $paragraph1->createTextRun(' ');
        $text->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('696252'));
        $text->getFont()->setSize(15);
        $paragraph1 = $shape->createParagraph();
        $paragraph1->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $paragraph1->getAlignment()->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_TOP);
        $text = $paragraph1->createTextRun('Performance et bien vivre son travail');
        $text->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFF'));
        $text->getFont()->setSize(32);
        $slide->addShape($shape);

        $shape = new RichText();
        $shape->setOffsetX(700)->setOffsetY(600)->setWidth(250)->setHeight(30);
        $textRun = $shape->createTextRun('Date ' . date("d/m/Y"));
        $textRun->getFont()->setName('Trebuchet MS')->setSize(18)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $slide->addShape($shape);
*/


        // ------------------------------------------------------------------------------------------------------------------------
        // SLIDE 2
        $slide = $presentation->createSlide();
        $this->masqueDiapositive($slide, $slideWidth, $slideHeight, 2);

        $shape = $slide->createRichTextShape()->setHeight($slideHeight-150)->setWidth($slideWidth)->setOffsetX(0)->setOffsetY(20);

        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("1. Rappel : A quoi sert un baromètre « performance + QVT » ?");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(18)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));

        //$bulletChar = "■"; // Carré plein Unicode U+25A0
        $bulletChar = "-";
        $items = ['ses raisons d’être', 'ce qu’il évalue', 'ce qu’il premet'];
        foreach ($items as $item) {
            $paragraph = $shape->createParagraph();
            $paragraph->getFont()->setName('Trebuchet MS')->setSize(16)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
            $paragraph->getAlignment()->setMarginLeft(80);
            $textRun = $shape->createTextRun($bulletChar . "  ");
            $textRun = $shape->createTextRun($item);
        }

        $shape->createParagraph()->createTextRun('');

        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("2. Quels résultats « macros » nous donne le baromètre réalisé pour votre entreprise ?");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(18)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));

        $shape->createParagraph()->createTextRun('');

        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("3. Synthèse des résultats macros");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(18)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));


        $shape->createParagraph()->createTextRun('');

        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("4. Quels résultats « micros » essentiels nous donne le baromètre réalisé pour votre entreprise ?");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(18)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));


        // ------------------------------------------------------------------------------------------------------------------------
        // SLIDE 3
        $slide = $presentation->createSlide();
        $this->masqueDiapositive($slide, $slideWidth, $slideHeight, 3);

        $shape = $slide->createRichTextShape()->setHeight($slideHeight-80)->setWidth($slideWidth)->setOffsetX(0)->setOffsetY(0);
        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("1. A quoi sert un baromètre « performance et QVT » ?");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(16)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));

        $shape->createParagraph()->createTextRun('');

        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("Ses raisons d’être");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(15)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));

        $items = ["Les entreprises sont confrontées à des contextes de + en + complexes, à des changements constants...",
                  "Elles n’ont d’autres choix que de développer en permanence leur agilité et leur résilience, leur ingéniosité, leur efficience, leurs performances",
                  "Les équipes* sont la clef de voûte. Tout repose sur :"];
        foreach ($items as $item) {
            $shape->createParagraph()->createTextRun('');
            $paragraph = $shape->createParagraph();
            $alignment = $paragraph->getAlignment();
            $alignment->setMarginLeft(30);
            $bulletChar = "■";
            $textRun = $shape->createTextRun($bulletChar . "  ");
            $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
            $textRun = $shape->createTextRun($item);
            $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        }

       // $shape->createParagraph()->createTextRun('');

        $items = ["leur mobilisation",
                "leur talent",
                "leur inventivité",
                "leur adaptabilité",
                "leur recours à l’intelligence collective",
                "leur bien-vivre leur travail"];

        foreach ($items as $item) {
            $paragraph = $shape->createParagraph();
            $alignment = $paragraph->getAlignment();
            $alignment->setMarginLeft(60);
            $bulletChar = "-";
            $textRun = $shape->createTextRun($bulletChar . "  ");
            $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
            $textRun = $shape->createTextRun($item);
            $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        }

        $shape->createParagraph()->createTextRun('');

        $paragraph = $shape->createParagraph();
        $alignment = $paragraph->getAlignment();
        $alignment->setMarginLeft(30);
        $bulletChar = "■";
        $textRun = $shape->createTextRun($bulletChar . "  ");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $textRun = $shape->createTextRun("Cette performance humaine et ce bien-vivre son travail reposent sur des facteurs aujourd’hui identifiés");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));

        $shape->createParagraph()->createTextRun('');
        $shape->createParagraph()->createTextRun('');
        $shape->createParagraph()->createTextRun('');

        $paragraph = $shape->createParagraph();
        $alignment = $paragraph->getAlignment();
        $alignment->setMarginLeft(120);
        $textRun = $shape->createTextRun("*");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $textRun = $shape->createTextRun("  « équipe » ici signifie : « toutes les personnes qui œuvrent dans l’entreprise quelque soit leur statut ");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(11)->setItalic(true)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));



        // ------------------------------------------------------------------------------------------------------------------------
        // SLIDE 4
        $slide = $presentation->createSlide();
        $this->masqueDiapositive($slide, $slideWidth, $slideHeight, 4);

        $shape = $slide->createRichTextShape()->setHeight($slideHeight-80)->setWidth($slideWidth)->setOffsetX(0)->setOffsetY(0);
        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("1. A quoi sert un baromètre « performance et QVT » ?");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(16)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));

        $this->addLignEmpty($shape, 12);

        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("Ce qu’il évalue");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(15)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));

        $this->addLignEmpty($shape, 12);

        $paragraph = $shape->createParagraph();
        $alignment = $paragraph->getAlignment();
        $alignment->setMarginLeft(30);
        $bulletChar = "■";
        $textRun = $shape->createTextRun($bulletChar . "  ");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $textRun = $shape->createTextRun("La présence, l’efficience ou la mise à mal de ces facteurs, indispensables à la QVT et à la performance");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));

        $this->addLignEmpty($shape, 12);

        $paragraph = $shape->createParagraph();
        $alignment = $paragraph->getAlignment();
        $alignment->setMarginLeft(30);
        $bulletChar = "■";
        $textRun = $shape->createTextRun($bulletChar . "  ");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $textRun = $shape->createTextRun("D’éventuels risques, à terme, de dégradation de la QVT et de la performance");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $textRun = $shape->createTextRun(" (liens avec les facteurs étudiés)");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(12)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));

        $items = ["Le pourcentage de personnes qui pourraient être touchées par ces risques, le cas échéant.Les éventuelles catégories qui seraient plus concernées que d’autres",
            "La présence de mal-être en devenir ou avéré"];
        foreach ($items as $item) {
            $this->addLignEmpty($shape, 8);
            $paragraph = $shape->createParagraph();
            $alignment = $paragraph->getAlignment();
            $alignment->setMarginLeft(30);
            $bulletChar = "■";
            $textRun = $shape->createTextRun($bulletChar . "  ");
            $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
            $textRun = $shape->createTextRun($item);
            $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        }

        $this->addLignEmpty($shape, 12);

        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("Ce qu’il permet");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(15)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));

        $this->addLignEmpty($shape, 8);

        $paragraph = $shape->createParagraph();
        $alignment = $paragraph->getAlignment();
        $alignment->setMarginLeft(30);
        $bulletChar = "■";
        $textRun = $shape->createTextRun($bulletChar . "  ");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $textRun = $shape->createTextRun("Déterminer les actions préventives et/ou correctives (voire curative) à mettre en œuvre pour préserver, ou renforcer ou reconstruire la performance et la QVT");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));

        $this->addLignEmpty($shape, 8);
        $paragraph = $shape->createParagraph();
        $alignment = $paragraph->getAlignment();
        $alignment->setMarginLeft(30);
        $bulletChar = "■";
        $textRun = $shape->createTextRun($bulletChar . "  ");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $textRun = $shape->createTextRun("Cibler les actions");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $textRun = $shape->createTextRun(" (éviter les pertes de temps et d’énergie inutiles) ");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(12)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $textRun = $shape->createTextRun("grâce :");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));

        $items = ["aux facteurs : 20/80",
            "aux critères de croisement",
            "au détail des résultats de chaque facteur, question par question"];

        foreach ($items as $item) {
            $paragraph = $shape->createParagraph();
            $alignment = $paragraph->getAlignment();
            $alignment->setMarginLeft(60);
            $bulletChar = "-";
            $textRun = $shape->createTextRun($bulletChar . "  ");
            $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
            $textRun = $shape->createTextRun($item);
            $textRun->getFont()->setName('Trebuchet MS')->setSize(12)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        }

        $items = ["Impliquer tous les acteurs",
            "Préserver ou renforcer le dialogue social sur la performance et la QVT",
            "Améliorer et pérenniser la performance et le bien-vivre son travail"];
        foreach ($items as $item) {
            $this->addLignEmpty($shape, 8);
            $paragraph = $shape->createParagraph();
            $alignment = $paragraph->getAlignment();
            $alignment->setMarginLeft(30);
            $bulletChar = "■";
            $textRun = $shape->createTextRun($bulletChar . "  ");
            $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
            $textRun = $shape->createTextRun($item);
            $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        }


        // ------------------------------------------------------------------------------------------------------------------------
        // SLIDE 4
        $slide = $presentation->createSlide();
        $this->masqueDiapositive($slide, $slideWidth, $slideHeight, 4);

        $shape = $slide->createRichTextShape()->setHeight($slideHeight-80)->setWidth($slideWidth)->setOffsetX(0)->setOffsetY(0);
        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(20);
        $textRun = $paragraph->createTextRun("1. A quoi sert un baromètre « performance et QVT » ?");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(16)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));

        $this->addLignEmpty($shape, 12);

        $paragraph = $shape->createParagraph();
        $paragraph->getAlignment()->setMarginLeft(40);
        $textRun = $paragraph->createTextRun("Les 8 facteurs qui doivent être efficients (partout, dans tous les périmètres)");
        $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(true)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));


        $shape = new RichText();
        $shape->setOffsetX($slideWidth-100)->setOffsetY(65)->setWidth(100)->setHeight(40);
        $paragraph = $shape->getParagraph(0);
        $paragraph->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $paragraph->getAlignment()->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_TOP);
        $text = $paragraph->createTextRun('8R ©');
        $text->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $text->getFont()->setSize(24)->setBold(true);
        $slide->addShape($shape);

        $this->addLignEmpty($shape, 12);

        $shape = new RichText();
        $shape->setOffsetX(50)->setOffsetY(110)->setWidth(350)->setHeight(50);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFFFF'));
        $shape->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
        $shape->getBorder()->setLineWidth(1);
        $shape->getBorder()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B')); // Rouge
        $paragraph1 = $shape->getParagraph(0);
        $paragraph1->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $paragraph1->getAlignment()->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_TOP);
        $text = $paragraph1->createTextRun('RESILIENCE, BIEN VIVRE, QVCT');
        $text->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $text->getFont()->setSize(12)->setBold(true);
        $paragraph1 = $shape->createParagraph();
        $paragraph1->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $paragraph1->getAlignment()->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_TOP);
        $text = $paragraph1->createTextRun(' ');
        $text->getFont()->setSize(12);
        $slide->addShape($shape);

        $shape = new RichText();
        $shape->setOffsetX(450)->setOffsetY(110)->setWidth(350)->setHeight(50);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FFFFFFFF'));
        $shape->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_SINGLE);
        $shape->getBorder()->setLineWidth(1);
        $shape->getBorder()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B')); // Rouge
        $paragraph1 = $shape->getParagraph(0);
        $paragraph1->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $paragraph1->getAlignment()->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_TOP);
        $text = $paragraph1->createTextRun('EFFICIENCE – PERFORMANCE');
        $text->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $text->getFont()->setSize(12)->setBold(true);
        $paragraph1 = $shape->createParagraph();
        $paragraph1->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $paragraph1->getAlignment()->setVertical(\PhpOffice\PhpPresentation\Style\Alignment::VERTICAL_TOP);
        $text = $paragraph1->createTextRun(' ');
        $text->getFont()->setSize(12);
        $slide->addShape($shape);

        // Logo RM
        $shape = new File();
        $shape->setPath(BASE_PATH . '/assets/images/flecheRouge.png')->setHeight(222)->setOffsetX(410)->setOffsetY(130);
        $slide->addShape($shape);
        /*
                $paragraph = $shape->createParagraph();
                $paragraph->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET)->setBulletChar("■")->setBulletColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
                $alignment = $paragraph->getAlignment();
                $alignment->setMarginLeft(50);  // Espace entre la puce et le texte
                $alignment->setMarginRight(50); // Espace après le texte
                $textRun = $paragraph->createTextRun("\tDes textes");
                $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));

                $paragraph = $shape->createParagraph();
                $paragraph->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET); // Active les puces classiques
                $textRun = $paragraph->createTextRun("Des textes encore");
                $textRun->getFont()->setName('Trebuchet MS')->setSize(14)->setBold(false)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        */

/*
        $slide = $presentation->createSlide();

        // Créer un tableau avec 3 colonnes et 4 lignes
        $table = $slide->createTableShape(3)
            ->setHeight(600)
            ->setWidth(800)
            ->setOffsetX(100)
            ->setOffsetY(100);

        // Ajouter les lignes et cellules
        $rowsData = [
            ["Nom", "Âge", "Ville"],
            ["Alice", "30", "Paris"],
            ["Bob", "25", "Lyon"],
            ["Charlie", "35", "Marseille"]
        ];

        // Pour chaque ligne de données
        foreach ($rowsData as $rowIndex => $rowData) {
            $row = $table->createRow();
            $row->setHeight(40);

            // Chaque ligne contient déjà 3 cellules (car nombre de colonnes = 3)
            // On remplit les cellules existantes avec le texte
            for ($i = 0; $i < 3; $i++) {
                $cell = $row->getCell($i);
                $textRun = $cell->createTextRun($rowData[$i]);
                $textRun->getFont()->setSize(14);
                $textRun->getFont()->setBold($rowIndex === 0); // en-tête en gras
                $textRun->getFont()->setColor(new \PhpOffice\PhpPresentation\Style\Color('000000'));
            }
        }
*/
        //------------------------------------------
/*
        $slide = $presentation->createSlide();


        $data = [
            ['1', 132, 471, 153, 34],
            ['2', 226, 329, 198, 37],
            ['3', 117, 460, 181, 32],
            ['4', 247, 402, 117, 24],
            ['5', 118, 478, 160, 34],
            ['6', 77, 376, 257, 80],
            ['7', 92, 397, 259, 42],
            ['8', 123, 392, 201, 74],
        ];

        //var_dump( BASE_PATH . 'assets\font\trebuc.ttf');

        $plot = new PHPlot(800, 600);

        //$plot->SetDataColors(array('92D050 ', 'FFFF00', 'ED7D31', 'FF0000'));
        //$plot->SetTitle('Ventes mensuelles (barres empilées)');
        //$plot->SetXTitle('Mois');
        //$plot->SetYTitle('Ventes');


        $plot->SetDrawXGrid(False); // Supprimer les lignes verticales
        $plot->SetDrawYGrid(False); // Supprimer les lignes horizontales

        $plot->SetLineWidths([1, 2, 2]); // Épaisseur des lignes par série (utile si barres avec contour)

        $plot->SetYDataLabelPos('plotstack');
        $plot->SetDrawDashedGrid(False);     // Lignes de fond
        $plot->SetShading(0);                // 0 = sans ombrage, >0 = ombrage
        $plot->SetDrawXDataLabelLines(False); // Pas de traits entre label X et barres

        $plot->SetPlotType('stackedbars');
        $plot->SetDataColors(array('#92D050', '#FFFF00', '#ED7D31', '#FF0000'));
        $plot->SetDataType('text-data');
        $plot->SetDataValues($data);

        //$plot->SetLegend(['Produit A', 'Produit B', 'Produit C', 'Produit D']);
        //$plot->SetLegendPosition(0, 0, 'plot', 0, 0, 10, 10);

        // Changer la police des valeurs sur les barres
        if(\Appy\Src\Config::ENV == 'PROD') {
            $plot->SetTTFPath(BASE_PATH . "/assets/font");
        } else {
            $plot->SetTTFPath('C:\wamp64\www\relais-managers-services\assets\font');
        }

        $plot->SetUseTTF(true);
        $font = 'trebuc.ttf';
        $plot->SetFontTTF('title', $font);
        $plot->SetFontTTF('x_title', $font);
        $plot->SetFontTTF('y_title', $font);
        $plot->SetFontTTF('x_label', $font);
        $plot->SetFontTTF('y_label', $font);

        $imageName = "graph";
        $imageName .= '-' . date("d-m-Y-H-i-s") . '.png';
        $plot->SetOutputFile($imageName);
        $plot->SetIsInline(true); // pour éviter l'affichage

// Génère le graphique
        $plot->DrawGraph();


        // Ajouter une image
        $shape = new File();
        $shape->setName('Mon image')
            ->setDescription('Description de l\'image')
            ->setPath($imageName)  // <- Remplace par le bon chemin
            ->setHeight(600)
            ->setOffsetX(100)
            ->setOffsetY(100);

        $slide->addShape($shape);*/

        $fileName = "BAROMETRE-RAPPORT";
        $fileName .= '-' . date("d-m-Y-H-i-s") . '.pptx';


        $oWriterPPTX = \PhpOffice\PhpPresentation\IOFactory::createWriter($presentation, 'PowerPoint2007');
        $oWriterPPTX->save($fileName);

       if (file_exists($fileName)) {

            header('Content-Description: File Transfer');
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header('Content-Disposition: attachment; filename=' . basename($fileName));
            header("Content-Transfer-Encoding: binary");
            header("Expires: 0");
            header("Pragma: public");
            //header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Cache-Control: no cache');
            header('Content-Length: ' . filesize($fileName));

            ob_clean();
            flush();

            readfile($fileName);
            exit();
        }

    }

    private function addLignEmpty($shape, $size) {
        $paragraph = $shape->createParagraph();
        $textRun = $shape->createTextRun(" ");
        $textRun->getFont()->setName('Trebuchet MS')->setSize($size);
    }

    private function masqueDiapositive($slide, $slideWidth, $slideHeight, $numberDiapositive) {

        // Cadre de couleur en haut à droite
        // 'FF' = opacité maximale + code couleur
        $shape = new RichText();
        $shape->setOffsetX($slideWidth - 70)->setOffsetY(0)->setWidth(70)->setHeight(15);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $slide->addShape($shape);

        // Cadre de couleur en bas
        $shape = new RichText();
        $shape->setOffsetX(0)->setOffsetY($slideHeight-15)->setWidth($slideWidth)->setHeight(15);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $slide->addShape($shape);


        $shape = new File();
        $shape->setPath(BASE_PATH . '/assets/images/logo-lettre-rm.png')->setHeight(60)->setOffsetX(45)->setOffsetY($slideHeight-80);
        $slide->addShape($shape);

        $shape = new RichText();
        $shape->setOffsetX(100)->setOffsetY($slideHeight-40)->setWidth(300)->setHeight(30);
        $textRun = $shape->createTextRun('© RELAIS ');
        $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $textRun = $shape->createTextRun('m');
        $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FFE9660B'));
        $textRun = $shape->createTextRun('anagers');
        $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
        $slide->addShape($shape);

        if($numberDiapositive != 1) {
            $shape = new RichText();
            $shape->setOffsetX($slideWidth-80)->setOffsetY($slideHeight-40)->setWidth(30)->setHeight(30);
            $textRun = $shape->createTextRun($numberDiapositive);
            $textRun->getFont()->setName('Trebuchet MS')->setSize(10)->setColor(new \PhpOffice\PhpPresentation\Style\Color('FF696252'));
            $slide->addShape($shape);
        }
    }

    public function generateWord() {

        $quizId = $_GET['quizId'];

        //On recupere les infos du quiz
        $quizRepository = new QuizRepository();
        $quiz = $quizRepository->getQuizById($quizId);

        $userRepository = new UsersRepository();
        $quizQuestionRepository = new QuizQuestionRepository();
        $quizUserResponseRepository = new QuizUserResponseRepository();
        $quizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
        $responseQuizCriteresBarometreRepository = new ResponseQuizCriteresBarometreRepository();

        //Recuperation du libellé des questions
        $quizQuestions = $quizQuestionRepository->getQuestionsByQuizIdAndType($quizId, 'INPUT-RADIO');

        //Recueration des chapitres
        $quizChapters = $quizQuestionRepository->getChapterBarometre($quiz->id);
        $ChaptersInfo = [];
        $categoriesForGraph = [];
        $categoriesLabelForGraph = [];
        $chapterNumber = 1;
        foreach ($quizChapters as $quizChapter) {
            //On ne prend par les chapitres vides
            if(trim($quizChapter->label) != '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold"></div>') {
                $ChaptersInfo[$chapterNumber]['label'] = $quizChapter->label;
                $ChaptersInfo[$chapterNumber]['number'] = $chapterNumber;

                $categoriesForGraph[] = $chapterNumber;
                $categoriesLabelForGraph[] = $this->formatChapterForWord($quizChapter->label);

                $chapterNumber = $chapterNumber + 1;
            }
        }

        $resultByChapter = [];
        $resultByChapterQuestion = [];

        //Recueration des réponses
        $quizUserResponsesByChapter = [];
        $critereRecherche = [];
        $critereRecherche['quizId'] = $quiz->id;
        $critereRecherche['questionReportOrderPlage'] = "(1,2,3,4,5,6,7,8,9,10)";
        $quizUserResponsesByChapter[1] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(11,12,13,14,15,16,17,18,19,20)";
        $quizUserResponsesByChapter[2] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(21,22,23,24,25,26,27,28,29,30)";
        $quizUserResponsesByChapter[3] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(31,32,33,34,35,36,37,38,39,40)";
        $quizUserResponsesByChapter[4] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(41,42,43,44,45,46,47,48,49,50)";
        $quizUserResponsesByChapter[5] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(51,52,53,54,55,56,57,58,59,60)";
        $quizUserResponsesByChapter[6] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(61,62,63,64,65,66,67,68,69,70)";
        $quizUserResponsesByChapter[7] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        $critereRecherche['questionReportOrderPlage'] = "(71,72,73,74,75,76,77,78,79,80)";
        $quizUserResponsesByChapter[8] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        if (array_key_exists('9', $ChaptersInfo)) {
            $critereRecherche['questionReportOrderPlage'] = "(81,82,83,84,85,86,87,88,89,90)";
            $quizUserResponsesByChapter[9] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        }
        if (array_key_exists('10', $ChaptersInfo)) {
            $critereRecherche['questionReportOrderPlage'] = "(91,92,93,94,95,96,97,98,99,100)";
            $quizUserResponsesByChapter[10] = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
        }

        //Recuperation des critères et valeur
        // construction d'un tableau de critere puis valeurs
        $QuizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
        $quizCriteresBarometre = $QuizCriteresBarometreRepository->getCriteresByQuizId($quiz->id);

        $tabCritereChoix = [];
        foreach($quizCriteresBarometre as $keyCritere => $value) {
            if(isset($value->titre)) {
                if($value->choix1) $tabCritereChoix[$value->titre]['A'] = $value->choix1;
                if($value->choix2) $tabCritereChoix[$value->titre]['B'] = $value->choix2;
                if($value->choix3) $tabCritereChoix[$value->titre]['C'] = $value->choix3;
                if($value->choix4) $tabCritereChoix[$value->titre]['D'] = $value->choix4;
                if($value->choix5) $tabCritereChoix[$value->titre]['E'] = $value->choix5;
                if($value->choix6) $tabCritereChoix[$value->titre]['F'] = $value->choix6;
                if($value->choix7) $tabCritereChoix[$value->titre]['G'] = $value->choix7;
                if($value->choix8) $tabCritereChoix[$value->titre]['H'] = $value->choix8;
                if($value->choix9) $tabCritereChoix[$value->titre]['I'] = $value->choix9;
                if($value->choix10) $tabCritereChoix[$value->titre]['J'] = $value->choix10;
            }
        }

        //On boucle sur les chapitre puis les critre puis les choix
        //Pour chacun on parcours les questions
        $tabChapterCritereChoix = [];
        for ($ChapterId = 1; $ChapterId <= 10; $ChapterId++) {
            $indexCritere = 1;
            foreach($tabCritereChoix as $keyCritere => $tabChoix) {
                foreach($tabChoix as $keyChoix => $value) {

                    $plageQuestion = "";
                    if($ChapterId == 1) $plageQuestion = "(1,2,3,4,5,6,7,8,9,10)";
                    elseif($ChapterId == 2) $plageQuestion = "(11,12,13,14,15,16,17,18,19,20)";
                    elseif($ChapterId == 3) $plageQuestion = "(21,22,23,24,25,26,27,28,29,30)";
                    elseif($ChapterId == 4) $plageQuestion = "(31,32,33,34,35,36,37,38,39,40)";
                    elseif($ChapterId == 5) $plageQuestion = "(41,42,43,44,45,46,47,48,49,50)";
                    elseif($ChapterId == 6) $plageQuestion = "(51,52,53,54,55,56,57,58,59,60)";
                    elseif($ChapterId == 7) $plageQuestion = "(61,62,63,64,65,66,67,68,69,70)";
                    elseif($ChapterId == 8) $plageQuestion = "(71,72,73,74,75,76,77,78,79,80)";
                    elseif($ChapterId == 9) $plageQuestion = "(81,82,83,84,85,86,87,88,89,90)";
                    elseif($ChapterId == 10) $plageQuestion = "(91,92,93,94,95,96,97,98,99,100)";

                    $critereRecherche = [];
                    $critereRecherche['quizId'] = $quiz->id;
                    $critereRecherche['questionReportOrderPlage'] = $plageQuestion;
                    $critereRecherche['column-critere-name'] = "response_critere" . $indexCritere;
                    $critereRecherche['choix'] = $keyChoix;

                    if ($ChapterId == 9) {
                        if(array_key_exists('9', $ChaptersInfo)) {
                            $result = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value] = $result;
                        }
                    }
                    elseif ($ChapterId == 10) {
                        if( array_key_exists('10', $ChaptersInfo)) {
                            $result = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
                            $tabChapterCritereChoix[$ChapterId][$keyCritere][$value] = $result;
                        }
                    }
                    else {
                        $result = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "user-ordre");
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value] = $result;
                    }

                    if(isset($tabChapterCritereChoix[$ChapterId][$keyCritere][$value])) {


                        /*var_dump($ChapterId . '<br>');
                        var_dump($indexCritere . '<br>');
                        var_dump($keyChoix . '<br>');*/

                        $nbEfficient = 0;
                        $nbPeuDegrade = 0;
                        $nbDegrade = 0;
                        $nbFortDegrade = 0;
                        $totalReponses = 0;
                        $valueTotalByUser = 0;
                        $quizUserId = 0;
                        $nombreFortsRisques = 0;
                        $nombreRisques = 0;
                        $nombrePeuDeRisque = 0;
                        $nombreSansRisque = 0;
                        $valueCoef = 0;
                        $lign = 1;

                        foreach ($result as $quizUserResponse) {

                            //Nbre de fois où un item à reçu des réponses :
                            //  "pas du tout vrai" = fort dégradé ; "Plutôt pas vrai" = dégradé ; "plutôt vrai" = peu dégradé; "tout à fait vrai" = efficient.
                            // (Nota -  Pour l'item 2, le principe est inversé : "pas du tout vrai" = efficient ; "plutôt pas vrai" = peu dégradé;
                            if ($ChapterId == 2) {
                                if ($quizUserResponse->value == 'TAFV') $nbFortDegrade = $nbFortDegrade + 1;
                                elseif ($quizUserResponse->value == 'PV') $nbDegrade = $nbDegrade + 1;
                                elseif ($quizUserResponse->value == 'PPV') $nbPeuDegrade = $nbPeuDegrade + 1;
                                elseif ($quizUserResponse->value == 'PDTV') $nbEfficient = $nbEfficient + 1;
                            } else {
                                if ($quizUserResponse->value == 'TAFV') $nbEfficient = $nbEfficient + 1;
                                elseif ($quizUserResponse->value == 'PV') $nbPeuDegrade = $nbPeuDegrade + 1;
                                elseif ($quizUserResponse->value == 'PPV') $nbDegrade = $nbDegrade + 1;
                                elseif ($quizUserResponse->value == 'PDTV') $nbFortDegrade = $nbFortDegrade + 1;
                            }

                            $totalReponses = $totalReponses + 1;

                            //Si on traite un nouveau user
                            //On remet les compteur à zéro
                            if ($lign != 1 && $quizUserResponse->quizUserId != $quizUserId) {

                                //On stocke le resultat
                                //En fonction de la valeur on somme dans la bonne variable de risque
                                if ($valueTotalByUser >= $quiz->risqueDeFr) $nombreFortsRisques = $nombreFortsRisques + 1;
                                elseif ($valueTotalByUser >= $quiz->risqueDeR && $valueTotalByUser < $quiz->risqueAFr) $nombreRisques = $nombreRisques + 1;
                                elseif ($valueTotalByUser >= $quiz->risqueDePdr && $valueTotalByUser < $quiz->risqueAR) $nombrePeuDeRisque = $nombrePeuDeRisque + 1;
                                elseif ($valueTotalByUser <= $quiz->risqueASr) $nombreSansRisque = $nombreSansRisque + 1;

                                //On remet à 0 le total par user
                                $valueTotalByUser = 0;

                                if ($ChapterId == 2) {
                                    if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefPdtv;
                                    elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPpv;
                                    elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPv;
                                    elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefTafv;

                                } else {
                                    if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefTafv;
                                    elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPv;
                                    elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPpv;
                                    elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefPdtv;
                                }

                                $valueTotalByUser = $valueTotalByUser + $valueCoef;

                            } else {
                                //On convertir la string en valeur selon le paramétrage
                                //on inverse la logique pour le chapitre
                                if ($ChapterId == 2) {
                                    if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefPdtv;
                                    elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPpv;
                                    elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPv;
                                    elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefTafv;
                                } else {
                                    if ($quizUserResponse->value == 'TAFV') $valueCoef = $quiz->coefTafv;
                                    elseif ($quizUserResponse->value == 'PV') $valueCoef = $quiz->coefPv;
                                    elseif ($quizUserResponse->value == 'PPV') $valueCoef = $quiz->coefPpv;
                                    elseif ($quizUserResponse->value == 'PDTV') $valueCoef = $quiz->coefPdtv;
                                }

                                $valueTotalByUser = $valueTotalByUser + $valueCoef;
                            }

                            $quizUserId = $quizUserResponse->quizUserId;
                            $lign = $lign + 1;

                        }

                        //On stocke le resultat du dernier user
                        if ($valueTotalByUser >= $quiz->risqueDeFr) $nombreFortsRisques = $nombreFortsRisques + 1;
                        elseif ($valueTotalByUser >= $quiz->risqueDeR && $valueTotalByUser < $quiz->risqueAFr) $nombreRisques = $nombreRisques + 1;
                        elseif ($valueTotalByUser >= $quiz->risqueDePdr && $valueTotalByUser < $quiz->risqueAR) $nombrePeuDeRisque = $nombrePeuDeRisque + 1;
                        elseif ($valueTotalByUser <= $quiz->risqueASr) $nombreSansRisque = $nombreSansRisque + 1;

                        //Caulcul de l'etat des facteurs
                        $totalReponses = $nbEfficient + $nbPeuDegrade + $nbDegrade + $nbFortDegrade;
                        $percentReponsesEfficient = number_format($nbEfficient * 100 / $totalReponses, 0, ',', '');
                        $percentReponsesPeuDegrade = number_format($nbPeuDegrade * 100 / $totalReponses, 0, ',', '');
                        $percentReponsesDegrade = number_format($nbDegrade * 100 / $totalReponses, 0, ',', '');
                        $percentReponsesFortDegrade = number_format($nbFortDegrade * 100 / $totalReponses, 0, ',', '');

                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbTotal'] = $totalReponses;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbEfficient'] = $nbEfficient;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbPeuDegrade'] = $nbPeuDegrade;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbDegrade'] = $nbDegrade;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbFortDegrade'] = $nbFortDegrade;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentEfficient'] = $percentReponsesEfficient;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentPeuDegrade'] = $percentReponsesPeuDegrade;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentDegrade'] = $percentReponsesDegrade;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentFortDegrade'] = $percentReponsesFortDegrade;

                        //Caulcul de l'impact
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbFortsRisques'] = $nombreFortsRisques;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbRisques'] = $nombreRisques;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbPeuDeRisque'] = $nombrePeuDeRisque;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['nbSansRisque'] = $nombreSansRisque;
                        $nombreDetousLesRisquesCalculated = $nombreFortsRisques + $nombreRisques + $nombrePeuDeRisque + $nombreSansRisque;
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentFortsRisques'] = number_format($nombreFortsRisques * 100 / $nombreDetousLesRisquesCalculated, 0, ',', '');
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentRisques'] = number_format($nombreRisques * 100 / $nombreDetousLesRisquesCalculated, 0, ',', '');
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentPeuDeRisque'] = number_format($nombrePeuDeRisque * 100 / $nombreDetousLesRisquesCalculated, 0, ',', '');
                        $tabChapterCritereChoix[$ChapterId][$keyCritere][$value]['percentSansRisque'] = number_format($nombreSansRisque * 100 / $nombreDetousLesRisquesCalculated, 0, ',', '');

                        /*
var_dump($totalReponses . '<br>');
var_dump($nbEfficient . '<br>');
var_dump($nbPeuDegrade . '<br>');
var_dump($nbDegrade . '<br>');
var_dump($nbFortDegrade . '<br>');
var_dump($nombreDeFortsRisques . '<br>');
var_dump($nombreDesRisques . '<br>');
var_dump($nombrePeuDeRisque . '<br>');
var_dump($nombreSansRisque . '<br>');
*/
                    }
                }
                $indexCritere++;
            }
        }

        /*
             On recupere la liste des critere et des reponses au critere
     ANCIENNETE - 0 à 5
     ANCIENNETE - 5 à 15
     ANCIENNETE - Plus de 15
     FONCTION - 0 à 5
     FONCTION - 5 à 15
     FONCTION - Plus de 15*/

        //Init des index du tableau
        $j = 1;
        $questionId = 0;
        foreach ($ChaptersInfo as $Chapter) {
            foreach ($quizUserResponsesByChapter[$j] as $quizUserResponse) {
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbEfficient'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbPeuDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbFortDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['nbTotal'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['percentEfficient'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['percentPeuDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['percentDegrade'] =  0;
                $resultByChapterQuestion[$j][$quizUserResponse->questionId]['percentFortDegrade'] =  0;
            }
            $j = $j + 1;
        }

        //Chapitre de 1 à 10
        $i = 1;
        // comptage pour chaque item et calcul des pourcentages
        foreach ($ChaptersInfo as $Chapter) {

            //////////////////////////////////
            //CALCUL pour l'ETAT DES IMPACTS
            //////////////////////////////////

            $totalReponsesChapitre = 0;
            $nombreReponsesChapitreEfficient = 0;
            $nombreReponsesChapitrePeuDegrade = 0;
            $nombreReponsesChapitreDegrade = 0;
            $nombreReponsesChapitreFortDegrade = 0;

            foreach ($quizUserResponsesByChapter[$i] as $quizUserResponse) {

                //Nbre de fois où un item à reçu des réponses :
                //  "pas du tout vrai" = fort dégradé ; "Plutôt pas vrai" = dégradé ; "plutôt vrai" = peu dégradé; "tout à fait vrai" = efficient.
                // (Nota -  Pour l'item 2, le principe est inversé : "pas du tout vrai" = efficient ; "plutôt pas vrai" = peu dégradé;
                if ($i == 2) {
                    if ($quizUserResponse->value == 'TAFV') $nombreReponsesChapitreFortDegrade = $nombreReponsesChapitreFortDegrade + 1;
                    elseif ($quizUserResponse->value == 'PV') $nombreReponsesChapitreDegrade = $nombreReponsesChapitreDegrade + 1;
                    elseif ($quizUserResponse->value == 'PPV') $nombreReponsesChapitrePeuDegrade = $nombreReponsesChapitrePeuDegrade + 1;
                    elseif ($quizUserResponse->value == 'PDTV') $nombreReponsesChapitreEfficient = $nombreReponsesChapitreEfficient + 1;
                } else {
                    if ($quizUserResponse->value == 'TAFV') $nombreReponsesChapitreEfficient = $nombreReponsesChapitreEfficient + 1;
                    elseif ($quizUserResponse->value == 'PV') $nombreReponsesChapitrePeuDegrade = $nombreReponsesChapitrePeuDegrade + 1;
                    elseif ($quizUserResponse->value == 'PPV') $nombreReponsesChapitreDegrade = $nombreReponsesChapitreDegrade + 1;
                    elseif ($quizUserResponse->value == 'PDTV') $nombreReponsesChapitreFortDegrade = $nombreReponsesChapitreFortDegrade + 1;
                }

                if ($i == 2) {
                    if ($quizUserResponse->value == 'TAFV') $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbFortDegrade'] = $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbFortDegrade'] + 1;
                    elseif ($quizUserResponse->value == 'PV') $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbDegrade'] = $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbDegrade'] + 1;
                    elseif ($quizUserResponse->value == 'PPV')  $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbPeuDegrade'] = $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbPeuDegrade'] + 1;
                    elseif ($quizUserResponse->value == 'PDTV') $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbEfficient'] =  $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbEfficient'] + 1;
                } else {
                    if ($quizUserResponse->value == 'TAFV') $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbEfficient'] =  $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbEfficient'] + 1;
                    elseif ($quizUserResponse->value == 'PV') $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbPeuDegrade'] = $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbPeuDegrade'] + 1;
                    elseif ($quizUserResponse->value == 'PPV') $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbDegrade'] = $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbDegrade'] + 1;
                    elseif ($quizUserResponse->value == 'PDTV') $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbFortDegrade'] = $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbFortDegrade'] + 1;
                }
                $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbTotal'] =  $resultByChapterQuestion[$i][$quizUserResponse->questionId]['nbTotal'] + 1;

            }

            //Calcul des statistique par chapitre
            $totalReponsesChapitre = $nombreReponsesChapitreEfficient + $nombreReponsesChapitrePeuDegrade + $nombreReponsesChapitreDegrade + $nombreReponsesChapitreFortDegrade;
            $percentReponsesChapitreEfficient = number_format($nombreReponsesChapitreEfficient * 100 / $totalReponsesChapitre, 0, ',', '');
            $percentReponsesChapitrePeuDegrade = number_format($nombreReponsesChapitrePeuDegrade * 100 / $totalReponsesChapitre, 0, ',', '');
            $percentReponsesChapitreDegrade = number_format($nombreReponsesChapitreDegrade * 100 / $totalReponsesChapitre, 0, ',', '');
            $percentReponsesChapitreFortDegrade = number_format($nombreReponsesChapitreFortDegrade * 100 / $totalReponsesChapitre, 0, ',', '');
            $resultByChapter[$i]['nbTotal'] = $totalReponsesChapitre;
            $resultByChapter[$i]['nbEfficient'] = $nombreReponsesChapitreEfficient;
            $resultByChapter[$i]['nbPeuDegrade'] = $nombreReponsesChapitrePeuDegrade;
            $resultByChapter[$i]['nbDegrade'] = $nombreReponsesChapitreDegrade;
            $resultByChapter[$i]['nbFortDegrade'] = $nombreReponsesChapitreFortDegrade;
            $resultByChapter[$i]['percentEfficient'] = $percentReponsesChapitreEfficient;
            $resultByChapter[$i]['percentPeuDegrade'] = $percentReponsesChapitrePeuDegrade;
            $resultByChapter[$i]['percentDegrade'] = $percentReponsesChapitreDegrade;
            $resultByChapter[$i]['percentFortDegrade'] = $percentReponsesChapitreFortDegrade;

            //////////////////////////////////
            //CALCUL pour l'ETAT DES FACTEURS
            //CALCUL pour le TAUX D'EXPOSITION
            //////////////////////////////////
            /// //On boucle sur les resultats
            $quizUserId = 0;
            $value = 0;
            $valueTotalQuestionsByUser = 0;
            $valueNegativeQuestionsByUser = 0;
            $nombreDeFortsRisques = 0;
            $nombreDesRisques = 0;
            $nombrePeuDeRisque = 0;
            $nombreSansRisque = 0;
            $lign = 1;
            foreach ($quizUserResponsesByChapter[$i] as $quizUserResponse) {

                //Si on traite un nouveau user
                //On remet les compteur à zéro
                if ($lign != 1 && $quizUserResponse->quizUserId != $quizUserId) {

                    //On stock la valeur pour chaque user
                    $resultByUserByChapter[$quizUserId][$i] = $valueNegativeQuestionsByUser;

                    //On stocke le resultat
                    //En fonction de la valeur on somme dans la bonne variable de risque
                    if ($valueTotalQuestionsByUser >= $quiz->risqueDeFr) $nombreDeFortsRisques = $nombreDeFortsRisques + 1;
                    elseif ($valueTotalQuestionsByUser >= $quiz->risqueDeR && $valueTotalQuestionsByUser <= $quiz->risqueAR) $nombreDesRisques = $nombreDesRisques + 1;
                    elseif ($valueTotalQuestionsByUser >= $quiz->risqueDePdr && $valueTotalQuestionsByUser <= $quiz->risqueAPdr) $nombrePeuDeRisque = $nombrePeuDeRisque + 1;
                    elseif ($valueTotalQuestionsByUser <= $quiz->risqueASr) $nombreSansRisque = $nombreSansRisque + 1;

                    //On remet à 0 le total par user
                    $valueTotalQuestionsByUser = 0;

                    //On remet à 0 le nombre de reponse negative par user
                    $valueNegativeQuestionsByUser = 0;

                    //On calcul pour le user suivant

                    //On convertir la string en valeur selon le paramétrage
                    //on inverse la logique pour le chapitre
                    if ($i == 2) {
                        if ($quizUserResponse->value == 'TAFV') $value = $quiz->coefPdtv;
                        elseif ($quizUserResponse->value == 'PV') $value = $quiz->coefPpv;
                        elseif ($quizUserResponse->value == 'PPV') $value = $quiz->coefPv;
                        elseif ($quizUserResponse->value == 'PDTV') $value = $quiz->coefTafv;

                    } else {
                        if ($quizUserResponse->value == 'TAFV') $value = $quiz->coefTafv;
                        elseif ($quizUserResponse->value == 'PV') $value = $quiz->coefPv;
                        elseif ($quizUserResponse->value == 'PPV') $value = $quiz->coefPpv;
                        elseif ($quizUserResponse->value == 'PDTV') $value = $quiz->coefPdtv;
                    }

                    $valueTotalQuestionsByUser = $valueTotalQuestionsByUser + $value;

                    //Pour le taux d'expostion on compte le nombre de reponse négative par user
                    //Negative = reponse 2 ou 3
                    if ($value == $quiz->coefPdtv || $value == $quiz->coefPpv) $valueNegativeQuestionsByUser = $valueNegativeQuestionsByUser + 1;

                } else {
                    //On convertir la string en valeur selon le paramétrage
                    //on inverse la logique pour le chapitre
                    if ($i == 2) {
                        if ($quizUserResponse->value == 'TAFV') $value = $quiz->coefPdtv;
                        elseif ($quizUserResponse->value == 'PV') $value = $quiz->coefPpv;
                        elseif ($quizUserResponse->value == 'PPV') $value = $quiz->coefPv;
                        elseif ($quizUserResponse->value == 'PDTV') $value = $quiz->coefTafv;
                    } else {
                        if ($quizUserResponse->value == 'TAFV') $value = $quiz->coefTafv;
                        elseif ($quizUserResponse->value == 'PV') $value = $quiz->coefPv;
                        elseif ($quizUserResponse->value == 'PPV') $value = $quiz->coefPpv;
                        elseif ($quizUserResponse->value == 'PDTV') $value = $quiz->coefPdtv;
                    }

                    $valueTotalQuestionsByUser = $valueTotalQuestionsByUser + $value;

                    //Pour le taux d'expostion on compte le nombre de reponse négative par user
                    //Negative = reponse 2 ou 3
                    if ($value == $quiz->coefPdtv || $value == $quiz->coefPpv) $valueNegativeQuestionsByUser = $valueNegativeQuestionsByUser + 1;

                }

                $quizUserId = $quizUserResponse->quizUserId;
                $lign = $lign + 1;
            }

            //On stocke le resultat du dernier user
            if ($valueTotalQuestionsByUser >= $quiz->risqueDeFr) $nombreDeFortsRisques = $nombreDeFortsRisques + 1;
            elseif ($valueTotalQuestionsByUser >= $quiz->risqueDeR && $valueTotalQuestionsByUser <= $quiz->risqueAR) $nombreDesRisques = $nombreDesRisques + 1;
            elseif ($valueTotalQuestionsByUser >= $quiz->risqueDePdr && $valueTotalQuestionsByUser <= $quiz->risqueAPdr) $nombrePeuDeRisque = $nombrePeuDeRisque + 1;
            elseif ($valueTotalQuestionsByUser <= $quiz->risqueASr) $nombreSansRisque = $nombreSansRisque + 1;

            //pour le taux d'exposition
            $resultByUserByChapter[$quizUserId][$i] = $valueNegativeQuestionsByUser;

            //On stock les resultats dans le tableau recap par chapitre
            $resultByChapter[$i]['nbDeFortsRisques'] = $nombreDeFortsRisques;
            $resultByChapter[$i]['nbDesRisques'] = $nombreDesRisques;
            $resultByChapter[$i]['nbPeuDeRisque'] = $nombrePeuDeRisque;
            $resultByChapter[$i]['nbSansRisque'] = $nombreSansRisque;

            $nombreDetousLesRisquesCalculated = $nombreDeFortsRisques + $nombreDesRisques + $nombrePeuDeRisque + $nombreSansRisque;
            $resultByChapter[$i]['percentDeFortsRisques'] = number_format($nombreDeFortsRisques * 100 / $nombreDetousLesRisquesCalculated, 0, ',', '');
            $resultByChapter[$i]['percentDesRisques'] = number_format($nombreDesRisques * 100 / $nombreDetousLesRisquesCalculated, 0, ',', '');
            $resultByChapter[$i]['percentPeuDeRisque'] = number_format($nombrePeuDeRisque * 100 / $nombreDetousLesRisquesCalculated, 0, ',', '');
            $resultByChapter[$i]['percentSansRisque'] = number_format($nombreSansRisque * 100 / $nombreDetousLesRisquesCalculated, 0, ',', '');

            $i = $i + 1;
        }

        //Calcul des statistique par chapitre/question
        foreach($resultByChapterQuestion as $keyChapitre => $value) {
            //var_dump($keyChapitre . "<br>");
            if (is_array($value)) {
                foreach ($value as $keyQuestion => $value) {
                    /*var_dump($keyQuestion . " : " . $value['nbEfficient'] . "<br>");
                    var_dump($keyQuestion . " : " . $value['nbPeuDegrade'] . "<br>");
                    var_dump($keyQuestion . " : " . $value['nbDegrade'] . "<br>");
                    var_dump($keyQuestion . " : " . $value['nbFortDegrade'] . "<br>");*/
                    $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient'] = number_format($value['nbEfficient'] * 100 / $value['nbTotal'], 0, ',', '');
                    $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade'] = number_format($value['nbPeuDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
                    $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade'] = number_format($value['nbDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
                    $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade'] = number_format($value['nbFortDegrade'] * 100 / $value['nbTotal'], 0, ',', '');
                    /*var_dump($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient'] . "<br>");
                    var_dump($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade'] . "<br>");
                    var_dump($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade'] . "<br>");
                    var_dump($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade'] . "<br>");*/
                }
            }
        }

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::FR_FR));

        $phpWord->addParagraphStyle('StyleParagrapheSautDeLigne', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheText1FirstPage', ['align' => 'left', 'spaceBefore' => 50, 'spaceAfter' => 5000, 'spacing' => 0]);
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
        $phpWord->addParagraphStyle('StyleParagrapheCenter', ['align' => 'center']);
        $phpWord->addParagraphStyle('StyleParagrapheTextCenterSpaceBefore40After40', ['align' => 'center', 'spaceBefore' => 40, 'spaceAfter' => 40, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheTextCenterSpaceBefore120After120', ['align' => 'center', 'spaceBefore' => 120, 'spaceAfter' => 120, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheTextCenterSpaceBefore', ['align' => 'center', 'spaceBefore' => 500, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheList', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceBefore' => 100, 'spaceAfter' => 10]);
        $phpWord->addParagraphStyle('StyleParagrapheTabRecapNumber', ['name' => 'Trebuchet MS', 'align' => 'right', 'spaceBefore' => 10, 'spaceAfter' => 10]);
        $phpWord->addParagraphStyle('StyleParagrapheTabAnalyseNumber', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceBefore' => 10, 'spaceAfter' => 10]);
        $phpWord->addFontStyle('StyleTexte5White', ['name' => 'Trebuchet MS', 'size' => 5, 'color' => "FFFFFF", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte8', ['name' => 'Trebuchet MS', 'size' => 8, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte9', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte9Orange', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => "ff8000"]);
        $phpWord->addFontStyle('StyleTexteChapter', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => 'E9660B', 'bold' => true, 'space' => array('before' => 10)]);
        $phpWord->addFontStyle('StyleTexte10', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte10Coche', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte11', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte11Colle', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'space' => array('before' => 5, 'after' => 5)]);
        $phpWord->addFontStyle('StyleTexte11Rouge', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B']);
        $phpWord->addFontStyle('StyleTexte11Blanc', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "FFFFFF"]);
        $phpWord->addFontStyle('StyleTexte11RougeMarginLeft', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B', 'indentation' => array('left' => 400, 'right' => 0)]);
        $phpWord->addFontStyle('StyleTexte11Colle10Before', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "000000", 'space' => array('before' => 10)]);
        $phpWord->addFontStyle('StyleTexte11Bold', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte11BoldUnderline', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'bold' => true, 'underline' => 'single']);
        $phpWord->addFontStyle('StyleTexte12', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte12RougeBold', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte12BoldUnderline', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => "696252", 'bold' => true, 'underline' => 'single']);
        $phpWord->addFontStyle('StyleTexte14Rouge', ['name' => 'Trebuchet MS', 'size' => 14, 'color' => 'E9660B']);
        $phpWord->addFontStyle('StyleTexte14RougeBold', ['name' => 'Trebuchet MS', 'size' => 14, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte16Vert', ['name' => 'Trebuchet MS', 'size' => 16, 'color' => "92D050"]);
        $phpWord->addFontStyle('StyleTexte16Orange', ['name' => 'Trebuchet MS', 'size' => 16, 'color' => "F97407"]);
        $phpWord->addFontStyle('StyleTexte16Rouge', ['name' => 'Trebuchet MS', 'size' => 16, 'color' => "FF0000"]);
        $phpWord->addFontStyle('StyleTexte18RougeBold', ['name' => 'Trebuchet MS', 'size' => 18, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte28RougeBold', ['name' => 'Trebuchet MS', 'size' => 28, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('list1', array('name' => 'Trebuchet MS', 'size' => 11, 'color' => '696252'));
        $phpWord->addTableStyle('StyleTableFirstPage', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableFooter', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableFirstPage', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableRecap', ['borderSize' => 0, 'borderColor' => '000000', 'cellMarginLeft' => 150, 'cellMarginRight' => 150, 'cellMarginTop' => 100, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableBarre', ['borderSize' => 0, 'borderColor' => 'ffffff', 'cellMarginLeft' => 0, 'cellMarginRight' => 0, 'cellMarginTop' => 0, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableBarreGraph', ['borderSize' => 1, 'borderColor' => '000000', 'cellMarginLeft' => 50, 'cellMarginRight' => 50, 'cellMarginTop' => 50, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableCroisementBrute', ['borderSize' => 1, 'borderColor' => '000000', 'cellMarginLeft' => 0, 'cellMarginRight' => 0, 'cellMarginTop' => 0, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
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

        //si on met des  marges sur la section se sont les marges de la page
        $section = $phpWord->addSection(array('marginLeft' => 1200, 'marginRight' => 1000, 'marginTop' => 800, 'marginBottom' => 800));

        //Tableau du header avec le logo uniquement sur la 1ere page
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
        $textrun->addText("Baromètre", 'StyleTexte28RougeBold', 'StyleParagrapheText1FirstPage');
        $textrun = $cell->addTextRun();
        $textrun->addText("", 'StyleTexte14Rouge', 'StyleParagrapheLeftColle');
        $textrun = $cell->addTextRun();
        $textrun->addText("Performance et bien vivre son travail", 'StyleTexte14Rouge', 'StyleParagrapheLeftColle');

        $this->addSautLigne($section, 12);

        //On affiche le logo du client si existant
        if ($quiz->logo) {
            $table = $section->addTable('StyleTableFirstPage');
            $table->addRow();
            $cell = $table->addCell(4000);
            $cell->addImage(BASE_PATH . "assets/images/logosClients/" . $quiz->logo, array('height' => 140, 'align' => 'left'));
            $cell = $table->addCell(6000);
        } else {
            $this->addSautLigne($section, 8);
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
        $section->addText('Novembre 2019', 'StyleTexte12', array('align' => 'right'));


        $footer = $section->addFooter();
        $footer->firstPage();
        $table = $footer->addTable();
        $table->addRow();
        /*
        $cell = $table->addCell(9000);
        $textrun = $cell->addTextRun();
        $textrun->addText('RM Conseil et Interventions','StyleTexte11Colle10Before','StyleParagrapheFooterTexte');
        $textrun->addImage(BASE_PATH . '/assets/images/carre-orange.png', array('width'  => 10, 'height' => 10, 'align'  => 'center'));
        */

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

        $footer_sub = $section->addFooter();
        $footer_sub->addPreserveText('{PAGE} / {NUMPAGES}','StyleTexte9','StyleParagraphePageNumberFooter');


        $section->addPageBreak();

        //-----------------------------------------------------------------------------------------

        $section = $phpWord->addSection();
        $section->addTitle('ETAT DES FACTEURS - Nombre de fois où un item a reçu des réponses', 1);
        //$section = $phpWord->addSection(array('colsNum' => 2, 'breakType' => 'continuous'));

        $arrayColor = ['92D050', 'FFFF00', 'FF9803', 'FF0000'];

        $arrayValueEfficient = [];
        $arrayValuePeuDegrade = [];
        $arrayValueDegrade= [];
        $arrayValueFortDegrade = [];
        $chapterNumber = 1;
        foreach ($ChaptersInfo as $Chapter) {
            $arrayValueEfficient[] = $resultByChapter[$chapterNumber]['nbEfficient'];
            $arrayValuePeuDegrade[] = $resultByChapter[$chapterNumber]['nbPeuDegrade'];
            $arrayValueDegrade[] = $resultByChapter[$chapterNumber]['nbDegrade'];
            $arrayValueFortDegrade[] = $resultByChapter[$chapterNumber]['nbFortDegrade'];
            $chapterNumber = $chapterNumber + 1;
        }

        $chart = $section->addChart('stacked_column', $categoriesLabelForGraph, $arrayValueEfficient);
        $chart->addSeries($categoriesLabelForGraph, $arrayValuePeuDegrade);
        $chart->addSeries($categoriesLabelForGraph, $arrayValueDegrade);
        $chart->addSeries($categoriesLabelForGraph, $arrayValueFortDegrade);

        $chart->getStyle()->setTitle("BARO-GRAPH-ETAT");
        $chart->getStyle()->setWidth(Converter::inchToEmu(6));
        $chart->getStyle()->setHeight(Converter::inchToEmu(3));
        $chart->getStyle()->setColors($arrayColor);
        $chart->getStyle()->setShowGridX(false);
        $chart->getStyle()->setShowGridY(true);
        $chart->getStyle()->setShowAxisLabels(true);
        $chart->getStyle()->setShowLegend(true);

        //Ajout du graph en nombre
        $chart->getStyle()->setDataLabelOptions(['showVal' => true,
            'showLegendKey' => false, //show the cart legend
            'showSerName' => false, // series name
            'showPercent' => false,
            'showLeaderLines' => false,
            'showBubbleSize' => false,
            'showCatName' => false,]);

        $this->addSautLigne($section, 2);

        $arrayPercentEfficient = [];
        $arrayPercentPeuDegrade = [];
        $arrayPercentDegrade= [];
        $arrayPercentFortDegrade = [];
        $chapterNumber = 1;
        foreach ($ChaptersInfo as $Chapter) {
            $arrayPercentEfficient[] = $resultByChapter[$chapterNumber]['percentEfficient'];
            $arrayPercentPeuDegrade[] = $resultByChapter[$chapterNumber]['percentPeuDegrade'];
            $arrayPercentDegrade[] = $resultByChapter[$chapterNumber]['percentDegrade'];
            $arrayPercentFortDegrade[] = $resultByChapter[$chapterNumber]['percentFortDegrade'];
            $chapterNumber = $chapterNumber + 1;
        }

        $chart = $section->addChart('percent_stacked_column', $categoriesLabelForGraph, $arrayPercentEfficient);
        $chart->addSeries($categoriesLabelForGraph, $arrayPercentPeuDegrade);
        $chart->addSeries($categoriesLabelForGraph, $arrayPercentDegrade);
        $chart->addSeries($categoriesLabelForGraph, $arrayPercentFortDegrade);

        $chart->getStyle()->setTitle("BARO-GRAPH-ETAT");
        $chart->getStyle()->setWidth(Converter::inchToEmu(6));
        $chart->getStyle()->setHeight(Converter::inchToEmu(3));
        $chart->getStyle()->setColors($arrayColor);
        $chart->getStyle()->setShowGridX(false);
        $chart->getStyle()->setShowGridY(true);
        $chart->getStyle()->setShowAxisLabels(true);
        $chart->getStyle()->setShowLegend(true);

        //Ajout du graph en pourcentage
        $chart->getStyle()->setDataLabelOptions(['showVal' => true,
            'showLegendKey' => false, //show the cart legend
            'showSerName' => false, // series name
            'showPercent' => false,
            'showLeaderLines' => false,
            'showBubbleSize' => false,
            'showCatName' => false,]);

        $this->addSautLigne($section, 2);

        //-----------------------------------------------------------------------------------------

        $section = $phpWord->addSection();
        $section->addTitle('ETAT DES IMPACTS - Nombre de personnes, par item, confrontées via les coef', 1);
        //$section = $phpWord->addSection(array('colsNum' => 2, 'breakType' => 'continuous'));

        $arrayColor = ['92D050', 'FFFF00', 'FF9803', 'FF0000'];

        $arrayDeFortsRisques = [];
        $arrayDesRisques = [];
        $arrayPeuDeRisque= [];
        $arraySansRisque = [];
        $chapterNumber = 1;
        foreach ($ChaptersInfo as $Chapter) {
            $arrayDeFortsRisques[] = $resultByChapter[$chapterNumber]['nbDeFortsRisques'];
            $arrayDesRisques[] = $resultByChapter[$chapterNumber]['nbDesRisques'];
            $arrayPeuDeRisque[] = $resultByChapter[$chapterNumber]['nbPeuDeRisque'];
            $arraySansRisque[] = $resultByChapter[$chapterNumber]['nbSansRisque'];
            $chapterNumber = $chapterNumber + 1;
        }

        $chart = $section->addChart('stacked_column', $categoriesLabelForGraph, $arrayDeFortsRisques);
        $chart->addSeries($categoriesLabelForGraph, $arrayDesRisques);
        $chart->addSeries($categoriesLabelForGraph, $arrayPeuDeRisque);
        $chart->addSeries($categoriesLabelForGraph, $arraySansRisque);

        $chart->getStyle()->setTitle("BARO-GRAPH-IMPACT");
        $chart->getStyle()->setWidth(Converter::inchToEmu(6));
        $chart->getStyle()->setHeight(Converter::inchToEmu(3));
        $chart->getStyle()->setColors($arrayColor);
        $chart->getStyle()->setShowGridX(false);
        $chart->getStyle()->setShowGridY(true);
        $chart->getStyle()->setShowAxisLabels(true);
        $chart->getStyle()->setShowLegend(true);

        //Ajout du graphique en nombre
        $chart->getStyle()->setDataLabelOptions(['showVal' => true,
            'showLegendKey' => false, //show the cart legend
            'showSerName' => false, // series name
            'showPercent' => false,
            'showLeaderLines' => false,
            'showBubbleSize' => false,
            'showCatName' => false,]);

        $arrayPercentDeFortsRisques = [];
        $arrayPercentDesRisques = [];
        $arrayPercentPeuDeRisque= [];
        $arrayPercentSansRisque = [];
        $chapterNumber = 1;
        foreach ($ChaptersInfo as $Chapter) {
            $arrayPercentDeFortsRisques[] = $resultByChapter[$chapterNumber]['percentDeFortsRisques'];
            $arrayPercentDesRisques[] = $resultByChapter[$chapterNumber]['percentDesRisques'];
            $arrayPercentPeuDeRisque[] = $resultByChapter[$chapterNumber]['percentPeuDeRisque'];
            $arrayPercentSansRisque[] = $resultByChapter[$chapterNumber]['percentSansRisque'];
            $chapterNumber = $chapterNumber + 1;
        }

        $chart = $section->addChart('percent_stacked_column', $categoriesLabelForGraph, $arrayPercentDeFortsRisques);
        $chart->addSeries($categoriesLabelForGraph, $arrayPercentDesRisques);
        $chart->addSeries($categoriesLabelForGraph, $arrayPercentPeuDeRisque);
        $chart->addSeries($categoriesLabelForGraph, $arrayPercentSansRisque);

        $chart->getStyle()->setTitle("BARO-GRAPH-IMPACT");
        $chart->getStyle()->setWidth(Converter::inchToEmu(6));
        $chart->getStyle()->setHeight(Converter::inchToEmu(3));
        $chart->getStyle()->setColors($arrayColor);
        $chart->getStyle()->setShowGridX(false);
        $chart->getStyle()->setShowGridY(true);
        $chart->getStyle()->setShowAxisLabels(true);
        $chart->getStyle()->setShowLegend(true);

        //Ajout du graphique en pourcntage
        $chart->getStyle()->setDataLabelOptions(['showVal' => true,
            'showLegendKey' => false, //show the cart legend
            'showSerName' => false, // series name
            'showPercent' => false,
            'showLeaderLines' => false,
            'showBubbleSize' => false,
            'showCatName' => false,]);

        $this->addSautLigne($section, 2);

        //-----------------------------------------------------------------------------------------

        $section = $phpWord->addSection();
        $section->addTitle('TAUX D\'EXPOSITION - Nombre de réponses négatives, tous thèmes confondus, par personne', 1);
        //$section = $phpWord->addSection(array('colsNum' => 2, 'breakType' => 'continuous'));

        $arrayColor = ['92D050', 'FFFF00', 'FF9803', 'FF0000'];

        //Pour le taux d'exposition
        //Pour chaque user On somme le nombre de reponse negative pour les chapitres
        $chaptersum = 0;
        $resultByUser = [];
        foreach($resultByUserByChapter as $key=>$arrayChapter) {
            $keyUser = $key;
            foreach($arrayChapter as $key=>$value) {
                $chaptersum = $chaptersum + $value;
            }
            $resultByUser[$keyUser] = $chaptersum;
            $chaptersum = 0;
        }

        $nombreTauxExpositionDeFortsRisques = 0;
        $nombreTauxExpositionDesRisques = 0;
        $nombreTauxExpositionPeuDeRisque = 0;
        $nombreTauxExpositionSansRisque = 0;
        //Pour chaque user on regarde la valeur du nb de réponse negative par rapport aux tranches de coef et on somme dans la bonne variable de risque
        foreach($resultByUser as $key=>$value) {
            if ($value >= $quiz->tauxDeFr) $nombreTauxExpositionDeFortsRisques = $nombreTauxExpositionDeFortsRisques + 1;
            elseif ($value >= $quiz->tauxDeR && $value <= $quiz->tauxAR) $nombreTauxExpositionDesRisques = $nombreTauxExpositionDesRisques + 1;
            elseif ($value >= $quiz->tauxDePdr && $value <= $quiz->tauxAPdr) $nombreTauxExpositionPeuDeRisque = $nombreTauxExpositionPeuDeRisque + 1;
            elseif ($value <= $quiz->tauxASr) $nombreTauxExpositionSansRisque = $nombreTauxExpositionSansRisque + 1;
        }

        $arrayNbTauxExpositionDeFortsRisques = [];
        $arrayNbTauxExpositionDesRisques = [];
        $arrayNbTauxExpositionPeuDeRisque = [];
        $arrayNbTauxExpositionSansRisque = [];
        $arrayNbTauxExpositionDeFortsRisques[] = $nombreTauxExpositionDeFortsRisques;
        $arrayNbTauxExpositionDesRisques[] = $nombreTauxExpositionDesRisques;
        $arrayNbTauxExpositionPeuDeRisque[] = $nombreTauxExpositionPeuDeRisque;
        $arrayNbTauxExpositionSansRisque[] = $nombreTauxExpositionSansRisque;

        $arrayPercentTauxExpositionDeFortsRisques = [];
        $arrayPercentTauxExpositionDesRisques = [];
        $arrayPercentTauxExpositionPeuDeRisque= [];
        $arrayPercentTauxExpositionSansRisque = [];
        $nombreTauxExpositionDetousLesRisquesCalculated = $nombreTauxExpositionDeFortsRisques + $nombreTauxExpositionDesRisques + $nombreTauxExpositionPeuDeRisque + $nombreTauxExpositionSansRisque;
        $arrayPercentTauxExpositionDeFortsRisques[] = number_format($nombreTauxExpositionDeFortsRisques * 100 / $nombreTauxExpositionDetousLesRisquesCalculated, 0, ',', '');
        $arrayPercentTauxExpositionDesRisques[] = number_format($nombreTauxExpositionDesRisques * 100 / $nombreTauxExpositionDetousLesRisquesCalculated, 0, ',', '');
        $arrayPercentTauxExpositionPeuDeRisque[] = number_format($nombreTauxExpositionPeuDeRisque * 100 / $nombreTauxExpositionDetousLesRisquesCalculated, 0, ',', '');
        $arrayPercentTauxExpositionSansRisque[] = number_format($nombreTauxExpositionSansRisque * 100 / $nombreTauxExpositionDetousLesRisquesCalculated, 0, ',', '');

        $categoriesForGraphTauxExposition = [];
        $categoriesForGraphTauxExposition[] = '';
        $chart = $section->addChart('stacked_column', $categoriesForGraphTauxExposition, $arrayNbTauxExpositionDeFortsRisques);
        $chart->addSeries($categoriesForGraphTauxExposition, $arrayNbTauxExpositionDesRisques);
        $chart->addSeries($categoriesForGraphTauxExposition, $arrayNbTauxExpositionPeuDeRisque);
        $chart->addSeries($categoriesForGraphTauxExposition, $arrayNbTauxExpositionSansRisque);

        $chart->getStyle()->setTitle("BARO-GRAPH-EXPOSITION");
        $chart->getStyle()->setWidth(Converter::inchToEmu(6));
        $chart->getStyle()->setHeight(Converter::inchToEmu(3));
        $chart->getStyle()->setColors($arrayColor);
        $chart->getStyle()->setShowGridX(false);
        $chart->getStyle()->setShowGridY(true);
        $chart->getStyle()->setShowAxisLabels(true);
        $chart->getStyle()->setShowLegend(true);

        $chart->getStyle()->setDataLabelOptions(['showVal' => true,
            'showLegendKey' => false, //show the cart legend
            'showSerName' => false, // series name
            'showPercent' => false,
            'showLeaderLines' => false,
            'showBubbleSize' => false,
            'showCatName' => false,]);

        $categoriesForGraphTauxExposition[] = '';
        $chart = $section->addChart('percent_stacked_column', $categoriesForGraphTauxExposition, $arrayPercentTauxExpositionDeFortsRisques);
        $chart->addSeries($categoriesForGraphTauxExposition, $arrayPercentTauxExpositionDesRisques);
        $chart->addSeries($categoriesForGraphTauxExposition, $arrayPercentTauxExpositionPeuDeRisque);
        $chart->addSeries($categoriesForGraphTauxExposition, $arrayPercentTauxExpositionSansRisque);

        $chart->getStyle()->setTitle("BARO-GRAPH-EXPOSITION");
        $chart->getStyle()->setWidth(Converter::inchToEmu(6));
        $chart->getStyle()->setHeight(Converter::inchToEmu(3));
        $chart->getStyle()->setColors($arrayColor);
        $chart->getStyle()->setShowGridX(false);
        $chart->getStyle()->setShowGridY(true);
        $chart->getStyle()->setShowAxisLabels(true);
        $chart->getStyle()->setShowLegend(true);

        $chart->getStyle()->setDataLabelOptions(['showVal' => true,
            'showLegendKey' => false, //show the cart legend
            'showSerName' => false, // series name
            'showPercent' => false,
            'showLeaderLines' => false,
            'showBubbleSize' => false,
            'showCatName' => false,]);

        $this->addSautLigne($section, 2);

        //-----------------------------------------------------------------------------------------

        //$section = $phpWord->addSection();
        $section = $phpWord->addSection(array('marginLeft' => 600, 'marginRight' => 600));
        //$section = $phpWord->addSection(array('marginLeft' => 200, 'marginRight' => 200, 'marginTop' => 400, 'marginBottom' => 400));
        //$section = $phpWord->addSection(array('colsNum' => 2, 'breakType' => 'continuous'));

        $section->addTitle('BARRES - Par Chapitre et pour chaque question - Nombre de fois où un item a reçu des réponses', 1);



        foreach($resultByChapterQuestion as $keyChapitre => $value) {

            //On affiche le titre du chapitre
            $tableTitre = $section->addTable('StyleTableBarre');
            $tableTitre->addRow();
            $labelChapitre = $ChaptersInfo[$keyChapitre]['label'];
            $tableTitre->addCell(12000)->addText($this->formatChapterForWord($labelChapitre), 'StyleTexte12BoldUnderline', 'StyleParagrapheTabAnalyseNumber');
            //\PhpOffice\PhpWord\Shared\Html::addHtml($cell, $labelChapitre);

            $this->addSautLigne($section, 1);

            if (is_array($value)) {

                $questionIndex = 1;
                foreach ($value as $keyQuestion => $value) {

                    //Recuperation du label de la question
                    $question = $quizQuestions[$keyQuestion];

                    if($questionIndex == 1 || $questionIndex == 3 || $questionIndex == 5 || $questionIndex == 7 || $questionIndex == 9) {
                        $table = $section->addTable('StyleTableBarre');
                        $table->addRow();
                        $cellCol1 = $table->addCell(6500);
                    }

                    if($questionIndex == 1 || $questionIndex == 3 || $questionIndex == 5 || $questionIndex == 7 || $questionIndex == 9) {

                        $tableCol1 = $cellCol1->addTable('StyleTableBarre');
                        $tableCol1->addRow(null, array('cantSplit' => true));
                        $cell = $tableCol1->addCell(500);
                        $cell->addText("Q".$questionIndex, 'StyleTexte11Blanc', 'StyleParagrapheTextCenterSpaceBefore');
                        //Question sur fond vert ou rouge en fonction du pourcentage
                        $cell->getStyle()->setBgColor('00B050');
                        //$cell->getStyle()->setBgColor('C00000');

                        $cellCol12 = $tableCol1->addCell(6000);
                        $tableCol11 = $cellCol12->addTable('StyleTableBarre');
                        $tableCol11->addRow();
                        $tableCol11->addCell(6000)->addText($this->formatQuestionForWord($question->label), 'StyleTexte9', 'StyleParagrapheCenter');
                        $tableCol11->addRow();
                        $cellGraph = $tableCol11->addCell(6000);
                        $tableGraph = $cellGraph->addTable('StyleTableBarreGraph');
                        $tableGraph->addRow();
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText("Efficient", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cellGraph->getStyle()->setBgColor('92D050');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText("Peu dégradé", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cellGraph->getStyle()->setBgColor('FFFF00');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText("Dégradé", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cellGraph->getStyle()->setBgColor('ED7D31');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText("Fort Dégradé", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cellGraph->getStyle()->setBgColor('FF0000');

                        $tableGraph->addRow();
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbEfficient'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbPeuDegrade'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbDegrade'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbFortDegrade'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $tableGraph->addRow();
                        $cellGraph = $tableGraph->addCell(1500);

                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade']."%", 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade']."%", 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade']."%", 'StyleTexte9', 'StyleParagrapheCenter');

                        $tableCol1 = $cellCol1->addTable('StyleTableBarre');
                        $tableCol1->addRow();
                        $cell = $tableCol1->addCell(6500);
                        $cell->getStyle()->setGridSpan(5);
                        $cell->addText(" ", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $tableCol1->addRow();
                        $tableCol1->addCell(500);
                        //Calcul de la longeur des 4 cellule du tableau par rapport au pourcentage
                        //  6000  -  100
                        //  1200  -  20 %
                        // formule 6000 * 20 /100 -> 1200
                        $pixelEfficient = number_format(6000 * $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient'] / 100, 0, ',', '');
                        $pixelPeuDegrade = number_format(6000 * $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade'] / 100, 0, ',', '');
                        $pixelDegrade = number_format(6000 * $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade'] / 100, 0, ',', '');
                        $pixelFortDegrade = number_format(6000 * $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade'] / 100, 0, ',', '');
                        $cell = $tableCol1->addCell($pixelEfficient);
                        $cell->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbEfficient'], 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cell->getStyle()->setBgColor('92D050');
                        $cell = $tableCol1->addCell($pixelPeuDegrade);
                        $cell->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbPeuDegrade'], 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cell->getStyle()->setBgColor('FFFF00');
                        $cell = $tableCol1->addCell($pixelDegrade);
                        $cell->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbDegrade'], 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cell->getStyle()->setBgColor('ED7D31');
                        $cell = $tableCol1->addCell($pixelFortDegrade);
                        $cell->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbFortDegrade'], 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cell->getStyle()->setBgColor('FF0000');
                        $tableCol1->addRow();
                        $cell = $tableCol1->addCell(6500);
                        $cell->getStyle()->setGridSpan(5);
                        $cell->addText(" ", 'StyleTexte12', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $tableCol1->addRow();
                        $cell = $tableCol1->addCell(6500);
                        $cell->getStyle()->setGridSpan(5);
                        $cell->addText(" ", 'StyleTexte12', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    }

                    if($questionIndex == 1 || $questionIndex == 3 || $questionIndex == 5 || $questionIndex == 7 || $questionIndex == 9) {

                        $cellCol1 = $tableCol1->addCell(500);
                        $cellCol1 = $table->addCell(6500);
                    }

                    if($questionIndex == 2 || $questionIndex == 4 || $questionIndex == 6 || $questionIndex == 8 || $questionIndex == 10) {

                        $tableCol1 = $cellCol1->addTable('StyleTableBarre');
                        $tableCol1->addRow(null, array('cantSplit' => true));
                        $cell = $tableCol1->addCell(500);
                        $cell->addText("Q".$questionIndex, 'StyleTexte11Blanc', 'StyleParagrapheTextCenterSpaceBefore');
                        //Question sur fond vert ou rouge en fonction du pourcentage
                        $cell->getStyle()->setBgColor('00B050');
                        //$cell->getStyle()->setBgColor('C00000');

                        $cellCol12 = $tableCol1->addCell(6000);
                        $tableCol11 = $cellCol12->addTable('StyleTableBarre');
                        $tableCol11->addRow();
                        $tableCol11->addCell(6000)->addText($this->formatQuestionForWord($quizQuestions[54]->label), 'StyleTexte9', 'StyleParagrapheCenter');
                        $tableCol11->addRow();
                        $cellGraph = $tableCol11->addCell(6000);
                        $tableGraph = $cellGraph->addTable('StyleTableBarreGraph');
                        $tableGraph->addRow();
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText("Efficient", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cellGraph->getStyle()->setBgColor('92D050');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText("Peu dégradé", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cellGraph->getStyle()->setBgColor('FFFF00');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText("Dégradé", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cellGraph->getStyle()->setBgColor('ED7D31');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText("Fort Dégradé", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cellGraph->getStyle()->setBgColor('FF0000');

                        $tableGraph->addRow();
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbEfficient'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbPeuDegrade'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbDegrade'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbFortDegrade'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $tableGraph->addRow();
                        $cellGraph = $tableGraph->addCell(1500);

                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient'], 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade']."%", 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade']."%", 'StyleTexte9', 'StyleParagrapheCenter');
                        $cellGraph = $tableGraph->addCell(1500);
                        $cellGraph->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade']."%", 'StyleTexte9', 'StyleParagrapheCenter');

                        $tableCol1 = $cellCol1->addTable('StyleTableBarre');
                        $tableCol1->addRow();
                        $cell = $tableCol1->addCell(6500);
                        $cell->getStyle()->setGridSpan(5);
                        $cell->addText(" ", 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $tableCol1->addRow();
                        $tableCol1->addCell(500);
                        //Calcul de la longeur des 4 cellule du tableau par rapport au pourcentage
                        //  6000  -  100
                        //  1200  -  20 %
                        // formule 6000 * 20 /100 -> 1200
                        $pixelEfficient = number_format(6000 * $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentEfficient'] / 100, 0, ',', '');
                        $pixelPeuDegrade = number_format(6000 * $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentPeuDegrade'] / 100, 0, ',', '');
                        $pixelDegrade = number_format(6000 * $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentDegrade'] / 100, 0, ',', '');
                        $pixelFortDegrade = number_format(6000 * $resultByChapterQuestion[$keyChapitre][$keyQuestion]['percentFortDegrade'] / 100, 0, ',', '');
                        $cell = $tableCol1->addCell($pixelEfficient);
                        $cell->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbEfficient'], 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cell->getStyle()->setBgColor('92D050');
                        $cell = $tableCol1->addCell($pixelPeuDegrade);
                        $cell->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbPeuDegrade'], 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cell->getStyle()->setBgColor('FFFF00');
                        $cell = $tableCol1->addCell($pixelDegrade);
                        $cell->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbDegrade'], 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cell->getStyle()->setBgColor('ED7D31');
                        $cell = $tableCol1->addCell($pixelFortDegrade);
                        $cell->addText($resultByChapterQuestion[$keyChapitre][$keyQuestion]['nbFortDegrade'], 'StyleTexte9', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $cell->getStyle()->setBgColor('FF0000');
                        $tableCol1->addRow();
                        $cell = $tableCol1->addCell(6500);
                        $cell->getStyle()->setGridSpan(5);
                        $cell->addText(" ", 'StyleTexte12', 'StyleParagrapheTextCenterSpaceBefore40After40');
                        $tableCol1->addRow();
                        $cell = $tableCol1->addCell(6500);
                        $cell->getStyle()->setGridSpan(5);
                        $cell->addText(" ", 'StyleTexte12', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    }

                    $questionIndex++;
                }
            }

            $section->addPageBreak();
        }


        //-----------------------------------------------------------------------------------------

        //$section = $phpWord->addSection();
        $section = $phpWord->addSection(array('marginLeft' => 600, 'marginRight' => 600));
        //$section = $phpWord->addSection(array('marginLeft' => 200, 'marginRight' => 200, 'marginTop' => 400, 'marginBottom' => 400));
        //$section = $phpWord->addSection(array('colsNum' => 2, 'breakType' => 'continuous'));

        $section->addTitle('CROISEMENT BRUTE - Par Chapitre et un tableau recap par critère - Nombre de fois où un item a reçu des réponses', 1);

        $this->addSautLigne($section, 2);

        $colorGray = "D9D9D9";
        $colorWhite = "FFFFFF";
        $colorCritere1 = "F8CBAD";
        $colorCritere2 = "FFE699";
        $colorCritere3 = "A9D08E";
        $colorCritere4 = "548235";
        $bgColor = "";

        foreach($tabChapterCritereChoix as $keyChapter => $tabCritere) {

            //On affiche le titre du chapitre
            $tableTitre = $section->addTable('StyleTableBarre');
            $tableTitre->addRow();
            $labelChapitre = $ChaptersInfo[$keyChapter]['label'];
            $tableTitre->addCell(12000)->addText($this->formatChapterForWord($labelChapitre), 'StyleTexte12BoldUnderline', 'StyleParagrapheTabAnalyseNumber');
            //\PhpOffice\PhpWord\Shared\Html::addHtml($cell, $labelChapitre);

            $this->addSautLigne($section, 2);

            $indexCritere = 1;
            foreach($tabCritere as $keyCritere => $tabChoix) {

                if($indexCritere == 1) $bgColor = $colorCritere1;
                elseif($indexCritere == 2) $bgColor = $colorCritere2;
                elseif($indexCritere == 3) $bgColor = $colorCritere3;
                elseif($indexCritere == 4) $bgColor = $colorCritere4;

                $table = $section->addTable('StyleTableCroisementBrute');
                $table->addRow(null, array('cantSplit' => true));
                $cell = $table->addCell(12000);
                $tableCritere = $cell->addTable('StyleTableCroisementBrute');
                $tableCritere->addRow();
                $cell = $tableCritere->addCell(12000);
                $cell->addText($keyCritere, 'StyleTexte12', 'StyleParagrapheTextCenterSpaceBefore120After120');
                $cell->getStyle()->setGridSpan(6);
                $cell->getStyle()->setBgColor($bgColor);
                $tableCritere->addRow();
                $cell = $tableCritere->addCell(3500)->addText("Etiquette", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell = $tableCritere->addCell(1500);
                $cell->addText("Efficient", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell->getStyle()->setBgColor('92D050');
                $cell = $tableCritere->addCell(1800);
                $cell->addText("Peu dégradé", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell->getStyle()->setBgColor('FFFF00');
                $cell = $tableCritere->addCell(1500);
                $cell->addText("Dégradé", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell->getStyle()->setBgColor('ED7D31');
                $cell = $tableCritere->addCell(1800);
                $cell->addText("Fort dégradé", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell->getStyle()->setBgColor('FF0000');
                $cell = $tableCritere->addCell(1900)->addText("Total général", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');

                $nbTotalEfficient = 0;
                $nbTotalPeuDegrade = 0;
                $nbTotalDegrade = 0;
                $nbTotalFortDegrade = 0;
                $nbChoix = 0;
                $indexLign = 1;
                foreach ($tabChoix as $keyChoix => $value) {

                    if ($indexLign%2 == 1) $bgColor = $colorGray;
                    else $bgColor = $colorWhite;

                    $tableCritere->addRow();
                    $cell = $tableCritere->addCell(3500);
                    $cell->addText($keyChoix, 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1500);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbEfficient'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1800);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbPeuDegrade'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1500);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbDegrade'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1800);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbFortDegrade'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1900);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbTotal'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);

                    $nbChoix = $nbChoix + 1;
                    $nbTotalEfficient = $nbTotalEfficient + $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbEfficient'];
                    $nbTotalPeuDegrade = $nbTotalPeuDegrade + $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbPeuDegrade'];
                    $nbTotalDegrade = $nbTotalDegrade + $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbDegrade'];
                    $nbTotalFortDegrade = $nbTotalFortDegrade + $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbFortDegrade'];

                    $indexLign++;
                }

                $tableCritere->addRow();
                $cell = $tableCritere->addCell(3500);
                $cell->addText("Total général", 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1500);
                $cell->addText($nbTotalEfficient, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1800);
                $cell->addText($nbTotalPeuDegrade, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1500);
                $cell->addText($nbTotalDegrade, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1800);
                $cell->addText($nbTotalFortDegrade, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1900);
                $cell->addText($nbTotalEfficient+$nbTotalPeuDegrade+$nbTotalDegrade+$nbTotalFortDegrade, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);

                $this->addSautLigne($section, 2);

                $indexCritere++;
            }

            $section->addPageBreak();
        }


        //-----------------------------------------------------------------------------------------

        //$section = $phpWord->addSection();
        $section = $phpWord->addSection(array('marginLeft' => 600, 'marginRight' => 600));
        //$section = $phpWord->addSection(array('marginLeft' => 200, 'marginRight' => 200, 'marginTop' => 400, 'marginBottom' => 400));
        //$section = $phpWord->addSection(array('colsNum' => 2, 'breakType' => 'continuous'));

        $section->addTitle('CROISEMENT COEF - Par Chapitre et un tableau recap par critère - Nombre de personnes, confrontées via les coef', 1);


        $this->addSautLigne($section, 2);

        $colorGray = "D9D9D9";
        $colorWhite = "FFFFFF";
        $colorCritere1 = "F8CBAD";
        $colorCritere2 = "FFE699";
        $colorCritere3 = "A9D08E";
        $colorCritere4 = "548235";
        $bgColor = "";

        foreach($tabChapterCritereChoix as $keyChapter => $tabCritere) {

            //On affiche le titre du chapitre
            $tableTitre = $section->addTable('StyleTableBarre');
            $tableTitre->addRow();
            $labelChapitre = $ChaptersInfo[$keyChapter]['label'];
            $tableTitre->addCell(12000)->addText($this->formatChapterForWord($labelChapitre), 'StyleTexte12BoldUnderline', 'StyleParagrapheTabAnalyseNumber');
            //\PhpOffice\PhpWord\Shared\Html::addHtml($cell, $labelChapitre);

            $this->addSautLigne($section, 2);

            $indexCritere = 1;
            foreach ($tabCritere as $keyCritere => $tabChoix) {

                if ($indexCritere == 1) $bgColor = $colorCritere1;
                elseif ($indexCritere == 2) $bgColor = $colorCritere2;
                elseif ($indexCritere == 3) $bgColor = $colorCritere3;
                elseif ($indexCritere == 4) $bgColor = $colorCritere4;

                $table = $section->addTable('StyleTableCroisementBrute');
                $table->addRow(null, array('cantSplit' => true));
                $cell = $table->addCell(12000);
                $tableCritere = $cell->addTable('StyleTableCroisementBrute');
                $tableCritere->addRow();
                $cell = $tableCritere->addCell(12000);
                $cell->addText($keyCritere, 'StyleTexte12', 'StyleParagrapheTextCenterSpaceBefore120After120');
                $cell->getStyle()->setGridSpan(6);
                $cell->getStyle()->setBgColor($bgColor);
                $tableCritere->addRow();
                $cell = $tableCritere->addCell(3500)->addText("Etiquette", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell = $tableCritere->addCell(1500);
                $cell->addText("Efficient", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell->getStyle()->setBgColor('92D050');
                $cell = $tableCritere->addCell(1800);
                $cell->addText("Peu dégradé", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell->getStyle()->setBgColor('FFFF00');
                $cell = $tableCritere->addCell(1500);
                $cell->addText("Dégradé", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell->getStyle()->setBgColor('ED7D31');
                $cell = $tableCritere->addCell(1800);
                $cell->addText("Fort dégradé", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                $cell->getStyle()->setBgColor('FF0000');
                $cell = $tableCritere->addCell(1900)->addText("Total général", 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');

                $nbTotalSansRisque = 0;
                $nbTotalPeuDeRisque = 0;
                $nbTotalRisques = 0;
                $nbTotalFortsRisques = 0;

                $nbChoix = 0;
                $indexLign = 1;
                foreach ($tabChoix as $keyChoix => $value) {

                    if ($indexLign % 2 == 1) $bgColor = $colorGray;
                    else $bgColor = $colorWhite;

                    $tableCritere->addRow();
                    $cell = $tableCritere->addCell(3500);
                    $cell->addText($keyChoix, 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1500);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbSansRisque'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1800);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbPeuDeRisque'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1500);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbRisques'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1800);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbFortsRisques'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);
                    $cell = $tableCritere->addCell(1900);
                    $cell->addText($tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbSansRisque'] +
                        $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbPeuDeRisque'] +
                        $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbRisques'] +
                        $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbFortsRisques'], 'StyleTexte10', 'StyleParagrapheTextCenterSpaceBefore40After40');
                    $cell->getStyle()->setBgColor($bgColor);

                    $nbChoix = $nbChoix + 1;
                    $nbTotalSansRisque = $nbTotalSansRisque + $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbSansRisque'];
                    $nbTotalPeuDeRisque = $nbTotalPeuDeRisque + $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbPeuDeRisque'];
                    $nbTotalRisques = $nbTotalRisques + $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbRisques'];
                    $nbTotalFortsRisques = $nbTotalFortsRisques + $tabChapterCritereChoix[$keyChapter][$keyCritere][$keyChoix]['nbFortsRisques'];

                    $indexLign++;

                }

                $tableCritere->addRow();
                $cell = $tableCritere->addCell(3500);
                $cell->addText("Total général", 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1500);
                $cell->addText($nbTotalSansRisque, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1800);
                $cell->addText($nbTotalPeuDeRisque, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1500);
                $cell->addText($nbTotalRisques, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1800);
                $cell->addText($nbTotalFortsRisques, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);
                $cell = $tableCritere->addCell(1900);
                $cell->addText($nbTotalSansRisque + $nbTotalPeuDeRisque + $nbTotalRisques + $nbTotalFortsRisques, 'StyleTexte10', 'StyleParagrapheCenter');
                $cell->getStyle()->setBgColor($colorGray);

                $this->addSautLigne($section, 2);

                $indexCritere++;

            }
            $section->addPageBreak();
        }



        // Retrouner le fichier à l'utilisateur
        $DocxResultName = "RAPPORTBARO_" . date("d-m-Y-H-i-s") . ".docx";
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

    public function formatChapterForExcel($Label) {
        $Label = str_replace('<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">', '', $Label);
        $Label = str_replace('<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold;">', '', $Label);
        $Label = str_replace("<div style='font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold'>", "", $Label);
        $Label = str_replace('</div>', '', $Label);
        return $Label;
    }

    public function formatChapterForWord($Label) {
        $Label = str_replace('<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">', '', $Label);
        $Label = str_replace('<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold;">', '', $Label);
        $Label = str_replace("<div style='font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold'>", "", $Label);
        $Label = str_replace('</div>', '', $Label);
        return $Label;
    }

    public function formatQuestionForWord($Label) {
        $Label = str_replace('<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">', "", $Label);
        $Label = str_replace('<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS;">', "", $Label);
        $Label = str_replace("<div style='font-size: 16px; color: #696252;font-family:Trebuchet MS'>", "", $Label);
        $Label = str_replace('</div>', '', $Label);
        return $Label;
    }

    public function formatQuestionForExcel($Label) {
        $Label = str_replace('<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">', "", $Label);
        $Label = str_replace('<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS;">', "", $Label);
        $Label = str_replace("<div style='font-size: 16px; color: #696252;font-family:Trebuchet MS'>", "", $Label);
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

    public function submitBaromReport(){
        $url = WEB_PATH . 'quiz.html';
        $quizId = $_POST['quizId'];
        $coefEfficientTafv = $_POST['coef_efficient_tafv'];
        $coefPeuDegradePv = $_POST['coef_peu_degrade_pv'];
        $coefDegradePpv = $_POST['coef_degrade_ppv'];
        $coefFortDegradePdtv = $_POST['coef_fort_degrade_pdtv'];
        $risquesSrDe = $_POST['risques_sr_de'];
        $risquesSrA = $_POST['risques_sr_a'];
        $risquesPdrDe = $_POST['risques_pdr_de'];
        $risquesPdrA = $_POST['risques_pdr_a'];
        $risquesRDe = $_POST['risques_r_de'];
        $risquesRA = $_POST['risques_r_a'];
        $risquesFrDe = $_POST['risques_fr_de'];
        $risquesFrA = $_POST['risques_fr_a'];
        $tauxExpositionSrDe = $_POST['taux_exposition_sr_de'];
        $tauxExpositionSrA = $_POST['taux_exposition_sr_a'];
        $tauxExpositionPdrDe = $_POST['taux_exposition_pdr_de'];
        $tauxExpositionPdrA = $_POST['taux_exposition_pdr_a'];
        $tauxExpositionRDe = $_POST['taux_exposition_r_de'];
        $tauxExpositionRA = $_POST['taux_exposition_r_a'];
        $tauxExpositionFrDe = $_POST['taux_exposition_fr_de'];
        $tauxExpositionFrA = $_POST['taux_exposition_fr_a'];

        $barometre = new Quiz();
        $barometre->id = $quizId;
        $barometre->coef_tafv = $coefEfficientTafv;
        $barometre->coef_pv = $coefPeuDegradePv;
        $barometre->coef_ppv = $coefDegradePpv;
        $barometre->coef_pdtv = $coefFortDegradePdtv;
        $barometre->risque_de_sr = $risquesSrDe;
        $barometre->risque_de_pdr = $risquesPdrDe;
        $barometre->risque_de_r = $risquesRDe;
        $barometre->risque_de_fr = $risquesFrDe;
        $barometre->risque_a_sr = $risquesSrA;
        $barometre->risque_a_pdr = $risquesPdrA;
        $barometre->risque_a_r = $risquesRA;
        $barometre->risque_a_fr = $risquesFrA;
        $barometre->taux_de_sr = $tauxExpositionSrDe;
        $barometre->taux_a_sr = $tauxExpositionSrA;
        $barometre->taux_de_pdr = $tauxExpositionPdrDe;
        $barometre->taux_a_pdr = $tauxExpositionPdrA;
        $barometre->taux_de_r = $tauxExpositionRDe;
        $barometre->taux_a_r = $tauxExpositionRA;
        $barometre->taux_de_fr = $tauxExpositionFrDe;
        $barometre->taux_a_fr = $tauxExpositionFrA;

        $quizRepository = new QuizRepository();
        $quizRepository->updateOptionsQuiz($barometre);

        //Recueration des chapitres pour ne savoir le nombre
        $quizQuestionRepository = new QuizQuestionRepository();
        $quizChapters = $quizQuestionRepository->getChapterBarometre($quizId);
        $chapterNumber = 0;
        foreach ($quizChapters as $quizChapter) {
            //On ne prend par les chapitres vides
            if (trim($quizChapter->label) != '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold"></div>') {
                $chapterNumber = $chapterNumber + 1;
            }
        }

        //Recuperation des critères pour compter le nombre de critere
        $quizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
        $quizCriteresBarometre = $quizCriteresBarometreRepository->getCriteresByQuizId($quizId);
        $critereNumber = 0;
        if (array_key_exists('1', $quizCriteresBarometre)) $critereNumber = $critereNumber + 1;
        if (array_key_exists('2', $quizCriteresBarometre)) $critereNumber = $critereNumber + 1;
        if (array_key_exists('3', $quizCriteresBarometre)) $critereNumber = $critereNumber + 1;
        if (array_key_exists('4', $quizCriteresBarometre)) $critereNumber = $critereNumber + 1;

        $quizReportBarometreRepository = new QuizReportBarometreRepository();
        $quizReportBarometreRepository->updateQuizReport($quizId, $_POST, $chapterNumber, $critereNumber);

        Appy::redirigeVers($url);
    }
}
