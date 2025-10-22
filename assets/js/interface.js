const tooltip = $(function () {
    $(".tooltip").tooltip({
        track: true,
        content: function () {
            return this.title.replace(/\r/g, '<br />');
        },
        show: {
            effect: "fade",
            delay: 200
        }
    });
});

const toogleDelete = $(".delete").click(function () {

    $(this).parent().toggle();
});

export { tooltip, toogleDelete };