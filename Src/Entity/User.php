<?php

namespace Appy\Src\Entity;

class User
{
    public $id;
    public $lastname;
    public $firstname;
    public $identifier;
    public $email;
    public $password;
    public $remember_token;
    public $confirmation_token;
    public $confirmed_at;
    public $reset_token;
    public $reset_at;
    public $role;
    public $last_ip;
    public $createdAt;
    public $last_connection_at;
    public $groupe;
    public $groupId;

    public function __construct() {
        self::setIdentifier();
    }

    public function getRoleLabel()
    {
        switch ($this->role)
        {
            case 1:
                return "ADMINISTRATEUR";
                break;
            case 2:
                return "GESTIONNAIRE";
                break;
            case 3:
                return "CONSULTANT";
                break;
            case 4:
                return "CLIENT";
                break;
            case 5:
                return "REPONDANT";
                break;
        }
    }

    public function isAdmin()
    {
        return ($this->role == 1);
    }

    // Rejet des lettres I et O et du chiffre 0 pour éviter les confusions.
    private function setIdentifier()
    {
        $characters = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $str = '';
        for ($i = 0; $i < 10; $i++) {
            $str .= $characters[rand(0, strlen($characters) - 1)];
        }
        $this->identifier = $str;
    }

    public function setRoleRepondant(){
        $this->role = 5;
    }

    public function addGroupe(Groupe $groupe)
    {
        $this->groupe = $groupe;
    }
}
