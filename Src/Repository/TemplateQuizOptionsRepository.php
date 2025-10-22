<?php

namespace Appy\Src\Repository;

use Appy\Src\Entity\TemplateQuizOptions;

class TemplateQuizOptionsRepository extends \Appy\Src\Manager
{
    public $table = "template_quiz_options";

    public $champs = array(
        'TQO.id',
        'TQO.quiz_type',
        'TQO.color_form',
        'TQO.header',
        'TQO.intro',
        'TQO.conclusion',
        'TQO.footer',
        'TQO.cc_p1_l1',
        'TQO.cc_p1_l2',
        'TQO.cc_p1_l3',
        'TQO.cc_p1_l4',
        'TQO.cc_p1_l5',
        'TQO.cc_p2_l1',
        'TQO.cc_p2_l2',
        'TQO.cc_p2_l3',
        'TQO.cc_p2_l4',
        'TQO.cc_p2_l5',
        'TQO.cc_p3_l1',
        'TQO.cc_p3_l2',
        'TQO.cc_p3_l3',
        'TQO.cc_p3_l4',
        'TQO.cc_p3_l5'
    );

    public function __construct($id = NULL)
    {
        parent::setTable($this->table);
        parent::__construct($id);
    }

    public function arrayToEntity($datas)
    {
        $quizOptionsList = array();

        foreach ($datas as $value) {
            $quizOption = new TemplateQuizOptions();
            $quizOption->id = $value['id'];
            $quizOption->quizType = $value['quiz_type'];
            $quizOption->colorForm = $value['color_form'];
            $quizOption->header = $value['header'];
            $quizOption->intro = $value['intro'];
            $quizOption->conclusion = $value['conclusion'];
            $quizOption->footer = $value['footer'];
            $quizOption->ccP1L1 = $value['cc_p1_l1'];
            $quizOption->ccP1L2 = $value['cc_p1_l2'];
            $quizOption->ccP1L3 = $value['cc_p1_l3'];
            $quizOption->ccP1L4 = $value['cc_p1_l4'];
            $quizOption->ccP1L5 = $value['cc_p1_l5'];
            $quizOption->ccP2L1 = $value['cc_p2_l1'];
            $quizOption->ccP2L2 = $value['cc_p2_l2'];
            $quizOption->ccP2L3 = $value['cc_p2_l3'];
            $quizOption->ccP2L4 = $value['cc_p2_l4'];
            $quizOption->ccP2L5 = $value['cc_p2_l5'];
            $quizOption->ccP3L1 = $value['cc_p3_l1'];
            $quizOption->ccP3L2 = $value['cc_p3_l2'];
            $quizOption->ccP3L3 = $value['cc_p3_l3'];
            $quizOption->ccP3L4 = $value['cc_p3_l4'];
            $quizOption->ccP3L5 = $value['cc_p3_l5'];

            $quizOptionsList[] = $quizOption;
        }

        return $quizOptionsList;
    }

    public function getTemplateByQuizType($quizType)
    {
        try {
            $sql = "SELECT " . implode(", ", $this->champs);
            $sql .= " FROM " . $this->table . " AS TQO WHERE TQO.quiz_type = '$quizType'";
            //var_dump($sql);
            $datas = \Appy\Src\Connexionbdd::query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $quizOptionsList = $this->arrayToEntity($datas);
            $quizOption = array_shift($quizOptionsList);

        } catch (\Exception $e) {
            throw new \Exception("<strong>Erreur dans la fonction \"" . __FUNCTION__ . "\" de la classe \"" . __CLASS__ . "\" ! :</strong><br/>" . $e->getMessage());
        }

        return $quizOption;
    }

    public function UpdateTemplatesQuizOptions($templateQuizOptions, $quizType)
    {
        $colorForm = addslashes($templateQuizOptions->colorForm);
        $header = addslashes($templateQuizOptions->header);
        $intro = addslashes($templateQuizOptions->intro);
        $conclusion = addslashes($templateQuizOptions->conclusion);
        $footer = addslashes($templateQuizOptions->footer);
        $ccP1L1 = addslashes($templateQuizOptions->ccP1L1);
        $ccP1L2 = addslashes($templateQuizOptions->ccP1L2);
        $ccP1L3 = addslashes($templateQuizOptions->ccP1L3);
        $ccP1L4 = addslashes($templateQuizOptions->ccP1L4);
        $ccP1L5 = addslashes($templateQuizOptions->ccP1L5);
        $ccP2L1 = addslashes($templateQuizOptions->ccP2L1);
        $ccP2L2 = addslashes($templateQuizOptions->ccP2L2);
        $ccP2L3 = addslashes($templateQuizOptions->ccP2L3);
        $ccP2L4 = addslashes($templateQuizOptions->ccP2L4);
        $ccP2L5 = addslashes($templateQuizOptions->ccP2L5);
        $ccP3L1 = addslashes($templateQuizOptions->ccP3L1);
        $ccP3L2 = addslashes($templateQuizOptions->ccP3L2);
        $ccP3L3 = addslashes($templateQuizOptions->ccP3L3);
        $ccP3L4 = addslashes($templateQuizOptions->ccP3L4);
        $ccP3L5 = addslashes($templateQuizOptions->ccP3L5);

        $sql = "UPDATE $this->table SET 
        color_form = '$colorForm',
        header = '$header',
        intro = '$intro',
        conclusion = '$conclusion',
        footer = '$footer',
        cc_p1_l1 = '$ccP1L1',
        cc_p1_l2 = '$ccP1L2',
        cc_p1_l3 = '$ccP1L3',
        cc_p1_l4 = '$ccP1L4',
        cc_p1_l5 = '$ccP1L5',
        cc_p2_l1 = '$ccP2L1',
        cc_p2_l2 = '$ccP2L2',
        cc_p2_l3 = '$ccP2L3',
        cc_p2_l4 = '$ccP2L4',
        cc_p2_l5 = '$ccP2L5',
        cc_p3_l1 = '$ccP3L1',
        cc_p3_l2 = '$ccP3L2',
        cc_p3_l3 = '$ccP3L3',
        cc_p3_l4 = '$ccP3L4',
        cc_p3_l5 = '$ccP3L5'
        WHERE quiz_type = '$quizType'";

        \Appy\Src\Connexionbdd::query($sql);
    }


}
