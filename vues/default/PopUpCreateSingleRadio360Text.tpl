<div>
    <form id="create-single-radio-text-form" name="create-single-radio-text-form" method="POST" action="<?=$urlCreateNewSingleRadio360Text?>">
        <hr class="divider">

        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Répondant normal :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <textarea id="respondent-text-create" class="textarea is-small" name="respondent_text" placeholder="Entrez le texte pour les répondants normaux"></textarea>
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
                        <textarea id="auto-evaluated-text-create" class="textarea is-small" name="auto_evaluated_text" placeholder="Entrez le texte pour les auto-évalués"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <hr class="divider">

        <div class="field is-grouped has-text-right">
            <div class="control">
                <button type="button" id="button-fermer-pop-up-create-single-radio360-text" class="button-fermer">Fermer</button>
            </div>
            <div class="control">
                <button type="submit" class="button-valider">Valider</button>
            </div>
        </div>
    </form>
</div>
