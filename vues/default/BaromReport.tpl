<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - BaromReport';
?>

<?=Appy\Src\Html::css("assets/css/table_responsive.css")?>
<?=Appy\Src\Html::css("assets/css/datatable.css")?>

<style>
    input[type=number] {
        width: 60px;
    }
    select {
        height: 25px!important;
        font-size: 14px!important;
        padding-top: 1px!important;
        padding-bottom: 1px!important;
    }
    select option {
        color: green;
    }

    select option[value=""] {
        color: red;
    }

    select:invalid {
        color: red;
    }
</style>

<div class="container">
    <?php include BASE_PATH."vues/flash.tpl" ?>
</div>

<div class="columns">
    <div class="column is-2" style="background-color: white">
        <div class="side-menu">
            <div class="buttons-container">
                <a style="pointer-events: none;opacity: 0.5" href="#" class="button-with-icon" title="Édition">
                    <span class="icon"><i class="fas fa-edit"></i></span><span class="button-text">Édition</span>
                </a>
                <hr class="divider">
                <a href="<?=WEB_PATH?>quiz.html/quizOptions?quizId=<?=$quiz->id?>" class="button-with-icon" title="Options">
                    <span class="icon"><i class="fas fa-cogs" aria-hidden="true"></i></span><span class="button-text">Options</span>
                </a>
                <hr class="divider">
                <a href="<?=WEB_PATH?>quiz.html/PublishQuiz?quizId=<?=$quiz->id?>" class="button-with-icon" title="Publication">
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
        <div class="columns is-mobile is-justify-content-flex-end">
            <div class="column has-text-right">
                <a id="btn-generate-data" class="btn-download-result-csv button-valider" title="Télécharger les résultats bruts" href="<?=WEB_PATH?>report.html/data<?=$quiz->type?>?quizId=<?=$quiz->id?>"  onclick="buttonAppearance('#btn-generate-data','far fa-file-excel','Télécharger les résultats bruts (env 30 secondes)','25000')"><i class="far fa-file-excel"></i>&nbsp;Télécharger les résultats bruts (env 25 secondes)</a>
                <a id="btn-generate-word" style="display: none"  class="button-valider" title="Générer" href="<?=WEB_PATH?>report.html/generateWord?quizId=<?=$quiz->id?>" onclick="buttonAppearance('#btn-generate-word','fas fa-file-word','Générer tous les graphiques (env 30 secondes)','30000')"><i class="fas fa-file-word"></i>&nbsp;Générer Word</a>
                <a id="btn-generate-ppt"  id="btn-generate-ppt" class="button-valider" title="Générer le rapport" href="<?=WEB_PATH?>report.html/generateReport<?=$quiz->type?>?quizId=<?=$quiz->id?>" onclick="buttonAppearance('#btn-generate-report','fas fa-file-word','Générer le rapport)','1000')"><i class="fas fa-file-word"></i>&nbsp;Générer le rapport</a>
            </div>
        </div>
        <div>
            <h1 class="title is-5 left-aligned">Résultats du Baromètre</h1>
        </div>
        <hr class="divider">

        <form action="<?=$urlSubmitBaromReport?>" method="POST">
            <input type="hidden" name="quizId" value="<?=$quiz->id?>">

            <div class="columns">
                <div class="column is-4">

                    <!-- Table des coefficients -->
                    <table id="coef" class="table is-fullwidth table_responsive">
                        <thead>
                        <tr>
                            <th>Coefficients (extract brut)</th>
                            <th>Coefficients corrigés</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Efficient</td>
                            <td>Tout à fait vrai</td>
                            <td><input type="number" class="input" name="coef_efficient_tafv" value="<?= $quiz->coefTafv  ?>"></td>
                        </tr>
                        <tr>
                            <td>Peu dégradé</td>
                            <td>Plutôt vrai</td>
                            <td><input type="number" class="input" name="coef_peu_degrade_pv" value="<?= $quiz->coefPv ?>"></td>
                        </tr>
                        <tr>
                            <td>Dégradé</td>
                            <td>Plutôt pas vrai</td>
                            <td><input type="number" class="input" name="coef_degrade_ppv" value="<?= $quiz->coefPpv ?>"></td>
                        </tr>
                        <tr>
                            <td>Fort Dégradé</td>
                            <td>Pas du tout vrai</td>
                            <td><input type="number" class="input" name="coef_fort_degrade_pdtv" value="<?= $quiz->coefPdtv ?>"></td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- Table des risques -->
                    <table id="risques" class="table is-fullwidth table_responsive">
                        <thead>
                        <tr>
                            <th>Risques avec coefficients</th>
                            <th>De</th>
                            <th>A</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Sans risque</td>
                            <td><input type="number" name="risques_sr_de" value="<?= $quiz->risqueDeSr ?>" class="input"></td>
                            <td><input type="number" name="risques_sr_a" value="<?= $quiz->risqueASr ?>" class="input"></td>
                        </tr>
                        <tr>
                            <td>Peu de risques</td>
                            <td><input type="number" name="risques_pdr_de" value="<?= $quiz->risqueDePdr ?>" class="input"></td>
                            <td><input type="number" name="risques_pdr_a" value="<?= $quiz->risqueAPdr ?>" class="input"></td>
                        </tr>
                        <tr>
                            <td>Risques</td>
                            <td><input type="number" name="risques_r_de" value="<?= $quiz->risqueDeR ?>" class="input"></td>
                            <td><input type="number" name="risques_r_a" value="<?= $quiz->risqueAR ?>" class="input"></td>
                        </tr>
                        <tr>
                            <td>Forts risques</td>
                            <td><input type="number" name="risques_fr_de" value="<?= $quiz->risqueDeFr ?>" class="input"></td>
                            <td><input type="number" name="risques_fr_a" value="<?= $quiz->risqueAFr ?>" class="input"></td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- Table des taux -->
                    <table id="taux" class="table is-fullwidth table_responsive">
                        <thead>
                        <tr>
                            <th>Taux d'exposition</th>
                            <th>De</th>
                            <th>A</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Sans risque</td>
                            <td><input type="number" name="taux_exposition_sr_de" value="<?= $quiz->tauxDeSr ?>" class="input"></td>
                            <td><input type="number" name="taux_exposition_sr_a" value="<?= $quiz->tauxASr ?>" class="input"></td>
                        </tr>
                        <tr>
                            <td>Peu de risques</td>
                            <td><input type="number" name="taux_exposition_pdr_de" value="<?= $quiz->tauxDePdr ?>" class="input"></td>
                            <td><input type="number" name="taux_exposition_pdr_a" value="<?= $quiz->tauxAPdr ?>" class="input"></td>
                        </tr>
                        <tr>
                            <td>Risques</td>
                            <td><input type="number" name="taux_exposition_r_de" value="<?= $quiz->tauxDeR ?>" class="input"></td>
                            <td><input type="number" name="taux_exposition_r_a" value="<?= $quiz->tauxAR ?>" class="input"></td>
                        </tr>
                        <tr>
                            <td>Forts risques</td>
                            <td><input type="number" name="taux_exposition_fr_de" value="<?= $quiz->tauxDeFr ?>" class="input"></td>
                            <td><input type="number" name="taux_exposition_fr_a" value="<?= $quiz->tauxAFr ?>" class="input"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="column is-4">
                    <!-- Table des rapports -->
                    <table id="coef" class="table is-fullwidth table_responsive">
                        <thead>
                        <tr>
                            <th>Graphiques Barres</th>
                            <th style="50px">Question</th>
                            <th style="width: 150px">Séléctionner OUI pour inclure dans le rapport</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $chapterId = 1;
                            foreach ($ChaptersInfo as $Chapter) {
                        ?>
                            <tr>
                                <td><?= $Chapter['label'] ?></td>
                                <td>Q1</td>
                                <td>
                                    <select name="C<?= $chapterId ?>Q1" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q1'}) { ?>style="background-color:#696252;color:white"<?php } ?>>
                                        <option value=""></option>
                                        <option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q1'}) { ?>selected<?php } ?>>OUI</option>
                                    </select>
                                </td>
                            </tr>
                            <tr><td></td><td>Q2</td><td><select name="C<?= $chapterId ?>Q2" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q2'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q2'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <tr><td></td><td>Q3</td><td><select name="C<?= $chapterId ?>Q3" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q3'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q3'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <tr><td></td><td>Q4</td><td><select name="C<?= $chapterId ?>Q4" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q4'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q4'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <tr><td></td><td>Q5</td><td><select name="C<?= $chapterId ?>Q5" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q5'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q5'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <tr><td></td><td>Q6</td><td><select name="C<?= $chapterId ?>Q6" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q6'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q6'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <tr><td></td><td>Q7</td><td><select name="C<?= $chapterId ?>Q7" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q7'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q7'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <tr><td></td><td>Q8</td><td><select name="C<?= $chapterId ?>Q8" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q8'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q8'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <tr><td></td><td>Q9</td><td><select name="C<?= $chapterId ?>Q9" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q9'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q9'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <tr><td></td><td>Q10</td><td><select name="C<?= $chapterId ?>Q10" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q10'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'Q10'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                        <?php
                            $chapterId = $chapterId + 1;
                        } ?>
                        </tbody>
                    </table>

                </div>
                <div class="column is-4">

                    <table id="coef" class="table is-fullwidth table_responsive">
                        <thead>
                        <tr>
                            <th>Croisement état facteurs (%)</th>
                            <th style="50px">Critère</th>
                            <th style="width: 150px">Séléctionner OUI pour inclure dans le rapport</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $chapterId = 1;
                            foreach ($ChaptersInfo as $Chapter) {
                        ?>
                            <tr>
                                <td><?= $Chapter['label'] ?></td>
                                <td><?= $quizCriteresBarometre[1]->titre ?></td>
                                <td>
                                    <select name="C<?= $chapterId ?>C1Coef" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'C1Coef'}) { ?>style="background-color:#696252;color:white"<?php } ?>>
                                    <option value=""></option>
                                    <option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'C1Coef'}) { ?>selected<?php } ?>>OUI</option>
                                    </select>
                                </td>
                            </tr>
                            <?php if (array_key_exists('2', $quizCriteresBarometre)) { ?>
                                <tr><td></td><td><?= $quizCriteresBarometre[2]->titre ?></td><td><select name="C<?= $chapterId ?>C2Coef" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'C2Coef'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'C2Coef'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <?php } ?>
                            <?php if (array_key_exists('3', $quizCriteresBarometre)) { ?>
                                <tr><td></td><td><?= $quizCriteresBarometre[3]->titre ?></td><td><select name="C<?= $chapterId ?>C3Coef" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'C3Coef'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'C3Coef'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <?php } ?>
                            <?php if (array_key_exists('4', $quizCriteresBarometre)) { ?>
                                <tr><td></td><td><?= $quizCriteresBarometre[4]->titre ?></td><td><select name="C<?= $chapterId ?>C4Coef" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'C4Coef'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'C4Coef'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                            <?php } ?>
                        <?php
                            $chapterId = $chapterId + 1;
                        } ?>
                        </tbody>
                    </table>

                    <table id="coef" class="table is-fullwidth table_responsive">
                        <thead>
                        <tr>
                            <th>Croisement Exposition (%)</th>
                            <th style="50px"></th>
                            <th style="width: 150px">Séléctionner OUI pour inclure dans le rapport</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td></td>
                            <td><?= $quizCriteresBarometre[1]->titre ?></td>
                            <td>
                                <select name="C1Expo" class="input select-color" <?php if ($quizReportBarometre->C1Expo) { ?>style="background-color:#696252;color:white"<?php } ?>>
                                <option value=""></option>
                                <option value="OUI" <?php if ($quizReportBarometre->C1Expo) { ?>selected<?php } ?>>OUI</option>
                                </select>
                            </td>
                        </tr>
                        <?php if (array_key_exists('2', $quizCriteresBarometre)) { ?>
                            <tr><td></td><td><?= $quizCriteresBarometre[2]->titre ?></td><td><select name="C2Expo" class="input select-color" <?php if ($quizReportBarometre->C2Expo) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->C2Expo) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                        <?php } ?>
                        <?php if (array_key_exists('3', $quizCriteresBarometre)) { ?>
                            <tr><td></td><td><?= $quizCriteresBarometre[3]->titre ?></td><td><select name="C3Expo" class="input select-color" <?php if ($quizReportBarometre->C3Expo) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->C3Expo) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                        <?php } ?>
                        <?php if (array_key_exists('4', $quizCriteresBarometre)) { ?>
                            <tr><td></td><td><?= $quizCriteresBarometre[4]->titre ?></td><td><select name="C4Expo" class="input select-color" <?php if ($quizReportBarometre->C4Expo) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->C4Expo) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                        <?php } ?>
                        </tbody>
                    </table>

                    <table id="coef" class="table is-fullwidth table_responsive">
                        <thead>
                        <tr>
                            <th>Croisement impact facteurs (%)</th>
                            <th style="50px">Critère</th>
                            <th style="width: 150px">Séléctionner OUI pour inclure dans le rapport</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $chapterId = 1;
                            foreach ($ChaptersInfo as $Chapter) {
                        ?>
                        <tr>
                            <td><?= $Chapter['label'] ?></td>
                            <td><?= $quizCriteresBarometre[1]->titre ?></td>
                            <td>
                                <select name="C<?= $chapterId ?>C1Risque" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'C1Risque'}) { ?>style="background-color:#696252;color:white"<?php } ?>>
                                <option value=""></option>
                                <option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'C1Risque'}) { ?>selected<?php } ?>>OUI</option>
                                </select>
                            </td>
                        </tr>
                        <?php if (array_key_exists('2', $quizCriteresBarometre)) { ?>
                            <tr><td></td><td><?= $quizCriteresBarometre[2]->titre ?></td><td><select name="C<?= $chapterId ?>C2Risque" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'C2Risque'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'C2Risque'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                        <?php } ?>
                        <?php if (array_key_exists('3', $quizCriteresBarometre)) { ?>
                            <tr><td></td><td><?= $quizCriteresBarometre[3]->titre ?></td><td><select name="C<?= $chapterId ?>C3Risque" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'C3Risque'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'C3Risque'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                        <?php } ?>
                        <?php if (array_key_exists('4', $quizCriteresBarometre)) { ?>
                            <tr><td></td><td><?= $quizCriteresBarometre[4]->titre ?></td><td><select name="C<?= $chapterId ?>C4Risque" class="input select-color" <?php if ($quizReportBarometre->{'C'.$chapterId.'C4Risque'}) { ?>style="background-color:#696252;color:white"<?php } ?>><option value=""></option><option value="OUI" <?php if ($quizReportBarometre->{'C'.$chapterId.'C4Risque'}) { ?>selected<?php } ?>>OUI</option></select></td></tr>
                        <?php } ?>
                        <?php
                            $chapterId = $chapterId + 1;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="field has-text-right">
                <button class="button-valider" type="submit">Sauvegarder les modifications</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function buttonAppearance($btnId, $iconName, $text, $millisecond)
    {
        $($btnId).css('pointer-events', 'none');
        $($btnId).css('opacity', '0.5');
        $($btnId).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>&emsp;EN COURS');

        setTimeout(function () {
            $html = "<i class=\"" + $iconName + "\"></i>&emsp;" + $text;
            $($btnId).css('pointer-events', 'auto');
            $($btnId).css('opacity', '1');
            $($btnId).html($html);
        }, $millisecond);
    }
    $(document).ready(function() {
        $('.select-color').change(function() {
            var selectedValue = $(this).val();
            if(selectedValue === "OUI") {
                $(this).css('background-color', '#696252');
                $(this).css('color', 'white');
            } else {
                $(this).css('background-color', 'white');
                $(this).css('color', 'black');
            }
        });
    });

</script>

<?=Appy\Src\Html::moduleJS("assets/js/table_responsive.js")?>
<?=Appy\Src\Html::scriptJS("assets/js/datatable.js")?>
