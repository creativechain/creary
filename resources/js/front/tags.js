import { catchError } from "../common/common";

(function () {
    let tagsContainer;

    function setUp(state, session, account) {
        if (!tagsContainer) {
            tagsContainer = new Vue({
                el: '#tags-explorer',
                data: {
                    lang: lang,
                    session: session,
                    account: account,
                    state: state
                }
            });
        } else {
            tagsContainer.session = session;
            tagsContainer.account = account;
            tagsContainer.state = state;
        }

        creaEvents.emit('crea.dom.ready');
    }

    function fetchTags(session, account) {
        crea.api.getState('/tags', function (err, result) {
            if (!catchError(err)) {
                setUp(result, session, account);
            }
        });
    }

    creaEvents.on('crea.session.login', function (session, account) {
        fetchTags(session, account);
    });
})();
