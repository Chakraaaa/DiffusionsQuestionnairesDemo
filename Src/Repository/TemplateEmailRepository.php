<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\TemplateEmail;

class TemplateEmailRepository extends \Appy\Src\Manager
{
    public $table = "email_template";

    public $champs = array(
        'ET.id',
        'ET.title',
        'ET.message',
        'ET.deleteable'
    );

    public $champsInsert = array(
        '`title`',
        '`message`',
        '`deleteable`'
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $emailTemplates = array();

        foreach ($datas as $value) {
            $emailTemplate = new TemplateEmail();
            $emailTemplate->id = $value['id'];
            $emailTemplate->title = $value['title'];
            $emailTemplate->message = $value['message'];
            $emailTemplate->deleteable = $value['deleteable'];

            $emailTemplates[] = $emailTemplate;
        }

        return $emailTemplates;
    }

    public function getAllEmailTemplates()
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS ET ORDER BY ET.title ASC";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $templates = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $templates;
    }

    public function getEmailTemplateById($templateId) : TemplateEmail
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS ET WHERE id = " . $templateId . ";";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $templates = $this->arrayToEntity($datas);
            $template = array_shift($templates);

        } catch (\Exception $e) {
            // Gestion des erreurs
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $template;
    }

    public function getEmailTemplateByType($quiz) : TemplateEmail
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS ET ";
            $sql.= " WHERE UPPER(ET.title) LIKE '%" . $quiz->type . "%' AND UPPER(ET.title) LIKE '%RAPPEL%'";
//var_dump($sql);
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $templates = $this->arrayToEntity($datas);
            $template = array_shift($templates);

        } catch (\Exception $e) {
            // Gestion des erreurs
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $template;
    }

    public function delete($templateId)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id = '" . $templateId . "';";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function CreateNewTemplate($title, $message)
    {
        $newTitle = addslashes($title);
        $newMessage = addslashes($message);
        $sql = "INSERT INTO " . $this->table . " (" . implode(",", $this->champsInsert) . ") ";
        $sql .= "VALUES ('" . $newTitle . "','" . $newMessage . "', 1)";
        \Appy\Src\Connexionbdd::query($sql);
    }

    public function UpdateTemplate($id, $message)
    {
        $newMessage = addslashes($message);
        $sql = "UPDATE " . $this->table . " SET message = '" . $newMessage . "' ";
        $sql .= "WHERE id = " . $id;
        //var_dump($sql);
        \Appy\Src\Connexionbdd::query($sql);
    }
}
