<form id="edit-single-radio-barom-form" name="edit-single-radio-barom-form" method="POST" action="<?=$urlSavePopUpEditSingleRadioBarom?>">
    <input type="hidden" name="question_id" value="<?=$question->id?>">
    <hr class="divider">

    <div class="columns">
        <div class="column is-one-third has-text-right">
            <label class="label is-right">Enoncé de la question :</label>
        </div>
        <div class="column is-two-thirds">
            <div class="field is-right">
                <div class="control">
                    <textarea id="single-respondent-text" class="textarea is-small" name="single_respondent_text" placeholder="Entrez le texte pour les répondants normaux"><?= htmlspecialchars(strip_tags($question->label)) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <hr class="divider">

    <div class="field is-grouped has-text-right">
        <div class="control">
            <button type="button" id="button-fermer-pop-up-edit-single-radioBarom-text" class="button-fermer">Fermer</button>
        </div>
        <div class="control">
            <button type="submit" class="button-valider">Valider</button>
        </div>
    </div>
</form>
