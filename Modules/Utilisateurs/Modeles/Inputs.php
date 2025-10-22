<?php

namespace Appy\Modules\Utilisateurs\Modeles;

class Inputs
{

    /**
     * Renvoie les input filtrés de la saisie
     *
     * @return Array Le POST filtré
     */
    public static function saisies()
    {

        $args = array(
            'cantons' => array(
                'filter' => FILTER_VALIDATE_INT,
                'flags'  => FILTER_REQUIRE_ARRAY
            ),
            //'lang'         => FILTER_SANITIZE_STRING,
            'nom'          => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom'       => FILTER_SANITIZE_SPECIAL_CHARS,
            'role'         => FILTER_SANITIZE_SPECIAL_CHARS,
        );

        $inputs = filter_input_array(INPUT_POST, $args);

        $inputs['cantons']      = $inputs['cantons'];
        $inputs['prenom']       = mb_strtoupper(mb_substr($inputs['prenom'], 0, 1), "UTF8").mb_substr($inputs['prenom'], 1);
        $inputs['nom']          = mb_strtoupper($inputs['nom'], "UTF8");
        $inputs['role']         = $inputs['role'];

        //\Appy\Src\Core\Appy::debug($inputs, true);
        return $inputs;
    }

    public static function new()
    {

        $args = array(
            'email'    => FILTER_SANITIZE_SPECIAL_CHARS,
            //'lang'     => FILTER_SANITIZE_STRING,
            'nom'      => FILTER_SANITIZE_SPECIAL_CHARS,
            'password' => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom'   => FILTER_SANITIZE_SPECIAL_CHARS,
            'role'     => FILTER_SANITIZE_SPECIAL_CHARS
        );

        $inputs = filter_input_array(INPUT_POST, $args);

        $inputs = array_map("trim", $inputs);

        $inputs['prenom'] = mb_strtoupper(mb_substr($inputs['prenom'], 0, 1), "UTF8").mb_substr($inputs['prenom'], 1);
        $inputs['nom']    = mb_strtoupper($inputs['nom'], "UTF8");
        $inputs['role']   = $inputs['role'];

        //\Appy\Src\Core\Appy::debug($inputs, true);
        return $inputs;
    }
}
