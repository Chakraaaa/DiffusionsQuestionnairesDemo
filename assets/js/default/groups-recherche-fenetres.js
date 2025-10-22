$(function () {

    var wWidth = $(window).width();
    var dWidth = wWidth * 0.5;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.5;

    $("#fen_popup_group").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });

    $(".btn_popup_group").on('click', function () {
        let groupId = $(this).data("id");
        let groupName = $(this).data("nom");
        let titrePopUp;
        $("#groupe_name").val('');
        titrePopUp = 'Ajout d\'un groupe';
        $("#fen_popup_group").dialog({
            title: titrePopUp
        });

        $("#fen_popup_group").dialog("open");
        $("#groupId").val(groupId);
        $("#groupName").val(groupName);
        return false;
    });

    $("#button-fermer-pop-up-groupe").on('click', function () {
        $("#fen_popup_group").dialog("close");
    });
});
