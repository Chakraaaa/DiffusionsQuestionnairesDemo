<?php

namespace Appy\Src;

/**
 * @brief Classe de création de requête MySQL
 *
 * La classe permet de créer des requêtes
 *
 * Nécessite PHP 5.4 minimum
 *
 * @version 1.1709 - Ajout du NAMESPACE Core
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2017 www.DavidSENSOLI.com
 */
class RequeteSQL
{
    /**
     *
     * @var Object Instance de connexion avec PDO
     */
    private $pdo;
    private $select;
    private $table;
    private $where;
    private $group;
    private $order;

    /**
     * La classe permet de créer des requêtes
     *
     * Nécessite PHP 5.4 minimum
     *
     */
    public function __construct()
    {

        $this->pdo = Connexionbdd::getInstance(); // Instancie PDO
    }

    /**
     * Détermine les champs à récupérer par la requête<br>
     * Si la fonction n'est pas utilisée la requête renverra tous les champs
     *
     * Syntaxe à utiliser <b>->select("nom", "prenom")<b>
     *
     * @param String $champs La liste des champs
     * @return Object L'instance de MySQL
     */
    public function select($champs)
    {

        $this->select = func_get_args();

        return $this;
    }

    /**
     * Détermine la table sur laquelle est faite la requête
     *
     * @param String $table Nom de la table
     * @return Object L'instance de MySQL
     */
    public function from($table)
    {

        $this->table = "`$table`";

        return $this;
    }

    public function where($conditions)
    {

        $this->where = func_get_args();

        return $this;
    }

    public function group($champ)
    {

        // Supprime les espaces s'il y en a
        $champ = str_replace(' ', '', $champ);

        $this->group = "GROUP BY `$champ`";

        return $this;
    }

    public function order($champs, $tri = "ASC")
    {

        $this->order = "ORDER BY ";

        // Supprime les espaces s'il y en a
        $champs = str_replace(' ', '', $champs);

        // Récupère les champs dans le tableau $parts
        $parts       = explode(",", $champs);
        $this->order .= join(", ", $parts);

        $this->order .= " ".$tri;

        return $this;
    }

    public function __toString()
    {

        $parts = ["SELECT"];

        if ($this->select) {
            $parts[] = join(", ", $this->select);
        } else {
            $parts[] = "*";
        }

        $parts[] = "FROM";
        $parts[] = $this->table;

        if ($this->where) {
            $parts[] = "WHERE ";
            $parts[] = "(".join(") AND (", $this->where).")";
        }

        if ($this->group) {
            $parts[] = $this->group;
        }

        if ($this->order) {
            $parts[] = $this->order;
        }

        $requete = join(" ", $parts);

        return $requete;
    }

    public function execute()
    {

        return $this->pdo->query($this);
    }
}
