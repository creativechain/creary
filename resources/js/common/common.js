import HttpClient from '../lib/http';
import * as Ipfs from 'ipfs-http-client';
import Session from '../lib/session';
import * as CREARY from '../common/ls';
import { Asset } from '../lib/amount';
import {
    clone,
    jsonify,
    jsonstring,
    isJSON,
    cleanArray,
    isUserFeed,
    randomNumber,
    toPermalink,
    getNavigatorLanguage,
    uniqueId,
    cancelEventPropagation,
    getPathPart, arrayBufferToBuffer, dataURLtoBlob,
} from '../lib/util';
import Errors from '../lib/error';
import { DEFAULT_ROLES } from '../lib/account';
import Pica from 'pica';
import gifShot from 'gifshot';
import { parseGIF, decompressFrames } from "gifuct-js";

/**
 * Created by ander on 25/09/18.
 */
class IpfsFile {
    constructor(hash, name, type, size) {
        this.hash = hash;
        this.name = name;
        this.type = type;
        this.size = size;
        this.url = 'https://ipfs.creary.net/ipfs/' + hash;
    }
}

const CONSTANTS = {
    ACCOUNT: {
        UPDATE_THRESHOLD: 1000 * 60 * 60,
        BLOCKED: ['volf', 'mercury', 'onecommett', 'onecomme', 'kwentyyy7', 'felixxx', 'olanin', 'djon', 'roxona', 'exrates1']
    },
    TRANSFER: {
        TRANSFER_CREA: 'transfer_crea',
        TRANSFER_CBD: 'transfer_cbd',
        TRANSFER_TO_SAVINGS_CREA: 'transfer_to_savings_crea',
        TRANSFER_TO_SAVINGS_CBD: 'transfer_to_savings_cbd',
        TRANSFER_FROM_SAVINGS_CREA: 'transfer_from_savings_crea',
        TRANSFER_FROM_SAVINGS_CBD: 'transfer_from_savings_cbd',
        TRANSFER_TO_VESTS: 'transfer_to_vests',
    },
    FILE_MAX_SIZE: {
        PROFILE: {
            IMAGE: 1024 * 500,
        },
        POST_BODY: {
            AUDIO: 100 * 1024 * 1024,
            VIDEO: 200 * 1024 * 1024,
            MP4: 200 * 1024 * 1024,
            WEBM: 200 * 1024 * 1024,
            IMAGE: 5 * 1024 * 1024,
            GIF: 10 * 1024 * 1024,
            WEBP: 10 * 1024 * 1024,
            DOWNLOAD: 200 * 1024 * 1024, //200 MB
        },
        POST_PREVIEW: {
            IMAGE: 500 * 1024,
            GIF: 1024 * 1024,
            WEBP: 1024 * 1024,
        },
    },
    TEXT_MAX_SIZE: {
        PROFILE: {
            PUBLIC_NAME: 21,
            ABOUT: 144,
            CONTACT: 55,
            WEB: 55,
        },
        TITLE: 55,
        DESCRIPTION: 233,
        COMMENT: 233,
        TAG: 21,
        PERMLINK: 255,
    },
    MAX_TAGS: 8,
    BUZZ: {
        USER_BLOCK_THRESHOLD: -30,
        MAX_LOG_NUM: 20,
        LEVELS: ['novice', 'trainee', 'advanced', 'expert', 'influencer', 'master', 'guru', 'genius'],
    },
    POST: {
        MAX_OTHER_PROJECTS: 12,
        MAX_COMMENT_SHOWN: 10,
        COMMENT_SHOW_INTERVAL: 10,
    },
    WITNESS: {
        DISABLED_SECONDS_THRESHOLD: 60 * 60 * 24 * 10,
    },
};

creaEvents.on('crea.session.login', function (session, account) {
    showBanner(session === false);
});

function showBanner(show) {
    if (window.bannerVue) {
        window.bannerVue.showBanner = show;
    }
}

function goTo(location) {
    window.location.href = location;
}

function showPost(post) {
    if (!post.url) {
        post.url = '/' + post.metadata.tags[0] + '/@' + post.author + '/' + post.permlink;
    }

    goTo(post.url);
}

