<?php
$this->titre = Appy\Src\Config::APPLI_NOM . ' - PublishQuiz';
if (isset($_GET['quizId']) && is_numeric($_GET['quizId'])) {
$quizId = intval($_GET['quizId']);
} else {
echo "<p style='color: red;'>Aucun ID de quiz spécifié.</p>";
exit;
}
$urlQuiz = WEB_PATH . "quiz.html/QuizId=" . $quizId;

?>

<?= Appy\Src\Html::css("assets/css/default/products.css") ?>
<?= Appy\Src\Html::css("assets/css/table_responsive.css") ?>
<?= Appy\Src\Html::css("assets/css/datatable.css") ?>
<?=Appy\Src\Html::scriptJS("assets/js/tinymce/tinymce.min.js")?>

<div class="container" style="margin: 0;">
    <?php include BASE_PATH . "vues/flash.tpl" ?>

    <!-- Structure en colonnes -->
    <div class="columns">
        <!-- Sidebar gauche -->
        <div class="column is-2" style="padding: 0; margin: 0;">
            <div class="box" style="height: 100%">
                <div class="buttons-container">
                    <a style="pointer-events: none;opacity: 0.5" href="#" class="button-with-icon" title="Édition">
                        <span class="icon"><i class="fas fa-edit"></i></span><span class="button-text">Édition</span>
                    </a>
                    <hr class="divider">
                    <a href="<?=WEB_PATH?>quiz.html/quizOptions?quizId=<?=$quiz->id?>" class="button-with-icon" title="Options">
                        <span class="icon"><i class="fas fa-cogs" aria-hidden="true"></i></span><span class="button-text">Options</span>
                    </a>
                    <hr class="divider">
                    <a href="<?=WEB_PATH?>quiz.html/PublishQuiz?quizId=<?=$quiz->id?>" class="button-with-icon" title="Publication">
                        <span class="icon"><i class="fas fa-paper-plane"></i></span><span class="button-text">Publication</span>
                    </a>
                    <hr class="divider">
                    <a href="<?=WEB_PATH?>quiz.html/SuiviQuiz?quizId=<?=$quiz->id?>" class="button-with-icon" title="Suivi">
                        <span class="icon"><i class="fas fa-paper-plane"></i></span><span class="button-text">Suivi</span>
                    </a>
                    <hr class="divider">
                    <a href="<?=WEB_PATH?>report.html?quizId=<?=$quiz->id?>" class="button-with-icon" title="Rapport">
                        <span class="icon"><i class="fas fa-file" aria-hidden="true"></i></span><span class="button-text">Rapport</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulaire principal -->
        <div class="column is-10 ml-5">
            <div class="form-container">
                <h1 class="title is-5">Publication du Questionnaire Anonyme</h1>

                <div class="column is-two-thirds" style="margin-top: 50px">
                    <div class="has-text-left">
                        <label class="label">Le document Word contient l'ensemble des courriers d'invitation.</label>
                        <?php if ($quiz->isType360()) { ?>
                            <label class="label">Attention à bien fournir à l'autoévalué(e) la seule invitation qui est nominative. Elle ne doit pas être donnée à une autre personne</label>
                            <label class="label">Toutes les autres invitations sont anonymes</label>
                        <?php } ?>
                        <br>
                        <a id="btn-generate-report" class="button-valider" title="Générer le rapport" href="<?=WEB_PATH?>quiz.html/courrier?quizId=<?=$quiz->id?>" onclick="buttonAppearance('#btn-generate-report','fas fa-file-word','Générer les courriers d\'invitation')"><i class="fas fa-file-word"></i>&nbsp;Générer les courriers d'invitation</a>

                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function buttonAppearance($btnId, $iconName, $text)
        {
            $($btnId).css('pointer-events', 'none');
            $($btnId).css('opacity', '0.5');
            $($btnId).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>&emsp;EN COURS');

            setTimeout(function () {
                $html = "<i class=\"" + $iconName + "\"></i>&emsp;" + $text;
                $($btnId).css('pointer-events', 'auto');
                $($btnId).css('opacity', '1');
                $($btnId).html($html);
            }, 1000);
        }
    </script>




    <div id="save-template-email-popup" style="display: none;">
        <?php include_once BASE_PATH."vues/default/SaveTemplateEmail.tpl"?>
    </div>

    <?=Appy\Src\Html::scriptJS("assets/js/default/pop-up-save-template-email.js");?>


