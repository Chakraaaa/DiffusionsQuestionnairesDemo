<form id="formCreateQuiz" name="formCreateQuiz" method="POST" action="<?=$urlCreate?>">
    <input id="quizType" type="hidden" name="quizType" value="360">
    <hr class="divider">
    <div class="container">
        <div class="columns">
            <div class="column is-one-third has-text-right">
                <label class="label is-right">Libellé du Questionnaire :</label>
            </div>
            <div class="column is-two-thirds">
                <div class="field is-right">
                    <div class="control">
                        <input id="quizName" class="input is-small" type="text" name="quizName" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="divider">
    <p class="has-text-right mt-5">
        <button type="button" id="ClosePopUpQuiz" class="button-fermer" name="btnFermerQuiz" value="fermer-quiz">Fermer</button>
        <button type="submit" class="button-valider" name="btnValiderQuiz" value="valider">Valider</button>
    </p>
</form>
