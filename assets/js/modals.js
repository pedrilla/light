window.modal = {

    queueArray: [],
    queueDone: false,
    domLoaded: false,

    templates: {
        message:   '#modal-message',
        confirm:   '#modal-confirm',
        container: '#modal-container'
    },

    selector: 'body > .modal',

    message: function (message, callback, options) {
        let template = $(this.templates.message).html().replace('{{message}}', message);
        this._hideModalIfItsOpenedAndOpenNew(template, callback, options);
    },

    confirm: function (message, callback, closeCallback, options) {

        let template = $(this.templates.confirm)
            .html()
            .replace('{{message}}', message);

        modal._hideModalIfItsOpenedAndOpenNew(template, closeCallback, options);

        $(document).on('click', modal.selector + ' ' + '[data-modal-confirm]', () => {

            modal.hide();

            if (callback){
                callback();
            }
        });
    },

    container: function (content, callback, options, title = 'Сообщение') {
        this._hideModalIfItsOpenedAndOpenNew($(this.templates.container).html().replace('{{content}}', content).replace('{{title}}', title), callback, options);
    },

    queue: function (content, type, callback) {

        this.queueArray.push({content: content, type: type, callback: callback});

        if (this.queueDone) {
            this.startModalsFromQueue();
        }
    },

    _hideModalIfItsOpenedAndOpenNew: function (template, callback, options) {

        if ($(this.selector).length) {

            $(this.selector).modal('hide');

            setTimeout(() => { this._show(template, callback, options); }, 400);

            return;
        }

        this._show(template, callback, options);
    },

    _show: function (template, callback, options) {

        $('body').append(template);

        $(this.selector).on('hidden.bs.modal', () => {

            if (callback) {
                callback();
            }

            $(this.selector).remove();
        });

        options = options || {};

        if (options.wide) {
            $(this.selector).find('.modal-dialog').addClass('modal-wide');
        }

        $(this.selector).modal('show');
    },

    hide: function () {
        $(this.selector).modal('hide');
    },

    isActive: function() {
        return $(this.selector).length > 0;
    },

    startModalsFromQueue: function () {

        this.queueDone = false;

        if (this.queueArray.length) {

            let queue = this.queueArray[0];
            this.queueArray.splice(0, 1);

            if (queue.type == 'confirm') {

                this[queue.type](queue.content, queue.callback, () => {
                    this.startModalsFromQueue();
                });
            }
            else {
                this[queue.type](queue.content, () => {

                    if (queue.callback) {
                        queue.callback();
                    }

                    this.startModalsFromQueue();
                });
            }
        }
        else {
            this.queueDone = true;
        }
    },

    start: function () {

        $(document).ready(() => {
            this.domLoaded = true;
            this.startModalsFromQueue();
        });
    }
};

modal.start();
