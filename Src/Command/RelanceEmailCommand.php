<?php

namespace Appy\Src\Command;

use Appy\Src\Repository\QuizRepository;
use Appy\Src\Repository\QuizUserRepository;
use Appy\Src\Repository\TemplateEmailRepository;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RelanceEmailCommand  extends \Appy\Src\Core\Module
{
    public function execute()
    {
        //Vérification du token de securité
        if ($_GET['secure-token'] == "5df524fNNfgD84fsjf1dsfd54ZRH") {

            $currentDate = new \DateTime();
            $currentDateFormatted = $currentDate->format('Y-m-d');
            //$currentDateFormatted = "2025-01-22";

            //On recherche en base les questionnaire qui ont une date de relance à la date du jour
            //Et ce uniquement sur les formaulaire qui ne sont pas anonyme (sur les anonyme on a pas d'adresse email

            $criteres = [
                'reminder_date' => $currentDateFormatted,
                'date_end_not_passed' => $currentDateFormatted,
                'anonymous' => false,
                'deleted' => false
            ];

            $quizRepository = new QuizRepository();
            $quizzes = $quizRepository->getQuizzes($criteres);
            //var_dump($quizzes);

            //Pour chaque questionnaire  on recupere la liste des quiz_user qui n'ot pas finalisé le questionnaire
            //Et pour chacun on envoi un email de relance

            foreach ($quizzes as $quiz) {

                $quizUserRepository = new QuizUserRepository();
                $criteresUser = ['status_not' => 'FINISH'];
                $quizUsers = $quizUserRepository->getQuizUsersByQuizId($quiz->id, $criteresUser);
                //var_dump($quizUsers);

                $templateEmailRepository = new TemplateEmailRepository();
                $emailTemplate = $templateEmailRepository->getEmailTemplateByType($quiz);

                foreach ($quizUsers as $user) {

                    if($user->userEmail != "") {
                        $urlResponse = "";
                        if(\Appy\Src\Config::ENV == 'PROD') {
                            $urlResponse = htmlspecialchars(\Appy\Src\Config::DOMAIN . "/" . $quiz->identifier . $user->userIdentifier);
                        } else {
                            $urlResponse =  "http://localhost/relais-managers-services/" . $quiz->identifier . $user->userIdentifier;
                        }

                        $subject = "Votre questionnaire en ligne \"" . $quiz->name . "\" n'est pas encore validé";

                        $messageMail = $this->replaceVariables($emailTemplate->message, $user, $quiz, $urlResponse);

                        \Appy\Src\Email::setDestinataires(array(array($user->userEmail, " ")));
                        //\Appy\Src\Email::setDestinataires(array(array("vdepeyre@yahoo.fr", " ")));
                        \Appy\Src\Email::setCopiesCacheesA(array(array("vdepeyre@webeosolution.fr", " ")));
                        \Appy\Src\Email::setSubject($subject);
                        \Appy\Src\Email::setHtml($messageMail);
                        \Appy\Src\Email::envoi();
                    }
                }
            }
        }
    }

    private function replaceVariables($message, $user, $quiz, $urlResponse)
    {
        $startDate = date('d-m-Y', strtotime($quiz->startDate));
        $endDate = date('d-m-Y', strtotime($quiz->endDate));

        $message = str_replace('[IDENTIFIANT]', $user->userIdentifier, $message);
        $message = str_replace('[NOM]', $user->userLastName, $message);
        $message = str_replace('[PRENOM]', $user->userFirstName, $message);
        $message = str_replace('[URL]', $urlResponse, $message);
        $message = str_replace('[TITRE]', $quiz->name, $message);
        $message = str_replace('[DATE_DEBUT]', $startDate, $message);
        $message = str_replace('[DATE_FIN]', $endDate, $message);

        $message = str_replace('a href', 'a style="color:#E9660B" href', $message);

        return $message;
    }
}
