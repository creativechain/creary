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
                    discussReady: false,
                    category: isFeed ? 'feed' : 'popular',
                    discuss: null,
                    license: null,
                    download: null,
                    oldApiCall: null,
                    needResetContent: false
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
                    hasContent: function () {
                        return this.oldApiCall && this.oldApiCall.total > 0;
                    },
                    onSelectCategory: function (event, category) {
                        cancelEventPropagation(event);

                        if (category !== this.category) {
                            if (category === 'feed') {
                                this.category = '@' + this.account.user.name;
                                this.discuss = 'feed';
                            } else {
                                this.category = category;
                                this.discuss = null;
                            }

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
                        console.log('selecting license', license, this.license);
                        cancelEventPropagation(event);
                        if (license !== this.license) {
                            this.license = license;
                            this.oldApiCall = null;
                            this.needResetContent = true;
                            this.loadContent();
                        }
                    },
                    onSelectDownload: function (event, download) {
                        cancelEventPropagation(event);

                        if (download !== this.download) {
                            this.download = download;
                            this.oldApiCall = null;
                            this.needResetContent = true;
                            this.loadContent();
                        }

                    },
                    resetContentFilters() {
                        this.license = null;
                        this.downloads = null;
                        this.oldApiCall = null;
                        this.needResetContent = true;
                        this.loadContent();
                    }
                }
            });
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

            } else {
                navbarFilter.discussions = [];

            }

            loadSlider();
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

    function loadOldContent(cleanContent = false) {
        console.log('Received load old content');
        oldApiCallLock(function (release) {
            let hasPrevQuery = navbarFilter.oldApiCall && navbarFilter.oldApiCall.next_page_url;
            let commentsApi = new CommentsApi();

            let onResult = function (err, result) {
                if (!catchError(err)) {
                    navbarFilter.oldApiCall = result;
                    release();

                    creaEvents.emit('crea.content.add', result.data, cleanContent);
                } else {
                    release();
                }
            };

            if (hasPrevQuery) {
                navbarFilter.needResetContent = false;
                commentsApi.get(navbarFilter.oldApiCall.next_page_url, onResult);
            } else {
                let adult = navbarFilter.account && navbarFilter.account.user.metadata.adult_content === 'hide' ? 0 : 1
                let discuss = navbarFilter.discuss ? navbarFilter.discuss : null;
                let download = navbarFilter.download;
                let license = navbarFilter.license ? navbarFilter.license.flag : null;

                if (isUserFeed()) {
                    let following = navbarFilter.account.user.followings;
                    commentsApi.feed(following, discuss, adult, download, license, 20, onResult);
                } else {
                    commentsApi.searchByReward(discuss, download, license, 20, onResult);
                }
            }
        });
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
                        creaEvents.emit('crea.posts', urlFilter, urlFilter, urlState, false, navbarFilter.needResetContent);
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
                                        creaEvents.emit('crea.posts', urlFilter, urlFilter, urlState, navbarFilter.hasContent(), navbarFilter.needResetContent);
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
                            navbarFilter.needResetContent = false;
                            commentsApi.get(navbarFilter.oldApiCall.next_page_url, onFeedComments);
                        }
                    } else {
                        let followings = navbarFilter.account.user.followings;
                        let adult = params.adult;
                        //If is user feed, no search ocnfigured
                        let search = params.search === 'feed' ? null : params.search;
                        let download = params.download;
                        let license = params.license ?  params.license.flag : null;

                        commentsApi.feed(followings, search, adult, download, license, 20, onFeedComments);
                    }

                } else {
                    //Check if state has content. If not has content, search in creary api
                    let category = getPathPart(urlFilter);
                    let discuss = getPathPart(urlFilter, 1);

                    let hasContent = urlState.discussion_idx[discuss][category].length > 0;
                    if (hasContent) {
                        creaEvents.emit('crea.posts', urlFilter, urlFilter, urlState, true, navbarFilter.needResetContent);
                    } else {
                        //Load old content from creary api
                        loadOldContent(true);
                    }
                }
            }
        });
    }

    function loadSlider() {
        setTimeout(function () {
            console.log('loading slider');
            mr.sliders.documentReady($);
            navbarFilter.discussReady = true;
        }, 1e3);
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
        loadOldContent();
    });

    creaEvents.on('crea.content.path', function (category, discuss) {
        navbarFilter.category = category;
        navbarFilter.discuss = discuss;
        navbarFilter.$forceUpdate();
    });

    creaEvents.on('crea.content.tag', function (tag) {
        navbarFilter.category = 'popular';
        navbarFilter.discuss = tag;
        navbarFilter.resetContentFilters();
    });

    creaEvents.on('crea.content.load', function () {
        loadContent();
    });

    creaEvents.on('crea.dom.ready', function () {
        $('.button-filter').on('click', function(){
            $('.row-filter-select').fadeToggle('show');
        });
    })
})();
