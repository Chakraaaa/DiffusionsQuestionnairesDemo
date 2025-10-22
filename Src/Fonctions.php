<?php
/**
 * \file  fonctions.class.php
 * @brief Fichier des fonctions de l'application
 *
 * @version 1.1906 - Passage en static des méthodes
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2019, www.DavidSENSOLI.com
 */

namespace Appy\Src;

use DateTime;

class Fonctions
{

    /**
     * Filtre des valeurs d'une BDD en UTF8 pour les utiliser dans fpdf
     *
     * @param $value
     * @return $value
     */
    public static function utf8ToFpdf($value)
    {

        $value = strtr($value, array('≤' => '=<', '≥' => '>='));
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        $value = iconv('UTF-8', 'windows-1252', $value);

        return $value;
    }

    /**
     * Récupération des saisies de date pour les mettre au format AAAA-MM-JJ
     *
     * @param String $string
     * @return String La date
     */
    public static function gereDate($string)
    {

        if (empty($string)) {                      // si la saisie est vide ou égale à 0
            return ("0000-00-00");                  // la fonction renvoie '0000-00-00'
        }

        $annee = substr(date("Y"), 0, 4);             // on récupère l'année en cours par défaut
        $mois  = substr(date("m"), 0, 2);              // on récupère le mois en cours par défaut
// On cherche les / pour identifier les différentes parties de la date avec un switch
        switch (substr_count($string, '/')) {
            case 0:                                                 // Cas d'une saisie sans /
                if (strlen($string) < 3) {                           // 1 ou 2 caractères saisis => le jour
                    $jour = str_pad($string, 2, "0", STR_PAD_LEFT);    // on complète en 0 s'il n'y a qu'un chiffre
                    if (!checkdate($mois, $jour, $annee)) {           // si la date n'existe pas
                        $jour = substr($string, 0, 1);                // on coupe la chaine en 2
                        $mois = substr($string, 1, 1);
                    }
                }
                if (strlen($string) < 5 AND strlen($string) > 2) {   // 3 ou 4 caractères saisis
                    $jour = substr($string, 0, 2);                    // on prend les 2 premiers pour le jour
                    $mois = substr($string, 2, 2);                    // on prend les 2 suivants (ou 1 seul) pour le mois
                    if (!checkdate($mois, $jour, $annee)) {           // si la date n'est pas bonne on fais l'inverse
                        $jour = substr($string, 0, 1);                // on prend le 1 premier pour le jour
                        $mois = substr($string, 1, 2);                // on prend les 2 suivants pour le mois
                    }
                }
                if (strlen($string) > 4) {               // plus de 4 caractères saisis
                    $jour  = substr($string, 0, 2);        // on prend les 2 premiers pour le jour
                    $mois  = substr($string, 2, 2);        // on prend les 2 suivants pour le mois
                    $annee = substr($string, 4, 4);       // on prend les 4 suivants pour l'année
                    if (strlen($annee) == 1) {           // si l'année a 1 chiffre
                        $annee = '200'.$annee;          // alors on part sur l'année 2000 + le chiffre
                    }
                    if (strlen($annee) == 2) {           // si l'année a 2 chiffres
                        if ($annee > 50) {              // et supérieure à 50
                            $annee = '19'.$annee;        // alors on part sur l'année 1900 + le chiffre
                        } else {                          // sinon
                            $annee = '20'.$annee;        // alors on part sur l'année 2000 + le chiffre
                        }
                    }
                    if (strlen($annee) == 3) {          // si l'année a 3 chiffres
                        $annee = '2'.$annee;             // alors on part sur l'année 2000 + le chiffre
                    }
                }
                break;

            case 1:
                $pos1 = strpos($string, '/');
                $jour = str_pad(substr($string, 0, $pos1), 2, "0", STR_PAD_LEFT);    // on récupère le jour devant le /
                $mois = str_pad(substr($string, $pos1 + 1, 2), 2, "0", STR_PAD_LEFT);  // on récupère le mois aprés le /
                break;

            case 2:
                $pos1  = strpos($string, '/');
                $pos2  = strpos($string, '/', $pos1 + 1);
                $jour  = str_pad(substr($string, 0, $pos1), 2, "0", STR_PAD_LEFT);                // on récupère le jour devant le 1er /
                $mois  = str_pad(substr($string, $pos1 + 1, $pos2 - $pos1 - 1), 2, "0", STR_PAD_LEFT);  // on récupère le mois entre les 2 /
                $annee = substr($string, $pos2 + 1, 4);                                         // on récupère l'année après le 2eme /
                if (strlen($annee) == 1) {           // si l'année a 1 chiffre
                    $annee = '200'.$annee;          // alors on part sur l'année 2000 + le chiffre
                }
                if (strlen($annee) == 2) {           // si l'année a 2 chiffres
                    if ($annee > 50) {              // et supérieure à 50
                        $annee = '19'.$annee;        // alors on part sur l'année 1900 + le chiffre
                    } else {                          // sinon
                        $annee = '20'.$annee;        // alors on part sur l'année 2000 + le chiffre
                    }
                }
                if (strlen($annee) == 3) {          // si l'année a 3 chiffres
                    $annee = '2'.$annee;             // alors on part sur l'année 2000 + le chiffre
                }
                break;
        }    // fin du swich

        if (!checkdate($mois, $jour, $annee)) {       // Dernier check dans le doute
            return ("0000-00-00");
        } else {
            return ($annee.'-'.$mois.'-'.$jour);    // Si tout ok, on renvoie la date
        }
    }

