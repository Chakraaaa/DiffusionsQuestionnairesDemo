<?php

namespace Appy\Src\Controller;

use Appy\Src\Core\Appy;
use Appy\Src\Entity\Quiz;
use Appy\Src\Entity\TemplateQuizQuestions;
use Appy\Src\Repository\GroupesRepository;
use Appy\Src\Repository\QuizUserResponseRepository;
use Appy\Src\Repository\TemplatePrccCategoryRepository;
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

class ReportPrccController extends \Appy\Src\Core\Controller
{
    public function data()
    {
        $quizId = $_GET['quizId'];
        $quizUserId = $_GET['quizUserId'];

        $quizRepository = new QuizRepository();
        $quizUserRepository = new QuizUserRepository();
        $quizUserResponseRepository = new QuizUserResponseRepository();
        $quizQuestionRepository = new QuizQuestionRepository();
        $templatePrccCategoryRepository = new TemplatePrccCategoryRepository();

        //On recupere les infos du quiz
        $quiz = $quizRepository->getQuizById($quizId);

        //On recupère les infos du répondant au quiz
        $quizUser = $quizUserRepository->getQuizUserById($quizUserId);

        //Recuperation des libellés des catégories
        $categoriesPrcc = $templatePrccCategoryRepository->getAllPrccCategoryTemplates();

        //On recupere les reponses des user
        $critereRecherche = [];
        $critereRecherche['quizId'] = $quizId;
        $critereRecherche['responseRequired'] = 1;
        $critereRecherche['quizUserId'] = $quizUser->id;
        $usersResponses = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");

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

        $sheet->getDefaultColumnDimension()->setWidth(4);

        $sheet->getStyle("A")->getAlignment()->setHorizontal('left');
        $sheet->getStyle("A")->getAlignment()->setVertical('center');
        $sheet->getStyle("A")->getFont()->setSize(11);
        $sheet->getColumnDimension('A')->setWidth(40);

        $sheet->getStyle("A2")->getFont()->setBold(true);
        $sheet->getStyle("D2:D100")->getFont()->setBold(true);

        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getStyle('C1:C100')->getAlignment()->setWrapText(true);

        $sheet->getStyle("G1:V2")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("G1:V2")->getAlignment()->setVertical('center');
        $sheet->getStyle("G1:V1")->getAlignment()->setWrapText(true);
        $sheet->getStyle("G1:V2")->getFont()->setSize(11);
        $sheet->getStyle("G1:V1")->getFont()->setBold(true);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(18);
        $sheet->getColumnDimension('L')->setWidth(18);
        $sheet->getColumnDimension('M')->setWidth(18);
        $sheet->getColumnDimension('N')->setWidth(18);
        $sheet->getColumnDimension('O')->setWidth(18);
        $sheet->getRowDimension(1)->setRowHeight(35);



        $sheet->setCellValueByColumnAndRow(1, 2, 'Nom / Prénom / Email');
        //Cas special du PRCC exemple
        if($quiz->id == 3) {
            $sheet->setCellValueByColumnAndRow(1, 3, "MATTEI Jérome ");
        } else {
            $sheet->setCellValueByColumnAndRow(1, 3, $quizUser->userLastName . " " . $quizUser->userFirstName . " " . $quizUser->userEmail);
        }

        $lignNumber = 2;
        $questionNumber = 1;
        foreach ($usersResponses as $userResponses) {

            $quizQuestion = $quizQuestionRepository->getQuestionById($userResponses->questionId);

            $labelQuestion = str_replace('<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">', '', $quizQuestion->label);
            $labelQuestion = str_replace("<div style='font-size: 16px; color: #696252;font-family:Trebuchet MS'>", '', $labelQuestion);
            $labelQuestion = str_replace('</div>', '', $labelQuestion);

            $value = "";
            if($userResponses->value == "PV") $value = 1;
            if($userResponses->value == "PF") $value = 0;

            $sheet->setCellValueByColumnAndRow(3, $lignNumber, $labelQuestion);
            $sheet->setCellValueByColumnAndRow(4, $lignNumber, "Q" . $questionNumber);
            $sheet->setCellValueByColumnAndRow(5, $lignNumber, $value);

            $lignNumber++;
            $questionNumber++;

        }

        $note = "Note pour voir les questions concernées par ce chiffre.\r\n\r\nCliquer sur cette cellule puis cliquer dans la barre de formule de SOMME.\r\n\r\nLes cellules concernées sont alors encadrées dans le fichier Excel.";
        // Parent normatif excessif -> Category 2 -> questions 6 9 20 22 49 68 71 77 80 81
        $sheet->setCellValueByColumnAndRow(7, 1, $categoriesPrcc[2]->labelShort);
        $sheet->setCellValue("G2", '=SUM(' . $this->getStringQuestionForSum(2) . ')');
        $sheet->getComment('G2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        // Parent nourricier excessif -> Category 5 ->  -> questions 2 10 24 42 54 55 66 85 86 87
        // 55 car il et sur le papier mais pas dans le Excel
        $sheet->setCellValueByColumnAndRow(8, 1, $categoriesPrcc[5]->labelShort);
        $sheet->setCellValue("H2", '=SUM(' . $this->getStringQuestionForSum(5) . ')');
        $sheet->getComment('H2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        // Parent normatif -> Category 1 ->  -> questions 8 11 27 43 45 76 78 79 83 84
        $sheet->setCellValueByColumnAndRow(9, 1, $categoriesPrcc[1]->labelShort);
        $sheet->setCellValue("I2", '=SUM(' . $this->getStringQuestionForSum(1) . ')');
        $sheet->getComment('I2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        // Parent donnant -> Category 4 ->  -> questions 15 23 25 26 30 32 38 46 47 53
        $sheet->setCellValueByColumnAndRow(10, 1, $categoriesPrcc[4]->labelShort);
        $sheet->setCellValue("J2", '=SUM(' . $this->getStringQuestionForSum(4) . ')');
        $sheet->getComment('J2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        // Adulte -> Category 7 ->  -> questions 3 5 16 31 40 41 50 51 52 72
        $sheet->setCellValueByColumnAndRow(11, 1, $categoriesPrcc[7]->labelShort);
        $sheet->setCellValue("K2", '=SUM(' . $this->getStringQuestionForSum(7) . ')');
        $sheet->getComment('K2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        // Enfant libre -> Category 9 ->  -> questions 13 33 35 60 61 62 65 74 75 88
        $sheet->setCellValueByColumnAndRow(12, 1, $categoriesPrcc[9]->labelShort);
        $sheet->setCellValue("L2", '=SUM(' . $this->getStringQuestionForSum(9) . ')');
        $sheet->getComment('L2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        // Enfant adapté -> Category 12 ->  -> questions 4 18 19 37 57 58 64 70 82 90
        $sheet->setCellValueByColumnAndRow(13, 1, $categoriesPrcc[12]->labelShort);
        $sheet->setCellValue("M2", '=SUM(' . $this->getStringQuestionForSum(12) . ')');
        $sheet->getComment('M2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        // Enfant libre excessif -> Category 10 ->  -> questions 12 14 21 36 39 48 63 69 73 89
        $sheet->setCellValueByColumnAndRow(14, 1, $categoriesPrcc[10]->labelShort);
        $sheet->setCellValue("N2", '=SUM(' . $this->getStringQuestionForSum(10) . ')');
        $sheet->getComment('N2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        // Enfant adapté excessif -> Category 13 ->  -> questions 1 7 17 28 29 34 44 56 59 67
        $sheet->setCellValueByColumnAndRow(15, 1, $categoriesPrcc[13]->labelShort);
        $sheet->setCellValue("O2", '=SUM(' . $this->getStringQuestionForSum(13) . ')');
        $sheet->getComment('O2')->setWidth("400px")->setHeight("150px")->getText()->createTextRun($note);

        $docName = "PRCC-" . $quizId;
        $docName = str_replace(' ', '_', $docName);
        $docName = str_replace(',', '-', $docName);
        $docName .= '-' . date("d-m-Y-H-i-s") . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($docName);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($docName);

        $this->downloadFileExcel($docName, $docName);
    }

    public function getStringQuestionForSum($prccCategoryId) {

        $templateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $questions = $templateQuizQuestionsRepository->getQuestionsByPrccCategory($prccCategoryId);
        $stringQuestionNumber  = "";
        foreach ($questions as $questions) {
            $stringQuestionNumber = $stringQuestionNumber . "E" . (intval($questions->ordre) + 1) . ",";
        }
        //Supprime le dernier ,
        $stringQuestionNumber = substr($stringQuestionNumber, 0, -1);

        return $stringQuestionNumber;
    }

    public function formatChapterForExcel($Label) {
        $Label = str_replace('<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">', '', $Label);
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
        $quizUserId = $_GET['quizUserId'];
        $pageGarde = 0;
        if (isset($_GET['pagegarde'])) {
            $pageGarde = $_GET['pagegarde'];
        }

        $quizRepository = new QuizRepository();
        $quizUserRepository = new QuizUserRepository();
        $quizUserResponseRepository = new QuizUserResponseRepository();
        $templatePrccCategoryRepository = new TemplatePrccCategoryRepository();

        //On recupere les infos du quiz
        $quiz = $quizRepository->getQuizById($quizId);

        //On recupère les infos du répondant au quiz
        $quizUser = $quizUserRepository->getQuizUserById($quizUserId);

        //Recuperation des libellés des catégories
        $categoriesPrcc = $templatePrccCategoryRepository->getAllPrccCategoryTemplates();

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::FR_FR));

        $phpWord->addParagraphStyle('StyleParagrapheSautDeLigne', ['name' => 'Trebuchet MS', 'align' => 'left', 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheText1FirstPage', ['align' => 'left', 'spaceBefore' => 200, 'spaceAfter' => 0, 'spacing' => 0]);
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
        $phpWord->addParagraphStyle('StyleParagrapheCenterTabText', ['align' => 'center', 'spaceAfter' => 0, 'spacing' => 0, 'indentation' => array('left' => 120, 'right' => 0)]);
        $phpWord->addParagraphStyle('StyleParagrapheLeftTabText', ['align' => 'left', 'spaceAfter' => 30, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheTablePrccTitle', ['align' => 'center', 'spaceAfter' => 30, 'spacing' => 0]);
        $phpWord->addParagraphStyle('StyleParagrapheTablePrccNumber', ['name' => 'Trebuchet MS', 'align' => 'center', 'spaceBefore' => 10, 'spaceAfter' => 10]);
        $phpWord->addFontStyle('StyleTexte5White', ['name' => 'Trebuchet MS', 'size' => 5, 'color' => "FFFFFF", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte8', ['name' => 'Trebuchet MS', 'size' => 8, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte9', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte9Orange', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => "ff8000"]);
        $phpWord->addFontStyle('StyleTexteChapter', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => 'E9660B', 'bold' => true, 'space' => array('before' => 10)]);
        $phpWord->addFontStyle('StyleTexte10Bold', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte10', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte10RougeBold', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte10Rouge', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => 'E9660B']);
        $phpWord->addFontStyle('StyleTexte10WhiteBold', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "FFFFFF", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte11', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte11Colle', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'space' => array('before' => 5, 'after' => 5)]);
        $phpWord->addFontStyle('StyleTexte11Rouge', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B']);
        $phpWord->addFontStyle('StyleTexte11RougeMarginLeft', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B', 'indentation' => array('left' => 400, 'right' => 0)]);
        $phpWord->addFontStyle('StyleTexte11Colle10Before', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "000000", 'space' => array('before' => 10)]);
        $phpWord->addFontStyle('StyleTexte11Bold', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte11RougeBold', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "E9660B", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte11BoldUnderline', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'bold' => true, 'underline' => 'single']);
        $phpWord->addFontStyle('StyleTexte12', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte12Noir', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => "000000"]);
        $phpWord->addFontStyle('StyleTexte12RougeBold', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte13Bold', ['name' => 'Trebuchet MS', 'size' => 13, 'color' => "696252", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte15Bold', ['name' => 'Trebuchet MS', 'size' => 15, 'color' => '696252', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte14Rouge', ['name' => 'Trebuchet MS', 'size' => 14, 'color' => 'E9660B']);
        $phpWord->addFontStyle('StyleTexte14RougeBold', ['name' => 'Trebuchet MS', 'size' => 14, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte18RougeBold', ['name' => 'Trebuchet MS', 'size' => 18, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte20', ['name' => 'Trebuchet MS', 'size' => 20, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte20Rouge', ['name' => 'Trebuchet MS', 'size' => 20, 'color' => "E9660B"]);
        $phpWord->addFontStyle('StyleTexte28RougeBold', ['name' => 'Trebuchet MS', 'size' => 28, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('list1', array('name' => 'Trebuchet MS', 'size' => 11, 'color' => '696252'));
        $phpWord->addTableStyle('StyleTableFirstPage', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableFooter', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableFirstPage', ['name' => 'Trebuchet MS', 'size' => 8, 'borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableRecap', ['borderSize' => 0, 'borderColor' => '000000', 'cellMarginLeft' => 150, 'cellMarginRight' => 150, 'cellMarginTop' => 100, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);
        $phpWord->addTableStyle('StyleTableText', ['borderSize' => 0, 'borderColor' => 'ffffff', 'cellMarginLeft' => 50, 'cellMarginRight' => 50, 'cellMarginTop' => 50, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);

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

            $this->addSautLigne($sectionPageGarde, 18);

            $textrun = $sectionPageGarde->addText("PROFIL RELATIONNEL COMPORTEMENTAL ET COGNITIF", 'StyleTexte20', 'StyleParagrapheCenterAfterColle');

            $this->addSautLigne($sectionPageGarde, 5);

            $textrun = $sectionPageGarde->addText($quizUser->userFirstName . " " . $quizUser->userLastName, 'StyleTexte20Rouge', 'StyleParagrapheCenterAfterColle');

            $this->addSautLigne($sectionPageGarde, 5);

            $table = $sectionPageGarde->addTable('StyleTableHeader');
            $table->addRow();
            $cell = $table->addCell(2000);
            $cell->addText('000000000000', 'StyleTexte5White', 'StyleParagrapheFooterHaut');
            $cell = $table->addCell(6000);
            $cell->addImage(BASE_PATH . "/assets/images/logo-rm-opacity.png", array('height' => 160, 'align' => 'center'));
            $cell = $table->addCell(2000);
            $cell->addText('000000000000', 'StyleTexte5White', 'StyleParagrapheFooterHaut');

            $this->addSautLigne($sectionPageGarde, 2);

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

        //HEADER
        //Tableau du header avec le logo uniquement sur la 1ere page
        $header = $section->addHeader();
        $header->firstPage();
        $table = $header->addTable('StyleTableHeader');
        $table->addRow();
        $cell = $table->addCell(4000);
        $cell->addImage(BASE_PATH . "/assets/images/logo-rm-simple.png", array('height' => 50,'width' => 150, 'align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addText(' ');
        $textrun = $cell->addTextRun('StyleParagrapheLeftColle');
        $textrun->addText("LE P.R.C.C.(1)", 'StyleTexte15Bold');

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

        //1ere page
        $table = $section->addTable('StyleTableFirstPage');
        $table->addRow();
        $cell = $table->addCell(5000);
        $cell = $table->addCell(5000);
        $textrun = $cell->addTextRun('StyleParagrapheRightColle');
        $textrun->addText($quizUser->userLastName . " " . $quizUser->userFirstName, 'StyleTexte12');
        $table->addRow();
        $cell = $table->addCell(5000);
        $cell = $table->addCell(5000);
        $textrun = $cell->addTextRun('StyleParagrapheRightColle');
        $now = new \DateTime();
        $textrun->addText(ucfirst(strftime('%B %Y',$now->getTimestamp())), 'StyleTexte12');

        $textrun = $section->addText("Ou ma stratégie habituelle pour m'en tirer dans la vie", 'StyleTexte12', 'StyleParagrapheCenterAfterColle');

        $this->addSautLigne($section, 1);

        $table = $section->addTable('StyleTableRecap');

        //Ajout de la ligne de legend
        // Pour mettre une hauteur fixe sur une ligne utiliser $row = $table->addRow(450, array("exactHeight" => true));
        $row = $table->addRow();
        $cell = $table->addCell(800);
        $cell->addText(' ', 'StyleTexte8');
        $cell->getStyle()->setBorderTopColor('ffffff');
        $cell->getStyle()->setBorderLeftColor('ffffff');
        $cell->getStyle()->setBorderTopSize(0);
        $cell->getStyle()->setBorderLeftSize(0);
        $cell = $table->addCell(600);
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[1]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[4]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[7]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[9]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[12]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(600);

        //On calcul le nombre de reponse positives pour 5  category de questions
        // Parent normatif - 1
        // parent nourricier - 4
        // Adulte - 7
        // Enfant libre - 9
        // Enfant adopté - 12
        $critereRecherche = [];
        $critereRecherche['quizId'] = $quizId;
        $critereRecherche['responseRequired'] = 1;
        $critereRecherche['prccCategory'] = 1;
        $critereRecherche['quizUserId'] = $quizUser->id;
        $usersResponsesCat1 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");
        $critereRecherche['prccCategory'] = 4;
        $usersResponsesCat4 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");
        $critereRecherche['prccCategory'] = 7;
        $usersResponsesCat7 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");
        $critereRecherche['prccCategory'] = 9;
        $usersResponsesCat9 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");
        $critereRecherche['prccCategory'] = 12;
        $usersResponsesCat12 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");

        //On compte le nombre de Plutot vrai
        $numberPVCat1 = 0;
        $numberPVCat4 = 0;
        $numberPVCat7 = 0;
        $numberPVCat9 = 0;
        $numberPVCat12 = 0;
        foreach ($usersResponsesCat1 as $userResponses) if($userResponses->value == "PV") $numberPVCat1 = $numberPVCat1 + 1;
        foreach ($usersResponsesCat4 as $userResponses) if($userResponses->value == "PV") $numberPVCat4 = $numberPVCat4 + 1;
        foreach ($usersResponsesCat7 as $userResponses) if($userResponses->value == "PV") $numberPVCat7 = $numberPVCat7 + 1;
        foreach ($usersResponsesCat9 as $userResponses) if($userResponses->value == "PV") $numberPVCat9 = $numberPVCat9 + 1;
        foreach ($usersResponsesCat12 as $userResponses) if($userResponses->value == "PV") $numberPVCat12 = $numberPVCat12 + 1;

        $this->displayLignTablePrcc($table, 10,"first",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 9,"blank",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 8,"blank",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 7,"blank",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 6,"blank",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 5,"smileyHigh",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 4,"blank",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 3,"blank",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 2,"blank",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);
        $this->displayLignTablePrcc($table, 1,"last",$numberPVCat1,$numberPVCat4,$numberPVCat7,$numberPVCat9,$numberPVCat12);

        //On calcul le nombre de reponse positives pour 5  category de questions
        // Parent normatif excessif - 2
        // parent nourricier excessif - 5
        // Enfant libre excessif - 10
        // Enfant adopté excessif - 13
        $critereRecherche['prccCategory'] = 2;
        $usersResponsesCat2 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");
        $critereRecherche['prccCategory'] = 5;
        $usersResponsesCat5 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");
        $critereRecherche['prccCategory'] = 10;
        $usersResponsesCat10 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");
        $critereRecherche['prccCategory'] = 13;
        $usersResponsesCat13 = $quizUserResponseRepository->getQuizUserResponse($critereRecherche, "ordre");

        //On compte le nombre de Plutot vrai
        $numberPVCat2 = 0;
        $numberPVCat5 = 0;
        $numberPVCat10 = 0;
        $numberPVCat13 = 0;
        foreach ($usersResponsesCat2 as $userResponses) if($userResponses->value == "PV") $numberPVCat2 = $numberPVCat2 + 1;
        foreach ($usersResponsesCat5 as $userResponses) if($userResponses->value == "PV") $numberPVCat5 = $numberPVCat5 + 1;
        foreach ($usersResponsesCat10 as $userResponses) if($userResponses->value == "PV") $numberPVCat10 = $numberPVCat10 + 1;
        foreach ($usersResponsesCat13 as $userResponses) if($userResponses->value == "PV") $numberPVCat13 = $numberPVCat13 + 1;

        $this->displayLignTablePrcc($table, 1,"first",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 2,"blank",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 3,"blank",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 4,"blank",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 5,"smileyLow",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 6,"blank",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 7,"blank",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 8,"blank",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 9,"blank",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);
        $this->displayLignTablePrcc($table, 10,"last",$numberPVCat2,$numberPVCat5,-1,$numberPVCat10,$numberPVCat13);

        //Ajout de la ligne de legend
        // Pour mettre une hauteur fixe sur une ligne utiliser $row = $table->addRow(450, array("exactHeight" => true));
        $row = $table->addRow();
        $cell = $table->addCell(800);
        $cell->addText(' ', 'StyleTexte8');
        $cell->getStyle()->setBorderBottomColor('ffffff');
        $cell->getStyle()->setBorderLeftColor('ffffff');
        $cell->getStyle()->setBorderBottomSize(0);
        $cell->getStyle()->setBorderLeftSize(0);
        $cell = $table->addCell(600);
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[2]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[5]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(1500);
        $cell->getStyle()->setBorderBottomColor('ffffff');
        $cell->getStyle()->setBorderTopColor('ffffff');
        $cell->getStyle()->setBorderBottomSize(0);
        $cell->getStyle()->setBorderTopSize(0);
        $cell->addText('', 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[10]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(1500);
        $cell->addText($categoriesPrcc[13]->labelShort, 'StyleTexte10WhiteBold', "StyleParagrapheTablePrccTitle");
        $cell->getStyle()->setBgColor('E9660B');
        $cell = $table->addCell(600);

        $this->addSautLigne($section, 1);

        $textrun = $section->addTextRun();
        $textrun->addText("(1) Le P.R.C.C. (Profil Relationnel Cognitif et Comportemental) est à l'origine un test souvent connu sous le nom de \"stratégogramme\" construit sur les fondements de l'analyse transactionnelle. Il est toujours essentiellement fondé sur l'analyse transactionnelle mais a été reconstruit par le Cabinet Relais Managers. Il ne s'agit pas d'un test de personnalité mais d'aide à la réflexion sur ses modes relationnels, ses façons d'analyser les situations, ses réactions. Nous avons retenu cet outil car il repose sur des fondements solides, il est simple d'accès, utilisable au quotidien. Il est ensuite facile, via un accompagnement ou de courtes formations (une majorité d'intervenants maitrisent cette approche), de travailler sur ses axes de vigilance.", 'StyleTexte9', 'StyleParagrapheLeftColle');


        //$section->addPageBreak();

        $section->addText("Le P.R.C.C. ci-dessus indique que " . $quizUser->userFirstName .  " :", 'StyleTexte11Bold', 'StyleParagrapheText');

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat1 > 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[1]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble bien présent", $styleTitle, 'StyleParagrapheText');
        $section->addText("Qui que soit l’interlocuteur et le statut :", $styleText, 'StyleParagrapheText');
        $arrayText = array(
            "Sait poser un cadre clair, fondé, juste, légitime (les attendus ; les règles ; les contraintes et limites ; le « contrat »)",
            "Sait dire ce qui pose problème, clairement mais avec pédagogie, avec objectivité en se référant aux faits et au cadre et non à ses seuls avis et jugements",
            "Est conscient que le regard de son interlocuteur peut différer du sien et sera prêt à l’étudier",
            "Sait acter un désaccord et le poser comme un sujet à résoudre",
            "Sait être ferme et imposer (sans agressivité, sans être dans la lutte de pouvoir), lorsque la Co-réflexion n’a pas été possible et que ses interlocuteurs sont hors cadre et sans légitimité");
        $this->addTabText($arrayText, $section, $styleText);
        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat1 <= 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText("Semble devoir être développé", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Peut penser qu’il n’est pas utile d’expliquer l’attendu ou les règles car « c’est évident, implicite » ; peut ne pas revenir sur l’attendu ou les règles pensant « qu’une fois que cela a été dit, c’est intégré »",
            "Peut avoir du mal à reposer l’attendu, le contrat",
            "Peut craindre de se positionner, par souci de comment il va être perçu derrière",
            "Peut avoir du mal à confronter aux écarts, à la règle");
        $this->addTabText($arrayText, $section, $styleText);

        $this->addSautLigne($section, 2);

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat2 >= 4) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[2]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble trop présent", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Peut avoir tendance avec tout le monde ou certaines personnes et/ou lui- même à être extrêmement exigeant ; au-delà de ce que le « contrat », le cadre, le contexte justifie… par principe ou via des valeurs telles que : « il faut toujours chercher à se dépasser »",
            "A tendance à voir tout ce qui ne va pas et a du mal à différencier ce qui est de l’ordre du détail sans réelle conséquence de ce qui doit être traité",
            "S’appuie davantage sur ses principes, valeurs, croyances plutôt que sur un cadre, des fondements, des faits, une analyse",
            "A tendance à considérer que les « faibles ont des excuses alors que les forts ont des solutions »",
            "A du mal à comprendre les limites des autres ou qu’on ne le comprenne pas",
            "A tendance à s’impatienter");
        $this->addTabText($arrayText, $section, $styleText);

        $this->addSautLigne($section, 2);

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat4 > 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[4]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble bien présent", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Propose, dans la mesure du possible, son aide et son soutien si réellement nécessaire, mais en restant vigilant à ne pas compenser les « manquements » des autres, à ne pas faire à la place ou prendre en charge ce qu’un autre est censé et pourrait résoudre",
            "Aide son entourage à trouver ses solutions (plutôt que proposer des solutions ou résoudre en lieu et place du dit entourage) (apprend aux autres à pécher plutôt qu’il ne les nourrit) ; il fait grandir ses collaborateurs",
            "Sait faire la différence entre « prendre en compte » et « prendre en charge »");
        $this->addTabText($arrayText, $section, $styleText);
        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat4 <= 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText("Semble devoir être développé", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Peut avoir du mal à être dans l’empathie, dans la compréhension de ceux qui ne sont pas comme lui, de ceux qui font différemment de lui",
            "Peut avoir des difficultés à se soucier des difficultés de l’autre",
            "Peut être assez peu dans le soutien, la valorisation, la reconnaissance, l’accompagnement de ses équipes",
            "Peut avoir tendance à répondre par la norme");
        $this->addTabText($arrayText, $section, $styleText);

        $this->addSautLigne($section, 2);

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat5 >= 4) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[5]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble trop présent", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Apporte son aide dès que quelqu’un lui semble être en difficulté même si on ne lui demande pas",
            "Ne distingue pas un besoin d’aide ponctuel et légitime d’une demande où le bénéficiaire est censé savoir et pouvoir résoudre par lui-même",
            "Résout plutôt que d’apprendre à résoudre",
            "Explique, apporte des solutions plutôt que de demander au bénéficiaire ce qu’il pourrait faire pour résoudre ses difficultés",
            "Peut s’oublier, se surcharger pour aider son entourage",
            "Se sent incapable de ne pas arriver à aider quelqu’un");
        $this->addTabText($arrayText, $section, $styleText);

        $this->addSautLigne($section, 2);

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat7 > 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[7]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble bien présent", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Analyse les situations avec le plus d’objectivité possible, en se référant au cadre, aux enjeux, au contexte, aux causes et conséquences, à d’autres regards avant d’agir, de décider",
            "Cherche dans la mesure du possible, à ne pas réagir à chaud notamment quand des situations sont stressantes et génèrent des émotions…",
            "Incite ses interlocuteurs à la réflexion ou à contribuer à une réflexion à trouver causes et solutions plutôt que d’expliquer les siennes",
            "Prend du recul ; sait s’extraire de ses jugements, de ses ressentis pour regarder plus objectivement une situation",
            "Sait aussi tourner sa capacité de réflexion sur soi afin de se questionner voire de se remettre en question quand c’est nécessaire");
        $this->addTabText($arrayText, $section, $styleText);
        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat7 <= 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText("Semble devoir être développé", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Peut avoir des difficultés à réfléchir, à objectiver, à prendre du recul",
            "Peut avoir tendance à être dans le jugement",
            "Peut confondre ses ressentis avec la réalité ; peut considérer que sa perception est la vérité",
            "Peut avoir du mal à aider les autres à réfléchir",
            "Peut considérer que les autres sont souvent la cause des problèmes alors que lui est souvent ok",
            "Peut considérer que réfléchir n’est pas agir ; peut considérer qu’on amoindrit son rôle de « chef » en étant trop dans la réflexion");
        $this->addTabText($arrayText, $section, $styleText);

        $section->addPageBreak();

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat9 > 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[9]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble bien présent", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Est en contact avec ce qu’il ressent (colère, joie, craintes, tristesse…) et ne cherche pas à ne pas ressentir",
            "Sait exprimer ce qu’il ressent si le cadre est adéquat",
            "Est spontané tout en prenant en compte le moment, le cadre, les conséquences de son expression",
            "Se rend lisible aux yeux des autres (on sait ce qu’il ressent) sans pour autant être intempestif, impulsif…",
            "Approuve et favorise la spontanéité des autres",
            "Sait distinguer spontanéité, expression libre, créativité de ce qui serait plutôt de l’impulsivité, une difficulté à se gérer et à gérer ses frustrations");
            $this->addTabText($arrayText, $section, $styleText);
        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat9 <= 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText("Semble devoir être développé", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Peut se trouver en difficulté dans les situations qui font appel à de la créativité, qui demandent de sortir des sentiers battus, du faire ou de la réflexion d’habitude employée",
            "Peut manquer de spontanéité ou être en difficulté voire juger la spontanéité des autres",
            "Peut avoir du mal à se réjouir, à se faire du bien, à s’écouter, à rire",
            "A tendance à prendre peu de liberté, à peu s’autoriser ; peut avoir du mal avec les personnes qui s’autorisent des choses (spontanéité, un petit écart par rapport au cadre) ; il peut manifester une forte réaction (incompréhension, jugement, colère rentrée ou non) face à quelqu’un qui franchit les limites",
            "Dit difficilement « stop », « non »",
            "Peut paraître peu lisible aux yeux des autres ; peut paraître peu sympathique ou difficilement accessible");
        $this->addTabText($arrayText, $section, $styleText);

        $this->addSautLigne($section, 2);

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat10 >= 4) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[10]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble trop présent", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "A du mal à se contenir, à ne pas réagir de façon impulsive dans un certain nombre de circonstances",
            "A du mal avec les contraintes, les limites (les siennes et/ou celles qu’on lui impose) ",
            "Peut facilement s’emporter, être mécontent même s’il ne le manifeste pas",
            "A tendance à être contestataire, en désaccord, réfractaire, notamment quand une situation le frustre ou le stresse ou lui donne le sentiment de limiter sa liberté, de renforcer les contraintes…",
            "Entre facilement dans la joute oratoire, dans la contre argumentation lors de discussion à enjeux",
            "Répond vite à la provocation");
        $this->addTabText($arrayText, $section, $styleText);

        $section->addPageBreak();

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat12 > 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[12]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble bien présent", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Sait faire avec les contraintes lorsqu’elles sont légitimes et restent supportables",
            "Sait différer ses réactions lorsqu’elles risquent d’être trop vives ou hors cadre",
            "Sait gérer sa frustration, prendre du recul, s’apaiser pour réagir avec plus de justesse",
            "Sait se remettre en question, entendre des critiques constructives et fondées");
        $this->addTabText($arrayText, $section, $styleText);
        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat12 <= 6) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText("Semble devoir être développé", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
        "Peut-être en difficulté pour accepter la réalité qui ne lui convient pas, peut avoir du mal avec les contraintes, les limites alors qu’elles sont fondées et à priori acceptables des postures qui l’agacent",
        "Peut manquer de goût pour l’effort",
        "Peut manquer de souplesse, de flexibilité");
        $this->addTabText($arrayText, $section, $styleText);

        $this->addSautLigne($section, 2);

        $styleTitle = "StyleTexte10Rouge";
        $styleText = "StyleTexte10";
        if($numberPVCat13 >= 4) {
            $styleTitle = "StyleTexte11RougeBold";
            $styleText = "StyleTexte11Bold";
        }
        $section->addText($categoriesPrcc[13]->label, "StyleTexte11RougeBold", 'StyleParagrapheText');
        $section->addText("Semble trop présent", $styleTitle, 'StyleParagrapheText');
        $arrayText = array(
            "Se sur adapte (pas toujours consciemment) même lorsque les raisons ne sont pas vraiment fondées ou lorsque la charge est trop lourde",
            "Ne sait pas (se) poser de limites, (se) dire non",
            "Cherche à être parfait, irréprochable",
            "A tendance à se conformer sans même se demander si c’est fondé et possible",
            "Peut se révéler corvéable à merci");
        $this->addTabText($arrayText, $section, $styleText);

        $this->addSautLigne($section, 2);

        // Retrouner le fichier à l'utilisateur
        $DocxResultName = "RAPPORT_PRCC_" . date("d-m-Y-H-i-s") . ".docx";
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
    private function addTabText($arrayText, $section, $style)
    {
        $table = $section->addTable('StyleTableText');

        foreach($arrayText as $key=>$value) {
            $row = $table->addRow();
            $cell = $table->addCell(800);
            $cell->addText('-', $style,'StyleParagrapheCenterTabText');
            $cell = $table->addCell(10000);
            $cell->addText($value, $style, 'StyleParagrapheLeftTabText');
        }
    }


    private function displayLignTablePrcc($table, $number, $typeLign, $number1, $number2, $number3, $number4, $number5)
    {
        $table->addRow(400, array("exactHeight" => true));
        $cell = $table->addCell(800);
        if ($typeLign == "blank" || $typeLign == "smileyHigh"|| $typeLign == "smileyLow") {
            $cell->getStyle()->setBorderBottomColor('ffffff');
            $cell->getStyle()->setBorderTopColor('ffffff');
            $cell->getStyle()->setBorderBottomSize(0);
            $cell->getStyle()->setBorderTopSize(0);
        } elseif ($typeLign == "first") {
            $cell->getStyle()->setBorderBottomColor('ffffff');
            $cell->getStyle()->setBorderBottomSize(0);
        }  elseif ($typeLign == "last") {
            $cell->getStyle()->setBorderTopColor('ffffff');
            $cell->getStyle()->setBorderTopSize(0);
        }
        if ($typeLign == "smileyHigh") {
            $cell->addImage(BASE_PATH . "/assets/images/smileyHigh.jpg", array('height' => 15, 'align' => 'center'));
        } elseif ($typeLign == "smileyLow") {
            $cell->addImage(BASE_PATH . "/assets/images/smileyLow.jpg", array('height' => 15, 'align' => 'center'));
        } else {
            $cell->addText(' ', 'StyleTexte8');
        }
        $cell = $table->addCell(600);
        $cell->addText($number, 'StyleTexte12Noir','StyleParagrapheTablePrccNumber');
        $cell = $table->addCell(1500);
        $cell->addText(' ', 'StyleTexte8');
        if($number1 >= $number) $cell->getStyle()->setBgColor('696252');
        $cell = $table->addCell(1500);
        $cell->addText(' ', 'StyleTexte8');
        if($number2 >= $number) $cell->getStyle()->setBgColor('696252');
        $cell = $table->addCell(1500);
        if($number3 == -1) {
            $cell->getStyle()->setBorderBottomColor('ffffff');
            $cell->getStyle()->setBorderTopColor('ffffff');
            $cell->getStyle()->setBorderBottomSize(0);
            $cell->getStyle()->setBorderTopSize(0);
        }
        $cell->addText(' ', 'StyleTexte8');
        if($number3 >= $number) $cell->getStyle()->setBgColor('696252');
        $cell = $table->addCell(1500);
        $cell->addText(' ', 'StyleTexte8');
        if($number4 >= $number) $cell->getStyle()->setBgColor('696252');
        $cell = $table->addCell(1500);
        $cell->addText(' ', 'StyleTexte8');
        if($number5 >= $number) $cell->getStyle()->setBgColor('696252');
        $cell = $table->addCell(600);
        $cell->addText($number, 'StyleTexte12Noir','StyleParagrapheTablePrccNumber');
    }
    private function formatForWordQuestion($label)
    {
        $labelFormated = str_replace('9px', '12px', $label);
        $labelFormated = str_replace('14px', '12px', $label);
        $labelFormated = str_replace('<br>', '<br/>', $labelFormated);

        return $labelFormated;
    }

}
