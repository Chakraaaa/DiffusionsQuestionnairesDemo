    <form id="import_form" name="import_form" method="POST" action="<?=$urlImport?>" enctype="multipart/form-data">
        <hr class="divider">
        <div class="container">
            <div class="columns">
                <div class="column has-text-centered">
                    <label class="label is-center">Format import (3 colonnes nom prenom email)</label>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third has-text-right">
                    <label class="label is-right">Importer un fichier Excel :</label>
                </div>
                <div class="column is-two-thirds">
                    <div class="field is-right">
                        <div class="control">
                                <input type="file" name="fichier_excel" id="fichier_excel" accept=".xlsx" required>
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
                            <select id="groupe_id_import" class="input is-small" name="groupe_id_import" required>
                                <option value="" disabled selected>-- Sélectionner un groupe --</option>
                                <?php foreach ($groupes as $groupe) { ?>
                                <option value="<?= $groupe->id ?>"><?= $groupe->groupeName ?></option>
                                <?php } ?>
                            </select>
                            <button id="btnCreateGroupImport" class="ButtonGroupe">Créer un groupe</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="divider">

        <p class="has-text-right mt-5">
            <button id="btn_close_popup" type="button" class="button-fermer" name="close" value="fermer">Fermer</button>
            <button type="submit" class="button-valider" name="btnValider" value="valider">Valider</button>
        </p>
    </form>


