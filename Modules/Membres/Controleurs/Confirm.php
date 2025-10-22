<?php

namespace Appy\Modules\Membres\Controleurs;

class Confirm
{
    static private $url;

    public function __construct()
    {
        self::$url = WEB_PATH.'membres.html';

        self::checkTokenDeConfirmation();
    }

    private function checkTokenDeConfirmation()
    {
        $session = \Appy\Src\Core\Session::getInstance();
        $user_id = intval($_GET['id']);
        $token   = $_GET['token'];
        $auth    = \Appy\Src\Core\Appy::getAuth();

        if ($auth->confirm($user_id, $token)) {
            $session->setFlash("success", "Votre compte a bien été validé.<br/>Merci de définir votre mot de passe.");
            \Appy\Src\Core\Appy::redirigeVers(self::$url."/account");
        } else {

            $session->setFlash("danger", "Ce lien n'est plus valide.");
            \Appy\Src\Core\Appy::redirigeVers(self::$url."/login");
        }
    }
}
