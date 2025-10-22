<?php
$this->titre = Appy\Src\Config::APPLI_NOM . ' - Groupes';
?>

<?= Appy\Src\Html::css("assets/css/default/products.css") ?>
<?= Appy\Src\Html::css("assets/css/table_responsive.css") ?>
<?= Appy\Src\Html::css("assets/css/datatable.css") ?>
<div class="container">
    <?php include BASE_PATH . "vues/flash.tpl" ?>

    <div class="level">
        <div class="level-left">
            <h1 class="title is-5">Administration des groupes</h1>
        </div>
        <div class="column is-two-thirds">
            <div class="has-text-right">
                <a class="btn_popup_group button-valider" title="Ajouter un groupe" data-id="btn_group"><i class="fas fa-plus"></i>&nbsp;Ajouter un groupe</a>
            </div>
        </div>
    </div>

    <table id="groups" class="table is-fullwidth table_responsive table-rm">
        <thead>
        <tr>
            <th>Nom</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($groupes as $groupe) { ?>
        <tr>
            <td>
                <p><?= $groupe->groupeName ?></p>
            </td>
            <td>
                <a class="btn_popup_edit_group icon-action-transform" title="Modifier" data-id="<?= $groupe->id ?>" data-nom="<?= $groupe->groupeName ?>">
                            <span class="icon">
                                <i class="fas fa-edit"></i>
                            </span>
                </a>
                <a class="icon-action-transform" onclick="deleteGroupe(<?= $groupe->id ?>)" title="Supprimer">
                    <span class="icon">
                        <i class="fas fa-trash"></i>
                    </span>
                </a>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function deleteGroupe(groupId) {
        if (confirm("Confirmez-vous la suppression ?")) {
            url = "<?= WEB_PATH; ?>groups.html/delete?groupId=" + groupId;
            document.location.href = url;
        }
    }
</script>

<div id="fen_popup_group" style="display: none;">
    <?php include_once BASE_PATH . "vues/default/GroupesSet.tpl" ?>
</div>

<div id="edit_group_popup" style="display: none;">
    <?php include_once BASE_PATH."vues/default/EditGroup.tpl" ?>
</div>



<!-- BOITES DE DIALOGUE -->
<?php if (!empty($msg_erreur)) { ?>
<div id="dialog" title="<?= $msg_erreur['titre'] ?>" style="display: none;">
    <p><?= $msg_erreur['msg'] ?></p>
</div>
<?php } ?>

<!-- SCRIPTS -->
<?= Appy\Src\Html::moduleJS("assets/js/default/groups-recherche-fenetres.js") ?>
<?= Appy\Src\Html::moduleJS("assets/js/table_responsive.js") ?>
<?= Appy\Src\Html::scriptJS("assets/js/datatable.js") ?>
<?= Appy\Src\Html::scriptJS("assets/js/default/edit-group.js") ?>
