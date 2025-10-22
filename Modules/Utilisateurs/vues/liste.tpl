<?php
$vue         = 'liste';
$this->titre = Appy\Src\Config::APPLI_NOM.' - Utilisateurs';
$this->css   = array(DIR_MODULE.'/css/defaut', 'table_responsive');

// Session si besoin
$session = Appy\Src\Core\Session::getInstance();

$utilisateur  = \Appy\Src\Core\Session::getInstance()->read('utilisateur');

?>
<style>
    #btn-is-hovered:hover
    {
        background-color: #ffcd00!important;
    }
</style>

<div class="container">
    <?php include BASE_PATH."Modules/Membres/vues/flash.tpl"?>
    <div class="level">
        <div class="level-left">
            <h1 class="title is-5" style="font-weight: 600;">Liste des utilisateurs Relais-Managers</h1>
        </div>


        <div class="column is-one-thirds">
            <div class="has-text-right">
                <a class="button-valider" href="<?=$url_controleur?>?add_ligne" title="Ajouter un utilisateur"><i class="fas fa-user-plus"></i>&nbsp;Ajouter un utilisateur</a>
            </div>
        </div>
    </div>



    <form action="<?=$url_controleur?>" method="POST">
        <table id="liste_applications" class="table is-fullwidth table_responsive table-rm">
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date de création</th>
                    <th>Dernière connexion</th>
                    <th>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($liste as $user) {?>
                    <tr>
                        <td>
                            <p><?=$user->prenom?></p>
                        </td>
                        <td>
                            <p><?=$user->nom?></p>
                        </td>
                        <td>
                            <p><?=$user->email?></p>
                        </td>
                        <td>
                            <p><?=$user->getRoleLabel()?>
                            </p>
                        </td>
                        <td>
                            <p><?=\Appy\Src\Html::dateTimeFrWithoutSecond($user->createdAt)?></p>
                        </td>
                        <td>
                            <p><?=\Appy\Src\Html::dateTimeFrWithoutSecond($user->last_connection_at)?></p>
                        </td>
                        <td>
                            <?php if (isset($utilisateur->role) && ($utilisateur->role == 1 || $utilisateur->role == 2)) { ?>
                                <?php if (!$user->isAdmin() || ($user->isAdmin() && $utilisateur->role == 1)) { ?>
                                    <a class="icon-action-transform" href="<?=$url_controleur?>?edit_ligne=<?=$user->id?>" title="Modifier">
                                        <span class="icon">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    </a>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </form>
</div>

<!-- BOITES DE DIALOGUE -->
<?php if (!empty($msg_erreur)) {?>
    <div id="dialog" title="<?=$msg_erreur['titre']?>">
        <p>
            <?=$msg_erreur['msg']?>
        </p>
    </div>
<?php }?>

<!-- SCRIPTS -->
<?=\Appy\Src\Html::moduleJS("Modules/Utilisateurs/js/liste.js")?>

