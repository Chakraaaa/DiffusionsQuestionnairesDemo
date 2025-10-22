$(function () {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.5;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.5;
    const dialogs = {
        chapter360: $("#fen_pop_up_create_single_chapter360").dialog({
            modal: true,
            autoOpen: false,
            minHeight: dHeight / 2,
            minWidth: dWidth
        }),
        radio360Text: $("#fen_pop_up_create_single_radio360_text").dialog({
            modal: true,
            autoOpen: false,
            minHeight: dHeight / 2,
            minWidth: dWidth
        }),
        radio360List: $("#fen_pop_up_create_single_radio360_list").dialog({
            modal: true,
            autoOpen: false,
            minHeight: $(window).height() * 0.5,
            minWidth: $(window).width() * 0.9
        })
    };

    $("#add_new_chapter360").on('click', function () {
        dialogs.chapter360.dialog({
            title: "Créer un nouveau chapitre"
        }).dialog("open");
        return false;
    });

    $("#add_new_radio360Text").on('click', function () {
        dialogs.radio360Text.dialog({
            title: "Ajouter une question"
        }).dialog("open");
        return false;
    });

    $("#add_new_radio360List").on('click', function () {
        dialogs.radio360List.dialog({
            title: "Ajouter une question liste"
        }).dialog("open");
        return false;
    });

    $(document).on('click', '#button-fermer-pop-up-create-single-chapter360', function () {
        dialogs.chapter360.dialog("close");
    });

    $(document).on('click', '#button-fermer-pop-up-create-single-radio360-text', function () {
        dialogs.radio360Text.dialog("close");
    });

    $(document).on('click', '#button-fermer-pop-up-create-single-radio360-list', function () {
        dialogs.radio360List.dialog("close");
    });
});
