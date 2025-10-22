<form action="<?=$urlSaveTemplateEmail?>" method="POST">
    <div class="field">
        <div class="columns is-vcentered">
            <div class="column is-narrow">
                <label class="label">Nom du nouveau modèle:</label>
            </div>
            <div class="column">
                <div class="control">
                    <input type="text" name="templateName" class="input is-small" required>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="quizId" value="<?= htmlspecialchars($quiz->id) ?>">
    <input type="hidden" id="hidden-message" name="messageHTML">
    <div class="buttons is-right">
        <button type="button" class="button-fermer" id="close-save-template-popup">Fermer</button>
        <button type="submit" class="button-valider">Valider</button>
    </div>
</form>
