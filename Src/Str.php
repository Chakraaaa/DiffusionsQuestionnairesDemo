<?php
/**
 * Classe des fonctions sur les chaines de caractères
 *
 * @author     David SENSOLI <dsensoli@gmail.com>
 * @copyright  2018 www.DavidSENSOLI.com
 * @version    v1.1812 - Ajout de la fonction enleverCaracteresSpeciaux()
 */

namespace Appy\Src;

class Str
{

    static function random($length)
    {
        $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }

    static function enleverCaracteresSpeciaux($text)
    {
        $utf8 = array(
            '/[áàâãªä]/u' => 'a',
            '/[ÁÀÂÃÄ]/u'  => 'A',
            '/[ÍÌÎÏ]/u'   => 'I',
            '/[íìîï]/u'   => 'i',
            '/[éèêë]/u'   => 'e',
            '/[ÉÈÊË]/u'   => 'E',
            '/[óòôõºö]/u' => 'o',
            '/[ÓÒÔÕÖ]/u'  => 'O',
            '/[úùûü]/u'   => 'u',
            '/[ÚÙÛÜ]/u'   => 'U',
            '/ç/'         => 'c',
            '/Ç/'         => 'C',
            '/ñ/'         => 'n',
            '/Ñ/'         => 'N',
            '//'          => '-', // conversion d'un tiret UTF-8 en un tiret simple
            '/[]/u'       => ' ', // guillemet simple
            '/[«»]/u'     => ' ', // guillemet double
            '/ /'         => ' ', // espace insécable (équiv. à 0x160)
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }
}
