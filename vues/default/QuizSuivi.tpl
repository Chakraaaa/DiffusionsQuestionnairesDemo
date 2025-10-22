<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - QuizOptions';
?>

<?=Appy\Src\Html::css("assets/css/table_responsive.css")?>
<?=Appy\Src\Html::css("assets/css/datatable.css")?>

<style>
    .progress-bordered {
        border: 1px solid #666;
        border-radius: 8px;
    }

</style>

<div class="container">
    <?php include BASE_PATH."vues/flash.tpl"?>
</div>

<div class="columns">
    <div class="column is-2" style="background-color: white">
        <div class="side-menu">
            <div class="buttons-container">
                <?php if($quiz->isType360()) { ?>
                    <a href="<?=WEB_PATH?>quiz.html/editSingleQuiz360?quizId=<?=$quiz->id?>" class="button-with-icon" title="Édition">
                        <span class="icon"><i class="fas fa-edit"></i></span><span class="button-text">Édition</span>
                    </a>
                <?php } elseif($quiz->isTypeBAROM()) { ?>
                    <a href="<?=WEB_PATH?>quiz.html/editSingleBarometre?quizId=<?=$quiz->id?>" class="button-with-icon" title="Édition">
                        <span class="icon"><i class="fas fa-edit"></i></span><span class="button-text">Édition</span>
                    </a>
                <?php } else { ?>
                    <a href="<?=WEB_PATH?>quiz.html/editSinglePRCC?quizId=<?=$quiz->id?>" class="button-with-icon" title="Édition">
                        <span class="icon"><i class="fas fa-edit"></i></span><span class="button-text">Édition</span>
                    </a>
                <?php } ?>
                <hr class="divider">
                <a href="<?=WEB_PATH?>quiz.html/quizOptions?quizId=<?=$quiz->id?>" class="button-with-icon" title="Options" style="<?php if ($quiz->id == 1 || $quiz->id == 2 || $quiz->id == 3) echo 'pointer-events: none; opacity: 0.5;'; ?>">
                    <span class="icon"><i class="fas fa-cogs" aria-hidden="true"></i></span><span class="button-text">Options</span>
                </a>
                <hr class="divider">
                <a href="<?=WEB_PATH?>quiz.html/PublishQuiz?quizId=<?=$quiz->id?>" class="button-with-icon" title="Publication" style="<?php if ($quiz->id == 1 || $quiz->id == 2 || $quiz->id == 3) echo 'pointer-events: none; opacity: 0.5;'; ?>">
                    <span class="icon"><i class="fas fa-paper-plane"></i></span><span class="button-text">Publication</span>
                </a>
                <hr class="divider">
                <a href="<?=WEB_PATH?>quiz.html/SuiviQuiz?quizId=<?=$quiz->id?>" class="button-with-icon" title="Suivi">
                    <span class="icon"><i class="fas fa-paper-plane"></i></span><span class="button-text">Suivi</span>
                </a>
                <hr class="divider">
                <a href="<?=WEB_PATH?>report.html?quizId=<?=$quiz->id?>" class="button-with-icon" title="Rapport">
                    <span class="icon"><i class="fas fa-file" aria-hidden="true"></i></span><span class="button-text">Rapport</span>
                </a>
            </div>
        </div>
    </div>

    <div class="column is-10">
        <div class="form-container">
            <div>
                <h1 class="title is-5 left-aligned">Suivi du Questionnaire</h1>
            </div>
            <div style="margin: 20px; padding: 10px;">
                <p>Taux de réponses : <strong><?= $tauxReponses ?>% (<?=$nbrQuizUsersFinish?>/<?=$nbrQuizUsers?>)</strong></p>
            </div>
            <table id="tableSuiviQuizUsers" class="table is-fullwidth table_responsive table-rm">
                <thead>
                <tr>
                    <th style="font-weight: 600;">Nom</th>
                    <th style="font-weight: 600;">Prénom</th>
                    <th style="font-weight: 600;">Email</th>
                    <th style="font-weight: 600;">Identifiant</th>
                    <th style="font-weight: 600;">Nb Réponses</th>
                    <th style="font-weight: 600;">Nb Questions</th>
                    <th style="font-weight: 600;">Statut</th>
                </tr>
                </thead>
                <tbody>
                <?php
    $statusTranslations = [
        "TODO" => "Non commencé",
                "PROGRESS" => "En cours",
                "FINISH" => "Terminé",
                ];

                foreach ($quizUsersData as $data) {
                $quizUser = $data['user'];
                $nbQuestions = $data['nbQuestions'];
                $nbResponses = $data['nbResponses'];
                $status = $quizUser->status;
                $statusText = $statusTranslations[$status] ?? $status;

                // Calcul du pourcentage de progression
                $progress = $nbQuestions > 0 ? round(($nbResponses / $nbQuestions) * 100) : 0;

                // Définir la couleur de la barre en fonction du statut
                $progressClass = "is-danger"; // Rouge par défaut
                if ($status === "PROGRESS") {
                $progressClass = "is-info"; // Bleu
                } elseif ($status === "FINISH") {
                $progressClass = "is-success"; // Vert
                }
                ?>
                <tr>
                    <td><?= $quizUser->userLastName ?></td>
                    <td><?= $quizUser->userFirstName ?></td>
                    <td>
                        <div><?= $quizUser->userEmail ?></div>
                        <?php if($quizUser->auto == 1) { ?>
                            <div style="font-weight: 700">Personne évaluée</div>
                        <?php } ?>
                    </td>
                    <td><?= $quizUser->userIdentifier ?></td>
                    <td><?= $nbResponses ?></td>
                    <td><?= $nbQuestions ?></td>
                    <td>
                        <div class="has-text-centered">
                            <small class="has-text-weight-bold has-text-<?= strtolower($progressClass) ?>"><?= $statusText ?></small>
                            <progress class="progress progress-bordered <?= $progressClass ?>" value="<?= $progress ?>" max="100">
                                <?= $progress ?>%
                            </progress>
                        </div>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>


        </div>
    </div>


<!-- le jquery était pas importé, j'ai donc du rajouter cette ligne -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

</script>


<!-- BOITES DE DIALOGUE -->
<?php if (!empty($msg_erreur)) { ?>
<div id="dialog" title="<?=$msg_erreur['titre']?>" style="display: none;">
    <p><?=$msg_erreur['msg']?></p>
</div>
<?php } ?>



<!-- SCRIPTS -->

<?=Appy\Src\Html::moduleJS("assets/js/table_responsive.js")?>
<?=Appy\Src\Html::scriptJS("assets/js/datatable.js")?>
