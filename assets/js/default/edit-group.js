$(function () {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.5;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.5;
    $("#edit_group_popup").dialog({
        modal: true,
        autoOpen: false,
        minHeight: dHeight / 2,
        minWidth: dWidth
    });
    $(".btn_popup_edit_group").on('click', function () {
        let groupId = $(this).data("id");
        let groupName = $(this).data("nom");
        console.log("ID du groupe:", groupId);
        console.log("Nom du groupe:", groupName);
        $("#edit_group_popup").dialog({
            title: "Modification du groupe"
        });

        $("#groupe_id").val(groupId);
        $("#new_groupe_name").val(groupName);

        $("#edit_group_popup").dialog("open");
        return false;
    });

    $("#button-fermer-pop-up-edit-groupe").on('click', function () {
        $("#edit_group_popup").dialog("close");
    });
});
