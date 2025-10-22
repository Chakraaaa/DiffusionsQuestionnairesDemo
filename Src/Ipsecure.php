<?php
/**
 * \file      securite.class.php
 * \brief     Classe de la gestion de la sécurité de l'application
 *
 */
/**
 * @brief Classe de la gestion de la sécurité de l'application
 *
 * @version 2.1701 - Modification du .htaccess pour bloquer les IP douteuses
 * @version 2.1607 - Ajout du namespace Core;
 * @version 1.1511
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2016, www.DavidSENSOLI.com
 */

namespace Core;

class Ipsecure
{

    /** Vérifie si l'adresse IP est blacklisté */
    public static function ip_blackliste()
    {

        if (file_exists('blacklist_ip.log')) {
            $liste_ip = file('blacklist_ip.log');
            if (in_array($_SERVER[REMOTE_ADDR].PHP_EOL, $liste_ip)) {

                return true;
            } else {

                return false;
            }
        }
    }

    /** Ecriture du fichier log erreurs.log */
    public static function log()
    {

        // Si l'URL qui a échouée contient une chaîne de requête.
        if (!empty($_SERVER['REQUEST_URI']) AND $_SERVER['REQUEST_URI'] != '/') {
            $line = date('Y-m-d H:i:s')." | $_SERVER[REMOTE_ADDR] | $_SERVER[QUERY_STRING] | $_SERVER[REQUEST_URI] | $_SERVER[HTTP_USER_AGENT]";
            file_put_contents('erreurs.log', $line.PHP_EOL, FILE_APPEND | LOCK_EX);

            self::blacklist($_SERVER['REMOTE_ADDR']);
        }
    }

    /**
     * Ecriture du fichier des IP blacklisté et blocage de l'IP dans le .htaccess
     * 
     * @param Strong $ip
     */
    private static function blacklist($ip)
    {

        $liste_ip = array();

        // Si le fichier blacklist_ip a été créé
        if (file_exists('blacklist_ip.log')) {
            // On récupère les IP douteuses enregistrées dans le fichier
            $liste_ip = file('blacklist_ip.log');
        }

        // Si l'IP en cause n'est pas dans la liste, on l'ajoute au fichier des IP douteuses
        if (!in_array($ip.PHP_EOL, $liste_ip)) {
            file_put_contents('blacklist_ip.log', $ip.PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        // Si l'IP en cause est dans la liste (donc 2ième fois qu'elle est en cause), on bloque son accès dans le .htaccess
        if (in_array($ip.PHP_EOL, $liste_ip)) {
            file_put_contents('.htaccess', 'Deny from '.$ip.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}
