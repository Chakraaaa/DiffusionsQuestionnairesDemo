<?php
/**
 * \file  objetorm.class.php
 * @brief Fichier de la classe d'objet générique MySQL
 *
 */
/**
 * @brief Classe d'objet générique MySQL
 *
 * La classe permet de créer, supprimer, modifier un objet dans une table.
 *
 * @see getAllTable() permet de récupérer l'array de tous les objets contenu dans la table.
 *
 * @version 4.1806 - Modification de la fonction setTable() pour gérer les classes qualifiées
 * @version 4.1804 - Utilisation de la classe statique Config
 * @version 3.1607 - Ajout du namespace Core;
 * @version 3.0914 - Ajout du pluriel sur le nom de la table
 * @version 2.0914 - Ajout de la gestion date_maj dans enregistre()
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2018, www.DavidSENSOLI.com
 */

namespace Appy\Src;

use PDO;

class Objetorm
{
    /**
     * La table MySQL de l'objet
     *
     * Elle portera le nom de la classe fille de l'objet.
     * @see setTable()
     *
     * @var String $tableSQL
     */
    private $tableSQL;

    /**
     * Tableau des attributs de l'objet
     * @var Array $attributs
     */
    private $attributs = array();

    /**
     * Avec ou sans identifiant
     *
     * @param type $id identifiant
     */
    public function __construct($id = NULL)
    {

        /** Définit le nom de table */
        $this->setTable();

        /** Définit le tableau des attributs */
        $this->prepareChamps();

        /** Si l'id n'est pas NULL, la fonction charge les attributs avec leur valeur */
        if ($id != NULL) {
            $this->attributs['id'] = $id;
            $this->charge($id);
        } else {
            $this->attributs['id'] = 0;
        }
    }

    /**
     * Utilise le nom de la classe fille pour la table.
     *
     * Check si la classe est qualifiée avec le namespace
     */
    private function setTable()
    {
        if (strtolower(get_class($this) == "parameter_month_shipping")) {
            $classe = explode("\\", strtolower(get_class($this)));
        } else {
            $classe = explode("\\", strtolower(get_class($this))."s");
        }


        // si la classe est qualifiée
        if (count($classe) > 1) {
            $this->tableSQL = end($classe);
        } else {
            if (strtolower(get_class($this) == "parameter_month_shipping")) {
                $this->tableSQL = strtolower(get_class($this));
            } else {
                $this->tableSQL = strtolower(get_class($this))."s";
            }

        }
    }

    /**
     * Définit le tableau des attributs avec le nom des colonnes de la table
     */
    private function prepareChamps()
    {

        $sql = "describe {$this->tableSQL}";
        try {
            $DB           = Connexionbdd::getInstance();
            $sth          = $DB->query($sql);
            $table_champs = $sth->fetchAll(PDO::FETCH_ASSOC);
            foreach ($table_champs as $champ) {
                $champ_name                   = $champ['Field'];
                $this->attributs[$champ_name] = NULL;
            }
        } catch (\PDOException $e) {
            throw new \Exception("<strong>Erreur dans la methode \"".__FUNCTION__."\" de la classe \"".get_class($this)."\" ! :</strong><br/>".$e->getMessage());
        }

        return true;
    }

    /**
     * Charge les attributs de l'objet contenus dans la ligne de la table
     *
     * @param Int id de l'objet
     * @return boolean Vrai ou faux
     * @throws Exception
     */
    private function charge($id)
    {

        try {
            $sql       = "SELECT * FROM $this->tableSQL WHERE `id` = '$id';";
            $DB        = Connexionbdd::getInstance();
            $sth       = $DB->query($sql);
            $attributs = $sth->fetch(PDO::FETCH_ASSOC);
            if (empty($attributs)) {
                throw new \Exception('<strong>id de l\'objet '.get_class($this).' inexistant !!!</strong>');
            }
            foreach ($attributs as $key => $value) {
                $this->attributs[$key] = $value;
            }
        } catch (\PDOException $e) {
            throw new \Exception("<strong>Erreur dans la methode \"".__FUNCTION__."\" de la classe \"".get_class($this)."\" ! :</strong><br/>".$e->getMessage());
        }

        return true;
    }

    public function __set($champ, $valeur)
    {
        if (array_key_exists($champ, $this->attributs)) {
            $this->attributs[$champ] = $valeur;
        }
    }

    public function __get($champ)
    {
        if (array_key_exists($champ, $this->attributs)) {
            return $this->attributs[$champ];
        }
    }

    /**
     * Renvoie le tableau des attributs de l'objet
     *
     * @return Array tableau('attribut' => valeur)
     */
    public function getAllAttributs()
    {
        return $this->attributs;
    }

