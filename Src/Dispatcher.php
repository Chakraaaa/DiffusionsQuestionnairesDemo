<?php
/**
 * Fichier de la classe du dispatcher pour les utilisateurs
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2019, www.DavidSENSOLI.com
 */

namespace Appy\Src;     

use Appy\Src\Command\RelanceEmailCommand;
use Appy\Src\Controller\UsersController;
use Appy\Src\Controller\QuizController;
use Appy\Src\Core\Appy;

class Dispatcher
{
    static public function membres($id = NULL, $action = NULL)
    {
        $fichier = '../Modules'.DS.'Membres'.DS.'main.php';
        require($fichier);
    }

    static public function utilisateurs($id = NULL, $action = NULL)
    {
        Core\Appy::getAuth()->restrict(WEB_PATH."membres.html/login");
        $fichier = '../Modules'.DS.'Utilisateurs'.DS.'main.php';
        require($fichier);
    }

    /**
     * Affiche la liste des utilisateurs
     */
    static public function users($id = NULL, $action = NULL)
    {
        Core\Appy::getAuth()->restrict(WEB_PATH . "membres.html/login");
        $controller = new \Appy\Src\Controller\UsersController();
        if ($action == 'delete') {
            $controller->delete();
        } else if ($action == "createUser" || $action == "createUsers") {
            $controller->create();
        } else if ($action == "deleteMultiple") {
            $controller->DeleteMultipleUsers();
        } else if ($action == "groupeMultiple") {
            $controller->GroupeMultipleUsers();
        }  else if ($action == "ImportUsers") {
            $controller->ImportUsers();
        }  else if ($action == "editUser") {
            $controller->EditUser();
        }  else if ($action == "ResetUsers") {
            $controller->ResetUsers();
        }  else {
            $controller->recherche();
        }
    }

    static public function Command($id = NULL, $action = NULL)
    {
        switch ($action) {
            case 'relanceEmail':
                $controller = new RelanceEmailCommand();
                $controller->execute();
                break;
        }
    }

    static public function groups($id = NULL, $action = NULL)
    {
        Core\Appy::getAuth()->restrict(WEB_PATH . "membres.html/login");
        $controller = new \Appy\Src\Controller\UsersController();
        if ($action == 'delete') {
            $controller->deleteGroupe();
        } else if ($action == "createGroupe") {
            $controller->createGroupe();
        }   else if ($action == "editGroup") {
            $controller->EditGroup();
        }   else if ($action == "addGroup") {
            $controller->addGroup();
        }   else {
            $controller->rechercheGroupes();
        }
    }

    static public function c($id = NULL, $action = NULL)
    {
        $controller = new \Appy\Src\Controller\QuizController();

        //si l'action fait 16 caractères  c'est une url de convocation avec identification automatique
        //sinon c'est une url de convication anonyme pour laquelle on doit rediriger vers un ecran de login
        if (strlen($action) == 16) {

            $quizId = substr($action, 0, 6);
            $userIdentifier = substr($action, 6);

            $urlResponse = "";
            if(\Appy\Src\Config::ENV == 'PROD') {
                $urlResponse = \Appy\Src\Config::DOMAIN . "/quiz.html/ResponseQuiz?quizId=" . $quizId . "&identifier=" . $userIdentifier;
            } else {
                $array_path = explode("/", $_SERVER['REQUEST_URI']);
                array_pop($array_path);
                $path       = implode("/", $array_path);
                $urlResponse =  "http://localhost/relais-managers-services/quiz.html/ResponseQuiz?quizId=" . $quizId . "&identifier=" . $userIdentifier;
            }

            Appy::redirigeVers($urlResponse);

        } else {
            $controller->loginAnonymous($action);
        }
    }


