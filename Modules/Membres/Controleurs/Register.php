<?php

namespace Appy\Modules\Membres\Controleurs;

use Appy\Modules\Membres\Modeles as Modeles;

class Register
{
    static private $vue;
    static private $url;
    static private $errors;

    public function __construct()
    {
        self::$url = WEB_PATH.'membres.html';

        self::checkPOST();

        self::setVue();
    }

    static private function checkPOST()
    {

        if (!empty($_POST)) {

            $errors    = array();
            $validator = New \Appy\Src\Validator($_POST);

            $validator->isEmail("email", "Votre email n'est pas valide");
            if ($validator->isValid()) {
                $validator->isUniq("email", 'users', "Ce mail est déja utilisé");
            }

            if ($validator->isValid()) {

                $session = \Appy\Src\Core\Session::getInstance();
                $auth    = new Modeles\Auth($session);
                $id = $auth->register($_POST['email'], $_POST['email']);

                $session->setFlash("success", "Un email de validation vous a été envoyé pour finir de valider votre compte.");
                \Appy\Src\Core\Appy::redirigeVers(WEB_PATH . 'utilisateurs.html?edit_ligne='.$id);
            } else {
                self::$errors = $validator->getErrors();
            }
        }
    }

    static private function prepareVue()
    {

        self::$vue = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'register';
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
