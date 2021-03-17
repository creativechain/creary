/**
 * Created by ander on 25/09/18.
 */

import R from '../lib/resources';
import Session from '../lib/session';
import mqtt from 'mqtt';
import HttpClient from '../lib/http';
import { jsonify, getPathPart, isUserFeed, isSmallScreen, cancelEventPropagation } from '../lib/util';
import { login, logout } from '../common/login';
import {
    catchError,
    performSearch,
    isInHome,
    hideModal,
    goTo,
    resolveFilter,
    updateUrl,
    parsePost,
    refreshAccessToken,
} from '../common/common';

import Errors from '../lib/error';

import Avatar from '../components/Avatar';
import { CommentsApi } from '../lib/creary-api';

(function () {
    Vue.component('avatar', Avatar);

    let navbarContainer;
    let canLoadBootstrapScripts = false;

    let navbarRightMenu = new Vue({
        el: '#navbar-right-menu',
        data: {
            lang: lang,
        },
    });

    /**
     *
     * @param {Session} session
     * @param userData
     */
    function updateNavbarSession(session, userData) {
        if (!navbarContainer) {
            console.log('Creating navbar...');
            navbarContainer = new Vue({
                el: '#navbar-container',
                name: 'navbar-container',
                data: {
                    lang: lang,
                    session: session,
                    user: userData ? userData.user : {},
                    nav: getPathPart(),
                    loginForm: {
                        xs: isSmallScreen(),
                        username: {
                            error: null,
                            value: '',
                        },
                        password: {
                            error: null,
                            value: '',
                        },
                    },
                    unreadNotifications: 0,
                },
                mounted: function mounted() {
                    //this.applyRightMenuEvents($);
                    $('#modal-login').parent().removeAttr('modal-attached');
                    mr.notifications.documentReady($);
                    console.log('Navbar cmounted!');
                },
                methods: {
                    applyRightMenuEvents: function applyRightMenuEvents($) {
                        mr.notifications.documentReady($);
                        mr.tabs.documentReady($);
                        mr.toggleClass.documentReady($);
                        console.log('applying menus');
                    },
                    closeLogin: function closeLogin() {
                        hideModal('#modal-login');
                        hideModal('#modal-login-d');
                    },
                    logout: logout,
                    login: function (event) {
                        cancelEventPropagation(event);
                        let that = this;

                        if (!this.loginForm.username.error) {
                            login(this.loginForm.username.value, this.loginForm.password.value, function (err) {
                                console.log(err);
                                if (err) {
                                    console.error(err);

                                    if (err === Errors.USER_LOGIN_ERROR) {
                                        that.loginForm.password.error = that.lang.ERROR[err].TITLE;
                                        console.error(that.lang.ERROR[err]);
                                    } else {
                                        that.loginForm.password.error = that.lang.ERROR.UNKNOWN_ERROR;
                                        console.error(that.lang.ERROR[err]);
                                    }
                                } else {
                                    that.closeLogin();
                                }
                            });
                        }
                    },
                    isUserFeed: isUserFeed,
                    checkUsername: checkUsername,
                    goTo: goTo,
                    getDefaultAvatar: R.getAvatar,
                    retrieveNowContent: retrieveNewContent,
                    retrieveTrendingContent: retrieveTrendingContent,
                    retrieveHotContent: retrieveHotContent,
                    retrievePromotedContent: retrievePromotedContent,
                },
            });
        } else {
            navbarContainer.session = session;
            navbarContainer.user = userData ? userData.user : {};
        }
    }

    function checkUsername(event) {
        let target = event.target;
        let username = target.value.toLowerCase();
        navbarContainer.loginForm.username.value = username; //console.log(target.value, username);

        if (!crea.utils.validateAccountName(username)) {
            let accounts = [username];
            //console.log("Checking", accounts);
            crea.api.lookupAccountNames(accounts, function (err, result) {
                if (err) {
                    console.error(err);
                    navbarContainer.loginForm.username.error = lang.ERROR.INVALID_USERNAME;
                } else if (result[0] == null) {
                    navbarContainer.loginForm.username.error = lang.ERROR.USERNAME_NOT_EXISTS;
                } else {
                    navbarContainer.loginForm.username.error = null;
                }
            });
        } else {
            navbarContainer.loginForm.username.error = lang.ERROR.INVALID_USERNAME;
        }
    }

    function retrieveContent(event, urlFilter) {
        if (isInHome()) {
            cancelEventPropagation(event);
        }

        let filter = resolveFilter(urlFilter);
        updateUrl(urlFilter);

        crea.api.getState(filter, function (err, urlState) {
            if (!catchError(err)) {
                if (isUserFeed()) {
                    let noFeedContent = function noFeedContent() {
                        //User not follows anything, load empty content
                        urlState.content = {};
                        creaEvents.emit('crea.posts', urlFilter, filter, urlState);
                    };

                    let commentsApi = new CommentsApi();
                    let adult = navbarContainer.user.metadata.adult_content === 'hide' ? 0 : 1;
                    commentsApi.feed(null, adult, 20, function (err, result) {
                        if (!catchError(err)) {
                            if (result.to) {
                                let count = result.to;

                                let onContentFetched = function onContentFetched() {
                                    count--;

                                    if (count <= 0) {
                                        creaEvents.emit('crea.posts', urlFilter, filter, urlState);
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
                    });
                } else {
                    creaEvents.emit('crea.posts', urlFilter, filter, urlState);
                }
            }
        });
    }

    function retrieveNewContent(event) {
        retrieveContent(event, '/now');
    }

    function retrieveTrendingContent(event) {
        retrieveContent(event, '/popular');
    }

    function retrieveHotContent(event) {
        retrieveContent(event, '/skyrockets');
    }

    function retrievePromotedContent(event) {
        retrieveContent(event, '/promoted');
    }

    /**
     *
     * @param {Session} session
     */
    function prepareNotifClient(session) {
        if (session) {
            let host = location.host;
            let port = window.wsPort;

            let account = session.getAccount();
            let username = session.account.username;

            let options = {
                host,
                port,
                clientId: username,
                username: username,
                password: account.getSignature(),
                clean: true,
                protocol: 'wss',
                //protocolId: 'MQTT',
            };

            console.log('MQTT options', options);
            const mqttClient = mqtt.connect(options);
            mqttClient.on('connect', function (connack) {
                console.log('MQTT connected!');

                //Subcribe to notifications messages
                mqttClient.subscribe(`${username}/notification`, function (err, granted) {
                    console.log('Subscription topic', err, granted);
                });
            });

            mqttClient.on('message', function (topic, message, packet) {
                console.log('Message received', topic, message.toString('utf8'), packet);
                creaEvents.emit('crea.notifications.update', session);
            });
        }
    }

    function enableRightMenu() {
        if (canLoadBootstrapScripts) {
            setTimeout(function () {
                //console.log('Activating right menu...');
                mr.notifications.documentReady($);
            }, 1e3);
        }
    }

    function enableProfileMenu() {
        if (canLoadBootstrapScripts) {
            setTimeout(function () {
                //console.log('Activating profile menu...');
                mr.toggleClass.documentReady($);
            }, 1e3);
        }
    }

    creaEvents.on('crea.notifications.unread', function (unreadNotifications) {
        navbarContainer.unreadNotifications = unreadNotifications.total;
        navbarContainer.$forceUpdate();
    });

    creaEvents.on('crea.posts', function () {
        navbarContainer.nav = getPathPart();
    });

    creaEvents.on('crea.session.update', function (session, account) {
        updateNavbarSession(session, account);
    });

    creaEvents.on('crea.session.login', function (session, account) {
        updateNavbarSession(session, account);
        if (window.mqtt_enable) {
            prepareNotifClient(session);
        }

        enableRightMenu();
        enableProfileMenu();
    });

    creaEvents.on('crea.session.logout', function () {
        updateNavbarSession(false, false);
        //console.log('Emitting', 'crea.modal.ready', 'event');
        creaEvents.emit('crea.modal.ready', true);

        enableRightMenu();
    });

    creaEvents.on('crea.dom.ready', function () {
        canLoadBootstrapScripts = true;
    });

    creaEvents.on('crea.content.filter', function (filter) {
        if (!filter.startsWith('/')) {
            filter = '/' + filter;
        }

        retrieveContent(null, filter);
    });
})();
