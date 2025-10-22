<?php
/**
 * Classe de validation des formulaires
 *
 * @version 1.2009 - Ajout de la fonction valueNumExistInTable()
 * @version 1.1909 - Ajout de la fonction isInArray()
 * @version 1.1810 - Ajout de la fonction isOnlyOne()
 *
 * @copyright DavidSENSOLI.com
 */

namespace Appy\Src;

class Validator
{
    private $data;
    private $errors = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function isAlpha($field, $errorMsg = NULL)
    {
        //if (empty($errorMsg)) {
            //$errorMsg = "<em>$field</em> n'est pas alphanumérique";
        //}

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->getField($field))) {
            $this->errors[$field] = $errorMsg;
            return false;
        }
        return true;
    }

    /**
     *  Renvoie TRUE si la valeur du champ n'existe pas dans la table
     *
     * @param String $field
     * @param String $table
     * @param String $errorMsg
     * @return boolean
     */
    public function isUniq($field, $table, $errorMsg = NULL)
    {
        if (empty($errorMsg)) {
            $errorMsg = "Le champ <em>$field</em> dans la table <em>$table</em> n'est pas unique";
        }

        $record = Connexionbdd::query("SELECT `$field` FROM `$table` WHERE `$field` = ?", [$this->getField($field)])->fetch();
        if ($record) {
            $this->errors[$field] = $errorMsg;
            return false;
        }
        return true;
    }

    /**
     * Renvoie TRUE si la valeur du champ n'existe pas avec un autre id
     *
     * @param String $field
     * @param String $table
     * @param Int $id
     * @param String $errorMsg
     * @return boolean
     */
    public function isOnlyOne($field, $table, $id, $errorMsg = NULL)
    {
        if (empty($errorMsg)) {
            $errorMsg = "Le champ <em>$field</em> dans la table <em>$table</em> n'est pas seul existant";
        }
        $record = Connexionbdd::query("SELECT `$field` FROM $table WHERE `$field` = ? AND id != $id", [$this->getField($field)])->fetch();

        if ($record) {
            $this->errors[$field] = $errorMsg;
            return false;
        }
        return true;
    }

    /**
     * Renvoie TRUE si la valeur numérique du champ existe pas dans la table
     *
     * Attention ! la valeur doit être numérique car testée avec WHERE `$field` = ?"
     *
     * @param string $field
     * @param string $table
     * @param type $errorMsg
     * @return boolean
     */
    public function valueNumExistInTable(string $field, string $table, $errorMsg = NULL)
    {
        if (empty($errorMsg)) {
            $errorMsg = "La valeur numérique du champ <em>$field</em> n'existe pas dans la table <em>$table</em>";
        }
        $record = Connexionbdd::query("SELECT `$field` FROM $table WHERE `$field` = ?", [$this->getField($field)])->fetch(\PDO::FETCH_COLUMN, 0);

        if ($record) {
            $this->errors[$field] = $errorMsg;
            return false;
        }
        return true;
    }

    /**
     *
     * Vérifie si la valeur du champ n'est pas dans l'array passé en arguments
     *
     * Renvoie TRUE si la valeur n'est pas présente !
     *
     * @param string $field
     * @param Array $array
     * @param string $errorMsg
     * @return boolean
     */
    public function isNotInArray($field, $array, $errorMsg = NULL)
    {
        if (empty($errorMsg)) {
            $errorMsg = "Le champ existe dans l'array".json_encode($array);
        }

        if (in_array($this->getField($field), $array)) {
            $this->errors[$field] = $errorMsg;
            return false;
        }

        return true;
    }

    /**
     *
     * Vérifie si la valeur du champ est dans l'array passé en arguments
     *
     * Renvoie TRUE si la valeur est présente !
     *
     * @param string $field
     * @param Array $array
     * @param string $errorMsg
     * @return boolean
     */
    public function isInArray($field, $array, $errorMsg = NULL)
    {
        if (empty($errorMsg)) {
            $errorMsg = "Le champ existe dans l'array".json_encode($array);
        }

        if (!in_array($this->getField($field), $array)) {
            $this->errors[$field] = $errorMsg;
            return false;
        }

        return true;
    }

    public function isEmail($field, $errorMsg = NULL)
    {
        if (empty($errorMsg)) {
            $errorMsg = "L'email n'est pas valide";
        }

        if (!filter_var($this->getField($field), FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $errorMsg;
            return false;
        }
        return true;
    }

    /**
     * Vérifie si le champ est confirmé, utilise le suffixe _confirm
     *
     * @param String $field
     * @param string $errorMsg
     * @return boolean
     */
    public function isConfirmed($field, $errorMsg = NULL)
    {
        if (empty($errorMsg)) {
            $errorMsg = "<em>$field</em> n'est pas confirmé correctement";
        }

        $value = $this->getField($field);
        if (empty($value) || $value != $this->getField($field.'_confirm')) {
            $this->errors[$field] = $errorMsg;
            return false;
        }
        return true;
    }

    public function isValid()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function getField($field)
    {
        if (!isset($this->data[$field])) {
            return null;
        }
        return $this->data[$field];
    }
}
