<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - PrccOptions';
?>

<?=Appy\Src\Html::css("assets/css/default/products.css")?>
<?=Appy\Src\Html::css("assets/css/table_responsive.css")?>
<?=Appy\Src\Html::css("assets/css/datatable.css")?>
<?=Appy\Src\Html::scriptJS("assets/js/tinymce/tinymce.min.js")?>

<div class="container">
    <?php include BASE_PATH."vues/flash.tpl"?>
</div>

<div class="columns">
    <div class="column is-2" style="background-color: white">
        <div class="side-menu">
            <div class="buttons-container">
                <a href="<?=WEB_PATH?>quiz.html/editSinglePRCC?quizId=<?=$quiz->id?>" class="button-with-icon" title="Édition">
                    <span class="icon"><i class="fas fa-edit"></i></span><span class="button-text">Édition</span>
                </a>
                <hr class="divider">
                <a href="<?=WEB_PATH?>quiz.html/quizOptions?quizId=<?=$quiz->id?>" class="button-with-icon" title="Options">
                    <span class="icon"><i class="fas fa-cogs" aria-hidden="true"></i></span><span class="button-text">Options</span>
                </a>
                <hr class="divider">
                <a <?php if ($quiz->isPublishable() == false) echo 'style="pointer-events: none; opacity: 0.5;"'; ?> href="<?=WEB_PATH?>quiz.html/PublishQuiz?quizId=<?=$quiz->id?>" class="button-with-icon" title="Publication">
                    <span class="icon"><i class="fas fa-paper-plane"></i></span><span class="button-text">Publication</span>
                </a>
                <hr class="divider">
                <a <?php if ($quiz->isPublishable() == false) echo 'style="pointer-events: none; opacity: 0.5;"'; ?> href="<?=WEB_PATH?>quiz.html/SuiviQuiz?quizId=<?=$quiz->id?>" class="button-with-icon" title="Suivi">
                    <span class="icon"><i class="fas fa-paper-plane"></i></span><span class="button-text">Suivi</span>
                </a>
                <hr class="divider">
                <a <?php if ($quiz->isPublishable() == false) echo 'style="pointer-events: none; opacity: 0.5;"'; ?> href="<?=WEB_PATH?>report.html?quizId=<?=$quiz->id?>" class="button-with-icon" title="Rapport">
                    <span class="icon"><i class="fas fa-file" aria-hidden="true"></i></span><span class="button-text">Rapport</span>
                </a>
            </div>
        </div>
    </div>

    <div class="column is-10">
        <div class="form-container" style="max-width: 100%">
            <div>
                <h1 class="title is-5 left-aligned">Options du PRCC</h1>
            </div>
            <form action="<?=$urlSubmitPrccOptions?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="quizId" value="<?=$quiz->id?>">

                <hr class="divider">

                <div class="field">
                    <label class="label textes-options">Titre du questionnaire</label>
                    <div class="control">
                        <input class="input input_titre" type="text" name="title" placeholder="Titre du questionnaire" value="<?= htmlspecialchars($quiz->name ?? '') ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label textes-options">Votre logo</label>
                    <div class="control">
                        <input type="file" name="logo" accept="image/*" onchange="previewLogo(this)">
                        <div id="logo-preview" style="margin-top: 10px;">
                            <?php if (!empty($quiz->logo)): ?>
                            <img src="<?= WEB_PATH . 'assets/images/logosClients/' . $quiz->logo ?>" alt="Logo actuel" style="max-width: 100px;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <label class="label textes-options">Date de début</label>
                    <div class="control">
                        <input class="input input_date" type="date" name="start_date" value="<?= htmlspecialchars($quiz->startDate ?? '') ?>" required>
                    </div>

                    <label class="label textes-options ml-5">Date de fin</label>
                    <div class="control">
                        <input class="input input_date" type="date" name="end_date" value="<?= htmlspecialchars($quiz->endDate ?? '') ?>" required>
                    </div>

                    <label class="label textes-options ml-5">Date de relance</label>
                    <div class="control">
                        <input class="input input_date" type="date" name="reminder_date" value="<?= htmlspecialchars($quiz->reminderDate ?? '') ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label textes-options">Couleur de fond du formulaire</label>
                    <div class="control">
                        <input class="input input_color" type="text" name="color_form" placeholder="#696252" value="<?= htmlspecialchars($quiz->colorForm ?? '#696252') ?>" required>
                    </div>
                </div>
                <!-- Assigner à un groupe -->
                <div class="field">
                    <label class="label textes-options">Assigner à un groupe</label>
                    <div class="control select-container">
                        <select id="groupe_id" class="input is-small" name="groupe_id" required <?= $quizUserExists ? 'disabled' : '' ?>>
                        <option value="">-- Choisir un groupe --</option>
                        <?php foreach ($groupes as $groupe): ?>
                        <option value="<?= $groupe->id ?>" <?= isset($quiz->groupeId) && $quiz->groupeId == $groupe->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($groupe->groupeName) ?>
                        </option>
                        <?php endforeach; ?>
                        </select>
                        <?php if ($quizUserExists): ?>
                        <input type="hidden" name="groupe_id" value="<?= $quiz->groupeId ?>">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field is-horizontal" id="respondant-field" style="display: <?= $quizUserExists ? 'none' : 'block' ?>;">
                    <label class="label">Choisir la personne évaluée OU laisser vide pour créer un PRRC pour chaque membre du groupe</label>
                    <div class="control select-container">
                        <select id="respondant_id" class="input is-small" name="respondant_id" <?= $quizUserExists ? 'disabled' : '' ?>>
                        <!-- Les options de répondants seront insérées ici via AJAX -->
                        </select>
                        <?php if ($quizUserExists): ?>
                        <input type="hidden" name="respondant_id" value="<?= $quiz->autoUserId?>">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field">
                    <label class="checkbox">
                        <input type="checkbox" id="show-header" <?= !empty($quiz->header) ? 'checked' : '' ?>> Ajouter un bandeau en entête (max 100 caractères)
                    </label>
                    <div class="mt-3" id="header-input" style="display: <?= !empty($quiz->header) ? 'block' : 'none' ?>;">
                        <input class="input" type="text" name="header" placeholder="Bandeau" maxlength="100" value="<?= htmlspecialchars($quiz->header ?? '') ?>">
                    </div>
                </div>

                <div class="field">
                    <label class="checkbox">
                        <input type="checkbox" id="add-intro" <?= !empty($quiz->intro) ? 'checked' : '' ?>> Ajouter une page d'introduction
                    </label>
                    <div class="mt-3" id="intro-input" style="display: <?= !empty($quiz->intro) ? 'block' : 'none' ?>;">
                        <textarea name="intro" id="intro-textarea"><?= htmlspecialchars($quiz->intro ?? '') ?></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="checkbox">
                        <input type="checkbox" id="add-conclusion" <?= !empty($quiz->conclusion) ? 'checked' : '' ?>> Ajouter une page de conclusion
                    </label>
                    <div class="mt-3" id="conclusion-input" style="display: <?= !empty($quiz->conclusion) ? 'block' : 'none' ?>;">
                        <textarea name="conclusion" id="conclusion-textarea"><?= htmlspecialchars($quiz->conclusion ?? '') ?></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="checkbox">
                        <input type="checkbox" id="show-footer" <?= !empty($quiz->footer) ? 'checked' : '' ?>> Ajouter un pied-de-page (max 100 caractères)
                    </label>
                    <div class="mt-3" id="footer-input" style="display: <?= !empty($quiz->footer) ? 'block' : 'none' ?>;">
                        <input class="input" type="text" name="footer" placeholder="Pied de page" maxlength="100" value="<?= htmlspecialchars($quiz->footer ?? '') ?>">
                    </div>
                </div>

                <hr class="divider">
                <div class="field has-text-right">
                    <a href="<?=$url?>" type="button" class="button-fermer">Fermer</a>
                    <button class="button-valider" type="submit">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- le jquery était pas importé, j'ai donc du rajouter cette ligne -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#groupe_id').change(function() {
            const groupeId = $(this).val();
            if (groupeId) {
                fetchRespondants(groupeId);
            } else {
                $('#respondant-field').hide();
                $('#respondant_id').empty();
            }
        });
        const groupeId = $('#groupe_id').val();
        if (groupeId) {
            $('#groupe_id').trigger('change');
        }
    });

    function fetchRespondants(groupeId) {
        $.ajax({
            url: '<?=$urlFetchUsers?>',
            type: 'GET',
            data: { 'groupeId': groupeId },
            success: function(data) {
                console.log(data);
                $('#respondant_id').html(data);
                $('#respondant-field').show();
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de la récupération des répondants : ", error);
                alert("Une erreur s'est produite lors de la récupération des répondants.");
            }
        });
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
<?=Appy\Src\Html::scriptJS("assets/js/default/quiz-options-20250310.js")?>
