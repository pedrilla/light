window.loader = {

    enabled: true,

    template: `<div class="loader" id="loader"><div class="bmd-spinner bmd-spinner-primary"><svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20"></circle></svg></div></div>`,

    show: function (force) {

        if ($('#loader').is(':visible')) {
            return;
        }

        if (!$('#loader').length) {
            $('body').append(this.template);
        }

        if (this.enabled || force) {
            setTimeout(() => {
                $('#loader').css({'opacity': '1'});
            }, 100);
        }
    },

    hide: function () {
        $('#loader').css({'opacity': '0'});
        setTimeout(() => {
            $('#loader').remove();
        }, 500);
    }
};