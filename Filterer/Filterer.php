<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ContentBundle\Filterer;

use Darvin\ContentBundle\Event\Filterer\BuildConstraintEvent;
use Darvin\ContentBundle\Event\Filterer\FiltererEvents;
use Darvin\ContentBundle\Translatable\TranslatableManagerInterface;
use Darvin\ContentBundle\Translatable\TranslationJoinerInterface;
use Darvin\Utils\ORM\QueryBuilderUtil;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\MappingException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filterer
 */
class Filterer implements FiltererInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslatableManagerInterface
     */
    private $translatableManager;

    /**
     * @var \Darvin\ContentBundle\Translatable\TranslationJoinerInterface
     */
    private $translationJoiner;

    /**
     * @var \Symfony\Component\OptionsResolver\OptionsResolver
     */
    private $optionsResolver;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadataInfo[]
     */
    private $doctrineMetadata;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                            $em                  Entity manager
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface     $eventDispatcher     Event dispatcher
     * @param \Darvin\ContentBundle\Translatable\TranslatableManagerInterface $translatableManager Translatable manager
     * @param \Darvin\ContentBundle\Translatable\TranslationJoinerInterface   $translationJoiner   Translation joiner
     */
    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $eventDispatcher,
        TranslatableManagerInterface $translatableManager,
        TranslationJoinerInterface $translationJoiner
    ) {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->translatableManager = $translatableManager;
        $this->translationJoiner = $translationJoiner;
        $this->optionsResolver = new OptionsResolver();
        $this->doctrineMetadata = [];

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritDoc}
     */
    public function filter(QueryBuilder $qb, ?array $filterData = null, array $options = [], bool $conjunction = true): void
    {
        if (empty($filterData)) {
            return;
        }
        foreach ($filterData as $field => $value) {
            if (null === $value) {
                unset($filterData[$field]);
            }
        }
        if (empty($filterData)) {
            return;
        }

        $rootAliases = $qb->getRootAliases();

        if (count($rootAliases) > 1) {
            throw new \InvalidArgumentException('Only single root alias query builders are supported.');
        }
        try {
            $options = $this->optionsResolver->resolve($options);
        } catch (ExceptionInterface $ex) {
            throw new \InvalidArgumentException(sprintf('Options are invalid: "%s".', $ex->getMessage()));
        }

        $this->addConstraints($qb, $filterData, $options, $conjunction, $rootAliases[0]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver Options resolver
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'non_strict_comparison_fields' => [],
            ])
            ->setAllowedTypes('non_strict_comparison_fields', 'array');
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb          Query builder
     * @param array                      $filterData  Filter data
     * @param array                      $options     Options
     * @param bool                       $conjunction Whether to use conjunction (otherwise - disjunction)
     * @param string                     $rootAlias   Query builder root alias
     */
    private function addConstraints(QueryBuilder $qb, array $filterData, array $options, bool $conjunction, string $rootAlias): void
    {
        $rootEntities = $qb->getRootEntities();
        $entityClass = $rootEntities[0];

        $where = [];

        foreach ($filterData as $field => $value) {
            $strictComparison = !in_array($field, $options['non_strict_comparison_fields']);

            $event = new BuildConstraintEvent($qb, $field, $value, $entityClass, $rootAlias, $strictComparison);

            $this->eventDispatcher->dispatch($event, FiltererEvents::BUILD_CONSTRAINT);

            $where[] = null !== $event->getConstraint()
                ? $event->getConstraint()
                : $this->buildConstraint($qb, $field, $value, $entityClass, $rootAlias, $strictComparison);
        }

        $qb->andWhere(implode($conjunction ? ' AND ' : ' OR ', $where));
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb               Query builder
     * @param string                     $field            Field
     * @param mixed                      $value            Value
     * @param string                     $entityClass      Entity class
     * @param string                     $rootAlias        Query builder root alias
     * @param bool                       $strictComparison Whether to use strict comparison
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    private function buildConstraint(QueryBuilder $qb, string $field, $value, string $entityClass, string $rootAlias, bool $strictComparison): string
    {
        $qb->setParameter($field, $strictComparison ? $value : '%'.$value.'%');

        $meta = $this->getDoctrineMetadata($entityClass);

        if (isset($meta->associationMappings[$field]) && ClassMetadataInfo::MANY_TO_MANY === $meta->associationMappings[$field]['type']) {
            if (null === QueryBuilderUtil::findJoinByAlias($qb, $rootAlias, $field)) {
                $qb->innerJoin(sprintf('%s.%s', $rootAlias, $field), $field);
            }

            return sprintf('%s = :%1$s', $field);
        }

        $property = preg_replace('/(From|To)$/', '', $field);

        if (!isset($meta->associationMappings[$property]) && !isset($meta->fieldMappings[$property])) {
            if (!$this->translatableManager->isTranslatable($entityClass)) {
                throw new \InvalidArgumentException(
                    sprintf('Property "%s::$%s" is not association or mapped field.', $entityClass, $property)
                );
            }

            $this->translationJoiner->joinTranslation($qb, false, null, TranslatableManagerInterface::TRANSLATIONS_PROPERTY, true);

            $rootAlias = TranslatableManagerInterface::TRANSLATIONS_PROPERTY;
        }

        return sprintf('%s.%s %s :%s', $rootAlias, $property, $this->getConstraintExpression($field, $strictComparison), $field);
    }

    /**
     * @param string $field            Constraint field
     * @param bool   $strictComparison Whether to use strict comparison
     *
     * @return string
     */
    private function getConstraintExpression(string $field, bool $strictComparison): string
    {
        if (preg_match('/From$/', $field)) {
            return '>=';
        }
        if (preg_match('/To$/', $field)) {
            return '<=';
        }

        return $strictComparison ? '=' : 'LIKE';
    }

    /**
     * @param string $entityClass Entity class
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadataInfo
     * @throws \InvalidArgumentException
     */
    private function getDoctrineMetadata(string $entityClass): ClassMetadataInfo
    {
        if (!isset($this->doctrineMetadata[$entityClass])) {
            try {
                $this->doctrineMetadata[$entityClass] = $this->em->getClassMetadata($entityClass);
            } catch (MappingException $ex) {
                throw new \InvalidArgumentException(sprintf('Unable to get Doctrine metadata for class "%s".', $entityClass));
            }
        }

        return $this->doctrineMetadata[$entityClass];
    }
}
