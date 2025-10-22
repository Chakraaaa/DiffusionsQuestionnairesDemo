<?php
$vue         = 'new';
$this->titre = Appy\Src\Config::APPLI_NOM.' - Utilisateurs';
$this->css   = array(DIR_MODULE.'/css/defaut');

$utilisateur  = \Appy\Src\Core\Session::getInstance()->read('utilisateur');

?>

<div class="container" id="utilisateur">

    <div class="level">

        <div class="level-left">
            <h1 class="title is-5">Création d'un nouvel utilisateur</h1>
        </div>

        <div class="level-right">

        </div>

    </div>

    <form action="<?=$url_controleur?>" method="POST">

        <div class="columns is-multiline mt-6">

            <div class="column is-three-quarters">

                <div class="field is-horizontal">
                    <div class="field-body">
                        <div class="field">
                            <p class="control is-expanded">                                
                                <input class="input" type="text" placeholder="Prénom" name="prenom" required="true" autocomplete="on">
                            </p>
                        </div>
                        <div class="field">
                            <p class="control is-expanded">
                                <input class="input is-success" type="text" placeholder="Nom" name="nom" required="true" autocomplete="on">
                            </p>
                        </div>
                        <div class="field-label is-normal">
                            <label class="label">Rôle</label>
                        </div>
                            <div class="field is-narrow">
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select class="input" name="role" required="true">
                                            <option value="">--Sélectionner un rôle--</option>
                                            <?php if (isset($utilisateur->role) && $utilisateur->role == 1) { ?>
                                                <option value="1">Administrateur</option>
                                            <?php }?>
                                            <option value="2">Gestionnaire</option>
                                            <option value="3">Consultant</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>

            </div>


            <div class="column is-two-fifths">

                <div class="field">
                    <label class="label">Adresse Email de connexion</label>
                </div>

                <div class="field is-horizontal">

                    <div class="field-body">
                        <!--<input class="input is-success" type="hidden" placeholder="Identifiant" name="username">-->
                        <div class="field">
                            <p class="control is-expanded has-icons-left">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input class="input is-success" type="email" placeholder="Email" name="email" required="true" autocomplete="on">
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <div class="has-text-right">
            <input type="hidden" name="ligne_editee" value="<?=$ligne_editee->id?>" >
            <button class="button-valider" name="new_utilisateur" title="enregistrer les modifications"><i class="fas fa-save"></i>&nbsp;Valider</button>
        </div>

    </form> 

</div>
<!-- BOITES DE DIALOGUE -->
<?php if (!empty($msg_erreur)) {?>
    <div id="dialog" title="<?=$msg_erreur['titre']?>">
        <p class="notification is-warning">
            <?php foreach ($msg_erreur['msg'] as $ligne) {?>
                <?=$ligne?><br/>
            <?php }?>
        </p>
    </div>
<?php }?>

<!-- SCRIPTS -->
<?=\Appy\Src\Html::moduleJS("Modules/".DIR_MODULE."/js/edition.js")?>

