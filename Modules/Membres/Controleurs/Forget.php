<?php

namespace Appy\Modules\Membres\Controleurs;

class Forget
{
    static private $vue;
    static private $url;

    public function __construct()
    {
        self::$url = WEB_PATH.'membres.html';

        self::checkPOST();

        self::setVue();
    }

    static private function checkPOST()
    {

        if (!empty($_POST) AND ! empty($_POST['email'])) {

            $auth = \Appy\Src\Core\Appy::getAuth();

            if ($auth->resetPassword($_POST['email'])) {

                \Appy\Src\Core\Session::getInstance()->setFlash("success", "Les instructions de rappel de votre mot de passe vous ont été envoyé par mail.");
                \Appy\Src\Core\Appy::redirigeVers(self::$url."/login");
            } else {
                \Appy\Src\Core\Session::getInstance()->setFlash("danger", "Aucun compte ne correspond à cet email.");
            }
        }
    }

    static private function prepareVue()
    {

        self::$vue = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'forget';
    }

    static private function setVue()
    {
        self::preparevue();
        $vue = new \Appy\Src\Core\Vue(self::$vue);
        $vue->generer(
            array(
                "url" => self::$url,
            )
        );
    }
}
