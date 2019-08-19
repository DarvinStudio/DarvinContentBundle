(() => {
    const SELECTOR = {
        container: '.js-content-sortable[data-reposition-url][data-reposition-class][data-reposition-csrf]',
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
                    'class': options.repositionClass,
                    ids:     $items.map((i, item) => {
                        return $(item).data('id');
                    }).get(),
                    _token: options.repositionCsrf
                };

                for (let name of [
                    'slug',
                    'tags',
                    'offset',
                ]) {
                    let optionName = 'reposition' + name.charAt(0).toUpperCase() + name.slice(1);

                    if (options[optionName]) {
                        data[name] = options[optionName];
                    }
                }

                $.ajax({
                    url:  options.repositionUrl,
                    type: 'post',
                    data: data
                }).done((data) => {
                    App.notify(data.message, data.success ? 'success' : 'error');
                }).fail(App.onAjaxFail);
            }
        });
    })();
    $(document).on('app.ajax.html', (e, args) => {
        init(args.$html);
    });
})();
