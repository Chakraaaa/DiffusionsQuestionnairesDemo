<?php
/**
 * @brief DUMP QUOTIDIEN
 *
 * @version 4.1901 - Ajout de l'attribut de l'instance de connexion à la BDD, de checkErreurs() et temporise()
 * @version 4.1804 - Utilisation de la classe statique Config
 * @version 3.1611 - Ajout du namespace Core;
 * @version 1.1611 - Ajout des optimisations de tables
 * @version 1.1607 - Ajout du changment de répertoire sur le serveur FTP de sauvegarde
 * @version 1.0914 - Compression avec ZIP http://php.net/manual/fr/class.ziparchive.php
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2016, www.DavidSENSOLI.com
 */
/** GENERATION BDD */
/*

  CREATE TABLE IF NOT EXISTS `sauvegarde` (
  `date` date NOT NULL,
  `duree_fichier` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `duree_ftp` varchar(10) COLLATE utf8_bin DEFAULT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

 */

namespace Appy\Src;

use ZipArchive;

class Dump
{
    /** Instance de connexion à la base de données */
    private static $db;

    /** Chemin de l'exécutable de mysqldump */
    private static $path_mysqldump;

    /** Table où la date de sauvegarde est stockée */
    private static $table = 'sauvegarde';

    /** Nom du fichier .sql */
    private static $file;

    /** Nom du fichier zip */
    private static $zip_file;
    private static $duree_fichier = 0;
    private static $duree_ftp     = 0;

    /** Erreurs */
    private static $erreurs = array();

    public static function start()
    {
        self::$db             = Connexionbdd::getInstance();
        self::$path_mysqldump = Config::PATH_MYSQLDUMP;

        if (self::check()) {

            echo '<p>Sauvegarde quotidienne de '.ucfirst(Config::APPLICATION).' !</p>';
            self::temporise();

            self::optimizeTables();
            self::save();
            self::majTemps();

            self::checkErreurs();
        }
    }

    private static function optimizeTables()
    {
        echo "<p>Optimisation des tables</p>";
        self::temporise();

        try {
            $liste_des_tables = self::$db->query('SHOW TABLES FROM '.Config::DB_DATABASE)->fetchAll(\PDO::FETCH_COLUMN, 0);
            $sql              = sprintf("OPTIMIZE TABLE %s", implode(',', $liste_des_tables));
            self::$db->query($sql);
        } catch (Exception $e) {
            self::$erreurs[] = "<strong>Erreur dans la fonction \"".__FUNCTION__."\"  ! :</strong><br/>".$e->getMessage()."</strong>";
        }

        return true;
    }

    /**
     * DUMP la BDD dans un fichier .SQL
     *
     * @return boolean
     */
    private static function setFichierSQL()
    {

        ini_set('memory_limit', "256M");    // Augmente la mémoire
        ini_set('max_execution_time', "0"); // Evite le message d'erreur du dépassement des 60 sec. si le fichier est trop lourd

        self::$duree_fichier = microtime(true);
        $cmd                 = self::$path_mysqldump." --opt -h ".Config::DB_HOST." -u ".Config::DB_USERNAME." -p".Config::DB_PASSWORD." ".strtolower(Config::DB_DATABASE)." > ".self::$file."";

        system($cmd, $erreur);

        self::$duree_fichier = round((microtime(true) - self::$duree_fichier), 0);

        if (!empty($erreur)) {
            if (file_exists(self::$file)) {
                unlink(self::$file);
            }
            self::$erreurs[] = "Erreur dans la commande : retour de la fonction system = <strong>$erreur<strong> !";
        }

        return true;
    }

    /**
     * Compresse le fichier .SQL dans une archive ZIP
     *
     * @return boolean
     * @throws Exception
     */
    private static function setFichierZip()
    {
        $zip = new ZipArchive();
        if ($zip->open(self::$zip_file, ZipArchive::CREATE) !== true) {
            self::$erreurs[] = "Impossible d'ouvrir le fichier <".self::$zip_file.">\n";
        }
        $zip->addFile(self::$file);
        $zip->setArchiveComment(utf8_decode("Sauvegarde de l'application ".Config::APPLICATION."\nCr&eacute;&eacute;e le ".date('d/m/Y &agrave; H:i')));
        $zip->close();

        return true;
    }

