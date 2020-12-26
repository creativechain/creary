/**
 * Created by ander on 9/10/18.
 */
import R from '../lib/resources';
import {Asset} from '../lib/amount';
import {License} from '../lib/license';
import {
    cancelEventPropagation,
    getParameterByName,
    getPathPart,
    isUserFeed,
    jsonify,
    NaNOr,
    toLocaleDate
} from '../lib/util';
import {
    catchError,
    getAccounts,
    getDiscussion,
    parseAccount,
    parsePost,
    removeBlockedContents,
    showModal,
    showProfile,
    updateUrl,
    updateUserSession
} from "../common/common";

//Components import
import Avatar from "../components/Avatar";
import Recommend from "../components/Recommend";
import NewLike from "../components/NewLike";
import LinkName from "../components/LinkName";
import ButtonFollow from "../components/ButtonFollow";
import {CommentsApi} from "../lib/creary-api";

(function () {
    //Load Vue components
    Vue.component('avatar', Avatar);
    Vue.component('recommend', Recommend);
    Vue.component('new-like', NewLike);
    Vue.component('linkname', LinkName);
    Vue.component('btn-follow', ButtonFollow);

    let homePosts;
    let lastPage;
    let session, account;

    /**
     *
     * @param {string} urlFilter
     * @param {string} filter
     * @param state
     * @returns {License}
     */
    function showPosts(urlFilter, filter, state) {
        console.log(urlFilter, filter, state);
        let content = state.content;
        let accounts = state.accounts;
        let cKeys = Object.keys(content);
        let newKeys = [];

        for (let x = 0; x < cKeys.length; x++) {
            let k = cKeys[x];

            if (!content[k].parent_author) {
                content[k].metadata = jsonify(content[k].json_metadata);
                newKeys.push(k);
            }
        }

        state.content = content;
        let aKeys = Object.keys(accounts);
        aKeys.forEach(function (k) {
            accounts[k] = parseAccount(accounts[k]);
        });
        state.accounts = accounts; //Normalize filter

        if (filter.startsWith('/')) {
            filter = filter.substring(1);
        }

        let category = getPathPart(urlFilter);
        let discuss = getPathPart(urlFilter, 1) || '';

        if (isUserFeed(getPathPart()) && !state.discussion_idx[discuss]) {
            cKeys = newKeys;
            cKeys.sort(function (k1, k2) {
                let d1 = toLocaleDate(content[k1].created);
                let d2 = toLocaleDate(content[k2].created);
                return d2.valueOf() - d1.valueOf();
            });
            state.discussion_idx[discuss] = {};
            state.discussion_idx[discuss][category] = cKeys;
            lastPage = lastPage ? lastPage : 1;
        } else if (window.location.pathname === '/search') {
            lastPage = getParameterByName('page') || 1;
        } else {
            let contentArray = state.discussion_idx[discuss][category];
            lastPage = state.content[contentArray[contentArray.length - 1]];
        }

        state.discussion_idx[discuss][category] = removeBlockedContents(state, account, state.discussion_idx[discuss][category]);

        if (!homePosts) {
            homePosts = new Vue({
                el: '#home-posts',
                name: 'home-posts',
                data: {
                    session: session,
                    account: account,
                    filter: filter,
                    category: category,
                    discuss: discuss,
                    urlFilter: urlFilter,
                    state: state,
                    cleaningContent: false,
                    search: getParameterByName('query'),
                    simpleView: false,
                    lang: lang
                },
                updated: function updated() {
                    if (mr.masonry) {
                        mr.masonry.windowLoad();
                        mr.masonry.updateLayout();
                    }
                },
                methods: {
                    isFeed: function () {
                        return isUserFeed();
                    },
                    getDefaultAvatar: R.getAvatar,
                    toggleSimpleView: function () {
                        this.simpleView = !this.simpleView;
                        console.log('SimpleView', this.simpleView)
                    },
                    onFollow: function onFollow(err, result) {
                        //creaEvents.emit('crea.content.filter', this.urlFilter);
                        updateUserSession();
                    },
                    openPost: function (post, event) {
                        cancelEventPropagation(event);
                        creaEvents.emit('navigation.post.data', post, this.state, this.discuss, this.category);
                        showModal('#modal-post');
                    },
                    parseAsset: function parseAsset(asset) {
                        return Asset.parse(asset).toFriendlyString();
                    },
                    getBuzzClass: function getBuzzClass(account) {
                        let buzzClass = {};
                        let levelName = account.buzz.level_name;

                        buzzClass[levelName] = true;
                        return buzzClass;
                    },
                    getFeaturedImage: function getFeaturedImage(post) {
                        let featuredImage;
                        if (!this.account && post.adult_content || this.account && this.account.user.metadata.adult_content === 'warn' && post.adult_content) {
                            featuredImage = {
                                url: R.IMG.POST.NSFW
                            }
                        } else {
                            featuredImage = post.metadata.featuredImage;
                        }

                        if (featuredImage && featuredImage.hash) {
                            return {
                                url: apiOptions.ipfs + featuredImage.hash
                            };
                        } else if (featuredImage && featuredImage.url) {
                            return featuredImage;
                        }

                        return {};
                    },
                    getTags: function getTags(post) {
                        let tags = post.metadata.tags;
                        let linkedTags = []; //Select only 8 first tags

                        tags = tags.slice(0, 7);
                        tags.forEach(function (t) {
                            linkedTags.push('<a href="/popular/' + encodeURIComponent(t) + '">' + t + '</a>');
                        });
                        return linkedTags.join(', ');
                    },
                    getFutureDate: function getFutureDate(date) {
                        return toLocaleDate(date).fromNow();
                    },
                    hasPaid: function hasPaid(post) {
                        let now = moment();
                        let payout = toLocaleDate(post.cashout_time);
                        return now.isAfter(payout);
                    },
                    getPayoutPostDate: function getPayoutPostDate(post) {
                        let date = toLocaleDate(post.cashout_time);

                        if (this.hasPaid(post)) {
                            date = toLocaleDate(post.last_payout);
                        }

                        return date.fromNow();
                    },
                    hasPromotion: function hasPromotion(post) {
                        let amount = Asset.parseString(post.promoted);
                        return amount.amount > 0;
                    },
                    getPromotion: function getPromotion(post) {
                        let amount = Asset.parseString(post.promoted);
                        return '$ ' + amount.toPlainString();
                    },
                    getPayout: function getPayout(post) {
                        let amount = Asset.parseString(post.pending_payout_value);

                        if (this.hasPaid(post)) {
                            amount = Asset.parseString(post.total_payout_value);
                            amount = amount.add(Asset.parseString(post.curator_payout_value));
                        } //amount.amount = parseInt(amount.amount / 1000000000);


                        return '$ ' + amount.toPlainString();
                    },
                    getPendingPayouts: function getPendingPayouts(post, asset) {
                        asset = asset ? asset.toLowerCase() : '';

                        let PRICE_PER_CREA = Asset.parse({
                            amount: Asset.parseString(this.state.feed_price.base).toFloat() / Asset.parseString(this.state.feed_price.quote).toFloat(),
                            nai: 'cbd'
                        });
                        let CBD_PRINT_RATE = this.state.props.cbd_print_rate;
                        let CBD_PRINT_RATE_MAX = 10000;
                        let payout = Asset.parse(post.pending_payout_value); //payout.amount = parseInt(payout.amount / 1000000000);

                        let PENDING_PAYOUT = payout;
                        let PERCENT_CREA_DOLLARS = post.percent_crea_dollars / 20000;
                        let PENDING_PAYOUT_CBD = Asset.parse({
                            amount: PENDING_PAYOUT.toFloat() * PERCENT_CREA_DOLLARS,
                            nai: 'cbd'
                        });
                        let PENDING_PAYOUT_CGY = Asset.parse({
                            amount: NaNOr(((PENDING_PAYOUT.toFloat() - PENDING_PAYOUT_CBD.toFloat()) / PRICE_PER_CREA.toFloat()), 0),
                            nai: 'cgy'
                        });
                        let PENDING_PAYOUT_PRINTED_CBD = Asset.parse({
                            amount: NaNOr((PENDING_PAYOUT_CBD.toFloat() * (CBD_PRINT_RATE / CBD_PRINT_RATE_MAX)), 0),
                            nai: 'cbd'
                        });
                        let PENDING_PAYOUT_PRINTED_CREA = Asset.parse({
                            amount: NaNOr(((PENDING_PAYOUT_CBD.toFloat() - PENDING_PAYOUT_PRINTED_CBD.toFloat()) / PRICE_PER_CREA.toFloat()), 0),
                            nai: 'crea'
                        });

                        switch (asset) {
                            case 'cgy':
                                return PENDING_PAYOUT_CGY.toFriendlyString(null, false);
                            case 'cbd':
                                return PENDING_PAYOUT_PRINTED_CBD.toFriendlyString(null, false);
                            case 'crea':
                                return PENDING_PAYOUT_PRINTED_CREA.toFriendlyString(null, false);
                            default:
                                return '(' + PENDING_PAYOUT_PRINTED_CBD.toFriendlyString(null, false) + ', ' + PENDING_PAYOUT_PRINTED_CREA.toFriendlyString(null, false) + ', ' + PENDING_PAYOUT_CGY.toFriendlyString(null, false) + ')';
                        }

                    },
                    parseJSON: function parseJSON(strJson) {
                        if (strJson && strJson.length > 0) {
                            try {
                                return JSON.parse(strJson);
                            } catch (e) {
                                catchError(e);
                            }
                        }

                        return {};
                    },
                    onVote: function onVote(err, result, post) {
                        catchError(err);
                        //updateUserSession();
                        let that = this;
                        getDiscussion(post.author, post.permlink, function (err, result) {
                            if (!err) {
                                let updatedPost = parsePost(result, post.reblogged_by);
                                that.state.content[updatedPost.link] = updatedPost;
                                that.$forceUpdate();
                            }
                        })
                    },
                    getLicense: function getLicense(flag) {
                        if (flag) {
                            return License.fromFlag(flag);
                        }

                        return new License(LICENSE.FREE_CONTENT);
                    }
                }
            });

            setTimeout(function () {
                creaEvents.emit('crea.dom.ready');
            }, 200);
        } else {
            homePosts.session = session;
            homePosts.account = account;
            homePosts.filter = filter;
            homePosts.category = category;
            homePosts.discuss = discuss;
            homePosts.state = state;
            homePosts.urlFilter = urlFilter;
            homePosts.cleaningContent = false;
            homePosts.search = getParameterByName('query');
        }

        homePosts.$forceUpdate();
        creaEvents.emit('crea.content.path', category, discuss);
        console.log('cat', category, 'discuss', discuss);

    }

    creaEvents.on('crea.posts', function (urlFilter, filter, state, hasContent, needCleanContent) {
        let authors = [];
        let category = getPathPart(urlFilter);
        let discuss = getPathPart(urlFilter, 1) || '';
        console.log('crea.posts', urlFilter, filter, hasContent, needCleanContent, category, discuss);

        for (let c in state.content) {
            let author = state.content[c].author;

            if (!authors.includes(author)) {
                authors.push(author);
            }

            state.content[c] = parsePost(state.content[c]);
        }

        if (isUserFeed()) {
            //Retrieve another accounts
            getAccounts(authors, function (err, result) {
                if (!catchError(err)) {
                    let accounts = {};
                    result.forEach(function (a) {
                        accounts[a.name] = a;
                    });

                    if (homePosts) {

                        if (needCleanContent) {
                            cleanHomeContent(urlFilter)
                        }

                        //Accounts
                        for (let a in accounts) {
                            homePosts.state.accounts[a] = accounts[a];
                        }

                        //Posts
                        for (let _c in state.content) {
                            homePosts.state.content[_c] = state.content[_c];
                            if (!homePosts.state.discussion_idx['feed'][getPathPart()].includes(_c)) {
                                homePosts.state.discussion_idx['feed'][getPathPart()].push(_c);
                            }
                        }

                        state = homePosts.state;
                    } else {
                        state.accounts = accounts;
                    }

                    if (!hasContent) {
                        if (homePosts) {
                            homePosts.state.discussion_idx['feed'][getPathPart()] = [];
                        }
                        state.content = {};
                    }

                    //Result is for applied filters and no has accounts
                    showPosts(urlFilter, filter, state);
                }
            });
        } else {
            if (homePosts) {

                if (needCleanContent) {
                    cleanHomeContent(urlFilter);

                    homePosts.state.category = category;
                    homePosts.state.discuss = discuss;
                }

                if (homePosts.urlFilter === urlFilter) {
                    for (let a in state.accounts) {
                        homePosts.state.accounts[a] = parseAccount(state.accounts[a]);
                    }
                }
            }

            console.log('State', state);
            let ck = Object.keys(state.content);
            let reblogsFetched = 0;
            let onAllReblogs = function () {
                reblogsFetched++;

                //console.log('onAllReblogs', reblogsFetched, ck.length);
                if (reblogsFetched >= ck.length) {
                    showPosts(urlFilter, filter, state);
                }
            };

            let onReblogs = function (k, d, reblogs) {
                state.content[k] = parsePost(d, reblogs);
            };

            for (let x = 0; x < ck.length; x++) {
                let d = state.content[ck[x]];

                (function (x, ck, d) {
                    let commentsApi = new CommentsApi();
                    commentsApi.comment(d.author, d.permlink, function (err, data) {
                        if (err) {
                            console.log('Fail getting comment data', d.author, d.permlink);
                            onReblogs(ck[x], d);
                            onAllReblogs();
                        } else {
                            onReblogs(ck[x], d, data.reblogged_by);
                            onAllReblogs();
                        }
                    });
                })(x, ck, d)

            }
        }
    });

    function beforeInit(urlFilter = null) {
        let path = currentPage ? currentPage.pathname : window.location.pathname;
        console.log('beforeInit', path, urlFilter, getPathPart(), getPathPart(null, 1));

        if (path === '/') {
            if (session) {
                urlFilter = urlFilter ? urlFilter : '/@' + session.account.username + '/feed';
                updateUrl(urlFilter);
                creaEvents.emit('crea.content.path', 'feed');
                //creaEvents.emit('crea.content.filter', urlFilter);
            } else {
                updateUrl('/popular')
                creaEvents.emit('crea.content.path', 'popular');
                //creaEvents.emit('crea.content.filter', '/popular');
            }

            creaEvents.emit('crea.content.load');
        } else {
            if (isUserFeed(getPathPart())) {
                if (session) {
                    if (getPathPart() !== ('@' + session.account.username)) {
                        showProfile(getPathPart());
                    } else {
                        //Show user feed
                        creaEvents.emit('crea.content.path', 'feed');
                        creaEvents.emit('crea.content.load');
                    }
                } else {
                    //Avoid show feed if current user is not logged
                    showProfile(getPathPart());
                }
            } else {
                creaEvents.emit('crea.content.path', getPathPart(), getPathPart(null, 1));
                creaEvents.emit('crea.content.load');
            }
        }
    }

    function cleanHomeContent(urlFilter = null) {
        console.log('cleaning home content', urlFilter);
        let category = getPathPart(urlFilter);
        let discuss = getPathPart(urlFilter, 1);
        if (homePosts) {

            homePosts.cleaningContent = true;

            if (!homePosts.state.discussion_idx[discuss]) {
                homePosts.state.discussion_idx[discuss] = {};
            }

            let lastDiscuss = homePosts.discuss;
            let lastCategory = homePosts.category;

            homePosts.state.discussion_idx[discuss][category] = [];
            homePosts.discuss = discuss;
            homePosts.category = category;
            homePosts.state.discussion_idx[lastDiscuss][lastCategory] = [];
            homePosts.state.content = {};
            homePosts.$forceUpdate();
        }
    }

    function addApiContent(apiContents, cleanContent, callback) {
        console.log('Adding content', apiContents.length, apiContents);
        let discussions = [];
        let accounts = [];
        let count = apiContents.length;

        if (cleanContent) {
            cleanHomeContent();
        }

        let onContentFetched = function onContentFetched() {
            count--;

            console.log('Fetched. Remain', count);
            if (count <= 0) {
                getAccounts(accounts, function (err, newAccounts) {
                    if (!catchError(err)) {
                        //Update accounts
                        newAccounts.forEach(function (a) {
                            homePosts.state.accounts[a.name] = parseAccount(a);
                        });

                        //Sort
                        /*discussions.sort(function (k1, k2) {
                            let d1 = toLocaleDate(k1.created);
                            let d2 = toLocaleDate(k2.created);
                            return d2.valueOf() - d1.valueOf();
                        });*/
                        let discuss = homePosts.discuss;
                        let category = homePosts.category;

                        //Update Posts
                        discussions.forEach(function (d) {
                            let permlink = d.author + '/' + d.permlink;
                            homePosts.state.content[permlink] = d;
                            homePosts.state.discussion_idx[discuss][category].push(permlink);
                        });

                        homePosts.state.discussion_idx[discuss][category] = removeBlockedContents(homePosts.state, account, homePosts.state.discussion_idx[discuss][category]);
                        homePosts.state.discussions = homePosts.state.discussion_idx[discuss][category];
                        homePosts.cleaningContent = false;
                        homePosts.$forceUpdate();
                        creaEvents.emit('navigation.state.update', homePosts.state);
                    }

                    if (callback) {
                        callback();
                    }
                });
            }
        };

        apiContents.forEach(function (d) {
            let permlink = d.author + '/' + d.permlink;

            if (!homePosts.state.content[permlink]) {

                crea.api.getContent(d.author, d.permlink, function (err, result) {
                    if (err) {
                        console.error('Error getting', permlink, err);
                    } else {
                        let p = parsePost(result);
                        p.reblogged_by = d.reblogged_by;
                        discussions.push(p);

                        if (!homePosts.state.accounts[d.author] && !accounts.includes(d.author)) {
                            accounts.push(d.author);
                        }

                        onContentFetched();
                    }
                });
            } else {
                homePosts.state.content[permlink].reblogged_by = d.reblogged_by;
                onContentFetched();
            }
        });
    }

    creaEvents.on('crea.session.update', function (s, a) {
        homePosts.session = session = s;
        homePosts.account = account = a;
        --lastPage;
        beforeInit(homePosts.urlFilter);
    });

    creaEvents.on('crea.session.login', function (s, a) {
        session = s;
        account = a;
        //console.log(clone(a));
        beforeInit();
    });

    creaEvents.on('crea.content.add', function (apiContents, cleanContent) {
        addApiContent(apiContents, cleanContent);
    });

    let onScrollCalling = false;
    let feedResult;
    creaEvents.on('crea.scroll.bottom', function () {

        if (!onScrollCalling) {
            onScrollCalling = true;

            if (isUserFeed()) {
                creaEvents.emit('crea.content.load');
                setTimeout(function () {
                    onScrollCalling = false;
                }, 1e3);

            } else {
                feedResult = null;
                let apiCall;
                let category = homePosts.category;
                let discuss = homePosts.discuss ? homePosts.discuss : '';

                switch (category) {
                    case 'now':
                        apiCall = crea.api.getDiscussionsByNow;
                        break;

                    case 'skyrockets':
                        apiCall = crea.api.getDiscussionsBySkyrockets;
                        break;

                    case 'promoted':
                        apiCall = crea.api.getDiscussionsByPromoted;
                        break;

                    case 'popular':
                        apiCall = crea.api.getDiscussionsByPopular;
                        break;
                    default:
                        apiCall = crea.api['getDiscussionsBy' + category.capitalize()];
                        break;
                }

                if (apiCall) {
                    apiCall(lastPage.author, lastPage.permlink, discuss, 21, function (err, result) {
                        if (err) {
                            console.error(err);
                        } else {
                            //Get new accounts
                            let discussions = result.discussions;

                            //Remove first duplicate post
                            discussions.shift();
                            let topDiscussions = [];
                            let accounts = [];

                            let reblogsFetched = 0;
                            let onAllReblogs = function () {
                                reblogsFetched++;
                                if (reblogsFetched >= topDiscussions.length) {
                                    //Get new accounts
                                    getAccounts(accounts, function (err, newAccounts) {
                                        if (!catchError(err)) {
                                            //Update accounts
                                            newAccounts.forEach(function (a) {
                                                homePosts.state.accounts[a.name] = a;
                                            }); //Update Posts

                                            let discuss = homePosts.discuss;
                                            topDiscussions.forEach(function (d) {
                                                let permlink = d.author + '/' + d.permlink;
                                                homePosts.state.content[permlink] = d;

                                                homePosts.state.discussion_idx[discuss][category].push(permlink);
                                            });

                                            homePosts.state.discussion_idx[discuss][category] = removeBlockedContents(homePosts.state, account, homePosts.state.discussion_idx[discuss][category]);
                                            homePosts.state.discussions = homePosts.state.discussion_idx[discuss][category];
                                            lastPage = topDiscussions[topDiscussions.length - 1];
                                            homePosts.$forceUpdate();
                                            creaEvents.emit('navigation.state.update', homePosts.state);
                                        }

                                        onScrollCalling = false;
                                    });
                                }
                            };

                            if (discussions.length) {
                                for (let x = 0; x < discussions.length; x++) {
                                    let d = discussions[x];
                                    //For /now discussions, check post active date
                                    if (category === 'now') {
                                        let postCreatedDate = moment(d.created, 'YYYY-MM-DDTHH:mm:ss');
                                        let postPayoutDate = moment(d.last_payout, 'YYYY-MM-DDTHH:mm:ss');
                                        if (postCreatedDate.isAfter(postPayoutDate)) {
                                            //Post is active
                                            topDiscussions.push(d);
                                        } else {
                                            continue;
                                        }
                                    } else {
                                        topDiscussions.push(d);
                                    }

                                    (function (x, d) {

                                        let onReblogs = function (reblogs) {

                                            topDiscussions[x] = parsePost(d, reblogs);

                                            if (!homePosts.state.accounts[d.author] && !accounts.includes(d.author)) {
                                                accounts.push(d.author);
                                            }
                                        };

                                        let commentApi = new CommentsApi();
                                        commentApi.comment(d.author, d.permlink, function (err, result) {
                                            if (err) {
                                                onReblogs();
                                                onAllReblogs();
                                            } else {
                                                onReblogs(result.reblogged_by);
                                                onAllReblogs();
                                            }
                                        });
                                    })(topDiscussions.length-1, d)
                                }
                            } else {
                                if (discuss) {
                                    creaEvents.emit('crea.content.old');
                                }

                                onScrollCalling = false;
                            }

                        }
                    });
                }
            }
        }
    });

    creaEvents.on('crea.search.content', function (data) {
        let searchState = {
            content: {},
            accounts: {},
            discussion: []
        };
        let count = 0;

        let onFinish = function onFinish(state) {
            count++;

            if (count >= data.length) {
                console.log(state);
                state.content = searchState.content;
                state.accounts = searchState.accounts; //Sort by active_votes

                searchState.discussion.sort(function (c1, c2) {
                    return state.content[c2].active_votes.length - state.content[c1].active_votes.length;
                });
                state.discussion_idx[""] = {};
                state.discussion_idx[""].search = searchState.discussion;
                creaEvents.emit('crea.posts', '/search', 'search', state);
            }
        };

        let _loop = function _loop(x) {
            let getState = function getState(r) {
                let permalink = r.author + '/' + r.permlink;
                let url = '/' + r.tags[0] + '/@' + permalink;
                crea.api.getState(url, function (err, result) {
                    if (err) {
                        console.error(err);
                        getState(r);
                    } else {
                        searchState.discussion.push(permalink);
                        searchState.accounts[r.author] = result.accounts[r.author];
                        searchState.content[permalink] = result.content[permalink];
                        onFinish(result);
                    }
                });
            };

            getState(data[x]);
        };

        for (let x = 0; x < data.length; x++) {
            _loop(x);
        }

        if (data.length === 0) {
            crea.api.getState('/no_results', function (err, result) {
                if (!catchError(err)) {
                    onFinish(result);
                }
            });
        }
    });

    creaEvents.on('crea.dom.ready', function () {
        $("#view-changer").click(function () {
            homePosts.toggleSimpleView();
        });
    })
})();
