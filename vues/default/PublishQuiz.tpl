<?php
$this->titre = Appy\Src\Config::APPLI_NOM . ' - PublishQuiz';
if (isset($_GET['quizId']) && is_numeric($_GET['quizId'])) {
$quizId = intval($_GET['quizId']);
} else {
echo "<p style='color: red;'>Aucun ID de quiz spécifié.</p>";
exit;
}
$urlQuiz = WEB_PATH . "quiz.html/QuizId=" . $quizId;

?>

<?= Appy\Src\Html::css("assets/css/default/products.css") ?>
<?= Appy\Src\Html::css("assets/css/table_responsive.css") ?>
<?= Appy\Src\Html::css("assets/css/datatable.css") ?>
<?=Appy\Src\Html::scriptJS("assets/js/tinymce/tinymce.min.js")?>

<div class="container" style="margin: 0;">
    <?php include BASE_PATH . "vues/flash.tpl" ?>

    <!-- Structure en colonnes -->
    <div class="columns">
        <!-- Sidebar gauche -->
        <div class="column is-2" style="padding: 0; margin: 0;">
            <div class="box" style="height: 100%">
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

        <!-- Formulaire principal -->
        <div class="column is-10 ml-5">
            <div class="form-container">
                <h1 class="title is-5">Publication du Questionnaire</h1>

                <?php if($quiz->isTypePRCC()) { ?>
                    <?php if($quiz->autoUserId) { ?>
                        <h2 class="title is-6">Email d'invitation à destination de <?= $quiz->autoUserFirstName ?> <?= $quiz->autoUserLastName ?> (<?= $quiz->autoUserEmail ?>)</h2>
                    <?php } else { ?>
                        <h2 class="title is-6">Email d'invitation à destination du groupe <?= $group->groupeName ?></h2>
                    <?php } ?>
                <?php } ?>

                <form id="form-submit-quiz" method="POST" action="<?= $urlCreate ?>">
                    <input type="hidden" name="idQuiz" value="<?= htmlspecialchars($quiz->id) ?>">
                    <input type="hidden" name="template-message-id" id="template-message-id" value="">

                    <!-- Objet Section -->
                    <div class="field">
                        <label class="label">Objet du mail</label>
                        <div>
                            <input class="input" type="text" name="objet" id="objet" required>
                        </div>
                    </div>

                    <?php if(!$quiz->isTypePRCC()) { ?>

                        <!-- Groupe Section -->
                        <div class="field">
                            <label class="label">Groupe</label>
                            <div class="columns">
                                <div class="column is-two-thirds">
                                    <div class="control">
                                        <div class="select">
                                            <select id="groupe" name="groupe" required>
                                                <option value="">Sélectionner un groupe</option>
                                                <?php foreach ($groupes as $groupe): ?>
                                                <option value="<?= htmlspecialchars($groupe->id) ?>"><?= htmlspecialchars($groupe->groupeName) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-one-third">
                                    <div id="group-info">
                                        <!-- Résultats seront affichés ici après la requête AJAX -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>


                    <div class="field">
                        <label class="label">Sélectionner un modèle d'email :</label>
                        <div class="control">
                            <div class="select">
                                <select id="email-template-select">
                                    <?php foreach ($templates as $template): ?>
                                    <option value="<?= $template->id ?>" data-deletable="<?= $template->deleteable ?>">
                                        <?= htmlspecialchars($template->title) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">Message</label>
                                <div class="control">
                                    <textarea id="message-input" class="textarea" name="message" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="column is-two-fifths mt-5">
                            <div class="field">
                                <label style="margin-bottom: 0" class="label has-text-weight-bold">Variables disponibles pour personnaliser l'email :</label>
                                <div style="margin-bottom: 10px">Cliquez sur le bouton, puis coller dans l'email (CTRL+V)</div>
                                <div class="control">
                                    <ul style="font-size: 15px;list-style:none">
                                        <li style="margin-bottom: 5px"><button type="button" id="btn-identifiant" class="copyButton btn-identifiant">[IDENTIFIANT]</button> : Identifiant de l'utilisateur</li>
                                        <li style="margin-bottom: 5px"><button type="button" id="btn-nom" class="copyButton btn-nom">[NOM]</button> : Nom de l'utilisateur</li>
                                        <li style="margin-bottom: 5px"><button type="button" id="btn-prenom" class="copyButton btn-prenom">[PRENOM]</button> : Prénom de l'utilisateur</li>
                                        <li style="margin-bottom: 5px"><button type="button" id="btn-url" class="copyButton btn-url">[URL]</button> : Lien d'accès au questionnaire</li>
                                        <li style="margin-bottom: 5px"><button type="button" id="btn-titre" class="copyButton btn-titre">[TITRE]</button> : Titre du questionnaire</li>
                                        <li style="margin-bottom: 5px"><button type="button" id="btn-date-debut" class="copyButton btn-date-debut">[DATE_DEBUT]</button> : Date de début du questionnaire</li>
                                        <li style="margin-bottom: 5px"><button type="button" id="btn-date-fin" class="copyButton btn-date-fin">[DATE_FIN]</button> : Date de fin du questionnaire</li>
                                    </ul>
                                    <input type="hidden" id="copyInput">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="buttons mt-3">
                        <a href="#" id="btn-delete-template" class="button-fermer">Supprimer le modèle</a>
                        <button type="button" id="open-save-template-popup" class="button-valider ml-4">Créer un nouveau modèle</button>
                        <button id="btn-submit-publish-and-save" type="button" name="btnSendEmail" class="button-valider ml-4">Enregistrer ce modèle et envoyer</button>
                        <button id="btn-submit-publish" type="submit" name="btnSendEmail" class="button-valider ml-4">Envoyer</button>
                    </div>

                </form>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#groupe').change(function() {
            const groupeId = $(this).val();
            const quizId = <?=$quiz->id?>;
            if (groupeId) {
                fetchGroupInfo(groupeId, quizId);
            } else {
                $('#group-info').empty();
            }
        });

        function fetchGroupInfo(groupeId, quizId) {
            $.ajax({
                url: '<?=$urlFetchGroupInfo?>',
                type: 'GET',
                data: { 'groupeId': groupeId, 'quizId': quizId },
                success: function(data) {
                    $('#group-info').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Erreur lors de la récupération des informations du groupe : ", error);
                    alert("Une erreur s'est produite lors de la récupération des informations du groupe.");
                }
            });
        }


            $("#btn-submit-publish-and-save").click(function(){

                const objetMail = $('#objet').val();
                if(objetMail === '') {
                    alert('Vous devez renseigner l\'objet de l\'email');
                    return false;
                } else {
                    const templateMsgId = $('#email-template-select').val();
                    $('#template-message-id').val(templateMsgId);
                    $('#form-submit-quiz').attr('action', '<?= $urlCreate ?>?saveEmail=1');
                    $("#form-submit-quiz").submit(); //
                }
            });


        $('#email-template-select').change(function() {
            const selectedOption = $(this).find('option:selected');
            const templateId = selectedOption.val();
            const quizId = <?=$quiz->id?>;
            const isDeletable = selectedOption.data('deletable');

            if (isDeletable === 0) {
                $('#btn-delete-template').off('click').on('click', function(e) {
                    e.preventDefault();
                    alert("Impossible de supprimer le modèle de base.");
                }).removeAttr('href');
            } else {
                const deleteUrl = "<?=$urlDeleteTemplateEmail?>?quizId=" + quizId + "&templateId=" + templateId;
                $('#btn-delete-template').attr('href', deleteUrl).off('click');
            }

            loadEmailTemplateMessage(templateId);
        });

        loadEmailTemplateMessage($('#email-template-select').val());

        function loadEmailTemplateMessage(templateId) {
            $.ajax({
                url: '<?=$urlTemplatesEmails?>',
                type: 'GET',
                data: { templateId: templateId },
                success: function(response) {
                    if (tinymce.get('message-input')) {
                        tinymce.get('message-input').setContent(response);
                    } else {
                        console.log('TinyMCE n\'est pas initialisé');
                        $('#message-input').val(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erreur lors de la récupération du modèle d'email : ", error);
                    alert("Impossible de charger le modèle d'email.");
                }
            });
        }
        tinymce.init({
            selector: '#message-input',
            height: 500,
            plugins: 'link image code contextmenu lists',
            language_url: "https://questionnaire.relaismanagers.fr/assets/js/tinymce/fr_FR.js",
            language : "fr_FR",
            contextmenu: "paste | link image inserttable | cell row column deletetable",
            menubar: 'edit insert format',
            toolbar: "undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist",
            license_key: 'gpl',
            setup: function(editor) {
                editor.on('init', function() {
                    $('#message-input').removeAttr('required');
                    loadEmailTemplateMessage($('#email-template-select').val());
                });
            }
        });

        $('form').on('submit', function(event) {
            var messageHTML = tinymce.get('message-input').getContent();
            console.log(messageHTML)
            $('#hidden-message').val(messageHTML);
            $('#btn-submit-publish').css('pointer-events', 'none');
            $('#btn-submit-publish').css('opacity', '0.5');
            $('#btn-submit-publish').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>&emsp;EN COURS');
        });

        $(".copyButton").click(function() {
            const copyBuffer = $(this).attr("id");
            const nameBtn = '#' + copyBuffer;
            const text = $(nameBtn).html();
            navigator.clipboard.writeText(text);
        });
    });
</script>




<div id="save-template-email-popup" style="display: none;">
    <?php include_once BASE_PATH."vues/default/SaveTemplateEmail.tpl"?>
</div>

<?=Appy\Src\Html::scriptJS("assets/js/default/pop-up-save-template-email.js");?>