function showProfile(username) {
    username = username.match(/[\w\.\d-]+/gm);
    if (username) {
        goTo('/@' + username);
    }
}

function updateUrl(url, title, data, isModal = false) {
    title = title ? title : lang.METADATA[url] ? lang.METADATA[url].TITLE : lang.METADATA['/'].TITLE;
    //console.log('Title:', title);
    $('title').html(title);

    let currentLocation = window.location.pathname;

    if (isInHome()) {
        currentPage.homeUrl = currentLocation;
        currentPage.homeTitle = currentPage.title;
    }

    /*    if (currentLocation !== url || !isModal) {
        //If is different location, not change
        currentPage.parentUrl = currentLocation;
        currentPage.parentTitle = currentPage.title;
    }*/

    window.history.pushState(data, title, url);

    currentPage.pathname = url;
    currentPage.title = title ? title : document.title;
}

/**
 *
 * @param {string} location
 */
function toHome(location) {
    if (location) {
        if (window.location.href.indexOf(location) > -1) {
            goTo('/');
        }
    } else {
        goTo('/');
    }
}

/**
 *
 * @param {string} filter
 * @returns {string}
 */
function resolveFilter(filter) {
    filter = filter.toLowerCase();
    /*if (filter.startsWith('/popular')) {
        return filter.replace('/popular', '/trending');
    } else if (filter.startsWith('/skyrockets')) {
        return filter.replace('/skyrockets', '/hot');
    } else if (filter.startsWith('/now')) {
        return filter.replace('/now', '/created');
    }*/

    return filter;
}

/**
 *
 * @returns {boolean}
 */
function isInHome() {
    let filters = [
        '/skyrockets',
        '/popular',
        '/now',
        '/popular30',
        '/created',
        '/promoted',
        '/votes',
        '/actives',
        '/cashout',
        '/responses',
        '/payout',
        '/payout_comments',
        '/search',
    ]; //Check if path is user feed

    let s = Session.getAlive();

    if (s && isUserFeed(s.account.username)) {
        return true;
    }

    let pathPart = '/' + getPathPart();
    return filters.includes(pathPart);
}

function showModal(id) {
    /*$(id).on('modalOpened.modals.mr', function () {
        creaEvents.emit('crea.modal.show.' + id)
    }).on('modalClosed.modals.mr', function () {
        creaEvents.emit('crea.modal.hide.' + id)
    })*/

    mr.modals.showModal(id);
}

function hideModal(id) {
    mr.modals.hideModal(id);
}

function createBlockchainAccount(username, password, callback) {
    let keys = crea.auth.getPrivateKeys(username, password, DEFAULT_ROLES);
    refreshAccessToken(function (accessToken) {
        let http = new HttpClient(apiOptions.apiUrl + '/createCrearyAccount');
        http.withCredentials(false)
            .setHeaders({
                Authorization: 'Bearer ' + accessToken,
            })
            .when('done', function (data) {
                if (callback) {
                    callback(null, data);
                }
            })
            .when('fail', function (response, textStatus, request) {
                if (callback) {
                    callback(response.error);
                }
            })
            .post({
                username: username,
                active: keys.activePubkey,
                owner: keys.ownerPubkey,
                posting: keys.postingPubkey,
                memo: keys.memoPubkey,
            });
    });
}

function removeBlockedContents(state, accountState, discussion_idx) {
    //console.log('State:', jsonify(jsonstring(state)), 'Account:', jsonify(jsonstring(accountState)), 'discussion:', discussion_idx);
    if (state) {
        //Remove post for blocked users
        let cKeys = discussion_idx ? discussion_idx : Object.keys(state.content);

        if (accountState) {
            let allowedContents = [];
            cKeys.forEach(function (ck) {
                let c = state.content[ck];

                //If author is blocked, post must be blocked
                if (accountState.user.blockeds.indexOf(c.author) < 0) {
                    if (c.adult_content) {
                        //console.log('Adult Filtering:', c.title, c.metadata.adult, c.metadata.tags);
                        if (accountState.user.metadata.adult_content !== 'hide') {
                            //Filter adult content
                            allowedContents.push(ck);
                        }
                    } else {
                        //No adult, include
                        allowedContents.push(ck);
                    }
                }
            });

            return allowedContents;
        }

        return cKeys;
    }

    return null;
}

