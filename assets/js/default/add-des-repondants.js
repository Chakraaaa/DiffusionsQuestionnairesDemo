$(function () {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.5;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.5;

    $("#AddUsersPopUp").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });

    $(".btn_add_des_repondants").on('click', function () {
        let userid = $(this).data("id");
        let titrePopUp = 'Ajout de répondants';

        $("#AddUsersPopUp").dialog({
            title: titrePopUp
        });

        $("#AddUsersPopUp").dialog("open");
        $("#userid").val(userid);
        return false;
    });

    $("#AddUsersPopUp").on('submit', 'form', function (event) {

        let nbrRepondants = $("#nbr_repondants").val();

        if (nbrRepondants < 2 || nbrRepondants > 500) {
            alert("Veuillez saisir un nombre entre 2 et 500.");
            return;
        }
    });

    $("#BtnClosePopupDesUsers").on('click', function () {
        $("#AddUsersPopUp").dialog("close");
    });
});
