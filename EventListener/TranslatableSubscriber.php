<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener;

use Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface;
use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Id\IdentityGenerator;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

/**
 * Translatable event subscriber
 */
class TranslatableSubscriber implements EventSubscriber
{
    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    private $entityResolver;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface
     */
    private $localeSetter;

    /**
     * @param \Darvin\Utils\ORM\EntityResolverInterface                            $entityResolver Entity resolver
     * @param \Darvin\ContentBundle\Translatable\TranslatableLocaleSetterInterface $localeSetter   Translatable locale setter
     */
    public function __construct(EntityResolverInterface $entityResolver, TranslatableLocaleSetterInterface $localeSetter)
    {
        $this->entityResolver = $entityResolver;
        $this->localeSetter = $localeSetter;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
            Events::prePersist,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs Event arguments
     */
    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();

        if ($entity instanceof TranslatableInterface) {
            $this->localeSetter->setLocales($entity);
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs Event arguments
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();

        if ($entity instanceof TranslatableInterface) {
            $this->localeSetter->setLocales($entity);
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $args Event arguments
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $meta = $args->getClassMetadata();

        if (is_a($meta->getName(), TranslatableInterface::class, true)) {
            $this->mapTranslatable($meta);
        }
        if (is_a($meta->getName(), TranslationInterface::class, true)) {
            $this->mapTranslation($meta);
        }
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $meta Metadata
     */
    private function mapTranslatable(ClassMetadataInfo $meta): void
    {
        if (!$meta->hasAssociation('translations')) {
            $class = $this->entityResolver->resolve($meta->getName());

            $meta->mapOneToMany([
                'fieldName'     => 'translations',
                'targetEntity'  => $this->entityResolver->resolve($class::{'getTranslationEntityClass'}()),
                'mappedBy'      => 'translatable',
                'cascade'       => ['persist', 'merge', 'remove'],
                'orphanRemoval' => true,
                'fetch'         => ClassMetadataInfo::FETCH_LAZY,
                'indexBy'       => 'locale',
            ]);
        }
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $meta Metadata
     */
    private function mapTranslation(ClassMetadataInfo $meta): void
    {
        if (!$meta->hasField('id')) {
            (new ClassMetadataBuilder($meta))->createField('id', 'integer')->generatedValue('IDENTITY')->makePrimaryKey()->build();

            $meta->setIdGenerator(new IdentityGenerator());
        }
        if (!$meta->hasAssociation('translatable')) {
            $class = $this->entityResolver->resolve($meta->getName());

            $meta->mapManyToOne([
                'fieldName'    => 'translatable',
                'targetEntity' => $this->entityResolver->resolve($class::{'getTranslatableEntityClass'}()),
                'inversedBy'   => 'translations',
                'cascade'      => ['persist', 'merge'],
                'fetch'        => ClassMetadataInfo::FETCH_LAZY,
                'joinColumns'  => [
                    [
                        'name'                 => 'translatable_id',
                        'referencedColumnName' => 'id',
                        'onDelete'             => 'CASCADE',
                    ],
                ],
            ]);
        }

        $constraint = $meta->getTableName().'_unique_translation';

        if (!isset($meta->table['uniqueConstraints'][$constraint])) {
            $meta->table['uniqueConstraints'][$constraint] = [
                'columns' => ['translatable_id', 'locale'],
            ];
        }
        if (!($meta->hasField('locale') || $meta->hasAssociation('locale'))) {
            $meta->mapField([
                'fieldName' => 'locale',
                'type'      => 'string',
            ]);
        }
    }
}
