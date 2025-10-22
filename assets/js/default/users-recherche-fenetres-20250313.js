$(function () {
    function configurerPopup(idPopup, minWidthRatio, minHeightRatio) {
        var wWidth = $(window).width();
        var dWidth = wWidth * minWidthRatio;
        var wHeight = $(window).height();
        var dHeight = wHeight * minHeightRatio;

        $(idPopup).dialog({
            modal: true,
            autoOpen: false,
            minHeight: dHeight,
            minWidth: dWidth,
            title: "Création d'un nouveau groupe"
        });
    }

    configurerPopup("#fen_popup_group_import", 0.4, 0.5);
    configurerPopup("#fen_popup_group_single", 0.4, 0.5);
    configurerPopup("#fen_popup_group_edit_user", 0.4, 0.5);
    configurerPopup("#fen_popup_group_multiple", 0.4, 0.5);
    configurerPopup("#import_popup", 0.5, 0.5);
    configurerPopup("#fen_popup_user", 0.5, 0.5);
    configurerPopup("#fen_popup_multiple_group_edit", 0.4, 0.5);

    $("#btn-groupe-select-multiple").on('click', function () {
        $("#fen_popup_multiple_group_edit").dialog("option", "title", "Assigner à un groupe");
        $("#fen_popup_multiple_group_edit").dialog("open");
        return false;
    });

    $("#btnCreateGroupImport").on('click', function () {
        $("#NewGroupNameImport").val('');
        $("#fen_popup_group_import").dialog("option", "title", "Création d'un nouveau groupe");
        $("#fen_popup_group_import").dialog("open");
        return false;
    });

    $("#btnCreateGroupSingleUser").on('click', function () {
        $("#NewGroupNameSingle").val('');
        $("#fen_popup_group_single").dialog("option", "title", "Création d'un nouveau groupe");
        $("#fen_popup_group_single").dialog("open");
        return false;
    });

    $("#btnCreateGroupEditUser").on('click', function () {
        $("#NewGroupNameEditUser").val('');
        $("#fen_popup_group_edit_user").dialog("option", "title", "Création d'un nouveau groupe");
        $("#fen_popup_group_edit_user").dialog("open");
        return false;
    });

    $("#btnCreateGroupMultipleUsers").on('click', function () {
        $("#NewGroupNameMultiple").val('');
        $("#fen_popup_group_multiple").dialog("option", "title", "Création d'un nouveau groupe");
        $("#fen_popup_group_multiple").dialog("open");
        return false;
    });

    $(".btn_import").on('click', function () {
        $("#import_popup").dialog("option", "title", "Importer des répondants");
        $("#import_popup").dialog("open");
    });

    $(".btn_popup_user").on('click', function () {
        let userid = $(this).data("id");
        let user_lastname = $(this).data("user_lastname");
        let user_firstname = $(this).data("user_firstname");
        let user_email = $(this).data("user_email");
        let user_groupe = $(this).data("user_groupe");
        let titrePopUp = userid === 'btn_repondant' ? 'Ajout d\'un répondant' : 'Modification d\'un répondant';

        $("#fen_popup_user").dialog("option", "title", titrePopUp);
        $("#userid").val(userid);
        $("#user_lastname").val(user_lastname);
        $("#user_firstname").val(user_firstname);
        $("#user_email").val(user_email);
        $("#user_groupe").val(user_groupe);
        $("#fen_popup_user").dialog("open");
        return false;
    });

    $("#btnCloseGroupImport").on('click', function () {
        $("#fen_popup_group_import").dialog("close");
    });

    $("#btnCloseGroupSingle").on('click', function () {
        $("#fen_popup_group_single").dialog("close");
    });

    $("#btnCloseGroupEditUser").on('click', function () {
        $("#fen_popup_group_edit_user").dialog("close");
    });

    $("#btnCloseEditPopupUser").on('click', function () {
        $("#fen_popup_group_edit_user").dialog("close");
    });

    $("#btnCloseGroupMultiple").on('click', function () {
        $("#fen_popup_group_multiple").dialog("close");
    });

    $("#btn_close_popup").on('click', function () {
        $("#import_popup").dialog("close");
    });

    $("#btnClosePopupUser").on('click', function () {
        $("#fen_popup_user").dialog("close");
    });

    $("#btnCloseMultipleGroupEdit").on('click', function () {
        $("#fen_popup_multiple_group_edit").dialog("close");
    });



    $("#import_popup").on('submit', 'form', function () {
        if ($("#groupe_id_import").val() === "") {
            alert("Veuillez sélectionner un groupe.");
            return false;
        }
    });
});
