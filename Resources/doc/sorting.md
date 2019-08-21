Sorting
=======

## Enabling functionality

1. Enable functionality in package configuration:

    ```yaml
    # config/packages/darvin_content.yaml
    darvin_content:
        sorting:
            enabled: true
    ```

2. Add sorting scripts to your template:

    ```twig
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ asset('bundles/darvincontent/scripts/sorting.js') }}"></script>
    ```

## Sorting paginated results

1. Install "beberlei/doctrineextensions":

    ```shell
    $ composer require beberlei/doctrineextensions
    ```

2. Enable "FIELD" DQL function:

    ```yaml
    # config/packages/doctrine.yaml
    doctrine:
        orm:
            dql:
                string_functions:
                    field: DoctrineExtensions\Query\Mysql\Field
    ```

3. Add order by clause to your query builder **before** paginating:

    ```php
    $qb = $this->getProductRepository()->createBuilderForCatalogPage($catalog->getTreePath());

    $this->get('darvin_content.sorting.sorter')->addOrderByClause($qb);

    $pagination = $this->getPaginator()->paginate($qb);
    ```

4. Render HTML attributes required by sorting scripts:

    ```twig
    <ul{{ content_sort_attr(pagination) }}>
        {% for product in products %}
            <li{{ content_sort_item_attr(product) }}>

                {% include '@DarvinECommerce/product/card.html.twig' %}

            </li>
        {% endfor %}
    </ul>
    ```

5. Go to zero page of your pagination and enjoy.

## Sorting generic iterable

1. Render HTML attributes required by sorting scripts and apply "content_sort" Twig filter to your iterable:

    ```twig
    <ul{{ content_sort_attr(products) }}>
        {% for product in products|content_sort %}
            <li{{ content_sort_item_attr(product) }}>

                {% include '@DarvinECommerce/product/card.html.twig' %}

            </li>
        {% endfor %}
    </ul>
    ```
   
2. Enjoy.
