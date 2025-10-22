<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - Réponse au questionnaire';
$currentDate = new DateTime();
$startDate = new DateTime($quiz->startDate);
$endDate = new DateTime($quiz->endDate);
?>
<style>

    body{
        background-color: <?= $quiz->colorForm ?>;
    }
    .btn-ok {
        background-color: white;
        border: 2px solid #2e629a;
        color: black;
        padding: 16px 16px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s, border-color 0.3s;
        width: 20%;
    }

    .btn-ok:hover {
        background-color: #f0f0f0;
        border-color: #444444;
    }
    .custom-input {
        border: none;
        margin-top: 5px;
        border-bottom: 2px solid black;
        background-color: #f7f7f7;
        outline: none;
        padding: 10px;
        border-radius: 4px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    .radio-container {
        display: inline-block;
        margin-right: 20px;
        text-align: center;
    }

    .radio-input {
        display: none;
    }

    .radio-number {
        display: block;
        font-size: 16px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .radio-box {
        display: inline-block;
        width: 30px;
        height: 30px;
        border: 1px solid #ccc;
        border-radius: 5px;
        text-align: center;
        line-height: 50px;
        background-color: white;
        cursor: pointer;
        transition: none;
    }

    .radio-input:checked + .radio-box {
        background-color: #2e629a;
        color: white;
        border-color: #2e629a;
    }

    .radio-input:checked + .radio-box::after {
        content: '✔';
        color: white;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .radio-input:focus + .radio-box {
        outline: none;
        box-shadow: none;
    }

    .radio-box:focus {
        outline: none;
        box-shadow: none;
    }

    .radio-box::before {
        content: '';
    }

    .container {
        background-color: white;
        padding: 20px;
        width: 70%;
        margin-top: 40px;
        margin-bottom: 40px;
    }

    #submitQuizButton {
        font-size: 30px;
        padding: 15px 30px;
        min-width: 200px;
    }

    .quiz-header, .quiz-footer {
        margin-bottom: 20px;
    }

    .questions ul {
        list-style: square;
        margin-left: 30px;
    }

    .question .text-response {
        margin-right: 100px;margin-bottom: 20px;
    }

    .button-valider {
        font-size: 20px!important;
    }

    .error {
        border: 2px solid red;
        animation: shake 0.2s linear 2;
    }
    .radio-container.error {
        background-color: #ffe6e6;
    }

    #quizTitle {
        font-size: 32px;
        color: #696252;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }


    /* Styles pour les écrans de taille mobile */
    @media (max-width: 714px) {
        .container {
            background-color: white;
            padding: 5px;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .question .text-response {
            margin-right: 10px;margin-bottom: 20px;
        }
        .radio-box {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            line-height: 15px;
            font-size: 12px;
            background-color: white;
            cursor: pointer;
            transition: none;
        }

    }


</style>

<div>
    <div class="container">
        <div class="columns is-mobile is-vcentered">
            <div class="column is-flex is-justify-content-flex-start">
                <img class="logo-response" src="<?= WEB_PATH . 'assets/images/logo-rm-simple.png'?>" alt="LogoRM">
            </div>
            <div class="column is-flex is-justify-content-flex-end is-align-items-center">
                <?php if($quiz->logo) { ?>
                <img class="logo-response" src="<?= WEB_PATH . 'assets/images/logosClients/' . $quiz->logo ?>" alt="Logo">
                <?php } ?>
            </div>
        </div>
        <div class="columns is-mobile">
            <div class="column is-flex is-justify-content-center">
                <h1 id="quizTitle"><?=$quiz->name?></h1>
            </div>
        </div>
        <main style="background-color: white">
            <div class="quiz-header">
                <?php if (!empty($quiz->header)) { ?>
                <h1 style="font-size: 20px; font-weight: bold; color: #2c3e50;margin-top:20px; margin-bottom: 40px"><?= $quiz->header ?></h1>
                <?php } ?>
                <?php if (!empty($quiz->intro)) { ?>
                <p style="font-size: 16px; color: #333;"><?= nl2br($quiz->intro) ?></p>
                <?php } ?>
            </div>
            <div class="questions">
                <?php $questionCounter = 0; ?>
                <?php foreach ($questions as $index => $question) {
                    $questionLabel = str_replace("Trebuchet MS", "Work Sans", $question->label);
                ?>
                <div class="question" id="question_<?= $question->id ?>" style="margin-bottom: <?php if($question->questionType == 'CHAPTER') { ?>20px<?php } else { ?>100px<?php } ?>;">
                    <?php if ($question->questionType == 'INPUT-RADIO') { ?>
                    <div style="top: 24px; position: relative"><?= ++$questionCounter ?></div>
                    <div class="text-response" style="margin-left: <?php if($question->reportOrdre < 10) { ?>27px<?php } else { ?>35px<?php } ?>">
                        <?= $questionLabel ?>
                    </div>
                    <div class="has-text-right">
                        <div class="radio-container">
                            <span class="radio-number">Plutôt vrai</span>
                            <label class="radio">
                                <input type="radio"
                                       name="question_<?= $question->id ?>"
                                       value="PV"
                                       class="radio-input"
                                        <?php if($modeTest==0) { ?>
                                            <?= ($question->quizUserResponse->value != '' && $question->quizUserResponse->value == 'PV') ? 'checked' : '' ?>
                                        <?php } ?>
                                >
                                <span class="radio-box"></span>
                            </label>
                        </div>
                        <div class="radio-container">
                            <span class="radio-number">Plutôt faux</span>
                            <label class="radio">
                                <input type="radio"
                                       name="question_<?= $question->id ?>"
                                       value="PF"
                                       class="radio-input"
                                        <?php if($modeTest==0) { ?>
                                            <?= ($question->quizUserResponse->value != '' && $question->quizUserResponse->value == 'PF') ? 'checked' : '' ?>
                                        <?php } ?>
                                >
                                <span class="radio-box"></span>
                            </label>
                        </div>
                    </div>
                    <button
                            class="button btn-ok mt-5 is-pulled-right"
                    <?php if (isset($arrayQuestionAndNextQuestion[$question->id])) { ?>
                    data-question="question_<?= $question->id ?>" data-next-question="question_<?= $arrayQuestionAndNextQuestion[$question->id] ?>"
                    <?php } else { ?>
                    data-question="question_<?= $question->id ?>"
                    <?php } ?>
                    >
                    Ok
                    </button>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <div class="quiz-footer">
                <?php if (!empty($quiz->conclusion)) { ?>
                <div style="font-size: 20px; color: #333;margin-top:20px; margin-bottom: 40px">
                    <?= nl2br($quiz->conclusion) ?>
                </div>
                <?php } ?>
                <?php if (!empty($quiz->footer)) { ?>
                <div style="font-size: 16px; color: #333;">
                    <?= nl2br($quiz->footer) ?>
                </div>
                <?php } ?>
            </div>
        </main>
        <p class="has-text-right mt-5 mr-3">
            <button <?php if($modeTest != 0) { ?>style="pointer-events:none;opacity:0.5"<?php } ?> id="submitQuizButton" class="button-valider is-fullwidth">Valider</button>

        </p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#submitQuizButton').on('click', function() {
            $('#submitQuizButton').css('pointer-events', 'none');
            $('#submitQuizButton').css('opacity', '0.5');
            $('#submitQuizButton').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>&emsp;EN COURS');

            let allAnswered = true;
            let unansweredCount = 0;
            let firstUnansweredElement = '';
            $('.error').removeClass('error');
            $('.questions .question').each(function () {
                const inputText = $(this).find('input[type="text"]');
                const inputRadios = $(this).find('input[type="radio"]');
                if (inputText.length && inputText.val().trim() === '') {
                    if(firstUnansweredElement === '') {
                        firstUnansweredElement = this.id;
                    }
                    allAnswered = false;
                    unansweredCount++;
                    inputText.addClass('error');
                }
                else if (inputRadios.length && !inputRadios.is(':checked')) {
                    if(firstUnansweredElement === '') {
                        firstUnansweredElement = this.id;
                    }
                    allAnswered = false;
                    unansweredCount++;
                    $(this).find('.radio-container').addClass('error');
                }
            });
            if (allAnswered) {
                const redirectUrl = "<?=$urlRemerciement?>";
                window.location.href = redirectUrl;
            } else {
                if(unansweredCount === 1) {
                    alert("Vous n'avez pas répondu à " + unansweredCount + " question.");
                } else {
                    alert("Vous n'avez pas répondu à " + unansweredCount + " questions.");
                }
                $('#submitQuizButton').css('pointer-events', 'auto');
                $('#submitQuizButton').css('opacity', '1');
                $('#submitQuizButton').html('Valider');

                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#" + firstUnansweredElement).offset().top - 40
                }, 1000);
            }
        });
        $('button.btn-ok').on('click', function() {

            const questionId = $(this).data('question');

            if (!$("input[name='" + questionId + "']:checked").val()) {
                alert('Vous devez répondre à la question avant de passer à la suivante');
                return false;
            }
            else {
                const nextQuestionId = $(this).data('next-question');

                if(nextQuestionId) {
                    const nextQuestion = $('#' + nextQuestionId);

                    if (nextQuestion.length) {
                        const offset = nextQuestion.offset().top;
                        const isMobile = window.innerWidth <= 768;
                        const scrollTo = isMobile
                            ? offset
                            : offset - ($(window).height() / 2) + (nextQuestion.outerHeight() / 2); // Centrage pour PC

                        $('html, body').animate({
                            scrollTop: scrollTo
                        }, 750);
                    } else {
                        $('html, body').animate({
                            scrollTop: $(document).height()
                        }, 750);
                    }
                }

            }
        });

        $('input[type="radio"]').on('change', function() {
            const radioInput = $(this);
            const questionContainer = radioInput.closest('.question');
            questionContainer.find('.radio-container').removeClass('error');
            const questionId = $(this).attr('name').split('_')[1];
            const response = $(this).val();
            const quizUserId = '<?=$quizUser->id ?>';
            var url = '<?=$urlSaveResponse?>';
            <?php if($modeTest == 0) { ?>
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    'question_id': questionId,
                    'response': response,
                    'quizUserId': quizUserId
                },
                success: function(response) {
                    console.log(response)
                    if (response.status === 'success') {
                        console.log("Réponse enregistrée !");
                    } else {
                        console.error("Échec de l'enregistrement de la réponse");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erreur lors de l'enregistrement de la réponse : ", error);
                }
            });
            <?php } ?>
        });
    });
</script>

