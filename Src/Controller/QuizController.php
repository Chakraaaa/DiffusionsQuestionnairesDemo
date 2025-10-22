<?php

namespace Appy\Src\Controller;

use Appy\Src\Core\Appy;
use Appy\Src\Entity\Quiz;
use Appy\Src\Entity\QuizUser;
use Appy\Src\Entity\ResponseQuizCriteresBarometre;
use Appy\Src\Entity\TemplateEmail;
use Appy\Src\Entity\QuizCriteresBarometre;
use Appy\Src\Entity\Parameters;
use Appy\Src\Entity\TemplateQuizOptions;
use Appy\Src\Repository\ParametersRepository;
use Appy\Src\Repository\QuizReportBarometreRepository;
use Appy\Src\Repository\ResponseQuizCriteresBarometreRepository;
use Appy\Src\Repository\TemplateEmailRepository;
use Appy\Src\Repository\GroupesRepository;
use Appy\Src\Repository\QuizUserResponseRepository;
use Appy\Src\Repository\TemplatePrccCategoryRepository;
use Appy\Src\Repository\TemplateQuizOptionsRepository;
use Appy\Src\Repository\UsersRepository;
use Appy\Src\Repository\QuizRepository;
use Appy\Src\Repository\QuizUserRepository;
use Appy\Src\Repository\TemplateQuizQuestionsRepository;
use Appy\Src\Repository\QuizQuestionRepository;
use Appy\Src\Repository\QuizCriteresBarometreRepository;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Language;
use DateTime;
class QuizController extends \Appy\Src\Core\Controller
{
    public function rechercheQuiz()
    {
        $msg_erreur = [];
        $url = WEB_PATH . 'quiz.html';
        $urlCreate = WEB_PATH . 'quiz.html/createQuiz';
        $critereRecherche = [];
        if (isset($_POST['quiz-type'])) {
            $quizType = filter_input(INPUT_POST, 'quiz-type', FILTER_SANITIZE_SPECIAL_CHARS);
            $_SESSION['recherche']['quiz-type'] = $quizType; // Stockage dans la session
        }
        if (isset($_SESSION['recherche']['quiz-type'])) {
            $critereRecherche['quiz-type'] = $_SESSION['recherche']['quiz-type'];
        } else {
            $critereRecherche['quiz-type'] = '';
        }
        $critereRecherche['deleted'] = '0';
        $quizRepository = new QuizRepository();
        $quizzes = $quizRepository->getQuizzes($critereRecherche);
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'Quiz';
        self::showVue([
            'quizzes' => $quizzes,
            'urlCreate' => $urlCreate,
            'msg_erreur' => $msg_erreur,
            'url' => $url,
        ]);
    }

    public function delete(){
        $url = WEB_PATH . 'quiz.html';
        $quizId = $_GET['quizId'];
        $quizRepository = new QuizRepository();
        $quizRepository->deleteQuiz($quizId);
        Appy::redirigeVers($url);
    }

    public function createNewQuiz(){
        $url = WEB_PATH . 'quiz.html';
        try {
            $quizType = $_POST['quizType'];
            $quizRepository = new QuizRepository();
            $newQuiz = new Quiz();
            $newQuiz->name = $_POST['quizName'];
            $newQuiz->type = $quizType;
            $newQuiz->identifier = \Appy\Src\Str::random(10); // Génération d'un identifiant unique
            $quizId = $quizRepository->createQuiz($newQuiz);
            $templateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
            $questions = $templateQuizQuestionsRepository->getQuestionsByType($quizType);
            $quizQuestionRepository = new QuizQuestionRepository();
            $quizQuestionRepository->createQuizQuestions($quizId, $questions);

            $templateQuizOptionsRepository = new TemplateQuizOptionsRepository();
            $quizOptions = $templateQuizOptionsRepository->getTemplateByQuizType($quizType);
            $quizRepository->insertQuizOptionsByQuizId($quizId, $quizOptions);

            if ($newQuiz->isTypeBarom()) {
                $quizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
                $quizCriteresBarometreRepository->createQuizCriteresBarometre($quizId);
                $quizRepository->initOptionsBarom();

                $quizReportBarometreRepository = new QuizReportBarometreRepository();
                $quizReportBarometreRepository->initReportBarometre($quizId);
            }
            Appy::redirigeVers($url);
        } catch (\Exception $e) {
            echo "<strong>Erreur :</strong> " . $e->getMessage();
        }
    }

    public function showPublishQuiz()
    {
        $EmailTemplateRepository = new TemplateEmailRepository();
        $templates = $EmailTemplateRepository->getAllEmailTemplates();
        $quizId = $_GET['quizId'];
        $urlCreate = WEB_PATH . 'quiz.html/SendQuiz';
        $urlSaveTemplateEmail = WEB_PATH . 'quiz.html/SaveTemplateEmail';
        $urlDeleteTemplateEmail = WEB_PATH . 'quiz.html/DeleteTemplateEmail';
        $urlFetchGroupInfo = WEB_PATH . 'quiz.html/fetchGroupInfo';
        $urlTemplatesEmails = WEB_PATH . 'quiz.html/templatesEmails';

        $quizRepository = new QuizRepository();
        $quiz = $quizRepository->getQuizById($quizId);
        $urlResponse = WEB_PATH . "quiz.html/ResponseQuiz?quizId=" . $quiz->identifier;

        $groupesRepository = new GroupesRepository();
        $groupes = $groupesRepository->getAllGroupes();
        $group = $groupesRepository->getGroupById($quiz->groupeId);

        if ($quiz->anonymous == 1) {
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'PublishAnonymousQuiz';
            self::showVue([
                'quiz' => $quiz,
            ]);
        } else {
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'PublishQuiz';
            self::showVue([
                'webPath' => WEB_PATH,
                'quiz' => $quiz,
                'groupes' => $groupes,
                'group' => $group,
                'urlCreate' => $urlCreate,
                'urlResponse' => $urlResponse,
                'urlFetchGroupInfo' => $urlFetchGroupInfo,
                'urlTemplatesEmails' => $urlTemplatesEmails,
                'quizId' => $quizId,
                'templates' => $templates,
                'urlSaveTemplateEmail' => $urlSaveTemplateEmail,
                'urlDeleteTemplateEmail' => $urlDeleteTemplateEmail,
            ]);
        }
    }

    public function createCourrier()
    {
        $quizId = $_GET['quizId'];

        $QuizQuestionRepository = new QuizQuestionRepository();
        $usersRepository = new UsersRepository();
        $quizRepository = new QuizRepository();
        $QuizUserRepository = new QuizUserRepository();

        $quiz = $quizRepository->getQuizById($quizId);

        //On recupère la liste de question du quiz
        $quizQuestions = $QuizQuestionRepository->getQuestionsByQuizId($quizId);

        //On prend tous les user mêmes ceux sans email car le questionnaire est anonyme
        //On veut le user avec l'email (autoevalué) en 1er
        $critereRechercheRepondant = [];
        $critereRechercheRepondant['groupe'] = $quiz->groupeId;
        $repondants = $usersRepository->getAllRepondants($critereRechercheRepondant, 'U.email DESC');
        foreach ($repondants as $repondant) {

            //On ne traite pas les répondant qui ont deja le quiz
            $quizUserExist = $QuizUserRepository->getQuizUserByIdentifiers($quiz->identifier, $repondant->identifier);

            if($quizUserExist == null) {

                $QuizUserRepository = new QuizUserRepository();
                //Si on traite l'autoevalué on met le boolean à 1
                $auto = 0;
                if ($quiz->autoUserEmail == $repondant->email) $auto = 1;

                //On fait l'insert que si pas deja existant
                $quizUserid = $QuizUserRepository->getQuizUserId($quiz->id, $repondant->id);
                if ($quizUserid == null) {
                    $QuizUserId = $QuizUserRepository->insertQuizUser($repondant, $quiz, $auto);


                    if ($quiz->isTypeBarom()){
                        $QuizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
                        $QuizCriteresBarometreId = $QuizCriteresBarometreRepository->getIdByQuizId($quiz->id);
                        $ResponseQuizCriteresBarometreRepository = new ResponseQuizCriteresBarometreRepository();
                        $ResponseQuizCriteresBarometreRepository->InitiateCriteresResponses($QuizCriteresBarometreId, $QuizUserId );
                    }

                    foreach ($quizQuestions as $question) {
                        $QuizUserResponseRepository = new QuizUserResponseRepository();
                        $value = '';
                        /*if ($question->ordre == 2) {
                            $fonctions = ['Hierarchie', 'Transverse', 'Equipe'];
                            $value = $fonctions[array_rand($fonctions, 1)];
                        } elseif ($question->questionType == 'INPUT-RADIO') {
                            $value = rand(0, 4);
                        }*/
                        $QuizUserResponseRepository->insertResponses($question->id, $QuizUserId, $value);
                    }
                }
            }
        }

        //On construit le document word de convocation
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
        $phpWord->addFontStyle('StyleTexte9Orange', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => "ff8000"]);
        $phpWord->addFontStyle('StyleTexteChapter', ['name' => 'Trebuchet MS', 'size' => 9, 'color' => 'E9660B', 'bold' => true, 'space' => array('before' => 10)]);
        $phpWord->addFontStyle('StyleTexte10', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte10Bold', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte10Underline', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252", 'underline' => 'single']);
        $phpWord->addFontStyle('StyleTexte10Coche', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte10RougeBold', ['name' => 'Trebuchet MS', 'size' => 10, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte11', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte11Underline', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'underline' => 'single']);
        $phpWord->addFontStyle('StyleTexte11Colle', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'space' => array('before' => 5, 'after' => 5)]);
        $phpWord->addFontStyle('StyleTexte11Rouge', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B']);
        $phpWord->addFontStyle('StyleTexte11RougeBold', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte11RougeMarginLeft', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => 'E9660B', 'indentation' => array('left' => 400, 'right' => 0)]);
        $phpWord->addFontStyle('StyleTexte11Colle10Before', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "000000", 'space' => array('before' => 10)]);
        $phpWord->addFontStyle('StyleTexte11Bold', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'bold' => true]);
        $phpWord->addFontStyle('StyleTexte11BoldUnderline', ['name' => 'Trebuchet MS', 'size' => 11, 'color' => "696252", 'bold' => true, 'underline' => 'single']);
        $phpWord->addFontStyle('StyleTexte12', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => "696252"]);
        $phpWord->addFontStyle('StyleTexte12RougeBold', ['name' => 'Trebuchet MS', 'size' => 12, 'color' => 'E9660B', 'bold' => true]);
        $phpWord->addTableStyle('StyleTableListe', ['borderSize' => 0, 'borderColor' => 'ffffff', 'cellMarginLeft' => 50, 'cellMarginRight' => 50, 'cellMarginTop' => 50, 'cellMarginBottom' => 0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0]);

        //si on met des  marges sur la section se sont les marges de la page
        $section = $phpWord->addSection(array('marginLeft' => 1200, 'marginRight' => 1000, 'marginTop' => 800, 'marginBottom' => 800));

        // Ajout du Header
        //Tableau du header avec le logo RM à gauche et eventuellement logo client à droite
        $header = $section->addHeader();
        $table = $header->addTable('StyleTableHeader');
        $table->addRow();
        $cell = $table->addCell(5000);
        $cell->addImage(BASE_PATH . "/assets/images/logo-rm-simple.png", array('height' => 40,'width' => 100, 'align' => 'left'));
        $cell = $table->addCell(5000);
        //On affiche le logo du client si existant
        if ($quiz->logo) {
            $cell->addImage(BASE_PATH . "assets/images/logosClients/" . $quiz->logo, array('height' => 50, 'align' => 'right'));
        }

        //Ajout du pied de page
        $footer = $section->addFooter();
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


