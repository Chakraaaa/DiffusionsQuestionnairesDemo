<?php
/**
 * Classe de lancement de l'application
 *
 * @version 5.2012 - Ajout de la méthode insertTraductions()
 * @version 5.2009 - Suppression de la méthode loadJS()
 * @version 4.2007 - Amélioration du message d'erreur
 * @version 4.1912 - Ajout de la méthode loadJS()
 * @version 3.1910 - Passage en Throwable pour les erreurs
 * @version 3.1902 - Changement du routeur
 * @version 3.1901 - Suppression de la fonction load_modeles()
 * @version 2.1810 - Autoloading par Composer
 * @version 2.1804 - Utilisation de la classe statique Config
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2020, www.DavidSENSOLI.com
 */

namespace Appy\Src\Core;

class Appy
{
    private static $session;

    /**
     * Lance l'application
     */
    public static function run()
    {
        if (\Appy\Src\Config::SAUVEGARDE_ACTIVE) {
            \Appy\Src\Dump::start();
        }

        self::$session = Session::getInstance();

        self::getAuth()->connectFromCookie();

        self::runRouter();
    }

    /**
     * Lance le routeur
     *
     * @see http://altorouter.com/
     */
    private static function runRouter()
    {

        $router = new Router();

        $web_path = substr_replace(WEB_PATH, "", -1); // On enlève le "/" pour ne pas gêner le fonctionnement du routeur
        $router->setBasePath($web_path);

        $router->map('GET', "/", function() {
            self::dispatch("accueil");
        }, "accueil");
        $router->map('GET|POST', "/[a:c].html/[a:a]?", function($params) {
            $controleur = $params['c'];
            $action = isset($params['a']) ? $params['a'] : NULL;
            self::dispatch($controleur, $action);
        });


        $router->map('GET|POST', "/[a:c]-[i:id].html/[a:a]?", function($params) {
            $controleur = $params['c'];
            $id = $params['id'];
            $action = isset($params['a']) ? $params['a'] : NULL;
            self::dispatch($controleur, $action, $id);
        });

        $match = $router->match();

        if (is_array($match) && is_callable($match['target'])) {
            call_user_func($match['target'], $match['params']);
        } else {
            \Appy\Src\Dispatcher::erreur("Erreur d'URL ! Le routeur n'a pas identifié la route demandée ou la fonction n'existe pas dans le dispatcher... ");
        }
    }

    /**
     * Dispatch le controleur suivant la réponse du routeur et récupère les exceptions si besoin
     *
     * @param string $controleur
     * @param string $action
     * @param int $id
     */
    private static function dispatch(string $controleur, string $action = NULL, int $id = NULL)
    {
        try {
            if (method_exists('\Appy\Src\Dispatcher', $controleur)) {

                // a commenter
                //self::isApplicationAutorisee($controleur);

                \Appy\Src\Dispatcher::$controleur($id, $action);
            } else {
                throw new \Exception("La méthode $controleur() n'existe pas dans la classe dispatcher");
            }
        } catch (\Throwable $e) {
            \Appy\Src\Dispatcher::erreur('Erreur dans '.$e->getFile().' - Line <strong>'.$e->getLine().'</strong><br/><strong>'.$e->getMessage().'</strong>');
        }
    }

    public static function getAuth()
    {
        return new \Appy\Modules\Membres\Modeles\Auth(
            self::$session, array(
            //'restriction_msg' => "Vous n'avez pas le droit d'accéder à cette page !",
            'redirected_url'  => WEB_PATH."membres.html/login"
            )
        );
    }

    /**
     * Affiche la variable pour du debug et die() le script
     *
     * @param Var $variable
     * @param Boolean $die
     */
    public static function debug($variable, $die = false)
    {
        echo '<pre>';
        print_r($variable);
        echo '</pre>';

        if ($die) {
            die();
        }
    }

