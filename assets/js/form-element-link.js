$(document).ready(function(){

    $(document).on('click', '[data-control-link] .dropdown-menu a', function(){

        let controlLink = $(this).closest('[data-control-link]');
        let option = $(this).data('option');

        let url = {
            'news-one': {
                url: '/news',
                title: 'Выберете новость'
            },
            'stock-one': {
                url: '/stock',
                title: 'Выберете акцию'
            },
            'category': {
                url: '/category',
                title: 'Выберете категорию'
            },
            'product': {
                url: '/product',
                title: 'Выберете товар'
            },
            'page': {
                url: '/page',
                title: 'Выберете страницу'
            }
        };

        if (url[option]) {

            $.get(url[option].url, {modal: true}, (content) => {

                modal.container(content, null, {wide: true}, url[option].title);

                $(modal.selector).find('[data-control-select-controller]').on('click', function(){

                    let item = JSON.parse(atob($(this).data('control-select-controller')));

                    if (option == 'page') {
                        insertItemText(item.id, item.title, controlLink);
                    }
                    else if (option == 'product') {
                        insertItemImage(item.id, item.title, item.images[0], controlLink);
                    }
                    else {
                        insertItemImage(item.id, item.title, item.image, controlLink);
                    }
                });
            });
        }
        else if (option == 'external') {
            let template = controlLink.find('[data-template-external]').html();
            controlLink.find('[data-link-container]').html(template);
        }
        else {
            controlLink.find('[data-link-container]').html('');
        }
    });

    function insertItemSimple (value, controlLink) {

        let template = controlLink.find('[data-template-simple]').html()
            .split('{{value}}').join(value);

        controlLink.find('[data-link-container]').html(template);
    }

    function insertItemText (value, title, controlLink) {

        let template = controlLink.find('[data-template-title]').html()
            .split('{{title}}').join(title)
            .split('{{value}}').join(value);

        controlLink.find('[data-link-container]').html(template);
    }

    function insertItemImage (value, title, image, controlLink) {

        let template = controlLink.find('[data-template-image]').html()
            .split('{{image}}').join(image)
            .split('{{title}}').join(title)
            .split('{{value}}').join(value);

        controlLink.find('[data-link-container]').html(template);
    }

});
