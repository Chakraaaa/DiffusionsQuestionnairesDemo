<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - Utilisateurs';
?>
<div class="container" style="font-family: Work Sans">
    <?php include BASE_PATH."vues/flash.tpl"?>


    <div class="level">
        <div class="level-left">
            <h1 class="title is-5" style="font-weight: 600;">Modèle 360 - Chapitres et questions</h1>
        </div>
        <div class="column is-two-thirds">
            <div class="has-text-right">
                <a id="add_new_chapter360" class="button-valider" title="Ajouter un chapitre"><i class="fas fa-plus"></i>&nbsp;Ajouter un chapitre</a>
                <a id="add_new_radio360Text" class="button-valider" title="Ajouter une question simple"><i class="fas fa-plus"></i> Ajouter une question simple</a>
                <a id="add_new_radio360List" class="button-valider" title="Ajouter une question liste"><i class="fas fa-plus"></i> Ajouter une question liste</a>
            </div>
        </div>
    </div>
    <div class="questions">
        <?php
    $questionCounter = 0;
    foreach ($questions as $index => $question) {
        $questionLabel = str_replace("Trebuchet MS", "Work Sans", $question->label);
        $questionLabelAuto = str_replace("Trebuchet MS", "Work Sans", $question->labelAuto);
        if (strtoupper($question->questionType) === 'CHAPTER') {
        $questionCounter = 0;
        } else if ($question->ordre !== 2 && $question->ordre !== 3 && strtoupper($question->questionType) === 'INPUT-RADIO') {
        $questionCounter++;
        }
        ?>
        <div class="question" id="question_<?= $question->id ?>" style="margin-bottom: <?php if ($question->questionType == 'CHAPTER') { ?>20px<?php } else { ?>100px<?php } ?>;">
            <div class="question-number">
                <?php if (strtoupper($question->questionType) !== 'CHAPTER' && $question->ordre !== '2' && $question->ordre !== '3'): ?>
                Question <?= $questionCounter ?>:
                <?php endif; ?>
                <a class="icon-edit-questions" title="Modifier" data-id="<?= $question->id ?>">
                    <span><i class="fas fa-edit"></i></span>
                </a>
                <?php if ($question->ordre > 53): ?>
                <a href="<?= $urlDeleteQuestion360 ?>?QuestionId=<?= $question->id ?>"
                   class="icon-delete-questions"
                   title="Supprimer"
                   onclick="return confirm('<?= $question->question_type == 'CHAPTER' ? 'Êtes-vous sûr de vouloir supprimer le chapitre?' : 'Êtes-vous sûr de vouloir supprimer la question?' ?>');">
                    <span><i class="fas fa-trash"></i></span>
                </a>
                <?php endif; ?>
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

    <div id="fen_pop_up_edit_chapter360" style="display: none;">
        <?php include_once BASE_PATH."vues/default/ContentPopUpEditChapter360.tpl"?>
    </div>
    <div id="fen_pop_up_edit_radio360_text" style="display: none;">
        <?php include_once BASE_PATH."vues/default/ContentPopUpEditRadio360Text.tpl"?>
    </div>
    <div id="fen_pop_up_edit_radio360_list" style="display: none;">
        <?php include_once BASE_PATH."vues/default/ContentPopUpEditRadio360List.tpl"?>
    </div>
    <div id="fen_pop_up_edit_question_position" class="fen-pop-up" style="display:none;">
        <?php include_once BASE_PATH."vues/default/ContentPopUpEditQuestionPosition.tpl"?>
    </div>
    <div id="fen_pop_up_create_chapter360" style="display: none;">
        <?php include_once BASE_PATH."vues/default/PopUpCreateChapter360.tpl"?>
    </div>
    <div id="fen_pop_up_create_radio360_text" style="display: none;">
        <?php include_once BASE_PATH."vues/default/PopUpCreateRadio360Text.tpl"?>
    </div>
    <div id="fen_pop_up_create_radio360_list" style="display: none;">
        <?php include_once BASE_PATH."vues/default/PopUpCreateRadio360List.tpl"?>
    </div>

    <!-- le jquery était pas importé, j'ai donc du rajouter cette ligne -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            const dialogs = {
                chapter: $("#fen_pop_up_edit_chapter360").dialog({
                    modal: true,
                    autoOpen: false,
                    minHeight: $(window).height() * 0.4,
                    minWidth: $(window).width() * 0.4
                }),
                radioList: $("#fen_pop_up_edit_radio360_list").dialog({
                    modal: true,
                    autoOpen: false,
                    minHeight: $(window).height() * 0.5,
                    minWidth: $(window).width() * 0.9
                }),
                radioText: $("#fen_pop_up_edit_radio360_text").dialog({
                    modal: true,
                    autoOpen: false,
                    minHeight: $(window).height() * 0.5,
                    minWidth: $(window).width() * 0.5
                }),
            };

            function closeAllDialogs() {
                for (let dialogKey in dialogs) {
                    dialogs[dialogKey].dialog("close");
                }
            }

            $(".icon-edit-questions").on('click', function () {
                console.log("test");
                const questionId = $(this).data("id");

                if (!questionId) {
                    alert("ID de la question introuvable.");
                    return;
                }

                const url = '<?=$urlPopUpEditQuestion?>';
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        'question_id': questionId
                    },
                    success: function(response) {
                        closeAllDialogs();

                        if ($(response).find('#edit-chapter-form').length > 0) {
                            $("#fen_pop_up_edit_chapter360").html(response).dialog("option", "title", "Modifier le nom du chapitre").dialog("open");
                        } else if ($(response).find('#edit-radio-list-form').length > 0) {
                            $("#fen_pop_up_edit_radio360_list").html(response).dialog("option", "title", "Modifier la question (Liste)").dialog("open");
                        } else if ($(response).find('#edit-radio-text-form').length > 0) {
                            $("#fen_pop_up_edit_radio360_text").html(response).dialog("option", "title", "Modifier la question (Texte)").dialog("open");
                        } else {
                            alert("Type de question inconnu ou non pris en charge.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors de la récupération de la question : ", error);
                        alert("Une erreur s'est produite. Veuillez réessayer.");
                    }
                });

                return false;
            });

            $(document).on('click', '#button-switch-to-text', function () {
                const questionId = $(this).data('question-id');

                $.ajax({
                    url: '<?=$urlPopUpEditQuestion?>',
                    type: 'GET',
                    data: {
                        question_id: questionId,
                        switch_to_text: true
                    },
                    success: function(response) {
                        closeAllDialogs();
                        $('#fen_pop_up_edit_radio360_text').html(response).dialog("open");
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors de la modification de la question : ", error);
                        alert("Une erreur est survenue. Veuillez réessayer.");
                    }
                });
            });

            $(document).on('click', '#button-switch-to-list', function () {
                var respondentText = $('#respondent-text').val();
                var autoEvaluatedText = $('#auto-evaluated-text').val();
                const questionId = $(this).data('question-id');
                $.ajax({
                    url: '<?=$urlPopUpEditQuestion?>',
                    type: 'GET',
                    data: {
                        question_id: questionId,
                        switch_to_list: true,
                        list_title: respondentText,
                        list_title_auto: autoEvaluatedText
                    },
                    success: function(response) {
                        closeAllDialogs();
                        $('#fen_pop_up_edit_radio360_list').html(response).dialog("open");
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors de la modification de la question : ", error);
                        alert("Une erreur est survenue. Veuillez réessayer.");
                    }
                });
            });
            $(document).on('click', '#button-fermer-pop-up-edit-chapter360', function () {
                dialogs.chapter.dialog("close");
            });
            $(document).on('click', '#button-fermer-pop-up-edit-radio360-list', function () {
                dialogs.radioList.dialog("close");
            });
            $(document).on('click', '#button-fermer-pop-up-edit-radio360-text', function () {
                dialogs.radioText.dialog("close");
            });
        });

    </script>

    <?=Appy\Src\Html::scriptJS("assets/js/default/edit360-boutons.js");?>