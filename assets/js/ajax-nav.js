window.NAV = {

    history: [],
    referrer: undefined,

    /**
     * EVENTS
     */
    events: {
        'before':   [],
        'redirect': [],
        'after':    []
    },

    listen: function (event, handler) {
        this.events[event].push(handler);
        return this.events[event].length - 1;
    },

    unListen: function (event, index) {
        this.events[event].splice(index, 1);
    },

    _call: function(event, request){
        this.events[event].forEach(function(callback){
            callback(request);
        });
    },


    url: null,
    interval: undefined,

    start: function () {

        this.url = this.getCurrentUrl();
        this.history.push(this.getCurrentUrl());

        this.interval = setInterval(() => {
            this.handler();
        }, 10);
    },

    stop: function () {
        clearInterval(this.interval);
    },

    handler: function () {

        let currentUrl = this.getCurrentUrl();

        if (currentUrl !== this.url) {

            this.url = currentUrl;
            this.history.push(this.url);

            this.load(this.url);
        }
    },

    load: function (url, callback) {
        this.request(url, callback);
    },

    reload: function (callback) {
        this.request(this.url, callback);
    },

    nav: function(url, callback){
        this._setUrl(url);
        this.load(this.url, callback);
    },

    back: function () {

        if (this.history.length > 1) {

            this.history.splice(this.history.length-1);
            let url = this.history[this.history.length - 1];
            this.history.splice(this.history.length-1);

            this.nav(url);
            return;
        }

        NAV.nav('/' + location.pathname.split('/')[1]);
    },


    get: function (url, callback) {
        this.request(url, callback);
    },

    post: function (url, data, callback) {
        this._setUrl(url);
        this.request(url, callback, data, 'post');
    },

    request: function (url, callback, requestData, method) {

        let xmlHttpRequest = this._getXmlHttpRequest();

        xmlHttpRequest.open(method || "GET", this._addHash(url), true);

        xmlHttpRequest.setRequestHeader(
            'X-Requested-With',
            'XMLHttpRequest'
        );

        if (method === 'post') {

            xmlHttpRequest.setRequestHeader(
                'Content-Type',
                'application/x-www-form-urlencoded'
            );

            xmlHttpRequest.setRequestHeader(
                'X-Redirect',
                this.getReferrer()
            );
        }

        let callbackData = {
            'url': url,
            'requestData': requestData,
            'method': method,
            'xmlHttpRequest': xmlHttpRequest
        };

        this._call('before', callbackData);

        xmlHttpRequest.onprogress = () => {

            if (xmlHttpRequest.responseURL && !this._isEqualUrls(url, xmlHttpRequest.responseURL)) {

                this._setUrl(xmlHttpRequest.responseURL);
                this._call('redirect', callbackData);
            }
        };

        xmlHttpRequest.onload = () => {

            callbackData.responseData = xmlHttpRequest.responseText;
            this._call('after', callbackData);

            if (callback) {
                callback(xmlHttpRequest.responseText);
            }
        };

        xmlHttpRequest.send(requestData);
    },

    _setUrl: function (url) {

        this.stop();

        url = url.replace(location.protocol + '//' + location.hostname, '');

        this.url = url;
        history.pushState({}, null, url);

        if (this.history[this.history.length-1] !== url) {
            this.history.push(url);
        }

        this.start();
    },

    _getXmlHttpRequest: function () {

        if (typeof XMLHttpRequest !== 'undefined') {
            return new XMLHttpRequest();
        }

        try {
            return new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {

            try {
                return new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (ee) {
                return false;
            }
        }
    },

    _addHash: function (url) {

        let rand = ("" + Math.random()).substr(2, 15);

        if (url.indexOf('?') === -1) {
            return url + '?_=' + rand;
        }

        return url + '&_=' + rand;
    },

    _isEqualUrls: function(requested, response){

        let index = 0;

        if (response.indexOf('?_=') !== -1) {
            index = response.indexOf('?_=');
        }
        else if (response.indexOf('&_=') !== -1) {
            index = response.indexOf('&_=');
        }

        response = response.replace(location.protocol + '//' + location.host, '');

        if (index) {
            response = response.slice(0, -18);
        }

        return requested === response;
    },

    getCurrentUrl: function () {

        let url = location.pathname;

        if (location.search.length) {
            url = url + location.search;
        }

        return url;
    },

    getReferrer: function () {

        let referrer = '/' + this.url.split('/')[1];

        if (this.history[this.history.length-2]) {
            referrer = this.history[this.history.length-2];
        }

        if (referrer.indexOf('/manage/')) {
            referrer = '/' + referrer.split('/')[1];
        }

        return referrer;
    },

    getQueryParams: function () {

        let params = {};

        if (location.search.length) {
            location.search.substr(1).split('&').forEach((param) => {
                params[param.split('=')[0]] = param.split('=')[1];
            });
        }

        return params;
    },

    getQueryParamsFromString: function (query) {

        let params = {};

        if (query.length) {
            query.split('?')[1].split('&').forEach((param) => {
                params[param.split('=')[0]] = param.split('=')[1];
            });
        }

        return params;

    },

    getQueryStringFrom: function (obj) {

        let query = "";

        Object.keys(obj).forEach((key, index) => {

            if (index === 0) {
                query += key + '=' + obj[key];
            }
            else {
                query += '&' + key + '=' + obj[key];
            }
        });

        if (query.length) {
            query = "?" + query;
        }

        return query;

    }
};

NAV.start();