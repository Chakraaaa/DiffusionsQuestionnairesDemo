$(function () {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.5;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.5;

    // Configuration du pop-up pour créer un nouveau chapitre
    $("#fen_pop_up_create_chapter360").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });

    // Ouverture du pop-up pour créer un nouveau chapitre
    $("#add_new_chapter360").on('click', function () {
        $("#fen_pop_up_create_chapter360").dialog({
            title: "Créer un nouveau chapitre"
        });
        $("#fen_pop_up_create_chapter360").dialog("open");
        return false;
    });

    // Configuration du pop-up pour ajouter une question simple
    $("#fen_pop_up_create_radio360_text").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });

    // Ouverture du pop-up pour ajouter une question simple
    $("#add_new_radio360Text").on('click', function () {
        $("#fen_pop_up_create_radio360_text").dialog({
            title: "Ajouter une question"
        });
        $("#fen_pop_up_create_radio360_text").dialog("open");
        return false;
    });

    $("#fen_pop_up_create_radio360_list").dialog({
        modal: true,
        autoOpen: false,
        minHeight: $(window).height() * 0.5,
        minWidth: $(window).width() * 0.9
    });

    $("#add_new_radio360List").on('click', function () {
        $("#fen_pop_up_create_radio360_list").dialog({
            title: "Ajouter une question liste"
        });
        $("#fen_pop_up_create_radio360_list").dialog("open");
        return false;
    });

    $("#button-fermer-pop-up-create-chapter360").on('click', function () {
        $("#fen_pop_up_create_chapter360").dialog("close");
    });

    $("#button-fermer-pop-up-create-radio360-list").on('click', function () {
        $("#fen_pop_up_create_radio360_list").dialog("close");
    });

    $("#button-fermer-pop-up-create-radio360-text").on('click', function () {
        $("#fen_pop_up_create_radio360_text").dialog("close");
    });
});

