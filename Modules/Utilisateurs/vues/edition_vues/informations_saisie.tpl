
<div class="columns is-multiline">

    <div class="column is-two-thirds">

        <div class="field is-horizontal">
            <div class="field-body">
                <div class="field">
                    <p class="control is-expanded has-icons-left">
                        <input class="input" type="text" placeholder="Prénom" name="prenom" value="<?=$ligne_editee->firstname?>" required="true">
                    </p>
                </div>
                <div class="field">
                    <p class="control is-expanded has-icons-left has-icons-right">
                        <input class="input is-success" type="text" placeholder="Nom" name="nom" value="<?=$ligne_editee->lastname?>" required="true">
                    </p>
                </div>
                <div class="field is-narrow">
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select name="role" required="true">
                                <option value="">--Sélectionner un rôle--</option>
                                <?php if (isset($utilisateur->role) && $utilisateur->role == 1) { ?>
                                    <option value="2" <?php if ($ligne_editee->role == 1) { ?> selected <?php }?>>Administrateur</option>
                                <?php }?>
                                <option value="3" <?php if ($ligne_editee->role == 2) { ?> selected <?php }?>>Gestionnaire</option>
                                <option value="4" <?php if ($ligne_editee->role == 3) { ?> selected<?php }?>>Consultant</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="has-text-right">
    <input type="hidden" name="ligne_editee" value="<?=$ligne_editee->id?>" >
    <button class="button-valider" name="save_ligne" title="Enregistrer les modifications"><i class="fas fa-save"></i>&nbsp;Valider</button>
</div>
