<p>Il y a <?= $users_nombres ?> personnes avec un email.</p>
<a class="btn-details" data-details-type="emails_all">Détails</a>
<p><?= $nbrUsersAlreadyReceivedEmail ?> personnes ont déjà reçu l'invitation.</p>
<a class="btn-details" data-details-type="emails_received">Détails</a>
<p>Il y aura donc <?= $totalEmailsToSend ?> emails à envoyer.</p>
<a class="btn-details" data-details-type="emails_to_send">Détails</a>

<div id="details-popup" style="display: none;">
    <!-- RESULTATS AJAX -->
</div>

<script>
    $(function () {
        $(document).on('click', '.btn-details', function () {
            const detailsType = $(this).data('details-type');
            $.ajax({
                url: '<?=$urlFetchPopupEmailsDetails?>' + '&type=' + detailsType,
                type: 'GET',
                success: function (data) {
                    const $popup = $("#details-popup");
                    $popup.html(data);
                    if ($popup.dialog("instance")) {
                        $popup.dialog("destroy");
                    }

                    const wWidth = $(window).width();
                    const wHeight = $(window).height();
                    $popup.dialog({
                        modal: true,
                        minWidth: wWidth * 0.5,
                        minHeight: wHeight * 0.3,
                        close: function () {
                            $popup.html("");
                        }
                    });

                    $popup.dialog("open");
                    $(document).off('click', '#close-popup-emails-details').on('click', '#close-popup-emails-details', function () {
                        $popup.dialog("close");
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Erreur lors de la récupération des détails : ", error);
                    alert("Une erreur s'est produite lors de la récupération des détails.");
                }
            });
        });
    });
</script>



