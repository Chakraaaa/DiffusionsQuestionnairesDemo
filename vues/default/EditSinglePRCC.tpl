<?php
$this->titre = Appy\Src\Config::APPLI_NOM . ' - EditSinglePRCC';
?>
<div class="container" style="font-family: Work Sans">
    <?php include BASE_PATH . "vues/flash.tpl" ?>

    <div class="level">
        <div class="level-left">
        </div>
        <div class="column is-two-thirds">
            <div class="has-text-right">
                <a target="_blank" href="<?= $urlModeTest ?>" class="button-valider" title="Tester le formulaire">Test PRCC</a>
            </div>
        </div>
    </div>

    <div class="level-left">
        <h1 class="title is-5" style="font-weight: 600; margin-bottom: 50px;">PRCC - Questions spécifiques</h1>
    </div>

    <div class="single-questions">
        <?php
        $questionCounter = 0;
        foreach ($questions as $index => $question) {
        $questionLabel = str_replace("Trebuchet MS", "Work Sans", $question->label);
        $questionCounter++;
        ?>
        <div class="single-question" id="single_question_<?= $question->id ?>" style="margin-bottom: 30px;">
            <div class="single-question-number">
                Question <?= $questionCounter ?>:
                <a class="icon-edit-single-questions" title="Modifier" data-id="<?= $question->id ?>">
                    <span><i class="fas fa-edit"></i></span>
                </a>
            </div>
            <div>
                <div class="text-response"><?= $question->label ?></div>
            </div>
        </div>
        <?php } ?>

        <div id="fen_pop_up_edit_single_question_PRCC" style="display: none;">
            <?php include_once BASE_PATH . "vues/default/PopUpEditSingleQuestionPrcc.tpl" ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            const dialogs = {
                radio: $("#fen_pop_up_edit_single_question_PRCC").dialog({
                    modal: true,
                    autoOpen: false,
                    minHeight: $(window).height() * 0.4,
                    minWidth: $(window).width() * 0.9
                }),
            };

            function closeAllDialogs() {
                for (let dialogKey in dialogs) {
                    dialogs[dialogKey].dialog("close");
                }
            }

            $(".icon-edit-single-questions").on('click', function () {
                const questionId = $(this).data("id");

                if (!questionId) {
                    alert("ID de la question introuvable.");
                    return;
                }

                const url = '<?= $urlPopUpEditSingleQuestionPRCC ?>';
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        'questionId': questionId,
                        'quizId': <?=$quizId?>
            },
                success: function (response) {
                    closeAllDialogs();

                    $("#fen_pop_up_edit_single_question_PRCC")
                        .html(response)
                        .dialog("option", "title", "Modifier le nom du chapitre")
                        .dialog("open");
                },
                error: function (xhr, status, error) {
                    console.error("Erreur lors de la récupération de la question : ", error);
                    alert("Une erreur s'est produite. Veuillez réessayer.");
                }
            });

                return false;
            });

            $(document).on('click', '#button-fermer-pop-up-edit-single-question-prcc', function () {
                dialogs.radio.dialog("close");
            });
        });
    </script>
</div>
