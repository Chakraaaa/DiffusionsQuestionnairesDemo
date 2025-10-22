<?php
/** LOCAL */
date_default_timezone_set('Europe/Paris');
setlocale(LC_ALL, "fr_FR.utf8");

/** APPLICATION */
ini_set('display_errors', true);
error_reporting(E_ALL | E_DEPRECATED);

/** CREATION DES CONSTANTES DES CHEMINS */
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', substr_replace(dirname(__FILE__), "", -6)); // On retire le "public" du chemin
define('WEB_PATH', str_replace("public/index.php", '', $_SERVER['SCRIPT_NAME']));

/** ROLES */
define('Adminisitrateur', 1);
define('Rédacteur', 2);
define('Superviseur', 3);
define('Élu', 4);

require_once "../vendor".DS."autoload.php";

// Chargement des variables d'environnement (.env)
if (file_exists(BASE_PATH.'/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->safeLoad();
}

\Appy\Src\Core\Appy::run();

exit();
