<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\Groupe;
use Appy\Src\Entity\Quiz;

class GroupesRepository extends \Appy\Src\Manager
{
    public $table = "groupes";

    public $champs = array(
        'G.`id`',
        'G.`groupe_name`',
        'G.`created_at`'
    );

    public $champsInsert = array(
        '`groupe_name`',
        '`created_at`'
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $groupes = array();

        foreach ($datas as $key => $value) {
            $groupe = new Groupe();
            $groupe->id = $value['id'];
            $groupe->groupeName = $value['groupe_name'];
            $groupe->createdAt = $value['created_at'];
            $groupes[$value['id']] = $groupe;
        }

        return $groupes;
    }

    public function getGroupById($groupId) : Groupe
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs);
            $sql.= " FROM " . $this->table . " AS G WHERE G.id = " . $groupId;

            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $groups = $this->arrayToEntity($datas);
            $group = array_shift($groups);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $group;
    }

    public function getAllGroupes()
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS G ORDER BY G.groupe_name ASC";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $groupes = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $groupes;
    }

    public function deleteGroupe($groupeId)
    {
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE id = '" . $groupeId . "';";
            \Appy\Src\Connexionbdd::query($sql);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return true;
    }

    public function createGroupe($groupe)
    {
        $sql = "INSERT INTO " . $this->table . " (groupe_name) 
                VALUES ('" . $groupe->groupeName . "')";

        \Appy\Src\Connexionbdd::query($sql);
        return \Appy\Src\Connexionbdd::lastInsertId();

    }

    public function UpdateGroup($groupe)
    {
        $sql = "UPDATE $this->table
            SET groupe_name = '$groupe->groupeName' 
            WHERE id = '$groupe->id'";
        \Appy\Src\Connexionbdd::query($sql);
    }


}
