<form id="AddGroupeMultiple" name="AddGroupeMultiple" method="POST">

    <hr class="divider">

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nom du Groupe :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="NewGroupNameMultiple" class="input is-small" type="text" name="NewGroupNameMultiple" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <p class="has-text-right mt-5">
        <button type="button" id="btnCloseGroupMultiple" class="button-fermer" name="btnCloseGroupMultiple" value="fermer">Fermer</button>
        <button id="addGroupButtonSubmitMultiple" type="button" class="button-valider" name="addGroupButtonSubmitMultiple" value="valider" onclick="submitAddGroupMultiple();">Valider</button>
    </p>
</form>