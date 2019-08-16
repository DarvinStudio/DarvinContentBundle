(() => {
    const SELECTOR = {
        container: '.js-content-sortable',
        item:      '[data-id]'
    };

    let init;
    (init = function (context) {
        $(context || 'body').find(SELECTOR.container).sortable({
            items:  SELECTOR.item,
            update: (e, ui) => {
                let $items = ui.item.closest(SELECTOR.container).find(SELECTOR.item),
                    ids    = [];

                if ($items.length > 0) {
                    ids = $items.map((i, item) => {
                        return $(item).data('id');
                    }).get();
                }
            }
        });
    })();
    $(document).on('app.ajax.html', (e, args) => {
        init(args.$html);
    });
})();
