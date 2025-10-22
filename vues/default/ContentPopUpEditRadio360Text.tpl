<div class="has-text-right">
    <button type="button" id="button-switch-to-list" class="button-valider" data-question-id="<?=$question->id?>">Passer en mode liste</button>
</div>
<form id="edit-radio-text-form" name="edit-radio-text-form" method="POST" action="<?=$urlSavePopUpEditRadio360Text?>">
        <input type="hidden" name="question_id" value="<?=$question->id?>">
        <hr class="divider">

        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Répondant normal :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <textarea id="respondent-text" class="textarea is-small" name="respondent_text" placeholder="Entrez le texte pour les répondants normaux"><?= htmlspecialchars(strip_tags($question->label)) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Répondant auto-évalué :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <textarea id="auto-evaluated-text" class="textarea is-small" name="auto_evaluated_text" placeholder="Entrez le texte pour les auto-évalués"><?= htmlspecialchars(strip_tags($question->labelAuto)) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <hr class="divider">

        <div class="field is-grouped has-text-right">
            <div class="control">
                <button type="button" id="button-fermer-pop-up-edit-radio360-text" class="button-fermer">Fermer</button>
            </div>
            <div class="control">
                <button type="submit" class="button-valider">Valider</button>
            </div>
        </div>
    </form>