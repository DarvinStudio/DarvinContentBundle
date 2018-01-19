7.1.0:

- Allow to throw HTTP exceptions (instances of "Symfony\Component\HttpKernel\Exception\HttpException") in widget classes.

- Allow to redirect by throwing "Darvin\ContentBundle\Widget\Embedder\Exception\RedirectException" in widget classes.

7.1.1: Add "rel='nofollow'" to "all" link in pagination.

7.1.2: Do not whitelist pagination query parameters in the canonical URL generator.
