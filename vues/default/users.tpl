<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - Utilisateurs';
?>

<?=Appy\Src\Html::css("assets/css/default/products.css")?>
<?=Appy\Src\Html::css("assets/css/table_responsive.css")?>
<?=Appy\Src\Html::css("assets/css/datatable.css")?>

<div class="container" style="font-family: Work Sans">
    <?php include BASE_PATH."vues/flash.tpl"?>

    <div class="level">
        <div class="level-left">
            <h1 class="title is-5" style="font-weight: 600;">Liste des Répondants</h1>
        </div>
        <div class="column is-two-thirds">
            <div class="has-text-right">
                <a style="font-weight: 400;" class="btn_import button-valider" title="Importer des répondants" data-id="btn_import"><i class="fas fa-plus"></i>&nbsp;Importer des répondants</a>
                <a style="font-weight: 400;" class="btn_popup_user button-valider" title="Ajouter un répondant" data-id="btn_repondant"><i class="fas fa-plus"></i>&nbsp;Ajouter un répondant</a>
                <a style="font-weight: 500;" class="btn_add_des_repondants button-valider" title="Ajouter des identifiants" data-id="btn_des_repondants"><i class="fas fa-plus"></i>&nbsp;Ajouter des identifiants</a>
            </div>
        </div>
    </div>

    <div>
        <div id="multiple-button-container" style="display: none;">
            <div style="display: flex;">
                <button id="delete-selected" class="button-valider" title="Supprimer les utilisateurs sélectionnés">
                    <span class="icon">
                        <i class="fas fa-trash icon-white"></i>
                    </span>
                    Supprimer
                </button>
                <button id="btn-groupe-select-multiple" class="button-valider" title="Assigner à un groupe" style="margin-left: 5px">
                    Assigner à un groupe
                </button>
            </div>
        </div>
    </div>

    <div class="columns is-justify-content-flex-end is-fullwidth" style="display: flex; align-items: center;">
        <form action="<?=$url?>" method="POST" style="display: flex;">
            <div class="field" style="flex: 1; margin-right: 10px;">
                <input id="recherche_EmailNomPrenomIdentifiant" class="input" type="text" name="recherche_EmailNomPrenomIdentifiant" placeholder="Rechercher par nom, prénom, email et identifiant" value="<?php
                if (isset($_SESSION['recherche']['EmailNomPrenomIdentifiant'])) {
                    echo $_SESSION['recherche']['EmailNomPrenomIdentifiant'];
                }
            ?>" onkeypress="if(event.key === 'Enter') { this.form.submit(); }">
            </div>
            <div class="field" style="margin-right: 10px;">
                <select id="groupe_id" class="input" name="groupe_id" onchange="this.form.submit();">
                    <option value="">Sélectionner un groupe</option>
                    <?php foreach ($groupes as $groupe): ?>
                    <option value="<?= $groupe->id ?>" <?= (isset($_SESSION['recherche']['groupe']) && $_SESSION['recherche']['groupe'] == $groupe->id) ? 'selected' : '' ?>>
                    <?= $groupe->groupeName ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <div class="field" style="margin-right: 10px;margin-top: -12px">
            <form action="<?=$urlReset?>" method="POST" style="display: inline;">
                <input id="reset" class="input" type="hidden" name="reset" value="1">
                <button type="submit" class="button button-reset-custom" title="Réinitialiser la recherche">
                <span class="icon has-text-white">
                    <i class="fas fa-undo"></i>
                </span>
                </button>
            </form>
        </div>
    </div>

    <table id="users" class="table is-fullwidth table_responsive table-rm">
        <thead>
        <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th style="font-weight: 600;">Nom</th>
            <th style="font-weight: 600;">Prénom</th>
            <th style="font-weight: 600;">Email</th>
            <th style="font-weight: 600;">Identifiant</th>
            <th style="font-weight: 600;">Groupe</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($utilisateurs as $user) { ?>
        <tr style="font-weight: 400;">
            <td><input type="checkbox" class="user-checkbox" data-user-id="<?=$user->id?>"></td>
            <td><p style="font-weight: 300;"><?=$user->lastname?></p></td>
            <td><p><?=$user->firstname?></p></td>
            <td><p><?=$user->email?></p></td>
            <td><p><?=$user->identifier?></p></td>
            <td><p><?= $user->groupe ? $user->groupe->groupeName : '' ?></p></td>
            <td>
                <a class="icon-action-transform btn_edit_user" title="Modifier"
                   data-id="<?=$user->id?>"
                   data-nom="<?=$user->lastname?>"
                   data-prenom="<?=$user->firstname?>"
                   data-email="<?=$user->email?>"
                   data-role="<?=$user->role?>"
                   data-groupe-id="<?=$user->groupId?>">
   <span class="icon">
       <i class="fas fa-edit"></i>
   </span>
                </a>

                <a class="icon-action-transform" onclick="deleteUser(<?=$user->id?>)" title="Supprimer">
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


<div id="fen_popup_user" style="display: none;">
    <?php include_once BASE_PATH."vues/default/UsersSet.tpl"?>
</div>

<div id="edit_user_popup" style="display: none;">
    <?php include_once BASE_PATH."vues/default/EditUser.tpl"?>
</div>

<div id="AddUsersPopUp" style="display: none;">
    <?php include_once BASE_PATH."vues/default/AddUsers.tpl"?>
</div>

<div id="import_popup" style="display: none;">
    <?php include_once BASE_PATH."vues/default/ImportUsers.tpl"?>
</div>

<div id="fen_popup_group_import" style="display: none;">
    <?php include_once BASE_PATH."vues/default/AddGroupUsersImport.tpl" ?>
</div>

<div id="fen_popup_group_single" style="display: none;">
    <?php include_once BASE_PATH."vues/default/AddGroupSingleUser.tpl" ?>
</div>

<div id="fen_popup_group_multiple" style="display: none;">
    <?php include_once BASE_PATH."vues/default/AddGroupMultipleUsers.tpl" ?>
</div>

<div id="fen_popup_group_edit_user" style="display: none;">
    <?php include_once BASE_PATH."vues/default/AddGroupEditUser.tpl" ?>
</div>

<div id="fen_popup_multiple_group_edit" style="display: none;">
    <?php include_once BASE_PATH."vues/default/MultipleGroupEditUser.tpl" ?>
</div>

<!-- BOITES DE DIALOGUE -->
<?php if (!empty($msg_erreur)) { ?>
<div id="dialog" title="<?=$msg_erreur['titre']?>" style="display: none;">
    <p><?=$msg_erreur['msg']?></p>
</div>
<?php } ?>

<script type="text/javascript">
    function submitAddGroupImport() {
        const newGroupName = $("#NewGroupNameImport").val().trim();
        if (newGroupName === "") {
            alert("Le nom du groupe ne peut pas être vide.");
            return;
        }

        var url = '<?=$urlAddGroup?>';

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                'NewGroupName': newGroupName
            },
            success: function(data) {
                var celluleSelectGroupe = "#groupe_id_import";
                $(celluleSelectGroupe).html(data);
                $("#fen_popup_group_import").dialog("close");
                $("#NewGroupNameImport").val('');
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de l'ajout du groupe : ", error);
                alert("Une erreur s'est produite lors de l'ajout du groupe. Veuillez réessayer.");
            }
        });
    }

    function submitAddGroupSingle() {
        const newGroupName = $("#NewGroupNameSingle").val().trim();
        if (newGroupName === "") {
            alert("Le nom du groupe ne peut pas être vide.");
            return;
        }

        var url = '<?=$urlAddGroup?>';

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                'NewGroupName': newGroupName
            },
            success: function(data) {
                var celluleSelectGroupe = "#groupe_id_single";
                $(celluleSelectGroupe).html(data);
                $("#fen_popup_group_single").dialog("close");
                $("#NewGroupNameSingle").val('');
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de l'ajout du groupe : ", error);
                alert("Une erreur s'est produite lors de l'ajout du groupe. Veuillez réessayer.");
            }
        });
    }

    function submitAddGroupEditUser() {
        const newGroupName = $("#NewGroupNameEditUser").val().trim();
        if (newGroupName === "") {
            alert("Le nom du groupe ne peut pas être vide.");
            return;
        }

        var url = '<?=$urlAddGroup?>';

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                'NewGroupName': newGroupName
            },
            success: function(data) {
                var celluleSelectGroupe = "#groupe-edit-id";
                $(celluleSelectGroupe).html(data);
                $("#fen_popup_group_edit_user").dialog("close");
                $("#NewGroupNameEditUser").val('');
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de l'ajout du groupe : ", error);
                alert("Une erreur s'est produite lors de l'ajout du groupe. Veuillez réessayer.");
            }
        });
    }

    function submitAddGroupMultiple() {
        const newGroupName = $("#NewGroupNameMultiple").val().trim();
        if (newGroupName === "") {
            alert("Le nom du groupe ne peut pas être vide.");
            return;
        }

        var url = '<?=$urlAddGroup?>';

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                'NewGroupName': newGroupName
            },
            success: function(data) {
                var celluleSelectGroupe = "#groupe_id_multiple";
                $(celluleSelectGroupe).html(data);
                $("#fen_popup_group_multiple").dialog("close");
                $("#NewGroupNameMultiple").val('');
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de l'ajout du groupe : ", error);
                alert("Une erreur s'est produite lors de l'ajout du groupe. Veuillez réessayer.");
            }
        });
    }

    function deleteUser(userId) {
        if (confirm("Confirmez-vous la suppression ?")) {
            url = "<?=WEB_PATH;?>users.html/delete?userId=" + userId;
            document.location.href = url;
        }
    }
</script>


<!-- SCRIPTS -->
<?=Appy\Src\Html::scriptJS("assets/js/default/users-recherche-fenetres-20250313.js");?>
<?=Appy\Src\Html::scriptJS("assets/js/default/add-des-repondants.js");?>
<?=Appy\Src\Html::moduleJS("assets/js/table_responsive.js")?>
<?=Appy\Src\Html::scriptJS("assets/js/datatable.js")?>
<?=Appy\Src\Html::scriptJS("assets/js/default/user-selection-20250317.js");?>
<?=Appy\Src\Html::scriptJS("assets/js/default/edit-user.js");?>