        //On boucle sur chaque repondant pour ajouter une page de convocation
        $i = 1;
        foreach ($repondants as $repondant) {

            $this->addSautLigne($section, 1);

            $title = "";
            if($quiz->isTypeBarom()) {
                $title = "BAROMETRE – COURRIER INVITATION";
            } else {
                $title = "360 - COURRIER INVITATION";
                if ($quiz->autoUserEmail == $repondant->email) {
                    $title = $title . " (auto-évalué";
                    if($quiz->sexeAutoUser == "F") {
                        $title = $title . "e";
                    }
                    $title = $title . ")";
                }
            }

            $section->addText($title, 'StyleTexte12RougeBold', 'StyleParagrapheCenterAfterColle');
            $this->addSautLigne($section, 3);

            if($quiz->isTypeBarom()) {
                $section->addText("Bonjour,", 'StyleTexte10', 'StyleParagrapheText');
            } else {
                if ($quiz->autoUserEmail == $repondant->email) {
                    $nameAutoUser = "";
                    if ($quiz->autoUserFirstName) $nameAutoUser = $quiz->autoUserFirstName;
                    if ($quiz->autoUserLastName) $nameAutoUser .= " " . strtoupper($quiz->autoUserLastName);

                    $section->addText("Bonjour " . $nameAutoUser . ",", 'StyleTexte10', 'StyleParagrapheText');
                } else {
                    $section->addText("Bonjour,", 'StyleTexte10', 'StyleParagrapheText');
                }
            }

            $this->addSautLigne($section, 1);

            if($quiz->isTypeBarom()) {

                $section->addText("Vous allez répondre à un baromètre social qui vise à recueillir votre perception de vie au travail.", 'StyleTexte10', 'StyleParagrapheText');
                $section->addText("Ce baromètre se présente sous forme d’un questionnaire en huit parties, sur différents thèmes (votre travail au quotidien, vos relations avec les autres, la reconnaissance, la » récupération », …)", 'StyleTexte10', 'StyleParagrapheText');
                $section->addText("Votre participation, très importante, nous permettra d’identifier des leviers d’action pour améliorer la qualité de vie au travail et la performance de l’entreprise.", 'StyleTexte10', 'StyleParagrapheText');

                $this->addSautLigne($section, 1);

                $textRun = $section->addTextRun('StyleParagrapheText');
                $textRun->addText('Le questionnaire est disponible en ligne du ', 'StyleTexte10Bold');
                $startDate = new \DateTime($quiz->startDate);
                $textRun->addText(strftime('%A %d %B %Y', $startDate->getTimestamp()), 'StyleTexte10RougeBold');
                $textRun->addText(' au ', 'StyleTexte10Bold');
                $endDate = new \DateTime($quiz->endDate);
                $textRun->addText(strftime('%A %d %B %Y', $endDate->getTimestamp()), 'StyleTexte10RougeBold');
                $textRun->addText(' inclus.', 'StyleTexte10Bold');

                $this->addSautLigne($section, 1);

                $section->addText("Si possible, ne soyez pas dérangé(e) pendant que vous répondez aux questions. Vous avez besoin d’environ 20 minutes.", 'StyleTexte10', 'StyleParagrapheText');
                $section->addText("L’attribution aléatoire des codes d’authentification et la remise sous enveloppe, tout autant aléatoire, nous permet de garantir l’anonymat de vos réponses.", 'StyleTexte10', 'StyleParagrapheText');

                $this->addSautLigne($section, 1);

                $section->addText('Comment accéder au baromètre ?', 'StyleTexte10Underline', 'StyleParagrapheText');

                $urlResponse = "";
                if (\Appy\Src\Config::ENV == 'PROD') {
                    $urlResponse = htmlspecialchars(\Appy\Src\Config::DOMAIN . "/" . $quiz->identifier);
                } else {
                    $urlResponse = "http://" . $_SERVER['SERVER_NAME'] . "/relais-managers-services/" . $quiz->identifier;
                }
                $textRun = $section->addTextRun('StyleParagrapheText');
                $textRun->addText('Vous allez vous rendre à l’adresse suivante : ', 'StyleTexte10Bold');
                $textRun->addText($urlResponse, 'StyleTexte10RougeBold');
                $textRun = $section->addTextRun('StyleParagrapheText');
                $textRun->addText('Ensuite, vous saisissez le code d’identification : ', 'StyleTexte10Bold');
                $textRun->addText($repondant->identifier, 'StyleTexte10RougeBold');

                $this->addSautLigne($section, 1);

            } else {
                $section->addText("Nous vous remercions d'avoir donné votre accord pour répondre à ce questionnaire :", 'StyleTexte10', 'StyleParagrapheText');
                $section->addText($quiz->name, 'StyleTexte10RougeBold', 'StyleParagrapheText');
                $this->addSautLigne($section, 1);

                $section->addText("Pour le remplir, merci de vous rendre à l’adresse suivante : ", 'StyleTexte10', 'StyleParagrapheText');
                $urlResponse = "";
                if (\Appy\Src\Config::ENV == 'PROD') {
                    $urlResponse = htmlspecialchars(\Appy\Src\Config::DOMAIN . "/" . $quiz->identifier);
                } else {
                    $urlResponse = "http://" . $_SERVER['SERVER_NAME'] . "/relais-managers-services/" . $quiz->identifier;
                }
                $section->addText($urlResponse, 'StyleTexte10RougeBold', 'StyleParagrapheText');
                $this->addSautLigne($section, 2);

                $textRun = $section->addTextRun('StyleParagrapheText');
                $textRun->addText('Ensuite, vous saisissez le code d’identification : ', 'StyleTexte10');
                $textRun->addText($repondant->identifier, 'StyleTexte10RougeBold');
                $section->addText('Ce dernier vous est réservé, ne le transmettez pas.', 'StyleTexte10', 'StyleParagrapheText');
                $this->addSautLigne($section, 1);

                $textRun = $section->addTextRun('StyleParagrapheText');
                $textRun->addText('Le questionnaire est disponible en ligne jusqu’au ', 'StyleTexte10');
                $endDate = new \DateTime($quiz->endDate);
                $textRun->addText(strftime('%A %d %B %Y', $endDate->getTimestamp()), 'StyleTexte10RougeBold');
                $this->addSautLigne($section, 1);

                $section->addText('La confidentialité de vos réponses est assurée.', 'StyleTexte10', 'StyleParagrapheText');
                $this->addSautLigne($section, 1);
            }

            $section->addText('Vous pouvez joindre :', 'StyleTexte10Underline', 'StyleParagrapheText');
            $this->addSautLigne($section, 1);
            $table = $section->addTable('StyleTableListe');
            if($quiz->ccP1L1) {
                $row = $table->addRow();
                $cell = $table->addCell(300);
                $cell = $table->addCell(400);
                $cell->addText(' -   ', 'StyleTexte10');
                $cell = $table->addCell(9300);
                $cell->addText($quiz->ccP1L1, 'StyleTexte10');
            }
            if($quiz->ccP1L2) {
                $row = $table->addRow();
                $cell = $table->addCell(300);
                $cell = $table->addCell(400);
                $cell->addText(' -   ', 'StyleTexte10');
                $cell = $table->addCell(9300);
                $cell->addText($quiz->ccP1L2, 'StyleTexte10');
            }
            if($quiz->ccP1L3) {
                $row = $table->addRow();
                $cell = $table->addCell(300);
                $cell = $table->addCell(400);
                $cell->addText(' -   ', 'StyleTexte10');
                $cell = $table->addCell(9300);
                $cell->addText($quiz->ccP1L3, 'StyleTexte10');
            }

            $this->addSautLigne($section, 2);

            if($quiz->isTypeBarom()) {
                $section->addText('Nous vous remercions de votre participation,', 'StyleTexte10', 'StyleParagrapheText');
                $section->addText('Cordialement', 'StyleTexte10', 'StyleParagrapheText');
            } else {
                $section->addText('Cordialement', 'StyleTexte10', 'StyleParagrapheText');
            }
            $section->addText('Relais Managers', 'StyleTexte10', 'StyleParagrapheText');



            //On n'ajoute un saut de page sauf pour la dernière invitation
            if($i != count($repondants)) {
                $section->addPageBreak();
            }

            $i++;
        }

        $DocxResultName = "INVITATIONS_QUESTIONNAIRE_" . date("d-m-Y-H-i-s") . ".docx";
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

    public function SendQuizEmails()
    {
        $quizRepository = new QuizRepository();
        $QuizQuestionRepository = new QuizQuestionRepository();
        $usersRepository = new UsersRepository();
        $QuizUserRepository = new QuizUserRepository();

        $subject = $_POST['objet'];
        $message = $_POST['message'];
        $quizId = $_POST['idQuiz'];

        if (isset($_GET['saveEmail'])) {
            if (isset($_POST['template-message-id'])) {
                $templateMessageId = $_POST['template-message-id'];
                $templateEmailRepository = new TemplateEmailRepository();
                $templateEmailRepository->UpdateTemplate($templateMessageId, $message);
            }
        }

        $quiz = $quizRepository->getQuizById($quizId);
        $quizQuestions = $QuizQuestionRepository->getQuestionsRequiredResponseByQuizId($quizId);

        //Pour le PRCC on recupere soit le group soit le repondant par rapport au autouseremail
        //Pour le 360 et le BARO on recupère la liste des répondant du groupe
        $repondants = null;
        if ($quiz->isTypePRCC()) {
            if($quiz->autoUserIdentifier) {
                $critereRecherche = [];
                $critereRecherche['identifier'] = $quiz->autoUserIdentifier;
                $repondants = $usersRepository->getAllRepondants($critereRecherche);
            } else {
                $repondants = $usersRepository->getRespondantsWithEmailByGroupeId($quiz->groupeId);
            }
        } else {
            $groupeId = $_POST['groupe'];
            $repondants = $usersRepository->getRespondantsWithEmailByGroupeId($groupeId);
        }

        $nbEmail = 0;
        $demoLinks = []; // Pour collecter les liens en mode démo
        
        foreach ($repondants as $repondant) {

            //On ne traite pas les répondant quoi ont deja le quiz
            $quizUserExist = $QuizUserRepository->getQuizUserByIdentifiers($quiz->identifier, $repondant->identifier);

            if($quizUserExist == null) {
                $urlResponse = "";
                if(\Appy\Src\Config::ENV == 'PROD') {
                    $urlResponse = htmlspecialchars(\Appy\Src\Config::DOMAIN . "/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&identifier=" . $repondant->identifier);
                } else {
                    $urlResponse = "http://localhost/relais-managers-services/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&identifier=" . $repondant->identifier;
                }

                $messageMail = $this->replaceVariables($message, $repondant, $quiz, $urlResponse);

                $auto = 0;
                if($quiz->autoUserEmail == $repondant->email) $auto = 1;
                $QuizUserId = $QuizUserRepository->insertQuizUser($repondant, $quiz, $auto);
                if ($quiz->isTypeBarom()){
                    $QuizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
                    $QuizCriteresBarometreId = $QuizCriteresBarometreRepository->getIdByQuizId($quiz->id);
                    $ResponseQuizCriteresBarometreRepository = new ResponseQuizCriteresBarometreRepository();
                    $ResponseQuizCriteresBarometreRepository->InitiateCriteresResponses($QuizCriteresBarometreId, $QuizUserId );
                }

                foreach ($quizQuestions as $question) {
                    $value = NULL;
                    $QuizUserResponseRepository = new QuizUserResponseRepository();

                    //AUTOCOMPLETE POUR TEST
                    /*
                    if($quiz->isType360()) {
                        if ($question->questionType == 'INPUT-RADIO') {
                            if ($question->ordre == 2) {
                                if($auto == 0) {
                                    $fonctions = ['Hierarchie', 'Transverse', 'Equipe'];
                                    $value = $fonctions[array_rand($fonctions, 1)];
                                }
                            } else {
                                $value = rand(0, 4);
                            }
                        }
                    }
                    */

                    $QuizUserResponseRepository->insertResponses($question->id, $QuizUserId, $value);
                }

                // Mode démo: collecter les liens pour affichage groupé
                if (!\Appy\Src\Config::mailActive()) {
                    $demoLinks[] = [
                        'email' => $repondant->email,
                        'name' => $repondant->firstname . ' ' . $repondant->lastname,
                        'link' => $urlResponse
                    ];
                    
                    // Debug: afficher chaque lien généré
                    if (\Appy\Src\Config::DEBUG) {
                        error_log("DEBUG DEMO - Lien généré pour {$repondant->email}: {$urlResponse}");
                    }
                    
                    // Debug: var_dump pour débogage visuel (à supprimer en production)
                    if (\Appy\Src\Config::DEBUG) {
                        echo "<pre>DEBUG - Lien généré pour {$repondant->email}: {$urlResponse}</pre>";
                    }
                } else {
                    \Appy\Src\Email::setDestinataires(array(array("$repondant->email", $repondant->lastname)));
                    \Appy\Src\Email::setSubject($subject);
                    \Appy\Src\Email::setHtml($messageMail);
                    
                    // Débogage de l'envoi d'email
                    if (\Appy\Src\Config::DEBUG) {
                        error_log("DEBUG EMAIL - Tentative d'envoi vers: " . $repondant->email);
                        error_log("DEBUG EMAIL - MAIL_ACTIVE: " . (\Appy\Src\Config::mailActive() ? 'true' : 'false'));
                    }
                    
                    $result = \Appy\Src\Email::envoi();
                    
                    if (\Appy\Src\Config::DEBUG) {
                        error_log("DEBUG EMAIL - Résultat envoi: " . ($result ? 'succès' : 'échec'));
                    }

                    $nbEmail++;
                }
            }

        }
        
        // Mode démo: afficher tous les liens générés
        if (!\Appy\Src\Config::mailActive() && !empty($demoLinks)) {
            $session = \Appy\Src\Core\Session::getInstance();
            $linksText = "Mode démo : les mails ne sont pas activés. Liens générés :<br>";
            foreach ($demoLinks as $link) {
                $linksText .= "• {$link['name']} ({$link['email']}): <a href='{$link['link']}' target='_blank'>{$link['link']}</a><br>";
            }
            $session->setFlash("info", $linksText);
            
            // Debug: var_dump de tous les liens collectés (à supprimer en production)
            if (\Appy\Src\Config::DEBUG) {
                echo "<pre>DEBUG - Tous les liens collectés :</pre>";
                var_dump($demoLinks);
            }
        }

        // Afficher les messages de succès seulement si les emails sont vraiment envoyés
        if (\Appy\Src\Config::mailActive()) {
            $session = \Appy\Src\Core\Session::getInstance();
            if($nbEmail == 0) {
                $session->setFlash("success", "Aucun email d'invitation envoyé. Le ou les répondants ayant déjà reçu l'invitation");
            } elseif($nbEmail == 1) {
                $session->setFlash("success", " 1 email d'invitation a été envoyé avec succès");
            } else {
                $session->setFlash("success", $nbEmail . " emails d'invitation ont été envoyés avec succès");
            }
        }


        Appy::redirigeVers(WEB_PATH . 'quiz.html/SuiviQuiz?quizId=' . $quizId);
    }

