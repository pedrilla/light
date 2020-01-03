$(document).ready(function(){

    $(document).on('submit', '[data-category-position-filter-form]', function(){
        NAV.nav($(this).attr('action') + '?' + $(this).serialize());
        return false;
    });

    $(document).on('click', '[data-category-position-filter-form] [data-control-language]', function(){
        $('[data-category-position-filter-form] [data-control-category] input').val('');
    });

    $(document).on('click', '[data-category-position-filter-form] [data-option]', function(){
        setTimeout(() => {
            $('[data-category-position-filter-form]').submit();
        }, 300);
    });


});