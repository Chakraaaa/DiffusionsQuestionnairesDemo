<?php

namespace Appy\Modules\Membres\Controleurs;

class Reset
{
    static private $vue;
    static private $url;

    public function __construct()
    {
        self::$url = WEB_PATH.'membres.html';

        self::checkGET();

        self::setVue();
    }

    static private function checkGET()
    {
        $session = \Appy\Src\Core\Session::getInstance();
        if (isset($_GET['id']) AND isset($_GET['token'])) {

            $auth = \Appy\Src\Core\Appy::getAuth();
            $user = $auth->checkResetToken($_GET['id'], $_GET['token']);

            if ($user) {

                if (!empty($_POST)) {

                    $validator = new \Appy\Src\Validator($_POST);
                    $validator->isConfirmed("password");
                    if ($validator->isValid()) {

                        $password = $auth->hashPassword($_POST['password']);

                        \Appy\Src\Connexionbdd::query("UPDATE users SET password = ?, reset_token = NULL , reset_at = NULL WHERE id = ?", [$password, $user->id]);

                        $session->setFlash("success", "Votre mot de passe a bien été modifié.");
                        $session->write('utilisateur', $user);

                        \Appy\Src\Core\Appy::redirigeVers(self::$url."/login");
                    } else {
                        $session->setFlash("danger", "Les mots de passe ne sont pas identiques");
                    }
                }
            } else {
                $session->setFlash("danger", "Ce lien n'est pas ou plus valide.");
                \Appy\Src\Core\Appy::redirigeVers(self::$url."/login");
            }
        } else {
            \Appy\Src\Core\Appy::redirigeVers(WEB_PATH);
        }
    }

    static private function prepareVue()
    {

        self::$vue = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'reset';
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
