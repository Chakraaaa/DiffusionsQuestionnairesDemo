<form id="formCreateGroupe" name="formCreateGroupe" method="POST" action="<?=$urlCreate?>">

    <hr class="divider">

    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nom du Groupe :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="groupe_name" class="input is-small" type="text" name="groupe_name" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <p class="has-text-right mt-5">
        <button type="button" class="button-fermer"  id="button-fermer-pop-up-groupe" name="btnFermerPopUpGroupe" value="fermer">Fermer</button>
        <button type="submit" class="button-valider" name="btnValider" value="valider">Valider</button>
    </p>
</form>
