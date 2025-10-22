<?php

namespace Appy\Src;

/**
 * Classe de manipulation de fichiers
 */
class Fichiers
{

    /**
     * Mets la première lettre en majuscules de tous les fichiers du répertoire passé en argument
     *
     *
     * @param String $repertoire
     */
    public static function setUcfirst($repertoire)
    {

        $dossier = opendir($repertoire);

        if ($dossier) {

            while (false !== ($fichier = readdir($dossier))) {

                if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php') {

                    rename($repertoire.DIRECTORY_SEPARATOR.$fichier, $repertoire.DIRECTORY_SEPARATOR.ucfirst($fichier));
                }
            }
        }
    }

    /**
     * Renvoie la liste des fichiers presents dans le répertoire par ordre alphanumérique
     *
     * @param string $repertoire
     */
    public static function scanDir(string $repertoire)
    {

        $array_names_file = [];
        $dir_handle       = opendir($repertoire);

        while ($fichier = readdir($dir_handle)) {

            if (is_file($repertoire."/".$fichier)) {
                $array_names_file[] = $fichier;
            }
        }
        closedir($dir_handle);

        sort($array_names_file);

        return $array_names_file;
    }
}
