$(document).ready(function() {
    $('#select-all').change(function() {
        $('.user-checkbox').prop('checked', this.checked);
        toggleMultipleButton();
    });

    $('.user-checkbox').change(function() {
        toggleMultipleButton();
    });

    function toggleMultipleButton() {
        const anyChecked = $('.user-checkbox:checked').length > 0;
        $('#multiple-button-container').toggle(anyChecked);
    }
    $('#delete-selected').click(function() {
        const selectedIds = $('.user-checkbox:checked').map(function() {
            return $(this).data('user-id');
        }).get();
        if (selectedIds.length > 0 && confirm('Confirmez-vous la suppression des utilisateurs sélectionnés ?')) {
            const idsString = selectedIds.join(',');
            const url = "users.html/deleteMultiple?usersIds=" + idsString;
            document.location.href = url;
        }
    });

    $('#MultipleGroupEditButtonSubmit').click(function() {
        const selectedIds = $('.user-checkbox:checked').map(function() {
            return $(this).data('user-id');
        }).get();
        if (selectedIds.length > 0 && confirm('Confirmez-vous la validation ?')) {

            const groupSelectedId = $("#multiple-groupe-edit-id").val().trim();
            if (groupSelectedId === "") {
                alert("Le nom du groupe ne peut pas être vide.");
                return;
            } else {
                const idsString = selectedIds.join(',');
                const url = "users.html/groupeMultiple?usersIds=" + idsString + "&grouIdMultiple=" + groupSelectedId;
                document.location.href = url;
            }

        }
    });

});
