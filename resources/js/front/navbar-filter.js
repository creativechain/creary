import {cancelEventPropagation, getPathPart, isUserFeed} from "../lib/util";
import {LICENSE} from "../lib/license";
import * as mutexify from 'mutexify';
import {CommentsApi, TagsApi} from "../lib/creary-api";
import {catchError, parsePost, updateUrl} from "../common/common";


(function () {
    let oldApiCallLock = mutexify();
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
                    oldApiCall: null
                },
                methods: {
                    isUserFeed: isUserFeed,
                    loadContent: loadContent,
                    getParams: function () {
                        return {
                            search: this.isUserFeed() ? null : this.discuss,
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
                            this.oldApiCall = null;
                            this.loadContent();
                        }
                    },
                    onSelectDownload: function (event, download) {
                        cancelEventPropagation(event);

                        if (download !== this.download) {
                            this.download = download;
                            this.oldApiCall = null;
                            this.loadContent();
                        }

                    },
                    resetContentFilters() {
                        this.license = null;
                        this.downloads = null;
                        this.oldApiCall = null;
                        this.loadContent();
                    }
                }
            });

            setTimeout(function () {
                //mr.sliders.documentReady($);
                $('.button-filter').on('click', function(){
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
        navbarFilter.$forceUpdate();

        console.log('Loading content for', navbarFilter.category, navbarFilter.discuss);

        let category = navbarFilter.category;
        let discuss = navbarFilter.discuss;
        let params = navbarFilter.getParams();

        if (category === 'feed') {
            let user = getPathPart();
            retrieveContent(`/${user}/feed`, params);
        } else if (discuss) {
            retrieveContent(`/${category}/${discuss}`, params);
        } else {
            retrieveContent(`/${category}`, params);
        }
    }

    function retrieveContent(urlFilter, params) {
        /*if (isInHome()) {
            cancelEventPropagation(event);
        }*/

        updateUrl(urlFilter);

        crea.api.getState(urlFilter, function (err, urlState) {
            if (!catchError(err)) {
                if (isUserFeed()) {
                    let noFeedContent = function noFeedContent() {
                        //User not follows anything, load empty content
                        urlState.content = {};
                        creaEvents.emit('crea.posts', urlFilter, urlFilter, urlState);
                    };

                    let onFeedComments = function (err, result) {
                        navbarFilter.oldApiCall = result;

                        if (!catchError(err)) {
                            if (result.data.length) {
                                let count = result.data.length;

                                let onContentFetched = function onContentFetched() {
                                    count--;
                                    //console.log('Content fetched. Remaining', count);
                                    if (count <= 0) {
                                        //creaEvents.emit('crea.posts', urlFilter, urlFilter, urlState);
                                        creaEvents.emit('crea.posts', urlFilter, urlFilter, urlState);
                                    }
                                };

                                urlState.content = {};
                                result.data.forEach(function (d) {
                                    let permlink = d.author + '/' + d.permlink;

                                    if (!urlState.content[permlink]) {
                                        crea.api.getContent(d.author, d.permlink, function (err, result) {
                                            if (err) {
                                                console.error('Error getting', permlink, err);
                                            } else {
                                                urlState.content[permlink] = parsePost(result, d.reblogged_by);
                                            }

                                            onContentFetched();
                                        });
                                    }
                                });
                            } else {
                                noFeedContent();
                            }
                        }
                    }
                    let commentsApi = new CommentsApi();
                    if (navbarFilter.oldApiCall) {
                        console.log('Calling feed next page', navbarFilter.oldApiCall.next_page_url)
                        if (navbarFilter.oldApiCall.next_page_url) {
                            commentsApi.get(navbarFilter.oldApiCall.next_page_url, onFeedComments);
                        }
                    } else {
                        commentsApi.feed(navbarFilter.account.user.followings, params.search, params.adult, params.download, params.license, 20, onFeedComments);
                    }

                } else {
                    creaEvents.emit('crea.posts', urlFilter, urlFilter, urlState);
                }
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

    creaEvents.on('crea.content.old', function () {
        console.log('Received load old content');
        oldApiCallLock(function (release) {
            let hasPrevQuery = navbarFilter.oldApiCall && navbarFilter.oldApiCall.next_page_url;
            let commentsApi = new CommentsApi();

            let onResult = function (err, result) {
                if (!catchError(err)) {
                    navbarFilter.oldApiCall = result;
                    release();

                    creaEvents.emit('crea.content.add', result.data);
                } else {
                    release();
                }

            };

            if (hasPrevQuery) {
                commentsApi.get(navbarFilter.oldApiCall.next_page_url, onResult);
            } else {
                if (isUserFeed()) {
                    let following = navbarFilter.account.user.followings;
                    let adult = navbarFilter.account.user.metadata.adult_content === 'hide' ? 0 : 1
                    let discuss = navbarFilter.discuss ? navbarFilter.discuss : null;
                    let download = navbarFilter.download;
                    let license = navbarFilter.license;
                    commentsApi.feed(following, discuss, adult, download, license, 20, onResult);
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
