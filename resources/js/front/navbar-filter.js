let cEvent = 'crea.modal.loaded';
creaEvents.on(cEvent, function () {
    console.log('NavFilter cEvent', cEvent);
    mr.sliders.documentReady($);
})
