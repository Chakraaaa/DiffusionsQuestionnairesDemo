<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\User;
use Appy\Src\Entity\Groupe;

class UsersRepository extends \Appy\Src\Manager
{
    public $table = "users";


    public $champs = array(
        'U.id',
        'U.lastname',
        'U.firstname',
        'U.identifier',
        'U.email',
        'U.password',
        'U.remember_token',
        'U.confirmation_token',
        'U.confirmed_at',
        'U.reset_token',
        'U.reset_at',
        'U.role',
        'U.last_ip',
        'U.created_at',
        'U.last_connection_at',
        'U.group_id',
        'G.groupe_name'

    );

    public $champsInsert = array(
        '`lastname`',
        '`firstname`',
        '`identifier`',
        '`email`',
        '`password`',
        '`remember_token`',
        '`confirmation_token`',
        '`confirmed_at`',
        '`reset_token`',
        '`reset_at`',
        '`role`',
        '`last_ip`',
        '`created_at`',
        '`last_connection_at`',
        '`group_id`',
    );


    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $users = array();

        foreach ($datas as $key => $value) {

            $user = new User();
            $user->id = $value['id'];
            $user->lastname = $value['lastname'];
            $user->firstname = $value['firstname'];
            $user->identifier = $value['identifier'];
            $user->email = $value['email'];
            $user->password = $value['password'];
            $user->remember_token = $value['remember_token'];
            $user->confirmation_token = $value['confirmation_token'];
            $user->confirmed_at = $value['confirmed_at'];
            $user->reset_token = $value['reset_token'];
            $user->reset_at = $value['reset_at'];
            $user->role = $value['role'];
            $user->last_ip = $value['last_ip'];
            $user->createdAt = $value['created_at'];
            $user->last_connection_at = $value['last_connection_at'];
            $user->groupId = $value['group_id'];
            if ($value['group_id']) {
                $groupe = new Groupe();
                $groupe->id = $value['group_id'];
                $groupe->groupeName = $value['groupe_name'];
                $user->addGroupe($groupe);
            }

            $users[$value['id']] = $user;
        }

