<form id="MultipleGroupeAditForm" name="MultipleGroupeAditForm" method="POST">

    <hr class="divider">

    <div class="container">
        <div class="columns" id="groupe">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Séléctionner un groupe  :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <select id="multiple-groupe-edit-id" class="input is-small" name="multiple-groupe-edit-id" required>
                            <option value="" >-- Sélectionner un groupe --</option>
                            <?php foreach ($groupes as $groupe) { ?>
                                <option value="<?= htmlspecialchars($groupe->id) ?>">
                                    <?= htmlspecialchars($groupe->groupeName) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <p class="has-text-right mt-5">
        <button type="button" id="btnCloseMultipleGroupEdit" class="button-fermer" name="btnCloseMultipleGroupEdit" value="fermer">Fermer</button>
        <button id="MultipleGroupEditButtonSubmit" type="button" class="button-valider" name="MultipleGroupEditButtonSubmit" value="valider">Valider</button>
    </p>
</form>