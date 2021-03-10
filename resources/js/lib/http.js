/**
 * Created by ander on 11/10/18.
 */

import * as EventEmitter from 'events';
import { default as axios } from 'axios';
import { clean, randomNumber } from './util';

class HttpClient extends EventEmitter {
    constructor(url) {
        super();
        this.id = randomNumber(0, Number.MAX_SAFE_INTEGER);
        this.url = url;
        this.params = null;
        this.method = null;
        this.headers = {};
        this.mimeType = 'multipart/form-data';
        this.contentType = false;
        this.requestToken = null;
        this.credentials = true;
    }

    __exec() {
        let that = this;
        this.requestToken = axios.CancelToken.source();

        let settings = {
            url: this.url,
            method: this.method,
            headers: this.headers,
            withCredentials: this.credentials,
        };

        //console.log('AXIOS SETTINGS', settings);

        if (this.params) {
            if (this.method === 'GET') {
                let query = new URLSearchParams(this.params).toString();
                settings.url += `?${query}`;
            } else {
                settings.data = this.params;
            }
        }

        axios(settings)
            .then((response) => {
                this.emit('always' + this.id, response.data, response.statusText, response.request);
                if (response.status >= 200 && response.status <= 299) {
                    this.emit('done' + this.id, response.data, response.statusText, response.request);
                } else {
                    this.emit('fail' + this.id, response.data, response.statusText, response.request);
                }
            })
            .catch((error) => {
                //console.error('Axios error', error, error.response);
                //console.error(error);
                that.emit('fail' + that.id, error.response.data, error.response.statusText, error.response.request);
            });
    }

    /**
     *
     * @param {string} event
     * @param {function} callback
     * @returns {HttpClient}
     */
    when(event, callback) {
        this.on(event + this.id, callback);
        return this;
    }

    /**
     *
     * @param headers
     * @returns {HttpClient}
     */
    setHeaders(headers) {
        this.headers = headers;
        return this;
    }

    /**
     *
     * @param credentials
     * @returns {HttpClient}
     */
    withCredentials(credentials = true) {
        this.credentials = credentials;
        return this;
    }

    /**
     *
     * @param params
     * @returns {HttpClient}
     */
    post(params) {
        this.params = clean(params);
        this.method = 'POST';
        this.__exec();

        return this;
    }

    /**
     *
     * @param params
     * @returns {HttpClient}
     */
    get(params) {
        this.params = clean(params);
        this.method = 'GET';
        this.__exec();

        return this;
    }

    abort() {
        if (this.requestToken) {
            this.requestToken.cancel();
        }
    }
}

export default HttpClient;