function parseAccount(account) {
    if (account) {
        account = clone(account);
        account.buzz = crea.formatter.reputation(
            account.reputation,
            CONSTANTS.BUZZ.LEVELS.length,
            CONSTANTS.BUZZ.MAX_LOG_NUM
        );
        //Level 1 for bad users
        if (account.buzz.level <= 0) {
            account.buzz.level = 1;
        }
        account.buzz.level_name = CONSTANTS.BUZZ.LEVELS[account.buzz.level - 1];
        account.buzz.level_title = lang.BUZZ[account.buzz.level - 1];
        account.buzz.blocked = account.buzz.formatted <= CONSTANTS.BUZZ.USER_BLOCK_THRESHOLD;
        account.profile_blocked = BLOCKED_ACCOUNTS.includes(account.name);

        account.metadata = jsonify(account.json_metadata);

        if (account.buzz.blocked || account.profile_blocked) {
            account.metadata.avatar = {};
        } else {
            account.metadata.avatar = account.metadata.avatar || {};
        }

        account.metadata.adult_content = account.metadata.adult_content || 'warn';
        account.metadata.post_rewards = account.metadata.post_rewards || '100';
        account.metadata.comment_rewards = account.metadata.comment_rewards || '50';
        account.metadata.lang = account.metadata.lang || getNavigatorLanguage();

        //remove https:// or http:// on web metadata
        if (account.metadata.web) {
            account.metadata.web = account.metadata.web.replace('https://', '').replace('http://', '');
        }

        //console.log(jsonify(jsonstring(account)));
        return account;
    }

    return account;
}

function parsePost(post, reblogged_by) {
    if (!reblogged_by || !Array.isArray(reblogged_by)) {
        reblogged_by = [];
    }

    if (post) {
        post = clone(post);
        post.metadata = jsonify(post.json_metadata);
        post.metadata.tags = post.metadata.tags || [];
        post.link = post.author + '/' + post.permlink;
        post.url = '/' + post.metadata.tags[0] + '/@' + post.link;
        post.body = isJSON(post.body) ? jsonify(post.body) : post.body;
        post.body = cleanArray(post.body);

        //Beneficiaries
        let contributors = [];
        let bAuthor = {
            account: post.author,
            weight: 100,
        };

        post.beneficiaries.forEach((b) => {
            let c = clone(b);
            c.weight /= 100;
            bAuthor.weight -= c.weight;
            contributors.push(c);
        });

        contributors.unshift(bAuthor);
        post.contributors = contributors;

        //Has adult content
        post.adult_content =
            post.metadata.adult ||
            (post.metadata.tags &&
                (post.metadata.tags.includes('nsfw') ||
                    post.metadata.tags.includes('adult') ||
                    post.metadata.tags.includes('nude') ||
                    post.metadata.tags.includes('porn')));

        post.down_votes = [];
        post.up_votes = [];
        post.active_votes.forEach(function (v) {
            if (v.percent < 0) {
                //Content reports
                post.down_votes.push(v);
            } else if (v.percent > 0) {
                //Content likes
                post.up_votes.push(v);
            }
        });

        let session = Session.getAlive();
        post.reported = false;
        if (session) {
            //Set reported by user
            let username = session.account.username;
            for (let x = 0; x < post.down_votes.length; x++) {
                let v = post.down_votes[x];
                if (v.voter === username) {
                    post.reported = true;
                    break;
                }
            }
        }

        let toStringAsset = function toStringAsset(data) {
            if (typeof data === 'object') {
                return Asset.parse(data).toFriendlyString(null, false);
            }

            return data;
        };

        post.refused_payouts = Asset.parse(post.max_accepted_payout).amount <= 0;
        post.curator_payout_value = toStringAsset(post.curator_payout_value);
        post.max_accepted_payout = toStringAsset(post.max_accepted_payout);
        post.pending_payout_value = toStringAsset(post.pending_payout_value);
        post.promoted = toStringAsset(post.promoted);
        post.total_payout_value = toStringAsset(post.total_payout_value);
        post.total_pending_value = toStringAsset(post.total_pending_value);

        if (!post.reblogged_by || post.reblogged_by.length === 0) {
            post.reblogged_by = reblogged_by;
        }
    }

    return post;
}

