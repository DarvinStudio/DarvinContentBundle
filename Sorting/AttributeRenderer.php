<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting;

use Darvin\ContentBundle\Form\Type\Sorting\RepositionType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Sorting attribute renderer
 */
class AttributeRenderer implements AttributeRendererInterface
{
    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager CSRF token manager
     * @param \Doctrine\Common\Persistence\ObjectManager                 $om               Object manager
     * @param \Symfony\Component\HttpFoundation\RequestStack             $requestStack     Request stack
     * @param \Symfony\Component\Routing\RouterInterface                 $router           Router
     */
    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        ObjectManager $om,
        RequestStack $requestStack,
        RouterInterface $router
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->om = $om;
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function renderContainerAttr(iterable $target, array $tags = [], ?string $slug = null, array $attr = []): string
    {
        if ($target instanceof AbstractPagination) {
            $request = $this->requestStack->getCurrentRequest();

            if (null === $request
                || 0 !== $request->query->getInt($target->getPaginatorOption(PaginatorInterface::PAGE_PARAMETER_NAME), -1)
                || $request->query->has($target->getPaginatorOption(PaginatorInterface::SORT_FIELD_PARAMETER_NAME))
            ) {
                return $this->renderAttr($attr);
            }
        }

        $objects = [];

        foreach ($target as $key => $object) {
            $objects[$key] = $object;
        }
        if (empty($objects)) {
            return $this->renderAttr($attr);
        }
        if (null === $slug) {
            $request = $this->requestStack->getCurrentRequest();

            if (null !== $request) {
                $params = $request->attributes->get('_route_params', []);

                if (isset($params['slug'])) {
                    $slug = $params['slug'];
                }
            }
        }
        if (null === $slug && empty($tags)) {
            return $this->renderAttr($attr);
        }

        $first = reset($objects);

        return $this->renderAttr(array_merge($attr, [
            'class'                 => trim(sprintf('%s js-content-sortable', $attr['class'] ?? '')),
            'data-reposition-url'   => $this->router->generate('darvin_content_sorting_reposition'),
            'data-reposition-class' => base64_encode(ClassUtils::getClass($first)),
            'data-reposition-csrf'  => $this->csrfTokenManager->getToken(RepositionType::CSRF_TOKEN_ID)->getValue(),
            'data-reposition-slug'  => $slug,
            'data-reposition-tags'  => $tags,
        ]));
    }

    /**
     * {@inheritDoc}
     */
    public function renderItemAttr($object, array $attr = []): string
    {
        $ids = $this->om->getClassMetadata(ClassUtils::getClass($object))->getIdentifierValues($object);

        return $this->renderAttr(array_merge($attr, [
            'data-id' => reset($ids),
        ]));
    }

    /**
     * @param array $attr Attributes
     *
     * @return string
     */
    private function renderAttr(array $attr): string
    {
        $parts = [];

        foreach ($attr as $name => $value) {
            if (is_array($value)) {
                if (empty($value)) {
                    continue;
                }

                $value = json_encode($value);
            }
            if (null !== $value) {
                $parts[] = sprintf('%s="%s"', $name, htmlspecialchars((string)$value));
            }
        }
        if (empty($parts)) {
            return '';
        }

        return sprintf(' %s', implode(' ', $parts));
    }
}
