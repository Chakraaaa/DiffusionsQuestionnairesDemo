$(function () {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.5;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.45;
    $("#save-template-email-popup").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });
    $("#open-save-template-popup").on('click', function () {
        let titrePopUp = 'Enregistrer un nouveau modèle';
        $("#save-template-email-popup").dialog({
            title: titrePopUp
        });

        $("#save-template-email-popup").dialog("open");
        return false;
    });

    $("#close-save-template-popup").on('click', function () {
        $("#save-template-email-popup").dialog("close");
    });
});
