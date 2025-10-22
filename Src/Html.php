<?php
/**
 * \file  html.php
 * @brief Fichier de la classe d'affichage du html
 *
 */
/**
 * @brief classe d'affichage du html
 *
 * @version 1.1911 - Ajout du paramètre jour dans la fonction dateFR()
 * @version 1.1812 - Correction du bug dans la fonction css()
 * @version 1.180719 - Ajout de la fonction img et de la fonction script
 * @version 1.171101 - Ajout de la fonction input avec le tableau d'arguments
 *
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2017, www.DavidSENSOLI.com
 */

namespace Appy\Src;

class Html
{

    /**
     * Renvoie la date au format Français si différente de '0000-00-00'
     * 
     * @param String $date au format YYYY-MM-DD
     * @return type
     */
    public static function dateFR($date, $jour = false)
    {
        if ($date != '0000-00-00') {
            if ($jour) {
                return strftime("%A %d %B %G", strtotime($date));
            } else {
                return date("d/m/Y", strtotime($date));
            }
        } else {
            return null;
        }
    }

    /**
     * Renvoie la datetime au format Français si date différente de '0000-00-00'
     *
     * @param String $date au format YYYY-MM-DD HH:MM:SS
     * @return type
     */
    public static function datetimeFR($date, $jour = false)
    {
        if ($date != '0000-00-00') {
            if ($jour) {
                return strftime("%A %d %B %G", strtotime($date));
            } else {
                return date("d/m/Y H:i:s", strtotime($date));
            }
        } else {
            return null;
        }
    }

    public static function dateTimeFrWithoutSecond($date, $jour = false)
    {
        if ($date) {
            if ($jour) {
                return strftime("%A %d %B %G", strtotime($date));
            } else {
                return date("d/m/Y H:i", strtotime($date));
            }
        } else {
            return null;
        }
    }

    /**
     * Renvoie une chaine avec le prix
     *      avec 2 chiffres apres la virgule
     *      virgule en sépareur de décimal
     *      espace en separateur de millier
     *      et le signe € à la fin
     *
     * @param float $nombre
     * @return type
     */
    public static function displayPrice($nombre)
    {
        $retour = "";
        if($nombre) {
            $retour = number_format($nombre, 2,',',' '). ' €';
        }
        return $retour;
    }

    /**
     * Renvoie une chaine avec le prix
     *      avec 0 chiffres apres la virgule
     *      espace en separateur de millier
     *      et le signe € à la fin
     *
     * @param float $nombre
     * @return type
     */
    public static function displayIndicatorEuro($nombre)
    {
        return number_format($nombre, 0,',',' '). ' €';
    }

    /**
     * Renvoie une marge en pourcentage
     *      formule : (marge HT - prix achat HT) * 100
     *      et le signe % à la fin =
     *
     * @param float $nombre
     * @param float $nombre
     * @return type
     */
    public static function displayMargePercent($marge, $parameters)
    {
        if($marge != "") {
            //On affiche en rouge si pourcentage inférieur à 10 ou supérieuer à 150
            $alert = "";
            $margeLow = $parameters['order_marge_mail_percent_low'];
            $margeHigh = $parameters['order_marge_mail_percent_high'];
            if($marge < $margeLow || $marge > $margeHigh) {
                $alert = 'style="color:red;font-weight: 900"';
            }
            return '<span '.$alert.'>'. number_format($marge, 0,',',' '). ' %</span>';
        } else {
            return "";
        }

    }

    public static function displayMargeImport($pricePurchase, $priceSale)
    {
        /*
         * Exemple de calcul du taux de marge :
            Un produit est acheté 40 euros pour être revendu à 65 euros.
            Donc la marge commerciale appliquée est de  25 euros.
            Le taux de marge = (1 - (25 / 40)) x 100
         */
        $return = "";
        if($priceSale && $pricePurchase) {
            $return = number_format((1-($pricePurchase / $priceSale))*100, 0,',',''). ' %';
        }

        return $return ;
    }

    public static function displayMargeImportAlert($pricePurchaseNew, $priceSaleNew,$pricePurchase, $priceSale)
    {
        $styleAlert = "";

        if($pricePurchaseNew && $priceSaleNew && $pricePurchase && $priceSale) {
            $margeNew = number_format((1-($pricePurchaseNew / $priceSaleNew))*100, 0,',','');
            $marge = number_format((1-($pricePurchase / $priceSale))*100, 0,',','');

            if(abs($margeNew-$marge) > 5) {
                $styleAlert = 'style="background-color: lightyellow"';
            }
        } else {
            $styleAlert = 'style="background-color: lightblue"';
        }

        return $styleAlert;
    }