function getAccounts(accounts, callback) {
    crea.api.getAccounts(accounts, function (err, result) {
        if (callback) {
            if (err) {
                callback(err);
            } else {
                for (let x = 0; x < result.length; x++) {
                    result[x] = parseAccount(result[x]);
                }

                callback(null, result);
            }
        }
    });
}

function getDiscussion(author, permlink, callback) {
    if (typeof permlink === 'function') {
        callback = permlink;
        let all;
        [all, author, permlink] = /([\w\.\d-]+)\/([\w\d-]+)/gm.exec(author);
    } else {
        author = /[\w\.\d-]+/gm.exec(author)[0];
    }

    crea.api.getDiscussion(author, permlink, function (err, discussion) {
        if (!err) {
            discussion = parsePost(discussion);
            callback(null, discussion);
        } else {
            callback(err, null);
        }
    });
}

function recommendPost(author, permlink, reblog, callback) {
    let s = Session.getAlive();

    if (s) {
        if (typeof reblog === 'function') {
            callback = reblog;
            reblog = true;
        }

        let recommendedJson = {
            account: s.account.username,
            author: author,
            permlink: permlink,
        };

        recommendedJson = [reblog ? 'reblog' : 'unreblog', recommendedJson];

        requireRoleKey(s.account.username, 'posting', function (postingKey) {
            crea.broadcast.customJson(
                postingKey,
                [],
                [s.account.username],
                'follow',
                jsonstring(recommendedJson),
                function (err, result) {
                    if (callback) {
                        if (err) {
                            callback(err);
                        } else {
                            callback(null, result);
                        }
                    }
                }
            );
        });
    }
}

function ignoreUser(following, ignore, callback) {
    let s = Session.getAlive();

    if (s) {
        let followJson = {
            follower: s.account.username,
            following: following,
            what: ignore ? ['ignore'] : [],
        };
        followJson = ['follow', followJson];
        requireRoleKey(s.account.username, 'posting', function (postingKey) {
            globalLoading.show = true;
            crea.broadcast.customJson(
                postingKey,
                [],
                [s.account.username],
                'follow',
                jsonstring(followJson),
                function (err, result) {
                    globalLoading.show = false;

                    if (callback) {
                        if (err) {
                            callback(err);
                        } else {
                            callback(null, result);
                        }
                    }
                }
            );
        });
    } else if (callback) {
        callback(Errors.USER_NOT_LOGGED);
    }
}

function makeComment(comment, post, parentPost, callback) {
    let session = Session.getAlive();

    if (session && comment.length > 0) {
        requireRoleKey(session.account.username, 'posting', function (postingKey) {
            globalLoading.show = true;
            let parentAuthor = post ? post.parent_author : parentPost.author;
            let parentPermlink = post ? post.parent_permlink : parentPost.permlink;

            let permlink;
            let tags = [];
            if (post) {
                //Reply edit case;
                permlink = post.permlink;
                tags.push(post.metadata.tags[0]);
            } else {
                //New Reply case
                if (parentPost.parent_author) {
                    //Reply of comment
                    permlink = uniqueId();
                } else {
                    //Reply of post/publication
                    permlink = toPermalink(crea.formatter.commentPermlink(parentAuthor, parentPermlink));
                }

                tags.push(parentPost.metadata.tags[0]);
            }

            if (permlink.length > CONSTANTS.TEXT_MAX_SIZE.PERMLINK) {
                permlink = permlink.substring(0, CONSTANTS.TEXT_MAX_SIZE.PERMLINK);
            }

            console.log(permlink.length, parentPermlink.length);
            let metadata = {
                tags: tags,
                app: 'creary',
                version: '1.0.0',
            };
            /*crea.broadcast.comment(postingKey, parentAuthor, parentPermlink, session.account.username, permlink, '', comment, '', jsonstring(metadata), function (err, result) {
                globalLoading.show = false;

                if (!catchError(err)) {
                    postContainer.comment = '';
                    fetchContent();
                }
            });*/
            crea.broadcast.comment(
                postingKey,
                parentAuthor,
                parentPermlink,
                session.account.username,
                permlink,
                '',
                comment,
                '',
                jsonstring(metadata),
                callback
            );
        });
    }
}

