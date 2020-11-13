let cEvent = 'crea.content.loaded';
creaEvents.on(cEvent, function () {
    console.log('NavFilter cEvent', cEvent);
    mr.sliders.documentReady($);
})
