<form id="formCreateUser" name="formCreateUser" method="POST" action="<?=$urlCreateUser?>">
    <input type="hidden" name="form_type" value="single_user">

    <hr class="divider">

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Prénom :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="user_firstname" class="input is-small" type="text" name="user_firstname" value="">
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
                        <input id="user_lastname" class="input is-small" type="text" name="user_lastname" value="">
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
                        <input id="user_email" class="input is-small" type="text" name="user_email" value="">
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
                        <select id="groupe_id_single" class="input is-small" name="groupe_id_single">
                            <option value="" disabled selected>-- Sélectionner un groupe --</option>
                            <?php foreach ($groupes as $groupe) { ?>
                            <option value="<?= $groupe->id ?>"><?= $groupe->groupeName ?></option>
                            <?php } ?>
                        </select>
                        <button id="btnCreateGroupSingleUser" class="ButtonGroupe">Créer un groupe</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <p class="has-text-right mt-5">
        <button id="btnClosePopupUser" type="button" class="button-fermer" name="btnFermer" value="fermer">Fermer</button>
        <button type="submit" class="button-valider" name="btnValider" value="valider">Valider</button>
    </p>
</form>


