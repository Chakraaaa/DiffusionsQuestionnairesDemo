$(function () {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.5;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.5;

    $("#edit_user_popup").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });

    $(".btn_edit_user").on('click', function () {
        let titrePopUp = 'Modification d\'un utilisateur';
        $("#edit_user_popup").dialog({
            title: titrePopUp
        });

        let userId = $(this).data('id');
        let userNom = $(this).data('nom') || '';
        let userPrenom = $(this).data('prenom') || '';
        let userEmail = $(this).data('email') || '';
        let userGroupId = $(this).data('groupe-id') || '';
        console.log(userId, userNom, userPrenom, userEmail, userGroupId);
        $("#user-edit-id").val(userId);
        $("#user-edit-lastname").val(userNom);
        $("#user-edit-firstname").val(userPrenom);
        $("#user-edit-email").val(userEmail);
        if (userGroupId) {
            $("#groupe-edit-id").val(userGroupId);
        } else {
            $("#groupe-edit-id").val('');
        }

        $("#edit_user_popup").dialog("open");
        return false;
    });

    // Fermeture de la pop-up
    $("#btnCloseEditPopupUser").on('click', function () {
        $("#edit_user_popup").dialog("close");
    });
});
