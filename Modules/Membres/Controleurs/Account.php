<?php

namespace Appy\Modules\Membres\Controleurs;

use Appy\Src\Core\Appy;

class Account
{
    static private $vue;
    static private $url;
    static private $errors;
    static private $session;
    static private $user;
    static private $validator;

    public function __construct()
    {
        self::$url       = WEB_PATH.'membres.html';
        self::$session   = \Appy\Src\Core\Session::getInstance();
        self::$validator = new \Appy\Src\Validator($_POST);

        self::checkPassword();
        self::checkEmail();

        self::setVue();
    }

    static private function getUser()
    {
        $user_id = self::$session->read('utilisateur')->id;
        $user    = \Appy\Src\Connexionbdd::query("SELECT * FROM users WHERE id = ?", [$user_id])->fetch();

        return $user;
    }

    static private function checkPassword()
    {

        if (!empty($_POST['password'])) {

            self::$validator->isConfirmed("password");

            if (!self::$validator->isValid()) {
                self::$session->setFlash("danger", "Les mots de passe ne sont pas identiques");
            } else {
                $user_id  = self::$session->read('utilisateur')->id;
                $auth     = \Appy\Src\Core\Appy::getAuth();
                $password = $auth->hashPassword($_POST["password"]);
                \Appy\Src\Connexionbdd::query("UPDATE users SET password = ? WHERE id = ?", [$password, $user_id]);
                self::$session->setFlash("success", "Votre mot de passe a été mis à jour.");

                $urlRedirect = WEB_PATH;

                Appy::redirigeVers($urlRedirect);
            }
        }
    }

    static private function checkEmail()
    {

        if (!empty($_POST['email'])) {

            if (self::$validator->isEmail("email", "Votre email n'est pas valide")) {
                self::$validator->isConfirmed("email", "Les emails ne sont pas identiques");
            }

            if (self::$validator->isValid()) {
                $user_id = self::$session->read('utilisateur')->id;
                \Appy\Src\Connexionbdd::query("UPDATE users SET email = ? WHERE id= ?", [$_POST['email'], $user_id]);
                self::$session->setFlash("success", "Votre email a été mis à jour.");
            } else {

                $errorMsg = self::$validator->getErrors();

                self::$session->setFlash("danger", $errorMsg['email']);
            }
        }
    }

    static private function prepareVue()
    {
        self::$user = self::getUser();
        self::$vue  = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'account';
    }

    static private function setVue()
    {
        self::preparevue();
        $vue = new \Appy\Src\Core\Vue(self::$vue);
        $vue->generer(
            array(
                "errors" => self::$errors,
                "url"    => self::$url,
                'user'   => self::$user
            )
        );
    }
}
