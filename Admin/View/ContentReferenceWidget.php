<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Admin\View;

use Darvin\AdminBundle\EntityNamer\EntityNamerInterface;
use Darvin\AdminBundle\Security\Permissions\Permission;
use Darvin\AdminBundle\View\Widget\Widget\AbstractWidget;
use Darvin\AdminBundle\View\Widget\Widget\ShowLinkWidget;
use Darvin\ContentBundle\Entity\ContentReference;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Content reference admin view widget
 */
class ContentReferenceWidget extends AbstractWidget
{
    /**
     * @var \Darvin\AdminBundle\EntityNamer\EntityNamerInterface
     */
    private $entityNamer;

    /**
     * @var \Darvin\AdminBundle\View\Widget\Widget\ShowLinkWidget
     */
    private $showLinkWidget;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Darvin\AdminBundle\EntityNamer\EntityNamerInterface  $entityNamer    Entity namer
     * @param \Darvin\AdminBundle\View\Widget\Widget\ShowLinkWidget $showLinkWidget Show link admin view widget
     * @param \Symfony\Contracts\Translation\TranslatorInterface    $translator     Translator
     */
    public function __construct(EntityNamerInterface $entityNamer, ShowLinkWidget $showLinkWidget, TranslatorInterface $translator)
    {
        $this->entityNamer = $entityNamer;
        $this->showLinkWidget = $showLinkWidget;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    protected function createContent(object $entity, array $options): ?string
    {
        $ref = $this->getPropertyValue($entity, $options['property']);

        if (null === $ref) {
            return null;
        }
        if (!$ref instanceof ContentReference) {
            throw new \InvalidArgumentException(sprintf(
                'View widget "%s" requires property value to be instance of "%s", got "%s".',
                $this->getAlias(),
                ContentReference::class,
                is_object($ref) ? get_class($ref) : gettype($ref)
            ));
        }

        $entity = $ref->getObject();

        if (null === $entity) {
            return null;
        }

        $parts = [$this->translator->trans(sprintf('entity_name.single.%s', $this->entityNamer->name($entity)), [], 'admin')];

        $showLink = trim((string)$this->showLinkWidget->getContent($entity, [
            'text' => true,
        ]));

        $parts[] = '' !== $showLink ? $showLink : (string)$entity;

        return implode(' ', $parts);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequiredPermissions(): iterable
    {
        yield Permission::VIEW;
    }
}