    /**
     * Redirige vers une page en testant si les entêtes ont déja été envoyés
     *
     * @param String $url
     */
    public static function redirigeVers($url)
    {
        if (!headers_sent($filename, $linenum)) {
            header("Location: $url");
            exit;
        } else {
            die(utf8_decode("Les en-têtes ont déja été envoyés, depuis le fichier $filename à la ligne $linenum.\n"));
        }
    }

    /**
     * Charge la liste des fichiers .css inclus dans un array
     *
     * <p>2 possibilités :</p>
     * <p>Le nom du fichier seul (chargé dans le répertoire css de l'application)</p>
     * <p>le nom du module / le répertoire CSS / Le nom du fichier</p>
     *
     * @param Array $array_files_css
     * @return string $html la balise meta html
     */
    public static function loadCSS($array_files_css)
    {

        $html = "";

        if (!empty($array_files_css)) {

            foreach ($array_files_css as $fichier_css) {

                $chemin_css    = explode("/", $fichier_css);
                $path_file_css = implode(DS, $chemin_css); // On récréé le chemin pour la fonction filemtime des modules

                if (count($chemin_css) == 1) {
                    $html .= "<link rel='stylesheet' href='".WEB_PATH."assets/css/".$fichier_css.".css?v=".filemtime(BASE_PATH."assets".DS."css".DS."$fichier_css.css")."'>";
                } else {
                    $html .= "<link rel='stylesheet' href='".WEB_PATH."Modules/".$fichier_css.".css?v=".filemtime(BASE_PATH."Modules".DS."$path_file_css.css")."'>";
                }

                unset($chemin_css, $path_file_css);
            }

            return $html;
        }
    }

    /**
     * Renvoie l'array des traductions de la langue sélectionnée
     *
     * Cherche la langue prévue, renvoie FR si non trouvé, ou bien une exception
     *
     * @param string $path
     * @param string $lang
     * @return Array $traductions des vues
     * @throws \Exception
     */
    public static function insertTraductions(string $path, string $lang)
    {
        if (file_exists(BASE_PATH.$path.DS.$lang.".php")) {
            $file_traduction = BASE_PATH.$path.DS.$lang.".php";
        } else {
            if (file_exists(BASE_PATH.$path.DS."fr.php")) {
                $file_traduction = BASE_PATH.$path.DS."fr.php";
            } else {
                throw new \Exception("Aucun ficher de traduction trouvé !");
            }
        }

        require_once $file_traduction;

        return $traductions;
    }

    private static function isApplicationAutorisee($controleur)
    {
        try {
            $sql         = "SELECT `id`, `dispatcher` FROM `applications` WHERE `applications`.`dispatcher` LIKE '$controleur'";
            $application = \Appy\Src\Connexionbdd::query($sql)->fetch();

            if (!$application) {
                return;
            } else {
                $session     = Session::getInstance();
                $utilisateur = $session::read('utilisateur');
                if ($utilisateur->role < UTILISATEUR) {
                    return;
                } else {
                    $applications_autorisees = json_decode($utilisateur->applications, true);
                    if (in_array($application->id, $applications_autorisees)) {
                        return true;
                    } else {
                        $log = date('d/m/Y H:i')." - Dispatcher bloqué : ".$application->dispatcher." - Utilisateur : ID ".$utilisateur->id." ".$utilisateur->prenom." ".$utilisateur->nom." - Dernière IP :".$utilisateur->last_ip."\n";
                        file_put_contents("../log/acces_bloque.log", $log, FILE_APPEND | LOCK_EX);
                        $vue = new Vue('../vues'.DS.'non_autorise');
                        $vue->generer(array());
                        exit();
                    }
                }
            }
        } catch (Exception $e) {
            \Appy\Src\Dispatcher::erreur('Erreur dans '.$e->getFile().' - Line <strong>'.$e->getLine().'</strong><br/><strong>'.$e->getMessage().'</strong>');
        }
    }
}
