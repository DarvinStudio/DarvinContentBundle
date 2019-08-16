(() => {
    let init;
    (init = function (context) {
        $(context || 'body').find('.js-content-sortable').sortable();
    })();
})();
