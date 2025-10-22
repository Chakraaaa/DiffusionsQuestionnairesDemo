<?php

namespace Appy\Modules\Membres\Controleurs;
use Appy\Src\Repository\ActivityRepository;

class Login
{
    static private $auth;
    static private $vue;
    static private $url;
    static private $errors;

    public function __construct()
    {
        self::$url  = WEB_PATH.'membres.html/login';
        self::$auth = \Appy\Src\Core\Appy::getAuth();

        if (self::$auth->user()) {
            \Appy\Src\Core\Appy::redirigeVers(WEB_PATH."membres.html/account");
        }

        self::checkPOST();

        self::setVue();
    }

    static private function checkPOST()
    {
        $session = \Appy\Src\Core\Session::getInstance();

        if (!empty($_POST) AND ! empty($_POST['password'])) {

            $user = self::$auth->login($_POST['email'], $_POST['password'], isset($_POST['remember']));

            if ($user) {

                $user = $_SESSION['utilisateur'];
                $message = "IP : " . $_SERVER['REMOTE_ADDR'];

                //$activityRepository = new ActivityRepository();
                //$activityRepository->insert("UTILISATEUR CONNEXION", $user, $message);

                self::$auth->setLastConnexion($user);

                if ($session->read('page_demandee')) {
                    \Appy\Src\Core\Appy::redirigeVers($session->read('page_demandee'));
                } else {
                    //$session->setFlash("success", "Vous êtes connecté.");
                    \Appy\Src\Core\Appy::redirigeVers(WEB_PATH);
                }
            } else {
                $session->setFlash("danger", "Les identifiants sont incorrects !");
            }
        } else {
            if (!empty($_POST)) {
                $session->setFlash("danger", "Vous n'avez pas rempli tous les champs !");
            }
        }
    }

    static private function prepareVue()
    {
        self::$vue = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'login';
    }

    static private function setVue()
    {
        self::preparevue();
        $vue = new \Appy\Src\Core\Vue(self::$vue);
        $vue->generer(
            array(
                "errors" => self::$errors,
                "url"    => self::$url,
            )
        );
    }
}
