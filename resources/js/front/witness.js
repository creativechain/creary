/**
 * Created by ander on 7/11/18.
 */
import Vue from 'vue';
import { crea } from '../common/conf';
import { updateUserSession, catchError } from "../common/common";
import VueLazyload from "vue-lazyload";

//Import components
import WitnessLike from "../components/WitnessLike";

(function () {
    Vue.use(VueLazyload);

    Vue.component('witness-like', WitnessLike);
    let witnessContainer;

    function updateWitnessView(session, account, state) {
        if (!witnessContainer) {
            witnessContainer = new Vue({
                el: '#witnesses',
                data: {
                    lang: lang,
                    session: session,
                    account: account,
                    state: state
                },
                methods: {
                    onVote: function onVote() {
                        updateUserSession();
                    }
                }
            });
        } else {
            witnessContainer.session = session;
            witnessContainer.account = account;
            witnessContainer.state = state;
        }

        creaEvents.emit('crea.dom.ready', 'publish');
    }

    function fetchWitness(session, account) {
        crea.api.getState('/~witnesses', function (err, result) {
            if (!catchError(err)) {
                let wKeys = Object.keys(result.witnesses);
                wKeys.sort(function (w1, w2) {
                    return result.witnesses[w2].votes - result.witnesses[w1].votes;
                });
                result.ordered_witnesses = wKeys;
                updateWitnessView(session, account, result);
            }
        });
    }

    function fetchUserAccount(session, account) {
        if (session) {
            crea.api.getAccounts([session.account.username], function (err, result) {
                if (err) {
                    console.error(err);
                    fetchWitness(session, account);
                } else {
                    let accnt = result[0];
                    account.user.witness_votes = accnt.witness_votes;
                    fetchWitness(session, account);
                }
            });
        } else {
            fetchWitness(session, account);
        }
    }

    creaEvents.on('crea.session.login', function (session, account) {
        fetchUserAccount(session, account);
    });

    creaEvents.on('crea.session.update', function (session, account) {
        fetchUserAccount(session, account);
    });

})();