    public static function removeAccents($chaine)
    {
        $chaine = strtolower($chaine);
        $chaine = preg_replace('#Ç#', 'C', $chaine);
        $chaine = preg_replace('#ç#', 'c', $chaine);
        $chaine = preg_replace('#è|é|ê|ë#', 'e', $chaine);
        $chaine = preg_replace('#È|É|Ê|Ë#', 'E', $chaine);
        $chaine = preg_replace('#à|á|â|ã|ä|å#', 'a', $chaine);
        $chaine = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $chaine);
        $chaine = preg_replace('#ì|í|î|ï#', 'i', $chaine);
        $chaine = preg_replace('#Ì|Í|Î|Ï#', 'I', $chaine);
        $chaine = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $chaine);
        $chaine = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $chaine);
        $chaine = preg_replace('#ù|ú|û|ü#', 'u', $chaine);
        $chaine = preg_replace('#Ù|Ú|Û|Ü#', 'U', $chaine);
        $chaine = preg_replace('#ý|ÿ#', 'y', $chaine);
        $chaine = preg_replace('#Ý#', 'Y', $chaine);

        return ($chaine);
    }

    public static function formatedDate($date)
    {
        $date = str_replace('-', '', $date);
        $jour = substr($date, -2, 2);
        $mois = substr($date, -4, 2);
        switch ($mois) {
            case '01' :
                $mois = 'Janvier';
                break;
            case '02' :
                $mois = 'Février';
                break;
            case '03' :
                $mois = 'Mars';
                break;
            case '04' :
                $mois = 'Avril';
                break;
            case '05' :
                $mois = 'Mai';
                break;
            case '06' :
                $mois = 'Juin';
                break;
            case '07' :
                $mois = 'Juillet';
                break;
            case '08' :
                $mois = 'Août';
                break;
            case '09' :
                $mois = 'Septembre';
                break;
            case '10' :
                $mois = 'Octobre';
                break;
            case '11' :
                $mois = 'Novembre';
                break;
            case '12' :
                $mois = 'Décembre';
                break;
        }
        $annee = substr($date,0, 4);
        $date = $jour.' '.$mois.' '.$annee;

        return $date;
    }

    public static function formatedMontant($montant)
    {
        $montantForTest = str_replace(" ", "", $montant);
        $montantForTest = str_replace(",", ".", $montantForTest);
        //On supprime les espaces encodés en HTML
        $montantForTest = preg_replace('~(?:\s|&nbsp;)+~u', '',$montantForTest);
        $montantForTest = trim($montantForTest);

        if (is_numeric($montantForTest)) {
            settype($montantForTest, "double");
            $montant = number_format($montantForTest, 2, ',', ' '). " €";
        } else {
            $montant = str_replace(",", ".", $montant);
        }

        return $montant;
    }

    public static function formatedMontantGraphique($montant)
    {
        $montantForTest = str_replace(" ", "", $montant);
        $montantForTest = str_replace(",", ".", $montantForTest);
        //On supprime les espaces encodés en HTML
        $montantForTest = preg_replace('~(?:\s|&nbsp;)+~u', '',$montantForTest);
        $montantForTest = trim($montantForTest);

        if (is_numeric($montantForTest)) {
            settype($montantForTest, "double");
            $montant = number_format($montantForTest, 0, ',', ' '). " €";
        } else {
            $montant = str_replace(",", ".", $montant);
        }

        return $montant;
    }

    public static function formatedMontantExcel($montant)
    {
        //On supprime les espace et on remplace les , par des .
        //On supprime les espaces encodés en HTML
        //On cherche ensuite à convertir en type double
        //Si c'est bon on renvoie ce montant
        //Sinon on garde le format d'origine juste en rempalcant les , par des .
        $montantModified = str_replace(" ", "",str_replace(",", ".", $montant));
        $montantModified = preg_replace('~(?:\s|&nbsp;)+~u', '',$montantModified);
        if (is_numeric($montantModified)) {
            settype($montantModified, "double");
        } else {
            $montantModified = str_replace(",", ".", $montant);
        }

        return $montantModified;
    }

    public static function isValidDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function formatedMontantCalcul($montant)
    {
        //Si la chaine contaient des lettres on ne fait rien
        //Dans le cas contraire on supprime les espaces et le signe €  et on remplace les , par des .
        if (preg_match('/[A-Za-z]/', $montant))
        {
            $montantModified = $montant;
        } else {
            $montantModified = str_replace("€", "", str_replace(",", ".", str_replace(" ", "", $montant)));
        }

        //On cherche ensuite à convertir en type double
        //Si c'est bon on renvoie ce montant
        //Sinon on renvoit 0 pour le calcul
        $montantModified = preg_replace('~(?:\s|&nbsp;)+~u', '',$montantModified);
        if (is_numeric($montantModified)) {
            settype($montantModified, "double");
        } else {
            $montantModified = 0;
        }

        return $montantModified;
    }
    public static function formatedMontantDisplayWord($montant)
    {
        //Si la chaine contaient des lettres on ne fait rien
        //Dans le cas contraire on supprime les espaces et le signe €  et on remplace les , par des .
        if (preg_match('/[A-Za-z]/', $montant))
        {
            $montantModified = $montant;
        } else {
            $montantModified = str_replace("€", "", str_replace(",", ".", str_replace(" ", "", $montant)));
        }

        //On cherche ensuite à convertir en type double
        $montantModified = preg_replace('~(?:\s|&nbsp;)+~u', '',$montantModified);
        if (is_numeric($montantModified)) {
            settype($montantModified, "double");
        }

        return $montantModified;
    }

    public static function formatedString($chaine)
    {
        if (strlen($chaine) > 100) {
            $chaine = substr($chaine, 0, 99) . '...';
        }
        return $chaine;
    }

    public static function setColorPolitique($politiqueLabel)
    {
        $colorDefaultPolitique = 'ffffcc';
        $colorPolitique = $colorDefaultPolitique;

        if($politiqueLabel == 'AGRICULTURE') {
            $colorPolitique = 'ffffcc';
        } elseif($politiqueLabel == 'AIDE AUX COMMUNES') {
            $colorPolitique = 'ffcc99';
        } elseif($politiqueLabel == 'ATTRACTIVITE TERRITORIALE') {
            $colorPolitique = 'ffcccc';
        } elseif($politiqueLabel == 'COLLEGE') {
            $colorPolitique = 'ff99cc';
        } elseif($politiqueLabel == 'ECONOMIE') {
            $colorPolitique = 'ffccff';
        } elseif($politiqueLabel == 'GARANTIE DEPARTEMENTALE') {
            $colorPolitique = 'cc99ff';
        } elseif($politiqueLabel == 'GESTION DU DOMAINE PUBLIC') {
            $colorPolitique = 'ccccff';
        } elseif($politiqueLabel == 'HABITAT') {
            $colorPolitique = '99ccff';
        } elseif($politiqueLabel == 'JEUNESSE') {
            $colorPolitique = 'ccffff';
        } elseif($politiqueLabel == 'SOCIAL') {
            $colorPolitique = '99ffcc';
        } elseif($politiqueLabel == 'SPORT, CULTURE ET ASSOCIATIONS') {
            $colorPolitique = 'ccffcc';
        } elseif($politiqueLabel == 'TRANSITIONS DURABLES') {
            $colorPolitique = 'ccff99';
        }

        return $colorPolitique;
    }
}
