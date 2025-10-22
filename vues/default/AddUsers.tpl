<form id="AddUsersPopUp" name="formAddRepondants" method="POST" action="<?=$urlCreateUsers?>">
    <input id="RoleDesRepondants" class="input is-small" type="hidden" name="RoleDesRepondants" value="5">
    <input type="hidden" name="form_type" value="multiple_users">

    <hr class="divider">

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nombres de répondants :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="nbr_repondants" class="input is-small" type="number" name="nbr_repondants" value="" placeholder="Saisir un nombre entre 2 et 500.">
                    </div>
                </div>
            </div>
        </div>
        <div class="columns" id="groupe">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Assigner un groupe :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <select id="groupe_id_multiple" class="input is-small" name="groupe_id_multiple" required>
                            <option value="" disabled selected>-- Sélectionner un groupe --</option>
                            <?php foreach ($groupes as $groupe) { ?>
                            <option value="<?= $groupe->id ?>"><?= $groupe->groupeName ?></option>
                            <?php } ?>
                        </select>
                        <button id="btnCreateGroupMultipleUsers" class="ButtonGroupe">Créer un groupe</button>
                    </div>
                </div>
            </div>
        </div>

        <hr class="divider">

    <p class="has-text-right mt-5">
        <button id="BtnClosePopupDesUsers" type="button" class="button-fermer" name="btnFermer" value="fermer">Fermer</button>
        <button type="submit" class="button-valider" name="btnValider" value="valider">Valider</button>
    </p>
</form>

