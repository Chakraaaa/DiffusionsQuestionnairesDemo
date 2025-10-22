<?php
/**
 * Controleur CRUD du module "Utilisateur"
 * 
 * (Crtl + H peut suffir à réutiliser)
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2020, www.DavidSENSOLI.com
 */

namespace Appy\Modules\Utilisateurs\Controleurs;

use Appy\Modules\Utilisateurs\Modeles as Modeles;
use Appy\Src\Repository\ActivityRepository;
use Appy\Src\Validator;

trait Crud
{

    /** Suppression d'une ligne */
    static private function delLigne()
    {
        parent::init(['utilisateur', 'flash']);
        $utilisateur = $_SESSION['utilisateur'];
        if (isset($_POST['del_ligne'])) {

            $ligne_id = filter_input(INPUT_POST, 'del_ligne', FILTER_VALIDATE_INT);
            $ligne    = new Modeles\User($ligne_id);
            $ligne->efface();
            //$activityRepository = new ActivityRepository();
            //$activityRepository->insert("UTILISATEUR SUPPRESSION", $utilisateur, "ID : " . $ligne_id);

            \Appy\Src\Core\Appy::redirigeVers(self::$url);
        }
    }

    /** Enregistrement d'une ligne */
    static private function saveLigne()
    {
        parent::init(['utilisateur', 'flash']);
        $utilisateur = $_SESSION['utilisateur'];

        if (isset($_POST['save_ligne'])) {

            $saisies   = Modeles\Inputs::saisies();
            $ligne_id  = filter_input(INPUT_POST, 'ligne_editee', FILTER_VALIDATE_INT);
            $validator = new Validator($saisies);
            //$validator->isOnlyOne("initiales", "utilisateurs", $ligne_id, "Ces initiales sont déja utilisées !");

            if ($validator->isValid()) {

                // Cas d'une création d'une ligne
                if (empty($ligne_id)) {
                    $ligne_id = (new Modeles\User())->nouveau();
                }

                $ligne = new Modeles\User($ligne_id);
                $ligne->enregistreUser($ligne_id, $saisies['nom'], $saisies['prenom'], $saisies['role']);
                $session = \Appy\Src\Core\Session::getInstance();
                $session->setFlash("success", "Les modifications ont été enregistrées.");
                //$activityRepository = new ActivityRepository();
                //$activityRepository->insert("UTILISATEUR MODIFICATION",  $utilisateur,$saisies['nom'] . " " . $saisies['prenom'] . " " . $saisies['role']);
                \Appy\Src\Core\Appy::redirigeVers(self::$url);
            } else {
                self::$msg_erreur['titre'] = "Erreur !";
                self::$msg_erreur['msg']   = implode("<br/>", $validator->getErrors());
                self::$msg_erreur['msg']   .= "<br/>Aucune saisie n'a été enregistrée...";
            }
        }
    }

    /** Edition d'une nouvelle ligne */
    static private function editLigne()
    {
        parent::init(['utilisateur', 'flash']);
        $utilisateur = $_SESSION['utilisateur'];

        if ($utilisateur->role == 1 || $utilisateur->role == 2) {
            if (isset($_GET['edit_ligne'])) {
                $id                 = filter_input(INPUT_GET, 'edit_ligne', FILTER_VALIDATE_INT);
                self::$ligne_editee = new Modeles\User($id);
            }
        } else {
            $session = \Appy\Src\Core\Session::getInstance();
            $session->setFlash("danger", "Opération non autorisée");
        }
    }

    /** Création d'une nouvelle ligne */
    static private function newligne()
    {
        if (isset($_GET['add_ligne'])) {
            self::$ligne_editee = new Modeles\User();
        }
    }

    /** Ajout d'un utilisateur */
    static private function newUser()
    {
        parent::init(['utilisateur', 'flash']);
        $utilisateur = $_SESSION['utilisateur'];
        if (isset($_POST['new_utilisateur'])) {

            $saisies   = Modeles\Inputs::new();
            $errors    = array();
            $validator = New \Appy\Src\Validator($saisies);

            $validator->isEmail("email", "Votre email n'est pas valide");
            if ($validator->isValid()) {
                $validator->isUniq("email", 'users', "Ce mail est déja utilisé");
            }

            if ($validator->isValid()) {

                self::register($saisies['password'], $saisies['email'], $saisies['nom'], $saisies['prenom'], $saisies['role'], 'FR');

                $session = \Appy\Src\Core\Session::getInstance();
                $session->setFlash("success", "Utilisateur créé avec succès ! <br/>Un email a été envoyé à l'utilisateur pour qu'il définisse son mot de passe.");
                \Appy\Src\Core\Appy::redirigeVers(WEB_PATH."utilisateurs.html");
            } else {

                self::$msg_erreur_create = $validator->getErrors();
            }
        }
    }

    static public function register($password, $email, $nom, $prenom, $role, $lang)
    {
        $pass  = password_hash($password, PASSWORD_BCRYPT);
        $token = \Appy\Src\Str::random(60);
        $sql = "INSERT INTO `users` SET `password` = '".$pass."', `email`= '".$email."', `confirmation_token` = '".$token."', `firstname` = '".$prenom."', `lastname` = '".$nom."', `role` = '".$role."', created_at = NOW() ;";

        \Appy\Src\Connexionbdd::query($sql);

        $user_id = \Appy\Src\Connexionbdd::lastInsertId();
        $lien    = self::getLien($user_id, $token, "confirm");

        \Appy\Src\Email::setDestinataires(array(array($email, " ")));
        \Appy\Src\Email::setCopiesCacheesA(array(array("vdepeyre@webeosolution.fr", " ")));
        \Appy\Src\Email::setSubject("Création de votre compte utilisateur");
        \Appy\Src\Email::setHtml("<p>Un compte vient de vous être créé !</p><p>Afin de valider votre compte et définir votre mot de passe, merci de cliquer sur le lien suivant : <a href='$lien'>$lien</a></p><p style='font-weight: bolder;'>N'oubliez pas de choisir un mot de passe sécurisé.</p>");
        \Appy\Src\Email::envoi();
    }

    static private function getLien($user_id, $token, $action)
    {

        $array_path = explode("/", $_SERVER['REQUEST_URI']);
        array_pop($array_path);
        $path       = implode("/", $array_path);

        $url = "";
        if(\Appy\Src\Config::ENV == "DEV") {
            $url = "http://".$_SERVER['SERVER_NAME'].$path."/membres.html/$action?id=".$user_id."&token=$token";
        } else {
            $url = "https://".$_SERVER['SERVER_NAME'].$path."/membres.html/$action?id=".$user_id."&token=$token";
        }

        return $url;
    }
}
