<div class="has-text-right">
<button type="button" id="button-switch-to-text" class="button-valider" data-question-id="<?=$question->id?>">Passer en mode texte</button>
</div>
<form id="edit-radio-list-form" name="edit-radio-list-form" method="POST" action="<?=$urlSavePopUpEditRadio360List?>">
    <input type="hidden" name="question_id" value="<?=$question->id?>">

    <hr class="divider">
    <div class="columns mt-5">
        <div class="column is-one-third is-paddingless">
            <label class="label is-right">Nom de la liste pour les répondants :</label>
        </div>
        <div class="column is-two-thirds is-paddingless">
                    <input id="list-title-normal" class="input is-small is" type="text" name="list_title_normal" value="<?= htmlspecialchars(strip_tags($listTitle)) ?>" placeholder="Entrez un intitulé de liste pour répondant normal">
        </div>
    </div>

    <?php foreach ($listItems as $index => $item){ ?>
    <div class="columns">
        <div style="padding: 0;" class="column mt-2">
                <div>
                    <input id="list-item-normal-<?= $index ?>" class="input is-small" type="text" name="list_item_normal_<?= $index ?>" value="<?= htmlspecialchars(strip_tags($item)) ?>" placeholder="Entrez un élément pour répondant normal">
                </div>
        </div>
    </div>
    <?php } ?>

    <?php for ($i = count($listItems); $i < 10; $i++){ ?>
    <div class="columns">
        <div style="padding: 0;" class="column mt-2">
                <div>
                    <input id="list-item-normal-<?= $i ?>" class="input is-small" type="text" name="list_item_normal_<?= $i ?>" placeholder="Entrez un élément pour répondant">
                </div>
        </div>
    </div>
    <?php } ?>
    <hr class="divider">
    <div class="columns mt-5">
        <div class="column is-one-third is-paddingless">
            <label class="label">Nom de la liste pour le répondant auto-évalué :</label>
        </div>
        <div class="column is-two-thirds is-paddingless">
            <div class="field">
                    <input id="list-title-auto-evaluated" class="input is-small" type="text" name="list_title_auto_evaluated" value="<?= htmlspecialchars(strip_tags($listTitleAuto)) ?>" placeholder="Entrez un intitulé de liste pour répondant auto-évalué">
            </div>
        </div>
    </div>

    <?php foreach ($listItemsAuto as $index => $item){ ?>
    <div class="columns">
        <div style="padding: 0;" class="column mt-2">
                <div>
                    <input id="list-item-auto-evaluated-<?= $index ?>" class="input is-small" type="text" name="list_item_auto_evaluated_<?= $index ?>" value="<?= htmlspecialchars(strip_tags($item)) ?>" placeholder="Entrez un élément pour répondant">
                </div>
        </div>
    </div>
    <?php } ?>

    <?php for ($i = count($listItemsAuto); $i < 10; $i++){ ?>
    <div class="columns">
        <div style="padding: 0;" class="column mt-2">
                <div>
                    <input id="list-item-auto-evaluated-<?= $i ?>" class="input is-small" type="text" name="list_item_auto_evaluated_<?= $i ?>" placeholder="Entrez un élément pour répondant auto-évalué">
                </div>
        </div>
    </div>
    <?php } ?>

    <hr class="divider">

    <div class="field is-grouped has-text-right">
        <div class="control">
            <button type="button" id="button-fermer-pop-up-edit-radio360-list" class="button-fermer">Fermer</button>
        </div>
        <div class="control">
            <button type="submit" class="button-valider">Valider</button>
        </div>
    </div>
</form>