function deleteComment(post, session, callback) {
    if (session && post && post.author === session.account.username) {
        requireRoleKey(session.account.username, 'posting', function (postingKey) {
            globalLoading.show = true;
            crea.broadcast.deleteComment(postingKey, post.author, post.permlink, callback);
        });
    }
}

function editComment(comment, post, session, callback) {
    if (session && post) {
        requireRoleKey(session.account.username, 'posting', function (postingKey) {
            globalLoading.show = true;
            crea.broadcast.comment(
                postingKey,
                post.parent_author,
                post.parent_permlink,
                post.author,
                post.permlink,
                post.title,
                comment,
                post.json_metadata,
                callback
            );
        });
    }
}

function makeDownload(event, session, user, post, callback) {
    cancelEventPropagation(event);

    if (session) {
        requireRoleKey(session.account.username, 'active', function (activeKey) {
            globalLoading.show = true;

            let downloadResource = function downloadResource() {
                setTimeout(function () {
                    let authorBuff = Buffer.from(post.author);
                    let permlinkBuff = Buffer.from(post.permlink);
                    let buff = Buffer.concat([authorBuff, permlinkBuff]);
                    let signature = crea.utils.Signature.signBuffer(buff, activeKey);
                    let s64 = signature.toBuffer().toString('base64');
                    crea.api.getDownload(
                        session.account.username,
                        post.author,
                        post.permlink,
                        s64,
                        function (err, result) {
                            globalLoading.show = false;

                            if (!catchError(err)) {
                                let re = /Qm[a-zA-Z0-9]+/;
                                let hash = re.exec(result.resource)[0];
                                console.log(hash); //For .rar, .zip or unrecognized MIME type

                                if (!post.download.type) {
                                    post.download.type = 'application/octet-stream';
                                }

                                //Delete diacritics in filename
                                let normalizedName = post.download.name.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                                let _url =
                                    apiOptions.ipfsd + '/' + post.download.type + '/' + hash + '/' + normalizedName;

                                _url += '?stream=false';

                                hideModal('#modal-download');
                                if (callback) {
                                    console.log('Callback is present');
                                    callback();
                                } else {
                                    console.log('Callback null?', callback);
                                }
                                //Close modal download
                                downloadFile(_url, post.download.name);
                            }
                        }
                    );
                }, 3000);
            };

            let payDownload = function payDownload() {
                crea.broadcast.commentDownload(
                    activeKey,
                    session.account.username,
                    post.author,
                    post.permlink,
                    function (err, result) {
                        if (!catchError(err)) {
                            downloadResource();
                            //fetchContent();
                        } else {
                            globalLoading.show = false;
                        }
                    }
                );
            };

            if (post.download.downloaders.includes(user.name)) {
                //Download paid
                downloadResource();
            } else {
                payDownload();
            }
        });
    } else {
        console.log('NO session', session);
    }
}

function updateUserSession() {
    let session = Session.getAlive();

    if (session) {
        session.login(function (err, account) {
            if (!catchError(err)) {
                let count = 2;

                let onTaskEnded = function onTaskEnded(session, account) {
                    --count;

                    if (count === 0) {
                        creaEvents.emit('crea.session.update', session, account);
                    }
                };

                let followings = [];
                let blockeds = [];
                crea.api.getFollowing(session.account.username, '', 'blog', 1000, function (err, result) {
                    if (!catchError(err)) {
                        result.following.forEach(function (f) {
                            followings.push(f.following);
                        });
                        account.user.followings = followings;
                        onTaskEnded(session, account);
                    }
                });
                crea.api.getFollowing(session.account.username, '', 'ignore', 1000, function (err, result) {
                    if (!catchError(err)) {
                        result.following.forEach(function (f) {
                            blockeds.push(f.following);
                        });
                        account.user.blockeds = blockeds;
                        onTaskEnded(session, account);
                    }
                });
            }
        });
    } else {
        creaEvents.emit('crea.session.update', false);
    }
}