    static public function quiz($id = NULL, $action = NULL)
    {
        if ($action != "ResponseQuiz" &&
            $action != "saveResponse" &&
            $action != "saveCritere" &&
            $action != "ResponseQuiz" &&
            $action != "remerciement" &&
            $action != "finish" &&
            $action != "checkLoginAnonymous" &&
            $action != "HorsDates") {
            Core\Appy::getAuth()->restrict(WEB_PATH . "membres.html/login");
        }
        $controller = new \Appy\Src\Controller\QuizController();
        if ($action == 'delete') {
            $controller->delete();
        } else if ($action == "createQuiz") {
            $controller->createNewQuiz();
        } else if ($action == "PublishQuiz") {
            $controller->showPublishQuiz();
        }  else if ($action == "SendQuiz") {
            $controller->SendQuizEmails();
        }  else if ($action == "ResponseQuiz") {
            $controller->respondToQuiz();
        }  else if ($action == "saveResponse") {
            $controller->saveResponse();
        }  else if ($action == "saveCritere") {
            $controller->saveCritere();
        }  else if ($action == "SuiviQuiz") {
            $controller->suiviQuiz();
        }  else if ($action == "quizOptions") {
            $controller->quizOptions();
        }  else if ($action == "SubmitQuizOptions") {
            $controller->submitQuizOptions();
        }  else if ($action == "SubmitBarometreOptions") {
            $controller->submitBarometreOptions();
        }  else if ($action == "SubmitPrccOptions") {
            $controller->SubmitPrccOptions();
        }  else if ($action == "fetchRespondants") {
            $controller->fetchRespondants();
        }  else if ($action == "fetchUsers") {
            $controller->fetchRespondants();
        }  else if ($action == "fetchGroupInfo") {
            $controller->fetchGroupInfo();
        }  else if ($action == "templatesEmails") {
            $controller->getEmailTemplateMessage();
        }  else if ($action == "PopupEmailsDetails") {
            $controller->EmailsDetails();
        }  else if ($action == "SaveTemplateEmail") {
            $controller->SaveTemplateEmail();
        }  else if ($action == "DeleteTemplateEmail") {
            $controller->DeleteTemplateEmail();
        }  else if ($action == "HorsDates") {
            $controller->HorsDates();
        }  else if ($action == "remerciement") {
            $controller->Remerciement();
        }  else if ($action == "notFound") {
            $controller->notFound();
        }  else if ($action == "finish") {
            $controller->finish();
        }  else if ($action == "editBaromQuestions") {
            $controller->editBaromQuestions();
        }  else if ($action == "savePopUpEditChapterBarom") {
            $controller->savePopUpEditChapterBarom();
        }  else if ($action == "savePopUpEditRadioBarom") {
            $controller->savePopUpEditRadioBarom();
        }  else if ($action == "edit360Questions") {
            $controller->edit360Questions();
        }   else if ($action == "editPRCCQuestions") {
            $controller->editPRCCQuestions();
        }  else if ($action == "editSingleQuiz360") {
            $controller->editSingleQuiz360();
        }  else if ($action == "editSingleBarometre") {
            $controller->editSingleBarometre();
        }  else if ($action == "editSinglePRCC") {
            $controller->editSinglePRCC();
        }  else if ($action == "popUpEditSingleQuestionBarom") {
            $controller->popUpEditSingleQuestionBarom();
        }  else if ($action == "popUpEditSingleQuestionPRCC") {
            $controller->popUpEditSingleQuestionPRCC();
        }  else if ($action == "deleteQuestion360") {
            $controller->deleteQuestion360();
        }  else if ($action == "deleteSingleQuestion360") {
            $controller->deleteSingleQuestion360();
        }  else if ($action == "popUpEditQuestion") {
            $controller->popUpEditQuestion();
        }  else if ($action == "popUpEditQuestionPRCC") {
            $controller->popUpEditQuestionPRCC();
        }  else if ($action == "popUpEditCategoryPRCC") {
            $controller->popUpEditCategoryPRCC();
        }  else if ($action == "popUpEditSingleQuestion") {
            $controller->popUpEditSingleQuestion();
        }  else if ($action == "popUpEditQuestionBarom") {
            $controller->popUpEditQuestionBarom();
        }   else if ($action == "savePopUpEditSingleRadioBarom") {
            $controller->savePopUpEditSingleRadioBarom();
        }  else if ($action == "savePopUpEditSingleQuestionPrcc") {
            $controller->savePopUpEditSingleQuestionPrcc();
        }  else if ($action == "savePopUpEditSingleChapterBarom") {
            $controller->savePopUpEditSingleChapterBarom();
        }  else if ($action == "savePopUpEditChapter360") {
            $controller->savePopUpEditChapter360();
        }  else if ($action == "savePopUpEditSingleChapter360") {
            $controller->savePopUpEditSingleChapter360();
        }  else if ($action == "savePopUpEditRadio360Text") {
            $controller->savePopUpEditRadio360Text();
        }  else if ($action == "savePopUpEditSingleRadio360Text") {
            $controller->savePopUpEditSingleRadio360Text();
        }  else if ($action == "savePopUpEditRadio360List") {
            $controller->savePopUpEditRadio360List();
        }  else if ($action == "savePopUpEditSingleRadio360List") {
            $controller->savePopUpEditSingleRadio360List();
        }  else if ($action == "savePopUpEditQuestionPRCC") {
            $controller->savePopUpEditQuestionPRCC();
        }  else if ($action == "savePopUpEditCategory") {
            $controller->savePopUpEditCategory();
        }  else if ($action == "createNewChapter360") {
            $controller->createNewChapter360();
        }  else if ($action == "createNewChapterSingle360") {
            $controller->createNewSingleChapter360();
        }  else if ($action == "createNewRadio360Text") {
            $controller->createNewRadio360Text();
        }  else if ($action == "createNewRadioSingle360Text") {
            $controller->createNewSingleRadio360Text();
        }  else if ($action == "createNewRadio360List") {
            $controller->createNewRadio360List();
        }  else if ($action == "createNewRadioSingle360List") {
            $controller->createNewSingleRadio360List();
        }  else if ($action == "checkLoginAnonymous") {
            $controller->checkLoginAnonymous();
        } else if ($action == "courrier") {
            $controller->createCourrier();
        } else if ($action == "OptionsModeles") {
            $controller->OptionsModeles();
        } else if ($action == "saveModele") {
            $controller->saveModele();
        } else if ($action == "GeneralSettings") {
            $controller->GeneralSettings();
        } else if ($action == "submitGeneralSettings") {
            $controller->submitGeneralSettings();
        } else {
            $controller->rechercheQuiz();
        }
    }