    /**
     * Renvoie une remise en pourcentage
     *      formule : (prix HT * 100 / prix remisé HT)
     *      entre paranthèse et le signe - au début et % à la fin
     *
     * @param float $nombre
     * @param float $nombre
     * @return type
     */
    public static function displayRemise($price, $priceDiscount)
    {
        return (100-number_format($priceDiscount*100/$price, 0,',',' ')). ' %';
    }

            /**
     * Renvoie un input en html
     *
     * @param string Type d'input
     * @param string Valeur de name
     * @param string Valeur de value
     * @param array Tableau associatif des arguments (exemple 'size' => '50')
     * @return string Html de l'input
     */
    public static function input($type, $name, $value, $arg = NULL)
    {
        $html = "<input type='$type' id='$name' name='$name' value='$value' ";

        if (is_array($arg)) {

            foreach ($arg as $key => $value) {
                $html .= " $key='$value'";
            }
        }

        $html .= " />";

        return $html;
    }

    /**
     * Renvoie une balise a avec un mailto dans l'attribut href
     *
     * @param string $adresse
     * @param array Tableau associatif des arguments (exemple 'title' => 'le titre')
     * @return string Html de la balise a
     */
    public static function mailto($adresse, $arg = NULL)
    {
        $html = "<a href='mailto:$adresse'";

        if (is_null($arg) OR!array_key_exists("title", $arg)) {
            $html .= " title='Envoyez un mail &agrave; $adresse'";
        }

        if (is_array($arg)) {

            foreach ($arg as $key => $value) {
                $html .= " $key='$value'";
            }
        }

        $html .= ">$adresse</a>";

        return $html;
    }

    /**
     * Renvoie un lien html
     *
     * @param string Nom du lien
     * @param string L'adresse
     * @param array Tableau associatif des arguments (exemple 'title' => 'le titre')
     * @return string Html de la balise a
     */
    public static function lien($nom, $adresse, $arg = NULL)
    {
        $html = "<a href='$adresse'";

        if (is_null($arg) OR!array_key_exists("title", $arg)) {
            $html .= " title='$nom'";
        }

        if (is_array($arg)) {

            foreach ($arg as $key => $value) {
                $html .= " $key='$value'";
            }
        }

        $html .= ">$nom</a>";

        return $html;
    }

    /**
     * Crée une balise img d'un fichier placé dans assets/images
     *
     * ajoute la version du fichier avec filemtime()
     * 
     * @param String $name_file Nom du fichier
     * @param String $alt l'attribut alt
     * @param Array $style Array de style
     * @return String Html
     */
    public static function img($name_file, $alt = "image", $style = NULL, $arg = NULL)
    {

        $web_path = WEB_PATH."assets/images/".$name_file;
        $version  = filemtime(BASE_PATH."assets".DS."images".DS.$name_file);

        $html = '<img src="'.$web_path.'?v='.$version.'" alt="'.$alt.'"';

        if (is_array($style)) {
            $html .= ' style="';
            foreach ($style as $key => $value) {
                $html .= $key.': '.$value.';';
            }
            $html .= '"';
        }

        if (is_array($arg)) {
            foreach ($arg as $key => $value) {
                $html .= " $key='$value'";
            }
        }

        $html .= ' >';

        return $html;
    }

    /**
     * Insère un fichier JS
     *
     * @param String $path_file Le chemin du fichier JS
     * @return String l'Html
     */
    public static function scriptJS($path_file)
    {

        $chemin    = explode("/", $path_file);
        $web_path  = WEB_PATH.implode("/", $chemin);
        $base_path = BASE_PATH.implode(DS, $chemin);
        $version   = filemtime($base_path);

        if ($version) {
            return "<script defer src='$web_path?v=$version'></script>";
        } else {
            throw new \Exception("Le fichier $base_path n'existe pas !");
        }
    }

    /**
     * Insère un fichier JS de type module
     *
     * @param String $path_file Le chemin du fichier JS
     * @return String l'Html
     */
    public static function moduleJS($path_file)
    {

        $chemin    = explode("/", $path_file);
        $web_path  = WEB_PATH.implode("/", $chemin);
        $base_path = BASE_PATH.implode(DS, $chemin);
        $version   = filemtime($base_path);

        if ($version) {
            return "<script type='module' src='$web_path?v=$version'></script>";
        } else {
            throw new \Exception("Le fichier $base_path n'existe pas !");
        }
    }

    /**
     * Insère un fichier CSS
     *
     * @param String $path_file Le chemin du fichier CSS
     * @return String l'Html
     */
    public static function css($path_file)
    {

        $chemin    = explode("/", $path_file);
        $web_path  = WEB_PATH.implode("/", $chemin);
        $base_path = BASE_PATH.implode(DS, $chemin);
        $version   = filemtime($base_path);

        if ($version) {
            return "<link rel='stylesheet' href='$web_path?v=$version'>";
        } else {
            throw new Exception("Le fichier $base_path n'existe pas !");
        }
    }
}
