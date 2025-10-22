<form id="formEditUser" name="formEditUser" method="POST" action="<?=$urlEdit?>">
    <input id="user-edit-id" type="hidden" name="user_id">

    <hr class="divider">

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Prénom :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="user-edit-firstname" class="input is-small" type="text" name="user_firstname" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nom :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="user-edit-lastname" class="input is-small" type="text" name="user_lastname" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Email :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="user-edit-email" class="input is-small" type="email" name="user_email" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="columns" id="groupe">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Assigner à un groupe :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <select id="groupe-edit-id" class="input is-small" name="groupe-edit-id" required>
                            <!-- Option par défaut -->
                            <option value="" disabled>-- Sélectionner un groupe --</option>
                            <!-- Boucle pour les groupes -->
                            <?php foreach ($groupes as $groupe) { ?>
                            <option value="<?= htmlspecialchars($groupe->id) ?>"
                            <?= (!empty($utilisateur->groupId) && $utilisateur->groupId == $groupe->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($groupe->groupeName) ?>
                            </option>
                            <?php } ?>
                        </select>
                        <button class="ButtonGroupe" id="btnCreateGroupEditUser">Créer un groupe</button>
                    </div>
                </div>
            </div>
        </div>

        <hr class="divider">

    <p class="has-text-right mt-5">
        <button id="btnCloseEditPopupUser" type="button" class="button-fermer" name="btnFermer" value="fermer">Fermer</button>
        <button type="submit" class="button-valider" name="btnValider" value="valider">Valider</button>
    </p>
</form>
