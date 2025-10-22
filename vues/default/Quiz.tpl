<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - Quiz';
?>

<?=Appy\Src\Html::css("assets/css/default/products.css")?>
<?=Appy\Src\Html::css("assets/css/table_responsive.css")?>
<?=Appy\Src\Html::css("assets/css/datatable.css")?>

<div class="container">
    <?php include BASE_PATH."vues/flash.tpl"?>

    <div class="level">
        <div class="level-left">
            <h1 class="title is-5">Liste des Questionnaires</h1>
        </div>
        <div class="column is-two-thirds">
            <div class="has-text-right">
                <a class="btn_popup_quiz button-valider" title="Ajouter un 360" data-id="btn_quiz_360"><i class="fas fa-plus"></i>&nbsp;360</a>
                <a class="btn_popup_create_barom button-valider" title="Ajouter un baromètre" ><i class="fas fa-plus"></i>&nbsp;BAROMETRE</a>
                <a class="btn_popup_create_prcc button-valider" title="Ajouter un PRCC" "><i class="fas fa-plus"></i>&nbsp;PRCC</a>
            </div>
        </div>
    </div>

    <div class="columns is-justify-content-flex-end is-fullwidth" style="display: flex; align-items: center;">
        <form action="<?=$url?>" method="POST" style="display: flex;">
            <div class="field" style="margin-right: 10px;">
                <select id="quiz-type" class="input" name="quiz-type" onchange="this.form.submit();">
                    <option value="">Sélectionner un type</option>
                    <option value="360" <?= (isset($_SESSION['recherche']['quiz-type']) && $_SESSION['recherche']['quiz-type'] == '360') ? 'selected' : '' ?>>360</option>
                    <option value="BAROM" <?= (isset($_SESSION['recherche']['quiz-type']) && $_SESSION['recherche']['quiz-type'] == 'BAROM') ? 'selected' : '' ?>>BAROMETRE</option>
                    <option value="PRCC" <?= (isset($_SESSION['recherche']['quiz-type']) && $_SESSION['recherche']['quiz-type'] == 'PRCC') ? 'selected' : '' ?>>PRCC</option>
                </select>
            </div>
        </form>
    </div>

    <table id="quiz" class="table is-fullwidth table_responsive table-rm">
        <thead>
        <tr>
            <th>Titre</th>
            <th style="width:50px;text-align: center">Type</th>
            <th style="width:110px">Répondants</th>
            <th style="width:110px">Réponses</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($quizzes as $quiz) { ?>
        <tr>
            <td>
                <p><?=$quiz->name?></p>
            </td>
            <td>
                <p style="text-align: center">
                    <?= $quiz->isTypeBarom() ? "BAROMETRE" : $quiz->type ?>
                </p>
            </td>

            <td>
                <p style="text-align: center"><?=$quiz->nbUsers?></p>
            </td>
            <td>
                <p style="text-align: center"><?=$quiz->nbReponses?></p>
            </td>
        <td>
            <a href="<?php
        if ($quiz->isType360()) { ?>
            <?=WEB_PATH?>quiz.html/editSingleQuiz360?quizId=<?=$quiz->id?>
        <?php } elseif ($quiz->isTypeBarom()) { ?>
            <?=WEB_PATH?>quiz.html/editSingleBarometre?quizId=<?=$quiz->id?>
        <?php } else { ?>
            <?=WEB_PATH?>quiz.html/editSinglePRCC?quizId=<?=$quiz->id?>
        <?php } ?>"
               class="icon-action-transform"
               title="Édition"
               data-id="<?=$quiz->id?>"
               data-nom="<?=$quiz->name?>"
               data-type="<?=$quiz->type?>"
               style="color: black;<?php if ($quiz->id == 1 || $quiz->id == 2 || $quiz->id == 3) echo 'pointer-events: none; opacity: 0.5;'; ?>"
            <span class="icon">
        <i class="fas fa-edit"></i>
    </span>
            </a>
            <a href="<?=WEB_PATH?>quiz.html/quizOptions?quizId=<?=$quiz->id?>" title="Options" data-id="<?=$quiz->id?>" data-nom="<?=$quiz->name?>" data-type="<?=$quiz->type?>" style="color: black;<?php if ($quiz->id == 1 || $quiz->id == 2 || $quiz->id == 3) echo 'pointer-events: none; opacity: 0.5;'; ?>">
    <span class="icon">
        <i class="fas fa-cogs" aria-hidden="true"></i>
    </span>
            </a>
            <a href="<?=WEB_PATH?>quiz.html/PublishQuiz?quizId=<?=$quiz->id?>" class="icon-action-transform" title="Publication" data-id="<?=$quiz->id?>" data-nom="<?=$quiz->name?>" data-type="<?=$quiz->type?>" style="color: black;<?php if ($quiz->isPublishable() == false || $quiz->id == 1 || $quiz->id == 2 || $quiz->id == 3)echo 'pointer-events: none; opacity: 0.5;'; ?>">
    <span class="icon">
        <i class="fas fa-paper-plane"></i>
    </span>
            </a>
            <a href="<?=WEB_PATH?>quiz.html/SuiviQuiz?quizId=<?=$quiz->id?>" class="icon-action-transform" title="Suivi" style="color: black<?php if ($quiz->isPublishable() == false && $quiz->id != 1 && $quiz->id != 2 & $quiz->id != 3)echo 'pointer-events: none; opacity: 0.5;'; ?>">
    <span class="icon">
        <i class="fas fa-table" aria-hidden="true"></i>
    </span>
            </a>
            <?php if (\Appy\Src\Config::mailActive()) { ?>
            <a href="<?=WEB_PATH?>report.html?quizId=<?=$quiz->id?>" class="icon-action-transform" title="Rapport" style="color: black;<?php if ($quiz->isPublishable() == false && $quiz->id != 1 && $quiz->id != 2 & $quiz->id != 3) echo 'pointer-events: none; opacity: 0.5;'; ?>">
    <span class="icon">
        <i class="fas fa-file" aria-hidden="true"></i>
    </span>
            </a>
            <?php } else { ?>
            <a href="#" onclick="alert('Les rapports ne sont pas disponibles en mode démo'); return false;" class="icon-action-transform" title="Rapport" style="color: black; opacity: 0.5;">
    <span class="icon">
        <i class="fas fa-file" aria-hidden="true"></i>
    </span>
            </a>
            <?php } ?>
            <a id="delete-quiz" class="icon-action-transform" onclick="deleteQuiz(<?= $quiz->id ?>)" title="Supprimer" style="color: black;<?php if ($quiz->id == 1 || $quiz->id == 2 || $quiz->id == 3) echo 'pointer-events: none; opacity: 0.5;'; ?>">
    <span class="icon">
        <i class="fas fa-trash"></i>
    </span>
            </a>

        </td>
    </tr>
    <?php } ?>
