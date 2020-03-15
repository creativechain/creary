import { moment } from '../common/common';

/**
 * Created by ander on 29/09/18.
 */

/**
 *
 * @param {Event} event
 */
function cancelEventPropagation(event) {
    if (event && event.preventDefault) {
        event.preventDefault();
    }
}

function isJSON(string) {
    try {
        JSON.parse(string);
        return true;
    } catch (e) {}

    return false;
}

/**
 *
 * @param obj
 * @returns {*}
 */
function jsonify(obj) {
    if (obj && typeof obj == 'string') {
        try {
            return JSON.parse(obj);
        } catch (e) {
            console.error('JSON error', e, 'Object:', obj);
        }
    }

    return {};
}

/**
 *
 * @param obj
 * @returns {string}
 */
function jsonstring(obj) {
    if (obj && typeof obj== 'object') {
        return JSON.stringify(obj);
    }

    return obj;
}

function validateEmail(email) {
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 *
 * @param name
 * @param url
 * @returns {*}
 */
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    let regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

/**
 *
 * @param base64
 * @returns {ArrayBuffer}
 */
function base64ToBuffer(base64) {
    let binary_string = window.atob(base64);
    let len = binary_string.length;
    let bytes = new Uint8Array(len);

    for (let i = 0; i < len; i++) {
        bytes[i] = binary_string.charCodeAt(i);
    }

    return bytes.buffer;
}

/**
 *
 * @param {string} str
 * @returns {string}
 */
function toPermalink(str) {
    let re = /[^a-z0-9]+/gi; // global and case insensitive matching of non-char/non-numeric

    let re2 = /^-*|-*$/g; // get rid of any leading/trailing dashes

    str = str.replace(re, '-'); // perform the 1st regexp

    return str.replace(re2, '').toLowerCase();
}

function createAuth(key) {
    return {
        weight_threshold: 1,
        account_auths: [],
        key_auths: [[key, 1]]
    };
}

function copyToClipboard(element) {
    if (element) {

        if (typeof element == 'string') {
            element = document.getElementById(element);
        }

        element.select();

        try {
            document.execCommand('copy');
        } catch (err) {
            console.error();
        }
    }
}

function randomNumber(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 *
 * @param {string|number|Date} date
 * @returns {moment}
 */
function toLocaleDate(date) {
    if (date) {
        return moment.utc(date).local();
    }

    return moment.utc(0).local();
}

/**
 *
 * @param {Date} date
 * @returns {moment}
 */
function toUTCDate(date) {
    let dateString = date.toISOString();
    console.log('Date tot utc', date, dateString);
    return moment(dateString, 'YYYY-MM-DDTHH:mm:ss.SZ');
}

/**
 *
 * @param src
 * @returns {*}
 */
function clone(src) {
    return jsonify(jsonstring(src));
}

function humanFileSize(size) {
    let UNIT = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    let i = Math.floor(Math.log(size) / Math.log(1024));
    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + UNIT[i];
}

/**
 *
 * @param {string} username
 * @returns {boolean}
 */
function isUserFeed() {
    let username = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    let path = currentPage ? currentPage.pathname : window.location.pathname;
    let regexp = '(\/@[a-zA-Z0-9]+\/feed)';

    if (username) {
        username = username.replace('@', '');
        regexp = '(\/@' + username + '\/feed)';
    }

    return new RegExp(regexp).exec(path) !== null;
}

/**
 *
 * @param {string} web
 * @returns {string}
 */
function toUrl(web) {
    if (web) {
        if (!web.startsWith('http://') && !web.startsWith('https://')) {
            web = 'http://' + web;
        }

        return web;
    }

    return null;
}

/**
 *
 * @param {string} path
 * @param {number} index
 * @returns {string}
 */
function getPathPart(path, index) {
    index = index !== undefined ? index : 0;
    path = path || (currentPage ? currentPage.pathname : null) || window.location.pathname;
    let parts = path.split('/');
    parts.splice(0, 1);
    return parts[index] || '';
}

function getNavigatorLanguage() {
    return navigator.language.split('-')[0];
}

/**
 *
 * @param {Array} array
 * @returns {Array}
 */
function cleanArray(array) {
    if (Array.isArray(array)) {
        let elements = [];
        array.forEach(function (el) {
            if (el) {
                elements.push(el);
            }
        });
        return elements;
    }

    return array;
}

/**
 *
 * @returns {boolean}
 */
function isSmallScreen() {
    return window.screen.width < 768;
}

/**
 *
 * @param {string} text
 * @returns {string}
 */
function removeEmojis(text) {
    if (text && typeof text === 'string') {
        return text.replace(/([\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF])/g, '');
    }

    return '';
}

function NaNOr(number, defaultN) {
    if (!isNaN(number)) {
        return number;
    }

    if (defaultN !== undefined && defaultN !== null && !isNaN(defaultN)) {
        return defaultN;
    }

    return NaN;
}

/**
 *
 * @param obj1
 * @param obj2
 * @returns {boolean}
 */
function isEqual(obj1, obj2) {
    if (typeof obj1 !== typeof obj2) {
        return false;
    }

    let jObj1 = jsonstring(obj1);
    let jObj2 = jsonstring(obj2);
    return jObj1 === jObj2;
}

/**
 *
 * @param mixedObj
 * @returns {Array}
 */
function mixToArray(mixedObj) {
    let arr = [];

    for (let x = 0; x < mixedObj.length; x++) {
        arr.push(mixedObj[x]);
    }

    return arr;
}

/**
 *
 * @param {string} tag
 * @returns {string}
 */
function normalizeTag(tag) {
    if (tag && tag.startsWith('#')) {
        return normalizeTag(tag.substring(1, tag.length));
    }

    return tag.toLowerCase();
}

function linkfy(str, target) {
    if (!target) {
        target = '_blank'
    }
    let newStr = str.replace(/(<a href=")?((https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)))(">(.*)<\/a>)?/gi, function () {
        return '<a href="' + arguments[2] + '" target="' + target + '">' + (arguments[7] || arguments[2]) + '</a>';
    });

    return newStr;
}

/**
 *
 * @returns {string}
 */
function uniqueId() {
    return Math.random().toString(36).substr(2, 9)
}

function makeMentions(comment, state) {
    let body = comment.body || comment;

    //console.log(body);
    //First, save links that contains @user;
    let httpUserMatches = body.match(/(https|http):\/\/[\d\w\/-]*.[\d\w\/-]+[@\d\w\/-]+/g);
    let httpLinks = {};
    if (httpUserMatches) {
        httpUserMatches.forEach(function (m) {
            let httpId = uniqueId();
            httpLinks[httpId] = m;
            body = body.replace(m, httpId);
        })
    }

    //console.log(httpMatches, httpLinks)
    //Second, find @users
    let userMatches = body.match(/@[\w\.\d-]+/gm);

    if (userMatches) {
        userMatches = Array.trim(userMatches);
        userMatches.forEach(function (m) {

            let mention = m.replace('@', '').toLowerCase();
            let user = state.accounts[mention];
            user = user ? user.metadata.publicName || user.name : m;
            let link = '<a href="/@' + mention + '">' + user + '</a>';
            //console.log(m, link)
            body = body.replace(m + ' ', link + ' ');
            body = body.replace(' ' + m + ' ', ' ' + link + ' ');
            body = body.replace(' ' + m, ' ' + link);
        });

        //console.log(userMatches);
    }

    //Third, find #tags
    let tagMatches = body.match(/[#][a-zA-Z0-9]{2,}/gm);
    if (tagMatches) {
        tagMatches = Array.trim(tagMatches);
        tagMatches.forEach(function (m) {

            let tag = m.replace('#', '').toLowerCase();
            let link = '<a href="/popular/' + tag + '">#' + tag + '</a>';
            //console.log(m, link)
            body = body.replace(m + ' ', link + ' ');
            body = body.replace(' ' + m + ' ', ' ' + link + ' ');
            body = body.replace(' ' + m, ' ' + link);
        });
    }

    //Fourth, restore links
    let httpKeys = Object.keys(httpLinks);
    httpKeys.forEach(function (k) {
        body = body.replace(k, httpLinks[k])
    });

    return body;
}

export {
    cancelEventPropagation, isJSON, jsonify, jsonstring, validateEmail, getParameterByName, base64ToBuffer, toPermalink,
    createAuth, copyToClipboard, randomNumber, toLocaleDate, toUTCDate, clone, humanFileSize, isUserFeed, toUrl, getPathPart,
    getNavigatorLanguage, cleanArray, isSmallScreen, removeEmojis, NaNOr, isEqual, mixToArray, normalizeTag, linkfy,
    uniqueId, makeMentions
};
