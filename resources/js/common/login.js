/**
 * Created by ander on 29/09/18.
 */

import Session from '../lib/session';
import { catchError, CONSTANTS } from "./common";
import errors from '../lib/error';

function startLogin() {
    let username = $('#login-username').val();
    let password = $('#login-password').val();
    login(username, password);
    return false;
}

/**
 *
 * @param {string} username
 * @param {string} password
 * @param callback
 */
function login(username, password, callback) {
    //Check roles;
    let session;

    if (crea.auth.isWif(password)) {
        //Unknown role
        session = Session.create(username, password, null);
    } else {
        //Default role (posting)
        session = Session.create(username, password);
    }

    session.login(function (err, account) {
        if (CONSTANTS.ACCOUNT.BLOCKED.includes(username.toLowerCase())) {
            err = errors.USER_LOGIN_ERROR;
            session.logout();
        }

        if (err) {
            if (callback) {
                callback(err);
            }
        } else {
            session.save();
            let count = 2;

            let onTaskEnded = function onTaskEnded(session, account) {
                --count;

                if (count === 0) {
                    creaEvents.emit('crea.session.login', session, account);

                    if (callback) {
                        callback(null, session, account);
                    }
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
}

function logout() {
    Session.getAlive().logout();

    creaEvents.emit('crea.session.logout');
}

export {
    startLogin, login, logout
};
