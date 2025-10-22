<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\Quiz;
use Appy\Src\Entity\QuizUser;
use Appy\Src\Entity\User;

class QuizUserRepository extends \Appy\Src\Manager
{
    public $table = "quiz_user";

    public $champs = array(
        'QU.id',
        'QU.user_id',
        'QU.user_firstname',
        'QU.user_lastname',
        'QU.user_identifier',
        'QU.user_email',
        'QU.quiz_id',
        'QU.auto',
        'QU.status',
        'QU.created_at'
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $quizUsers = array();

        foreach ($datas as $key => $value) {

            $quizUser = new QuizUser();
            $quizUser->id = $value['id'];
            $quizUser->userId = $value['user_id'];
            $quizUser->userFirstName = $value['user_firstname'];
            $quizUser->userLastName = $value['user_lastname'];
            $quizUser->userIdentifier = $value['user_identifier'];
            $quizUser->userEmail = $value['user_email'];
            $quizUser->quizId = $value['quiz_id'];
            $quizUser->auto = $value['auto'];
            $quizUser->status = $value['status'];
            $quizUser->createdAt = $value['created_at'];


            $quizUsers[$value['id']] = $quizUser;
        }

        return $quizUsers;
    }

    public function getQuizUserId($quizId, $userId) {
        try {
            $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS QU WHERE QU.quiz_id = " . $quizId . " AND QU.user_id = " . $userId . ";";
            $data = \Appy\Src\Connexionbdd::query($sql)->fetch(\PDO::FETCH_ASSOC);
            $quizUserid = $data['id'] ?? null;
            return $quizUserid;

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
    }

    public function getQuizUserById($quizUserId) {
        try {
            $sql = "SELECT " . implode(", ", $this->champs);
            $sql .= " FROM " . $this->table . " AS QU INNER JOIN quiz Q ON Q.id = QU.quiz_id ";
            $sql .= " WHERE QU.id = " . $quizUserId . ";";

            $data = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $quizUsers = $this->arrayToEntity($data);
            $quizUser = array_shift($quizUsers);
            return $quizUser;

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }
    }

    public function insertQuizUser(User $user, $quiz, $auto) {
        $sql = "INSERT INTO " . $this->table . " (user_id, quiz_id, user_lastname, user_firstname, user_identifier, user_email, auto) ";
        $sql .= "VALUES (" . $user->id . ", " . $quiz->id . ", '" . $user->lastname . "', '" . $user->firstname . "', '" . $user->identifier . "', '" . $user->email . "'," . $auto . ")";

        \Appy\Src\Connexionbdd::query($sql);
        $id = \Appy\Src\Connexionbdd::lastInsertId();
        return $id;
    }

    public function getQuizUserByIdentifiers($quizIdentifier, $userIdentifier)
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs);
            $sql .= " FROM " . $this->table . " AS QU INNER JOIN quiz Q ON Q.id = QU.quiz_id ";
            $sql .= " WHERE Q.identifier = '" . $quizIdentifier . "' AND QU.user_identifier = '" . $userIdentifier . "';";
            //var_dump($sql);
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $quizUsers = $this->arrayToEntity($datas);
            $quizUser = array_shift($quizUsers);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $quizUser;
    }

    public function updateStatus($quizUserId, $newStatus){

        $currentDate = (new \DateTime())->format('Y-m-d');

        $sql = "UPDATE quiz_user SET created_at = '" . $currentDate . "', status = '" . $newStatus . "' WHERE id = " . $quizUserId .";";
        //var_dump($sql);
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function checkIfQuizHasUsers($quizId) {
        $sql = "SELECT " . implode(", ", $this->champs) . " FROM quiz_user AS QU WHERE QU.quiz_id = " . $quizId . ";";
        $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return !empty($datas);
    }

    public function getQuizUsersByQuizId($quizId, $criteres = NULL, $order = NULL)
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS QU";
            $sql .= " WHERE QU.quiz_id = $quizId";
            if ($criteres) {
                foreach ($criteres as $key => $value) {
                    if ($key === 'status_not' && !empty($value)) {
                        $sql .= " AND QU.status != '" . $value . "'";
                    } elseif ($key === 'status' && !empty($value)) {
                        $sql .= " AND QU.status = '" . $value . "'";
                    }
                }
            }

            if($order) {
                if ($order == "id") {
                    $sql .= " ORDER BY id ASC";
                } elseif ($order == "email") {
                    $sql .= " ORDER BY QU.user_email ASC";
                }
            } else {
                $sql .= " ORDER BY QU.user_lastname ASC, QU.user_firstname ASC, QU.user_email ASC";
            }
//var_dump($sql);

            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $quizUsers = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $quizUsers;
    }


}
