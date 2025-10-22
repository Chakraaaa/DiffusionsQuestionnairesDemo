<?php
$this->titre = Appy\Src\Config::APPLI_NOM . ' - EditSingleQuestionsBarom';
?>
<div class="container" style="font-family: Work Sans">
    <?php include BASE_PATH . "vues/flash.tpl" ?>

    <div class="level">
        <div class="level-left">
        </div>
        <div class="column is-two-thirds">
            <div class="has-text-right">
                <a target="_blank" href="<?= $urlModeTest ?>" class="button-valider" title="Tester le formulaire">Test Baromètre</a>
            </div>
        </div>
    </div>

    <div class="level-left">
        <h1 class="title is-5" style="font-weight: 600; margin-bottom: 50px;">Baromètre - Questions spécifiques</h1>
    </div>

    <div class="single-questions">
        <?php
        $questionCounter = 0;
        $chapitreCounter = 0;
        foreach ($questions as $index => $question) {
        $questionLabel = str_replace("Trebuchet MS", "Work Sans", $question->label);
        if (strtoupper($question->questionType) === 'CHAPTER') {
        $chapitreCounter++;
        $questionCounter = 0;
        } else if (strtoupper($question->questionType) === 'INPUT-RADIO') {
        $questionCounter++;
        }
        ?>
        <div class="single-question" id="single_question_<?= $question->id ?>" style="margin-bottom: <?= ($question->questionType == 'CHAPTER') ? '20px' : '100px'; ?>;">
            <div class="single-question-number">
                <?php if (strtoupper($question->questionType) === 'CHAPTER') { ?>
                Chapitre <?= $chapitreCounter ?>:
                <?php } else { ?>
                Question <?= $questionCounter ?>:
                <?php } ?>
                <a class="icon-edit-single-questions" title="Modifier" data-id="<?= $question->id ?>">
                    <span><i class="fas fa-edit"></i></span>
                </a>
            </div>
            <?php
                switch (strtoupper($question->questionType)) {
            case 'INPUT-TEXT': ?>
            <div><?= $question->label ?></div>
            <?php break;

                    case 'INPUT-RADIO': ?>
            <div>
                <div class="text-response"><?= $question->label ?></div>
            </div>
            <?php break;

                    case 'TEXT': ?>
            <div><?= $question->label ?></div>
            <?php break;

                    case 'CHAPTER': ?>
            <h2><?= $question->label ?></h2>
            <?php break;
                }
                ?>
        </div>
        <?php } ?>
    </div>

    <div id="fen_pop_up_edit_single_chapterBarom" style="display: none;">
        <?php include_once BASE_PATH . "vues/default/ContentPopUpEditSingleChapterBarom.tpl" ?>
    </div>
    <div id="fen_pop_up_edit_single_radioBarom" style="display: none;">
        <?php include_once BASE_PATH . "vues/default/ContentPopUpEditSingleRadioBarom.tpl" ?>
    </div>

    <!-- jQuery Import -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            const dialogs = {
                chapter: $("#fen_pop_up_edit_single_chapterBarom").dialog({
                    modal: true,
                    autoOpen: false,
                    minHeight: $(window).height() * 0.3,
                    minWidth: $(window).width() * 0.4
                }),
                radio: $("#fen_pop_up_edit_single_radioBarom").dialog({
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

                const url = '<?= $urlPopUpEditSingleQuestionBarom ?>';
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        'questionId': questionId,
                        'quizId': <?=$quizId?>
                    },
                    success: function (response) {
                        closeAllDialogs();

                        if ($(response).find('#edit-single-chapter-form').length > 0) {
                            $("#fen_pop_up_edit_single_chapterBarom").html(response).dialog("option", "title", "Modifier le nom du chapitre").dialog("open");
                        } else if ($(response).find('#edit-single-radio-barom-form').length > 0){
                            $("#fen_pop_up_edit_single_radioBarom").html(response).dialog("option", "title", "Modifier l'énoncé de la question").dialog("open");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Erreur lors de la récupération de la question : ", error);
                        alert("Une erreur s'est produite. Veuillez réessayer.");
                    }
                });

                return false;
            });

            $(document).on('click', '#button-fermer-pop-up-edit-single-chapterBarom', function () {
                dialogs.chapter.dialog("close");
            });

            $(document).on('click', '#button-fermer-pop-up-edit-single-radioBarom-text', function () {
                dialogs.radio.dialog("close");
            });
        });
    </script>
</div>
