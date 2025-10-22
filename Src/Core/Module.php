<?php
/**
 * Classe parente d'un controleur de module
 *
 * Gère les variables de SESSION et affiche la vue
 *
 * @author    David SENSOLI <dsensoli@gmail.com>
 * @copyright 2019 http://www.DavidSENSOLI.com
 *
 * @version 5.1910 - Passage des controleurs et du Module en static complet + gestion des erreurs avec \Throwable
 * @version 4.1907 - Création de l'attribut protected $session pour l'utiliser dans le controleur
 * 
 */

namespace Appy\Src\Core;

use Appy\Src\Core\Session;

class Module
{
    static protected $gabarit;
    static protected $lang = 'fr';
    static protected $session;
    static protected $vue;

    /**
     *  Initialise le module et efface les variables de SESSION sauf celles voulues par le controleur
     *
     *  Les variables maintenues en session par le controleur sont passés par l'array $arr_session
     *
     * @param array $arr_session
     */
    static protected function init(array $arr_session = NULL)
    {
        try {

            self::$session = Session::getInstance();

            if (!is_null($arr_session)) {
                foreach ($_SESSION as $key => $value) {
                    if (!in_array($key, $arr_session)) {
                        unset($_SESSION[$key]);
                    }
                }
            }

            $utilisateur = self::$session->read('utilisateur');

            if (is_object($utilisateur)) {
                if (!empty($utilisateur->lang_site)) {
                    self::$lang = $utilisateur->lang_site;
                } else {
                    $utilisateur->lang_site = 'FR';
                    self::$session->write('utilisateur', $utilisateur);
                    self::$lang             = $utilisateur->lang_site;
                }
            }
        } catch (\Throwable $ex) {
            throw new \Exception("<strong>Erreur ! Ligne ".$ex->getLine()." - Méthode ".__METHOD__." - Classe ".__CLASS__." - Fichier ".$ex->getFile()."</strong><br/><br/>".$ex->getMessage());
        }
    }

    /**
     * Affiche la vue
     *
     * @param array $vars_vue
     */
    static protected function showVue(array $vars_vue)
    {
        try {
            $vue = new Vue(self::$vue);

            if (!empty(self::$gabarit)) {
                $vue->generer($vars_vue, self::$gabarit);
            } else {
                $vue->generer($vars_vue);
            }
        } catch (\Throwable $ex) {
            throw new \Exception("<strong>Erreur ! Ligne ".$ex->getLine()." - Méthode ".__METHOD__." - Classe ".__CLASS__." - Fichier ".$ex->getFile()."</strong><br/><br/>".$ex->getMessage());
        }
    }
}
