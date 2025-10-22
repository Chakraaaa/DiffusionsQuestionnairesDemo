<?php

namespace Appy\Modules\Membres\Controleurs;

class Logout
{
    static private $url;

    public function __construct()
    {
        self::$url = WEB_PATH.'membres.html';

        self::logout();
    }

    private function logout()
    {
        $auth = \Appy\Src\Core\Appy::getAuth();
        $auth->logout();
        //\Appy\Src\Core\Session::getInstance()->setflash("success", "Vous avez bien été déconnecté");

        \Appy\Src\Core\Appy::redirigeVers(self::$url."/login");
    }
}
