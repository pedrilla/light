$(document).ready(function(){

    /// SIGN OUT

    $(document).on('click', '[data-sign-out]', function(){
        modal.confirm('Вы уверены что хотите выйти?', () => {
            location.href = $(this).data('sign-out');
        });
    });




    /// TABLE

    $(document).on('click', '[data-enabled]', function(){
        modal.confirm($(this).data('title'), () => {
            $.get($(this).data('href'), () => {
                NAV.reload();
            });
        });
    });

    $(document).on('click', '[data-copy]', function(){
        modal.confirm($(this).data('title'), () => {
            $.get($(this).data('href'), () => {
                NAV.reload();
            });
        });
    });









    $(document).on('submit', '[data-form-table]', function () {
        NAV.nav(location.pathname + '?' + $('[data-form-table]').serialize());
        return false;
    });


    $(document).on('submit', '[data-form-table-modal]', function () {
        loader.show();
        $.get($(this).attr('action'), $(this).serialize(), (content) => {
            $(this).replaceWith(content);
            loader.hide();
        });
        return false;
    });



    $(document).on('click', '[data-paginator] [data-page]', function () {
        $(this).closest('form').find('[name="page"]').val($(this).data('page'));
        $(this).closest('form').submit();
    });

    $(document).on('click', '[data-form-table] [data-control-select] [data-option], [data-form-table-modal] [data-control-select] [data-option]', function () {
        setTimeout(() => {
            $(this).closest('form').submit();
        }, 300);
    });







    /// POSITION

    $(document).on('click', '[data-table-position]', function () {

        loader.show();

        $.get($(this).data('href'), (content) => {
            modal.container(content, () => {NAV.reload();}, {wide: true}, 'Управление позицией');
            loader.hide();
            applySortable();
        });
    });

    $(document).on('click', '[data-position-container] [data-control-select] [data-option]', function(){

        loader.show();

        $.get($('[data-position-container] form').data('language-action') + '?language=' + $(this).data('option'), (content) => {
            loader.hide();
            $('[data-position-container]').replaceWith(content);
            applySortable();
        });
    });

    function applySortable() {
        $('[data-sortable-list]').sortable({
            stop: function () {
                let form = $('[data-position-container] form');
                $.post(form.data('set-position-action'), form.serialize());
            }
        });
    }









    /// FORMS

    $(document).on('submit', '[data-from-manage]', function(){
        $.post(location.pathname, $(this).serialize(), (r) => {
            if (r.substr(0, 3) == 'ok:') {
                NAV.nav(r.substr(3));
                return;
            }
            $('#main').html(r);
        });
        return false;
    });



















    let activeSelectControl = null;

    $(document).on('click', '[data-form-table-modal] [data-control-select-controller]', function(){

        if (!activeSelectControl) {
            modal.hide();
        }

        try {

            let item = JSON.parse(atob($(this).data('control-select-controller')));

            if (activeSelectControl.data('self-id') == item.id) {
                modal.message('Нельзя добавить эту запись');
                return false;
            }

            let container = activeSelectControl.closest('[data-select-controller-container]');

            let template = container.find('[data-select-controller-template]').html();

            activeSelectControl.data('vars').split('|').forEach((variable) => {

                if (variable == 'image') {

                    if (item.images && item.images.length) {
                        template = template.split('{{image}}').join(item.images[0]);
                    }
                    else {
                        template = template.split('{{image}}').join(item.image);
                    }
                }
                else {
                    template = template.split('{{' + variable + '}}').join(item[variable]);
                    console.log(activeSelectControl);
                }
            });

            if (activeSelectControl.data('multiple')) {
                container.find('[data-select-controller-list]').append(template);
            }
            else {
                container.find('[data-select-controller-list]').html(template);
            }
        }
        catch (e) {}

        modal.hide();
    });

    $(document).on('click', '[data-select-controller]', function(){
        activeSelectControl = $(this);
        loader.show();
        $.get($(this).data('select-controller'), {modal: true}, (content) => {
            loader.hide();
            modal.container(content, () => {
                activeSelectControl = null;
            }, {wide: true}, $(this).data('title'));
        });
    });


    $(document).on('click', '[data-select-controller-container] [data-select-controller-delete]', function(){

        modal.confirm('Удалить запись?', () => {
            $(this).closest('[data-select-controller-item]').remove();
        });
    });


    setInterval(() => {

        $('[data-select-controller-container]').each(function(){
            if (!$(this).attr('data-initialized')) {
                $(this).attr('data-initialized', 'true');
                $(this).find('[data-select-controller-list]').sortable();
            }
        });

    }, 100);


});