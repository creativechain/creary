
(function () {
    creaEvents.on('crea.session.login', function (session) {
        setTimeout(function () {
            creaEvents.emit('crea.dom.ready');
        }, 1000);
    })
})();
