$(function () {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.5;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.5;
    $("#fen_popup_quiz").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });

    $(".btn_popup_quiz").on('click', function () {
        let titrePopUp = "Création d'un questionnaire 360";

        $("#fen_popup_quiz").dialog({
            title: titrePopUp
        });

        $("#fen_popup_quiz").dialog("open"); // Ouvrir le popup
    });

    $("#ClosePopUpQuiz").on('click', function () {
        $("#fen_popup_quiz").dialog("close");
    });


    $("#fen_popup_create_barom").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });

    $(".btn_popup_create_barom").on('click', function () {
        let titrePopUp = "Création d'un questionnaire baromètre";

        $("#fen_popup_create_barom").dialog({
            title: titrePopUp
        });

        $("#fen_popup_create_barom").dialog("open");
    });

    $("#ClosePopUpCreateBarom").on('click', function () {
        $("#fen_popup_create_barom").dialog("close");
    });

    $("#fen_popup_create_prcc").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });

    $(".btn_popup_create_prcc").on('click', function () {
        let titrePopUp = "Création d'un questionnaire PRCC";

        $("#fen_popup_create_prcc").dialog({
            title: titrePopUp
        });

        $("#fen_popup_create_prcc").dialog("open");
    });

    $("#ClosePopUpCreatePrcc").on('click', function () {
        $("#fen_popup_create_prcc").dialog("close");
    });

});