    private function replaceVariables($message, $user, $quiz, $urlResponse)
    {
        $startDate = date('d-m-Y', strtotime($quiz->startDate));
        $endDate = date('d-m-Y', strtotime($quiz->endDate));

        $message = str_replace('[IDENTIFIANT]', $user->identifier, $message);
        $message = str_replace('[NOM]', $user->lastname, $message);
        $message = str_replace('[PRENOM]', $user->firstname, $message);
        $message = str_replace('[URL]', $urlResponse, $message);
        $message = str_replace('[TITRE]', $quiz->name, $message);
        $message = str_replace('[DATE_DEBUT]', $startDate, $message);
        $message = str_replace('[DATE_FIN]', $endDate, $message);

        $message = str_replace('a href', 'a style="color:#E9660B" href', $message);

        return $message;
    }

    public function loginAnonymous($quizIdentifier)
    {
        $quizRepository = new QuizRepository();

        $critereRecherche = [];
        $critereRecherche['quizIdentifier'] = $quizIdentifier;
        $quizzes = $quizRepository->getQuizzes($critereRecherche);
        if($quizzes) {
            $quiz = $quizzes[0];

            $urlAction = WEB_PATH . 'quiz.html/checkLoginAnonymous';

            self::$gabarit = 'gabaritAjax';
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'LoginAnonymous';
            self::showVue([
                'quiz' => $quiz,
                'urlAction' => $urlAction,
            ]);
        }  else {
            //On redirige vers une page d'erreur
            self::$gabarit = 'gabaritAjax';
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'AdresseQuestionnaireInvalides';
            self::showVue([]);
        }

    }

    public function checkLoginAnonymous()
    {
        $quizRepository = new QuizRepository();
        $quizQuestionRepository = new QuizQuestionRepository();
        $quizUserRepository = new QuizUserRepository();
        $quizIdentifier = $_POST['quizIdentifier'] ?? null;
        $userIdentifier = $_POST['identifier'] ?? null;
        $critereRecherche = [];
        $critereRecherche['quizIdentifier'] = $quizIdentifier;
        $quizzes = $quizRepository->getQuizzes($critereRecherche);
        if($quizzes) {
            $quiz = $quizzes[0];
            $quizUser = $quizUserRepository->getQuizUserByIdentifiers($quizIdentifier, $userIdentifier);
            if (!$quizUser) {
                $urlAction = WEB_PATH . 'quiz.html/checkLoginAnonymous';

                self::$gabarit = 'gabaritAjax';
                self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'LoginAnonymous';
                self::showVue([
                    'quiz' => $quiz,
                    'urlAction' => $urlAction,
                    'error' => "L'identifiant est incorrect !",
                ]);
            } else {
                $urlResponseQuiz = WEB_PATH . 'quiz.html/ResponseQuiz?quizId=' . $quizIdentifier . '&identifier=' . $userIdentifier;
                Appy::redirigeVers($urlResponseQuiz);
                return;
            }

        }  else {
            //On redirige vers une page d'erreur
        }


    }

    public function respondToQuiz()
    {
        $quizRepository = new QuizRepository();
        $quizQuestionRepository = new QuizQuestionRepository();
        $quizUserRepository = new QuizUserRepository();
        $quizIdentifier = $_GET['quizId'] ?? null;
        $userIdentifier = $_GET['identifier'] ?? null;
        $modeTest = $_GET['modeTest'] ?? 0;  // 1 mode test repondant - 2 mode test autoevalué
        $urlNotFound = WEB_PATH . 'quiz.html/notFound';
        $urlFinish = WEB_PATH . 'quiz.html/finish';

        if (!$quizIdentifier || !$userIdentifier) {
            if($modeTest == 0) {
                Appy::redirigeVers($urlNotFound);
                return;
            }
        }

        if($modeTest ==0) {
            $quizUser = $quizUserRepository->getQuizUserByIdentifiers($quizIdentifier, $userIdentifier);
        } else {
            $quizUser = new QuizUser();
            $quizUser->id = 1;
            $quizUser->userId = 1;
            $quizUser->userFirstName = "Test";
            $quizUser->userLastName = "Test";
            $quizUser->userIdentifier = "AAAAAAAAAA";
            $quizUser->userEmail = "";
            $quizUser->quizId = $quizIdentifier;
            if($modeTest == 1) {
                $quizUser->auto = 0;
            } else {
                $quizUser->auto = 1;
            }
            $quizUser->status = "TODO";
        }

        if (!$quizUser) {
            Appy::redirigeVers($urlNotFound);
            return;
        }

        if ($quizUser->status == 'FINISH'){
            Appy::redirigeVers($urlFinish);
            return;
        }
        $critereRecherche = ['quizIdentifier' => $quizIdentifier];
        $quizzes = $quizRepository->getQuizzes($critereRecherche);

        if ($modeTest ==0 && empty($quizzes)) {
            Appy::redirigeVers($urlNotFound);
            return;
        }
        $quiz = $quizzes[0];
        $currentDate = new \DateTime();
        $currentDateFormatted = $currentDate->format('Y-m-d');
        $startDate = $quiz->startDate;
        $endDate = $quiz->endDate;
        if ($modeTest == 0)  {
            if( $currentDateFormatted < $startDate || $currentDateFormatted > $endDate) {
                $url = WEB_PATH . 'quiz.html/HorsDates?quizIdentifier=' . $quiz->identifier;
                Appy::redirigeVers($url);
                return;
            }
        }

        //si le quiz est supprimé
        if ($quiz->deleted) {
            Appy::redirigeVers($urlNotFound);
            return;
        }

        $quiz->intro = str_replace("<br>","",$quiz->intro);
        $quiz->intro = str_replace("\r","",$quiz->intro);
        $quiz->intro = str_replace("\n","",$quiz->intro);
        $quiz->conclusion = str_replace("<br>","",$quiz->conclusion);
        $quiz->conclusion = str_replace("\r","",$quiz->conclusion);
        $quiz->conclusion = str_replace("\n","",$quiz->conclusion);
        $questions = $quizQuestionRepository->getQuestionsAndResponseByQuizId($quiz->id, $quizUser->id);
        if ($quiz->isTypeBarom()) {
            $quizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
            $responseQuizCriteresBarometreRepository = new ResponseQuizCriteresBarometreRepository();
            $quizCriteresBarometreId = $quizCriteresBarometreRepository->getIdByQuizId($quiz->id);
            $responseQuizCriteresBarometre = $responseQuizCriteresBarometreRepository->getCriteresResponses($quizUser->id, $quizCriteresBarometreId);

            $filteredQuestions = [];
            $currentChapterLabel = '';

            foreach ($questions as $question) {
                if ($question->questionType == 'CHAPTER' && trim(strip_tags($question->label)) == '') {
                    continue;
                }

                if ($question->questionType == 'CHAPTER') {
                    $currentChapterLabel = $question->label;
                    $filteredQuestions[] = $question;
                } elseif ($question->questionType != 'CHAPTER' && $currentChapterLabel != '' && trim(strip_tags($question->label)) != '') {
                    $filteredQuestions[] = $question;
                }
            }
            $questions = $filteredQuestions;
        }

        $arrayQuestionAndNewQuestion = [];
        $arrayquestions = array_reverse($questions, true);
        $questionId = 0;
        foreach ($arrayquestions as $index => $question) {
            if ($questionId != 0) {
                $arrayQuestionAndNewQuestion[$question->id] = $questionId;
            }
            $questionId = $question->id;
        }
        $arrayQuestionAndNextQuestion = array_reverse($arrayQuestionAndNewQuestion, true);
        $urlRemerciement = WEB_PATH . 'quiz.html/remerciement?quizId=' . $quizIdentifier . '&identifier=' . $userIdentifier;
        $urlSaveResponse = WEB_PATH . 'quiz.html/saveResponse';

        self::$gabarit = 'gabaritRepondant';
        if ($quiz->isTypeBarom()) {
            $urlSaveCritere = WEB_PATH . 'quiz.html/saveCritere';
            $QuizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
            $criteres = $QuizCriteresBarometreRepository->getCriteresByQuizId($quiz->id);
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'ResponseBarometre';
            self::showVue([
                'quiz' => $quiz,
                'questions' => $questions,
                'quizUser' => $quizUser,
                'modeTest' => $modeTest,
                'urlSaveResponse' => $urlSaveResponse,
                'urlRemerciement' => $urlRemerciement,
                'arrayQuestionAndNextQuestion' => $arrayQuestionAndNextQuestion,
                'criteres' => $criteres,
                'urlSaveCritere' => $urlSaveCritere,
                'responseQuizCriteresBarometre' => $responseQuizCriteresBarometre
            ]);
        } elseif($quiz->type == 'PRCC'){
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'ResponsePrcc';
            self::showVue([
                'quiz' => $quiz,
                'questions' => $questions,
                'quizUser' => $quizUser,
                'modeTest' => $modeTest,
                'urlSaveResponse' => $urlSaveResponse,
                'urlRemerciement' => $urlRemerciement,
                'arrayQuestionAndNextQuestion' => $arrayQuestionAndNextQuestion
            ]);
        }else {
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'ResponseQuiz';
            self::showVue([
                'quiz' => $quiz,
                'questions' => $questions,
                'quizUser' => $quizUser,
                'modeTest' => $modeTest,
                'urlSaveResponse' => $urlSaveResponse,
                'urlRemerciement' => $urlRemerciement,
                'arrayQuestionAndNextQuestion' => $arrayQuestionAndNextQuestion,
            ]);
        }
    }


    public function HorsDates(){

        $quizIdentifier = $_GET['quizIdentifier'];

        $quizRepository = new QuizRepository();
        $critereRecherche = [];
        $critereRecherche['quizIdentifier'] = $quizIdentifier;
        $quizzes = $quizRepository->getQuizzes($critereRecherche);
        $quiz = $quizzes[0];
        $startDateFormatted = (new \DateTime($quiz->startDate))->format('d/m/Y');
        $endDateFormatted = (new \DateTime($quiz->endDate))->format('d/m/Y');
        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'HorsDates';
        self::showVue([
            'startDate' => $startDateFormatted,
            'endDate' => $endDateFormatted
        ]);
    }

