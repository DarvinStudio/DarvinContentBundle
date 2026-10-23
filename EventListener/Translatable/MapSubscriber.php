<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\EventListener\Translatable;

use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Id\IdentityGenerator;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

/**
 * Map translatable event subscriber
 */
class MapSubscriber implements EventSubscriber
{
    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    private $entityResolver;

    /**
     * @param \Darvin\Utils\ORM\EntityResolverInterface $entityResolver Entity resolver
     */
    public function __construct(EntityResolverInterface $entityResolver)
    {
        $this->entityResolver = $entityResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
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

        $constraint = sprintf('%s_unique_translation', $meta->getTableName());

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
