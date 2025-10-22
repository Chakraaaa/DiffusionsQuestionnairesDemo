/**
 * @brief WIDGETS JQUERY-UI
 *
 * @version 1.2009 - Création
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2020, www.DavidSENSOLI.com
 */

/** Calendrier */
const datepicker = $(".datepicker").datepicker({
    dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
    monthNames: ["Janvier", "F&eacute;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre"],
    dateFormat: "dd/mm/yy",
    autoSize: true
});

/** Boite de dialogue */
const dialog = $(function () {

    const wWidth = $(window).width();
    const wHeight = $(window).height();

    $("#dialog").dialog({
        modal: true,
        minWidth: wWidth / 3,
        minHeight: wHeight / 4,
        buttons: {
            Ok: function () {
                $(this).dialog("close");
            }
        },
        show: {
            effect: "blind",
            duration: 300
        },
        hide: {
            effect: "explode",
            duration: 300
        }
    });

    $(".dialog-button").click(function (event, wastriggered) {

        if (wastriggered) {

            //console.log("trigger");
            //event.preventDefault();

        } else {

            //console.log("mouse");

            event.preventDefault();

            var bouton = $(this);
            var wWidth = $(window).width();
            var dWidth = wWidth * 0.5;
            var msg = $(this).data('dsi-msg');

            $('<div></div>').appendTo('body')
                    .html(msg)
                    .dialog({
                        resizable: false,
                        height: "auto",
                        title: "Confirmation",
                        width: dWidth,
                        modal: true,
                        buttons: {
                            "Annuler": function () {
                                $(this).dialog("close");
                            },
                            "Ok": function () {
                                $(this).dialog("close");
                                bouton.trigger('click', true);
                            }
                        },
                        close: function (event, ui) {
                            $(this).remove();
                        }
                    });
        }

    });

});

export { datepicker, dialog };


