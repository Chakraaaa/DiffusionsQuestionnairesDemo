<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - QuizOptions';
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
                <a href="<?=WEB_PATH?>quiz.html/editSingleQuiz360?quizId=<?=$quiz->id?>" class="button-with-icon" title="Édition">
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

    <div class="column is-10">
        <div class="form-container" style="max-width: 100%">
            <div>
                <h1 class="title is-5 left-aligned">Options du 360</h1>
            </div>
            <form action="<?=$urlSubmitQuizOptions?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="quizId" value="<?=$quizId?>">

                <hr class="divider">

                <div class="field">
                        <label class="checkbox">
                            <input type="checkbox" id="anonymousBroadcast" name="anonymousBroadcast" value="0" <?= isset($quiz->anonymous) && $quiz->anonymous == 1 ? 'checked' : '' ?>> Diffusion anonyme du questionnaire
                        </label>
                </div>

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
                    <label class="label">Choisir la personne évaluée</label>
                    <div class="control select-container">
                        <select id="respondant_id" class="input is-small" name="respondant_id" <?= $quizUserExists ? 'disabled' : '' ?> required>
                        <!-- Les options de répondants seront insérées ici via AJAX -->
                        </select>
                        <?php if ($quizUserExists): ?>
                        <input type="hidden" name="respondant_id" value="<?= $quiz->autoUserId?>">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="field is-horizontal" id="sexe-container" style="display: none;">
                    <label class="label">Sexe de la personne évaluée</label>
                    <div class="control select-container">
                        <select name="sexe_auto_user" class="input is-small" required>
                            <option value="" disabled selected>-- Choisir le sexe de la personne évaluée --</option>
                            <option value="H" <?= isset($quiz->sexeAutoUser) && $quiz->sexeAutoUser == 'H' ? 'selected' : '' ?>>Homme</option>
                            <option value="F" <?= isset($quiz->sexeAutoUser) && $quiz->sexeAutoUser == 'F' ? 'selected' : '' ?>>Femme</option>
                            <option value="X" <?= isset($quiz->sexeAutoUser) && $quiz->sexeAutoUser == 'X' ? 'selected' : '' ?>>X</option>
                        </select>
                    </div>
                </div>
                <div class="field is-horizontal" id="fonction-personne" style="display: none;">
                    <label class="label">Fonction de la personne évaluée</label>
                    <div class="control">
                        <input class="input" type="text" name="fonction_auto_user" placeholder="ex: Directeur, Manager, etc." value="<?= htmlspecialchars($quiz->fonctionAutoUser ?? '') ?>" required>
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

                <div class="field" id="courrier-convocation" style="display: <?= isset($quiz->anonymous) && $quiz->anonymous == 1 ? 'block' : 'none' ?>;">
                    <label class="label textes-options mt-5">Courrier d'invitation </label>
                    <div class="mt-5">
                        <h2 class="title is-6 has-text-centered">Paragraphe  "Vous pouvez joindre :"</h2>
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line1" placeholder="Ligne 1" value="<?= htmlspecialchars($quiz->ccP1L1 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line2" placeholder="Ligne 2" value="<?= htmlspecialchars($quiz->ccP1L2 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line3" placeholder="Ligne 3" value="<?= htmlspecialchars($quiz->ccP1L3 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line4" placeholder="Ligne 4" value="<?= htmlspecialchars($quiz->ccP1L4 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line5" placeholder="Ligne 5" value="<?= htmlspecialchars($quiz->ccP1L5 ?? '') ?>">
                    </div>
                    <div class="mt-5" style="display: none">
                        <h2 class="title is-6 has-text-centered">Paragraphe 2</h2>
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line1" placeholder="Ligne 1" value="<?= htmlspecialchars($quiz->ccP2L1 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line2" placeholder="Ligne 2" value="<?= htmlspecialchars($quiz->ccP2L2 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line3" placeholder="Ligne 3" value="<?= htmlspecialchars($quiz->ccP2L3 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line4" placeholder="Ligne 4" value="<?= htmlspecialchars($quiz->ccP2L4 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line5" placeholder="Ligne 5" value="<?= htmlspecialchars($quiz->ccP2L5 ?? '') ?>">
                    </div>
                    <div class="mt-5" style="display: none">
                        <h2 class="title is-6 has-text-centered">Paragraphe 3</h2>
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line1" placeholder="Ligne 1" value="<?= htmlspecialchars($quiz->ccP3L1 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line2" placeholder="Ligne 2" value="<?= htmlspecialchars($quiz->ccP3L2 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line3" placeholder="Ligne 3" value="<?= htmlspecialchars($quiz->ccP3L3 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line4" placeholder="Ligne 4" value="<?= htmlspecialchars($quiz->ccP3L4 ?? '') ?>">
                        <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line5" placeholder="Ligne 5" value="<?= htmlspecialchars($quiz->ccP3L5 ?? '') ?>">
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
        $('#anonymousBroadcast').on('change', function() {
            if ($(this).prop('checked')) {
                $(this).val(1);
                $('#courrier-convocation').show();
            } else {
                $(this).val(0);
                $('#courrier-convocation').hide();
            }
        });

        if ($('#anonymousBroadcast').prop('checked')) {
            $('#courrier-convocation').show();
        } else {
            $('#courrier-convocation').hide();
        }

        $('#groupe_id').change(function() {
            $('#sexe-container').show();
            $('#fonction-personne').show();
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
            url: '<?=$urlFetchRespondants?>',
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
