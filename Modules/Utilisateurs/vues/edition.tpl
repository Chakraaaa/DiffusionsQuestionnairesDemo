<?php
$vue         = 'edition';
$this->titre = Appy\Src\Config::APPLI_NOM.' - Utilisateurs';
$this->css   = array(DIR_MODULE.'/css/defaut');

$utilisateur  = \Appy\Src\Core\Session::getInstance()->read('utilisateur');

?>

<div class="container" id="utilisateur">
    <?php include BASE_PATH."Modules/Membres/vues/flash.tpl"?>
    <div class="level">

        <div class="level-left">
            <h1 class="title is-5">Edition de l'utilisateur</h1>
        </div>

        <div class="level-right">

            <form action="<?=$url_controleur?>" method="POST">
                <button class="button-valider dialog-button tooltip level-item" name="del_ligne" value="<?=$ligne_editee->id?>" title="Supprimer l'utilisateur" data-dsi-msg="Confirmer la suppression">
                        <i class="fas fa-trash"></i>&nbsp;Supprimer
                </button>
            </form>

        </div>

    </div>

    <div>
        <?php include BASE_PATH."Modules/Membres/vues/flash.tpl"?>
    </div>

    <form action="<?=$url_controleur?>" method="POST">

        <div class="tabs">
            <ul>
                <li v-on:click="activetab=1" v-bind:class="[ activetab === 1 ? 'is-active' : '' ]"><a>Informations</a></li>
                <li style="display: none" v-on:click="activetab=2" v-bind:class="[ activetab === 3 ? 'is-active' : '' ]"><a>Mot de passe</a></li>
            </ul>
        </div>

        <div v-show="activetab===1" style="display: none;">
            <?php require "edition_vues".DS."informations_saisie.tpl";?>
        </div>

        <div v-show="activetab===2" style="display: none;">
            <h1 class="title is-6">Ré-initialisation du mot de passe</h1>
            <p class="tag">En développement...</p>
        </div>

    </form> 

</div>

<!-- SCRIPTS -->
<?=\Appy\Src\Html::scriptJS("assets/js/vue.min.js")?>
<?=\Appy\Src\Html::scriptJS("Modules/".DIR_MODULE."/js/vue.js")?>
<?=\Appy\Src\Html::moduleJS("Modules/".DIR_MODULE."/js/edition.js")?>

