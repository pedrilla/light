window.ajaxLoaded = false;

$(document).ready(() => {

    if (window.ajaxLoaded) {
        return;
    }

    window.ajaxLoaded = true;

    let layout = $('#main');

    NAV.listen('after', (page) => {
        layout.html(page.responseData);
        $(window).scrollTop(0);
        loader.hide();
    });

    NAV.listen('before', () => {

        if (modal.isActive()) {
            modal.hide();
        }

        loader.show();
    });

    $(document).on('click', 'a[href]', function(e){

        if ($(this).data('force')) {
            return;
        }

        if ($(this).attr('href').length && $(this).attr('href') != '#') {
            NAV.nav($(this).attr('href'));
            $('.nav .dropdown.open').removeClass('open');
        }

        $(this).blur();
        return false;
    });
});