    public function saveResponse()
    {
        $questionId = $_GET['question_id'];
        $response = $_GET['response'];
        $quizUserId= $_GET['quizUserId'];
        $quizRepository = new QuizRepository();
        try {
            $quizRepository->updateResponse($questionId, $response, $quizUserId);
            $quizRepository->updateUserStatus($quizUserId);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

        exit;
    }

    public function saveCritere(){
        $numeroCritere = $_GET['numeroCritere'];
        $response = $_GET['response'];
        $quizUserId= $_GET['quizUserId'];
        $quizCriteresBarometreId = $_GET['quizCriteresBarometreId'];
        $ResponseQuizCriteresBarometreRepository = new ResponseQuizCriteresBarometreRepository();
        $ResponseQuizCriteresBarometreRepository->updateCriteresResponses($numeroCritere, $quizUserId, $response, $quizCriteresBarometreId);
        exit;


    }
    public function quizOptions() {
        $url = WEB_PATH . 'quiz.html';
        $quizId = $_GET['quizId'];
        $urlSubmitQuizOptions = WEB_PATH . 'quiz.html/SubmitQuizOptions';
        $urlSubmitBarometreOptions = WEB_PATH . 'quiz.html/SubmitBarometreOptions';
        $urlSubmitPrccOptions = WEB_PATH . 'quiz.html/SubmitPrccOptions';
        $urlFetchRespondants = WEB_PATH . 'quiz.html/fetchRespondants?quizId=' . $quizId;
        $urlFetchUsers = WEB_PATH . 'quiz.html/fetchUsers?quizId=' . $quizId;
        $quizRepository = new QuizRepository();
        $quiz = $quizRepository->GetQuizById($quizId);
        $groupeRepository = new GroupesRepository();
        $groupes = $groupeRepository->getAllGroupes();
        $quizUserRepository = new QuizUserRepository();
        $quizUserExists = $quizUserRepository->checkIfQuizHasUsers($quizId);
        if ($quiz->isTypeBarom()) {
            $QuizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
            $criteres = $QuizCriteresBarometreRepository->getCriteresByQuizId($quizId);
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'BarometreOptions';
            self::showVue([
                'quiz' => $quiz,
                'url' => $url,
                'urlSubmitBarometreOptions' => $urlSubmitBarometreOptions,
                'criteres' => $criteres,
                'groupes' => $groupes,
                'quizUserExists' => $quizUserExists
            ]);

        } elseif ($quiz->type == "PRCC") {
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'PrccOptions';
            self::showVue([
                'quiz' => $quiz,
                'url' => $url,
                'urlSubmitPrccOptions' => $urlSubmitPrccOptions,
                'urlFetchUsers' => $urlFetchUsers,
                'groupes' => $groupes,
                'quizUserExists' => $quizUserExists
            ]);
        } else {
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'QuizOptions';
            self::showVue([
                'urlSubmitQuizOptions' => $urlSubmitQuizOptions,
                'quizId' => $quizId,
                'groupes' => $groupes,
                'urlFetchRespondants' => $urlFetchRespondants,
                'quiz' => $quiz,
                'url' => $url,
                'quizUserExists' => $quizUserExists
            ]);
        }
    }
    public function quizReport()
    {
        $quizId = $_GET['quizId'];
        $quizRepository = new QuizRepository();
        $quiz = $quizRepository->GetQuizById($quizId);
        $urlSubmitBaromReport = WEB_PATH . 'report.html/SubmitBaromReport';
        if ($quiz->isTypeBarom()) {


            //Recueration des chapitres
            $quizQuestionRepository = new QuizQuestionRepository();
            $quizChapters = $quizQuestionRepository->getChapterBarometre($quiz->id);
            $ChaptersInfo = [];
            $chapterNumber = 1;
            foreach ($quizChapters as $quizChapter) {
                //On ne prend par les chapitres vides
                if (trim($quizChapter->label) != '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold"></div>') {
                    //On diminue la size
                    $quizChapterLabel = str_replace('19px','14px',$quizChapter->label);
                    $ChaptersInfo[$chapterNumber]['label'] = $quizChapterLabel;
                    $ChaptersInfo[$chapterNumber]['number'] = $chapterNumber;
                    $chapterNumber = $chapterNumber + 1;
                }
            }

            //Recuperation des critère
            $quizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
            $quizCriteresBarometre = $quizCriteresBarometreRepository->getCriteresByQuizId($quiz->id);

            //recuperation des paramétrage de visu des reports
            $quizReportBarometreRepository = new QuizReportBarometreRepository();
            $quizReportBarometre = $quizReportBarometreRepository->getReportBarometreByQuizId($quizId);

            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . "BaromReport";
            self::showVue([
                'quiz' => $quiz,
                'ChaptersInfo' => $ChaptersInfo,
                'quizCriteresBarometre' => $quizCriteresBarometre,
                'quizReportBarometre' => $quizReportBarometre,
                'urlSubmitBaromReport' => $urlSubmitBaromReport
            ]);
        } else if ($quiz->isTypePRCC()) {
            $quizUserRepository = new QuizUserRepository();
            $quizUsers = $quizUserRepository->getQuizUsersByQuizId($quiz->id);
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . "ReportPRCC";
            self::showVue([
                'quiz' => $quiz,
                'quizUsers' => $quizUsers
            ]);
        } else {
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . "QuizReport";
            self::showVue([
                'quiz' => $quiz
            ]);
        }
    }
    public function suiviQuiz()
    {
        $quizId = $_GET['quizId'];
        $quizRepository = new QuizRepository();
        $quizUserRepository = new QuizUserRepository();
        $quizQuestionRepository = new QuizQuestionRepository();
        $quizUserResponseRepository = new QuizUserResponseRepository();
        $quizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
        $responseQuizCriteresBarometreRepository = new ResponseQuizCriteresBarometreRepository();
        $quiz = $quizRepository->GetQuizById($quizId);
        $quizUsers = $quizUserRepository->getQuizUsersByQuizId($quizId);
        $NbQuestions = $quizQuestionRepository->getNbQuestionsByQuizId($quizId);
        $nbrQuizUsers = count($quizUsers);
        $nbrQuizUsersFinish = 0;
        $quizUsersData = [];

        if ($quiz->isTypeBarom()) {
            $NbCriteres = $quizCriteresBarometreRepository->getNbCriteresByQuizId($quizId);
            $NbQuestions = $NbQuestions + $NbCriteres;
        }
        foreach ($quizUsers as $quizUser) {
            $NbReponses = $quizUserResponseRepository->getNbResponsesByUser($quiz, $quizUser->id);

            if($quiz->isTypeBarom()) {
                $responseQuizCriteresBarometre = $responseQuizCriteresBarometreRepository->getCriteresResponse($quizUser->id);
                if($responseQuizCriteresBarometre->responseCritere1 != "") $NbReponses++;
                if($responseQuizCriteresBarometre->responseCritere2 != "") $NbReponses++;
                if($responseQuizCriteresBarometre->responseCritere3 != "") $NbReponses++;
                if($responseQuizCriteresBarometre->responseCritere4 != "") $NbReponses++;
            }

            if ($quizUser->status === 'FINISH') {
                $nbrQuizUsersFinish++;
            }

            //Pour le 360 l'autoevalué on a une question en moins (la question sur le lien hiérarchique)
            if($quiz->isType360() && $quizUser->auto == 1) {
                $quizUsersData[] = [
                    'user' => $quizUser,
                    'nbQuestions' => $NbQuestions - 1,
                    'nbResponses' => $NbReponses,
                ];
            } else {
                $quizUsersData[] = [
                    'user' => $quizUser,
                    'nbQuestions' => $NbQuestions,
                    'nbResponses' => $NbReponses,
                ];
            }
        }
        $tauxReponses = ($nbrQuizUsers > 0) ? round(($nbrQuizUsersFinish / $nbrQuizUsers) * 100, 2) : 0;
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'QuizSuivi';
        self::showVue([
            'quiz' => $quiz,
            'quizUsersData' => $quizUsersData,
            'nbrQuizUsers' => $nbrQuizUsers,
            'nbrQuizUsersFinish' => $nbrQuizUsersFinish,
            'tauxReponses' => $tauxReponses
        ]);
    }


    public function submitQuizOptions() {
        $url = WEB_PATH . 'quiz.html';
        $quizId = $_POST['quizId'];
        $anonymousBroadcast = isset($_POST['anonymousBroadcast']) ? 1 : 0;
        $autoUserId = $_POST['respondant_id'];
        $UserRepository = new UsersRepository();
        $userauto = $UserRepository->getUserById($autoUserId);
        $quizRepository = new QuizRepository();
        $existingQuiz = $quizRepository->GetQuizById($quizId);
        $logoPath = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['logo']['tmp_name'];
            $fileName = $_FILES['logo']['name'];
            $fileSize = $_FILES['logo']['size'];
            $fileType = $_FILES['logo']['type'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if (in_array($fileType, $allowedTypes) && $fileSize < 5000000) {
                $guid = bin2hex(random_bytes(16));
                $newFileName = $guid . '.' . $ext;
                $uploadFileDir = ($_SERVER['DOCUMENT_ROOT'] .
                    (\Appy\Src\Config::ENV == 'PROD' ? '/assets/images/logosClients/' : '/relais-managers-services/assets/images/logosClients/'));
                
                // S'assurer que le dossier existe
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                
                $dest_path = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $logoPath = $newFileName;
                } else {
                    echo 'Erreur lors de l\'upload du logo.';
                }
            } else {
                echo 'Fichier invalide ou trop volumineux.';
            }
        } else {
            $logoPath = $existingQuiz->logo ?? '';
        }

        $quiz = new Quiz();
        $quiz->id = $quizId;
        $quiz->name = $_POST['title'] ?? '';
        $quiz->startDate = $_POST['start_date'] ?? '';
        $quiz->endDate = $_POST['end_date'] ?? '';
        $quiz->reminderDate = $_POST['reminder_date'] ?? '';
        $quiz->colorForm = $_POST['color_form'] ?? '';
        $quiz->groupeId = $_POST['groupe_id'] ?? '';
        $quiz->autoUserId = $autoUserId;
        $quiz->autoUserLastName = $userauto->lastname ?? '';
        $quiz->autoUserFirstName = $userauto->firstname ?? '';
        $quiz->autoUserIdentifier = $userauto->identifier ?? '';
        $quiz->autoUserEmail = $userauto->email ?? '';
        $quiz->sexeAutoUser = $_POST['sexe_auto_user'] ?? '';
        $quiz->fonctionAutoUser = $_POST['fonction_auto_user'] ?? '';
        $quiz->header = $_POST['header'] ?? '';
        $quiz->intro = $_POST['intro'] ?? '';
        $quiz->conclusion = $_POST['conclusion'] ?? '';
        $quiz->footer = $_POST['footer'] ?? '';
        $quiz->ccP1L1 = $_POST['paragraph1_line1'] ?? '';
        $quiz->ccP1L2 = $_POST['paragraph1_line2'] ?? '';
        $quiz->ccP1L3 = $_POST['paragraph1_line3'] ?? '';
        $quiz->ccP1L4 = $_POST['paragraph1_line4'] ?? '';
        $quiz->ccP1L5 = $_POST['paragraph1_line5'] ?? '';
        $quiz->ccP2L1 = $_POST['paragraph2_line1'] ?? '';
        $quiz->ccP2L2 = $_POST['paragraph2_line2'] ?? '';
        $quiz->ccP2L3 = $_POST['paragraph2_line3'] ?? '';
        $quiz->ccP2L4 = $_POST['paragraph2_line4'] ?? '';
        $quiz->ccP2L5 = $_POST['paragraph2_line5'] ?? '';
        $quiz->ccP3L1 = $_POST['paragraph3_line1'] ?? '';
        $quiz->ccP3L2 = $_POST['paragraph3_line2'] ?? '';
        $quiz->ccP3L3 = $_POST['paragraph3_line3'] ?? '';
        $quiz->ccP3L4 = $_POST['paragraph3_line4'] ?? '';
        $quiz->ccP3L5 = $_POST['paragraph3_line5'] ?? '';
        $quiz->logo = $logoPath;
        $quiz->anonymous = $anonymousBroadcast;
        $quizRepository->UpdateOptionsQuiz($quiz);
        $session = \Appy\Src\Core\Session::getInstance();
        $session->setFlash("success", "Modifications effectuées");
        Appy::redirigeVers($url);
    }

