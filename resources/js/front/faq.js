/**
 * Created by ander on 28/12/18.
 */
import Vue from 'vue';
import { linkfy, toPermalink } from "../lib/util";

(function () {
    let faqContainer;

    function setUp() {
        faqContainer = new Vue({
            el: '#faq-container',
            data: {
                lang: lang,
                faq: faq
            },
            methods: {
                linkfy: linkfy,
                toPermalink: toPermalink
            }
        });
        creaEvents.emit('crea.dom.ready');
    }

    creaEvents.on('crea.session.login', function (s, a) {
        setUp();
    });
})();
