import Avatar from '../components/Avatar';
import ButtonFollow from '../components/ButtonFollow';
import {getParameterByName} from '../lib/util';
import Username from '../components/Username';
import {updateUserSession} from '../common/common';
import {AccountsApi} from '../lib/creary-api';

(function () {
    const SEARCH_LIMIT = 20;

    Vue.component('avatar', Avatar);
    Vue.component('btn-follow', ButtonFollow);
    Vue.component('username', Username);

    let accountList;

    function setUp(session, account) {
        if (!accountList) {
            accountList = new Vue({
                el: '#accounts-list',
                name: 'accounts-list',
                data: {
                    session: session,
                    account: account,
                    lang: lang,
                    search: getParameterByName('q'),
                    limit: SEARCH_LIMIT,
                    items: [],
                    http: null,
                },
                methods: {
                    onFollow: function () {
                        updateUserSession();
                    },
                },
            });

            setTimeout(function () {
                creaEvents.emit('crea.dom.ready');
            }, 200);
        } else {
            accountList.search = getParameterByName('q');
            accountList.session = session;
            accountList.account = account;
        }

        console.log('list updated!');
    }

    function preSetup(session, account) {
        setUp(session, account);
        getAllResults();

        setTimeout(function () {
            console.log('Activating profile menu...');
            mr.dropdowns.documentReady($);
        }, 1e3);

        setTimeout(function () {
            console.log('Activating right menu...');
            mr.notifications.documentReady($);
        }, 1e3);
    }

    function getAllResults() {
        let hasPrevQuery = accountList.http && accountList.http.next_page_url;

        let accountApi = new AccountsApi();
        let onResponse = function (err, response) {
            onScrollCalling = false;
            if (err) {
                console.error('Error getting accounts', err);
            } else {
                accountList.http = response;
                accountList.items.push(...response.data);
                accountList.$forceUpdate();
            }
        };
        if (accountList.http) {
            if (hasPrevQuery) {
                accountApi.get(accountList.http.next_page_url, onResponse);
            }

            //No query if not has prev query
        } else {
            accountApi.search(accountList.search, SEARCH_LIMIT, onResponse);
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

    let onScrollCalling = false;
    creaEvents.on('crea.scroll.bottom', function () {
        console.log('On Scroll', onScrollCalling);
        if (!onScrollCalling) {
            onScrollCalling = true;
            getAllResults();
        }
    });
})();