    public function submitBarometreOptions() {
        $url = WEB_PATH . 'quiz.html';
        $quizId = $_POST['quizId'];
        $barometre = new Quiz();
        $barometre->id = $quizId;
        $barometre->name = $_POST['barometreTitle'] ?? '';
        $barometre->startDate = $_POST['barometre_start_date'] ?? '';
        $barometre->endDate = $_POST['barometre_end_date'] ?? '';
        $barometre->reminderDate = $_POST['barometre_reminder_date'] ?? '';
        $barometre->colorForm = $_POST['barometre_color_form'] ?? '';
        $barometre->anonymous = isset($_POST['barometreAnonymousBroadcast']) ? 1 : 0;
        $barometre->groupeId = $_POST['groupe_id'] ?? '';
        $barometre->header = $_POST['header'] ?? '';
        $barometre->intro = $_POST['intro'] ?? '';
        $barometre->conclusion = $_POST['conclusion'] ?? '';
        $barometre->footer = $_POST['footer'] ?? '';
        $barometre->ccP1L1 = $_POST['paragraph1_line1'] ?? '';
        $barometre->ccP1L2 = $_POST['paragraph1_line2'] ?? '';
        $barometre->ccP1L3 = $_POST['paragraph1_line3'] ?? '';
        $barometre->ccP1L4 = $_POST['paragraph1_line4'] ?? '';
        $barometre->ccP1L5 = $_POST['paragraph1_line5'] ?? '';
        $barometre->ccP2L1 = $_POST['paragraph2_line1'] ?? '';
        $barometre->ccP2L2 = $_POST['paragraph2_line2'] ?? '';
        $barometre->ccP2L3 = $_POST['paragraph2_line3'] ?? '';
        $barometre->ccP2L4 = $_POST['paragraph2_line4'] ?? '';
        $barometre->ccP2L5 = $_POST['paragraph2_line5'] ?? '';
        $barometre->ccP3L1 = $_POST['paragraph3_line1'] ?? '';
        $barometre->ccP3L2 = $_POST['paragraph3_line2'] ?? '';
        $barometre->ccP3L3 = $_POST['paragraph3_line3'] ?? '';
        $barometre->ccP3L4 = $_POST['paragraph3_line4'] ?? '';
        $barometre->ccP3L5 = $_POST['paragraph3_line5'] ?? '';
        $logoPath = null;
        if (isset($_FILES['barometreLogo']) && $_FILES['barometreLogo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['barometreLogo']['tmp_name'];
            $fileName = $_FILES['barometreLogo']['name'];
            $fileSize = $_FILES['barometreLogo']['size'];
            $fileType = $_FILES['barometreLogo']['type'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if (in_array($fileType, $allowedTypes) && $fileSize < 5000000) {
                $guid = bin2hex(random_bytes(16));
                $newFileName = $guid . '.' . $ext;

                if (\Appy\Src\Config::ENV == 'PROD') {
                    $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/logosClients/';
                } else {
                    $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/relais-managers-services/assets/images/logosClients/';
                }

                // S'assurer que le dossier existe
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $logoPath = $newFileName;
                } else {
                    echo 'Erreur lors de l\'upload du logo.';
                }
            } else {
                echo 'Fichier invalide ou trop volumineux.';
            }
        }
        if ($logoPath !== null) {
            $barometre->logo = $logoPath;
        }
        $criteres = [];
        for ($i = 1; $i <= 4; $i++) {
            $critere = new QuizCriteresBarometre();
            $critere->id = $i;
            $critere->titre = $_POST["critere{$i}_titre"] ?? '';
            for ($j = 1; $j <= 10; $j++) {
                $property = "choix{$j}";
                $critere->$property = $_POST["critere{$i}_choix{$j}"] ?? '';
            }
            $criteres[] = $critere;
        }
        $QuizCriteresBarometreRepository = new QuizCriteresBarometreRepository();
        $QuizCriteresBarometreRepository->updateQuizCriteresBarometre($quizId, $criteres);
        $quizRepository = new QuizRepository();
        $quizRepository->updateOptionsQuiz($barometre);
        Appy::redirigeVers($url);
    }


    public function fetchRespondants()
    {
        if (isset($_GET['groupeId'])) {
            $groupeId = $_GET['groupeId'];
            $quizId = $_GET['quizId'];
            $quizRepository = new QuizRepository();
            $quiz = $quizRepository->GetQuizById($quizId);
            $userRepository = new UsersRepository();
            $respondants = $userRepository->getRespondantsByGroupeId($groupeId);
            self::$gabarit = 'gabaritAjax';
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'SelectRespondant';
            self::showVue([
                'respondants' => $respondants,
                'quiz' => $quiz,
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function fetchGroupInfo(){
        if (isset($_GET['groupeId'])) {
            $groupeId = $_GET['groupeId'];
            $quizId = $_GET['quizId'];
            $urlFetchPopupEmailsDetails = WEB_PATH . 'quiz.html/PopupEmailsDetails?QuizId=' . $quizId . '&GroupeId=' . $groupeId; ;
            $userRepository = new UsersRepository();
            $users = $userRepository->getRespondantsWithEmailByGroupeId($groupeId);
            $users_nombres = count($users);
            $usersAlreadyReceivedEmail = $userRepository->getUsersAlreadyReceivedEmail($quizId,$users);
            $nbrUsersAlreadyReceivedEmail = count($usersAlreadyReceivedEmail);
            $totalEmailsToSend = $users_nombres - $nbrUsersAlreadyReceivedEmail;
            self::$gabarit = 'gabaritAjax';
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'GroupInfos';
            self::showVue([
                'users' => $users,
                'users_nombres' => $users_nombres,
                'usersAlreadyReceivedEmail' => $usersAlreadyReceivedEmail,
                'nbrUsersAlreadyReceivedEmail' => $nbrUsersAlreadyReceivedEmail,
                'totalEmailsToSend' => $totalEmailsToSend,
                'urlFetchPopupEmailsDetails' => $urlFetchPopupEmailsDetails
            ]);
        } else {
            echo json_encode(['success' => false]);
        }

    }


    public function getEmailTemplateMessage()
    {
        $templateId = $_GET['templateId'];
        $emailTemplateRepository = new TemplateEmailRepository();
        $template = $emailTemplateRepository->getEmailTemplateById($templateId);
        $template->message = html_entity_decode($template->message, ENT_QUOTES, 'UTF-8');
        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'EmailTemplate';
        self::showVue(['template' => $template]);

    }

    public function DeleteTemplateEmail() {
        $quizId = $_GET['quizId'] ?? null;
        $templateId = $_GET['templateId'] ?? null;
        $url = WEB_PATH . 'quiz.html/PublishQuiz?quizId=' . $quizId;
        $templateRepo = new TemplateEmailRepository();
        $templateRepo->delete($templateId);
        Appy::redirigeVers($url);
    }



    public function EmailsDetails()
    {
        $type = $_GET['type'] ?? null;
        $groupeId = $_GET['GroupeId'] ?? null;
        $quizId = $_GET['QuizId'] ?? null;
        if (!$type || !$groupeId || !$quizId) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètres manquants ou invalides.']);
            return;
        }

        $userRepository = new UsersRepository();
        switch ($type) {
            case 'emails_all':
                $title = "Tous les emails du groupe: ";
                $users = $userRepository->getRespondantsWithEmailByGroupeId($groupeId);
                break;
            case 'emails_received':
                $title = "Emails déjà envoyés: ";
                $usersAll = $userRepository->getRespondantsWithEmailByGroupeId($groupeId);
                $users= $userRepository->getUsersAlreadyReceivedEmail($quizId, $usersAll);
                break;
            case 'emails_to_send':
                $usersAll = $userRepository->getRespondantsWithEmailByGroupeId($groupeId);
                $usersAlreadyReceivedEmail = $userRepository->getUsersAlreadyReceivedEmail($quizId, $usersAll);
                $users = array_filter($usersAll, function ($user) use ($usersAlreadyReceivedEmail) {
                    return !in_array($user, $usersAlreadyReceivedEmail);
                });
                $title = "Emails à envoyer: ";
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Type inconnu.']);
                return;
        }
        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'ListEmailsAjax';
        self::showVue([
            'title' => $title,
            'users' => $users
        ]);
    }


    public function SaveTemplateEmail(){
        $title = $_POST['templateName'] ?? null;
        $message = $_POST['messageHTML'] ?? null;
        $quizId = $_POST['quizId'] ?? null;
        $url = WEB_PATH . 'quiz.html/PublishQuiz?quizId=' . $quizId;
        $templateEmailRepository = new TemplateEmailRepository();
        $templateEmailRepository->CreateNewTemplate($title, $message);
        Appy::redirigeVers($url);
    }



    public function Remerciement(){
        $urlFinish = WEB_PATH . 'quiz.html/finish';
        $quizIdentifier = $_GET['quizId'];
        $userIdentifier = $_GET['identifier'];
        $quizRepository = new QuizRepository();
        $quizUserRepository = new QuizUserRepository();
        $quizUser = $quizUserRepository->getQuizUserByIdentifiers($quizIdentifier, $userIdentifier);

        if ($quizUser->status == 'FINISH'){
            Appy::redirigeVers($urlFinish);
        }
        $criteres = ['quizIdentifier' => $quizIdentifier];
        $quiz = $quizRepository->getQuizzes($criteres)[0];
        $quizUserId = $quizUser->id;
        $newStatus = "FINISH";
        $quizUserRepository->updateStatus($quizUserId, $newStatus);
        self::$gabarit = 'gabaritRepondant';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'Remerciement';
        self::showVue([
            'quizUser' => $quizUser,
            'quiz' => $quiz
        ]);

    }

    public function finish(){
        self::$gabarit = 'gabaritRepondant';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'FinishedQuiz';
        self::showVue([]);

    }

    public function notFound(){
        header("HTTP/1.0 404 Not Found");
        exit;

    }

    public function editBaromQuestions(){
        $urlPopUpEditQuestionBarom = WEB_PATH . 'quiz.html/popUpEditQuestionBarom';
        $urlDeleteQuestionBarom = WEB_PATH . 'quiz.html/deleteQuestionBarom';
        $quizType = 'BAROM';
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $questions = $TemplateQuizQuestionsRepository->getQuestionsByType($quizType);
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'EditQuestionsBarom';
        self::showVue([
            'questions' => $questions,
            'urlPopUpEditQuestionBarom' => $urlPopUpEditQuestionBarom,
            'urlDeleteQuestionBarom' => $urlDeleteQuestionBarom,
        ]);
    }

    public function edit360Questions(){
        $urlPopUpEditQuestion = WEB_PATH . 'quiz.html/popUpEditQuestion';
        $urlCreateNewChapter360 = WEB_PATH . 'quiz.html/createNewChapter360';
        $urlCreateNewRadio360Text = WEB_PATH . 'quiz.html/createNewRadio360Text';
        $urlCreateNewRadio360List = WEB_PATH . 'quiz.html/createNewRadio360List';
        $urlDeleteQuestion360 = WEB_PATH . 'quiz.html/deleteQuestion360';
        $quizType = '360';
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $questions = $TemplateQuizQuestionsRepository->getQuestionsByType($quizType);
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'EditQuestions360';
        self::showVue([
            'questions' => $questions,
            'urlPopUpEditQuestion' => $urlPopUpEditQuestion,
            'urlCreateNewChapter360' => $urlCreateNewChapter360,
            'urlCreateNewRadio360Text' => $urlCreateNewRadio360Text,
            'urlCreateNewRadio360List' => $urlCreateNewRadio360List,
            'urlDeleteQuestion360' => $urlDeleteQuestion360,
        ]);
    }

    public function editSingleQuiz360() {
        $quizId = $_GET['quizId'];
        $urlPopUpEditSingleQuestion = WEB_PATH . 'quiz.html/popUpEditSingleQuestion?quizId=' . $quizId;
        $urlCreateNewSingleChapter360 = WEB_PATH . 'quiz.html/createNewChapterSingle360?quizId=' . $quizId;
        $urlCreateNewSingleRadio360Text = WEB_PATH . 'quiz.html/createNewRadioSingle360Text?quizId=' . $quizId;
        $urlCreateNewSingleRadio360List = WEB_PATH . 'quiz.html/createNewRadioSingle360List?quizId=' .$quizId;
        $urlDeleteSingleQuestion360 = WEB_PATH . 'quiz.html/deleteSingleQuestion360';
        $QuizQuestionsRepository = new QuizQuestionRepository();
        $questions = $QuizQuestionsRepository->getQuestionsByQuizId($quizId);

        $QuizRepository = new QuizRepository();
        $quiz = $QuizRepository->getQuizById($quizId);

        $urlModeTest = "";
        if(\Appy\Src\Config::ENV == 'PROD') {
            $urlModeTest = \Appy\Src\Config::DOMAIN . "/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&modeTest=1";
            $urlModeTestAuto = \Appy\Src\Config::DOMAIN . "/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&modeTest=2";
        } else {
            $array_path = explode("/", $_SERVER['REQUEST_URI']);
            array_pop($array_path);
            $path       = implode("/", $array_path);
            $urlModeTest =  "http://localhost/relais-managers-services/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&modeTest=1";
            $urlModeTestAuto =  "http://localhost/relais-managers-services/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&modeTest=2";
        }

        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'EditSingleQuiz360';
        self::showVue([
            'questions' => $questions,
            'quizId' => $quizId,
            'urlPopUpEditSingleQuestion' => $urlPopUpEditSingleQuestion,
            'urlCreateNewSingleChapter360' => $urlCreateNewSingleChapter360,
            'urlCreateNewSingleRadio360Text' => $urlCreateNewSingleRadio360Text,
            'urlCreateNewSingleRadio360List' => $urlCreateNewSingleRadio360List,
            'urlDeleteSingleQuestion360' => $urlDeleteSingleQuestion360,
            'urlModeTest' => $urlModeTest,
            'urlModeTestAuto' => $urlModeTestAuto,
        ]);
    }

    public function editSingleBarometre() {
        $quizId = $_GET['quizId'];
        $urlPopUpEditSingleQuestionBarom = WEB_PATH . 'quiz.html/popUpEditSingleQuestionBarom';
        $urlDeleteQuestionBarom = WEB_PATH . 'quiz.html/deleteSingleQuestionBarom';

        $QuizRepository = new QuizRepository();
        $QuizQuestionsRepository = new QuizQuestionRepository();
        $quiz = $QuizRepository->getQuizById($quizId);
        $questions = $QuizQuestionsRepository->getQuestionsByQuizId($quizId);

        $urlModeTest = "";
        if(\Appy\Src\Config::ENV == 'PROD') {
            $urlModeTest = \Appy\Src\Config::DOMAIN . "/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&modeTest=1";
        } else {
            $array_path = explode("/", $_SERVER['REQUEST_URI']);
            array_pop($array_path);
            $path       = implode("/", $array_path);
            $urlModeTest =  "http://localhost/relais-managers-services/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&modeTest=1";
        }

        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'EditSingleBarometre';
        self::showVue([
            'questions' => $questions,
            'quizId' => $quizId,
            'urlModeTest' => $urlModeTest,
            'urlPopUpEditSingleQuestionBarom' => $urlPopUpEditSingleQuestionBarom,
            'urlDeleteQuestionBarom' => $urlDeleteQuestionBarom
        ]);
    }

    public function popUpEditSingleQuestionBarom() {
        $questionId = $_GET['questionId'];
        $quizId = $_GET['quizId'];
        $urlSavePopUpEditSingleRadioBarom = WEB_PATH . 'quiz.html/savePopUpEditSingleRadioBarom?quizId=' . $quizId;
        $urlSavePopUpEditSingleChapterBarom = WEB_PATH . 'quiz.html/savePopUpEditSingleChapterBarom?quizId=' . $quizId;
        $QuizQuestionRepository = new QuizQuestionRepository();
        $question = $QuizQuestionRepository->getQuestionById($questionId);
        $questionText = strip_tags($question->label);
        if ($question->questionType == "INPUT-RADIO") {
            $vueContentPopUpEditQuestion = "ContentPopUpEditSingleRadioBarom";
        }
        else if ($question->questionType == "CHAPTER") {
            $vueContentPopUpEditQuestion = "ContentPopUpEditSingleChapterBarom";
        }
        else {
            $vueContentPopUpEditQuestion = "Type de question non pris en charge pour le moment";
        }

        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . $vueContentPopUpEditQuestion;
        self::showVue([
            'question' => $question,
            'questionText' => $questionText,
            'questionId' => $questionId,
            'urlSavePopUpEditSingleRadioBarom' => $urlSavePopUpEditSingleRadioBarom,
            'urlSavePopUpEditSingleChapterBarom' => $urlSavePopUpEditSingleChapterBarom,
        ]);
    }

    public function savePopUpEditSingleChapterBarom(){
        $chapterLabel = $_POST['single_chapter_label'];
        $questionId = $_POST['question_id'];
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleBarometre?quizId=' . $quizId;
        $label = '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">' . strtoupper(htmlspecialchars($chapterLabel)) . "</div>";
        $quizQuestionRepository = new QuizQuestionRepository();
        $label_auto = null;
        $quizQuestionRepository->UpdateLabelsByQuestionIdAndQuizId($questionId, $quizId, $label, $label_auto);
        Appy::redirigeVers($url);
    }

    public function savePopUpEditSingleRadioBarom(){
        $questionId = $_POST['question_id'];
        $TextLabel = $_POST['single_respondent_text'];
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleBarometre?quizId=' . $quizId;
        $label = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS;">' . htmlspecialchars($TextLabel) . "</div>";
        $quizQuestionRepository = new QuizQuestionRepository();
        $label_auto = null;
        $quizQuestionRepository->UpdateLabelsByQuestionIdAndQuizId($questionId, $quizId, $label, $label_auto);
        Appy::redirigeVers($url);

    }

    public function deleteQuestion360(){
        $url = WEB_PATH . 'quiz.html/edit360Questions';
        $questionId = $_GET['QuestionId'];
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $TemplateQuizQuestionsRepository->deleteQuestion360($questionId);
        Appy::redirigeVers($url);
    }

    public function deleteSingleQuestion360(){
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleQuiz360?quizId=' . $quizId;
        $questionId = $_GET['QuestionId'];
        $QuizQuestionRepository = new QuizQuestionRepository();
        $QuizQuestionRepository->deleteSingleQuestion360($questionId);
        Appy::redirigeVers($url);

    }

    public function popUpEditQuestion()
    {
        $questionId = $_GET['question_id'];
        $switchToList = isset($_GET['switch_to_list']) ? $_GET['switch_to_list'] : false;
        $switchToText = isset($_GET['switch_to_text']) ? $_GET['switch_to_text'] : false;
        $listTitle = "";
        $listTitleAuto = "";
        $listItems = [];
        $listItemsAuto = [];
        $title = "";
        $titleAuto = "";
        $responses = [];
        $responsesAuto = [];
        $urlSavePopUpEditChapter360 = WEB_PATH . 'quiz.html/savePopUpEditChapter360';
        $urlSavePopUpEditRadio360Text = WEB_PATH . 'quiz.html/savePopUpEditRadio360Text';
        $urlSavePopUpEditRadio360List = WEB_PATH . 'quiz.html/savePopUpEditRadio360List';
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $question = $TemplateQuizQuestionsRepository->getQuestionById($questionId);
        if ($switchToText) {
            $question->label = preg_replace('/<ul.*?>/', '', $question->label);
            $question->label = preg_replace('/<\/ul>/', '', $question->label);
            $question->label = preg_replace('/<li.*?>/', '', $question->label);
            $question->label = preg_replace('/<\/li>/', '', $question->label);
            $question->labelAuto = preg_replace('/<ul.*?>/', '', $question->labelAuto);
            $question->labelAuto = preg_replace('/<\/ul>/', '', $question->labelAuto);
            $question->labelAuto = preg_replace('/<li.*?>/', '', $question->labelAuto);
            $question->labelAuto = preg_replace('/<\/li>/', '', $question->labelAuto);
        }
        if ($question->questionType == "INPUT-RADIO" && $question->ordre !== '2') {
            if (strpos($question->label, "<ul>") !== false && strpos($question->label, "</ul>") !== false) {
                preg_match('/(.*)<ul>/', $question->label, $matches);
                $listTitle = isset($matches[1]) ? $matches[1] : '';
                preg_match_all('/<li>(.*?)<\/li>/', $question->label, $listItems);
                $listItems = isset($listItems[1]) ? $listItems[1] : [];
                preg_match('/(.*)<ul>/', $question->labelAuto, $matches);
                $listTitleAuto = isset($matches[1]) ? $matches[1] : '';
                preg_match_all('/<li>(.*?)<\/li>/', $question->labelAuto, $listItemsAuto);
                $listItemsAuto = isset($listItemsAuto[1]) ? $listItemsAuto[1] : [];
                $vueContentPopUpEditQuestion = "ContentPopUpEditRadio360List";
            } else {
                if ($switchToList) {
                    preg_match('/(.*)<ul>/', $question->label, $matches);
                    $listTitle = $_GET['list_title'];
                    preg_match_all('/<li>(.*?)<\/li>/', $question->label, $listItems);
                    $listItems = isset($listItems[1]) ? $listItems[1] : [];
                    preg_match('/(.*)<ul>/', $question->labelAuto, $matches);
                    $listTitleAuto = $_GET['list_title_auto'];
                    preg_match_all('/<li>(.*?)<\/li>/', $question->labelAuto, $listItemsAuto);
                    $listItemsAuto = isset($listItemsAuto[1]) ? $listItemsAuto[1] : [];
                    $vueContentPopUpEditQuestion = "ContentPopUpEditRadio360List";
                } else {
                    $vueContentPopUpEditQuestion = "ContentPopUpEditRadio360Text";
                }
            }
        }
        else if ($question->ordre == '2' || $question->ordre =='3' && $question->quizType == '360') {
            $vueContentPopUpEditQuestion = "Type de question non pris en charge";
        }
        else if ($question->questionType == "CHAPTER") {
            $vueContentPopUpEditQuestion = "ContentPopUpEditChapter360";
        }
        else {
            $vueContentPopUpEditQuestion = "Type de question non pris en charge";
        }
        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . $vueContentPopUpEditQuestion;
        self::showVue([
            'question' => $question,
            'questionId' => $questionId,
            'listTitle' => $listTitle,
            'listTitleAuto' => $listTitleAuto,
            'listItems' => $listItems,
            'listItemsAuto' => $listItemsAuto,
            'title' => $title,
            'responses' => $responses,
            'titleAuto' => $titleAuto,
            'responsesAuto' => $responsesAuto,
            'urlSavePopUpEditRadio360Text' => $urlSavePopUpEditRadio360Text,
            'urlSavePopUpEditRadio360List' => $urlSavePopUpEditRadio360List,
            'urlSavePopUpEditChapter360' => $urlSavePopUpEditChapter360,
        ]);
    }

    public function popUpEditSingleQuestion()
    {
        $quizId = $_GET['quizId'];
        $questionId = $_GET['question_id'];
        $switchToList = isset($_GET['switch_to_list']) ? $_GET['switch_to_list'] : false;
        $switchToText = isset($_GET['switch_to_text']) ? $_GET['switch_to_text'] : false;
        $listTitle = "";
        $listTitleAuto = "";
        $listItems = [];
        $listItemsAuto = [];
        $title = "";
        $titleAuto = "";
        $responses = [];
        $responsesAuto = [];
        $urlSavePopUpEditSingleChapter360 = WEB_PATH . 'quiz.html/savePopUpEditSingleChapter360?quizId=' . $quizId;
        $urlSavePopUpEditSingleRadio360Text = WEB_PATH . 'quiz.html/savePopUpEditSingleRadio360Text?quizId=' . $quizId;
        $urlSavePopUpEditSingleRadio360List = WEB_PATH . 'quiz.html/savePopUpEditSingleRadio360List?quizId=' . $quizId;
        $QuizQuestionRepository = new QuizQuestionRepository();
        $question = $QuizQuestionRepository->getQuestionById($questionId);
        if ($switchToText) {
            $question->label = preg_replace('/<ul.*?>/', '', $question->label);
            $question->label = preg_replace('/<\/ul>/', '', $question->label);
            $question->label = preg_replace('/<li.*?>/', '', $question->label);
            $question->label = preg_replace('/<\/li>/', '', $question->label);
            $question->labelAuto = preg_replace('/<ul.*?>/', '', $question->labelAuto);
            $question->labelAuto = preg_replace('/<\/ul>/', '', $question->labelAuto);
            $question->labelAuto = preg_replace('/<li.*?>/', '', $question->labelAuto);
            $question->labelAuto = preg_replace('/<\/li>/', '', $question->labelAuto);
        }
        if ($question->questionType == "INPUT-RADIO" && $question->ordre !== '2') {
            if (strpos($question->label, "<ul>") !== false && strpos($question->label, "</ul>") !== false) {
                preg_match('/(.*)<ul>/', $question->label, $matches);
                $listTitle = isset($matches[1]) ? $matches[1] : '';
                preg_match_all('/<li>(.*?)<\/li>/', $question->label, $listItems);
                $listItems = isset($listItems[1]) ? $listItems[1] : [];
                preg_match('/(.*)<ul>/', $question->labelAuto, $matches);
                $listTitleAuto = isset($matches[1]) ? $matches[1] : '';
                preg_match_all('/<li>(.*?)<\/li>/', $question->labelAuto, $listItemsAuto);
                $listItemsAuto = isset($listItemsAuto[1]) ? $listItemsAuto[1] : [];
                $vueContentPopUpEditQuestion = "ContentPopUpEditSingleRadio360List";
            } else {
                if ($switchToList) {
                    preg_match('/(.*)<ul>/', $question->label, $matches);
                    $listTitle = $_GET['list_title'];
                    preg_match_all('/<li>(.*?)<\/li>/', $question->label, $listItems);
                    $listItems = isset($listItems[1]) ? $listItems[1] : [];
                    preg_match('/(.*)<ul>/', $question->labelAuto, $matches);
                    $listTitleAuto = $_GET['list_title_auto'];
                    preg_match_all('/<li>(.*?)<\/li>/', $question->labelAuto, $listItemsAuto);
                    $listItemsAuto = isset($listItemsAuto[1]) ? $listItemsAuto[1] : [];
                    $vueContentPopUpEditQuestion = "ContentPopUpEditSingleRadio360List";
                } else {
                    $vueContentPopUpEditQuestion = "ContentPopUpEditSingleRadio360Text";
                }
            }
        }
        else if ($question->ordre == '2' || $question->ordre == '3' && $question->quizType == '360') {
            $vueContentPopUpEditQuestion = "Type de question non pris en charge";
        }
        else if ($question->questionType == "CHAPTER") {
            $vueContentPopUpEditQuestion = "ContentPopUpEditSingleChapter360";
        }
        else {
            $vueContentPopUpEditQuestion = "Type de question non pris en charge";
        }
        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . $vueContentPopUpEditQuestion;
        self::showVue([
            'question' => $question,
            'questionId' => $questionId,
            'listTitle' => $listTitle,
            'listTitleAuto' => $listTitleAuto,
            'listItems' => $listItems,
            'listItemsAuto' => $listItemsAuto,
            'title' => $title,
            'responses' => $responses,
            'titleAuto' => $titleAuto,
            'responsesAuto' => $responsesAuto,
            'urlSavePopUpEditSingleRadio360Text' => $urlSavePopUpEditSingleRadio360Text,
            'urlSavePopUpEditSingleRadio360List' => $urlSavePopUpEditSingleRadio360List,
            'urlSavePopUpEditSingleChapter360' => $urlSavePopUpEditSingleChapter360,
        ]);
    }


    public function popUpEditQuestionBarom()
    {
        $questionId = $_GET['question_id'];
        $urlSavePopUpEditChapterBarom = WEB_PATH . 'quiz.html/savePopUpEditChapterBarom';
        $urlSavePopUpEditRadioBarom = WEB_PATH . 'quiz.html/savePopUpEditRadioBarom';
        $templateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $question = $templateQuizQuestionsRepository->getQuestionById($questionId);
        $question->label = htmlspecialchars_decode($question->label, ENT_NOQUOTES);
        if ($question->questionType === 'CHAPTER') {
            $vueContentPopUpEditQuestion = "ContentPopUpEditChapterBarom";
        } elseif ($question->questionType === 'INPUT-RADIO') {
            $vueContentPopUpEditQuestion = "ContentPopUpEditRadioBarom";
        } else {
            $vueContentPopUpEditQuestion = "Type de question non pris en charge pour le moment";
        }

        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . $vueContentPopUpEditQuestion;
        self::showVue([
            'question' => $question,
            'questionId' => $questionId,
            'urlSavePopUpEditChapterBarom' => $urlSavePopUpEditChapterBarom,
            'urlSavePopUpEditRadioBarom' => $urlSavePopUpEditRadioBarom,
        ]);
    }


    public function savePopUpEditChapterBarom(){
        $url = WEB_PATH . 'quiz.html/editBaromQuestions';
        $questionId = $_POST['question_id'];
        $chapterLabel = $_POST['chapter_label'];
        $label = '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">' . strtoupper(htmlspecialchars($chapterLabel)) . "</div>";
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $label_auto = null;
        $TemplateQuizQuestionsRepository->UpdateLabelsByQuestionId($questionId, $label, $label_auto);
        Appy::redirigeVers($url);
    }

    public function savePopUpEditRadioBarom(){
        $url = WEB_PATH . 'quiz.html/editBaromQuestions';
        $questionId = $_POST['question_id'];
        $TextLabel = $_POST['respondent_text'];
        $label = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($TextLabel) . "</div>";
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $label_auto = null;
        $TemplateQuizQuestionsRepository->UpdateLabelsByQuestionId($questionId, $label, $label_auto);
        Appy::redirigeVers($url);

    }

    public function savePopUpEditChapter360(){
        $url = WEB_PATH . 'quiz.html/edit360Questions';
        $questionId = $_POST['question_id'];
        $chapterLabel = $_POST['chapter_label'];
        $chapterLabelAuto = $_POST['chapter_label_auto'];
        $label = '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">' . strtoupper(htmlspecialchars($chapterLabel)) . "</div>";
        $labelAuto = '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">' . strtoupper(htmlspecialchars($chapterLabelAuto)) . "</div>";
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $TemplateQuizQuestionsRepository->UpdateLabelsByQuestionId($questionId, $label, $labelAuto);
        Appy::redirigeVers($url);
    }

    public function savePopUpEditSingleChapter360(){
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleQuiz360?quizId=' . $quizId;
        $questionId = $_POST['question_id'];
        $chapterLabel = $_POST['chapter_label'];
        $chapterLabelAuto = $_POST['chapter_label_auto'];
        $label = '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">' . strtoupper(htmlspecialchars($chapterLabel)) . "</div>";
        $labelAuto = '<div style="font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold">' . strtoupper(htmlspecialchars($chapterLabelAuto)) . "</div>";
        $QuizQuestionRepository = new QuizQuestionRepository();
        $QuizQuestionRepository->UpdateLabelsByQuestionIdAndQuizId($questionId, $quizId, $label, $labelAuto);
        Appy::redirigeVers($url);
    }


    public function savePopUpEditRadio360Text(){
        $url = WEB_PATH . 'quiz.html/edit360Questions';
        $questionId = $_POST['question_id'];
        $TextLabel = $_POST['respondent_text'];
        $TextLabelAuto = $_POST['auto_evaluated_text'];
        $label = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($TextLabel) . "</div>";
        $labelAuto = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($TextLabelAuto) . "</div>";
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $TemplateQuizQuestionsRepository->UpdateLabelsByQuestionId($questionId, $label, $labelAuto);
        Appy::redirigeVers($url);

    }

    public function savePopUpEditSingleRadio360Text(){
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleQuiz360?quizId=' . $quizId;
        $questionId = $_POST['question_id'];
        $TextLabel = $_POST['respondent_text'];
        $TextLabelAuto = $_POST['auto_evaluated_text'];
        $label = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($TextLabel) . "</div>";
        $labelAuto = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($TextLabelAuto) . "</div>";
        $QuizQuestionRepository = new QuizQuestionRepository();
        $QuizQuestionRepository->UpdateLabelsByQuestionIdAndQuizId($questionId, $quizId, $label, $labelAuto);
        Appy::redirigeVers($url);
    }

    public function savePopUpEditRadio360List(){
        $url = WEB_PATH . 'quiz.html/edit360Questions';
        $questionId = $_POST['question_id'];
        $listTitleNormal = isset($_POST['list_title_normal']) ? $_POST['list_title_normal'] : '';
        $listItemsNormal = [];
        for ($i = 0; $i < 10; $i++) {
            $item = isset($_POST["list_item_normal_$i"]) ? $_POST["list_item_normal_$i"] : '';
            if (!empty($item)) {
                $listItemsNormal[] = $item;
            }
        }
        $htmlNormal = "<div style='font-size: 16px; color: #696252;font-family: Trebuchet MS;'>"
            . htmlspecialchars($listTitleNormal)
            . "<ul>";
        foreach ($listItemsNormal as $item) {
            $htmlNormal .= "<li>" . htmlspecialchars($item) . "</li>";
        }
        $htmlNormal .= "</ul></div>";
        $listItemsAutoEvaluated = [];
        for ($i = 0; $i < 10; $i++) {
            $item = isset($_POST["list_item_auto_evaluated_$i"]) ? $_POST["list_item_auto_evaluated_$i"] : '';
            if (!empty($item)) {
                $listItemsAutoEvaluated[] = $item;
            }
        }

        $htmlAutoEvaluated = "<div style='font-size: 16px; color: #696252;font-family: Trebuchet MS;'>"
            . htmlspecialchars($_POST['list_title_auto_evaluated'])
            . "<ul>";
        foreach ($listItemsAutoEvaluated as $item) {
            $htmlAutoEvaluated .= "<li>" . htmlspecialchars($item) . "</li>";
        }
        $htmlAutoEvaluated .= "</ul></div>";
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $TemplateQuizQuestionsRepository->UpdateLabelsByQuestionId($questionId, $htmlNormal, $htmlAutoEvaluated);
        Appy::redirigeVers($url);
    }

    public function savePopUpEditSingleRadio360List(){
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleQuiz360?quizId=' . $quizId;
        $questionId = $_POST['question_id'];
        $listTitleNormal = isset($_POST['list_title_normal']) ? $_POST['list_title_normal'] : '';
        $listItemsNormal = [];
        for ($i = 0; $i < 10; $i++) {
            $item = isset($_POST["list_item_normal_$i"]) ? $_POST["list_item_normal_$i"] : '';
            if (!empty($item)) {
                $listItemsNormal[] = $item;
            }
        }
        $htmlNormal = "<div style='font-size: 16px; color: #696252;font-family: Trebuchet MS;'>"
            . htmlspecialchars($listTitleNormal)
            . "<ul>";
        foreach ($listItemsNormal as $item) {
            $htmlNormal .= "<li>" . htmlspecialchars($item) . "</li>";
        }
        $htmlNormal .= "</ul></div>";
        $listItemsAutoEvaluated = [];
        for ($i = 0; $i < 10; $i++) {
            $item = isset($_POST["list_item_auto_evaluated_$i"]) ? $_POST["list_item_auto_evaluated_$i"] : '';
            if (!empty($item)) {
                $listItemsAutoEvaluated[] = $item;
            }
        }

        $htmlAutoEvaluated = "<div style='font-size: 16px; color: #696252; font-family: Trebuchet MS;'>"
            . htmlspecialchars($_POST['list_title_auto_evaluated'])
            . "<ul>";
        foreach ($listItemsAutoEvaluated as $item) {
            $htmlAutoEvaluated .= "<li>" . htmlspecialchars($item) . "</li>";
        }
        $htmlAutoEvaluated .= "</ul></div>";
        $QuizQuestionRepository = new QuizQuestionRepository();
        $QuizQuestionRepository->UpdateLabelsByQuestionIdAndQuizId($questionId, $quizId, $htmlNormal, $htmlAutoEvaluated);
        Appy::redirigeVers($url);
    }

    public function createNewChapter360(){
        $url = WEB_PATH . 'quiz.html/edit360Questions';
        $newChapterLabel = $_POST['new_chapter_label'];
        $newChapterLabelAuto = $_POST['new_chapter_label_auto'];
        $newLabel = "<div style='font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold'>" . strtoupper(htmlspecialchars($newChapterLabel)) . "</div>";
        $newLabelAuto = "<div style='font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold'>" . strtoupper(htmlspecialchars($newChapterLabelAuto)) . "</div>";
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $TemplateQuizQuestionsRepository->createNewChapter360($newLabel, $newLabelAuto);
        Appy::redirigeVers($url);

    }

    public function createNewSingleChapter360()
    {
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleQuiz360?quizId=' . $quizId;
        $newChapterLabel = $_POST['new_chapter_label'];
        $newChapterLabelAuto = $_POST['new_chapter_label_auto'];
        $newLabel = "<div style='font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold'>" . strtoupper(htmlspecialchars($newChapterLabel)) . "</div>";
        $newLabelAuto = "<div style='font-size: 19px; color: #ff6600;font-family:Trebuchet MS;font-weight: bold'>" . strtoupper(htmlspecialchars($newChapterLabelAuto)) . "</div>";
        $QuizQuestionsRepository = new QuizQuestionRepository();
        $QuizQuestionsRepository->createNewSingleChapter360($newLabel, $newLabelAuto, $quizId);
        Appy::redirigeVers($url);

    }

    public function createNewRadio360Text(){
        $url = WEB_PATH . 'quiz.html/edit360Questions';
        $newTextLabel = $_POST['respondent_text'];
        $newTextLabelAuto = $_POST['auto_evaluated_text'];
        $newLabel = "<div style='font-size: 16px; color: #696252;font-family:Trebuchet MS'>" . htmlspecialchars($newTextLabel) . "</div>";
        $newLabelAuto = "<div style='font-size: 16px; color: #696252;font-family:Trebuchet MS'>" . htmlspecialchars($newTextLabelAuto) . "</div>";
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $TemplateQuizQuestionsRepository->createNewRadio360Text($newLabel, $newLabelAuto);
        Appy::redirigeVers($url);
    }

    public function createNewSingleRadio360Text(){
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleQuiz360?quizId=' . $quizId;
        $newTextLabel = $_POST['respondent_text'];
        $newTextLabelAuto = $_POST['auto_evaluated_text'];
        $newLabel = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($newTextLabel) . "</div>";
        $newLabelAuto = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($newTextLabelAuto) . "</div>";
        $QuizQuestionsRepository = new QuizQuestionRepository();
        $QuizQuestionsRepository->createNewSingleRadio360Text($newLabel, $newLabelAuto, $quizId);
        Appy::redirigeVers($url);
    }

    public function createNewRadio360List() {
        $url = WEB_PATH . 'quiz.html/edit360Questions';
        $listTitleNormal = isset($_POST['new_list_title_normal']) ? $_POST['new_list_title_normal'] : '';
        $listItemsNormal = [];
        $listItemsAutoEvaluated = [];
        for ($i = 0; $i < 10; $i++) {
            $item = isset($_POST["new_list_item_normal_$i"]) ? $_POST["new_list_item_normal_$i"] : '';
            if (!empty($item)) {
                $listItemsNormal[] = $item;
            }
        }

        for ($i = 0; $i < 10; $i++) {
            $item = isset($_POST["new_list_item_auto_evaluated_$i"]) ? $_POST["new_list_item_auto_evaluated_$i"] : '';
            if (!empty($item)) {
                $listItemsAutoEvaluated[] = $item;
            }
        }

        $htmlNormal = "<div style='font-size: 16px; color: #696252; font-family: Trebuchet MS;'>"
            . htmlspecialchars($listTitleNormal)
            . "<ul>";
        foreach ($listItemsNormal as $item) {
            $htmlNormal .= "<li>" . htmlspecialchars($item) . "</li>";
        }
        $htmlNormal .= "</ul></div>";
        $htmlAutoEvaluated = "<div style='font-size: 16px; color: #696252; font-family: Trebuchet MS;'>"
            . htmlspecialchars($_POST['new_list_title_auto_evaluated'])
            . "<ul>";
        foreach ($listItemsAutoEvaluated as $item) {
            $htmlAutoEvaluated .= "<li>" . htmlspecialchars($item) . "</li>";
        }
        $htmlAutoEvaluated .= "</ul></div>";

        $templateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $templateQuizQuestionsRepository->createNewRadio360List($htmlNormal, $htmlAutoEvaluated);
        Appy::redirigeVers($url);
    }

    public function createNewSingleRadio360List() {
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSingleQuiz360?quizId=' . $quizId;
        $listTitleNormal = isset($_POST['new_list_title_normal']) ? $_POST['new_list_title_normal'] : '';
        $listItemsNormal = [];
        $listItemsAutoEvaluated = [];
        for ($i = 0; $i < 10; $i++) {
            $item = isset($_POST["new_list_item_normal_$i"]) ? $_POST["new_list_item_normal_$i"] : '';
            if (!empty($item)) {
                $listItemsNormal[] = $item;
            }
        }

        for ($i = 0; $i < 10; $i++) {
            $item = isset($_POST["new_list_item_auto_evaluated_$i"]) ? $_POST["new_list_item_auto_evaluated_$i"] : '';
            if (!empty($item)) {
                $listItemsAutoEvaluated[] = $item;
            }
        }

        $htmlNormal = "<div style='font-size: 16px; color: #696252; font-family: Trebuchet MS;'>"
            . htmlspecialchars($listTitleNormal)
            . "<ul>";
        foreach ($listItemsNormal as $item) {
            $htmlNormal .= "<li>" . htmlspecialchars($item) . "</li>";
        }
        $htmlNormal .= "</ul></div>";
        $htmlAutoEvaluated = "<div style='font-size: 16px; color: #696252; font-family: Trebuchet MS;'>"
            . htmlspecialchars($_POST['new_list_title_auto_evaluated'])
            . "<ul>";
        foreach ($listItemsAutoEvaluated as $item) {
            $htmlAutoEvaluated .= "<li>" . htmlspecialchars($item) . "</li>";
        }
        $htmlAutoEvaluated .= "</ul></div>";

        $QuizQuestionsRepository = new QuizQuestionRepository();
        $QuizQuestionsRepository->createNewSingleRadio360List($htmlNormal, $htmlAutoEvaluated, $quizId);
        Appy::redirigeVers($url);
    }

    public function editPRCCQuestions(){
        $urlPopUpEditQuestionPRCC = WEB_PATH . 'quiz.html/popUpEditQuestionPRCC';
        $urlPopUpEditCategoryPRCC = WEB_PATH . 'quiz.html/popUpEditCategoryPRCC';
        $urlDeletePrccQuestion = WEB_PATH . 'quiz.html/deletePrccQuestion';
        $quizType = 'PRCC';
        $templatePrccCategoryRepository = new TemplatePrccCategoryRepository();
        $categories = $templatePrccCategoryRepository->getAllPrccCategoryTemplates();
        $templateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $questions = $templateQuizQuestionsRepository->getQuestionsByType($quizType);
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'EditQuestionsPRCC';
        self::showVue([
            'categories' => $categories,
            'questions' => $questions,
            'urlPopUpEditQuestionPRCC' => $urlPopUpEditQuestionPRCC,
            'urlPopUpEditCategoryPRCC' => $urlPopUpEditCategoryPRCC,
            'urlDeletePrccQuestion' => $urlDeletePrccQuestion
        ]);

    }

    public function popUpEditQuestionPRCC(){
        $questionId = $_GET['question_id'];
        $urlSavePopUpEditQuestionPRCC = WEB_PATH . 'quiz.html/savePopUpEditQuestionPRCC';
        $templateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $question = $templateQuizQuestionsRepository->getQuestionById($questionId);
        $question->label = htmlspecialchars_decode($question->label, ENT_NOQUOTES);
        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'PopUpEditQuestionPRCC';
        self::showVue([
            'question' => $question,
            'urlSavePopUpEditQuestionPRCC' => $urlSavePopUpEditQuestionPRCC
        ]);

    }

    public function popUpEditCategoryPRCC(){
        $categoryId = $_GET['category_id'];
        $urlSavePopUpEditCategory = WEB_PATH . 'quiz.html/savePopUpEditCategory';
        $templatePrccCategoryRepository = new TemplatePrccCategoryRepository();
        $category = $templatePrccCategoryRepository->getTemplatePrccCategoryById($categoryId);
        $category->label = htmlspecialchars_decode($category->label, ENT_NOQUOTES);
        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'PopUpEditCategory';
        self::showVue([
            'category' => $category,
            'categoryId' => $categoryId,
            'urlSavePopUpEditCategory' => $urlSavePopUpEditCategory,
        ]);
    }

    public function savePopUpEditQuestionPRCC(){
        $url = WEB_PATH . 'quiz.html/editPRCCQuestions';
        $questionId = $_POST['question_id'];
        $TextLabel = $_POST['respondent_text'];
        $label = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($TextLabel) . "</div>";
        $TemplateQuizQuestionsRepository = new TemplateQuizQuestionsRepository();
        $label_auto = null;
        $TemplateQuizQuestionsRepository->UpdateLabelsByQuestionId($questionId, $label, $label_auto);
        Appy::redirigeVers($url);

    }

    public function savePopUpEditCategory(){
        $url = WEB_PATH . 'quiz.html/editPRCCQuestions';
        $categoryId = $_POST['category_id'];
        $TextLabel = $_POST['respondent_text'];
        $TextLabelShort = $_POST['respondent_text_short'];
        $label = htmlspecialchars($TextLabel);
        $labelShort = htmlspecialchars($TextLabelShort);
        $TemplatePrccCategoryRepository = new TemplatePrccCategoryRepository();
        $TemplatePrccCategoryRepository->UpdateLabelByCategoryId($categoryId, $label, $labelShort);
        Appy::redirigeVers($url);
    }

    public function submitPRCCOptions() {
        $url = WEB_PATH . 'quiz.html';
        $quizId = $_POST['quizId'];
        $anonymousBroadcast = isset($_POST['anonymousBroadcast']) ? 1 : 0;
        $autoUserId = isset($_POST['respondant_id']) ? $_POST['respondant_id'] : null;

        $UserRepository = new UsersRepository();
        $userauto = null;
        if($autoUserId) {
            $userauto = $UserRepository->getUserById($autoUserId);
        }
        $quizRepository = new QuizRepository();
        $existingQuiz = $quizRepository->GetQuizById($quizId);
        $logoPath = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['logo']['tmp_name'];
            $fileName = $_FILES['logo']['name'];
            $fileSize = $_FILES['logo']['size'];
            $fileType = $_FILES['logo']['type'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if (in_array($fileType, $allowedTypes) && $fileSize < 5000000) {
                $guid = bin2hex(random_bytes(16));
                $newFileName = $guid . '.' . $ext;
                $uploadFileDir = ($_SERVER['DOCUMENT_ROOT'] .
                    (\Appy\Src\Config::ENV == 'PROD' ? '/assets/images/logosClients/' : '/relais-managers-services/assets/images/logosClients/'));
                
                // S'assurer que le dossier existe
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                
                $dest_path = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $logoPath = $newFileName;
                } else {
                    echo 'Erreur lors de l\'upload du logo.';
                }
            } else {
                echo 'Fichier invalide ou trop volumineux.';
            }
        } else {
            $logoPath = $existingQuiz->logo ?? '';
        }

        $quiz = new Quiz();
        $quiz->id = $quizId;
        $quiz->name = $_POST['title'] ?? '';
        $quiz->startDate = $_POST['start_date'] ?? '';
        $quiz->endDate = $_POST['end_date'] ?? '';
        $quiz->reminderDate = $_POST['reminder_date'] ?? '';
        $quiz->colorForm = $_POST['color_form'] ?? '';
        $quiz->groupeId = $_POST['groupe_id'] ?? '';
        $quiz->autoUserId = $autoUserId;
        $quiz->autoUserLastName = $userauto->lastname ?? '';
        $quiz->autoUserFirstName = $userauto->firstname ?? '';
        $quiz->autoUserIdentifier = $userauto->identifier ?? '';
        $quiz->autoUserEmail = $userauto->email ?? '';
        $quiz->header = $_POST['header'] ?? '';
        $quiz->intro = $_POST['intro'] ?? '';
        $quiz->conclusion = $_POST['conclusion'] ?? '';
        $quiz->footer = $_POST['footer'] ?? '';
        $quiz->logo = $logoPath;
        $quiz->anonymous = $anonymousBroadcast;
        $quizRepository->UpdateOptionsQuiz($quiz);
        Appy::redirigeVers($url);
    }


    public function editSinglePRCC() {
        $quizId = $_GET['quizId'];
        $urlPopUpEditSingleQuestionPRCC = WEB_PATH . 'quiz.html/popUpEditSingleQuestionPRCC';
        $urlDeleteQuestionPRCC = WEB_PATH . 'quiz.html/deleteSingleQuestionPRCC';
        $QuizQuestionsRepository = new QuizQuestionRepository();
        $QuizRepository = new QuizRepository();
        $questions = $QuizQuestionsRepository->getQuestionsByQuizId($quizId);
        $quiz = $QuizRepository->getQuizById($quizId);

        $urlModeTest = "";
        if(\Appy\Src\Config::ENV == 'PROD') {
            $urlModeTest = \Appy\Src\Config::DOMAIN . "/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&modeTest=1";
        } else {
            $array_path = explode("/", $_SERVER['REQUEST_URI']);
            array_pop($array_path);
            $path       = implode("/", $array_path);
            $urlModeTest =  "http://localhost/relais-managers-services/quiz.html/ResponseQuiz?quizId=" . $quiz->identifier . "&modeTest=1";
        }

        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'EditSinglePRCC';
        self::showVue([
            'questions' => $questions,
            'quizId' => $quizId,
            'urlModeTest' => $urlModeTest,
            'urlPopUpEditSingleQuestionPRCC' => $urlPopUpEditSingleQuestionPRCC,
            'urlDeleteQuestionPRCC' => $urlDeleteQuestionPRCC
        ]);
    }

    public function popUpEditSingleQuestionPRCC() {
        $questionId = $_GET['questionId'];
        $quizId = $_GET['quizId'];
        $urlSavePopUpEditSingleQuestionPrcc = WEB_PATH . 'quiz.html/savePopUpEditSingleQuestionPrcc?quizId=' . $quizId;
        $QuizQuestionRepository = new QuizQuestionRepository();
        $question = $QuizQuestionRepository->getQuestionById($questionId);
        $questionText = strip_tags($question->label);
        self::$gabarit = 'gabaritAjax';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'PopUpEditSingleQuestionPrcc';
        self::showVue([
            'question' => $question,
            'questionText' => $questionText,
            'questionId' => $questionId,
            'urlSavePopUpEditSingleQuestionPrcc' => $urlSavePopUpEditSingleQuestionPrcc
        ]);
    }

    public function savePopUpEditSingleQuestionPrcc(){
        $questionId = $_POST['question_id'];
        $TextLabel = $_POST['single_respondent_text'];
        $quizId = $_GET['quizId'];
        $url = WEB_PATH . 'quiz.html/editSinglePRCC?quizId=' . $quizId;
        $label = '<div style="font-size: 16px; color: #696252;font-family:Trebuchet MS">' . htmlspecialchars($TextLabel) . "</div>";
        $quizQuestionRepository = new QuizQuestionRepository();
        $label_auto = null;
        $quizQuestionRepository->UpdateLabelsByQuestionIdAndQuizId($questionId, $quizId, $label, $label_auto);
        Appy::redirigeVers($url);

    }

    public function OptionsModeles() {
        $quizType = isset($_GET['quizType']) ? $_GET['quizType'] : null;
        $urlSaveModele = WEB_PATH . 'quiz.html/saveModele';
        $templateQuizOptionsRepository = new TemplateQuizOptionsRepository();
        $templateQuizOptions = $templateQuizOptionsRepository->getTemplateByQuizType($quizType);

        if ($templateQuizOptions && $templateQuizOptions->is360()) {
            $vue = 'OptionsModele360';
        } elseif ($templateQuizOptions && $templateQuizOptions->isBarom()) {
            $vue = 'OptionsModeleBarom';
        } elseif ($templateQuizOptions && $templateQuizOptions->isPRCC()) {
            $vue = "OptionsModelePRCC";
        }

        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . $vue;
        self::showVue([
            'urlSaveModele' => $urlSaveModele,
            'templateQuizOptions' => $templateQuizOptions
        ]);
    }



    public function saveModele()
    {
        $url = WEB_PATH . 'quiz.html';
        $quizType = $_POST['quiz_type'];
        $templateQuizOptions = new TemplateQuizOptions();
        $templateQuizOptions->colorForm = $_POST['color_form'] ?? null;
        $templateQuizOptions->header = $_POST['header'] ?? null;
        $templateQuizOptions->intro = $_POST['intro'] ?? null;
        $templateQuizOptions->conclusion = $_POST['conclusion'] ?? null;
        $templateQuizOptions->footer = $_POST['footer'] ?? null;
        $templateQuizOptions->ccP1L1 = $_POST['paragraph1_line1'] ?? '';
        $templateQuizOptions->ccP1L2 = $_POST['paragraph1_line2'] ?? '';
        $templateQuizOptions->ccP1L3 = $_POST['paragraph1_line3'] ?? '';
        $templateQuizOptions->ccP1L4 = $_POST['paragraph1_line4'] ?? '';
        $templateQuizOptions->ccP1L5 = $_POST['paragraph1_line5'] ?? '';
        $templateQuizOptions->ccP2L1 = $_POST['paragraph2_line1'] ?? '';
        $templateQuizOptions->ccP2L2 = $_POST['paragraph2_line2'] ?? '';
        $templateQuizOptions->ccP2L3 = $_POST['paragraph2_line3'] ?? '';
        $templateQuizOptions->ccP2L4 = $_POST['paragraph2_line4'] ?? '';
        $templateQuizOptions->ccP2L5 = $_POST['paragraph2_line5'] ?? '';
        $templateQuizOptions->ccP3L1 = $_POST['paragraph3_line1'] ?? '';
        $templateQuizOptions->ccP3L2 = $_POST['paragraph3_line2'] ?? '';
        $templateQuizOptions->ccP3L3 = $_POST['paragraph3_line3'] ?? '';
        $templateQuizOptions->ccP3L4 = $_POST['paragraph3_line4'] ?? '';
        $templateQuizOptions->ccP3L5 = $_POST['paragraph3_line5'] ?? '';
        $templateQuizOptionsRepository = new TemplateQuizOptionsRepository();
        $templateQuizOptionsRepository->UpdateTemplatesQuizOptions($templateQuizOptions, $quizType);
        Appy::redirigeVers($url);
    }

    public function GeneralSettings(){
        $urlSubmitGeneralSettings = WEB_PATH . 'quiz.html/submitGeneralSettings';
        $ParametersRepository = new ParametersRepository();
        $Parameters = $ParametersRepository->getAllparameters();
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'GeneralSettings';
        self::showVue([
            'urlSubmitGeneralSettings' => $urlSubmitGeneralSettings,
            'parameters' => $Parameters
        ]);
    }

    public function submitGeneralSettings(){
        $url = WEB_PATH . 'quiz.html';
        $nom = $_POST['contact_nom'];
        $telephone = $_POST['contact_telephone'];
        $ParametersRepository = new ParametersRepository();
        $ParametersRepository->updateParameters($nom, $telephone);
        Appy::redirigeVers($url);
    }

}
