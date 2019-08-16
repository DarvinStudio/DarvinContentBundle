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
            'class'               => trim(sprintf('%s js-content-sortable', $attr['class'] ?? '')),
            'data-reposition-url' => $this->router->generate('darvin_content_sorting_reposition'),
            'data-slug'           => $routeParams['slug'],
            'data-class'          => base64_encode(ClassUtils::getClass($first)),
            'data-csrf-token'     => $this->csrfTokenManager->getToken(RepositionType::CSRF_TOKEN_ID)->getValue(),
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
