/**
 * Created by ander on 7/12/18.
 */
import $ from 'jquery';
import * as Cookies from 'js-cookie';
import * as EventEmitter from 'events';
import * as crea from '@creativechain-fdn/crea-js';
import Vue from "vue";
import VueLazyload from "vue-lazyload";
import Compressor from 'compressorjs';
import moment from "moment";
import { Buffer } from 'buffer';


Vue.use(VueLazyload);
moment.locale($('html').attr('lang'));
window.apiOptions = {
    nodes: ['https://nodes.creary.net'],
    apiUrl: 'https://api.creary.net',
    ipfs: 'https://ipfs.creary.net/ipfs/',
    ipfsd: 'https://api.creary.net/ipfs',
    addressPrefix: 'CREA',
    symbol: {
        CREA: 'CREA',
        CGY: 'CGY',
        CBD: 'CBD',
        VESTS: 'VESTS'
    },
    nai: {
        CREA: '@@000000021',
        CBD: '@@000000013',
        VESTS: '@@000000037',
        CGY: '@@000000005'
    },
    chainId: '0000000000000000000000000000000000000000000000000000000000000000'
};

localStorage.debug = 'crea:*';
crea.api.setOptions(apiOptions);
crea.config.set('address_prefix', apiOptions.addressPrefix);
crea.config.set('chain_id', apiOptions.chainId);

window.$ = window.jQuery = $;
window.Vue = Vue;
window.crea = crea;
window.moment = moment;
window.Buffer = Buffer;
window.Compressor = Compressor;

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
        set: function(name, value, attributes) {
            m.set(name, value, attributes);
        },
        remove: function(name, attributes) {
            m.remove(name, attributes);
        }
    }
}

window.CreaCookies = createCookieInstance({
    domain: window.location.hostname
});

window.SAVINGS_BLACK_LIST = ['exrates', 'exrates1', 'exrates-test', 'exrates-test-2', 'exratesfull', 'skytali7'];

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



