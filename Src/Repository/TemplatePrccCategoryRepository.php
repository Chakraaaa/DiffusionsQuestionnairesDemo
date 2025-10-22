<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\TemplatePrccCategory;

class TemplatePrccCategoryRepository extends \Appy\Src\Manager
{
    public $table = "template_prcc_category";

    public $champs = array(
        'TPC.id',
        'TPC.label'
    );

    public $champsInsert = array(
        '`label`',
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $PrccCategoryTemplates = array();

        foreach ($datas as $value) {
            $PrccCategoryTemplate = new TemplatePrccCategory();
            $PrccCategoryTemplate->id = $value['id'];
            $PrccCategoryTemplate->label = $value['label'];
            $PrccCategoryTemplate->labelShort = $value['label_short'];
            $PrccCategoryTemplates[$PrccCategoryTemplate->id] = $PrccCategoryTemplate;
        }

        return $PrccCategoryTemplates;
    }

    public function getAllPrccCategoryTemplates()
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS TPC";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $templates = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $templates;
    }

    public function getTemplatePrccCategoryById($templateId)
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs) . " FROM " . $this->table . " AS TPC WHERE id = " . $templateId . ";";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $templates = $this->arrayToEntity($datas);
            $template = array_shift($templates);
        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $template;
    }

    public function UpdateLabelByCategoryId($categoryId, $label, $labelShort)
    {
        $newLabel = addslashes($label);
        $newLabelShort = addslashes($labelShort);

        $sql = "UPDATE " . $this->table . " ";
        $sql .= "SET label ='" . $newLabel . "', ";
        $sql .= " label_short ='" . $newLabelShort . "' ";
        $sql .= "WHERE id = $categoryId";
        \Appy\Src\Connexionbdd::query($sql);
    }

}
