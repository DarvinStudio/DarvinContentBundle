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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Sorting attribute renderer
 */
class AttributeRenderer implements AttributeRendererInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager     $om           Object manager
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack Request stack
     */
    public function __construct(ObjectManager $om, RequestStack $requestStack)
    {
        $this->om = $om;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function renderContainerAttr(array $objects, array $attr = []): string
    {
        if (empty($objects)) {
            return $this->renderAttr($attr);
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return $this->renderAttr($attr);
        }

        $routeParams = $request->attributes->get('_route_params', []);

        if (!isset($routeParams['slug'])) {
            return $this->renderAttr($attr);
        }

        $first = reset($objects);

        $attr = array_merge($attr, [
            'class'      => trim(sprintf('%s js-content-sortable', $attr['class'] ?? '')),
            'data-slug'  => $routeParams['slug'],
            'data-class' => base64_encode(ClassUtils::getClass($first)).'"',
        ]);

        return $this->renderAttr($attr);
    }

    /**
     * {@inheritDoc}
     */
    public function renderItemAttr($object, array $attr = []): string
    {
        $ids = $this->om->getClassMetadata(ClassUtils::getClass($object))->getIdentifierValues($object);

        $attr['data-id'] = reset($ids);

        return $this->renderAttr($attr);
    }

    /**
     * @param array $attr Attributes
     *
     * @return string
     */
    private function renderAttr(array $attr): string
    {
        $parts = [];

        foreach (array_map('htmlspecialchars', $attr) as $name => $value) {
            $parts[] = sprintf('%s="%s"', $name, $value);
        }
        if (empty($parts)) {
            return '';
        }

        return sprintf(' %s', implode(' ', $parts));
    }
}