function refreshAccessToken(callback) {
    let now = new Date().getTime();
    let expiration = localStorage.getItem(CREARY.ACCESS_TOKEN_EXPIRATION);
    expiration = isNaN(expiration) ? 0 : expiration;

    if (expiration <= now) {
        let url = apiOptions.apiUrl + '/oauth/v2/token';
        let http = new HttpClient(url);
        let params = {
            grant_type: 'client_credentials',
            client_id: '1_2e5ws1sr915wk0o4kksc0swwoc8kc4wgkgcksscgkkko404g8c',
            client_secret: '3c2x9uf9uwg0ook0kksk8koccsk44w0gg4csos04ows444ko4k',
        };
        http.withCredentials(false)
            .when('done', function (data) {
                localStorage.setItem(CREARY.ACCESS_TOKEN, data.access_token);
                localStorage.setItem(CREARY.ACCESS_TOKEN_EXPIRATION, new Date().getTime() + data.expires_in * 1000);

                if (callback) {
                    callback(data.access_token);
                }
            })
            .post(params);
    } else if (callback) {
        let accessToken = localStorage.getItem(CREARY.ACCESS_TOKEN);
        callback(accessToken);
    }
}

function resizeImage(file, callback) {
    let MAX_PIXEL_SIZE = 500;
    console.log(file);
    const FILE_TYPE_TO_COMPRESS = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif']
    if (FILE_TYPE_TO_COMPRESS.includes(file.type.toLowerCase())) {
        //Only PNG, JPG, JPEG, GIF

        let isGif = file.type.toLowerCase().includes('gif');
        let reader = new FileReader();
        reader.onload = function (event) {
            console.info('Image loaded');
            let compressImage = function (rawImage, compressCallback) {
                //console.log('Compressing image raw:', rawImage)
                let resizer = new Pica();
                let tmpImage = new Image();

                tmpImage.onload = function () {
                    let destCanvas = document.createElement('canvas');
                    if (tmpImage.width <= tmpImage.height && tmpImage.width > MAX_PIXEL_SIZE) {
                        //destCanvas = resizer.createCanvas(MAX_PIXEL_SIZE, Math.round(tmpImage.height * MAX_PIXEL_SIZE / tmpImage.width));
                        destCanvas.width = MAX_PIXEL_SIZE;
                        destCanvas.height = Math.round(tmpImage.height * MAX_PIXEL_SIZE / tmpImage.width);
                    } else if (tmpImage.height <= tmpImage.width && tmpImage.height > MAX_PIXEL_SIZE) {
                        //destCanvas = resizer.createCanvas(Math.round(tmpImage.width * MAX_PIXEL_SIZE / tmpImage.height), MAX_PIXEL_SIZE);
                        destCanvas.height = MAX_PIXEL_SIZE;
                        destCanvas.width = Math.round(tmpImage.width * MAX_PIXEL_SIZE / tmpImage.height);
                    } else if (compressCallback) {
                        //Nothing to do
                        console.log('Nothing to do');
                        compressCallback(false, file);
                        return;
                    }

                    resizer.resize(tmpImage, destCanvas)
                        .then( (result) => {
                            //console.log('resize resulted!', result)
                            return result.toDataURL('image/jpeg', 1);
                            //return resizer.toBlob(result, 'image/jpeg', 0.90)
                        })
                        .then(dataUrl => {
                            //console.log('Blob created', blob)
                            if (compressCallback) {
                                compressCallback(true, dataUrl);
                            }
                        });
                };

                tmpImage.src = rawImage;
            }

            if (isGif) {
                console.debug('Detected gif');
                file.arrayBuffer()
                    .then(fileBuffer => {
                        fileBuffer = arrayBufferToBuffer(fileBuffer);
                        console.log('fileBuffer', Buffer.isBuffer(fileBuffer))
                        let gif = parseGIF(fileBuffer);

                        let dFrames = decompressFrames(gif, true);
                        console.log('DFRAMES', dFrames);

                        let count = 0;
                        let buildGIF = function () {
                            count++;
                            if (count === dFrames.length) {
                                console.debug('Building new gif with compressed frames', Object.keys(compressedFrames), compressedFrames);
                                if (Object.keys(compressedFrames).length > 0) {
                                    let frameValues = [];
                                    for (let x = 0; x < count; x++) {
                                        frameValues.push(compressedFrames[x]);
                                    }

                                    gifShot.createGIF({
                                        images: frameValues
                                    }, obj => {
                                        console.log('Gif created', obj);
                                        if (!obj.error) {
                                            console.debug('New GIF created', obj.image);
                                            if (callback) {
                                                callback(dataURLtoBlob(obj.image));
                                            }
                                        } else {
                                            console.error('Error loading GIF', obj.error);
                                        }
                                    });
                                } else if (callback) {
                                    callback(file);
                                }

                            }
                        }

                        let compressedFrames = {};
                        let tempCanvas = document.createElement('canvas');
                        let tempCtx = tempCanvas.getContext('2d');

                        let fullCanvas = document.createElement('canvas');
                        let fullCtx = fullCanvas.getContext('2d');
                        let frameImageData;

                        for (let x = 0; x < dFrames.length; x++) {
                            let frame = dFrames[x];

                            if (!frameImageData) {
                                tempCanvas.width = frame.dims.width;
                                fullCanvas.width = frame.dims.width;
                                tempCanvas.height = frame.dims.height;
                                fullCanvas.height = frame.dims.height;
                                frameImageData = tempCtx.createImageData(frame.dims.width, frame.dims.height);
                            }

                            frameImageData.data.set(frame.patch);
                            tempCtx.putImageData(frameImageData, 0, 0);
                            fullCtx.drawImage(tempCanvas, frame.dims.left, frame.dims.top);

                            let dataUrl = fullCanvas.toDataURL('image/jpeg', 1);
                            compressImage(dataUrl, (compressed, compressedFrame) => {
                                //console.debug('Frame resized', compressed, compressedFrame);
                                if (compressed) {
                                    compressedFrames[x] = compressedFrame;
                                }

                                buildGIF();
                            });
                        }


                    })

            } else {
                console.log('Non-GIF image');
                compressImage(event.target.result, (compressed, compressedImage) => {
                    //console.log('nGIF', compressed, compressedImage);
                    if (callback) {
                        callback(dataURLtoBlob(compressedImage));
                    }
                })
            }

        };

        reader.readAsDataURL(file);
    } else if (callback) {
        callback(file);
    }
}

