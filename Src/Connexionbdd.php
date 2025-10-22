<?php
/**
 * \file  connexionbdd.class.php
 *
 * @version 1.1807 Ajout de la fonction query() et lastInsertId()
 *
 * @brief Fichier de la classe de connexion MySQL
 */

namespace Appy\Src;

use PDO;

/**
 * @brief CLASSE DE CONNEXION MySQL PAR PDO
 *
 * @version 4.1710 - Namespace Appy\Core
 * @version 3.1607 - Ajout du namespace Core;
 * @version 2.1601 - Gestion de l'encodage des caractères (utf8 par défaut)
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2016, www.DavidSENSOLI.com
 */
class Connexionbdd
{
    /**
     * L'instance
     * @var string $instance
     */
    private static $instance = NULL;

    /**
     * @static getInstance() Renvoie l'instance existante ou pas
     * @return object l'instance
     */
    public static function getInstance($encodage = 'utf8')
    {

        if (is_null(self::$instance)) {
            return self::connexionBDD($encodage);
        } else {
            return self::$instance;
        }
    }

    /**
     * @static connexionBDD() Créé une instance
     * @return object l'instance
     */
    private static function connexionBDD($encodage)
    {

        try {
            self::$instance = new PDO("mysql:host=".Config::DB_HOST.";dbname=".Config::DB_DATABASE, Config::DB_USERNAME, Config::DB_PASSWORD);

            /** Les éventuelles erreurs sont signalées sous la forme d'exceptions. */
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            /** Le pilote MySQL utilisera les versions bufferisées de l'API MySQL. */
            self::$instance->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

            /** Active la simulation des requétes préparées. */
            self::$instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            /** Renvoie par défaut un objet */
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

            /** Force l'encodage en UTF-8 */
            self::$instance->exec("SET CHARACTER SET $encodage");

            return self::$instance;
        } catch (PDOException $e) {
            throw new Exception("Echec de connexion à la base de données !<br/>".$e->getMessage()."");
        }
    }

    /**
     * Execute une requête préparée ou pas
     *
     * @param $query
     * @param bool|array $params
     * @return PDOStatement
     */
    public static function query($query, $params = FALSE)
    {
        try {
            if ($params) {
                $req = Connexionbdd::getInstance()->prepare($query);
                $req->execute($params);
            } else {
                $req = Connexionbdd::getInstance()->query($query);
            }
            return $req;
        } catch (PDOException $e) {
            throw new Exception("Erreur dans la fonction ".__METHOD__." de la classe ".__CLASS__." !<br/>".$e->getMessage()."");
        }
    }

    public static function lastInsertId()
    {
        try {
            return Connexionbdd::getInstance()->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur dans la fonction ".__METHOD__." de la classe ".__CLASS__." !<br/>".$e->getMessage()."");
        }
    }

    public static function count($query)
    {
        try {
            $res = Connexionbdd::getInstance()->query($query);
            return intval($res->fetchColumn());
        } catch (PDOException $e) {
            throw new Exception("Erreur dans la fonction ".__METHOD__." de la classe ".__CLASS__." !<br/>".$e->getMessage()."");
        }
    }

    /**
     * Ferme la connexion à la BDD
     */
    public static function ferme()
    {
        if (!is_null(self::$instance)) {
            self::$instance = null;
        }
    }
}
