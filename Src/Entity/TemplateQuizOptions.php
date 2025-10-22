<?php

namespace Appy\Src\Entity;

class TemplateQuizOptions
{
    public $id;
    public $quizType;
    public $colorForm;
    public $header;
    public $intro;
    public $conclusion;
    public $footer;
    public $ccP1L1;
    public $ccP1L2;
    public $ccP1L3;
    public $ccP1L4;
    public $ccP1L5;
    public $ccP2L1;
    public $ccP2L2;
    public $ccP2L3;
    public $ccP2L4;
    public $ccP2L5;
    public $ccP3L1;
    public $ccP3L2;
    public $ccP3L3;
    public $ccP3L4;
    public $ccP3L5;

    public function is360(){
        return $this->quizType === '360';
    }

    public function isBarom(){
        return $this->quizType === 'BAROM';
    }

    public function isPRCC(){
        return $this->quizType === 'PRCC';
    }
}


