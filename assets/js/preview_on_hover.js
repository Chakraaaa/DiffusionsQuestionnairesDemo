this.previewPreview = function () {

    /* CONFIG */
    xOffset = 300; // Décalage vertical au curseur
    yOffset = 30; // Décalage horizontal au curseur

    /* END CONFIG */
    $(".preview").css('cursor', 'zoom-in');
    $(".preview").hover(function (e) {

        var url_image = $(this).data('dsi-previewed');
        var extension = url_image.split(".").pop().toLowerCase();
        this.t = this.title;
        this.title = "";
        var caption = (this.t !== "") ? "<br/>" + this.t : "";

        if (extension != 'pdf') {
            $("body").append("<p id='preview'><span><img src='" + url_image + "' alt='Impossible de charger l&apos;image...' /></span><span>" + caption + "</span></p>");
            $("#preview img").css({
                'margin': '0px',
                'max-width': '100%',
                'height': 'auto'
            });

        } else {
            $("body").append("<p id='preview'><span><object type='application/pdf' data='" + url_image + "' width='100%' style='height: 50vh' ></object></span><span>" + caption + "</span></p></p>");
        }

        $("#preview").css({
            'position': 'absolute',
            'display': 'flex',
            'flex-direction': 'column',
            'justify-content': 'flex-end',
            'padding': '.5rem',
            'color': '#fff',
            'background-color': 'rgba(0, 0, 0, 0.8)',
            'z-index': '150',
            'text-align': 'center',
            'width': '33vw',
            'height': 'auto',
            'top': (e.pageY - xOffset) + "px",
            'left': (e.pageX + yOffset) + "px"
        }).fadeIn("slow");

    },
            function () {
                this.title = this.t;
                $("#preview").remove();
            });
    $(".preview").mousemove(function (e) {
        $("#preview")
                .css("top", (e.pageY - xOffset) + "px")
                .css("left", (e.pageX + yOffset) + "px");
    });
};
// Lancement du script au chargement de la page
$(document).ready(function () {
    previewPreview();
});


