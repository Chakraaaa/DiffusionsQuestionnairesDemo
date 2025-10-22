<div>
    <form id="create-single-chapter-form" name="create-single-chapter-form" method="POST" action="<?=$urlCreateNewSingleChapter360?>">
        <hr class="divider">

        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nouveau nom du Chapitre :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="new-chapter-label" class="input is-small" type="text" name="new_chapter_label" value="">
                    </div>
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Nouveau nom du Chapitre (auto-évalué) :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="new-chapter-label-auto" class="input is-small" type="text" name="new_chapter_label_auto" value="">
                    </div>
                </div>
            </div>
        </div>

        <hr class="divider">

        <div class="field is-grouped has-text-right">
            <div class="control">
                <button id="button-fermer-pop-up-create-single-chapter360" type="button" class="button-fermer">Fermer</button>
            </div>
            <div class="control">
                <button type="submit" class="button-valider">Valider</button>
            </div>
        </div>
    </form>
</div>
