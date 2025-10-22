<?php
/**
 * \file  regex.inc.php
 * @brief REGEX utilisees dans l'application
 *
 */
/**
 * @brief Classe des expressions régulières utilisées dans l'application
 *
 * @version 2.1805 - Passage en class static
 * @version 1.1604 - Ajout du REGEX_PRIX
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2018, www.DavidSENSOLI.com
 */

namespace Appy\Src;

class Regex
{

    /**
     * Saisie d'une quantitee mais avec 0 non autorise.
     */
    public static function quantite()
    {

        return "^[1-9][0-9]*";
    }

    /**
     * Saisie d'une operation dans un input ou directement d'un chiffre, la division n'est pas autorise.
     */
    public static function calcul()
    {

        return "^=*[\d*\+*\-*\**\(*\)*\s*]*";
    }

    /**
     * Saisie d'un entier positif.
     */
    public static function numero()
    {

        return "^[\d]*";
    }

    /**
     *  Saisie d'un prix unitaire avec 4 decimales max. on accepte le point ou la virgule.
     */
    public static function prix()
    {

        return "^[0-9][0-9]*\.?,?[0-9]{0,4}";
    }
}
