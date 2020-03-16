/**
 * Created by ander on 7/01/19.
 */
import Vue from 'vue';
import VueLazyload from "vue-lazyload";

(function () {
    Vue.use(VueLazyload);

    let errorContainer;

    function setUp() {
        if (!errorContainer) {
            errorContainer = new Vue({
                el: '#error-container',
                data: {
                    lang: lang,
                    url: window.location.pathname
                }
            });
        }
    }

    creaEvents.on('crea.session.login', function () {
        setUp();
    });
})();
