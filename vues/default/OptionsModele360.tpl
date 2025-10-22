<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - OptionsModele360';
?>

<?=Appy\Src\Html::css("assets/css/default/products.css")?>
<?=Appy\Src\Html::css("assets/css/table_responsive.css")?>
<?=Appy\Src\Html::css("assets/css/datatable.css")?>
<?=Appy\Src\Html::scriptJS("assets/js/tinymce/tinymce.min.js")?>

<div class="container">
    <?php include BASE_PATH."vues/flash.tpl"?>
</div>

<div class="form-container">
    <div>
        <h1 class="title is-5 left-aligned">Modèle 360 - Options</h1>
    </div>
    <form action="<?=$urlSaveModele?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="quiz_type" value="<?= $templateQuizOptions->quizType ?>">
        <hr class="divider">
        <div class="field">
            <label class="label textes-options">Couleur de fond du formulaire</label>
            <div class="control">
                <input class="input input_color" style="width: 100%;" type="text" name="color_form" placeholder="#696252" value="<?= htmlspecialchars($templateQuizOptions->colorForm ?? '#696252') ?>" required>
            </div>
        </div>

        <div class="field">
            <label class="checkbox">
                <input type="checkbox" id="show-header" <?= !empty($templateQuizOptions->header) ? 'checked' : '' ?>> Ajouter un bandeau en entête (max 100 caractères)
            </label>
            <div class="mt-3" id="header-input" style="display: <?= !empty($templateQuizOptions->header) ? 'block' : 'none' ?>;">
                <input class="input" style="width: 100%;" type="text" name="header" placeholder="Bandeau" maxlength="100" value="<?= htmlspecialchars($templateQuizOptions->header ?? '') ?>">
            </div>
        </div>

        <div class="field">
            <label class="checkbox">
                <input type="checkbox" id="add-intro" <?= !empty($templateQuizOptions->intro) ? 'checked' : '' ?>> Ajouter une page d'introduction
            </label>
            <div class="mt-3" id="intro-input" style="display: <?= !empty($templateQuizOptions->intro) ? 'block' : 'none' ?>;">
                <textarea name="intro" id="intro-textarea" style="width: 100%;"><?= htmlspecialchars($templateQuizOptions->intro ?? '') ?></textarea>
            </div>
        </div>

        <div class="field">
            <label class="checkbox">
                <input type="checkbox" id="add-conclusion" <?= !empty($templateQuizOptions->conclusion) ? 'checked' : '' ?>> Ajouter une page de conclusion
            </label>
            <div class="mt-3" id="conclusion-input" style="display: <?= !empty($templateQuizOptions->conclusion) ? 'block' : 'none' ?>;">
                <textarea name="conclusion" id="conclusion-textarea" style="width: 100%;"><?= htmlspecialchars($templateQuizOptions->conclusion ?? '') ?></textarea>
            </div>
        </div>

        <div class="field">
            <label class="checkbox">
                <input type="checkbox" id="show-footer" <?= !empty($templateQuizOptions->footer) ? 'checked' : '' ?>> Ajouter un pied-de-page (max 100 caractères)
            </label>
            <div class="mt-3" id="footer-input" style="display: <?= !empty($templateQuizOptions->footer) ? 'block' : 'none' ?>;">
                <input class="input" style="width: 100%;" type="text" name="footer" placeholder="Pied de page" maxlength="100" value="<?= htmlspecialchars($templateQuizOptions->footer ?? '') ?>">
            </div>
        </div>

        <div class="field">
            <label class="label textes-options mt-5">Courrier d'invitation</label>
            <div class="control">
                <div class="mt-5">
                    <h2 class="title is-6 has-text-centered">Paragraphe "Vous pouvez joindre :"</h2>
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line1" placeholder="Ligne 1" value="<?= htmlspecialchars($templateQuizOptions->ccP1L1 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line2" placeholder="Ligne 2" value="<?= htmlspecialchars($templateQuizOptions->ccP1L2 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line3" placeholder="Ligne 3" value="<?= htmlspecialchars($templateQuizOptions->ccP1L3 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line4" placeholder="Ligne 4" value="<?= htmlspecialchars($templateQuizOptions->ccP1L4 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph1_line5" placeholder="Ligne 5" value="<?= htmlspecialchars($templateQuizOptions->ccP1L5 ?? '') ?>">
                </div>
                <div class="mt-5" style="display: none">
                    <h2 class="title is-6 has-text-centered">Paragraphe 2</h2>
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line1" placeholder="Ligne 1" value="<?= htmlspecialchars($templateQuizOptions->ccP2L1 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line2" placeholder="Ligne 2" value="<?= htmlspecialchars($templateQuizOptions->ccP2L2 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line3" placeholder="Ligne 3" value="<?= htmlspecialchars($templateQuizOptions->ccP2L3 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line4" placeholder="Ligne 4" value="<?= htmlspecialchars($templateQuizOptions->ccP2L4 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph2_line5" placeholder="Ligne 5" value="<?= htmlspecialchars($templateQuizOptions->ccP2L5 ?? '') ?>">
                </div>
                <div class="mt-5" style="display: none">
                    <h2 class="title is-6 has-text-centered">Paragraphe 3</h2>
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line1" placeholder="Ligne 1" value="<?= htmlspecialchars($templateQuizOptions->ccP3L1 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line2" placeholder="Ligne 2" value="<?= htmlspecialchars($templateQuizOptions->ccP3L2 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line3" placeholder="Ligne 3" value="<?= htmlspecialchars($templateQuizOptions->ccP3L3 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line4" placeholder="Ligne 4" value="<?= htmlspecialchars($templateQuizOptions->ccP3L4 ?? '') ?>">
                    <input class="input mb-3" style="width: 100%; max-width: none;" type="text" name="paragraph3_line5" placeholder="Ligne 5" value="<?= htmlspecialchars($templateQuizOptions->ccP3L5 ?? '') ?>">
                </div>
            </div>
        </div>

        <hr class="divider">
        <div class="field has-text-right">
            <button class="button-valider" type="submit">Valider</button>
        </div>
    </form>
</div>

<!-- le jquery était pas importé, j'ai donc du rajouter cette ligne -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- BOITES DE DIALOGUE -->
<?php if (!empty($msg_erreur)) { ?>
<div id="dialog" title="<?=$msg_erreur['titre']?>" style="display: none;">
    <p><?=$msg_erreur['msg']?></p>
</div>
<?php } ?>

<?=Appy\Src\Html::scriptJS("assets/js/default/modele-360-options-20250310.js")?>
