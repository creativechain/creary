import {EventEmitter} from "events";
import HttpClient from "./http";
import {jsonify} from "./util";

const OPTIONS = {
    base_url: apiOptions.apiCrea
}

class CrearyApi extends EventEmitter {
    constructor(api, options = OPTIONS) {
        super();
        this._api = api;
        this._options = options;

    }

    /**
     *
     * @private
     */
    __initializeClient(endpoint, callback, buildUrl = true) {
        let url = buildUrl ? this._options.base_url + '/' + this._api +  endpoint : endpoint;
        this._http = new HttpClient(url);
        this._http.setHeaders({
            Accept: 'application/json'
        });

        this.__setCallback(callback);
    }

    /**
     *
     * @param callback
     * @private
     */
    __setCallback(callback) {
        this._http.when('done', function (response) {
            try {
                let data = jsonify(response);
                if (callback) {
                    callback(null, data);
                }
            } catch (e) {
                if (callback) {
                    callback(e);
                }
            }
        });

        this._http.when('fail', function (jqXHR, textStatus, errorThrown) {
            if (callback) {
                callback(textStatus);
            }
        });
    }

    /**
     *
     * @param params
     * @private
     */
    __get(params = {}) {
        this._http.get(params);
        //console.log('Caller', arguments.callee.caller.name);
    }

    /**
     *
     * @param endpoint
     * @param params
     * @private
     */
    __post(endpoint, params = {}) {
        this.__initializeClient(endpoint);
        this._http.post(params);
    }

    /**
     *
     * @param url
     * @param callback
     */
    get(url, callback) {
        this.__initializeClient(url, false, false);
        this.__setCallback(callback);
        this._http.get({});
    }

    /**
     *
     * @param url
     * @param callback
     */
    post(url, callback) {
        this.__initializeClient(url, false, false);
        this.__setCallback(callback);
        this._http.post({});
    }
}

class CommentsApi extends CrearyApi {
    constructor(options = OPTIONS) {
        super('comments', options);
    }

    /**
     *
     * @param following
     * @param search
     * @param adult
     * @param download
     * @param license
     * @param limit
     * @param callback
     */
    feed(following, search = null, adult = null, download = null, license = null, limit = 20, callback) {
        this.__initializeClient('/feed', callback);
        this.__get({ following, search, adult, download, license, limit });
    }

    /**
     *
     * @param search
     * @param download
     * @param license
     * @param limit
     * @param callback
     */
    searchByReward(search, download = null, license = null, limit = 20, callback) {
        this.__initializeClient('/searchByReward', callback);
        this.__get({ search, download, license, limit });
    }

    /**
     *
     * @param author
     * @param permlink
     * @param callback
     */
    comment(author, permlink, callback) {
        this.__initializeClient(`/${author}/${permlink}`, callback);
        this.__get()
    }
}

class TagsApi extends CrearyApi {
    constructor(options = OPTIONS) {
        super('tags', options);
    }

    /**
     *
     * @param limit
     * @param callback
     */
    index(limit = 20, callback) {
        this.__initializeClient('/', callback);
        this.__get({ limit });
    }

    /**
     *
     * @param search
     * @param limit
     * @param callback
     */
    search(search, limit = 20, callback) {
        this.__initializeClient('/search', callback);
        this.__get({ search, limit });
    }
}

export {
    CrearyApi, CommentsApi, TagsApi
}
