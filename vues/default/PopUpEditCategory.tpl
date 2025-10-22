<form id="edit-category-prcc-form" name="edit-category-prcc-form" method="POST" action="<?=$urlSavePopUpEditCategory?>">
    <input type="hidden" name="category_id" value="<?=$category->id?>">
    <hr class="divider">
    <div class="columns">
        <div class="column is-one-third has-text-right">
            <label class="label is-right">Libellé court pour le tableau:</label>
        </div>
        <div class="column is-two-thirds">
            <div class="field is-right">
                <div class="control">
                    <input <?php if ($category->id == 3 || $category->id == 6 || $category->id == 8 ||$category->id == 11 ||$category->id == 14) echo 'disabled'; ?> type="text" id="respondent_text_short" class="input is-small" name="respondent_text_short" value="<?= htmlspecialchars(strip_tags($category->labelShort)) ?>"></input>
                </div>
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column is-one-third has-text-right">
            <label class="label is-right">Libellé long pour le récapitulatif:</label>
        </div>
        <div class="column is-two-thirds">
            <div class="field is-right">
                <div class="control">
                    <input type="text" id="respondent_text" class="input is-small" name="respondent_text" value="<?= htmlspecialchars(strip_tags($category->label)) ?>"></input>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <div class="field is-grouped has-text-right">
        <div class="control">
            <button type="button" id="button-fermer-pop-up-edit-category-PRCC" class="button-fermer">Fermer</button>
        </div>
        <div class="control">
            <button type="submit" class="button-valider">Valider</button>
        </div>
    </div>
</form>