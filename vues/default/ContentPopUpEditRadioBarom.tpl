<form id="edit-radio-barom-form" name="edit-radio-barom-form" method="POST" action="<?=$urlSavePopUpEditRadioBarom?>">
    <input type="hidden" name="question_id" value="<?=$question->id?>">
    <hr class="divider">
    <div class="columns">
        <div class="column is-one-third has-text-right">
            <label class="label is-right">Enoncé de la question :</label>
        </div>
        <div class="column is-two-thirds">
            <div class="field is-right">
                <div class="control">
                    <textarea id="respondent-text" class="textarea is-small" name="respondent_text" placeholder="Entrez l'énoncé de la question"><?= htmlspecialchars(strip_tags($question->label)) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <div class="field is-grouped has-text-right">
        <div class="control">
            <button type="button" id="button-fermer-pop-up-edit-radioBarom" class="button-fermer">Fermer</button>
        </div>
        <div class="control">
            <button type="submit" class="button-valider">Valider</button>
        </div>
    </div>
</form>