    /**
     * Créé l' objet dans la BDD
     *
     * @return Int l'id créé ou false si erreur
     */
    public function nouveau()
    {

        $champs  = array();
        $valeurs = array();

        foreach ($this->attributs as $key => $value) {
            if ($key != 'id') {
                // Gestion d'une éventuelle date de création de la ligne
                if ($key != 'date_creation') {
                    $champs[]  = sprintf(' `%s`', $key);
                    $valeurs[] = sprintf(' \'%s\'', $value);
                } else {
                    $champs[]  = sprintf(' `%s`', $key);
                    $valeurs[] = sprintf(' \'%s\'', date('Y-m-d'));
                }
            }
        }

        $sql = sprintf("INSERT INTO $this->tableSQL (%s) VALUES (%s);", implode(',', $champs), implode(',', $valeurs));

        try {
            $DB = Connexionbdd::getInstance();
            $DB->exec($sql);
        } catch (\PDOException $e) {
            throw new \Exception("<strong>Erreur dans la methode \"".__FUNCTION__."\" de la classe \"".get_class($this)."\" ! :</strong><br/>".$e->getMessage());
        }

        return $DB->lastInsertId();
    }

    /**
     * Efface la ligne de l'objet dans la BDD
     *
     * @return boolean Vrai ou Faux
     * @throws Exception
     */
    public function efface()
    {
        $sql = "DELETE FROM $this->tableSQL WHERE `id` = ".$this->attributs['id'];

        try {
            $DB = Connexionbdd::getInstance();
            $DB->exec($sql);
        } catch (Exception $e) {
            throw new \Exception("<strong>Erreur dans la methode \"".__FUNCTION__."\" de la classe \"".get_class($this)."\" ! :</strong><br/>".$e->getMessage());
        }

        return true;
    }

    /**
     * Affecte aux attributs, s'ils existent, un tableau de type $array['champ'] = Valeur
     * Les attributs sont modifiés mais pas enregistrés dans la BDD
     *
     * @param type $array_champ_valeur
     */
    public function modifie_attributs($array_champ_valeur)
    {
        if (!empty($array_champ_valeur)) {
            foreach ($array_champ_valeur as $champ => $valeur) {                                                          // Récupère les modifications des propriétés et les change
                if (array_key_exists($champ, $this->attributs)) {
                    $this->attributs[$champ] = $valeur;
                }
            }
        }
    }

    /**
     * Enregistre l'objet en totalité ou les attributs spécifiés
     *
     * @param type $array_champ_valeur
     * @return boolean
     */
    public function enregistre($array_champ_valeur = NULL)
    {

        $set = array();

        if (empty($array_champ_valeur)) {
            $data = $this->attributs;
        } else {
            $data = $array_champ_valeur;
        }

        foreach ($data AS $key => $value) {
            if ($key != 'id') {
                if ($key != 'date_maj') {
                    $set[] = sprintf('`%s` = \'%s\'', $key, $value);
                } else {
                    $set[] = sprintf('`%s` = \'%s\'', $key, date('Y-m-d'));
                }
            }
        }

        $sql = sprintf("UPDATE $this->tableSQL SET %s WHERE id = '%s';", implode(',', $set), $this->attributs['id']);

        try {
            $DB = Connexionbdd::getInstance();
            $DB->exec($sql);
        } catch (\PDOException $e) {
            throw new \Exception("<strong>Erreur dans la methode \"".__FUNCTION__."\" de la classe \"".get_class($this)."\" ! :</strong><br/>".$e->getMessage());
        }

        return true;
    }

    public function enregistreUser($id, $nom, $prenom, $role)
    {
        $sql = "UPDATE users SET lastname ='".$nom."', firstname ='".$prenom."', role = '".$role."' WHERE id ='".$id."';";

        try {
            $DB = Connexionbdd::getInstance();
            $DB->exec($sql);
        } catch (\PDOException $e) {
            throw new \Exception("<strong>Erreur dans la methode \"".__FUNCTION__."\" de la classe \"".get_class($this)."\" ! :</strong><br/>".$e->getMessage());
        }

        return true;
    }


    /**
     * Duplique un objet dans la BDD et renvoie son id
     *
     * @return Int id de l'objet dupliqué
     */
    public function duplique()
    {

        self::charge($this->attributs['id']);

        return self::nouveau();
    }

    /**
     * Renvoie un array de tous les lignes contenues dans la table.
     * Le tableau est indexé par le nom de la colonne.
     *
     * @return array indexé par nom de champs
     */
    public function getAllTable()
    {

        $sql = "SELECT * from $this->tableSQL";

        try {
            $DB    = Connexionbdd::getInstance();
            $sth   = $DB->query($sql);
            $liste = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la methode \"".__FUNCTION__."\" de la classe \"".get_class($this)."\" ! :</strong><br/>".$e->getMessage());
        }

        return $liste;
    }
}
