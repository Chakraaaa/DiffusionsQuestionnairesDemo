<form id="AddGroupeImport" name="AddGroupeImport" method="POST">

    <hr class="divider">

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nom du Groupe :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="NewGroupNameImport" class="input is-small" type="text" name="NewGroupNameImport" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <p class="has-text-right mt-5">
        <button type="button" id="btnCloseGroupImport" class="button-fermer" name="btnCloseGroupImport" value="fermer">Fermer</button>
        <button id="addGroupButtonSubmitImport" type="button" class="button-valider" name="addGroupButtonSubmitImport" value="valider" onclick="submitAddGroupImport();">Valider</button>
    </p>
</form>

