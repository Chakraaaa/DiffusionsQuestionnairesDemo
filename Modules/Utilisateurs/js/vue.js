var app = new Vue({
    el: '#utilisateur',
    data: {
        activetab: 0
    },
    created: function () {
        if (sessionStorage.tabs_user) {
            this.activetab = parseInt(sessionStorage.tabs_user);
        } else {
            this.activetab = 1;
        }
    },
    updated: function () {
        sessionStorage.tabs_user = this.activetab;
    }
});
