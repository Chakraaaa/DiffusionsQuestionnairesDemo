<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\Parameters;

class ParametersRepository extends \Appy\Src\Manager
{
    public $table = "parameters";

    public $champs = array(
        'P.id',
        'P.code',
        'P.value',
        'P.label'
    );

    public $champsInsert = array(
        '`code`',
        '`value`',
        '`label`'
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $parameters = array();

        foreach ($datas as $value) {
            $parameter = new Parameters();
            $parameter->id = $value['id'];
            $parameter->code = $value['code'];
            $parameter->value = $value['value'];
            $parameter->label = $value['label'];

            $parameters[] = $parameter;
        }

        return $parameters;
    }

    public function getAllparameters()
    {
        try {
            $sql = "SELECT " . implode(",", $this->champs);
            $sql .= " FROM " . $this->table . " AS P";
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $parameters = $this->arrayToEntity($datas);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $parameters;
    }

    public function updateParameters($nom, $telephone){
        $newNom = addslashes($nom);
        $newTelephone = addslashes($telephone);
        $sql1 = "UPDATE parameters SET value = '$newNom' WHERE id = 1";
        $sql2 = "UPDATE parameters SET value = '$newTelephone' WHERE id = 2";
        \Appy\Src\Connexionbdd::query($sql1);
        \Appy\Src\Connexionbdd::query($sql2);
    }


}
