(() => {
    const SELECTOR = {
        container: '.js-content-sortable[data-reposition-url][data-class][data-csrf-token]',
        item:      '[data-id]'
    };

    let init;
    (init = function (context) {
        $(context || 'body').find(SELECTOR.container).sortable({
            items:  SELECTOR.item,
            update: (e, ui) => {
                const $container = ui.item.closest(SELECTOR.container);

                const $items = $container.find(SELECTOR.item);

                if (!$items.length) {
                    return;
                }

                const options = $container.data();

                let data = {
                    'class': options.class,
                    ids:     $items.map((i, item) => {
                        return $(item).data('id');
                    }).get(),
                    _token: options.csrfToken
                };

                if (options.slug) {
                    data.slug = options.slug;
                }

                $.ajax({
                    url:  options.repositionUrl,
                    type: 'post',
                    data: data
                }).done((data) => {
                    console.log(data);
                }).fail(App.onAjaxFail);
            }
        });
    })();
    $(document).on('app.ajax.html', (e, args) => {
        init(args.$html);
    });
})();
