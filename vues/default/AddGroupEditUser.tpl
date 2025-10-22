<form id="AddGroupeEditUser" name="AddGroupeEditUser" method="POST">

    <hr class="divider">

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nom du Groupe :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="NewGroupNameEditUser" class="input is-small" type="text" name="NewGroupNameEditUser" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <p class="has-text-right mt-5">
        <button type="button" id="btnCloseGroupEditUser" class="button-fermer" name="btnCloseGroupEditUser" value="fermer">Fermer</button>
        <button id="addGroupButtonSubmitEditUser" type="button" class="button-valider" name="addGroupButtonSubmitEditUser" value="valider" onclick="submitAddGroupEditUser();">Valider</button>
    </p>
</form>