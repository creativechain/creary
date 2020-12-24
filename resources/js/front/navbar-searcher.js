import {cancelEventPropagation, getParameterByName, jsonify} from "../lib/util";
import HttpClient from "../lib/http";
import Avatar from "../components/Avatar";

(function () {

    const SEARCH_LIMIT = 3;
    const MIN_SEARCH_CHARS = 3;

    Vue.component('avatar', Avatar);

    let navbarSearch;

    function setUp() {
        navbarSearch = new Vue({
            el: '#navbar-search',
            name: 'searcher',
            data: {
                lang: lang,
                search: getParameterByName('query'),
                limit: SEARCH_LIMIT,
                accounts: {
                    http: null,
                    items: []
                },
                tags: {
                    http: null,
                    items: []
                },
            },
            methods: {
                reset: function reset() {
                    this.search = null;
                    this.limit = SEARCH_LIMIT;
                },
                onSelectTag: function (event, tag) {
                    cancelEventPropagation(event);
                    this.search = tag.name;
                    creaEvents.emit('crea.content.tag', tag.name);
                },
                performSearch: function (event) {
                    cancelEventPropagation(event);

                    let that = this;

                    if (this.search && this.search.length >= MIN_SEARCH_CHARS) {
                        console.log('Searching', this.search)
                        let search = function (endpoint, section) {
                            if (section.http) {
                                section.http.abort();
                                console.log(that.search, `aborted ${endpoint}`);
                            }

                            section.http = new HttpClient(apiOptions.apiCrea + endpoint);
                            section.http.setHeaders({
                                accept: 'application/json'
                            })

                            section.http.on('done' + section.http.id, function (response) {
                                let data = jsonify(response);
                                console.log(`On response ${endpoint}`, data);

                                section.items = data.data;
                                section.http = null;
                                that.$forceUpdate();
                            })

                            section.http.when('fail', function (jqXHR, textStatus, errorThrown) {
                                //console.error(jqXHR, textStatus, errorThrown);
                                section.items = [];
                                section.http = null;
                                that.$forceUpdate();
                            });

                            section.http.get({
                                search: that.search,
                                limit: that.limit
                            });
                        }

                        search('/accounts/search', this.accounts);
                        search('/tags/search', this.tags);
                    } else {
                        let cleanSection = function (section) {
                            section.http = null;
                            section.items = [];
                        }

                        cleanSection(this.accounts);
                        cleanSection(this.tags);

                        this.$forceUpdate();
                    }
                }
            }
        });
    }

    creaEvents.on('crea.modal.ready', function () {
        console.log('Setting up searcher!');
        setUp();
    });
})()