        return $users;
    }


    public function getAllUsers()
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS U";
            $sql .= " LEFT JOIN groupes AS G ON U.group_id = G.id";
            $sql .= " ORDER BY U.created_at ASC";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $users = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $users;
    }


    public function deleteUser($UserId)
    {
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE id = " . $UserId . ";";
            \Appy\Src\Connexionbdd::query($sql);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return true;
    }


    public function updateGroup($UserId, $IdGroup)
    {
        try {
            $sql = "UPDATE " . $this->table . " SET group_id = " . $IdGroup . " WHERE id = " . $UserId . ";";
            \Appy\Src\Connexionbdd::query($sql);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return true;
    }

    public function createUser($user)
    {
        $identifier = $user->identifier;
        $sql = "INSERT INTO " . $this->table . " (identifier, lastname, firstname, email, role, group_id) 
                VALUES ('" . $identifier . "','" . $user->lastname . "','" . $user->firstname . "','" . $user->email . "','" . $user->role . "','" . $user->groupId . "')";

        \Appy\Src\Connexionbdd::query($sql);

    }

    public function getAllRepondants($criteres = NULL, $order = NULL)
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS U";
            $sql .= " LEFT JOIN groupes AS G ON U.group_id = G.id";
            $sql .= " WHERE 1=1";
            $hasCriteria = false;
            foreach ($criteres as $key => $value) {
                if ($key === 'role' && !empty($value)) {
                    $sql .= " AND U.role = '" . $value . "'";
                    $hasCriteria = true;
                }
                if ($key === 'EmailNomPrenomIdentifiant' && !empty($value)) {
                    $sql .= " AND (U.email LIKE '%" . $value . "%' OR U.lastname LIKE '%" . $value . "%' OR U.firstname LIKE '%" . $value . "%' OR U.identifier LIKE '%" . $value . "%')";
                    $hasCriteria = true;
                }
                if ($key === 'groupe' && !empty($value)) {
                    $sql .= " AND G.id = '" . $value . "'"; // Filtrer par groupe
                    $hasCriteria = true;
                }
                if ($key === 'email' && !empty($value)) {
                    $sql .= " AND U.email = '" . $value . "'";
                    $hasCriteria = true;
                }
                if ($key === 'identifier' && !empty($value)) {
                    $sql .= " AND U.identifier = '" . $value . "'";
                    $hasCriteria = true;
                }
            }

            // Ajouter la condition par défaut pour le rôle 5 si aucun autre critère n'existe
            if (!$hasCriteria) {
                $sql .= " AND U.role = '5'";
            }

            if($order) {
                $sql .= " ORDER BY " . $order;
            } else {
                $sql .= " ORDER BY U.created_at DESC";
            }

            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $users = $this->arrayToEntity($datas);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
        return $users;
    }

    public function getRespondantsByGroupeId($groupeId)
    {
        try {

            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS U";
            $sql .= " LEFT JOIN groupes AS G ON U.group_id = G.id";
            $sql .= " WHERE U.group_id = $groupeId";
            $sql .= " AND U.role = 5";
            $sql .= " ORDER BY U.lastname ASC";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $users = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $users;
    }


    public function getUserById($userId)
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs);
            $sql .= " FROM " . $this->table . " AS U";
            $sql .= " LEFT JOIN groupes AS G ON U.group_id = G.id"; // Ajout de la jointure
            $sql .= " WHERE U.id = " . $userId . ";";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $users = $this->arrayToEntity($datas);
            $user = array_shift($users);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $user;
    }

    public function getUserByIdentifier($userIdentifier)
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs);
            $sql .= " FROM " . $this->table . " AS U";
            $sql .= " LEFT JOIN groupes AS G ON U.group_id = G.id"; // Ajout de la jointure
            $sql .= " WHERE U.identifier = '" . $userIdentifier . "';";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $users = $this->arrayToEntity($datas);
            $user = array_shift($users);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $user;
    }



    public function emailExistsInGroup($email, $groupeId)
    {
        $sql = "SELECT COUNT(email) AS email_count FROM " . $this->table . " U WHERE U.email = '$email' AND U.group_id = $groupeId;";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetch(\PDO::FETCH_ASSOC);
        if ($datas) {
            $count = $datas['email_count'];
            return $count > 0;
        }

        return false;
    }

    public function UpdateUser($user)
    {
        $sql = "UPDATE $this->table 
            SET lastname = '$user->lastname', 
                firstname = '$user->firstname', 
                email = '$user->email', 
                role = '$user->role', 
                group_id = '$user->groupId' 
            WHERE id = '$user->id'";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function getRespondantsWithEmailByGroupeId($groupeId)
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS U";
            $sql .= " LEFT JOIN groupes AS G ON U.group_id = G.id";
            $sql .= " WHERE U.group_id = " . $groupeId;
            $sql .= " AND U.role = 5";
            $sql .= " AND U.email IS NOT NULL";
            $sql .= " AND U.email != ''";

            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $users = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $users;
    }

    public function getUsersAlreadyReceivedEmail($quizId, $users)
    {
        try {
            $userIds = array_map(function($user) {
                return $user->id;
            }, $users);

            if (empty($userIds)) {
                return [];
            }
            $userIdsString = implode(',', $userIds);
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS U";
            $sql .= " LEFT JOIN quiz_user AS QU ON QU.user_id = U.id";
            $sql .= " LEFT JOIN groupes AS G ON U.group_id = G.id";
            $sql .= " WHERE QU.quiz_id = :quizId";
            $sql .= " AND QU.user_id IN ($userIdsString)";

            $datas = \Appy\Src\Connexionbdd::query($sql, ['quizId' => $quizId])->fetchAll(\PDO::FETCH_ASSOC);
            $users = $this->arrayToEntity($datas);

            return $users;

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
    }
}
