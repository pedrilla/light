/* ===========================================================
 * trumbowyg.storage.js
 */

(function ($) {

    'use strict';

    $.extend(true, $.trumbowyg, {
        plugins: {
            storage: {
                init: function (trumbowyg) {

                    trumbowyg.addBtnDef('storage', {

                        fn: function () {

                            trumbowyg.saveRange();

                            $.get('/storage', {modal: true}, (storage) => {

                                modal.container(storage, null, {wide: true}, 'Управление файлами');

                                window.selectImage = (image) => {
                                    trumbowyg.execCmd('insertHtml', '<img src="' + image + '" />');
                                    modal.hide();
                                };
                            });
                        }
                    });
                }
            }
        }
    });

})(jQuery);