    static public function report($id = NULL, $action = NULL)
    {
        Core\Appy::getAuth()->restrict(WEB_PATH . "membres.html/login");
        $controller = new \Appy\Src\Controller\QuizController();
        $controller360 = new \Appy\Src\Controller\Report360Controller();
        $controllerPrcc = new \Appy\Src\Controller\ReportPrccController();
        $controllerBarometre = new \Appy\Src\Controller\ReportBarometreController();
        if ($action == "generate360") {
            $controller360->generate();
        }else if ($action == "generatePRCC") {
            $controllerPrcc->generate();
        }else if ($action == "data360") {
            $controller360->data();
        }else if ($action == "dataPRCC") {
            $controllerPrcc->data();
        }else if ($action == "dataBAROM") {
            $controllerBarometre->generateExel();
        } else if ($action == "SubmitBaromReport") {
            $controllerBarometre->submitBaromReport();
        } else if ($action == "generateWord") {
            $controllerBarometre->generateWord();
        } else if ($action == "generateReportBAROM") {
            $controllerBarometre->generateReport();
        } else {
            $controller->quizReport();
        }
    }

    static public function accueil()
    {
        Core\Appy::getAuth()->restrict(WEB_PATH."membres.html/login");
        $utilisateur = $_SESSION['utilisateur'];
        $controller = new QuizController();
        $controller->rechercheQuiz();
    }
    static public function erreur($erreur)
    {
        $vue = new Core\Vue('../vues'.DS.'erreur');
        $vue->generer(array(
            'msg_erreur' => $erreur
        ));
    }
}
