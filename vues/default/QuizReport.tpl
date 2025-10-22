<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - QuizOptions';
?>

<?=Appy\Src\Html::css("assets/css/table_responsive.css")?>
<?=Appy\Src\Html::css("assets/css/datatable.css")?>

<div class="container">
    <?php include BASE_PATH."vues/flash.tpl"?>
</div>

<div class="columns">
    <div class="column is-2" style="background-color: white">
        <div class="side-menu">
            <div class="buttons-container">
                <a href="#" class="button-with-icon" title="Édition">
                    <span class="icon"><i class="fas fa-edit"></i></span><span class="button-text">Édition</span>
                </a>
                <hr class="divider">
                <a href="<?=WEB_PATH?>quiz.html/quizOptions?quizId=<?=$quiz->id?>" class="button-with-icon" title="Options" style="<?php if ($quiz->id == 1 || $quiz->id == 2 || $quiz->id == 3) echo 'pointer-events: none; opacity: 0.5;'; ?>">
                    <span class="icon"><i class="fas fa-cogs" aria-hidden="true"></i></span><span class="button-text">Options</span>
                </a>
                <hr class="divider">
                <a href="<?=WEB_PATH?>quiz.html/PublishQuiz?quizId=<?=$quiz->id?>" class="button-with-icon" title="Publication" style="<?php if ($quiz->id == 1 || $quiz->id == 2 || $quiz->id == 3) echo 'pointer-events: none; opacity: 0.5;'; ?>">
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
    <div class="column is-10">
        <div class="form-container">
            <div>
                <h1 class="title is-5 left-aligned">Résultats du Questionnaire</h1>
            </div>
            <div class="column is-two-thirds" style="margin-top: 50px">
                <div class="has-text-left">
                    <a id="btn-generate-report" class="button-valider" title="Générer le rapport" href="<?=WEB_PATH?>report.html/generate<?=$quiz->type?>?quizId=<?=$quiz->id?>" onclick="buttonAppearance('#btn-generate-report','fas fa-file-word','Générer le rapport (env 25 secondes)','23000')"><i class="fas fa-file-word"></i>&nbsp;Générer le rapport (env 25 secondes)</a>
                </div>
            </div>
            <div class="column is-two-thirds">
                <div class="has-text-left">
                    <a id="btn-generate-report-garde" class="button-valider" title="Générer le rapport" href="<?=WEB_PATH?>report.html/generate<?=$quiz->type?>?quizId=<?=$quiz->id?>&pagegarde=1" onclick="buttonAppearance('#btn-generate-report-garde','fas fa-file-word','Générer le rapport avec page de garde  (env 25 secondes)','23000')"><i class="fas fa-file-word"></i>&nbsp;Générer le rapport avec page de garde (env 25 secondes)</a>
                </div>
            </div>
            <div class="column is-two-thirds">
                <div class="has-text-left">
                    <a id="btn-generate-data" class="btn-download-result-csv button-valider" title="Télécharger les résultats bruts" href="<?=WEB_PATH?>report.html/data<?=$quiz->type?>?quizId=<?=$quiz->id?>" onclick="buttonAppearance('#btn-generate-data','far fa-file-excel','Télécharger les résultats bruts (env 4 secondes)','4000')"><i class="far fa-file-excel"></i>&nbsp;Télécharger les résultats bruts (env 4 secondes)</a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- le jquery était pas importé, j'ai donc du rajouter cette ligne -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function buttonAppearance($btnId, $iconName, $text, $millisecond)
    {
        $($btnId).css('pointer-events', 'none');
        $($btnId).css('opacity', '0.5');
        $($btnId).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>&emsp;EN COURS');

        setTimeout(function () {
            $html = "<i class=\"" + $iconName + "\"></i>&emsp;" + $text;
            $($btnId).css('pointer-events', 'auto');
            $($btnId).css('opacity', '1');
            $($btnId).html($html);
        }, $millisecond);
    }

</script>


<!-- BOITES DE DIALOGUE -->
<?php if (!empty($msg_erreur)) { ?>
<div id="dialog" title="<?=$msg_erreur['titre']?>" style="display: none;">
    <p><?=$msg_erreur['msg']?></p>
</div>
<?php } ?>



<!-- SCRIPTS -->

<?=Appy\Src\Html::moduleJS("assets/js/table_responsive.js")?>
<?=Appy\Src\Html::scriptJS("assets/js/datatable.js")?>
