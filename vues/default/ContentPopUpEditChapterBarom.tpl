<form id="edit-chapter-form" name="edit-chapter-form" method="POST" action="<?=$urlSavePopUpEditChapterBarom?>">
    <input type="hidden" name="question_id" value="<?=$question->id?>">

    <hr class="divider">

    <div class="columns">
        <div class="column is-one-third has-text-right">
            <label class="label is-right">Nom du chapitre:</label>
        </div>
        <div class="column is-two-thirds">
            <div class="field is-right">
                <div class="control">
                    <input id="chapter-label" class="input is-small" type="text" name="chapter_label" value="<?= strip_tags($question->label) ?>">
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <div class="field is-grouped has-text-right">
        <div class="control">
            <button id="button-fermer-pop-up-edit-chapterBarom" type="button" class="button-fermer">Fermer</button>
        </div>
        <div class="control">
            <button type="submit" class="button-valider">Valider</button>
        </div>
    </div>
</form>
