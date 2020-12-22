import {cancelEventPropagation, getParameterByName, isUserFeed, jsonify} from "../lib/util";
import HttpClient from "../lib/http";
import {LICENSE} from "../lib/license";
import * as mutexify from 'mutexify';
import {CommentsApi, TagsApi} from "../lib/creary-api";
import {catchError} from "../common/common";


(function () {
    let oldContentLock = mutexify();
    let navbarFilter;

    function setUp(session, account, isFeed) {
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
                    category: isFeed ? 'feed' : 'popular',
                    discuss: null,
                    license: null,
                    download: null,
                    oldContent: null
                },
                methods: {
                    isUserFeed: isUserFeed,
                    loadContent: loadContent,
                    getParams: function () {
                        return {
                            search: this.discuss,
                            download: this.download,
                            license: this.license
                        }
                    },
                    onSelectCategory: function (event, category) {
                        cancelEventPropagation(event);

                        if (category !== this.category) {
                            this.category = category;
                            this.resetContentFilters();
                        }
                    },
                    onSelectDiscuss: function (event, discuss) {
                        cancelEventPropagation(event);

                        if (discuss !== this.discuss) {
                            this.discuss = discuss;
                            this.resetContentFilters();
                        }
                    },
                    onSelectLicense: function (event, license) {
                        cancelEventPropagation(event);
                        if (license !== this.license) {
                            this.license = license;
                            this.loadContent();
                        }
                    },
                    onSelectDownload: function (event, download) {
                        cancelEventPropagation(event);

                        if (download !== this.download) {
                            this.download = download;
                            this.loadContent();
                        }

                    },
                    resetContentFilters() {
                        this.license = null;
                        this.downloads = null;
                        this.oldContent = null;
                        this.loadContent();
                    }
                }
            });

            setTimeout(function () {
                //mr.sliders.documentReady($);
                $('.button-').on('click', function(){
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
        setUp(session, account, isUserFeed());
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

    function loadContent() {
        console.log('Loading content for', navbarFilter.category, navbarFilter.discuss);
        creaEvents.emit('crea.content.set', navbarFilter.category, navbarFilter.discuss, navbarFilter.getParams());
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

    creaEvents.on('crea.content.old', function () {
        console.log('Received load old content');
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
                if (isUserFeed()) {
                    let following = navbarFilter.account.user.followings;
                    let adult = navbarFilter.account.user.metadata.adult_content === 'hide' ? 0 : 1
                    let discuss = navbarFilter.discuss ? navbarFilter.discuss : null;
                    commentsApi.feed(following, discuss, adult, 20, onResult);
                } else {
                    commentsApi.searchByReward(navbarFilter.discuss, navbarFilter.download, navbarFilter.license, 20, onResult);
                }

            }
        })

    });

    creaEvents.on('crea.content.path', function (category, discuss) {
        navbarFilter.category = category;
        navbarFilter.discuss = discuss;
        navbarFilter.$forceUpdate();
    });

    creaEvents.on('crea.content.load', function () {
        loadContent();
    });
})();
