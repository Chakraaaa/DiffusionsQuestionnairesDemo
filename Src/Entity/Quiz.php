<?php

namespace Appy\Src\Entity;

class Quiz
{
    public $id;
    public $identifier;
    public $type;
    public $name;
    public $autoUserId;
    public $autoUserLastName;
    public $autoUserFirstName;
    public $autoUserIdentifier;
    public $autoUserEmail;
    public $createdAt;
    public $logo;
    public $startDate;
    public $endDate;
    public $reminderDate;
    public $colorForm;
    public $coefTafv;
    public $coefPv;
    public $coefPpv;
    public $coefPdtv;
    public $risqueDeSr;
    public $risqueDePdr;
    public $risqueDeR;
    public $risqueDeFr;
    public $risqueASr;
    public $risqueAPdr;
    public $risqueAR;
    public $risqueAFr;
    public $tauxDeSr;
    public $tauxDePdr;
    public $tauxDeR;
    public $tauxDeFr;
    public $tauxASr;
    public $tauxAPdr;
    public $tauxAR;
    public $tauxAFr;
    public $sexeAutoUser;
    public $header;
    public $intro;
    public $conclusion;
    public $footer;
    public $groupeId;
    public $fonctionAutoUser;
    public $deleted;
    public $anonymous;
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

public function __construct() {
        self::setIdentifier();
    }


    public function setQuizType($QuizType)
    {
        $this->quizType = $QuizType;
    }

    public function getQuizType()
    {
        return $this->type;
    }

    private function setIdentifier()
    {
        $characters = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $str = '';
        for ($i = 0; $i < 6; $i++) {
            $str .= $characters[rand(0, strlen($characters) - 1)];
        }
        $this->identifier = $str;
    }

    public function isType360(){
        return $this->type == '360';
    }

    public function isTypeBarom(){
        return $this->type == 'BAROM';
    }

    public function isTypePRCC(){
        return $this->type == 'PRCC';
    }

    public function isPublishable(){
        return $this->startDate != '';
    }
}