    /**
     * Lance la sauvegarde
     */
    private static function save()
    {
        self::$zip_file = strtolower(Config::APPLICATION).'-dump-'.date("Y-m-d").'.zip';
        self::$file     = strtolower(Config::APPLICATION).'-dump-'.date("Y-m-d").'.sql';

        /** Modifie la date de sauvegarde dans la table */
        self::$db->exec("UPDATE `".self::$table."` set `date`='".date("Y-m-d")."'");

        /** Création du fichier .SQL */
        echo '<p>Cr&eacute;ation du fichier de sauvegarde...</p>';
        self::temporise();
        if (self::setFichierSQL()) {

            echo "<p>Fichier ".self::$file." ( ".round((filesize(self::$file) / 1000000), 2)." Mo) cr&eacute;&eacute;</p>";
            self::temporise();

            if (!filesize(self::$file)) {
                self::$erreurs[] = "Le fichier de sauvegarde est vide ...";
            }
        }

        /** Création de l'archive ZIP du fichier .SQL */
        if (self::setFichierZip()) {
            echo '<p>Taille du fichier zipp&eacute; : '.round((filesize(self::$zip_file) / 1000000), 2).' Mo pour chargement sur serveur FTP...</p>';
            self::temporise();
        }

        /** On efface le fichier .SQL puisque l'archive est prête */
        unlink(self::$file);

        self::saveSurFTP();
    }

    /**
     * Check s'il y a besoin de lancer la sauvegarde
     *
     * @return boolean
     */
    private static function check()
    {
        $datesave       = self::$db->query("SELECT * FROM `".self::$table."`")->fetch(\PDO::FETCH_BOTH);
        $datejour       = date('Y-m-d');
        $dsave          = explode("-", $datesave[0]);
        $djour          = explode("-", $datejour);
        $auj            = mktime(0, 0, 0, $djour[2], $djour[1], $djour[0]);
        $save           = mktime(0, 0, 0, $dsave[2], $dsave[1], $dsave[0]);
        $timestamp_diff = $auj - $save;

        if ($timestamp_diff != 0) {
            return true;
        } else {
            return false;
        }
    }

    private static function saveSurFTP()
    {
        // Evite le message d'erreur du depassement des 60 sec. si le fichier est trop lourd
        ini_set('max_execution_time', "0");

        $upload          = false;
        self::$duree_ftp = microtime(true);

        // Tentative de connexion
        $conn_id = ftp_connect(Config::FTP_SERVER);

        // S'il y a connexion au serveur FTP
        if ($conn_id == TRUE) {
            echo '<p>Connect&eacute; au serveur de sauvegarde...</p>';
            self::temporise();

            // Identification...
            ftp_login($conn_id, Config::FTP_USER_NAME, Config::FTP_USER_PASS);

            // Changement du répertoire de sauvegarde si besoin
            if (ftp_chdir($conn_id, Config::FTP_DIR)) {
                echo "<p>Le dossier courant est maintenant : ".ftp_pwd($conn_id)."</p>";
            } else {
                self::$erreurs[] = "Impossible de changer pour le dossier : ".Config::FTP_DIR;
            }

            // Activation du mode passif
            ftp_pasv($conn_id, true);

            echo '<p>Chargement du fichier sur le serveur. Merci de patienter quelques instants...</p>';
            self::temporise();

            $upload = ftp_put($conn_id, self::$zip_file, self::$zip_file, FTP_BINARY);
            ftp_close($conn_id);
        } else {
            self::$erreurs[] = '<strong>Impossible de se connecter au serveur '.Config::FTP_SERVER.' !</strong>';
        }

        if ($upload) {
            self::$duree_ftp = round((microtime(true) - self::$duree_ftp), 0);
        } else {
            self::$duree_ftp = 0; // sinon
            echo '<p style="text-align: center;">
                <strong>Fichier de sauvegarde non charg&eacute; sur le serveur !!!</strong>
                <br/><strong>Contacter la maintenance si le problème persiste...
                </p>';                                                                    // On affiche ce message
        }

        unlink(self::$zip_file); // On efface l'archive ZIP
    }

    /** Enregistre les temps d'execution de la classe dans la BDD */
    private static function majTemps()
    {

        $sql = "UPDATE `sauvegarde` set `duree_fichier` = '".self::$duree_fichier."s', `duree_ftp` = '".self::$duree_ftp."s';";

        try {
            self::$db->exec($sql);
        } catch (\PDOException $e) {
            self::$erreurs[] = "<strong>Erreur dans la fonction \"".__FUNCTION__."\"  ! :</strong><br/>".$e->getMessage()."<br/>La requete utilisee : <strong>".$sql."</strong>";
        }
    }

    /**
     * Vérifie s'il y a des erreurs et les affiche
     *
     * sinon retourne sur la page d'accueil
     */
    private static function checkErreurs()
    {

        if (!empty(self::$erreurs)) {
            foreach (self::$erreurs as $erreur) {
                echo "<p>$erreur</p>";
                exit();
            }
        } else {
            echo '<p><strong>Sauvegarde termin&eacute;e... Appuyer sur F5 pour rafraichir</strong></p>';
            echo "<script>window.location.replace(\"accueil.html\")</script>";
        }
    }

    /**
     * Temporise les affichages html
     */
    private static function temporise()
    {
        ob_flush();
        flush();
        sleep(1);
    }
}
