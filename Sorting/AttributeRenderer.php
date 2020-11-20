<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Sorting;

use Darvin\ContentBundle\Security\Voter\Sorting\RepositionVoter;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Sorting attribute renderer
 */
class AttributeRenderer implements AttributeRendererInterface
{
    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \Doctrine\Persistence\ObjectManager
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
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker Authorization checker
     * @param \Doctrine\Persistence\ObjectManager                                          $om                   Object manager
     * @param \Symfony\Component\HttpFoundation\RequestStack                               $requestStack         Request stack
     * @param \Symfony\Component\Routing\RouterInterface                                   $router               Router
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ObjectManager $om,
        RequestStack $requestStack,
        RouterInterface $router
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->om = $om;
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function renderContainerAttr(iterable $target, array $tags = [], ?string $slug = null, array $attr = []): string
    {
        return $this->renderAttr($this->collectContainerAttr($target, $tags, $slug, $attr));
    }

    /**
     * {@inheritDoc}
     */
    public function renderItemAttr($object, array $attr = []): string
    {
        $class = ClassUtils::getClass($object);

        $ids = $this->om->getClassMetadata($class)->getIdentifierValues($object);

        if ($this->authorizationChecker->isGranted(RepositionVoter::REPOSITION, $class)) {
            $attr['data-id'] = reset($ids);
        }

        return $this->renderAttr($attr);
    }

    /**
     * @param iterable    $target Target
     * @param array       $tags   Tags
     * @param string|null $slug   Slug
     * @param array       $attr   Attributes
     *
     * @return array
     */
    private function collectContainerAttr(iterable $target, array $tags, ?string $slug, array $attr): array
    {
        $objects = [];

        foreach ($target as $key => $object) {
            $objects[$key] = $object;
        }
        if (empty($objects)) {
            return $attr;
        }

        $first = reset($objects);

        $class = ClassUtils::getClass($first);

        if (!$this->authorizationChecker->isGranted(RepositionVoter::REPOSITION, $class)) {
            return $attr;
        }
        if ($target instanceof AbstractPagination) {
            $request = $this->requestStack->getCurrentRequest();

            if (null === $request) {
                return $attr;
            }
            if (0 !== $request->query->getInt($target->getPaginatorOption(PaginatorInterface::PAGE_PARAMETER_NAME), -1)) {
                return $attr;
            }
            if ($request->query->has($target->getPaginatorOption(PaginatorInterface::SORT_FIELD_PARAMETER_NAME))) {
                return $attr;
            }
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
            return $attr;
        }

        return array_merge($attr, [
            'class'                 => trim(sprintf('%s js-content-sortable', $attr['class'] ?? '')),
            'data-reposition-class' => base64_encode($class),
            'data-reposition-slug'  => $slug,
            'data-reposition-tags'  => $tags,
            'data-reposition-url'   => $this->router->generate('darvin_content_sorting_reposition'),
        ]);
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
