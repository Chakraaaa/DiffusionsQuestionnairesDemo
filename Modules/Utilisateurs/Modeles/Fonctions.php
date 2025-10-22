<?php

namespace Appy\Modules\Utilisateurs\Modeles;

use \Appy\Src\Core\Session;
use Appy\Src\Entity\User;

/**
 * CLASSE DES FONCTIONS DU MODULE
 *
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2020, www.DavidSENSOLI.com
 */
class Fonctions
{

    /**
     * renvoie la totalité de la table des cantons
     *
     * @return Array Liste des users
     * @throws Exception
     */
    public static function getAllUtilisateurs()
    {
        $utilisateur = Session::getInstance()->read('utilisateur');

        try {
            $sql = "SELECT * FROM users";
            $sql .= " WHERE role IN (1,2,3)";
            $sql .= " ORDER BY role ASC, lastname ASC;";

            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $users = self::arrayToEntity($datas);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"".__FUNCTION__."\" de la classe \"".__CLASS__."\" ! :</strong><br/>".$e->getMessage());
        }


        return $users;
    }


    public static function getRole($role)
    {
        switch ($role)
        {
            case 1:
                return 'Administrateur';
                break;
            case 2:
                return 'Gestionnaire';
                break;
            case 3:
                return 'Consultant';
                break;
        }
    }

    public static function arrayToEntity($datas)
    {
        $users = array();

        foreach ($datas as $key => $value) {

            $user = new User();
            $user->id = $value['id'];
            $user->nom = $value['lastname'];
            $user->prenom = $value['firstname'];
            $user->email = $value['email'];
            $user->password = $value['password'];
            $user->remember_token = $value['remember_token'];
            $user->confirmation_token = $value['confirmation_token'];
            $user->confirmed_at = $value['confirmed_at'];
            $user->reset_token = $value['reset_token'];
            $user->reset_at = $value['reset_at'];
            $user->role = $value['role'];
            $user->last_ip = $value['last_ip'];
            $user->createdAt=$value['created_at'];
            $user->last_connection_at=$value['last_connection_at'];


            $users[$value['id']] = $user;
        }

        return $users;
    }
}
