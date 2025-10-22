<?php

namespace Appy\Src\Entity;

class QuizQuestion
{
    public $id;
    public $quizType;
    public $questionType;
    public $label;
    public $ordre;
    public $responseRequired;
    public $reportOrdre;
    public $createdAt;
    public $quizId;
    public $quizUserResponse;

    public function addResponse(QuizUserResponse $quizUserResponse)
    {
        $this->quizUserResponse = $quizUserResponse;
    }

}

?>
