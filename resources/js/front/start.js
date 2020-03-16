/**
 * Created by ander on 7/12/18.
 */

import * as Cookies from 'js-cookie';
import * as EventEmitter from 'events';
import Vue from "vue";
import VueLazyload from "vue-lazyload";

if (!String.format) {
    /**
     *
     * @param {string} format
     * @return {*|void|XML|string}
     */
    String.format = function (format) {
        let splitter = '%s';
        let parts = format.split(splitter);
        let newFormat = '';

        for (let x = 0; x < parts.length; x++) {
            let r = x + 1 < 1 || arguments.length <= x + 1 ? undefined : arguments[x + 1];

            if (!r) {
                r = '';
            }

            newFormat += parts[x];
            newFormat += r;
        }

        return newFormat;
    };
}

String.prototype.isEmpty = function () {
    return this.length === 0 || !this.trim();
};

String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

/**
 *
 * @param {string} delimiter
 * @param {...string} strings
 * @returns {string}
 */
String.join = function (delimiter, ...strings) {
    let str = '';
    strings.forEach(function (string) {
        str += (string + delimiter);
    });

    return str.replace(delimiter, '');
};

/**
 *
 * @returns {Array}
 */
Array.trim = function(array) {
    let uniqueArray = [];

    array.forEach(function (value) {
        if (uniqueArray.indexOf(value) < 0) {
            uniqueArray.push(value);
        }
    });

    return uniqueArray;
};

if (!Date.fromUTCString) {
    Date.fromUTCString = function (date) {
        return new Date(date + 'Z');
    }
}

window.creaEvents = new EventEmitter();
window.creaEvents.setMaxListeners(20);
window.jQuery = window.$ = require('jquery');
$.holdReady(true);

/**
 *
 * @returns {{set: set, get: (function(*=): *), remove: remove}}
 * @constructor
 */
let CookieManager = function() {
    return {
        get: function(name) {
            return Cookies.get(name);
        },
        set: function(name, value, attributes) {
            Cookies.set(name, value, attributes);
        },
        remove: function(name, attributes) {
            Cookies.remove(name, attributes);
        }
    };
};

/**
 *
 * @param attributes
 * @returns {{set: set, get: (function(*=): *), remove: remove}}
 */
function createCookieInstance(attributes) {
    let m = CookieManager();
    return {
        get: function(name) {
            return m.get(name);
        },
        set: function(name, value) {
            m.set(name, value, attributes);
        },
        remove: function(name) {
            m.remove(name, attributes);
        }
    }
}

window.CreaCookies = createCookieInstance({
    domain: window.location.hostname
});

window.currentPage = null;

creaEvents.on('crea.content.prepare', function () {
    console.log('Content prepare on start');
    Vue.use(VueLazyload);

    try {
        window.globalLoading = new Vue({
            el: '#global-loading',
            data: {
                show: true
            }
        });
    } catch (e) {
        console.error(e);
    }

    try {
        if (document.getElementById('home-banner') !== null) {
            window.bannerVue = new Vue({
                el: '#home-banner',
                data: {
                    showBanner: false,
                    lang: lang
                }
            });

        }
    } catch (e) {
        console.error(e);

    }

});



