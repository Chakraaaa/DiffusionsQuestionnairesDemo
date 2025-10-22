<?php
/**
 * \file  main.class.php
 *
 * @brief Fichier du controleur principal
 */

/**
 * @brief MAIN.PHP
 *
 * @version 1.1910 - Passage des controleurs et du Module en static complet + gestion des erreurs avec \Throwable
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2019, www.DavidSENSOLI.com
 */
use Appy\Modules\Utilisateurs\Controleurs;

define("DIR_MODULE", basename(__DIR__));

switch ($action) {

    default:
        Controleurs\Edit::load();
        break;
}
