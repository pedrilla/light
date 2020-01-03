$(document).ready(function(){

    window.trumbowygConf = {
        semantic: false,
        autogrow: true,
        removeformatPasted: true,
        btnsDef: {
            align: {
                dropdown: ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ico: 'justifyLeft'
            },
            lists: {
                dropdown: ['unorderedList', 'orderedList'],
                ico: 'unorderedList'
            },
            styles: {
                dropdown: ['strong', 'em', 'del', 'underline'],
                ico: 'strong'
            },
            scripts: {
                dropdown: ['superscript', 'subscript'],
                ico: 'superscript'
            },
            media: {
                dropdown: ['storage', 'insertImage', 'noembed', 'pasteImage', 'insertAudio', 'table', 'horizontalRule'],
                ico: 'insertImage'
            }
        },
        btns: [
            ['viewHTML'],
            ['formatting'],
            ['styles'],
            ['foreColor', 'backColor'],
            ['fontsize'],
            ['scripts'],
            ['link'],
            ['align'],
            ['lists'],
            ['media'],
            ['removeformat']
        ]
    };


    setInterval(function(){
        $('[data-editor-item] textarea').each(function(){
            if (!$(this).data('initialized')) {
                $(this).attr('data-initialized', 'true');
                $(this).trumbowyg(trumbowygConf);
            }
        });
    }, 100);

    setInterval(function(){

        if ($('[data-editor-item] textarea').length) {
            if ($('#trumbowyg-icons').css('visibility') == 'hidden') {
                $('#trumbowyg-icons').css('visibility', 'visible');
            }
            else {
                $('#trumbowyg-icons').css('visibility', 'hidden');
            }
        }

    }, 1000);


    $(document).on('click', '[data-editor] [data-editor-control=add]', function () {

        $(this).replaceWith(
            $('#template-control-' + $(this).data('name')).html() +
            $('#template-content-' + $(this).data('name')).html() +
            $('#template-control-' + $(this).data('name')).html()
        );

        $('[data-trumbowyg]').trumbowyg(trumbowygConf);
    });

    $(document).on('click', '[data-editor] [data-editor-item-delete]', function(){

        modal.confirm('Удалить секцию?', () => {

            var row = $(this).closest('[data-editor-item-content]');
            var delimiter = row.children().length - 1;

            $(this).closest('[data-cell]').remove();

            if (delimiter == 0) {
                row.next().remove();
                return;
            }

            if (delimiter == -1) {
                row.remove();
                return;
            }

            row.find('[data-cell]').each(function(){

                $(this).find('input').val('full');

                $(this).removeClass('col-sm-6');
                $(this).removeClass('col-sm-12');

                $(this).addClass('col-sm-12');
            });
        });
    });
});
