<style>
    .container {
        background-color: white;
        padding: 20px;
        width: 70%;
        margin-top: 40px;
        margin-bottom: 40px;
    }

    @media (max-width: 714px) {
        .container {
            background-color: white;
            padding: 5px;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
        }
</style>
<div class="container">
    <main style="background-color: white">
        <div class="columns is-mobile is-vcentered">
            <div class="column is-flex is-justify-content-flex-start">
                <?php if($quiz->logo) { ?>
                <img class="logo-response" src="<?= WEB_PATH . 'assets/images/logosClients/' . $quiz->logo ?>" alt="Logo">
                <?php } ?>
            </div>
            <div class="column is-flex is-justify-content-flex-end is-align-items-flex-start">
                <div style="display: flex; flex-direction: column; align-items: flex-end;">
                    <h1 id="quizTitle" style="font-size: 24px; font-weight: bold; color: #2c3e50;"><?=$quiz->name?></h1>
                </div>
            </div>
        </div>
        <div class="has-text-centered">
            <p> Merci d'avoir répondu à ce questionnaire</p>
            <p> Vos réponses ont bien été enregistrées</p>
        </div>
    </main>
</div>
