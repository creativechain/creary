import Avatar from "../components/Avatar";
import ButtonFollow from "../components/ButtonFollow";
import {getParameterByName, jsonify} from "../lib/util";
import HttpClient from "../lib/http";
import Username from "../components/Username";

const SEARCH_LIMIT = 20;

(function () {

    Vue.component('avatar', Avatar)
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
                    http: null
                },
                methods: {

                }
            })
        } else {
            accountList.search = getParameterByName('q')
            accountList.session = session;
            accountList.account = account;
        }

        getAllResults();

        console.log('list updated!');
    }

    function getAllResults() {

        let url = apiOptions.apiCrea + '/accounts/search'
        let params = {
            search: accountList.search,
            limit: SEARCH_LIMIT
        }

        if (accountList.http) {
            url = accountList.http.next_page_url;
            params = null;
        }

        if (url) {
            let http = new HttpClient(url);
            http.setHeaders({
                accept: 'application/json'
            })

            http.on('done' + http.id, function (response) {
                let data = jsonify(response);

                accountList.http = data;
                accountList.items.push(...data.data);
                accountList.$forceUpdate();
            })

            http.when('fail', function (jqXHR, textStatus, errorThrown) {
                console.error(jqXHR, textStatus, errorThrown);
            });

            http.get(params);
        }


    }

    creaEvents.on('crea.session.login', function (s, a) {
        setUp(s, a);
    });

    creaEvents.on('crea.session.logout', function (s, a) {
        setUp(false, false);
    });

    let onScrollCalling;
    creaEvents.on('crea.scroll.bottom', function () {
        if (!onScrollCalling) {
            onScrollCalling = true;
        }
    })
})()
