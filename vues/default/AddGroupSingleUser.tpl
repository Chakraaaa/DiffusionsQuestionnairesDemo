<form id="AddGroupeSingle" name="AddGroupeSingle" method="POST">

    <hr class="divider">

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nom du Groupe :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="NewGroupNameSingle" class="input is-small" type="text" name="NewGroupNameSingle" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <p class="has-text-right mt-5">
        <button type="button" id="btnCloseGroupSingle" class="button-fermer" name="btnCloseGroupSingle" value="fermer">Fermer</button>
        <button id="addGroupButtonSubmitSingle" type="button" class="button-valider" name="addGroupButtonSubmitSingle" value="valider" onclick="submitAddGroupSingle();">Valider</button>
    </p>
</form>