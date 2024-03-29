import R from '../lib/resources';
import { Asset } from '../lib/amount';
import { License } from '../lib/license';
import {
    cancelEventPropagation,
    clone,
    humanFileSize,
    makeMentions,
    NaNOr,
    randomNumber,
    toLocaleDate,
} from '../lib/util';
import {
    catchError,
    CONSTANTS,
    deleteComment,
    goTo,
    hideModal, hidePublication,
    ignoreUser,
    makeComment,
    makeDownload,
    parseAccount,
    parsePost,
    requireRoleKey,
    showModal,
    showPost,
    updateUrl,
    updateUserSession,
} from '../common/common';

//Components import
import Amount from '../components/Amount';
import Avatar from '../components/Avatar';
import Recommend from '../components/Recommend';
import RecommendPost from '../components/RecommendPost';
import NewLike from '../components/NewLike';
import LinkName from '../components/LinkName';
import Username from '../components/Username';
import PostLike from '../components/PostLike';
import PostLikeBig from '../components/PostLikeBig';
import PostAmount from '../components/PostAmount';
import CommentLike from '../components/CommentLike';
import ButtonFollow from '../components/ButtonFollow';
import { CommentsApi } from '../lib/creary-api';

(function () {
    //Load Vue components
    Vue.component('amount', Amount);
    Vue.component('avatar', Avatar);
    Vue.component('recommend', Recommend);
    Vue.component('recommend-post', RecommendPost);
    Vue.component('new-like', NewLike);
    Vue.component('comment-like', CommentLike);
    Vue.component('post-like', PostLike);
    Vue.component('post-like-big', PostLikeBig);
    Vue.component('post-amount', PostAmount);
    Vue.component('linkname', LinkName);
    Vue.component('username', Username);
    Vue.component('btn-follow', ButtonFollow);

    let lastPage;
    let postContainer, otherProjectsContainer;
    let promoteModal, downloadModal, reportModal, reportCommentModal, deletePublicationModal;
    let session, userAccount;

    function onVueReady() {
        /*        --vueInstances;

                if (vueInstances === 0) {
                    creaEvents.emit('crea.dom.ready');
                }*/
    }

    function setUp(state) {
        updateUrl(state.post.url, 'Creary - ' + state.post.title, state, true);
        //console.log('postNavigation', clone(state));

        if (!postContainer) {
            postContainer = new Vue({
                el: '#post-navigation-view',
                name: 'post-navigation',
                data: {
                    lang: lang,
                    CONSTANTS: CONSTANTS,
                    session: session,
                    user: userAccount ? userAccount.user : false,
                    state: state,
                    comment: '',
                    response_comment: '',
                    active_comment: null,
                    active_comment_edit: null,
                    active_response: null,
                    active_response_edit: null,
                    comments_shown: CONSTANTS.POST.MAX_COMMENT_SHOWN,
                    navigation: true,
                },
                watch: {
                    state: {
                        immediate: true,
                        deep: true,
                        handler: function handler(newVal, oldVal) {
                            if (newVal) {
                                globalLoading.show = !newVal.post;
                            }
                        },
                    },
                },
                mounted: function mounted() {
                    onVueReady();
                },
                methods: {
                    nextPost: nextPost,
                    lastPost: lastPost,
                    showPost: showPost,
                    humanFileSize: humanFileSize,
                    isPostModalVisible: isPostModalVisible,
                    getBuzzClass: function getBuzzClass(account) {
                        let buzzClass = {};
                        let levelName = account.buzz.level_name;

                        buzzClass[levelName] = true;
                        return buzzClass;
                    },
                    cbdToDollar: function cbdToDollar(cbd) {
                        return '$ ' + Asset.parseString(cbd).toPlainString();
                    },
                    assetToString: function assetToString(asset) {
                        return Asset.parse(asset).toFriendlyString();
                    },
                    assetPart: function assetPart(asset, part) {
                        asset = Asset.parse(asset);

                        switch (part) {
                            case 'int':
                                return asset.toPlainString(null, false).split('.')[0];
                            case 'dec':
                                return asset.toPlainString(null, false).split('.')[1];
                            case 'sym':
                                return asset.asset.symbol;
                            default:
                                return Asset.parse(asset).toFriendlyString();
                        }
                    },
                    getDefaultAvatar: R.getAvatar,
                    getLicense: function getLicense() {
                        return License.fromFlag(this.state.post.metadata.license);
                    },
                    dateFromNow: function dateFromNow(date) {
                        return toLocaleDate(date).fromNow();
                    },
                    formatDate: function formatDate(date) {
                        return moment(date + 'Z').format('LLLL');
                    },
                    hasPaid: function hasPaid(post) {
                        post = post || this.state.post;

                        let now = moment();
                        let payout = toLocaleDate(post.cashout_time);
                        return now.isAfter(payout);
                    },
                    getPayoutPostDate: function getPayoutPostDate(post) {
                        post = post || this.state.post;
                        let date = toLocaleDate(post.cashout_time);

                        if (this.hasPaid(post)) {
                            date = toLocaleDate(post.last_payout);
                        }

                        return date.fromNow();
                    },
                    hasPromotion: function hasPromotion() {
                        let post = this.state.post;
                        let amount = Asset.parseString(post.promoted);
                        return amount.amount > 0;
                    },
                    getNFTLink: function () {
                        if (this.state.post.metadata.other) {
                            return this.state.post.metadata.other.nftLink;
                        }

                        return null;
                    },
                    getPromotion: function getPromotion() {
                        let post = this.state.post;
                        let amount = Asset.parseString(post.promoted);
                        return '$ ' + amount.toPlainString();
                    },
                    getPayout: function getPayout(post, sym, dec) {
                        post = post || this.state.post;

                        if (!dec) {
                            dec = 2;
                        }

                        let amount = Asset.parseString(post.pending_payout_value);

                        if (this.hasPaid(post)) {
                            amount = Asset.parseString(post.total_payout_value);
                            amount = amount.add(Asset.parseString(post.curator_payout_value));
                        } //amount.amount = parseInt(amount.amount / 1000000000);

                        return (sym ? '$ ' : '') + amount.toPlainString(dec);
                    },
                    getFriendlyPayout: function getFriendlyPayout(post) {
                        post = post || this.state.post;
                        return this.getPayout(post, false) + ' CBD';
                    },
                    getPendingPayouts: function getPendingPayouts(post, asset) {
                        if (asset) {
                            console.log('Asset:', asset);
                        }
                        asset = asset ? asset.toLowerCase() : '';

                        post = post || this.state.post;
                        let PRICE_PER_CREA = Asset.parse({
                            amount:
                                Asset.parseString(this.state.feed_price.base).toFloat() /
                                Asset.parseString(this.state.feed_price.quote).toFloat(),
                            nai: 'cbd',
                        });
                        let CBD_PRINT_RATE = this.state.props.cbd_print_rate;
                        let CBD_PRINT_RATE_MAX = 10000;
                        let payout = Asset.parseString(post.pending_payout_value); //payout.amount = parseInt(payout.amount / 1000000000);

                        let PENDING_PAYOUT = payout;
                        let PERCENT_CREA_DOLLARS = post.percent_crea_dollars / 20000;
                        let PENDING_PAYOUT_CBD = Asset.parse({
                            amount: PENDING_PAYOUT.toFloat() * PERCENT_CREA_DOLLARS,
                            nai: 'cbd',
                        });
                        let PENDING_PAYOUT_CGY = Asset.parse({
                            amount: NaNOr(
                                (PENDING_PAYOUT.toFloat() - PENDING_PAYOUT_CBD.toFloat()) / PRICE_PER_CREA.toFloat(),
                                0
                            ),
                            nai: 'cgy',
                        });
                        let PENDING_PAYOUT_PRINTED_CBD = Asset.parse({
                            amount: NaNOr(PENDING_PAYOUT_CBD.toFloat() * (CBD_PRINT_RATE / CBD_PRINT_RATE_MAX), 0),
                            nai: 'cbd',
                        });
                        let PENDING_PAYOUT_PRINTED_CREA = Asset.parse({
                            amount: NaNOr(
                                (PENDING_PAYOUT_CBD.toFloat() - PENDING_PAYOUT_PRINTED_CBD.toFloat()) /
                                    PRICE_PER_CREA.toFloat(),
                                0
                            ),
                            nai: 'crea',
                        });

                        switch (asset) {
                            case 'cgy':
                                return PENDING_PAYOUT_CGY.toFriendlyString(null, false);
                            case 'cbd':
                                return PENDING_PAYOUT_PRINTED_CBD.toFriendlyString(null, false);
                            case 'crea':
                                return PENDING_PAYOUT_PRINTED_CREA.toFriendlyString(null, false);
                            default:
                                return (
                                    '(' +
                                    PENDING_PAYOUT_PRINTED_CBD.toFriendlyString(null, false) +
                                    ', ' +
                                    PENDING_PAYOUT_PRINTED_CREA.toFriendlyString(null, false) +
                                    ', ' +
                                    PENDING_PAYOUT_CGY.toFriendlyString(null, false) +
                                    ')'
                                );
                        }
                    },
                    showMoreComments: function () {
                        this.comments_shown += CONSTANTS.POST.COMMENT_SHOW_INTERVAL;
                        this.$forceUpdate();
                    },
                    getFeaturedImage: function getFeaturedImage(post) {
                        let featuredImage = post.metadata.featuredImage;

                        if (featuredImage && featuredImage.hash) {
                            return {
                                url: 'https://ipfs.creary.net/ipfs/' + featuredImage.hash,
                            };
                        } else if (featuredImage && featuredImage.url) {
                            return featuredImage;
                        }

                        return {};
                    },
                    getLinkedTags: function getLinkedTags(asString) {
                        //<a v-bind:href="'/popular/' + x">{{ x }}</a>
                        let tags = this.state.post.metadata.tags;
                        let linkedTags = [];
                        tags.forEach(function (t) {
                            linkedTags.push('<a href="/search?q=' + encodeURIComponent(t) + '">' + t + '</a>');
                        });

                        if (asString) {
                            return linkedTags.join(', ');
                        }

                        return linkedTags;
                    },
                    isSameUser: function isSameUser() {
                        if (this.session) {
                            return this.state.post.author === this.session.account.username;
                        }

                        return false;
                    },
                    isCommentResponse: function (comment, parentComment) {
                        return (
                            comment.parent_author === parentComment.author &&
                            comment.parent_permlink === parentComment.permlink
                        );
                    },
                    editPost: function editPost() {
                        let route = this.state.post.author + '/' + this.state.post.permlink;
                        goTo('/publish?edit=' + encodeURIComponent(route));
                    },
                    addComment: function (parentPost, commentReply, editingResponse) {
                        let that = this;

                        let post = editingResponse;
                        let comment = commentReply ? this.response_comment : this.comment;
                        makeComment(comment, post, parentPost, function (err, result) {
                            globalLoading.show = false;
                            if (!catchError(err)) {
                                if (commentReply) {
                                    that.cleanMakeResponse();
                                } else {
                                    that.cleanMakeComment();
                                }
                                showPostData(
                                    that.state.post,
                                    that.state,
                                    that.state.discuss,
                                    that.state.category,
                                    null,
                                    true
                                );
                            }
                        });
                    },
                    linkfy: function (comment) {
                        return makeMentions(comment, this.state);
                    },
                    mustShowCommentField: function (comment) {
                        return (
                            (this.active_response != null && this.active_response.link === comment.link) ||
                            (this.active_response_edit != null &&
                                this.active_response_edit.parent_author === comment.author &&
                                this.active_response_edit.parent_permlink === comment.permlink)
                        );
                    },
                    setActiveComment: function (activeComment) {
                        this.active_comment = activeComment;
                        if (reportCommentModal) {
                            reportCommentModal.active_comment = activeComment;
                            reportCommentModal.$forceUpdate();
                        }

                        this.$forceUpdate();
                    },
                    setActiveCommentEdit: function (editComment) {
                        this.active_comment_edit = editComment;
                        this.comment = editComment.body;
                        this.$forceUpdate();
                    },
                    cleanMakeComment: function () {
                        this.active_comment = null;
                        this.active_comment_edit = null;
                        this.comment = '';
                        this.$forceUpdate();
                    },
                    cleanMakeResponse: function () {
                        this.active_response = null;
                        this.active_response_edit = null;
                        this.response_comment = '';
                        this.$forceUpdate();
                    },
                    makeDownload: makeDownload,
                    removeComment: function (comment) {
                        let that = this;
                        deleteComment(comment, this.session, function (err, result) {
                            globalLoading.show = false;
                            if (!catchError(err)) {
                                showPostData(
                                    that.state.post,
                                    that.state,
                                    that.state.discuss,
                                    that.state.category,
                                    null,
                                    true
                                );
                            }
                        });
                    },
                    editComment: function (comment) {
                        this.response_comment = comment.body;
                        this.active_response_edit = comment;
                    },
                    ignoreUser: function () {
                        ignoreUser(this.state.post.author, true, function (err, result) {
                            updatePostData();
                        });
                    },
                    vote: function vote(weight, post) {
                        console.log('Vote', weight, post);
                        post = post || this.state.post;
                        if (this.session) {
                            let that = this;
                            let username = this.session.account.username;
                            requireRoleKey(username, 'posting', function (postingKey) {
                                globalLoading.show = true;
                                crea.broadcast.vote(
                                    postingKey,
                                    username,
                                    post.author,
                                    post.permlink,
                                    weight,
                                    function (err, result) {
                                        globalLoading.show = false;
                                        if (!catchError(err)) {
                                            showPostData(
                                                that.state.post,
                                                that.state,
                                                that.state.discuss,
                                                that.state.category,
                                                null,
                                                true
                                            );
                                            showModal('#modal-post');
                                        }
                                    }
                                );
                            });
                        }
                    },
                    onVote: function onVote(err) {
                        console.log('onVote, err:', err);
                        let that = this;
                        if (!catchError(err) && this.isPostModalVisible()) {
                            showPostData(
                                that.state.post,
                                that.state,
                                that.state.discuss,
                                that.state.category,
                                null,
                                true
                            );
                        }
                    },
                    onFollow: function onFollow(err, result) {
                        console.log('on follow-nav', err, result);
                        let that = this;
                        if (!catchError(err)) {
                            //updateUserSession();
                            //showPostData(that.state.post, that.state, that.state.discuss, that.state.category, null, true);
                        }
                    },
                },
            });
        } else {
            postContainer.state = state;
            postContainer.session = session;
            postContainer.user = userAccount ? userAccount.user : null;
        }

        postContainer.$forceUpdate();

        if (session) {
            if (!promoteModal) {
                promoteModal = new Vue({
                    el: '#modal-promote',
                    data: {
                        lang: lang,
                        session: session,
                        user: userAccount ? userAccount.user : null,
                        state: state,
                        amount: 0,
                    },
                    mounted: function mounted() {
                        onVueReady();
                    },
                    methods: {
                        hideModalPromote: function hideModalPromote(event) {
                            cancelEventPropagation(event);
                            hideModal('#modal-promote');
                            showModal('#modal-post');
                        },
                        makePromotion: function makePromotion(event) {
                            cancelEventPropagation(event);
                            let from = this.session.account.username;
                            let to = 'null';
                            let memo = '@' + this.state.post.author + '/' + this.state.post.permlink;
                            let amount = parseFloat(this.amount) + 0.0001;
                            console.log(amount);
                            amount = Asset.parse({
                                amount: amount,
                                nai: apiOptions.nai.CBD,
                            }).toFriendlyString(null, false);
                            console.log(amount);
                            let that = this;
                            requireRoleKey(from, 'active', function (activeKey) {
                                globalLoading.show = true;
                                crea.broadcast.transfer(activeKey, from, to, amount, memo, function (err, result) {
                                    globalLoading.show = false;

                                    if (!catchError(err)) {
                                        that.hideModalPromote();
                                        updateUserSession();
                                    }
                                });
                            });
                        },
                    },
                });
            } else {
                promoteModal.session = session;
                promoteModal.user = userAccount ? userAccount.user : null;
                promoteModal.state = state;
            }

            if (state.post.download.resource) {
                if (!downloadModal) {
                    let price = Asset.parse(state.post.download.price);

                    let balance =
                        price.asset.symbol === apiOptions.symbol.CREA
                            ? Asset.parseString('0.000 CREA')
                            : Asset.parseString('0.000 CBD');
                    let alreadyPayed = false;

                    if (session) {
                        balance =
                            price.asset.symbol === apiOptions.symbol.CREA
                                ? Asset.parseString(userAccount.user.balance)
                                : Asset.parseString(userAccount.user.cbd_balance);
                        alreadyPayed = state.post.download.downloaders.includes(userAccount.user.name);
                    }

                    downloadModal = new Vue({
                        el: '#modal-download',
                        data: {
                            lang: lang,
                            session: session,
                            user: userAccount ? userAccount.user : false,
                            state: state,
                            modal: {
                                amount: price.toPlainString(null, false),
                                symbol: price.asset.symbol.toUpperCase(),
                                balance: balance.toFriendlyString(null, false),
                                alreadyPayed: alreadyPayed,
                                confirmed: false,
                            },
                        },
                        mounted: function mounted() {
                            onVueReady();
                        },
                        methods: {
                            cancelPay: function cancelPay() {
                                this.modal.confirmed = false;
                            },
                            confirmDownload: function confirmDownload() {
                                console.log('Donwload content', this.modal.alreadyPayed, this.modal.confirmed);
                                if (this.modal.alreadyPayed || this.modal.confirmed) {
                                    makeDownload(null, session, this.user, this.state.post, function () {
                                        console.log('On download success');
                                        showModal('#modal-post');
                                    });
                                } else {
                                    this.modal.confirmed = true;
                                }
                            },
                        },
                    });
                } else {
                    downloadModal.session = session;
                    downloadModal.user = userAccount ? userAccount.user : null;
                    downloadModal.state = state;
                }
            } else {
                //This post not has a download, so downloadModal cannot be mounted
                onVueReady();
            }

            if (!reportModal) {
                reportModal = new Vue({
                    el: '#modal-report',
                    data: {
                        lang: lang,
                        session: session,
                        user: userAccount ? userAccount.user : null,
                        state: state,
                    },
                    methods: {
                        vote: function vote(weight, post) {
                            console.log('Vote', weight, post);
                            post = post || this.state.post;
                            if (this.session) {
                                let that = this;
                                let username = this.session.account.username;
                                requireRoleKey(username, 'posting', function (postingKey) {
                                    globalLoading.show = true;
                                    crea.broadcast.vote(
                                        postingKey,
                                        username,
                                        post.author,
                                        post.permlink,
                                        weight,
                                        function (err, result) {
                                            globalLoading.show = false;
                                            catchError(err);
                                            showPostData(
                                                that.state.post,
                                                that.state,
                                                that.state.discuss,
                                                that.state.category,
                                                null,
                                                true
                                            );
                                            showModal('#modal-post');
                                        }
                                    );
                                });
                            }
                        },
                    },
                });
            } else {
                reportModal.session = session;
                reportModal.user = userAccount ? userAccount.user : null;
                reportModal.state = state;
            }

            if (!reportCommentModal) {
                reportCommentModal = new Vue({
                    el: '#modal-report-comment',
                    data: {
                        lang: lang,
                        session: session,
                        user: userAccount ? userAccount.user : null,
                        state: state,
                        active_comment: null,
                    },
                    methods: {
                        vote: function vote(weight, post) {
                            console.log('Vote', weight, post);
                            post = post || this.state.post;
                            if (this.session) {
                                let that = this;
                                let username = this.session.account.username;
                                requireRoleKey(username, 'posting', function (postingKey) {
                                    globalLoading.show = true;
                                    crea.broadcast.vote(
                                        postingKey,
                                        username,
                                        post.author,
                                        post.permlink,
                                        weight,
                                        function (err, result) {
                                            globalLoading.show = false;
                                            if (!catchError(err)) {
                                                showPostData(
                                                    that.state.post,
                                                    that.state,
                                                    that.state.discuss,
                                                    that.state.category,
                                                    null,
                                                    true
                                                );
                                                showModal('#modal-post');
                                            }
                                        }
                                    );
                                });
                            }
                        },
                    },
                });
            } else {
                reportCommentModal.session = session;
                reportCommentModal.user = userAccount ? userAccount.user : null;
                reportCommentModal.state = state;
            }

            if (!deletePublicationModal) {
                deletePublicationModal = new Vue({
                    el: '#modal-delete',
                    data: {
                        lang: lang,
                        session: session,
                        user: userAccount ? userAccount.user : null,
                        state: state,
                    },
                    mounted: function mounted() {
                        onVueReady();
                    },
                    methods: {
                        deletePublication: function () {
                            let that = this;
                            hidePublication(this.state.post, this.session, function (err, result) {
                                globalLoading.show = false;

                                if (!catchError(err)) {
                                    creaEvents.emit('crea.post.delete', that.state.post);

                                }
                            })
                        },
                    },
                });
            } else {
                deletePublicationModal.session = session;
                deletePublicationModal.user = userAccount ? userAccount.user : null;
                deletePublicationModal.state = state;
            }
        }
    }

    function isPostModalVisible() {
        return $('#modal-post').hasClass('modal-active');
    }

    function fetchOtherProjects(author, permlink, state) {
        let loadOtherProjects = function loadOtherProjects(discussions) {
            console.log('Others', discussions);

            otherProjectsContainer = new Vue({
                el: '#more-projects-navigation',
                data: {
                    lang: lang,
                    state: state,
                    otherProjects: discussions,
                    navigation: true,
                },
                updated: function () {
                    mr.sliders.documentReady($);

                    let fl = $('#more-projects-navigation .flickity-slider');
                    let count = fl.length;
                    console.log('Slider modal updated', count);
                    setTimeout(function () {
                        fl.each(function (index) {
                            let sl = $(this);
                            if (sl.children().length === 0) {
                                sl.parent().remove();
                            }
                        });
                    }, 500);
                },
                methods: {
                    loadPost: function (post, event) {
                        cancelEventPropagation(event);
                        let state = postContainer.state;
                        let moreProjects = [];
                        this.otherProjects.forEach(function (d) {
                            moreProjects.push(d.link);
                            state.content[d.link] = d;
                        });

                        let discuss = state.discuss || '';
                        if (!state.discussion_idx[discuss]) {
                            state.discussion_idx[discuss] = {};
                        }

                        state.discussion_idx[discuss].more_projects = moreProjects;
                        showPostData(
                            post,
                            postContainer.state,
                            discuss,
                            'more_projects',
                            moreProjects.indexOf(post.link)
                        );
                    },
                    getFeaturedImage: function getFeaturedImage(post) {
                        let featuredImage = post.metadata.featuredImage;

                        if (featuredImage && featuredImage.hash) {
                            return {
                                url: 'https://ipfs.creary.net/ipfs/' + featuredImage.hash,
                            };
                        } else if (featuredImage && featuredImage.url) {
                            return featuredImage;
                        }

                        return {};
                    },
                },
            });

            otherProjectsContainer.$forceUpdate();
        };

        let date = new Date().toISOString().replace('Z', '');
        crea.api.getDiscussionsByAuthorBeforeDateWith(
            {
                start_permlink: '',
                limit: 100,
                before_date: date,
                author: author,
            },
            function (err, result) {
                if (!catchError(err)) {
                    let discussions = [];
                    console.log('Other projects', result);

                    result.discussions.forEach(function (d) {
                        d = parsePost(d, d.reblogged_by);

                        if (d.permlink !== permlink && d.metadata.featuredImage) {
                            discussions.push(d);
                        }
                    });

                    if (discussions.length > CONSTANTS.POST.MAX_OTHER_PROJECTS) {
                        let selectedDiscuss = [];

                        for (let x = 0; x < CONSTANTS.POST.MAX_OTHER_PROJECTS; x++) {
                            let r = randomNumber(0, discussions.length - 1);
                            selectedDiscuss.push(discussions.splice(r, 1)[0]);
                        }

                        discussions = selectedDiscuss;
                    }

                    loadOtherProjects(discussions);
                }
            }
        );
    }

    function updatePostData() {
        if (postContainer) {
            showPostIndex(postContainer.state.postIndex, postContainer.state);
        }
    }

    function nextPost(event) {
        cancelEventPropagation(event);
        let state = postContainer.state;
        let postIndex = state.discussions.indexOf(state.author.name + '/' + state.post.permlink);

        if (postIndex >= 0 && postIndex <= state.discussions.length - 2) {
            postIndex++;
            showPostIndex(postIndex, state);

            if (postIndex >= state.discussions.length - 5) {
                creaEvents.emit('crea.scroll.bottom');
            }
        }
    }

    function lastPost(event) {
        cancelEventPropagation(event);
        let state = postContainer.state;
        let postIndex = state.discussions.indexOf(state.post.link);

        if (postIndex > 0 && postIndex <= state.discussions.length - 1) {
            postIndex--;
            showPostIndex(postIndex, state);
        }
    }

    function showPostIndex(postIndex, state) {
        console.log('postIndex', postIndex);
        let postContent = state.discussions[postIndex];
        let post = clone(state.postsData[postContent]);
        showPostData(post, state, state.discuss, state.category, postIndex);
    }

    function showPostData(post, state, discuss, category, postIndex, postRefresh) {
        //state.post = null;
        if (!postRefresh && postContainer) {
            postContainer.$set(postContainer.state, 'post', null);
            postContainer.$forceUpdate();
        }

        state = clone(state);
        console.log(discuss, category, state, post);
        let discussions = state.discussions || state.discussion_idx[discuss][category];

        let postUrl = `/${post.parent_permlink}/@${post.author}/${post.permlink}`;
        let postRoute = post.author + '/' + post.permlink;
        crea.api.getState(postUrl, function (err, postState) {
            if (!err) {
                if (state.postsData) {
                    postState.postsData = state.postsData;
                }

                postState.discuss = discuss || '';
                postState.category = category;
                postState.discussions = discussions;
                if (!postIndex) {
                    postState.postIndex = postState.discussions.indexOf(post.author + '/' + post.permlink);
                } else {
                    postState.postIndex = postIndex;
                }

                console.log(postState.postIndex, clone(postState.discussions), post.author + "/" + post.permlink);
                if (postState.postIndex >= postState.discussions.length - 5) {
                    creaEvents.emit('crea.scroll.bottom');
                }

                let onPostReblogs = function (reblogs) {
                    let aKeys = Object.keys(postState.accounts);

                    if (aKeys.length === 0) {
                        console.log('No post:', postState);
                    } else {
                        aKeys.forEach(function (k) {
                            postState.accounts[k] = parseAccount(postState.accounts[k]);
                        });

                        postState.post = parsePost(postState.content[postRoute], reblogs);
                        postState.author = parseAccount(postState.accounts[postState.post.author]);

                        //Order comments by date, latest first
                        let cKeys = Object.keys(postState.content);
                        cKeys.sort(function (k1, k2) {
                            let d1 = toLocaleDate(postState.content[k1].created);
                            let d2 = toLocaleDate(postState.content[k2].created);
                            return d2.valueOf() - d1.valueOf();
                        });
                        for (let c of cKeys) {
                            postState.post[c] = parsePost(postState.content[c]);
                        }

                        //console.log('CKeys', cKeys, clone(postState))
                        postState.post.comments = cKeys;

                        setUp(postState);

                        setTimeout(function () {
                            fetchOtherProjects(post.author, post.permlink, postState);
                        }, 300);
                    }
                };

                let commentApi = new CommentsApi();
                commentApi.comment(`@${post.author}`, post.permlink, function (err, result) {
                    if (err) {
                        onPostReblogs();
                    } else {
                        onPostReblogs(result.reblogged_by);
                    }
                });
            } else {
                console.error(err);
            }
        });
    }

    function updatePages() {
        lastPage = {
            pathname: window.location.pathname,
            title: document.title,
        };

        currentPage = {
            pathname: window.location.pathname,
            title: document.title,
        };
    }

    $(window).bind('popstate', function (event) {
        if (event.originalEvent.state && event.originalEvent.state.post) {
            setUp(event.originalEvent.state, true);
        }
    });

    creaEvents.on('navigation.state.update', function (state) {
        console.log('updating navigation state');
        if (postContainer) {
            let postState = postContainer.state;

            //state.post = postState.post;
            /*state.author = postState.author;
            state.discuss = postState.discuss;
            state.category = postState.category;*/
            //postState.postIndex = state.discussions.indexOf(postState.post.link);

            postState.discussions = state.discussions;
            postContainer.state.postsData = state.content;
            postContainer.$forceUpdate();
        }
    });

    creaEvents.on('crea.post.delete', function (post) {
        console.log('Post deleted', clone(post));

        hideModal('#modal-post');
        setTimeout(_ => {
            postContainer = null;
        }, 300)
    });

    creaEvents.on('navigation.post.data', showPostData);

    creaEvents.on('crea.session.login', function (s, a) {
        session = s;
        userAccount = a;

        updatePostData();
    });

    creaEvents.on('crea.session.update', function (s, a) {
        session = s;
        userAccount = a;
        updatePostData();
    });

    creaEvents.on('crea.session.logout', function () {
        session = false;
        userAccount = false;

        updatePostData();
    });

    creaEvents.on('crea.content.prepare', updatePages);

    creaEvents.on('crea.modal.ready', function () {
        console.log('MODALS Ready');
        setTimeout(function () {
            $('#modal-post')
                .on('modalOpened.modals.mr', function () {
                    console.log('Showing modal');
                    $('body').css({ overflow: 'hidden' });
                })
                .on('modalClosed.modals.mr', function () {
                    console.log('Closing modal', lastPage);
                    $('body').css({ overflow: '' });
                    updateUrl(lastPage.pathname, lastPage.title);
                });
        }, 1000);
    });
})();
