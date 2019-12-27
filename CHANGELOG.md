7.1.0:

- Allow to throw HTTP exceptions (instances of "Symfony\Component\HttpKernel\Exception\HttpException") in widget classes.

- Allow to redirect by throwing "Darvin\ContentBundle\Widget\Embedder\Exception\RedirectException" in widget classes.

7.1.1: Add "rel='nofollow'" to "all" link in pagination.

7.1.2: Do not whitelist pagination query parameters in the canonical URL generator.

7.1.3: Add "alternate_links()" macro.

7.1.4: Save new slug map items in the "post flush" event instead of "post persist".

7.1.5: Filterer: allow to filter by "many-to-many" associations.

7.2.0: Dispatch filterer build constraint event.

7.2.1: Allow to blacklist object classes in "Darvin\ContentBundle\Repository\SlugMapItemRepository::getBySlugsChildren()".

7.2.2: Init commands only in "dev" environment.

7.3.0:

- increase database limits for meta tags;

- fix create translations command.

7.3.2: Remove non-breaking spaces from page number macro translations.

7.3.3: Force make services public by default.

7.4.0: Add "nav_links()" macro.

7.4.3: Allow to disable canonical URL whitelisted parameter:

```yaml
darvin_content:
    canonical_url:
        parameter_whitelist:
            catalog_filter: false
```

8.0.0:
 
- Remove "darvin_content_content_show" route.

- Add "allowPageNumberExceed" paginator option.

8.1.0: Add [sorting](Resources/doc/sorting.md) functionality.

8.1.1: Support entity inheritance in sorting functionality.

8.2.0: Replace paginator with custom one with "Show All" functionality.

8.2.1: Add "content_locale_switcher()" Twig function.

8.2.5: Allow translation locale property to be null on left join in translation joiner.

8.2.7: Make front controller a service.

8.3.0:
 
- Reorganize abstract content controller.

- Make all services private.

8.3.3: Change method of checking translation entity's emptiness.

8.3.6: Use "object" type hint.

8.3.7: Register interfaces for autoconfiguration.

8.4.0: Add property embedder.

8.4.1: Make meta tags nullable.

8.4.2: Add meta tag provider.

8.4.3: Add meta template models.

8.4.4: Macros for empty content: empty_message

8.5.0: Add autocomplete functionality.

8.5.1: Allow to secure autocomplete functionality.
