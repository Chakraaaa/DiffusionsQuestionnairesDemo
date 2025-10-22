<?php
/**
 * \file  main.class.php
 * @brief Fichier du controleur principal du module utilisateur
 */
/**
 * @brief MAIN.PHP
 *
 * @version 1.1807
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2018, www.DavidSENSOLI.com
 */
define("DIR_MODULE", basename(__DIR__));

switch ($action) {
    case "account":
        Appy\Src\Core\Appy::getAuth()->restrict();
        new Appy\Modules\Membres\Controleurs\Account();
        break;

    case "confirm":
        new Appy\Modules\Membres\Controleurs\Confirm();
        break;

    case "forget":
        new Appy\Modules\Membres\Controleurs\Forget();
        break;

    case "login":
        new Appy\Modules\Membres\Controleurs\Login();
        break;

    case "logout":
        new Appy\Modules\Membres\Controleurs\Logout();
        break;

    case "register":
        //Appy\Src\Core\Appy::getAuth()->restrict();
        new Appy\Modules\Membres\Controleurs\Register();
        break;

    case "reset":
        new Appy\Modules\Membres\Controleurs\Reset();
        break;

    default:
        \Appy\Src\Core\Session::getInstance()->setFlash("danger", "Action inconnue !");
        Appy\Src\Core\redirige_vers(WEB_PATH);
        break;
}

