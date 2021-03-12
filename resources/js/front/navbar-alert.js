import Countdown from '../components/Countdown';
import { isBreakpoint } from '../lib/design';
import { cancelEventPropagation } from '../lib/util';

(function () {
    Vue.component('countdown', Countdown);

    let navbarAlert;

    let elId = isBreakpoint('xs') ? '#navbar-alert-mobile' : '#navbar-alert-desktop';

    console.log('Loading element', elId);
    function setTup() {
        try {
            if (!navbarAlert) {
                navbarAlert = new Vue({
                    el: elId,
                    name: 'navbar-alert',
                    data: {
                        lang: lang,
                        closed: false,
                    },
                    mounted: function () {
                        console.log('Navbar alert mounted');
                    },
                    methods: {
                        closeAlert: function (event) {
                            cancelEventPropagation(event);
                            this.closed = true;
                        },
                    },
                });
                console.log('Alert initialized!', navbarAlert);
            }
        } catch (e) {
            console.error(e);
        }
    }

    creaEvents.on('crea.modal.ready', function () {
        console.log('Initializing countdown...');
        setTup();
    });
})();
