<form id="create-radio-list-form" name="create-radio-list-form" method="POST" action="<?=$urlCreateNewRadio360List?>">
    <hr class="divider">
    <div class="columns">
        <div class="column is-one-third has-text-right">
            <label class="label is-right">Nom de la liste pour répondants :</label>
        </div>
        <div class="column is-two-thirds">
            <div class="field is-right">
                <div class="control">
                    <input id="new-list-title-normal" class="input is-small" type="text" name="new_list_title_normal" value="" placeholder="Entrez un intitulé de liste pour répondant">
                </div>
            </div>
        </div>
    </div>
    <?php for ($i = 0; $i < 10; $i++){ ?>
    <div class="columns">
        <div style="padding: 0;" class="column mt-2">
            <div>
                <input id="new-list-item-normal-<?= $i ?>" class="input is-small" type="text" name="new_list_item_normal_<?= $i ?>" placeholder="Entrez un élément pour répondant">
            </div>
        </div>
    </div>
    <?php } ?>
    <hr class="divider">
    <div class="columns">
        <div class="column is-one-third has-text-right">
            <label class="label is-right">Nom de la liste pour le répondant auto-évalué :</label>
        </div>
        <div class="column is-two-thirds">
            <div class="field is-right">
                <div class="control">
                    <input id="new-list-title-auto-evaluated" class="input is-small" type="text" name="new_list_title_auto_evaluated" value="" placeholder="Entrez un intitulé de liste pour répondant auto-évalué">
                </div>
            </div>
        </div>
    </div>
    <?php for ($i = 0; $i < 10; $i++){ ?>
    <div class="columns">
        <div style="padding: 0;" class="column mt-2">
            <div>
                <input id="new-list-item-auto-evaluated-<?= $i ?>" class="input is-small" type="text" name="new_list_item_auto_evaluated_<?= $i ?>" placeholder="Entrez un élément pour répondant auto-évalué">
            </div>
        </div>
    </div>
    <?php } ?>
    <hr class="divider">
    <div class="field is-grouped has-text-right">
        <div class="control">
            <button type="button" id="button-fermer-pop-up-create-radio360-list" class="button-fermer">Fermer</button>
        </div>
        <div class="control">
            <button type="submit" class="button-valider">Valider</button>
        </div>
    </div>
</form>
