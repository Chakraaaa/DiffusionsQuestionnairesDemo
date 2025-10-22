<?php
$this->titre = Appy\Src\Config::APPLI_NOM . ' - EditQuestionsBarom';
?>
<div class="container" style="font-family: Work Sans">
    <?php include BASE_PATH . "vues/flash.tpl" ?>

    <div class="level-left">
        <!-- Premier titre pour les catégories -->
        <h1 class="title is-5" style="font-weight: 600; margin-bottom: 30px;">PRCC - Catégories</h1>
    </div>

    <div class="categories">
        <?php
        $categoriesCounter = 0;
        foreach ($categories as $index => $category) {
        ?>
        <div class="category-item" id="category_<?= $category->id ?>"><?= htmlspecialchars($category->labelShort) ?></div>
        <div class="category-item" id="category_<?= $category->id ?>" style="margin-bottom: 20px;">
            <strong style="display: none">Catégorie <?= $categoriesCounter + 1 ?>:</strong> <?= htmlspecialchars($category->label) ?>
            <a class="icon-edit-categories" title="Modifier" data-id="<?= $category->id ?>" style="margin-left: 10px;">
                <span><i class="fas fa-edit"></i></span>
            </a>
        </div>
        <?php
            $categoriesCounter++;
        }
        ?>
    </div>

    <div class="level-left" style="margin-top: 50px;">
        <!-- Deuxième titre pour les questions -->
        <h1 class="title is-5" style="font-weight: 600; margin-bottom: 30px;">PRCC - Questions</h1>
    </div>

    <div class="questions">
        <?php
        $questionsCounter = 0;
        foreach ($questions as $index => $question) {
        $questionLabel = $question->label; // La div avec style est déjà stockée dans la BDD
        ?>
        <div id="question_<?= $question->id ?>" style="margin-bottom: 20px;">
            <div>
                <strong>Question <?= $questionsCounter + 1 ?>:</strong>
                <a class="icon-edit-questions" title="Modifier" data-id="<?= $question->id ?>" style="margin-left: 10px;">
                    <span><i class="fas fa-edit"></i></span>
                </a>
            </div>
            <?= $questionLabel ?>
        </div>
        <?php
            $questionsCounter++;
        }
        ?>
    </div>

    <div id="fen_pop_up_edit_categories" style="display: none;">
        <?php include_once BASE_PATH . "vues/default/ContentPopUpEditCategories.tpl" ?>
    </div>
    <div id="fen_pop_up_edit_radioPRCC" style="display: none;">
        <?php include_once BASE_PATH . "vues/default/ContentPopUpEditRadioPRCC.tpl" ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            const dialogs = {
                categories: $("#fen_pop_up_edit_categories").dialog({
                    modal: true,
                    autoOpen: false,
                    minHeight: $(window).height() * 0.3,
                    minWidth: $(window).width() * 0.4
                }),
                radio: $("#fen_pop_up_edit_radioPRCC").dialog({
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

            $(".icon-edit-questions").on('click', function () {
                const questionId = $(this).data("id");

                if (!questionId) {
                    alert("ID de la question introuvable.");
                    return;
                }

                const url = '<?= $urlPopUpEditQuestionPRCC ?>';
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        'question_id': questionId
                    },
                    success: function (response) {
                        closeAllDialogs();

                        $("#fen_pop_up_edit_radioPRCC").html(response).dialog("option", "title", "Modifier l'énoncé de la question").dialog("open");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erreur lors de la récupération de la question : ", error);
                        alert("Une erreur s'est produite. Veuillez réessayer.");
                    }
                });

                return false;
            });

            $(".icon-edit-categories").on('click', function () {
                const categoryId = $(this).data("id");

                if (!categoryId) {
                    alert("ID de la catégorie introuvable.");
                    return;
                }

                const url = '<?= $urlPopUpEditCategoryPRCC ?>';
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        'category_id': categoryId
                    },
                    success: function (response) {
                        closeAllDialogs();

                        $("#fen_pop_up_edit_categories").html(response).dialog("option", "title", "Modifier la catégorie").dialog("open");
                    },
                    error: function (xhr, status, error) {
                        console.error("Erreur lors de la récupération de la catégorie : ", error);
                        alert("Une erreur s'est produite. Veuillez réessayer.");
                    }
                });

                return false;
            });

            $(document).on('click', '#button-fermer-pop-up-edit-category-PRCC', function () {
                dialogs.categories.dialog("close");
            });

            $(document).on('click', '#button-fermer-pop-up-edit-question-PRCC', function () {
                dialogs.radio.dialog("close");
            });
        });
    </script>
</div>
