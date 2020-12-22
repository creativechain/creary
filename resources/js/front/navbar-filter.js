import {cancelEventPropagation, getParameterByName, isUserFeed, jsonify} from "../lib/util";
import HttpClient from "../lib/http";
import {LICENSE} from "../lib/license";
import * as mutexify from 'mutexify';
import {CommentsApi, TagsApi} from "../lib/creary-api";
import {catchError} from "../common/common";


(function () {
    let oldContentLock = mutexify();
    let navbarFilter;

    function setUp(session, account, hasSearch) {
        if (!navbarFilter) {
            navbarFilter = new Vue({
                el: '#navbar-filter',
                name: 'navbar-filter',
                data: {
                    lang: lang,
                    session: session,
                    account: account,
                    availableLicenses: [LICENSE.NON_PERMISSION, LICENSE.CREATIVE_COMMONS, LICENSE.FREE_CONTENT],
                    discussions: [],
                    category: hasSearch ? 'popular' : 'feed',
                    discuss: null,
                    license: null,
                    download: null,
                    oldContent: null
                },
                methods: {
                    isUserFeed: isUserFeed,
                    getParams: function () {
                        return {
                            search: this.discuss,
                            download: this.download,
                            license: this.license
                        }
                    },
                    onSelectCategory: function (event, category) {
                        cancelEventPropagation(event);

                        this.category = category;
                        this.loadContent();
                    },
                    onSelectDiscuss: function (event, discuss) {
                        cancelEventPropagation(event);

                        this.discuss = discuss;
                        this.loadContent();
                    },
                    onSelectLicense: function (event, license) {
                        cancelEventPropagation(event);
                        this.license = license;
                        this.loadContent();
                    },
                    onSelectDownload: function (event, download) {
                        cancelEventPropagation(event);
                        this.download = download;
                        this.loadContent();
                    },
                    resetContentFilters() {
                        this.license = null;
                        this.downloads = null;
                        this.loadContent();
                    },
                    loadContent() {
                        console.log('Loading content for', this.category, this.discuss);
                        creaEvents.emit('crea.content.set', this.category, this.discuss, this.getParams());
                    }
                }
            });

            setTimeout(function () {
                //mr.sliders.documentReady($);
                $('.button-º').on('click', function(){
                    $('.row-filter-select').fadeToggle('show');
                });
            }, 100)
        } else {
            navbarFilter.session = session;
            navbarFilter.account = account;
            navbarFilter.$forceUpdate();
        }
    }

    function preSetup(session, account) {
        let hasSearch = getParameterByName('search');
        setUp(session, account, hasSearch);
        fetchUsedTags();
    }

    function fetchUsedTags() {
        let tagsApi = new TagsApi();
        tagsApi.index(20, function (err, result) {
            if (!catchError(err)) {
                navbarFilter.discussions = result.data;
                navbarFilter.$forceUpdate();
                setTimeout(loadSlider, 100);
            } else {
                navbarFilter.discussions = [];
                navbarFilter.$forceUpdate();
            }
        });
    }

    function loadSlider() {
        mr.sliders.documentReady($);
    }

    creaEvents.on('crea.session.login', function (s, a) {
        preSetup(s, a);
    });

    creaEvents.on('crea.session.logout', function (s, a) {
        preSetup(false, false);
    });

    creaEvents.on('crea.session.update', function (session, account) {
        setUp(session, account);
    });

    creaEvents.on('crea.filter.set', function (category, discuss) {
        console.log('Filter set received!', category, discuss);
        navbarFilter.category = discuss === 'feed' ? discuss : category;
        navbarFilter.discuss = discuss === 'feed' ? null : discuss;
        navbarFilter.$forceUpdate();
    })

    creaEvents.on('crea.content.old', function () {
        oldContentLock(function (release) {
            let hasPrevQuery = navbarFilter.oldContent && navbarFilter.oldContent.next_page_url;
            let commentsApi = new CommentsApi();

            let onResult = function (err, result) {
                if (!catchError(err)) {
                    navbarFilter.oldContent = result;
                    release();

                    creaEvents.emit('crea.content.add', result.data);
                } else {
                    release();
                }

            };

            if (hasPrevQuery) {
                commentsApi.get(navbarFilter.oldContent.next_page_url, onResult);
            } else {
                let following = []
                if (isUserFeed()) {
                    commentsApi.feed(following, navbarFilter.discuss, navbarFilter.account.usage.metadata.adult_content, 20, onResult);
                } else {
                    commentsApi.searchByReward(navbarFilter.discuss, navbarFilter.download, navbarFilter.license, 20, onResult);
                }

            }
        })

    })
})();
