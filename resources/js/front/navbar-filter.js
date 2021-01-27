import { cancelEventPropagation, getParameterByName, getPathPart, isUserFeed } from '../lib/util';
import { LICENSE } from '../lib/license';
import * as mutexify from 'mutexify';
import { CommentsApi, TagsApi } from '../lib/creary-api';
import { catchError, parsePost, updateUrl } from '../common/common';
import { categorySlider } from './category-slider';

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
                    search: getParameterByName('q'),
                    discuss: null,
                    license: null,
                    download: null,
                    oldApiCall: null,
                    needResetContent: false,
                },
                methods: {
                    isUserFeed: isUserFeed,
                    loadContent: loadContent,
                    closeFilters: closeFilters,
                    closeCategoryDropdown: closeCategoryDropdown,
                    enableFilter: function () {
                        return this.isUserFeed() || this.category === 'search';
                    },
                    getFilterText: function () {
                        //discuss === 'feed' ? lang.FILTER.FEED : lang.FILTER[category.toUpperCase()]
                        if (this.isUserFeed() || this.discuss === 'feed') {
                            return this.lang.FILTER.FEED;
                        } else if (this.category === 'search') {
                            return String.format(this.lang.FILTER.SEARCH, '"' + this.search + '"');
                        } else {
                            return this.lang.FILTER[this.category.toUpperCase()];
                        }
                    },
                    linkForTag: function (tag) {
                        let link = '';
                        if (tag) {
                            if (this.isUserFeed()) {
                                link += `${window.location.pathname}?q=`;
                            } else if (this.category === 'search') {
                                link += '/search?q=';
                            } else if (['popular', 'now', 'promoted', 'skyrockets'].includes(this.category)) {
                                link += `/${this.category}/`;
                            } else {
                                link += '/popular/';
                            }

                            link += tag.name;
                        } else {
                            //All
                            if (this.isUserFeed()) {
                                link += window.location.pathname;
                            } else if (this.category === 'search') {
                                link += '/popular';
                            } else {
                                link += `/${this.category}`;
                            }
                        }

                        return link;
                    },
                    getParams: function () {
                        return {
                            search: this.isUserFeed() || this.category === 'search' ? this.search : this.discuss,
                            download: this.download,
                            license: this.license,
                        };
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
                                this.search = null;
                                this.closeFilters();
                            }

                            this.resetContentFilters();
                        }

                        this.closeCategoryDropdown();
                    },
                    onSelectDiscuss: function (event, discuss) {
                        cancelEventPropagation(event);

                        if ((this.isUserFeed() || this.category === 'search') && discuss !== this.search) {
                            this.search = discuss;
                            this.discuss = this.isUserFeed() ? this.discuss : null;
                            this.resetContentFilters();
                        } else if (discuss !== this.discuss) {
                            this.discuss = discuss;
                            this.search = null;
                            this.resetContentFilters();
                        }
                    },
                    onSelectLicense: function (event) {
                        //cancelEventPropagation(event);
                        //console.log('selecting license', event);

                        //this.license = license;
                        this.oldApiCall = null;
                        this.needResetContent = true;
                        this.loadContent();
                    },
                    onSelectDownload: function (event) {
                        //cancelEventPropagation(event);
                        //console.log('Download selected', event);

                        this.oldApiCall = null;
                        this.needResetContent = true;
                        this.loadContent();
                    },
                    resetContentFilters() {
                        this.license = null;
                        this.download = null;
                        this.oldApiCall = null;
                        this.needResetContent = true;
                        this.loadContent();
                    },
                },
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
        tagsApi.active(20, function (err, result) {
            if (!catchError(err)) {
                navbarFilter.discussions = result.data;
            } else {
                navbarFilter.discussions = [];
            }

            loadCategorySlider();
        });
    }

    function loadContent() {
        let currentPath = window.location.pathname;
        let postMatch = currentPath.match(/^([\w\d\-\/]+)\/(@[\w\.\d-]+)\/([\w\d-]+)$/);
        console.log('PostMatch', postMatch);
        if (postMatch) {
            return;
        }

        navbarFilter.$forceUpdate();

        console.log('Loading content for', navbarFilter.category, navbarFilter.discuss);

        let category = navbarFilter.category;
        let discuss = navbarFilter.discuss;
        let params = navbarFilter.getParams();

        if (discuss === 'feed') {
            let urlFilter = `/${category}/feed`;
            if (params.search && params.search !== 'feed') {
                urlFilter += `?q=${params.search}`;
            }

            retrieveContent(urlFilter, params);
        } else if (category === 'search') {
            let urlFilter = `/search?q=${params.search}`;

            retrieveContent(urlFilter, params);
        } else if (discuss) {
            retrieveContent(`/${category}/${discuss}`, params);
        } else {
            retrieveContent(`/${category}`, params);
        }

        if (category !== 'search') {
            creaEvents.emit('crea.search.update', null);
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

                    let accountNames = [];
                    let discussion_idx = [];

                    result.data.forEach((c) => {
                        if (!accountNames.includes(c.author)) {
                            accountNames.push(c.author);
                        }

                        discussion_idx.push(`${c.author}/${c.permlink}`);
                    });

                    creaEvents.emit('crea.content.add', result.data, accountNames, discussion_idx, cleanContent);
                }

                release();
            };

            if (hasPrevQuery) {
                navbarFilter.needResetContent = false;
                commentsApi.get(navbarFilter.oldApiCall.next_page_url, onResult);
                if (navbarFilter.category === 'search') {
                    creaEvents.emit('crea.search.start', 'search', navbarFilter.search, hasPrevQuery);
                }
            } else {
                let adult = navbarFilter.account && navbarFilter.account.user.metadata.adult_content === 'hide' ? 0 : 1;
                let search = navbarFilter.search;
                let download = navbarFilter.download;
                let license = navbarFilter.license ? navbarFilter.license.flag : null;

                if (isUserFeed()) {
                    let following = navbarFilter.account.user.followings;
                    if (following.length > 0) {
                        commentsApi.feed(search, adult, download, license, 20, onResult);
                    } else {
                        onResult(null, { data: [] });
                    }
                } else {
                    commentsApi.searchByReward(search, download, license, 20, onResult);
                    creaEvents.emit('crea.search.start', 'search', navbarFilter.search, hasPrevQuery);
                }
            }
        });
    }

    function retrieveContent(urlFilter, params) {
        /*if (isInHome()) {
                cancelEventPropagation(event);
            }*/

        updateUrl(urlFilter);
        let category = getPathPart(urlFilter);
        let discuss = getPathPart(urlFilter, 1);
        console.log('retrieveContent', category, discuss, urlFilter);

        crea.api.getState(urlFilter, function (err, urlState) {
            if (!catchError(err)) {
                if (isUserFeed()) {
                    let noFeedContent = function noFeedContent() {
                        //User not follows anything, load empty content
                        urlState.content = {};
                        creaEvents.emit(
                            'crea.posts',
                            urlFilter,
                            urlFilter,
                            urlState,
                            navbarFilter.hasContent(),
                            navbarFilter.needResetContent
                        );
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
                                        creaEvents.emit(
                                            'crea.posts',
                                            urlFilter,
                                            urlFilter,
                                            urlState,
                                            navbarFilter.hasContent(),
                                            navbarFilter.needResetContent
                                        );
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
                    };

                    let commentsApi = new CommentsApi();
                    if (navbarFilter.oldApiCall) {
                        console.log('Calling feed next page', navbarFilter.oldApiCall.next_page_url);
                        if (navbarFilter.oldApiCall.next_page_url) {
                            navbarFilter.needResetContent = false;
                            commentsApi.get(navbarFilter.oldApiCall.next_page_url, onFeedComments);
                        } else {
                            noFeedContent();
                        }
                    } else {
                        let followings = navbarFilter.account.user.followings;
                        let adult = params.adult;
                        let search = params.search;
                        let download = params.download;
                        let license = params.license ? params.license.flag : null;

                        if (followings.length > 0) {
                            commentsApi.feed(search, adult, download, license, 20, onFeedComments);
                        } else {
                            noFeedContent();
                        }
                    }
                } else if (category === 'search') {
                    loadOldContent(true);
                } else {
                    //Check if state has content. If not has content, search in creary api

                    let hasContent = urlState.discussion_idx[discuss][category].length > 0;
                    if (hasContent) {
                        creaEvents.emit(
                            'crea.posts',
                            urlFilter,
                            urlFilter,
                            urlState,
                            true,
                            navbarFilter.needResetContent
                        );
                    } else {
                        //Load old content from creary api
                        loadOldContent(true);
                    }
                }
            }
        });
    }

    function loadCategorySlider() {
        setTimeout(function () {
            console.log('Loading category slider');
            mr.sliders.documentReady($);
            navbarFilter.discussReady = true;

            categorySlider().init();
        }, 1e3);
    }

    function loadButtonFilterToggle() {
        $('.button-filter').on('click', function () {
            $('.row-filter-select').fadeToggle('show');
        });
    }

    function closeCategoryDropdown() {
        setTimeout(() => {
            $('#category-select').removeClass('dropdown--active');
            console.log('Closing dropdown');
        }, 100);
    }

    function closeFilters() {
        let filters = $('.row-filter-select');
        if (filters.css('display') !== 'none') {
            filters.fadeToggle('show');
        }
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
        navbarFilter.category = 'search';
        navbarFilter.discuss = null;
        navbarFilter.search = tag;
        navbarFilter.resetContentFilters();
    });

    creaEvents.on('crea.content.load', function () {
        loadContent();
    });

    creaEvents.on('crea.dom.ready', function () {
        let category = getPathPart();
        if (category === 'search' || isUserFeed()) {
            loadButtonFilterToggle();
        }
    });
})();
