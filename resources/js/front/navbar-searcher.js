import { cancelEventPropagation, getParameterByName, getPathPart, isUserFeed, jsonify } from '../lib/util';
import HttpClient from '../lib/http';
import Avatar from '../components/Avatar';
import { isInHome } from '../common/common';
import { AccountsApi, TagsApi } from '../lib/creary-api';

(function () {
    const SEARCH_LIMIT = 3;
    const MIN_SEARCH_CHARS = 2;

    Vue.component('avatar', Avatar);

    let navbarSearch;

    function setUp() {
        let search;
        let category = getPathPart();
        if (category === 'search') {
            search = getPathPart(null, 1);
        }

        navbarSearch = new Vue({
            el: '#navbar-search',
            name: 'searcher',
            data: {
                lang: lang,
                search: search,
                limit: SEARCH_LIMIT,
                accounts: {
                    http: null,
                    items: [],
                },
                tags: {
                    http: null,
                    items: [],
                },
            },
            methods: {
                isInHome: isInHome,
                reset: function reset() {
                    this.search = null;
                    this.limit = SEARCH_LIMIT;
                },
                onSelectTag: function (event, tag) {
                    if (this.isInHome()) {
                        cancelEventPropagation(event);
                        this.search = tag.name;
                        creaEvents.emit('crea.content.tag', tag.name);
                    }
                },
                performSearch: function (event) {
                    cancelEventPropagation(event);

                    let that = this;

                    if (this.search && this.search.length >= MIN_SEARCH_CHARS) {
                        console.log('Searching', this.search);

                        //Accounts search
                        if (this.accounts.http) {
                            this.accounts.http.abort();
                        } else {
                            this.accounts.http = new AccountsApi();
                        }

                        this.accounts.http.search(this.search, this.limit, (err, result) => {
                            if (err) {
                                this.accounts.items = [];
                            } else {
                                this.accounts.items = result.data;
                            }

                            this.$forceUpdate();
                        });

                        //Tags search
                        if (this.tags.http) {
                            this.tags.http.abort();
                        } else {
                            this.tags.http = new TagsApi();
                        }

                        this.tags.http.search(this.search, this.limit, (err, result) => {
                            if (err) {
                                this.tags.items = [];
                            } else {
                                this.tags.items = result.data;
                            }

                            this.$forceUpdate();
                        });
                    } else {
                        let cleanSection = function (section) {
                            section.http = null;
                            section.items = [];
                        };

                        cleanSection(this.accounts);
                        cleanSection(this.tags);

                        this.$forceUpdate();
                    }
                },
            },
        });
    }

    creaEvents.on('crea.modal.ready', function () {
        console.log('Setting up searcher!');
        setUp();
    });

    creaEvents.on('crea.search.update', function (search) {
        navbarSearch.search = null;
        navbarSearch.$forceUpdate();
    });
})();
