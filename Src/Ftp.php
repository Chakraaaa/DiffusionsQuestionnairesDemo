<?php

namespace Appy\Src;

/**
 * @brief CLASSE FTP
 *
 * @version 1.1911 - Classe en non static et création de la fonction download()
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2019, www.DavidSENSOLI.com
 */
class Ftp
{
    private $host;
    private $port;
    private $user;
    private $pass;
    private $conn;
    private $current_dir;
    private $remote_file;
    private $local_file;
    private $passif;

    public function __construct(string $host, int $port, string $user, string $pass, bool $passif = true)
    {
        $this->host   = $host;
        $this->port   = $port;
        $this->user   = $user;
        $this->pass   = $pass;
        $this->passif = $passif;

        $this->conn = ftp_connect($this->host, $this->port);

        // S'il y a connexion au serveur FTP
        if ($this->conn != false) {

            // On s'identifie
            if (!ftp_login($this->conn, $this->user, $this->pass)) {
                file_put_contents(BASE_PATH.'ftp.log', date("d/m/y H:i:s - ").__METHOD__." Echec de l'identification \n", FILE_APPEND | LOCK_EX);
            }
        } else {
            file_put_contents(BASE_PATH.'ftp.log', date("d/m/y H:i:s - ").__METHOD__." Impossible de se connecter au serveur ".self::$server." ! \n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Définit le dossier courant
     *
     * @param String $dir
     * @return boolean
     */
    public function setDir(string $dir)
    {

        if (ftp_chdir($this->conn, $dir)) {
            $this->current_dir = $dir;
            return true;
        } else {
            file_put_contents(BASE_PATH.'ftp.log', date("d/m/y H:i:s - ").__METHOD__." Impossible de changer de répertoire pour $dir. Le dossier courant reste ".ftp_pwd($this->conn)."\n", FILE_APPEND | LOCK_EX);
            return false;
        }
    }

    /**
     * Télécharge un fichier
     *
     * @param string $remote_file
     * @param string $local_file
     */
    public function download(string $remote_file, string $local_file)
    {
        $this->remote_file = $remote_file;
        $this->local_file  = $local_file;

        // Activation du mode passif
        if ($this->passif) {
            ftp_pasv($this->conn, true);
        }

        // Ouverture du fichier pour écriture
        $handle = fopen($local_file, 'w');

        // Tente de téléchargement le fichier $remote_file et de le sauvegarder dans $handle
        if (!ftp_fget($this->conn, $handle, $remote_file, FTP_ASCII, 0)) {
            file_put_contents(BASE_PATH.'ftp.log', date("d/m/y H:i:s - ").__METHOD__." Il y a un problème lors du téléchargement du fichier $remote_file dans $local_file\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Fermeture de la connexion
     */
    public function __destruct()
    {
        ftp_close($this->conn);
    }
}