</tbody>

    </table>

</div>

<div id="fen_popup_quiz" style="display: none;">
    <?php include_once BASE_PATH."vues/default/QuizSet.tpl"?>
</div>
</div>

<div id="fen_popup_create_barom" style="display: none;">
    <?php include_once BASE_PATH."vues/default/CreateBarom.tpl"?>
</div>

<div id="fen_popup_create_prcc" style="display: none;">
    <?php include_once BASE_PATH."vues/default/CreatePrcc.tpl"?>
</div>

<!-- BOITES DE DIALOGUE -->
<?php if (!empty($msg_erreur)) { ?>
<div id="dialog" title="<?=$msg_erreur['titre']?>" style="display: none;">
    <p><?=$msg_erreur['msg']?></p>
</div>
<?php } ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script type="text/javascript">
    function deleteQuiz(quizId) {
        if (confirm("Confirmez-vous la suppression ?")) {
            url = "<?= WEB_PATH; ?>quiz.html/delete?quizId=" + quizId;
            document.location.href = url;
        }
    }
</script>

<!-- SCRIPTS -->
<?=Appy\Src\Html::moduleJS("assets/js/default/publish-quiz.js")?>
<?=Appy\Src\Html::moduleJS("assets/js/default/quiz-recherche-fenetres.js")?>
<?=Appy\Src\Html::moduleJS("assets/js/table_responsive.js")?>
<?=Appy\Src\Html::scriptJS("assets/js/datatable.js")?>


