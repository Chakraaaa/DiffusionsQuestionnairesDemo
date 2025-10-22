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
use Appy\Src\Select;

class Edit extends \Appy\Src\Core\Module
{
    /** Attributs à destination de la vue */
    static private $role;
    static private $msg_erreur_create = [];
    static private $ligne_editee      = 0;
    static private $liste;
    static private $msg_erreur        = [];
    static private $url;

    use Crud;

    /** Chargement du controleur */
    static public function load()
    {
        parent::init(['utilisateur', 'flash']);
        self::$url         = WEB_PATH.'utilisateurs.html';

        self::delLigne();
        self::editLigne();
        self::saveLigne();
        self::newLigne();
        self::newUser();

        self::setVue();
    }

    /** Gère les variables */
    static private function prepareVue()
    {
        if (!empty(self::$msg_erreur_create)) {

            self::$vue                 = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'new';
            self::$msg_erreur['titre'] = "Erreur à la création de l'utilisateur !";
            self::$msg_erreur['msg']   = self::$msg_erreur_create;

            return;
        }

        if (is_object(self::$ligne_editee) OR isset($_GET['add_ligne'])) {

            if (isset($_GET['add_ligne']))
            {

                /** NOUVEL UTILISATEUR */
                self::$vue = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'new';
            }
            else
            {
                /** EDITION */
                self::$vue               = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'edition';
                self::$role              = Modeles\Fonctions::getRole(self::$ligne_editee->role);
            }
        } else {

            /** LISTE */
            self::$liste = Modeles\Fonctions::getAllUtilisateurs();
            self::$vue   = BASE_PATH.'Modules'.DS.DIR_MODULE.DS.'vues'.DS.'liste';
        }
    }

    /** Affiche la vue */
    static private function setVue()
    {
        self::prepareVue();

        self::showVue([
            'ligne_editee'            => self::$ligne_editee,
            'liste'                   => self::$liste,
            'msg_erreur'              => self::$msg_erreur,
            'url_controleur'          => self::$url,
        ]);
    }
}