function uploadToIpfs(file, maxSize, callback) {
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        if (!maxSize) {
            //If maxSize is undefined means that file format is not allowed
            if (callback) {
                callback(lang.PUBLISH.FILE_FORMAT_NOT_ALLOWED);
            }
        } else if (file.size <= maxSize) {
            let ipfs = Ipfs('https://ipfs.creary.net:5002');
            ipfs.add(file, { pin: true, 'stream-channels': true })
                .then((response) => {
                    let f = new IpfsFile(response.path, file.name, file.type, response.size);
                    callback(null, f);
                })
                .catch((err) => {
                    if (!err) {
                        callback(Errors.UPLOAD_FAIL);
                    } else {
                        callback(err);
                    }
                });
        } else {
            globalLoading.show = false;
            console.error('File', file.name, 'too large. Size:', file.size, 'MAX:', maxSize);

            if (callback) {
                callback(Errors.FILE_TOO_LARGE);
            }
        }
    } else {
        globalLoading.show = false;
    }
}

/**
 *
 * @param {string} url
 * @param {string} filename
 */
function downloadFile(url, filename) {
    let element = document.createElement('a');
    element.setAttribute('href', url);

    //No uncomment, Firefox on OSX do not anything
    //element.setAttribute('target', '_blank');
    element.setAttribute('download', filename); //element.setAttribute('target', '_blank');

    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

function performSearch(search, page = 1, inHome = false, callback) {
    let path = '/search?query=' + encodeURIComponent(search) + '&page=' + page;

    if (inHome) {
        updateUrl(path);
        refreshAccessToken(function (accessToken) {
            let http = new HttpClient(apiOptions.apiUrl + '/searchCreaContent');
            http.setHeaders({
                Authorization: 'Bearer ' + accessToken,
            })
                .when('done', function (response) {
                    let data = jsonify(response).data;

                    for (let x = 0; x < data.length; x++) {
                        data[x].tags = jsonify(data[x].tags);
                    }

                    creaEvents.emit('crea.search.content', data);

                    if (callback) {
                        callback();
                    }
                })
                .when('fail', function (response, textStatus, request) {
                    console.error(response, textStatus, request);
                    catchError(response.error);
                })
                .get({
                    search: search,
                    page: page,
                });
        });
    } else {
        goTo(path);
    }
}

/**
 *
 * @param err
 * @param show
 */
function catchError(err, show = true) {
    if (err) {
        let title;
        let body = [];

        if (err.stack) {
            console.trace(err.stack);
        } else {
            console.error('Error', err);
            err = lang.ERROR[err] || err;

            console.log('Error text', err);
        }

        if (typeof err === 'string') {
            title = err;
        } else {
            if (err.name) {
                title = name;
            } else if (err.TITLE) {
                title = err.TITLE;
            }

            if (err.message) {
                let m = err.message.split(':');
                let message = m[m.length - 1];
                console.error(message);

                //RC Special case
                if (
                    message === ' Account does not have enough flow to vote.' ||
                    message.includes('RC. Please wait to transact, or energize CREA.')
                ) {
                    title = lang.ERROR.INSUFFICIENT_RC.TITLE;
                    body = lang.ERROR.INSUFFICIENT_RC.BODY;
                    console.log(body);
                } else {
                    body.push(message);
                }
            }

            if (err.BODY) {
                body = err.BODY;
            }
        }

        console.log('Show error', show);
        if (show) {
            console.log('Showing alert', title, body);
            showAlert(title, body);
        }

        if (body.length) {
            return body.join(', ');
        } else {
            return title;
        }
    }

    return false;
}

/**
 *
 * @param {string} title
 * @param {...string} body
 */
function showAlert(title, body) {
    let config = {
        title: title,
        body: typeof body === 'string' ? [body] : body,
    };

    console.log(config);
    creaEvents.emit('crea.alert', config);
}

/**
 *
 * @param {string} username
 * @param {string} role
 * @param {boolean} login
 * @param {function} callback
 */
function requireRoleKey(username, role, login, callback) {
    if (typeof login === 'function') {
        callback = login;
        login = false;
    }

    console.log(username, role, callback != null);
    if (callback) {
        let id = randomNumber(0, Number.MAX_SAFE_INTEGER);
        let session = Session.getAlive();

        if (session && session.account.keys[role]) {
            callback(session.account.keys[role].prv, session.account.username);
        } else {
            let listener = function (roleKey, username) {
                if (callback) {
                    callback(roleKey, username);
                }

                creaEvents.off('crea.auth.role.' + id, listener);
            };
            creaEvents.on('crea.auth.role.' + id, listener);
            creaEvents.emit('crea.auth.role', username, role, login, id);
        }
    }
}

export {
    CONSTANTS,
    showBanner,
    goTo,
    showPost,
    showProfile,
    updateUrl,
    toHome,
    resolveFilter,
    isInHome,
    showModal,
    hideModal,
    createBlockchainAccount,
    removeBlockedContents,
    parseAccount,
    parsePost,
    getAccounts,
    getDiscussion,
    recommendPost,
    ignoreUser,
    makeComment,
    deleteComment,
    editComment,
    makeDownload,
    updateUserSession,
    refreshAccessToken,
    resizeImage,
    uploadToIpfs,
    downloadFile,
    performSearch,
    catchError,
    showAlert,
    requireRoleKey,
};
