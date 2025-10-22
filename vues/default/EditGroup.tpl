<form id="formEditGroupe" name="formEditGroupe" method="POST" action="<?=$urlEditGroup?>">
    <input type="hidden" id="groupe_id" name="groupe_id" value=""/>

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nom du Groupe :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="new_groupe_name" class="input is-small" type="text" name="new_groupe_name" value=""/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="has-text-right mt-5">
        <button type="button" class="button-fermer" id="button-fermer-pop-up-edit-groupe" name="btnFermerPopUpGroupe" value="fermer">Fermer</button>
        <button type="submit" class="button-valider" name="btnValider" value="valider">Valider</button>
    </p>
</form>
