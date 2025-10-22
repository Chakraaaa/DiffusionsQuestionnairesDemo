$(document).ready(function() {
    if ($("#intro-textarea").val().trim() !== "") {
        $("#intro-input").show();
        tinymce.init({
            selector: '#intro-textarea',
            plugins: 'link image code contextmenu lists',
            language_url: "https://questionnaire.relaismanagers.fr/assets/js/tinymce/fr_FR.js",
            language : "fr_FR",
            contextmenu: "paste | link image inserttable | cell row column deletetable",
            menubar: 'edit insert format',
            toolbar: "undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist",
            license_key: 'gpl',
        });
        $("#add-intro").prop('checked', true);
    }

    if ($("#conclusion-textarea").val().trim() !== "") {
        $("#conclusion-input").show();
        tinymce.init({
            selector: '#conclusion-textarea',
            plugins: 'link image code contextmenu lists',
            language_url: "https://questionnaire.relaismanagers.fr/assets/js/tinymce/fr_FR.js",
            language : "fr_FR",
            contextmenu: "paste | link image inserttable | cell row column deletetable",
            menubar: 'edit insert format',
            toolbar: "undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist",
            license_key: 'gpl',
        });
        $("#add-conclusion").prop('checked', true);
    }

    $("#show-header").change(function() {
        $("#header-input").toggle(this.checked);
        if (!this.checked) {
            $("#header-input input").val('');
        }
    });

    $("#add-intro").change(function() {
        $("#intro-input").toggle(this.checked);
        if (this.checked) {
            tinymce.init({
                selector: '#intro-textarea',
                plugins: 'link image code contextmenu lists',
                language_url: "https://questionnaire.relaismanagers.fr/assets/js/tinymce/fr_FR.js",
                language : "fr_FR",
                contextmenu: "paste | link image inserttable | cell row column deletetable",
                menubar: 'edit insert format',
                toolbar: "undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist",
                license_key: 'gpl',
            });
        } else {
            tinymce.get('intro-textarea').remove();
            $("#intro-textarea").val('');
        }
    });

    $("#add-conclusion").change(function() {
        $("#conclusion-input").toggle(this.checked);
        if (this.checked) {
            tinymce.init({
                selector: '#conclusion-textarea',
                plugins: 'link image code contextmenu lists',
                language_url: "https://questionnaire.relaismanagers.fr/assets/js/tinymce/fr_FR.js",
                language : "fr_FR",
                contextmenu: "paste | link image inserttable | cell row column deletetable",
                menubar: 'edit insert format',
                toolbar: "undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist",
                license_key: 'gpl',
            });
        } else {
            tinymce.get('conclusion-textarea').remove();
            $("#conclusion-textarea").val('');
        }
    });

    $("#show-footer").change(function() {
        $("#footer-input").toggle(this.checked);
        if (!this.checked) {
            $("#footer-input input").val('');
        }
    });
});
