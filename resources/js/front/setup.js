/**
 * Created by ander on 11/10/18.
 */
import Errors from "../lib/error";
import Session from "../lib/session";
import { cancelEventPropagation } from '../lib/util';
import { catchError } from "../common/common";
import HttpClient from "../lib/http";

(function () {

    window.addEventListener('load', function (ev) {
        //console.log("Resources loaded");
        window.creaEvents.emit('crea.content.loaded');
    });

    document.addEventListener('DOMContentLoaded', function () {
        //console.log("DOM loaded");
        window.creaEvents.emit('crea.content.prepare');

        let session = Session.getAlive();

        if (session) {
            session.login(function (err, account) {
                if (err) {
                    console.error(err);

                    if (err === Errors.USER_LOGIN_ERROR) {
                        session.logout();
                    }

                    creaEvents.emit('crea.session.login', false);
                } else {
                    let count = 2;
                    let onTaskEnded = function onTaskEnded(session, account) {
                        --count;

                        if (count === 0) {
                            //console.log('Emitting session')
                            creaEvents.emit('crea.session.login', session, account);
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
            creaEvents.emit('crea.session.login', false);
        }
    });

    function updateCookies(session, account) {
        //console.log('Cookie session', session, account);
        if (session) {
            CreaCookies.set('creary.username', session.account.username, { expires: 365 });
        } else {
            CreaCookies.remove('creary.username')
        }

        let lang = navigator.language.toLowerCase().split('-')[0];
        if (account && account.user.metadata.lang) {
            lang = account.user.metadata.lang;
        }

        //Set language
        CreaCookies.set('creary.language', lang, { expires: 365 });

        //console.log(navLang, CreaCookies.get('creary.language'));
    }

    let unreadNotifications = null;
    let allNotifications = null;

    function fetchUnreadNotifications(session, account) {
        if (session) {
            let url = `/~api/notification/@${session.account.username}/unread`;
            let httpClient = new HttpClient(url);
            httpClient.on('done' + httpClient.id, function (data) {
                unreadNotifications =  JSON.parse(data);
                //console.log('Notifications', unreadNotifications);

                //creaEvents.emit('crea.notifications.all', notifications);
                creaEvents.emit('crea.notifications.unread', unreadNotifications);
            });
            httpClient.get({});
        }
    }

    function fetchAllNotifications(session, account) {
        if (session) {
            let url = `/~api/notification/@${session.account.username}`;
            if (allNotifications) {
                //If next page is not provided, not call
                if (!allNotifications.next_page_url) {
                    creaEvents.emit('crea.notifications.all', false);
                    return false;
                }

                url = allNotifications.next_page_url || url;
            }
            let httpClient = new HttpClient(url);
            httpClient.on('done' + httpClient.id, function (data) {
                allNotifications =  JSON.parse(data);
                //console.log('Notifications', allNotifications);

                creaEvents.emit('crea.notifications.all', allNotifications);
                //creaEvents.emit('crea.notifications.unread', unread);
            });
            httpClient.get({});
        }
    }

    function preSetup(session, account) {
        updateCookies(session, account);
        fetchAllNotifications(session, account);
        fetchUnreadNotifications(session, account);
    }

    creaEvents.on('crea.notifications.update', function (session, account) {
        fetchAllNotifications(session, account);
        fetchUnreadNotifications(session, account);
    });

    creaEvents.on('crea.notifications.more', function (session, account) {
        console.log('Getting more notifications');
        fetchAllNotifications(session, account);
    });

    creaEvents.on('crea.session.login', function (session, account) {
        globalLoading.show = false;
        preSetup(session, account);
    });

    creaEvents.on('crea.session.update', preSetup);

    creaEvents.on('crea.session.logout', preSetup);

    creaEvents.on('crea.modal.ready', function (remove) {
        setTimeout(function () {

            if (remove) {
                //Remove login modals to prevent id conflicts
                $('.all-page-modals #modal-login').remove();
                $('.all-page-modals #modal-login-d').remove();
            }

            mr.modals.documentReady($);
        }, 500);

    });

    creaEvents.on('crea.dom.ready', function () {
        //console.log('DOM ready received');
        $.holdReady(false);
        $(window).scroll(function (event) {
            let scrollHeight = $(document).height();
            let scrollPosition = $(window).height() + $(window).scrollTop();
            let bottom = (scrollHeight - scrollPosition) / scrollHeight;

            if (bottom <= 0.01) {
                // when scroll to bottom of the page
                creaEvents.emit('crea.scroll.bottom');
            }
        }); //Inputs length validations;

        $('input, textarea').each(function (index, element) {
            let limit = parseInt($(element).attr('maxlength'));
            $(element).keypress(function (e) {
                let length = e.target.value.length;

                if (e.charCode > 0 && length === limit) {
                    cancelEventPropagation(e);
                }
            });
        });
        $('[data-toggle="popover"]').popover();

        //Build modals
        console.log('Emitting', 'crea.modal.ready', 'event');
        creaEvents.emit('crea.modal.ready');
    });


})();
