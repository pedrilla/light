$(document).ready(function () {
    
    setTimeout(() => {
        $(document).click();
    }, 300);


    $(document).on('click', '[data-control-select] [data-option]', function () {

        $($(this).closest('[data-control-select]').find('input')[0]).val($(this).data('option'));

        $(this).closest('.dropdown-menu')
            .find('li > a')
            .removeClass('bmd-bg-primary')
            .removeClass('bmd-text-grey-50');

        $(this)
            .addClass('bmd-bg-primary')
            .addClass('bmd-text-grey-50');

    });


    $(document).on('focus', '.form-group .bmd-field-group > input, .form-group .bmd-field-group > textarea', function () {

        if (!$(this).closest('.form-group').hasClass('has-error')) {
            $(this).closest('.form-group').addClass('has-primary');
        }

        $(this).closest('.bmd-field-group').find('.bmd-label').addClass('up').removeClass('down');
    });

    $(document).on('blur', '.form-group .bmd-field-group > input, .form-group .bmd-field-group > textarea', function () {

        $(this).closest('.form-group').removeClass('has-primary');

        if ($(this).val().length) {
            $(this).closest('.bmd-field-group').find('.bmd-label').addClass('up').removeClass('down');
        } else {
            $(this).closest('.bmd-field-group').find('.bmd-label').addClass('down').removeClass('up');
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.bmd-fab-speed-dialer').length) {
            $('.bmd-fab-speed-dialer').removeClass('press');
        }
    });

    $(document).on('click', '[data-image-large]', function () {
        modal.container('<img class="full-screen-image" src="' + $(this).data('image-large') + '" />');
    });

    function normalizeUi() {

        $('[data-toggle="tooltip"][data-bmd-state]').each(function () {

            if ($(this).data('initialized')) {
                return;
            }
            $(this).attr('data-initialized', 'true');

            $(this).tooltip({
                template: bmd_GLOBAL.tooltipStateTemplate.replace('bmd-tooltip-state', 'bmd-tooltip-' + $(this).data('bmd-state'))
            });
        });

        setTimeout(() => {

            $('.bmd-field-group > .bmd-label').each(function () {

                if ($(this).data('initialized')) {
                    return;
                }
                $(this).attr('data-initialized', 'true');

                $('.form-group .bmd-field-group > input, .form-group .bmd-field-group > textarea').focus().blur();
            });

        }, 300);


        if ($('[data-color-picker]').length) {

            $('[data-color-picker]').each(function () {

                if (!$(this).data('initialized')) {
                    $(this).attr('data-initialized');

                    $(this).ColorPicker({
                        color: $(this).find('input').val(),
                        onShow: (colpkr) => {
                            $(colpkr).fadeIn(100);
                            return false;
                        },
                        onHide: (colpkr) => {
                            $(colpkr).fadeOut(100);
                            return false;
                        },
                        onChange: (hsb, hex, rgb) => {
                            $(this).find('div').css('backgroundColor', '#' + hex);
                            $(this).find('input').val('#' + hex);
                        }
                    });
                }
            });
        } else {
            if ($('body > .colorpicker').length) {
                $('body > .colorpicker').remove();
            }
        }

        if ($('[data-datetimepicker]').length) {

            $('[data-datetimepicker]').each(function () {

                if (!$(this).data('initialized')) {
                    $(this).attr('data-initialized', 'true');

                    $(this).bootstrapMaterialDatePicker({
                        time: true,
                        weekStart: 1,
                        format: $(this).data('format'),
                    }).on('change', () => {
                        $(this).blur();
                    });
                }
            });
        }

        if ($('[data-datepicker]').length) {

            $('[data-datepicker]').each(function () {

                if (!$(this).data('initialized')) {
                    $(this).attr('data-initialized', 'true');

                    $(this).bootstrapMaterialDatePicker({
                        time: false,
                        weekStart: 1,
                        format: $(this).data('format'),
                    }).on('change', () => {
                        $(this).blur();
                    });
                }
            });
        }
    }

    setInterval(normalizeUi, 100);
});
